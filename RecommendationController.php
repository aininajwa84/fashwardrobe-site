<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wardrobe;
use App\Models\Recommendation;
use App\Models\ShoppingItem;
use App\Services\LazadaScraperService; 
use App\Services\SheinRealScraperService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RecommendationController extends Controller
{
    public function index()
    {
        // Get user's recent recommendations
        $recommendations = auth()->user()->recommendations()
            ->latest()
            ->take(5)
            ->get();
        
        return view('recommendation.index', compact('recommendations'));
    }

    public function generate(Request $request)
    {
        // Log everything for debugging
        Log::info('=== RECOMMENDATION GENERATE START ===');
        Log::info('User ID: ' . auth()->id());
        Log::info('Request Data:', $request->all());
        
        $validated = $request->validate([
            'theme' => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:50',
            'preferences' => 'nullable|array',
        ]);

        // Get ALL wardrobe items first for debugging
        $allWardrobes = auth()->user()->wardrobes()->get();
        Log::info('Total wardrobe items: ' . $allWardrobes->count());
        
        if ($allWardrobes->count() > 0) {
            Log::info('Sample items in database:', [
                'names' => $allWardrobes->take(3)->pluck('name')->toArray(),
                'themes' => $allWardrobes->take(3)->pluck('theme')->toArray(),
                'occasions' => $allWardrobes->take(3)->pluck('occasion')->toArray(),
            ]);
        }

        // 1. Search in user's wardrobe
        $query = auth()->user()->wardrobes();
        
        $theme = strtolower(trim($request->theme));
        Log::info('Searching for theme: ' . $theme . ' in theme/occasion field');
        
        // Map form themes to possible database values
        $themeMappings = [
            'casual' => ['casual', 'casual day out', 'weekend', 'outdoor', 'everyday'],
            'formal' => ['formal', 'formal event', 'office', 'work', 'business', 'professional'],
            'work' => ['work', 'office', 'formal', 'business', 'professional'],
            'party' => ['party', 'party night out', 'night out', 'event', 'celebration', 'night'],
            'wedding' => ['wedding', 'ceremony', 'formal'],
            'beach' => ['beach', 'outdoor', 'summer', 'swim', 'vacation'],
            'date' => ['date', 'date night', 'romantic', 'dinner', 'evening'],
            'sport' => ['sport', 'sport exercise', 'gym', 'athletic', 'workout', 'exercise'],
            'travel' => ['travel', 'comfort', 'outdoor', 'adventure', 'journey']
        ];
        
        // Get search terms for this theme
        $searchTerms = $themeMappings[$theme] ?? [$theme];
        Log::info('Search terms:', $searchTerms);
        
        // Search with OR conditions for all possible terms in theme OR occasion fields
        $query->where(function($q) use ($searchTerms) {
            foreach ($searchTerms as $term) {
                $q->orWhere('theme', 'like', '%' . $term . '%')
                  ->orWhere('occasion', 'like', '%' . $term . '%');
            }
        });
        
        // Color filter
        if ($request->has('color') && $request->color !== 'null' && $request->color !== '' && $request->color !== null) {
            $query->where('color', 'like', '%' . $request->color . '%');
            Log::info('Color filter: ' . $request->color);
        }
        
        // Category filter
        if ($request->has('category') && $request->category !== 'null' && $request->category !== '' && $request->category !== null) {
            $query->where('category', $request->category);
            Log::info('Category filter: ' . $request->category);
        }
        
        // Filter by preferences
        if ($request->has('preferences')) {
            $preferences = $request->preferences;
            Log::info('Preferences:', $preferences);
            
            if (in_array('favorite', $preferences)) {
                $query->where('is_favorite', true);
            }
            if (in_array('new', $preferences)) {
                $query->where('created_at', '>=', now()->subDays(30));
            }
            if (in_array('unused', $preferences)) {
                $query->where(function($q) {
                    $q->where('last_worn_date', '<=', now()->subMonths(3))
                      ->orWhereNull('last_worn_date');
                });
            }
        }

        $items = $query->get();
        
        Log::info('Found ' . $items->count() . ' matching items');
        if ($items->count() > 0) {
            Log::info('Matching items:', $items->pluck('name', 'theme')->toArray());
        }

        // DEBUG: Check the recommendations table structure
        Log::info('Checking recommendations table structure...');
        $tableInfo = DB::select("SHOW COLUMNS FROM recommendations WHERE Field = 'source'");
        Log::info('Source column info:', (array) $tableInfo[0]);

        // Use single character source values to avoid any truncation
        $source = $items->count() > 0 ? 'w' : 'e'; // w = wardrobe, e = empty
        
        try {
            Recommendation::create([
                'user_id' => auth()->id(),
                'theme' => $request->theme,
                'source' => $source, // Single character
                'data' => json_encode([
                    'color' => $request->color,
                    'category' => $request->category,
                    'preferences' => $request->preferences,
                    'result_count' => $items->count(),
                    'total_wardrobe_count' => $allWardrobes->count(),
                    'search_terms_used' => $searchTerms,
                    'matched_items' => $items->pluck('name')->toArray()
                ])
            ]);
            Log::info('Recommendation saved successfully with source: ' . $source);
        } catch (\Exception $e) {
            Log::error('Failed to save recommendation: ' . $e->getMessage());
            // Continue even if saving fails
        }

        // DEBUG MODE: Always show result page first
        if (config('app.debug')) {
            Log::info('DEBUG MODE: Showing result page with ' . $items->count() . ' items');
            
            if ($items->count() == 0) {
                Log::warning('No items found in wardrobe! Showing empty result page.');
            }
            
            return view('recommendation.result', [
                'items' => $items,
                'searchTheme' => $request->theme,
                'searchColor' => $request->color,
                'searchCategory' => $request->category,
                'debugInfo' => [
                    'total_wardrobe' => $allWardrobes->count(),
                    'search_terms' => $searchTerms,
                    'all_themes' => $allWardrobes->pluck('theme')->unique()->values(),
                    'all_occasions' => $allWardrobes->pluck('occasion')->unique()->values()
                ]
            ]);
        }

        // PRODUCTION: Show result page if items found
        if ($items->count() > 0) {
            Log::info('Showing result page with ' . $items->count() . ' matching items');
            return view('recommendation.result', [
                'items' => $items,
                'searchTheme' => $request->theme,
                'searchColor' => $request->color,
                'searchCategory' => $request->category
            ]);
        }

        Log::warning('No matching items found in wardrobe. Redirecting to online search...');
        
        // Build search keyword from theme
        $keyword = $request->theme;
        if ($request->category && $request->category !== 'null' && $request->category !== '' && $request->category !== null) {
            $keyword .= ' ' . $request->category;
        }
        
        // Redirect to online search with parameters
        return redirect()->route('recommendation.online')
            ->with([
                'search_keyword' => $keyword,
                'category' => $request->category,
                'message' => 'No matching items found in your wardrobe. Showing online results for "' . $request->theme . '"'
            ]);
    }

    public function getOnlineRecommendations(Request $request)
    {
        $validated = $request->validate([
            'keyword' => 'required|string|min:2',
            'category' => 'nullable|string',
            'platform' => 'nullable|string',
            'min_price' => 'nullable|numeric',
            'max_price' => 'nullable|numeric',
            'sort_by' => 'nullable|string'
        ]);
        
        $keyword = $validated['keyword'];
        $category = $validated['category'] ?? '';
        $platform = $validated['platform'] ?? 'shein';

         $products = [];
    
    if ($platform === 'shein' || $platform === 'all') {
        // Use SHEIN scraper
        $sheinScraper = new SheinRealScraperService();
        $sheinProducts = $sheinScraper->searchProducts($keyword, $category, $platform === 'all' ? 6 : 12);
    // Filter only real products
            $sheinProducts = array_filter($sheinProducts, function($product) {
                return !isset($product['is_real']) || $product['is_real'] === true;
            });
            
            $products = array_merge($products, $sheinProducts);
    }
    
    if ($platform === 'lazada' || $platform === 'all') {
        // Use Lazada scraper
        $lazadaScraper = new LazadaScraperService();
        $lazadaProducts = $lazadaScraper->searchProducts($keyword, $category, $platform === 'all' ? 6 : 12);
        
        // Make sure Lazada also returns only real data
            $lazadaProducts = array_filter($lazadaProducts, function($product) {
                return !isset($product['is_simulated']) || $product['is_simulated'] === false;
            });

        $products = array_merge($products, $lazadaProducts);
    }

    // If no products found, return empty with message
        if (empty($products)) {
            return redirect()->back()
                ->withErrors(['error' => 'No real products found. The website may have blocked our request. Please try again later.'])
                ->withInput();
        }

    shuffle($products);
        
        // Try to save with single character source
        try {
            Recommendation::create([
                'user_id' => auth()->id(),
                'theme' => $keyword,
                'source' => 'o', // o = online
                'data' => json_encode([
                    'keyword' => $keyword,
                    'category' => $category,
                    'platform' => $platform,
                    'result_count' => count($products),
                    'is_real_data' => true
                ])
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save online recommendation: ' . $e->getMessage());
        }
        
        return view('recommendation.online', [
            'products' => $realProducts,
            'keyword' => $keyword,
            'category' => $category,
            'platform' => $platform,
            'real_count' => count($products)
        ]);
    }

    public function result(Request $request)
    {
        // If user directly accesses result page without search, redirect to index
        if (!session()->has('search_data') && !$request->has('search')) {
            return redirect()->route('recommendation.index');
        }
        
        // Get search parameters from session or request
        $searchTheme = session('search_theme', $request->get('theme', ''));
        $searchColor = session('search_color', $request->get('color', ''));
        $searchCategory = session('search_category', $request->get('category', ''));
        
        // If we have items in session, use them
        if (session()->has('search_items')) {
            $items = session('search_items');
            session()->forget(['search_items', 'search_theme', 'search_color', 'search_category']);
            
            return view('recommendation.result', [
                'items' => $items,
                'searchTheme' => $searchTheme,
                'searchColor' => $searchColor,
                'searchCategory' => $searchCategory
            ]);
        }
        
        // Otherwise, perform a new search
        return redirect()->route('recommendation.index');
    }

    public function simulatePurchase(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string',
            'price' => 'required|string',
            'store' => 'required|string',
            'link' => 'nullable|url',
            'image' => 'nullable|url',
        ]);

        // Simulate purchase process
        $purchaseData = [
            'transaction_id' => 'DEMO-' . strtoupper(uniqid()),
            'product' => $validated['product_name'],
            'price' => $validated['price'],
            'store' => $validated['store'],
            'status' => 'completed',
            'purchased_at' => now()->toDateTimeString(),
            'estimated_delivery' => now()->addDays(3)->toDateString(),
        ];

        // Log purchase simulation (for FYP demo purposes)
        Log::info('Purchase simulated', $purchaseData);

        // Add to purchase history
        ShoppingItem::create([
            'user_id' => auth()->id(),
            'name' => $validated['product_name'],
            'price' => $validated['price'],
            'platform' => $validated['store'],
            'product_link' => $validated['link'] ?? '#',
            'image_url' => $validated['image'] ?? null,
            'status' => 'purchased',
            'purchased_at' => now(),
        ]);

        return redirect()->back()
            ->with('purchase_success', true)
            ->with('purchase_data', $purchaseData);
    }

    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string',
            'price' => 'required|string',
            'store' => 'required|string',
            'link' => 'nullable|url',
            'image' => 'nullable|url',
        ]);

        // Check if item already exists in cart
        $existingItem = ShoppingItem::where('user_id', auth()->id())
            ->where('name', $validated['product_name'])
            ->where('status', 'wishlist')
            ->first();
        
        if ($existingItem) {
            return redirect()->back()
                ->with('cart_info', 'Product already in your shopping cart!');
        }

        // Create shopping cart item
        ShoppingItem::create([
            'user_id' => auth()->id(),
            'name' => $validated['product_name'],
            'price' => $validated['price'],
            'platform' => $validated['store'],
            'product_link' => $validated['link'] ?? '#',
            'image_url' => $validated['image'] ?? null,
            'status' => 'wishlist',
        ]);

        return redirect()->back()
            ->with('cart_success', 'Product added to shopping cart!');
    }

    public function viewCart()
    {
        $cartItems = auth()->user()->shoppingItems()
            ->where('status', 'wishlist')
            ->latest()
            ->get();
        
        $total = $cartItems->sum(function($item) {
            return floatval(str_replace(['RM', ',', ' '], '', $item->price));
        });

        $purchasedItems = auth()->user()->shoppingItems()
            ->where('status', 'purchased')
            ->latest()
            ->take(5)
            ->get();

        return view('recommendation.cart', compact('cartItems', 'total', 'purchasedItems'));
    }

    public function online(Request $request)
    {
        // Get parameters from request or session
        $keyword = $request->get('keyword', session('search_keyword', 'clothing'));
        $category = $request->get('category', session('search_category', ''));
        
        // Clear session data if present
        if (session()->has('search_keyword')) {
            session()->forget(['search_keyword', 'search_category']);
        }
        
        // Use the scraper service
        $lazadaScraper = new LazadaScraperService();
        $realProducts = $lazadaScraper->searchProducts($keyword, $category, 12);
        
        return view('recommendation.online', [
            'products' => $realProducts,
            'keyword' => $keyword,
            'category' => $category
        ]);
    }

    public function removeFromCart($id)
    {
        $cartItem = ShoppingItem::where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();
        
        $cartItem->delete();
        
        return redirect()->route('recommendation.cart')
            ->with('cart_success', 'Item removed from cart!');
    }

    public function clearCart()
    {
        ShoppingItem::where('user_id', auth()->id())
            ->where('status', 'wishlist')
            ->delete();
        
        return redirect()->route('recommendation.cart')
            ->with('cart_success', 'All items removed from cart!');
    }

    public function addOutfitToCart(Request $request)
    {
        $validated = $request->validate([
            'outfit_name' => 'required|string',
            'items' => 'required|array',
            'items.*.name' => 'required|string',
            'items.*.price' => 'required|string',
            'items.*.store' => 'required|string',
        ]);
        
        $addedCount = 0;
        
        foreach ($validated['items'] as $item) {
            // Check if already in cart
            $existing = ShoppingItem::where('user_id', auth()->id())
                ->where('name', $item['name'])
                ->where('status', 'wishlist')
                ->exists();
            
            if (!$existing) {
                ShoppingItem::create([
                    'user_id' => auth()->id(),
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'platform' => $item['store'],
                    'product_link' => '#',
                    'status' => 'wishlist',
                    'outfit_name' => $validated['outfit_name'],
                ]);
                $addedCount++;
            }
        }
        
        return redirect()->route('recommendation.cart')
            ->with('cart_success', $addedCount . ' items from outfit added to cart!');
    }
}
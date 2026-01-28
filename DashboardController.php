<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wardrobe;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get stats
        $wardrobeItems = $user->wardrobes()->count();
        $categories = $user->wardrobes()->distinct('category')->count('category');
        
        $stats = [
            'wardrobe_items' => $wardrobeItems,
            'categories' => $categories,
            'outfit_suggestions' => $wardrobeItems > 0 ? rand(3, 10) : 0,
        ];
        
        // Get recent items
        $recent_items = $user->wardrobes()
            ->latest()
            ->take(5)
            ->get();
        
        // Today's date
        $today = now()->format('l, F j, Y');
        
        // Occasion suggestions
        $occasion_suggestions = [
            'Casual Chic Look',
            'Comfortable footwear',
            'Light layers for weather',
            'Accessorize minimally'
        ];
        
        return view('dashboard', compact(
            'stats', 
            'recent_items', 
            'today', 
            'occasion_suggestions'
        ));
    }
}
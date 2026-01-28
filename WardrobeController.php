<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wardrobe;
use Illuminate\Support\Facades\Storage;

class WardrobeController extends Controller
{
    /**
     * Display a listing of wardrobe items with search & filter
     */
    public function index(Request $request)
    {
        $query = auth()->user()->wardrobes();
        
        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('category', 'like', "%{$search}%")
                  ->orWhere('color', 'like', "%{$search}%")
                  ->orWhere('theme', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }
        
        // Filter by category
        if ($request->has('category') && $request->category != 'all') {
            $query->where('category', $request->category);
        }
        
        // Filter by color
        if ($request->has('color') && $request->color != 'all') {
            $query->where('color', $request->color);
        }
        
        $items = $query->latest()->paginate(12);
        
        // Get unique categories and colors for filters
        $categories = Wardrobe::where('user_id', auth()->id())->distinct()->pluck('category')->filter()->values();
        $colors = Wardrobe::where('user_id', auth()->id())->distinct()->pluck('color')->filter()->values();
        
        return view('wardrobe.index', compact('items', 'categories', 'colors'));
    }

    // Store
    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required',
            'color' => 'required',
            'image' => 'required|image',
        ]);

        // Upload image
        $imagePath = $request->file('image')->store('wardrobe', 'public');

        // Create item
        Wardrobe::create([
            'user_id' => auth()->id(),
            'category' => $request->category,
            'color' => $request->color,
            'image' => $imagePath,
            'name' => $request->name,        // Optional - tak perlu validation
            'theme' => $request->theme,      // Optional - tak perlu validation  
            'notes' => $request->notes,      // Optional - tak perlu validation
        ]);

        return redirect()->route('wardrobe.index')
            ->with('success', '✅ Item saved successfully!');
    }

    // Store item from online recommendation
    public function storeFromOnline(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'image_url' => 'required',
            'category' => 'required',
            'color' => 'required',
        ]);

        // Create item
        Wardrobe::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'image' => $request->image_url,
            'category' => $request->category,
            'color' => $request->color,
            'brand' => $request->brand ?? 'Online Store',
            'price' => $request->price ?? 'Not specified',
            'purchase_link' => $request->purchase_link,
            'source' => 'online_recommendation',
        ]);

        return back()->with('success', 'Item added to your wardrobe successfully!');
    }

    // Show
    public function show($id)
    {
        $item = Wardrobe::where('user_id', auth()->id())->findOrFail($id);
        return view('wardrobe.show', compact('item'));
    }

    // Edit
    public function edit($id)
    {
        $item = Wardrobe::where('user_id', auth()->id())->findOrFail($id);
        return view('wardrobe.edit', compact('item'));
    }

    // Update 
    public function update(Request $request, $id)
    {
        $item = Wardrobe::where('user_id', auth()->id())->findOrFail($id);

        $request->validate([
            'category' => 'required',
            'color' => 'required',
            'image' => 'nullable|image',
        ]);

        // Update data
        $data = [
            'category' => $request->category,
            'color' => $request->color,
            'name' => $request->name,
            'theme' => $request->theme,
            'notes' => $request->notes,
        ];

        // Update image
        if ($request->hasFile('image')) {
            // Delete old image
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            
            $path = $request->file('image')->store('wardrobe', 'public');
            $data['image'] = $path;
        }

        $item->update($data);

        return redirect()->route('wardrobe.index')
            ->with('success', 'Clothing item updated successfully!');
    }

    // Delete
    public function destroy($id)
    {
        $item = Wardrobe::where('user_id', auth()->id())->findOrFail($id);
        
        // Delete image from storage
        if ($item->image) {
            Storage::disk('public')->delete($item->image);
        }
        
        $item->delete();

        return redirect()->route('wardrobe.index')
            ->with('success', 'Clothing item deleted successfully!');
    }
}
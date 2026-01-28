{{-- resources/views/recommendation/cart.blade.php --}}
@extends('layouts.app')

@section('title', 'Shopping Cart - Smart Wardrobe')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-purple-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-purple-100 to-pink-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">🛒 Your Shopping Cart</h1>
            <p class="text-gray-600">Items saved for purchase from online recommendations</p>
        </div>

        <!-- Cart Items -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">{{ $cartItems->count() }} Items in Cart</h2>
                    <p class="text-sm text-gray-500">Total: <span class="font-bold text-purple-600">RM {{ number_format($total, 2) }}</span></p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('recommendation.online') }}" 
                       class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Find More Items
                    </a>
                </div>
            </div>

            @if($cartItems->count() > 0)
                <div class="space-y-4">
                    @foreach($cartItems as $item)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                            <div class="flex items-start">
                                <!-- Product Image -->
                                <div class="w-24 h-24 rounded-lg overflow-hidden bg-gray-100 mr-4">
                                    @if($item->image_url)
                                        <img src="{{ $item->image_url }}" 
                                             alt="{{ $item->name }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-r from-purple-100 to-pink-100 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-purple-300" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Product Info -->
                                <div class="flex-1">
                                    <div class="flex justify-between">
                                        <div>
                                            <h3 class="font-medium text-gray-900">{{ $item->name }}</h3>
                                            <p class="text-sm text-gray-600 mt-1">{{ $item->platform }}</p>
                                            <p class="text-lg font-bold text-purple-600 mt-2">{{ $item->price }}</p>
                                        </div>
                                        <div class="text-right">
                                            <!-- Status Badge -->
                                            <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Actions -->
                                    <div class="flex items-center justify-between mt-4">
                                        <div class="flex space-x-3">
                                            @if($item->product_link && $item->product_link != '#')
                                                <a href="{{ $item->product_link }}" 
                                                   target="_blank"
                                                   class="px-3 py-1 bg-gradient-to-r from-red-600 to-red-700 text-white text-sm rounded hover:from-red-700 hover:to-red-800 transition">
                                                    Buy Now
                                                </a>
                                            @else
                                                <form action="{{ route('recommendation.simulate.purchase') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="product_name" value="{{ $item->name }}">
                                                    <input type="hidden" name="price" value="{{ $item->price }}">
                                                    <input type="hidden" name="store" value="{{ $item->platform }}">
                                                    <input type="hidden" name="link" value="{{ $item->product_link }}">
                                                    <input type="hidden" name="image" value="{{ $item->image_url }}">
                                                    
                                                    <button type="submit" 
                                                            class="px-3 py-1 bg-gradient-to-r from-orange-600 to-orange-700 text-white text-sm rounded hover:from-orange-700 hover:to-orange-800 transition">
                                                        Simulate Purchase
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            <!-- Move to Wardrobe -->
                                            <form action="{{ route('wardrobe.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="name" value="{{ $item->name }}">
                                                <input type="hidden" name="brand" value="{{ $item->platform }}">
                                                <input type="hidden" name="price" value="{{ $item->price }}">
                                                <input type="hidden" name="purchase_link" value="{{ $item->product_link }}">
                                                <input type="hidden" name="image_url" value="{{ $item->image_url }}">
                                                <input type="hidden" name="source" value="shopping_cart">
                                                
                                                <button type="submit" 
                                                        class="px-3 py-1 bg-green-100 text-green-700 text-sm rounded hover:bg-green-200 transition">
                                                    Add to Wardrobe
                                                </button>
                                            </form>
                                        </div>
                                        
                                        <!-- Remove Button -->
                                        <form action="{{ route('recommendation.cart.remove', $item->id) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Remove this item from cart?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-800">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Cart Summary -->
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-gray-600">Total Items: {{ $cartItems->count() }}</p>
                            <p class="text-lg font-bold text-gray-900">Total: RM {{ number_format($total, 2) }}</p>
                        </div>
                        <div class="flex space-x-4">
                            <button onclick="alert('Checkout functionality would be implemented here for real e-commerce')"
                                    class="px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg hover:from-purple-700 hover:to-purple-800 transition font-medium">
                                Proceed to Checkout
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty Cart -->
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Your shopping cart is empty</h3>
                    <p class="text-gray-600 mb-4">Add items from online recommendations to get started</p>
                    <a href="{{ route('recommendation.index') }}" 
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg hover:from-purple-700 hover:to-purple-800 transition font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Browse Recommendations
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
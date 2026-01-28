{{-- resources/views/recommendation/online.blade.php --}}
@extends('layouts.app')

@section('title', 'Online Recommendations - Smart Wardrobe')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header with Scraping Status -->
        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-green-100 to-blue-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">🛒 Real Online Recommendations</h1>
            
            <!-- Platform Tabs -->
            <div class="flex justify-center mb-4">
                <div class="inline-flex rounded-lg border border-gray-300 p-1 bg-white">
                    <a href="{{ route('recommendation.online') }}?keyword={{ urlencode($keyword ?? '') }}&platform=shein" 
                       class="px-4 py-2 rounded-md text-sm font-medium {{ ($platform ?? 'shein') == 'shein' ? 'bg-purple-600 text-white' : 'text-gray-600 hover:text-gray-900' }}">
                        SHEIN
                    </a>
                    <a href="{{ route('recommendation.online') }}?keyword={{ urlencode($keyword ?? '') }}&platform=lazada" 
                       class="px-4 py-2 rounded-md text-sm font-medium {{ ($platform ?? '') == 'lazada' ? 'bg-orange-600 text-white' : 'text-gray-600 hover:text-gray-900' }}">
                        Lazada
                    </a>
                    <a href="{{ route('recommendation.online') }}?keyword={{ urlencode($keyword ?? '') }}&platform=all" 
                       class="px-4 py-2 rounded-md text-sm font-medium {{ ($platform ?? '') == 'all' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-gray-900' }}">
                        All Platforms
                    </a>
                </div>
            </div>
            
            @if(isset($keyword) && !empty($keyword))
                <p class="text-gray-600">
                    Found {{ count($products) }} products for "<span class="font-semibold">{{ $keyword }}</span>" on 
                    @if(($platform ?? 'shein') == 'shein')
                        SHEIN
                    @elseif(($platform ?? '') == 'lazada')
                        Lazada
                    @else
                        SHEIN & Lazada
                    @endif
                </p>
            @endif

            <!-- Success Messages -->
            @if(session('cart_success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-green-800">{{ session('cart_success') }}</span>
                        <a href="{{ route('recommendation.cart') }}" class="ml-auto text-green-700 hover:text-green-900 font-medium">
                            View Cart →
                        </a>
                    </div>
                </div>
            @endif

            @if(session('purchase_success'))
                <div class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-6">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-blue-500 mr-4 mt-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div class="flex-1">
                            <h4 class="font-bold text-blue-800 text-lg mb-2">🎉 Purchase Successful!</h4>
                            <div class="bg-white p-4 rounded-lg mb-3">
                                <p class="font-medium">Transaction ID: {{ session('purchase_data')['transaction_id'] }}</p>
                                <p class="text-gray-600">Product: {{ session('purchase_data')['product'] }}</p>
                                <p class="text-gray-600">Price: {{ session('purchase_data')['price'] }}</p>
                                <p class="text-gray-600">Status: <span class="font-medium text-green-600">{{ session('purchase_data')['status'] }}</span></p>
                                <p class="text-gray-600">Estimated Delivery: {{ session('purchase_data')['estimated_delivery'] }}</p>
                            </div>
                            <p class="text-sm text-blue-700">
                                This is a simulation for FYP demonstration. In production, this would redirect to the platform's checkout.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Scraping Status Badge -->
            @php
                $realProducts = array_filter($products, function($product) {
                    return !isset($product['is_simulated']) || $product['is_simulated'] === false;
                });
                $isRealData = count($realProducts) > 0;
                $currentPlatform = $platform ?? 'shein';
            @endphp
            
            <div class="mt-3 inline-flex items-center px-4 py-2 rounded-full text-sm font-medium
                {{ $isRealData ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.2 6.5 10.266a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"/>
                </svg>
                @if($isRealData)
                    ✅ Live data scraped from 
                    @if($currentPlatform == 'shein')
                        SHEIN
                    @elseif($currentPlatform == 'lazada')
                        Lazada
                    @else
                        multiple platforms
                    @endif
                @else
                    ⚠️ Showing simulated data (real scraping failed)
                @endif
            </div>
        </div>

        <!-- Search Bar -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <form action="{{ route('recommendation.search.online') }}" method="GET" class="space-y-4">
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Search Input -->
                    <div class="flex-1">
                        <label for="keyword" class="sr-only">Search products</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" 
                                   name="keyword" 
                                   id="keyword" 
                                   value="{{ $keyword ?? '' }}"
                                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="Search for dresses, shoes, accessories..." 
                                   required>
                        </div>
                    </div>
                    
                    <!-- Platform Select -->
                    <div class="w-full md:w-48">
                        <label for="platform" class="sr-only">Platform</label>
                        <select name="platform" 
                                id="platform" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white">
                            <option value="shein" {{ ($platform ?? 'shein') == 'shein' ? 'selected' : '' }}>SHEIN Malaysia</option>
                            <option value="lazada" {{ ($platform ?? '') == 'lazada' ? 'selected' : '' }}>Lazada Malaysia</option>
                            <option value="all" {{ ($platform ?? '') == 'all' ? 'selected' : '' }}>All Platforms</option>
                        </select>
                    </div>
                    
                    <!-- Search Button -->
                    <div>
                        <button type="submit" 
                                class="w-full md:w-auto px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg hover:from-purple-700 hover:to-purple-800 transition font-medium flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Search
                        </button>
                    </div>
                </div>
                
                <!-- Category Filters -->
                <div class="flex flex-wrap gap-2">
                    <span class="text-sm text-gray-600 mr-2">Quick categories:</span>
                    @foreach(['dress', 'top', 'bottom', 'shoes', 'accessories', 'bags'] as $cat)
                        <button type="button"
                                onclick="document.getElementById('keyword').value='{{ $cat }}'; this.form.submit();"
                                class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition">
                            {{ ucfirst($cat) }}
                        </button>
                    @endforeach
                </div>
            </form>
        </div>

        <!-- Online Products -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">
                        @if($currentPlatform == 'shein')
                            🛍️ Products from SHEIN
                        @elseif($currentPlatform == 'lazada')
                            🛍️ Products from Lazada
                        @else
                            🛍️ Products from SHEIN & Lazada
                        @endif
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        @if($currentPlatform == 'shein')
                            Real-time products scraped from SHEIN Malaysia
                        @elseif($currentPlatform == 'lazada')
                            Real-time products scraped from Lazada Malaysia
                        @else
                            Real-time products scraped from multiple platforms
                        @endif
                        @if(isset($products[0]['scraped_at']))
                            • Scraped: {{ \Carbon\Carbon::parse($products[0]['scraped_at'])->diffForHumans() }}
                        @endif
                    </p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('recommendation.index') }}" 
                       class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Recommendations
                    </a>
                </div>
            </div>

            @if(count($products) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($products as $index => $product)
                        <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition hover:-translate-y-1 duration-300 {{ isset($product['is_simulated']) && $product['is_simulated'] ? 'border-dashed opacity-90' : '' }}">
                            <!-- Product Image -->
                            <div class="w-full h-48 bg-gray-100 overflow-hidden relative">
                                @if(isset($product['image']) && $product['image'] && !str_contains($product['image'], 'placeholder'))
                                    <img src="{{ $product['image'] }}" 
                                         alt="{{ $product['name'] }}"
                                         class="w-full h-full object-cover hover:scale-105 transition duration-300"
                                         onerror="this.src='https://via.placeholder.com/300x300/F5F5F5/{{ $product['platform'] == 'shein' ? 'EA5455' : '666666' }}?text={{ urlencode($product['store'] ?? ucfirst($product['platform'] ?? 'Product')) }}'">
                                @else
                                    <div class="w-full h-full bg-gradient-to-r 
                                        {{ $product['platform'] == 'shein' ? 'from-pink-100 to-purple-100' : 'from-orange-100 to-pink-100' }} 
                                        flex items-center justify-center">
                                        @if($product['platform'] == 'shein')
                                            <div class="text-center">
                                                <div class="text-3xl mb-2">🛍️</div>
                                                <span class="text-sm text-gray-600">SHEIN</span>
                                            </div>
                                        @else
                                            <svg class="w-16 h-16 text-orange-300" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </div>
                                @endif
                                
                                <!-- Simulated Badge (if applicable) -->
                                @if(isset($product['is_simulated']) && $product['is_simulated'])
                                    <div class="absolute top-2 right-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded">
                                        Demo
                                    </div>
                                @else
                                    <div class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded">
                                        Live
                                    </div>
                                @endif
                                
                                <!-- Platform Logo -->
                                <div class="absolute bottom-2 left-2 bg-white bg-opacity-90 px-2 py-1 rounded text-xs font-medium
                                    {{ $product['platform'] == 'shein' ? 'text-pink-600' : 'text-orange-600' }}">
                                    {{ $product['platform'] == 'shein' ? 'SHEIN' : 'Lazada' }}
                                </div>
                            </div>
                            
                            <div class="p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-900 line-clamp-2" title="{{ $product['name'] }}">
                                            {{ $product['name'] }}
                                        </h3>
                                        
                                        <!-- Price Section -->
                                        <div class="mt-2">
                                            <p class="text-lg font-bold {{ $product['platform'] == 'shein' ? 'text-pink-600' : 'text-orange-600' }}">
                                                {{ $product['price'] }}
                                            </p>
                                            @if(!empty($product['original_price']) && $product['original_price'] != $product['price'])
                                                <p class="text-sm text-gray-400 line-through">
                                                    {{ $product['original_price'] }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Store Badge -->
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded whitespace-nowrap ml-2">
                                        {{ $product['store'] }}
                                    </span>
                                </div>
                                
                                <!-- Additional Info -->
                                @if(!empty($product['rating']) || !empty($product['sold']))
                                    <div class="flex items-center text-sm text-gray-500 mt-2">
                                        @if(!empty($product['rating']))
                                            <span class="flex items-center mr-3">
                                                ⭐ {{ $product['rating'] }}
                                            </span>
                                        @endif
                                        @if(!empty($product['sold']))
                                            <span>• {{ $product['sold'] }}</span>
                                        @endif
                                    </div>
                                @endif
                                
                                <!-- Action Buttons -->
                                <div class="mt-4 space-y-2">
                                    <!-- Add to Wardrobe Button -->
                                    <form action="{{ route('wardrobe.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="name" value="{{ $product['name'] }}">
                                        <input type="hidden" name="brand" value="{{ $product['store'] }}">
                                        <input type="hidden" name="price" value="{{ $product['price'] }}">
                                        <input type="hidden" name="purchase_link" value="{{ $product['link'] ?? '#' }}">
                                        <input type="hidden" name="image_url" value="{{ $product['image'] ?? '' }}">
                                        <input type="hidden" name="source" value="online_recommendation">
                                        <input type="hidden" name="category" value="{{ $category ?? 'online' }}">
                                        <input type="hidden" name="color" value="varies">
                                        <input type="hidden" name="occasion" value="{{ $keyword ?? 'general' }}">
                                        <input type="hidden" name="platform" value="{{ $product['platform'] }}">
                                        <button type="submit"
                                                class="w-full px-3 py-2 bg-blue-50 text-blue-700 text-sm rounded hover:bg-blue-100 transition flex items-center justify-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                            </svg>
                                            Save to Wardrobe
                                        </button>
                                    </form>

                                    <!-- Add to Cart & Buy Now Row -->
                                    <div class="flex space-x-2">
                                        <!-- Add to Cart Button -->
                                        <form action="{{ route('recommendation.add.to.cart') }}" method="POST" class="flex-1">
                                            @csrf
                                            <input type="hidden" name="product_name" value="{{ $product['name'] }}">
                                            <input type="hidden" name="price" value="{{ $product['price'] }}">
                                            <input type="hidden" name="store" value="{{ $product['store'] }}">
                                            <input type="hidden" name="link" value="{{ $product['link'] ?? '#' }}">
                                            <input type="hidden" name="image" value="{{ $product['image'] ?? '' }}">
                                            <input type="hidden" name="platform" value="{{ $product['platform'] }}">
                                            
                                            <button type="submit"
                                                    class="w-full px-3 py-2 bg-purple-50 text-purple-700 text-sm rounded hover:bg-purple-100 transition flex items-center justify-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                </svg>
                                                Add to Cart
                                            </button>
                                        </form>

                                        <!-- Buy Now Button -->
                                        @if(!empty($product['link']) && $product['link'] != '#')
                                            <a href="{{ $product['link'] }}" 
                                               target="_blank"
                                               class="flex-1 px-3 py-2 bg-gradient-to-r 
                                               {{ $product['platform'] == 'shein' ? 'from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700' : 'from-red-600 to-red-700 hover:from-red-700 hover:to-red-800' }} 
                                               text-white text-sm rounded-lg transition flex items-center justify-center font-medium">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                                </svg>
                                                Buy Now
                                            </a>
                                        @else
                                            <form action="{{ route('recommendation.simulate.purchase') }}" method="POST" class="flex-1">
                                                @csrf
                                                <input type="hidden" name="product_name" value="{{ $product['name'] }}">
                                                <input type="hidden" name="price" value="{{ $product['price'] }}">
                                                <input type="hidden" name="store" value="{{ $product['store'] }}">
                                                <input type="hidden" name="link" value="{{ $product['link'] ?? '#' }}">
                                                <input type="hidden" name="image" value="{{ $product['image'] ?? '' }}">
                                                <input type="hidden" name="platform" value="{{ $product['platform'] }}">
                                                
                                                <button type="submit"
                                                        class="w-full px-3 py-2 bg-gradient-to-r 
                                                        {{ $product['platform'] == 'shein' ? 'from-pink-500 to-purple-500 hover:from-pink-600 hover:to-purple-600' : 'from-orange-600 to-orange-700 hover:from-orange-700 hover:to-orange-800' }} 
                                                        text-white text-sm rounded-lg transition flex items-center justify-center font-medium">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                                    </svg>
                                                    Simulate Purchase
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Platform Stats (if showing all platforms) -->
                @if($currentPlatform == 'all' && count($products) > 0)
                    @php
                        $sheinCount = count(array_filter($products, function($p) {
                            return ($p['platform'] ?? '') == 'shein';
                        }));
                        $lazadaCount = count(array_filter($products, function($p) {
                            return ($p['platform'] ?? '') == 'lazada';
                        }));
                    @endphp
                    
                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Platform Distribution</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center p-4 bg-pink-50 rounded-lg">
                                <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center mr-4">
                                    <span class="text-pink-600 font-bold">S</span>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">SHEIN Malaysia</h4>
                                    <p class="text-sm text-gray-600">{{ $sheinCount }} products found</p>
                                </div>
                            </div>
                            <div class="flex items-center p-4 bg-orange-50 rounded-lg">
                                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-4">
                                    <span class="text-orange-600 font-bold">L</span>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Lazada Malaysia</h4>
                                    <p class="text-sm text-gray-600">{{ $lazadaCount }} products found</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
            @else
                <!-- No Products Found -->
                <div class="text-center py-12">
                    <div class="w-24 h-24 mx-auto mb-4 rounded-full 
                        {{ $currentPlatform == 'shein' ? 'bg-pink-100' : 'bg-orange-100' }} 
                        flex items-center justify-center">
                        @if($currentPlatform == 'shein')
                            <span class="text-3xl">🛍️</span>
                        @else
                            <svg class="w-12 h-12 {{ $currentPlatform == 'shein' ? 'text-pink-400' : 'text-orange-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @endif
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        No products found on 
                        @if($currentPlatform == 'shein')
                            SHEIN
                        @elseif($currentPlatform == 'lazada')
                            Lazada
                        @else
                            these platforms
                        @endif
                    </h3>
                    <p class="text-gray-600 mb-4">Try a different search term or category</p>
                    <div class="space-x-3">
                        <button onclick="document.getElementById('keyword').value='dress'; document.forms[0].submit()"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            Search for "dress"
                        </button>
                        <a href="{{ route('recommendation.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                            Back to Recommendations
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Updated Disclaimer -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-8">
            <div class="flex">
                <svg class="w-6 h-6 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h4 class="font-medium text-blue-800">Real-time Data Collection</h4>
                    <p class="text-sm text-blue-700 mt-1">
                        This FYP system performs <strong>real web scraping from SHEIN Malaysia and Lazada Malaysia</strong>. 
                        Products are fetched live from their search results. When real scraping fails 
                        (due to website changes or blocking), the system automatically shows simulated 
                        products for demonstration purposes.
                    </p>
                    <div class="mt-3 text-xs text-blue-600">
                        <span class="inline-flex items-center mr-4">
                            <span class="w-3 h-3 bg-green-500 rounded-full mr-1"></span> Live: Real scraped data
                        </span>
                        <span class="inline-flex items-center">
                            <span class="w-3 h-3 bg-yellow-500 rounded-full mr-1"></span> Demo: Simulated data
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-center space-x-4">
            <a href="{{ route('recommendation.index') }}" 
               class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Search
            </a>
            <a href="{{ route('recommendation.cart') }}" 
               class="px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg hover:from-purple-700 hover:to-purple-800 transition font-medium flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                View Cart ({{ auth()->user()->shoppingItems()->where('status', 'wishlist')->count() }})
            </a>
        </div>
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Animation for product cards */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.product-card {
    animation: fadeInUp 0.5s ease-out;
    animation-fill-mode: both;
}

/* Staggered animation delays */
.product-card:nth-child(1) { animation-delay: 0.1s; }
.product-card:nth-child(2) { animation-delay: 0.2s; }
.product-card:nth-child(3) { animation-delay: 0.3s; }
.product-card:nth-child(4) { animation-delay: 0.4s; }
.product-card:nth-child(5) { animation-delay: 0.5s; }
.product-card:nth-child(6) { animation-delay: 0.6s; }
.product-card:nth-child(7) { animation-delay: 0.7s; }
.product-card:nth-child(8) { animation-delay: 0.8s; }
</style>

<script>
// Add animation classes to product cards
document.addEventListener('DOMContentLoaded', function() {
    const productCards = document.querySelectorAll('.border.border-gray-200.rounded-lg');
    productCards.forEach((card, index) => {
        card.classList.add('product-card');
        card.style.animationDelay = `${(index % 8) * 0.1}s`;
    });
});

// Platform switching
function switchPlatform(platform) {
    const url = new URL(window.location.href);
    url.searchParams.set('platform', platform);
    window.location.href = url.toString();
}

// Quick search
function quickSearch(keyword) {
    document.getElementById('keyword').value = keyword;
    document.forms[0].submit();
}
</script>
@endsection
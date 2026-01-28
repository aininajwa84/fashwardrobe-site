@extends('layouts.app')

@section('title', 'Online Search - Smart Wardrobe')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">🔍 Advanced Online Search</h1>
            <p class="text-gray-600">Real-time products from Lazada & Shopee Malaysia</p>
        </div>
        
        <!-- Search Form -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <form action="{{ route('recommendation.search.online') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Keyword -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" 
                               name="keyword" 
                               value="{{ $keyword ?? '' }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               placeholder="e.g., dress, shirt, shoes" required>
                    </div>
                    
                    <!-- Platform -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Platform</label>
                        <select name="platform" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="all" {{ ($platform ?? 'all') == 'all' ? 'selected' : '' }}>All Platforms</option>
                            <option value="lazada" {{ ($platform ?? '') == 'lazada' ? 'selected' : '' }}>Lazada Only</option>
                            <option value="shopee" {{ ($platform ?? '') == 'shopee' ? 'selected' : '' }}>Shopee Only</option>
                        </select>
                    </div>
                    
                    <!-- Sort By -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                        <select name="sort_by" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="popular" {{ ($sort_by ?? 'popular') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                            <option value="price_asc" {{ ($sort_by ?? '') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_desc" {{ ($sort_by ?? '') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="rating" {{ ($sort_by ?? '') == 'rating' ? 'selected' : '' }}>Highest Rating</option>
                        </select>
                    </div>
                    
                    <!-- Search Button -->
                    <div class="flex items-end">
                        <button type="submit" 
                                class="w-full px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition flex items-center justify-center font-medium">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Search Now
                        </button>
                    </div>
                </div>
                
                <!-- Advanced Filters -->
                <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Min Price (RM)</label>
                        <input type="number" 
                               name="min_price" 
                               value="{{ $filters['min_price'] ?? '' }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                               placeholder="0" min="0">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max Price (RM)</label>
                        <input type="number" 
                               name="max_price" 
                               value="{{ $filters['max_price'] ?? '' }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                               placeholder="1000" min="0">
                    </div>
                    
                    <div class="flex items-end">
                        <button type="button" 
                                onclick="clearFilters()"
                                class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                            Clear Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Results Summary -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Found {{ count($products) }} products 
                    @if(isset($keyword))
                        for "{{ $keyword }}"
                    @endif
                </h2>
                <p class="text-sm text-gray-500">
                    Scraped in real-time from {{ $platform == 'all' ? 'Lazada & Shopee' : ucfirst($platform) }}
                    • Last updated: {{ now()->format('h:i A') }}
                </p>
            </div>
            
            <div class="flex items-center space-x-2">
                <!-- Live Status Indicator -->
                <div class="flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                    Live Scraping
                </div>
                
                <!-- Refresh Button -->
                <button onclick="refreshResults()"
                        class="px-3 py-1 border border-gray-300 rounded-lg hover:bg-gray-50 transition flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Refresh
                </button>
            </div>
        </div>
        
        <!-- Products Grid -->
        @if(count($products) > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($products as $product)
                    @include('components.product-card', ['product' => $product])
                @endforeach
            </div>
        @else
            <!-- No Results -->
            <div class="bg-white rounded-2xl shadow-xl p-12 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-medium text-gray-900 mb-2">No products found</h3>
                <p class="text-gray-600 mb-6">Try a different search term or check your filters</p>
                <div class="space-x-3">
                    <a href="{{ route('recommendation.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Back to Recommendations
                    </a>
                    <button onclick="document.querySelector('input[name=keyword]').value='clothing'; document.forms[0].submit()"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Try "clothing"
                    </button>
                </div>
            </div>
        @endif
        
        <!-- Platform Comparison -->
        @if($platform == 'all' && count($products) > 0)
            <div class="mt-12 bg-white rounded-2xl shadow-xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Platform Comparison</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Lazada Stats -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center mb-3">
                            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-orange-600 font-bold">L</span>
                            </div>
                            <h4 class="font-medium text-gray-900">Lazada Malaysia</h4>
                        </div>
                        <div class="space-y-2">
                            @php
                                $lazadaProducts = array_filter($products, function($p) {
                                    return strtolower($p['platform'] ?? '') == 'lazada';
                                });
                                $lazadaCount = count($lazadaProducts);
                                $avgPrice = $this->calculateAveragePrice($lazadaProducts);
                            @endphp
                            <div class="flex justify-between">
                                <span class="text-gray-600">Products Found:</span>
                                <span class="font-medium">{{ $lazadaCount }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Average Price:</span>
                                <span class="font-medium">RM{{ number_format($avgPrice, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status:</span>
                                <span class="text-green-600 font-medium">✅ Live</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Shopee Stats -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center mb-3">
                            <div class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-white font-bold">S</span>
                            </div>
                            <h4 class="font-medium text-gray-900">Shopee Malaysia</h4>
                        </div>
                        <div class="space-y-2">
                            @php
                                $shopeeProducts = array_filter($products, function($p) {
                                    return strtolower($p['platform'] ?? '') == 'shopee';
                                });
                                $shopeeCount = count($shopeeProducts);
                                $avgPrice = $this->calculateAveragePrice($shopeeProducts);
                            @endphp
                            <div class="flex justify-between">
                                <span class="text-gray-600">Products Found:</span>
                                <span class="font-medium">{{ $shopeeCount }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Average Price:</span>
                                <span class="font-medium">RM{{ number_format($avgPrice, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status:</span>
                                <span class="text-green-600 font-medium">✅ Live</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Disclaimer -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-xl p-6">
            <div class="flex">
                <svg class="w-6 h-6 text-blue-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h4 class="font-medium text-blue-800">Web Scraping Notice</h4>
                    <p class="text-sm text-blue-700 mt-1">
                        This system performs <strong>real-time web scraping</strong> from Lazada and Shopee Malaysia.
                        Products are fetched directly from the platforms' search results. 
                        <span class="block mt-1">
                            <span class="inline-flex items-center mr-3">
                                <span class="w-3 h-3 bg-green-500 rounded-full mr-1"></span> Real scraped data
                            </span>
                            <span class="inline-flex items-center">
                                <span class="w-3 h-3 bg-yellow-500 rounded-full mr-1"></span> Fallback data (when scraping fails)
                            </span>
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function refreshResults() {
    const searchForm = document.querySelector('form');
    searchForm.submit();
}

function clearFilters() {
    document.querySelector('input[name="min_price"]').value = '';
    document.querySelector('input[name="max_price"]').value = '';
    document.querySelector('select[name="sort_by"]').value = 'popular';
    document.querySelector('select[name="platform"]').value = 'all';
}

// Auto-refresh every 5 minutes
setTimeout(() => {
    if(confirm('Refresh results to get latest products?')) {
        refreshResults();
    }
}, 300000); // 5 minutes
</script>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.product-card {
    animation: fadeIn 0.5s ease-out;
    animation-fill-mode: both;
}

.product-card:nth-child(1) { animation-delay: 0.1s; }
.product-card:nth-child(2) { animation-delay: 0.2s; }
.product-card:nth-child(3) { animation-delay: 0.3s; }
.product-card:nth-child(4) { animation-delay: 0.4s; }
.product-card:nth-child(5) { animation-delay: 0.5s; }
.product-card:nth-child(6) { animation-delay: 0.6s; }
</style>
@endsection
@php
    $product = (object) $product;
    $isSimulated = $product->is_simulated ?? false;
    $platform = strtolower($product->platform ?? 'lazada');
@endphp

<div class="product-card border border-gray-200 rounded-lg overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 {{ $isSimulated ? 'border-dashed opacity-90' : '' }}">
    <!-- Platform Badge -->
    <div class="absolute top-3 left-3 z-10">
        <span class="px-2 py-1 text-xs font-medium rounded-full 
            {{ $platform == 'lazada' ? 'bg-orange-100 text-orange-800' : 'bg-orange-500 text-white' }}">
            {{ ucfirst($platform) }}
        </span>
    </div>
    
    <!-- Live/Demo Badge -->
    @if($isSimulated)
        <div class="absolute top-3 right-3 px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full font-medium">
            Demo
        </div>
    @else
        <div class="absolute top-3 right-3 px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium">
            Live
        </div>
    @endif
    
    <!-- Product Image -->
    <div class="w-full h-48 bg-gray-100 overflow-hidden relative">
        @if(!empty($product->image))
            <img src="{{ $product->image }}" 
                 alt="{{ $product->name }}"
                 class="w-full h-full object-cover hover:scale-110 transition duration-500"
                 onerror="this.src='https://via.placeholder.com/300x300/F5F5F5/666666?text={{ urlencode(substr($product->name, 0, 20)) }}'">
        @else
            <div class="w-full h-full bg-gradient-to-r from-gray-100 to-gray-200 flex items-center justify-center">
                <svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"/>
                </svg>
            </div>
        @endif
        
        <!-- Discount Badge -->
        @if(!empty($product->original_price) && $product->original_price != $product->price)
            <div class="absolute bottom-3 left-3 bg-red-500 text-white text-xs px-2 py-1 rounded">
                SAVE {{ calculateDiscount($product->price, $product->original_price) }}%
            </div>
        @endif
    </div>
    
    <!-- Product Info -->
    <div class="p-4">
        <!-- Name -->
        <h3 class="font-medium text-gray-900 line-clamp-2 mb-2" title="{{ $product->name }}">
            {{ $product->name }}
        </h3>
        
        <!-- Store -->
        <div class="flex items-center text-sm text-gray-500 mb-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            {{ $product->store }}
        </div>
        
        <!-- Price -->
        <div class="mb-3">
            <div class="flex items-center">
                <span class="text-lg font-bold text-red-600">
                    {{ $product->price }}
                </span>
                @if(!empty($product->original_price) && $product->original_price != $product->price)
                    <span class="ml-2 text-sm text-gray-400 line-through">
                        {{ $product->original_price }}
                    </span>
                @endif
            </div>
        </div>
        
        <!-- Rating & Sold -->
        <div class="flex items-center justify-between text-sm mb-4">
            @if(!empty($product->rating))
                <div class="flex items-center">
                    <span class="text-yellow-400">★</span>
                    <span class="ml-1 text-gray-700">{{ $product->rating }}</span>
                </div>
            @endif
            
            @if(!empty($product->sold))
                <div class="text-gray-600">
                    {{ $product->sold }}
                </div>
            @endif
        </div>
        
        <!-- Action Buttons -->
        <div class="flex space-x-2">
            <!-- Add to Cart -->
            <form action="{{ route('recommendation.add.to.cart') }}" method="POST" class="flex-1">
                @csrf
                <input type="hidden" name="product_name" value="{{ $product->name }}">
                <input type="hidden" name="price" value="{{ $product->price }}">
                <input type="hidden" name="store" value="{{ $product->store }}">
                <input type="hidden" name="link" value="{{ $product->link ?? '#' }}">
                <input type="hidden" name="image" value="{{ $product->image ?? '' }}">
                <input type="hidden" name="platform" value="{{ $platform }}">
                
                <button type="submit"
                        class="w-full px-3 py-2 bg-blue-50 text-blue-700 text-sm rounded hover:bg-blue-100 transition flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Add to Cart
                </button>
            </form>
            
            <!-- View Product -->
            @if(!empty($product->link) && $product->link != '#')
                <a href="{{ $product->link }}" 
                   target="_blank"
                   class="flex-1 px-3 py-2 border border-gray-300 text-gray-700 text-sm rounded hover:bg-gray-50 transition flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    View
                </a>
            @endif
        </div>
    </div>
</div>

@php
    function calculateDiscount($currentPrice, $originalPrice) {
        $current = preg_replace('/[^0-9.]/', '', $currentPrice);
        $original = preg_replace('/[^0-9.]/', '', $originalPrice);
        
        if($original > 0 && $current > 0) {
            $discount = (($original - $current) / $original) * 100;
            return round($discount);
        }
        return 0;
    }
@endphp
{{-- resources/views/recommendation/result.blade.php --}}
@extends('layouts.app')

@section('title', 'Recommendation Results - Smart Wardrobe')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-green-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Recommendation Results</h1>
            <p class="text-gray-600">Found {{ $items->count() }} matching items in your wardrobe</p>
        </div>

        <!-- Results -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-800">Matching Items</h2>
                <a href="{{ route('recommendation.index') }}" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    New Search
                </a>
            </div>

            @if($items->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($items as $item)
                        <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition">
                            @if($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" 
                                     alt="{{ $item->name }}" 
                                     class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-400">No Image</span>
                                </div>
                            @endif
                            
                            <div class="p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-medium text-gray-900">{{ $item->name ?: 'Unnamed Item' }}</h3>
                                        <p class="text-sm text-gray-600">{{ $item->category }} • {{ $item->color }}</p>
                                    </div>
                                    @if($item->is_favorite)
                                        <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endif
                                </div>
                                
                                @if($item->theme)
                                    <div class="mt-2">
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">Theme: {{ $item->theme }}</span>
                                    </div>
                                @endif
                                
                                @if($item->occasion)
                                    <div class="mt-2">
                                        <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded">Occasion: {{ $item->occasion }}</span>
                                    </div>
                                @endif
                                
                                @if($item->notes)
                                    <p class="mt-2 text-sm text-gray-700">{{ Str::limit($item->notes, 50) }}</p>
                                @endif
                                
                                <div class="mt-4 flex justify-between items-center">
                                    <span class="text-sm text-gray-500">{{ $item->season }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    
                    <h3 class="text-xl font-bold text-gray-700 mb-3">No items found for "{{ $searchTheme ?? 'formal' }}"</h3>
                    <p class="text-gray-600 mb-8 max-w-md mx-auto">
                        But don't worry! Our Telegram bot can search Zalora for you.
                    </p>

                    <!-- ========== TELEGRAM BOT INTEGRATION ========== -->
                    <div class="max-w-lg mx-auto bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200 rounded-xl p-8 mb-8 shadow-sm">
                        <div class="flex flex-col items-center">
                            <!-- Telegram Logo -->
                            <div class="w-20 h-20 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center mb-6 shadow-md">
                                <i class="fab fa-telegram text-white text-3xl"></i>
                            </div>
                            
                            <h4 class="text-2xl font-bold text-gray-800 mb-2">Search with Telegram Bot</h4>
                            <p class="text-gray-600 text-center mb-6">
                                Our working Telegram bot will find <strong>{{ $searchTheme ?? 'formal' }}</strong> outfits from Zalora and send them directly to your Telegram.
                            </p>
                            
                            <!-- TELEGRAM DIRECT LINK -->
                            @php
                                $botUsername = 'smartwardrobe_search_bot'; //
                                $theme = $searchTheme ?? 'formal';
                                $telegramDirectLink = "https://t.me/$botUsername?start=$theme";
                                
                                // Web version as fallback
                                $telegramWebLink = "https://web.telegram.org/k/#@$botUsername";
                            @endphp
                            
                            <a href="{{ $telegramDirectLink }}" 
                               target="_blank"
                               class="inline-flex items-center justify-center px-6 py-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white font-bold rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 mb-4 w-full max-w-sm">
                                <i class="fab fa-telegram text-2xl mr-3"></i>
                                <div class="text-left">
                                    <div class="font-bold text-lg">Search on Telegram</div>
                                    <div class="text-blue-100 text-sm font-normal">Click to open @{{ Smart Wardrobe Bot }}</div>
                                </div>
                            </a>
                            
                            <!-- OR SCAN QR CODE -->
                            <div class="text-center mt-6">
                                <p class="text-gray-500 text-sm mb-3">Or scan QR code with your phone:</p>
                                <div class="inline-block p-4 bg-white border border-gray-300 rounded-lg">
                                    <!-- QR code with t.me link - opens Telegram app directly on mobile -->
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($telegramDirectLink) }}" 
                                         alt="Scan to open Telegram" 
                                         class="w-48 h-48">
                                </div>
                                <p class="text-gray-500 text-xs mt-2">Scanning opens Telegram app directly (if installed)</p>
                            </div>
                            
                            <!-- Instructions -->
                            <div class="mt-8 p-4 bg-white border border-blue-100 rounded-lg">
                                <h5 class="font-bold text-gray-800 mb-2">How it works:</h5>
                                <ol class="text-gray-600 text-sm space-y-2">
                                    <li>1. Click the button above or scan QR code</li>
                                    <li>2. Your Telegram app will open with our bot</li>
                                    <li>3. Bot will automatically search for "{{ $searchTheme ?? 'formal' }}" outfits</li>
                                    <li>4. Browse real products from Zalora</li>
                                    <li>5. Click products to view on Zalora website</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <!-- ========== END TELEGRAM INTEGRATION ========== -->
                    
                    <!-- Alternative Manual Search -->
                    <div class="mt-8">
                        <p class="text-gray-500 text-sm mb-4">Or search manually:</p>
                        <div class="flex justify-center space-x-4">
                            <a href="https://www.zalora.com.my/catalog/?q={{ urlencode($searchTheme ?? 'formal') }}" 
                               target="_blank" 
                               class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                                <i class="fas fa-external-link-alt mr-2"></i> Zalora
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-center space-x-4">
            <a href="{{ route('recommendation.index') }}" 
               class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                Back to Search
            </a>
            <a href="{{ route('wardrobe.index') }}" 
               class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition font-medium">
                View All Items
            </a>
        </div>
    </div>
</div>

<!-- Font Awesome for Telegram icon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    /* Smooth hover effect */
    a {
        transition: all 0.3s ease;
    }
</style>
@endsection
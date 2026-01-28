{{-- resources/views/recommendation/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Recommendations - Smart Wardrobe')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-purple-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Smart Recommendations</h1>
            <p class="text-gray-600">Get outfit suggestions based on your wardrobe and preferences</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Recommendation Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6">Generate Recommendations</h2>
                    
                    <form action="{{ route('recommendation.generate') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Theme/Event -->
                            <div>
                                <label for="theme" class="block text-sm font-medium text-gray-700 mb-2">
                                    Event / Theme *
                                </label>
                                <select id="theme" 
                                        name="theme" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white"
                                        required>
                                    <option value="">Select Theme</option>
                                    <option value="casual">Casual Day Out</option>
                                    <option value="formal">Formal Event</option>
                                    <option value="work">Work/Office</option>
                                    <option value="party">Party/Night Out</option>
                                    <option value="wedding">Wedding</option>
                                    <option value="beach">Beach/Outdoor</option>
                                    <option value="date">Date Night</option>
                                    <option value="sport">Sport/Exercise</option>
                                    <option value="travel">Travel</option>
                                </select>
                            </div>

                            <!-- Color Preference -->
                            <div>
                                <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                                    Color Preference
                                </label>
                                <select id="color" 
                                        name="color" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white">
                                    <option value="">Any Color</option>
                                    @foreach(App\Models\Wardrobe::colors() as $key => $color)
                                        <option value="{{ $key }}">{{ $color }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Category -->
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                    Item Type
                                </label>
                                <select id="category" 
                                        name="category" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white">
                                    <option value="">All Types</option>
                                    @foreach(App\Models\Wardrobe::categories() as $key => $category)
                                        <option value="{{ $key }}">{{ $category }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Additional Preferences -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Additional Preferences
                            </label>
                            <div class="flex flex-wrap gap-3">
                                @foreach(['favorite' => 'Favorite Items Only', 'new' => 'Recently Added', 'unused' => 'Rarely Worn'] as $key => $label)
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" 
                                               name="preferences[]" 
                                               value="{{ $key }}"
                                               class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                        <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Generate Button -->
                        <div class="mt-8">
                            <button type="submit" 
                                    class="w-full px-6 py-4 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg hover:from-purple-700 hover:to-purple-800 transition duration-300 font-medium shadow-lg hover:shadow-xl flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Generate Recommendations
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Recent Recommendations -->
                <div class="mt-8 bg-white rounded-2xl shadow-xl p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6">Recent Recommendations</h2>
                    <div class="space-y-4">
                        @for($i = 1; $i <= 3; $i++)
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="font-medium text-gray-900">Casual Weekend Outfit</h3>
                                        <p class="text-sm text-gray-600">Generated 2 days ago</p>
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">Top</span>
                                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">Bottom</span>
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded">Shoes</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            <!-- Right Column - Tips & Stats -->
            <div>
                <!-- Tips Card -->
                <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Tips for Better Recommendations</h2>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm text-gray-700">Upload clear photos with good lighting</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm text-gray-700">Tag items with accurate categories and colors</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm text-gray-700">Mark your favorite items for quick access</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm text-gray-700">Update item details regularly</span>
                        </li>
                    </ul>
                </div>

                <!-- Stats Card -->
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-xl p-6 text-white">
                    <h2 class="text-xl font-semibold mb-6">Your Wardrobe Stats</h2>
                    <div class="space-y-4">
                        @php
                            $wardrobeStats = auth()->user()->wardrobes();
                            $totalItems = $wardrobeStats->count();
                            $categories = $wardrobeStats->distinct('category')->count('category');
                        @endphp
                        
                        <div class="flex justify-between items-center">
                            <span>Total Items</span>
                            <span class="text-2xl font-bold">{{ $totalItems }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Categories</span>
                            <span class="text-2xl font-bold">{{ $categories }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Recommendations</span>
                            <span class="text-2xl font-bold">{{ rand(5, 20) }}</span>
                        </div>
                    </div>
                    
                    @if($totalItems > 0)
                        <div class="mt-6 pt-6 border-t border-purple-400">
                            <a href="{{ route('wardrobe.index') }}" 
                               class="block text-center px-4 py-2 bg-white text-purple-600 rounded-lg hover:bg-purple-50 transition font-medium">
                                View All Items
                            </a>
                        </div>
                    @else
                        <div class="mt-6 pt-6 border-t border-purple-400">
                            <a href="{{ route('wardrobe.create') }}" 
                               class="block text-center px-4 py-2 bg-white text-purple-600 rounded-lg hover:bg-purple-50 transition font-medium">
                                Add Your First Item
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex justify-between items-center bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                <div>
                    <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                        {{ __('My Dashboard') }}
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                        👋 Hi {{ Auth::user()->full_name ?? Auth::user()->name }}! • {{ now()->format('l, d F Y') }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-calendar-day mr-1"></i> {{ $today ?? now()->format('F j, Y') }}
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Welcome Section -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold mb-2">Smart Fashion Wardrobe Assistant</h3>
                        <p class="opacity-90">Manage your clothing collection effortlessly and get outfit inspiration!</p>
                    </div>
                    <div class="text-4xl">
                        👕
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Items -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-lg transition border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 mr-4">
                        <i class="fas fa-tshirt text-blue-600 dark:text-blue-300"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Clothing</p>
                        <p class="text-2xl font-bold">{{ $stats['wardrobe_items'] ?? 0 }}</p>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">In your collection</p>
            </div>

            <!-- Outfit Suggestions -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-lg transition border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 dark:bg-green-900 mr-4">
                        <i class="fas fa-lightbulb text-green-600 dark:text-green-300"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Outfit Suggestions</p>
                        <p class="text-2xl font-bold">{{ $stats['outfit_suggestions'] ?? 0 }}</p>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Recommended for you</p>
            </div>

            <!-- Today's Style -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-lg transition border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900 mr-4">
                        <i class="fas fa-sun text-purple-600 dark:text-purple-300"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Today's Style</p>
                        <p class="text-2xl font-bold">{{ ucfirst($occasion_suggestions[0] ?? 'Casual') }}</p>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Perfect for {{ $today ?? 'today' }}</p>
            </div>

            <!-- Favorites -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-lg transition border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900 mr-4">
                        <i class="fas fa-star text-yellow-600 dark:text-yellow-300"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Favorites</p>
                        <p class="text-2xl font-bold">{{ $stats['favorites'] ?? 0 }}</p>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Saved items</p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">
                            <i class="fas fa-rocket mr-2 text-purple-500"></i>What Would You Like To Do?
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <a href="{{ route('wardrobe.index') }}" 
                               class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white p-4 rounded-xl text-center transition shadow-md hover:shadow-lg flex flex-col items-center justify-center">
                                <i class="fas fa-tshirt text-2xl mb-2"></i>
                                <p class="font-medium text-sm">View Wardrobe</p>
                            </a>
                            <a href="{{ route('upload.create') }}" 
                               class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white p-4 rounded-xl text-center transition shadow-md hover:shadow-lg flex flex-col items-center justify-center">
                                <i class="fas fa-plus-circle text-2xl mb-2"></i>
                                <p class="font-medium text-sm">Add New Item</p>
                            </a>
                            <a href="{{ route('recommendation.index') }}" 
                               class="bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white p-4 rounded-xl text-center transition shadow-md hover:shadow-lg flex flex-col items-center justify-center">
                                <i class="fas fa-magic text-2xl mb-2"></i>
                                <p class="font-medium text-sm">Get Inspirations</p>
                            </a>
                            <a href="{{ route('profile.edit') }}" 
                               class="bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white p-4 rounded-xl text-center transition shadow-md hover:shadow-lg flex flex-col items-center justify-center">
                                <i class="fas fa-user-edit text-2xl mb-2"></i>
                                <p class="font-medium text-sm">My Profile</p>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Recent Wardrobe Items -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                <i class="fas fa-history mr-2 text-blue-500"></i>Recent Clothing Items
                            </h3>
                            <a href="{{ route('wardrobe.index') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                View All →
                            </a>
                        </div>
                        
                        @if(isset($recent_items) && $recent_items->count() > 0)
                            <div class="space-y-3">
                                @foreach($recent_items as $item)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition group">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 rounded-lg bg-gradient-to-r from-blue-100 to-blue-200 dark:from-blue-800 dark:to-blue-900 flex items-center justify-center mr-3">
                                            @if($item->image)
                                                <img src="{{ $item->image_url }}" 
                                                     alt="{{ $item->name }}" 
                                                     class="w-full h-full object-cover rounded-lg">
                                            @else
                                                <i class="fas fa-tshirt text-blue-600 dark:text-blue-300 text-lg"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800 dark:text-gray-200 group-hover:text-blue-600">
                                                {{ $item->name ?? 'Clothing Item' }}
                                            </p>
                                            <div class="flex items-center mt-1">
                                                <span class="text-xs px-2 py-1 rounded-full mr-2 
                                                    {{ $item->category == 'top' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : 
                                                       ($item->category == 'bottom' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 
                                                       'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-300') }}">
                                                    {{ $item->category ?? 'Clothing' }}
                                                </span>
                                                <span class="text-xs text-gray-500">{{ $item->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="inline-block p-4 rounded-full bg-blue-100 dark:bg-blue-900 mb-3">
                                    <i class="fas fa-tshirt text-3xl text-blue-600 dark:text-blue-300"></i>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400">Your Collection is Empty</p>
                                <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Get started by adding your first clothing item</p>
                                <a href="{{ route('upload.create') }}" class="inline-block mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    <i class="fas fa-plus mr-1"></i>Add Clothing Item
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-8">
                <!-- Today's Recommendation -->
                <div class="bg-gradient-to-r from-green-500 to-teal-500 overflow-hidden shadow-sm sm:rounded-lg text-white">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-3 flex items-center">
                            <i class="fas fa-lightbulb mr-2"></i>Today's Outfit Ideas
                        </h3>
                        <p class="text-sm opacity-90 mb-4">
                            For {{ $today ?? 'today' }}, Try This Look:
                        </p>
                        <div class="space-y-2">
                            @if(isset($occasion_suggestions) && count($occasion_suggestions) > 0)
                                @foreach($occasion_suggestions as $suggestion)
                                <div class="flex items-center bg-white/20 p-2 rounded-lg">
                                    <i class="fas fa-check-circle mr-2 text-yellow-300"></i>
                                    <span class="text-sm">{{ $suggestion }}</span>
                                </div>
                                @endforeach
                            @else
                                <div class="flex items-center bg-white/20 p-2 rounded-lg">
                                    <i class="fas fa-check-circle mr-2 text-yellow-300"></i>
                                    <span class="text-sm">Casual Chic Look</span>
                                </div>
                                <div class="flex items-center bg-white/20 p-2 rounded-lg">
                                    <i class="fas fa-check-circle mr-2 text-yellow-300"></i>
                                    <span class="text-sm">Comfortable footwear</span>
                                </div>
                                <div class="flex items-center bg-white/20 p-2 rounded-lg">
                                    <i class="fas fa-check-circle mr-2 text-yellow-300"></i>
                                    <span class="text-sm">Light layers</span>
                                </div>
                            @endif
                        </div>
                        <a href="{{ route('recommendation.index') }}" class="inline-block mt-4 px-4 py-2 bg-white text-green-600 rounded-lg font-medium hover:bg-gray-100">
                            See All Recommendations
                        </a>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200 flex items-center">
                            <i class="fas fa-chart-pie mr-2 text-purple-500"></i>Statistics
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">Total Items</span>
                                <span class="font-bold">{{ $stats['wardrobe_items'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">Categories</span>
                                <span class="font-bold text-green-600">{{ $stats['categories'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">Favorites</span>
                                <span class="font-bold text-yellow-600">{{ $stats['favorites'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Weather/Season Info -->
                <div class="bg-gradient-to-r from-blue-500 to-cyan-500 overflow-hidden shadow-sm sm:rounded-lg text-white">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-3 flex items-center">
                            <i class="fas fa-cloud-sun mr-2"></i>Seasonal Tips
                        </h3>
                        <p class="text-sm opacity-90 mb-3">
                            {{ now()->format('F') }} Fashion Advice
                        </p>
                        <div class="space-y-2">
                            <div class="flex items-center bg-white/20 p-2 rounded-lg">
                                <i class="fas fa-umbrella mr-2"></i>
                                <span class="text-sm">Prepare for {{ in_array(now()->month, [11,12,1,2]) ? 'winter' : 'summer' }} weather</span>
                            </div>
                            <div class="flex items-center bg-white/20 p-2 rounded-lg">
                                <i class="fas fa-palette mr-2"></i>
                                <span class="text-sm">{{ in_array(now()->month, [3,4,5]) ? 'Spring' : 'Autumn' }} colors trending</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400">
            <p>Smart Wardrobe • User Dashboard • {{ now()->format('d/m/Y') }}</p>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Custom styles for dashboard */
.bg-gradient-to-r {
    background-size: 200% 200%;
    animation: gradient 3s ease infinite;
}

@keyframes gradient {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.hover\:shadow-lg {
    transition: all 0.3s ease;
}

.transition {
    transition: all 0.3s ease;
}
</style>
@endpush
@extends('layouts.app')

@section('title', 'My Profile - Smart Wardrobe')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <div class="flex items-center mb-4 md:mb-0">
                        <!-- Profile Image -->
                        <div class="relative">
                            @if(auth()->user()->profile_image)
                                <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" 
                                     alt="{{ auth()->user()->full_name }}" 
                                     class="w-20 h-20 rounded-full object-cover border-4 border-white dark:border-gray-700 shadow">
                            @else
                                <div class="w-20 h-20 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 
                                            flex items-center justify-center text-white text-2xl font-bold border-4 border-white dark:border-gray-700 shadow">
                                    {{ substr(auth()->user()->full_name ?? auth()->user()->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        
                        <div class="ml-4">
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                                {{ auth()->user()->full_name ?? auth()->user()->name }}
                            </h2>
                            <p class="text-gray-600 dark:text-gray-400">
                                <i class="fas fa-user-circle mr-1"></i> {{ auth()->user()->username ?? auth()->user()->email }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">
                                Member since {{ auth()->user()->created_at->format('F Y') }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex space-x-3">
                        <a href="{{ route('dashboard') }}" 
                           class="px-4 py-2 border border-gray-300 dark:border-gray-600 
                                  text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span>Please fix the following errors:</span>
                </div>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Profile Forms -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Update Profile Information (Breeze) -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Profile Information
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Update your account's profile information and email address.
                        </p>
                    </header>

                    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                        @csrf
                    </form>

                    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
                        @csrf
                        @method('patch')

                        <!-- Profile Image Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Profile Picture
                            </label>
                            <div class="flex items-center space-x-4">
                                <div class="relative">
                                    @if(auth()->user()->profile_image)
                                        <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" 
                                             alt="Current profile" 
                                             class="w-16 h-16 rounded-full object-cover border-2 border-gray-300">
                                    @endif
                                </div>
                                <div>
                                    <input type="file" 
                                           name="profile_image" 
                                           accept="image/*"
                                           class="block w-full text-sm text-gray-500
                                                  file:mr-4 file:py-2 file:px-4
                                                  file:rounded-full file:border-0
                                                  file:text-sm file:font-semibold
                                                  file:bg-purple-50 file:text-purple-700
                                                  hover:file:bg-purple-100">
                                    <p class="text-xs text-gray-500 mt-1">JPG, PNG or GIF (Max 2MB)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Full Name -->
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Full Name
                            </label>
                            <input type="text" 
                                   id="full_name" 
                                   name="full_name" 
                                   value="{{ old('full_name', auth()->user()->full_name) }}"
                                   class="mt-1 block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 
                                          rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 
                                          dark:bg-gray-700 dark:text-white">
                            @error('full_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Username -->
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Username
                            </label>
                            <input type="text" 
                                   id="username" 
                                   name="username" 
                                   value="{{ old('username', auth()->user()->username) }}"
                                   class="mt-1 block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 
                                          rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 
                                          dark:bg-gray-700 dark:text-white">
                            @error('username')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Email
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $user->email) }}"
                                   class="mt-1 block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 
                                          rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 
                                          dark:bg-gray-700 dark:text-white"
                                   required>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <div class="mt-2">
                                    <p class="text-sm text-gray-800 dark:text-gray-200">
                                        Your email address is unverified.
                                        <button form="send-verification" 
                                                class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                                            Click here to re-send the verification email.
                                        </button>
                                    </p>
                                    @if (session('status') === 'verification-link-sent')
                                        <p class="mt-2 text-sm text-green-600 dark:text-green-400">
                                            A new verification link has been sent to your email address.
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Phone Number
                            </label>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone', $user->phone) }}"
                                   class="mt-1 block w-full px-4 py-2 border border-gray-300 dark:border-gray-600 
                                          rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 
                                          dark:bg-gray-700 dark:text-white">
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit" 
                                    class="px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold 
                                           text-xs text-white uppercase tracking-widest hover:bg-purple-700 
                                           focus:bg-purple-700 active:bg-purple-800 focus:outline-none 
                                           focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 
                                           dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Save Profile
                            </button>

                            @if (session('status') === 'profile-updated')
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Saved.
                                </p>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Update Password (Breeze) -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Right Column - Settings & Stats -->
            <div class="space-y-8">
                <!-- Account Statistics -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Account Statistics
                        </h2>
                    </header>

                    <form method="post" action="{{ route('profile.update.notifications') }}" class="mt-6 space-y-4">
                        @csrf
                        
                        @php
                            $notifications = json_decode(auth()->user()->notification_settings ?? '{}', true);
                        @endphp

                       <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Wardrobe Items</span>
                            <span class="font-bold">{{ auth()->user()->wardrobes()->count() }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Recommendations</span>
                            <span class="font-bold text-purple-600">{{ auth()->user()->recommendations()->count() }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Member For</span>
                            <span class="font-bold">{{ auth()->user()->created_at->diffInDays(now()) }} days</span>
                        </div>
                    </div>
                    </form>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    @include('profile.partials.delete-user-form')
                </div>
                </div>
            </div>  
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Custom toggle switch styles */
.peer:checked ~ .peer-checked\:bg-blue-600 {
    background-color: #2563eb;
}

.peer:checked ~ .peer-checked\:bg-green-600 {
    background-color: #16a34a;
}

.peer:checked ~ .peer-checked\:bg-purple-600 {
    background-color: #9333ea;
}

.dark .peer:checked ~ .peer-checked\:bg-blue-600 {
    background-color: #1d4ed8;
}

.dark .peer:checked ~ .peer-checked\:bg-green-600 {
    background-color: #15803d;
}

.dark .peer:checked ~ .peer-checked\:bg-purple-600 {
    background-color: #7c3aed;
}
</style>
@endpush
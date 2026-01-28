<!-- resources/views/layouts/sidebar.blade.php -->
<aside id="sidebar" 
       class="fixed left-0 top-0 z-40 h-screen w-64 transition-all duration-300 bg-gradient-to-b from-blue-900 via-purple-800 to-blue-900"
       :class="isCollapsed ? '-translate-x-full sm:translate-x-0 sm:w-20' : 'translate-x-0'"
       x-data="{ isCollapsed: false }">
    
    <!-- Toggle Button (inside sidebar) -->
    <div class="absolute -right-3 top-6 z-50 hidden sm:block">
        <button @click="isCollapsed = !isCollapsed" 
                class="p-2 bg-white border border-purple-300 rounded-full shadow-md hover:shadow-lg transition hover:bg-purple-50">
            <i class="fas fa-chevron-left text-purple-600 w-4 h-4 transition-transform duration-300" 
               :class="isCollapsed ? 'rotate-180' : ''"></i>
        </button>
    </div>

    <div class="h-full px-3 py-4 overflow-y-auto" :class="isCollapsed ? 'px-2' : 'px-3'">
        
        <!-- Logo (Collapsible) -->
        <div class="mb-8" :class="isCollapsed ? 'px-0' : 'px-3'">
            <a href="{{ route('dashboard') }}" class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-r from-pink-500 to-purple-500 mr-3 shadow">
                    <i class="fas fa-tshirt text-white text-xl"></i>
                </div>
                <span class="self-center text-xl font-semibold whitespace-nowrap text-white transition-opacity duration-300"
                      :class="isCollapsed ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                    Smart Wardrobe
                </span>
            </a>
        </div>

        <!-- User Profile (Collapsible) -->
        <div class="mb-6" :class="isCollapsed ? 'px-0' : 'px-3'">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-pink-500 to-purple-500 flex items-center justify-center text-white font-bold shadow">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="ml-3 transition-all duration-300 overflow-hidden" 
                     :class="isCollapsed ? 'opacity-0 w-0' : 'opacity-100 w-auto'">
                    <p class="font-medium text-white truncate">{{ Auth::user()->full_name ?? Auth::user()->name }}</p>
                    <p class="text-xs text-purple-200 truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <ul class="space-y-2 font-medium">
            <!-- Dashboard -->
            <li>
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center p-2 text-white rounded-lg hover:bg-purple-700 group transition {{ request()->routeIs('dashboard') ? 'bg-purple-700' : '' }}"
                   :title="isCollapsed ? 'Dashboard' : ''">
                    <i class="fas fa-home w-5 h-5 text-purple-300 transition duration-75 group-hover:text-white"></i>
                    <span class="ms-3 transition-all duration-300 overflow-hidden whitespace-nowrap"
                          :class="isCollapsed ? 'opacity-0 w-0' : 'opacity-100 w-auto'">
                        Dashboard
                    </span>
                </a>
            </li>

            <!-- My Wardrobe -->
            <li>
                <a href="{{ route('wardrobe.index') }}" 
                   class="flex items-center p-2 text-white rounded-lg hover:bg-purple-700 group transition {{ request()->routeIs('wardrobe.*') ? 'bg-purple-700' : '' }}"
                   :title="isCollapsed ? 'My Wardrobe' : ''">
                    <i class="fas fa-tshirt w-5 h-5 text-purple-300 transition duration-75 group-hover:text-white"></i>
                    <span class="ms-3 transition-all duration-300 overflow-hidden whitespace-nowrap"
                          :class="isCollapsed ? 'opacity-0 w-0' : 'opacity-100 w-auto'">
                        My Wardrobe
                    </span>
                </a>
            </li>

            <!-- Upload Photo -->
            <li>
                <a href="{{ route('upload.create') }}" 
                   class="flex items-center p-2 text-white rounded-lg hover:bg-purple-700 group transition"
                   :title="isCollapsed ? 'Upload Photo' : ''">
                    <i class="fas fa-camera w-5 h-5 text-purple-300 transition duration-75 group-hover:text-white"></i>
                    <span class="ms-3 transition-all duration-300 overflow-hidden whitespace-nowrap"
                          :class="isCollapsed ? 'opacity-0 w-0' : 'opacity-100 w-auto'">
                        Upload Photo
                    </span>
                </a>
            </li>

            <!-- Recommendation -->
            <li>
                <a href="{{ route('recommendation.index') }}" 
                   class="flex items-center p-2 text-white rounded-lg hover:bg-purple-700 group transition {{ request()->routeIs('recommendation.*') ? 'bg-purple-700' : '' }}"
                   :title="isCollapsed ? 'Recommendation' : ''">
                    <i class="fas fa-lightbulb w-5 h-5 text-purple-300 transition duration-75 group-hover:text-white"></i>
                    <span class="ms-3 transition-all duration-300 overflow-hidden whitespace-nowrap"
                          :class="isCollapsed ? 'opacity-0 w-0' : 'opacity-100 w-auto'">
                        Recommendation
                    </span>
                </a>
            </li>

            <!-- Profile -->
            <li>
                <a href="{{ route('profile.edit') }}" 
                   class="flex items-center p-2 text-white rounded-lg hover:bg-purple-700 group transition {{ request()->routeIs('profile.*') ? 'bg-purple-700' : '' }}"
                   :title="isCollapsed ? 'Profile' : ''">
                    <i class="fas fa-user w-5 h-5 text-purple-300 transition duration-75 group-hover:text-white"></i>
                    <span class="ms-3 transition-all duration-300 overflow-hidden whitespace-nowrap"
                          :class="isCollapsed ? 'opacity-0 w-0' : 'opacity-100 w-auto'">
                        Profile
                    </span>
                </a>
            </li>

        <!-- Logout Button (Bottom) - RED VERSION -->
        <div class="absolute bottom-4 left-0 right-0" :class="isCollapsed ? 'px-2' : 'px-3'">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" 
                        class="flex items-center w-full p-2 text-white rounded-lg hover:bg-red-600 bg-red-500 group transition shadow hover:shadow-md"
                        :title="isCollapsed ? 'Log Out' : ''">
                    <i class="fas fa-sign-out-alt w-5 h-5 text-white"></i>
                    <span class="ms-3 transition-all duration-300 overflow-hidden whitespace-nowrap font-medium"
                          :class="isCollapsed ? 'opacity-0 w-0' : 'opacity-100 w-auto'">
                        Log Out
                    </span>
                </button>
            </form>
        </div>
    </div>
</aside>
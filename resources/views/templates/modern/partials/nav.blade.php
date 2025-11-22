<nav x-data="{ open: false }" class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('tenant.home') }}" class="flex items-center">
                    @if($site?->logo_path)
                        <img src="{{ Storage::url($site->logo_path) }}" alt="{{ $site->site_name }}" class="h-10">
                    @else
                        <span class="text-xl font-bold text-primary">{{ $site?->site_name ?? $tenant->name }}</span>
                    @endif
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('tenant.home') }}" class="text-gray-700 hover:text-primary transition {{ request()->routeIs('tenant.home') ? 'text-primary font-semibold' : '' }}">
                    Home
                </a>
                <a href="{{ route('tenant.properties') }}" class="text-gray-700 hover:text-primary transition {{ request()->routeIs('tenant.properties*') ? 'text-primary font-semibold' : '' }}">
                    Properties
                </a>
                <a href="{{ route('tenant.about') }}" class="text-gray-700 hover:text-primary transition {{ request()->routeIs('tenant.about') ? 'text-primary font-semibold' : '' }}">
                    About
                </a>
                <a href="{{ route('tenant.contact') }}" class="px-4 py-2 bg-primary text-white rounded-lg hover:opacity-90 transition">
                    Contact
                </a>
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden flex items-center">
                <button @click="open = !open" class="text-gray-700 hover:text-primary">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="open" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation -->
    <div x-show="open" x-cloak class="md:hidden bg-white border-t">
        <div class="px-4 py-3 space-y-3">
            <a href="{{ route('tenant.home') }}" class="block text-gray-700 hover:text-primary">Home</a>
            <a href="{{ route('tenant.properties') }}" class="block text-gray-700 hover:text-primary">Properties</a>
            <a href="{{ route('tenant.about') }}" class="block text-gray-700 hover:text-primary">About</a>
            <a href="{{ route('tenant.contact') }}" class="block text-gray-700 hover:text-primary">Contact</a>
        </div>
    </div>
</nav>

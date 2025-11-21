<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-24">
            <div class="flex items-center">
                <a href="{{ route('tenant.home') }}" class="flex items-center">
                    @if($site?->logo)
                        <img src="{{ Storage::url($site->logo) }}" alt="{{ $site->business_name }}" class="h-14">
                    @else
                        <span class="text-3xl font-serif tracking-wider text-primary">{{ $site?->business_name ?? $tenant->name }}</span>
                    @endif
                </a>
            </div>
            <div class="hidden md:flex items-center space-x-10">
                <a href="{{ route('tenant.home') }}" class="text-sm uppercase tracking-widest text-gray-700 hover:text-secondary transition {{ request()->routeIs('tenant.home') ? 'text-secondary' : '' }}">Home</a>
                <a href="{{ route('tenant.properties') }}" class="text-sm uppercase tracking-widest text-gray-700 hover:text-secondary transition {{ request()->routeIs('tenant.properties*') ? 'text-secondary' : '' }}">Collection</a>
                <a href="{{ route('tenant.about') }}" class="text-sm uppercase tracking-widest text-gray-700 hover:text-secondary transition {{ request()->routeIs('tenant.about') ? 'text-secondary' : '' }}">About</a>
                <a href="{{ route('tenant.contact') }}" class="text-sm uppercase tracking-widest px-6 py-3 bg-primary text-white hover:bg-secondary transition">Inquire</a>
            </div>
            <div class="md:hidden flex items-center">
                <button @click="open = !open" class="text-gray-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="open" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <div x-show="open" x-cloak class="md:hidden bg-white border-t">
        <div class="px-4 py-4 space-y-4">
            <a href="{{ route('tenant.home') }}" class="block text-sm uppercase tracking-widest">Home</a>
            <a href="{{ route('tenant.properties') }}" class="block text-sm uppercase tracking-widest">Collection</a>
            <a href="{{ route('tenant.about') }}" class="block text-sm uppercase tracking-widest">About</a>
            <a href="{{ route('tenant.contact') }}" class="block text-sm uppercase tracking-widest">Inquire</a>
        </div>
    </div>
</nav>

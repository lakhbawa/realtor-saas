<footer class="bg-primary text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-xl font-serif font-bold mb-4">{{ $site?->site_name ?? $tenant->name }}</h3>
                <p class="text-white/70 mb-4">{{ $site?->tagline ?? 'Your trusted real estate partner' }}</p>
                @if($site?->license_number)<p class="text-white/50 text-sm">{{ $site->license_number }}</p>@endif
            </div>
            <div>
                <h4 class="font-semibold mb-4 text-secondary">Quick Links</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('tenant.home') }}" class="text-white/70 hover:text-secondary transition">Home</a></li>
                    <li><a href="{{ route('tenant.properties') }}" class="text-white/70 hover:text-secondary transition">Properties</a></li>
                    <li><a href="{{ route('tenant.about') }}" class="text-white/70 hover:text-secondary transition">About</a></li>
                    <li><a href="{{ route('tenant.contact') }}" class="text-white/70 hover:text-secondary transition">Contact</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4 text-secondary">Contact</h4>
                <ul class="space-y-2 text-white/70">
                    @if($site?->email)<li><a href="mailto:{{ $site->email }}" class="hover:text-secondary">{{ $site->email }}</a></li>@endif
                    @if($site?->phone)<li><a href="tel:{{ $site->phone }}" class="hover:text-secondary">{{ $site->phone }}</a></li>@endif
                    @if($site?->address)<li>{{ $site->address }}</li>@endif
                </ul>
            </div>
        </div>
        <div class="border-t border-white/20 mt-8 pt-8 text-center text-white/50 text-sm">
            <p>&copy; {{ date('Y') }} {{ $site?->site_name ?? $tenant->name }}. All rights reserved.</p>
        </div>
    </div>
</footer>

<footer class="bg-primary text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 text-center md:text-left">
            <div>
                <h3 class="text-2xl font-serif tracking-wider mb-4">{{ $site?->site_name ?? $tenant->name }}</h3>
                <p class="text-white/60 font-light">{{ $site?->tagline ?? 'Exceptional properties for exceptional clients' }}</p>
            </div>
            <div>
                <h4 class="text-sm uppercase tracking-widest mb-4 text-secondary">Navigate</h4>
                <ul class="space-y-3 font-light">
                    <li><a href="{{ route('tenant.home') }}" class="text-white/60 hover:text-secondary transition">Home</a></li>
                    <li><a href="{{ route('tenant.properties') }}" class="text-white/60 hover:text-secondary transition">Collection</a></li>
                    <li><a href="{{ route('tenant.about') }}" class="text-white/60 hover:text-secondary transition">About</a></li>
                    <li><a href="{{ route('tenant.contact') }}" class="text-white/60 hover:text-secondary transition">Inquire</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-sm uppercase tracking-widest mb-4 text-secondary">Contact</h4>
                <ul class="space-y-3 text-white/60 font-light">
                    @if($site?->email)<li><a href="mailto:{{ $site->email }}" class="hover:text-secondary">{{ $site->email }}</a></li>@endif
                    @if($site?->phone)<li><a href="tel:{{ $site->phone }}" class="hover:text-secondary">{{ $site->phone }}</a></li>@endif
                    @if($site?->address)<li>{{ $site->address }}</li>@endif
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10 mt-12 pt-8 text-center text-white/40 text-xs uppercase tracking-widest">
            <p>&copy; {{ date('Y') }} {{ $site?->site_name ?? $tenant->name }}</p>
        </div>
    </div>
</footer>

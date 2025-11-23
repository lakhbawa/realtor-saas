@extends('templates.modern.layouts.app')

@section('content')
<!-- Hero Header with Background -->
<section class="relative bg-gray-900 text-white overflow-hidden">
    <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" alt="Real Estate" class="w-full h-full object-cover opacity-30">
        <div class="absolute inset-0 bg-gradient-to-r from-gray-900 via-gray-900/95 to-gray-900/80"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="max-w-3xl">
            <span class="inline-block px-4 py-1 rounded-full text-sm font-medium bg-primary/20 text-primary border border-primary/30 mb-6">
                Browse Listings
            </span>
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Find Your <span class="text-primary">Dream Property</span></h1>
            <p class="text-xl text-gray-300">Explore our curated collection of exceptional properties</p>
        </div>
    </div>
</section>

<!-- Advanced Search Filters -->
<section class="bg-white shadow-lg sticky top-16 z-40 border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <form action="{{ route('tenant.properties') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Address, City, ZIP..."
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Property Type</label>
                    <select name="type" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary appearance-none bg-white cursor-pointer">
                        <option value="">All Types</option>
                        <option value="house" {{ request('type') === 'house' ? 'selected' : '' }}>House</option>
                        <option value="condo" {{ request('type') === 'condo' ? 'selected' : '' }}>Condo</option>
                        <option value="townhouse" {{ request('type') === 'townhouse' ? 'selected' : '' }}>Townhouse</option>
                        <option value="land" {{ request('type') === 'land' ? 'selected' : '' }}>Land</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary appearance-none bg-white cursor-pointer">
                        <option value="">Buy or Rent</option>
                        <option value="for_sale" {{ request('status') === 'for_sale' ? 'selected' : '' }}>For Sale</option>
                        <option value="for_rent" {{ request('status') === 'for_rent' ? 'selected' : '' }}>For Rent</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bedrooms</label>
                    <select name="bedrooms" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary appearance-none bg-white cursor-pointer">
                        <option value="">Any Beds</option>
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ request('bedrooms') == $i ? 'selected' : '' }}>{{ $i }}+ Beds</option>
                        @endfor
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90 transition flex items-center justify-center gap-2 shadow-lg shadow-primary/25">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Search
                    </button>
                    @if(request()->hasAny(['search', 'type', 'status', 'bedrooms']))
                        <a href="{{ route('tenant.properties') }}" class="px-4 py-3 text-gray-600 hover:text-gray-900 border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Properties Grid -->
<section class="py-16 bg-gradient-to-b from-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($properties->count())
            <div class="flex flex-wrap items-center justify-between mb-8 gap-4">
                <div>
                    <p class="text-gray-600">
                        <span class="font-semibold text-gray-900">{{ $properties->total() }}</span> properties found
                        @if(request()->hasAny(['search', 'type', 'status', 'bedrooms']))
                            <span class="text-primary">with your filters</span>
                        @endif
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500">Sort by:</span>
                    <select class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary">
                        <option>Newest First</option>
                        <option>Price: Low to High</option>
                        <option>Price: High to Low</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($properties as $property)
                    @include('templates.modern.partials.property-card', ['property' => $property])
                @endforeach
            </div>

            <div class="mt-12 flex justify-center">
                {{ $properties->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-20">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">No Properties Found</h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">We couldn't find any properties matching your criteria. Try adjusting your filters or check back later.</p>
                <a href="{{ route('tenant.properties') }}" class="inline-flex items-center px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90 transition">
                    Clear All Filters
                </a>
            </div>
        @endif
    </div>
</section>

<!-- CTA Section -->
<section class="relative py-20 overflow-hidden">
    <div class="absolute inset-0 bg-gray-900">
        <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" alt="Contact" class="w-full h-full object-cover opacity-20">
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Can't Find What You're Looking For?</h2>
        <p class="text-xl text-gray-300 mb-8 max-w-2xl mx-auto">
            Let me help you find the perfect property. I have access to off-market listings and can search for exactly what you need.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('tenant.contact') }}" class="inline-flex items-center justify-center px-8 py-4 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90 transition shadow-lg shadow-primary/25">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Get in Touch
            </a>
            @if($site?->phone)
                <a href="tel:{{ $site->phone }}" class="inline-flex items-center justify-center px-8 py-4 bg-white/10 backdrop-blur-sm text-white rounded-xl font-semibold border border-white/20 hover:bg-white/20 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    {{ $site->phone }}
                </a>
            @endif
        </div>
    </div>
</section>
@endsection

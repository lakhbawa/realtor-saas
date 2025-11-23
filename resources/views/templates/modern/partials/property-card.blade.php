<div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-xl transition group">
    <a href="{{ route('tenant.property', $property->slug) }}" class="block">
        <div class="relative h-52 overflow-hidden">
            @if($property->featured_image)
                <img src="{{ Storage::url($property->featured_image) }}" alt="{{ $property->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
            @elseif($property->images->count())
                <img src="{{ Storage::url($property->images->first()->image_path) }}" alt="{{ $property->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
            @else
                <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="{{ $property->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
            <div class="absolute top-3 left-3 flex gap-2">
                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ ($property->listing_status ?? 'for_sale') === 'for_sale' ? 'bg-green-500 text-white' : 'bg-blue-500 text-white' }}">
                    {{ ($property->listing_status ?? 'for_sale') === 'for_sale' ? 'For Sale' : 'For Rent' }}
                </span>
            </div>
            @if($property->is_featured)
                <div class="absolute top-3 right-3">
                    <span class="px-3 py-1 text-xs font-semibold bg-yellow-500 text-white rounded-full">Featured</span>
                </div>
            @endif
            <div class="absolute bottom-3 left-3">
                <span class="text-2xl font-bold text-white drop-shadow-lg">${{ number_format($property->price) }}</span>
                @if(($property->listing_status ?? 'for_sale') === 'for_rent')
                    <span class="text-sm text-white/80">/mo</span>
                @endif
            </div>
        </div>
    </a>
    <div class="p-5">
        <a href="{{ route('tenant.property', $property->slug) }}" class="block">
            <h3 class="text-lg font-bold text-gray-900 group-hover:text-primary transition mb-1 line-clamp-1">{{ $property->title }}</h3>
        </a>
        <p class="text-gray-500 text-sm flex items-center mb-4">
            <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span class="line-clamp-1">{{ $property->address }}, {{ $property->city }}, {{ $property->state }}</span>
        </p>
        <div class="flex items-center gap-4 text-sm text-gray-600 border-t border-gray-100 pt-4">
            @if($property->bedrooms)
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-1 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span class="font-medium">{{ $property->bedrooms }}</span> Beds
                </span>
            @endif
            @if($property->bathrooms)
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-1 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                    </svg>
                    <span class="font-medium">{{ $property->bathrooms }}</span> Baths
                </span>
            @endif
            @if($property->square_feet)
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-1 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                    </svg>
                    <span class="font-medium">{{ number_format($property->square_feet) }}</span> sqft
                </span>
            @endif
        </div>
    </div>
</div>

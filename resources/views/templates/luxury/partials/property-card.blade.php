<div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition">
    <a href="{{ route('tenant.property', $property->slug) }}">
        <div class="relative h-48">
            @if($property->images->count())
                <img src="{{ Storage::url($property->images->first()->image_path) }}" alt="{{ $property->title }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
            @endif
            <div class="absolute top-3 left-3">
                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $property->listing_status === 'for_sale' ? 'bg-green-500 text-white' : 'bg-blue-500 text-white' }}">
                    {{ $property->listing_status === 'for_sale' ? 'For Sale' : 'For Rent' }}
                </span>
            </div>
            @if($property->is_featured)
                <div class="absolute top-3 right-3">
                    <span class="px-3 py-1 text-xs font-semibold bg-yellow-500 text-white rounded-full">Featured</span>
                </div>
            @endif
        </div>
    </a>
    <div class="p-5">
        <div class="flex justify-between items-start mb-2">
            <span class="text-2xl font-bold text-primary">${{ number_format($property->price) }}</span>
            @if($property->listing_status === 'for_rent')
                <span class="text-sm text-gray-500">/month</span>
            @endif
        </div>
        <a href="{{ route('tenant.property', $property->slug) }}" class="block">
            <h3 class="text-lg font-semibold text-gray-900 hover:text-primary transition">{{ $property->title }}</h3>
        </a>
        <p class="text-gray-500 text-sm mt-1">{{ $property->address }}, {{ $property->city }}, {{ $property->state }}</p>
        <div class="flex items-center gap-4 mt-4 text-sm text-gray-600">
            @if($property->bedrooms)
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    {{ $property->bedrooms }} Beds
                </span>
            @endif
            @if($property->bathrooms)
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                    </svg>
                    {{ $property->bathrooms }} Baths
                </span>
            @endif
            @if($property->square_feet)
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                    </svg>
                    {{ number_format($property->square_feet) }} sqft
                </span>
            @endif
        </div>
    </div>
</div>

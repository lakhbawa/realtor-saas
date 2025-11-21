@extends('templates.modern.layouts.app')

@section('content')
<!-- Header -->
<section class="bg-gray-900 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold">Our Properties</h1>
        <p class="text-gray-300 mt-2">Browse our available listings</p>
    </div>
</section>

<!-- Filters -->
<section class="bg-white border-b sticky top-16 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <form action="{{ route('tenant.properties') }}" method="GET" class="flex flex-wrap gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search properties..."
                class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">

            <select name="type" class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary">
                <option value="">All Types</option>
                <option value="house" {{ request('type') === 'house' ? 'selected' : '' }}>House</option>
                <option value="condo" {{ request('type') === 'condo' ? 'selected' : '' }}>Condo</option>
                <option value="townhouse" {{ request('type') === 'townhouse' ? 'selected' : '' }}>Townhouse</option>
                <option value="land" {{ request('type') === 'land' ? 'selected' : '' }}>Land</option>
            </select>

            <select name="status" class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary">
                <option value="">Buy or Rent</option>
                <option value="for_sale" {{ request('status') === 'for_sale' ? 'selected' : '' }}>For Sale</option>
                <option value="for_rent" {{ request('status') === 'for_rent' ? 'selected' : '' }}>For Rent</option>
            </select>

            <select name="bedrooms" class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary">
                <option value="">Any Beds</option>
                @for($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}" {{ request('bedrooms') == $i ? 'selected' : '' }}>{{ $i }}+ Beds</option>
                @endfor
            </select>

            <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:opacity-90 transition">
                Search
            </button>

            @if(request()->hasAny(['search', 'type', 'status', 'bedrooms']))
                <a href="{{ route('tenant.properties') }}" class="px-4 py-2 text-gray-600 hover:text-gray-900">
                    Clear Filters
                </a>
            @endif
        </form>
    </div>
</section>

<!-- Properties Grid -->
<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($properties->count())
            <p class="text-gray-600 mb-6">{{ $properties->total() }} properties found</p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($properties as $property)
                    @include('templates.modern.partials.property-card', ['property' => $property])
                @endforeach
            </div>

            <div class="mt-10">
                {{ $properties->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-600">No properties found</h3>
                <p class="text-gray-500 mt-2">Try adjusting your search criteria</p>
            </div>
        @endif
    </div>
</section>
@endsection

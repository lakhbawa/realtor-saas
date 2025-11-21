@extends('templates.modern.layouts.app')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gray-900 text-white">
    @if($site?->hero_image)
        <div class="absolute inset-0">
            <img src="{{ Storage::url($site->hero_image) }}" alt="Hero" class="w-full h-full object-cover opacity-40">
        </div>
    @endif
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
        <div class="max-w-3xl">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                {{ $site?->business_name ?? $tenant->name }}
            </h1>
            <p class="text-xl text-gray-300 mb-8">
                {{ $site?->tagline ?? 'Your trusted partner in finding the perfect home' }}
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('tenant.properties') }}" class="px-6 py-3 bg-primary text-white rounded-lg font-semibold hover:opacity-90 transition">
                    View Properties
                </a>
                <a href="{{ route('tenant.contact') }}" class="px-6 py-3 bg-white text-gray-900 rounded-lg font-semibold hover:bg-gray-100 transition">
                    Get in Touch
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Featured Properties -->
@if($featuredProperties->count())
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900">Featured Properties</h2>
            <p class="text-gray-600 mt-2">Discover our handpicked selection of exceptional properties</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($featuredProperties as $property)
                @include('templates.modern.partials.property-card', ['property' => $property])
            @endforeach
        </div>

        <div class="text-center mt-10">
            <a href="{{ route('tenant.properties') }}" class="inline-flex items-center px-6 py-3 border-2 border-primary text-primary rounded-lg font-semibold hover:bg-primary hover:text-white transition">
                View All Properties
                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </div>
</section>
@endif

<!-- About Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                @if($site?->headshot)
                    <img src="{{ Storage::url($site->headshot) }}" alt="{{ $site?->business_name ?? $tenant->name }}" class="rounded-2xl shadow-xl w-full max-w-md mx-auto">
                @else
                    <div class="bg-primary/10 rounded-2xl p-12 text-center">
                        <svg class="w-32 h-32 mx-auto text-primary/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                @endif
            </div>
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">About {{ $site?->business_name ?? $tenant->name }}</h2>
                @if($site?->bio)
                    <div class="prose prose-lg text-gray-600">
                        {!! $site->bio !!}
                    </div>
                @else
                    <p class="text-gray-600 mb-6">
                        With years of experience in real estate, I'm dedicated to helping you find your perfect home or sell your property at the best price.
                    </p>
                @endif
                @if($site?->license_number)
                    <p class="text-sm text-gray-500 mt-4">{{ $site->license_number }}</p>
                @endif
                <a href="{{ route('tenant.about') }}" class="inline-flex items-center text-primary font-semibold mt-4 hover:underline">
                    Learn More About Me
                    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
@if($testimonials->count())
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900">What Clients Say</h2>
            <p class="text-gray-600 mt-2">Hear from our satisfied clients</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($testimonials as $testimonial)
                <div class="bg-gray-50 rounded-xl p-6">
                    <div class="flex items-center mb-4">
                        @for($i = 0; $i < $testimonial->rating; $i++)
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <p class="text-gray-600 mb-4">"{{ $testimonial->content }}"</p>
                    <p class="font-semibold text-gray-900">{{ $testimonial->client_name }}</p>
                    @if($testimonial->client_location)
                        <p class="text-sm text-gray-500">{{ $testimonial->client_location }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- CTA Section -->
<section class="py-16 bg-primary">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold text-white mb-4">Ready to Find Your Dream Home?</h2>
        <p class="text-white/80 mb-8 max-w-2xl mx-auto">
            Let's work together to find the perfect property for you. Contact me today to get started.
        </p>
        <a href="{{ route('tenant.contact') }}" class="inline-flex items-center px-8 py-4 bg-white text-primary rounded-lg font-semibold hover:bg-gray-100 transition">
            Contact Me Today
        </a>
    </div>
</section>
@endsection

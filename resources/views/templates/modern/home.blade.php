@extends('templates.modern.layouts.app')

@section('content')
<!-- Hero Section with Dramatic Design -->
<section class="relative min-h-[90vh] flex items-center overflow-hidden">
    <!-- Background Image with Overlay -->
    <div class="absolute inset-0">
        @if($site?->hero_image)
            <img src="{{ Storage::url($site->hero_image) }}" alt="Hero" class="w-full h-full object-cover">
        @else
            <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=1920&q=80" alt="Luxury Home" class="w-full h-full object-cover">
        @endif
        <div class="absolute inset-0 bg-gradient-to-r from-gray-900/90 via-gray-900/70 to-transparent"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="text-white">
                <div class="inline-flex items-center px-4 py-2 bg-primary/20 backdrop-blur-sm rounded-full text-primary mb-6 border border-primary/30">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span class="text-sm font-medium">Top Rated Real Estate Professional</span>
                </div>

                <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold mb-6 leading-tight">
                    {{ $site?->site_name ?? $tenant->name }}
                </h1>

                <p class="text-xl md:text-2xl text-gray-300 mb-8 leading-relaxed max-w-xl">
                    {{ $site?->tagline ?? 'Turning your real estate dreams into reality with expertise, dedication, and personalized service.' }}
                </p>

                @if($site?->years_experience || $site?->brokerage)
                <div class="flex flex-wrap gap-6 mb-8 text-gray-300">
                    @if($site?->years_experience)
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ $site->years_experience }}+ Years Experience</span>
                    </div>
                    @endif
                    @if($site?->brokerage)
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span>{{ $site->brokerage }}</span>
                    </div>
                    @endif
                </div>
                @endif

                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('tenant.properties') }}" class="group px-8 py-4 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90 transition-all shadow-lg shadow-primary/30 inline-flex items-center">
                        View Properties
                        <svg class="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                    <a href="{{ route('tenant.contact') }}" class="px-8 py-4 bg-white/10 backdrop-blur-sm text-white rounded-xl font-semibold hover:bg-white/20 transition-all border border-white/30">
                        Schedule Consultation
                    </a>
                </div>
            </div>

            <!-- Agent Card -->
            @if($site?->headshot)
            <div class="hidden lg:block">
                <div class="relative">
                    <div class="absolute -inset-4 bg-gradient-to-r from-primary to-primary/50 rounded-3xl blur-2xl opacity-30"></div>
                    <div class="relative bg-white/10 backdrop-blur-md rounded-3xl p-8 border border-white/20">
                        <img src="{{ Storage::url($site->headshot) }}" alt="{{ $site?->site_name ?? $tenant->name }}" class="w-64 h-64 object-cover rounded-2xl mx-auto mb-6 ring-4 ring-white/20">
                        <div class="text-center text-white">
                            <h3 class="text-2xl font-bold mb-1">{{ $site?->site_name ?? $tenant->name }}</h3>
                            @if($site?->license_number)
                            <p class="text-gray-300 text-sm mb-4">{{ $site->license_number }}</p>
                            @endif
                            <div class="flex justify-center gap-4">
                                @if($site?->phone)
                                <a href="tel:{{ $site->phone }}" class="p-3 bg-white/10 rounded-xl hover:bg-primary transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </a>
                                @endif
                                @if($site?->email)
                                <a href="mailto:{{ $site->email }}" class="p-3 bg-white/10 rounded-xl hover:bg-primary transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
        <svg class="w-6 h-6 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
        </svg>
    </div>
</section>

<!-- Stats Section -->
<section class="py-12 bg-white border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <div class="text-4xl md:text-5xl font-bold text-primary mb-2">{{ $featuredProperties->count() > 0 ? $featuredProperties->count() * 10 : 50 }}+</div>
                <div class="text-gray-600 font-medium">Properties Sold</div>
            </div>
            <div>
                <div class="text-4xl md:text-5xl font-bold text-primary mb-2">${{ $featuredProperties->count() > 0 ? number_format($featuredProperties->sum('price') / 1000000, 0) : 25 }}M+</div>
                <div class="text-gray-600 font-medium">Sales Volume</div>
            </div>
            <div>
                <div class="text-4xl md:text-5xl font-bold text-primary mb-2">{{ $site?->years_experience ?? 10 }}+</div>
                <div class="text-gray-600 font-medium">Years Experience</div>
            </div>
            <div>
                <div class="text-4xl md:text-5xl font-bold text-primary mb-2">5.0</div>
                <div class="text-gray-600 font-medium flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    Star Rating
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Properties -->
@if($featuredProperties->count())
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between mb-12">
            <div>
                <span class="text-primary font-semibold tracking-wider uppercase text-sm">Exclusive Listings</span>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mt-2">Featured Properties</h2>
                <p class="text-gray-600 mt-4 max-w-2xl">Handpicked selection of exceptional properties that match the highest standards.</p>
            </div>
            <a href="{{ route('tenant.properties') }}" class="mt-6 md:mt-0 inline-flex items-center text-primary font-semibold hover:underline">
                View All Properties
                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($featuredProperties as $property)
                <article class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="relative overflow-hidden">
                        @if($property->featured_image)
                            <img src="{{ Storage::url($property->featured_image) }}" alt="{{ $property->title }}" class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500">
                        @else
                            <img src="https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=800&q=80" alt="{{ $property->title }}" class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500">
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900/60 to-transparent"></div>
                        <div class="absolute top-4 left-4">
                            @if($property->is_featured)
                            <span class="px-3 py-1 bg-primary text-white text-xs font-bold uppercase rounded-full">Featured</span>
                            @endif
                        </div>
                        <div class="absolute bottom-4 left-4 right-4">
                            <div class="text-2xl font-bold text-white">${{ number_format($property->price) }}</div>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-primary transition">
                            <a href="{{ route('tenant.property', $property->slug) }}">{{ $property->title }}</a>
                        </h3>
                        <p class="text-gray-600 flex items-center mb-4">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $property->city }}{{ $property->state ? ', ' . $property->state : '' }}
                        </p>
                        <div class="flex items-center justify-between pt-4 border-t">
                            <div class="flex gap-4 text-sm text-gray-600">
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
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- About Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div class="relative">
                <div class="absolute -inset-4 bg-primary/10 rounded-3xl transform rotate-3"></div>
                @if($site?->headshot)
                    <img src="{{ Storage::url($site->headshot) }}" alt="{{ $site?->site_name ?? $tenant->name }}" class="relative rounded-2xl shadow-2xl w-full max-w-lg mx-auto">
                @else
                    <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=600&q=80" alt="Real Estate Professional" class="relative rounded-2xl shadow-2xl w-full max-w-lg mx-auto">
                @endif

                <!-- Floating Card -->
                <div class="absolute -bottom-8 -right-8 bg-white rounded-2xl shadow-xl p-6 max-w-xs">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">100%</div>
                            <div class="text-gray-600 text-sm">Client Satisfaction</div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <span class="text-primary font-semibold tracking-wider uppercase text-sm">Your Trusted Partner</span>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mt-2 mb-6">Why Work With Me?</h2>

                @if($site?->bio)
                    <div class="prose prose-lg text-gray-600 mb-8">
                        {!! $site->bio !!}
                    </div>
                @else
                    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                        With years of dedicated experience in the real estate industry, I bring unmatched expertise, integrity, and personalized service to every transaction. Your success is my priority.
                    </p>
                @endif

                @if($site?->specialties)
                <div class="mb-8">
                    <h4 class="font-semibold text-gray-900 mb-3">Specialties:</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach(explode(',', $site->specialties) as $specialty)
                        <span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-full text-sm font-medium">{{ trim($specialty) }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-2 gap-6 mb-8">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Licensed & Insured</h4>
                            <p class="text-sm text-gray-600">Full compliance guaranteed</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">24/7 Availability</h4>
                            <p class="text-sm text-gray-600">Always here for you</p>
                        </div>
                    </div>
                </div>

                <a href="{{ route('tenant.about') }}" class="inline-flex items-center text-primary font-semibold hover:underline">
                    Learn More About Me
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
@if($testimonials->count())
<section class="py-20 bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between mb-16">
            <div class="text-center md:text-left">
                <span class="text-primary font-semibold tracking-wider uppercase text-sm">Testimonials</span>
                <h2 class="text-4xl md:text-5xl font-bold mt-2">What My Clients Say</h2>
                <p class="text-gray-400 mt-4 max-w-2xl">Real stories from real clients who trusted me with their biggest investment.</p>
            </div>
            <a href="{{ route('tenant.testimonials') }}" class="mt-6 md:mt-0 inline-flex items-center text-primary font-semibold hover:underline">
                View All Reviews
                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($testimonials as $testimonial)
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl p-8 border border-gray-700 flex flex-col">
                    <!-- Client Info -->
                    <div class="flex items-start gap-4 mb-4">
                        @if($testimonial->client_photo)
                            <img src="{{ Storage::url($testimonial->client_photo) }}" alt="{{ $testimonial->client_name }}" class="w-14 h-14 rounded-xl object-cover ring-2 ring-primary/30">
                        @else
                            <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-primary to-primary/70 flex items-center justify-center">
                                <span class="text-white font-bold text-lg">{{ $testimonial->client_initials }}</span>
                            </div>
                        @endif
                        <div class="flex-1">
                            <p class="font-semibold text-white">{{ $testimonial->client_name }}</p>
                            @if($testimonial->client_location)
                                <p class="text-gray-400 text-sm">{{ $testimonial->client_location }}</p>
                            @endif
                            <div class="flex mt-1">
                                @for($i = 0; $i < ($testimonial->rating ?? 5); $i++)
                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                        </div>
                    </div>

                    <!-- Quote -->
                    <p class="text-gray-300 leading-relaxed flex-1 mb-4">"{{ Str::limit($testimonial->content, 180) }}"</p>

                    <!-- Transaction Badge & Date -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-700">
                        @if($testimonial->transaction_type)
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $testimonial->transaction_type === 'bought' ? 'bg-green-500/20 text-green-400' : ($testimonial->transaction_type === 'sold' ? 'bg-blue-500/20 text-blue-400' : 'bg-orange-500/20 text-orange-400') }}">
                                {{ $testimonial->transaction_type_label }}
                            </span>
                        @else
                            <span class="text-gray-500 text-sm">Verified Client</span>
                        @endif
                        @if($testimonial->transaction_date)
                            <span class="text-gray-500 text-sm">{{ $testimonial->transaction_date->format('M Y') }}</span>
                        @endif
                    </div>

                    <!-- Property Link -->
                    @if($testimonial->property)
                        <a href="{{ route('tenant.property', $testimonial->property->slug) }}" class="mt-4 flex items-center gap-3 p-3 bg-gray-800 rounded-xl hover:bg-gray-700 transition group">
                            @if($testimonial->property->featured_image)
                                <img src="{{ Storage::url($testimonial->property->featured_image) }}" alt="{{ $testimonial->property->title }}" class="w-12 h-10 rounded-lg object-cover">
                            @else
                                <div class="w-12 h-10 rounded-lg bg-gray-700 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                </div>
                            @endif
                            <span class="text-sm text-gray-300 group-hover:text-primary truncate">{{ $testimonial->property->title }}</span>
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Recent Blog Posts -->
@if(isset($recentPosts) && $recentPosts->count())
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between mb-12">
            <div>
                <span class="text-primary font-semibold tracking-wider uppercase text-sm">Latest Insights</span>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mt-2">From the Blog</h2>
            </div>
            <a href="{{ route('tenant.blog') }}" class="mt-6 md:mt-0 inline-flex items-center text-primary font-semibold hover:underline">
                View All Articles
                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($recentPosts as $post)
                <article class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all group">
                    @if($post->featured_image)
                        <a href="{{ route('tenant.blog.post', $post->slug) }}" class="block overflow-hidden">
                            <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                        </a>
                    @endif
                    <div class="p-6">
                        <div class="text-sm text-gray-500 mb-2">
                            {{ $post->published_at->format('F j, Y') }}
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-primary transition">
                            <a href="{{ route('tenant.blog.post', $post->slug) }}">
                                {{ $post->title }}
                            </a>
                        </h3>
                        @if($post->excerpt)
                            <p class="text-gray-600 line-clamp-2">{{ $post->excerpt }}</p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- CTA Section -->
<section class="relative py-24 overflow-hidden">
    <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=1920&q=80" alt="Beautiful Home" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-primary/90"></div>
    </div>
    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">Ready to Find Your Dream Home?</h2>
        <p class="text-xl text-white/80 mb-10 max-w-2xl mx-auto">
            Let's work together to make your real estate dreams a reality. Schedule a free consultation today.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('tenant.contact') }}" class="px-8 py-4 bg-white text-primary rounded-xl font-bold hover:bg-gray-100 transition-all shadow-lg">
                Schedule Consultation
            </a>
            @if($site?->phone)
            <a href="tel:{{ $site->phone }}" class="px-8 py-4 bg-white/10 text-white rounded-xl font-bold hover:bg-white/20 transition-all border border-white/30">
                Call {{ $site->phone }}
            </a>
            @endif
        </div>
    </div>
</section>
@endsection

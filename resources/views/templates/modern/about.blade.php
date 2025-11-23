@extends('templates.modern.layouts.app')

@section('content')
<!-- Hero Section -->
<section class="relative min-h-[60vh] flex items-center bg-gray-900 overflow-hidden">
    <div class="absolute inset-0">
        @if($site?->hero_image)
            <img src="{{ Storage::url($site->hero_image) }}" alt="{{ $site?->site_name ?? $tenant->name }}" class="w-full h-full object-cover opacity-30">
        @else
            <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" alt="Real Estate" class="w-full h-full object-cover opacity-30">
        @endif
        <div class="absolute inset-0 bg-gradient-to-r from-gray-900 via-gray-900/90 to-gray-900/70"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="inline-block px-4 py-1 rounded-full text-sm font-medium bg-primary/20 text-primary border border-primary/30 mb-6">
                    Your Trusted Real Estate Partner
                </span>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6">
                    Meet <span class="text-primary">{{ $site?->site_name ?? $tenant->name }}</span>
                </h1>
                @if($site?->tagline)
                    <p class="text-xl text-gray-300 mb-8">{{ $site->tagline }}</p>
                @else
                    <p class="text-xl text-gray-300 mb-8">Dedicated to helping you find your perfect property with expertise, integrity, and personalized service.</p>
                @endif
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('tenant.contact') }}" class="inline-flex items-center px-8 py-4 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90 transition shadow-lg shadow-primary/25">
                        Get in Touch
                        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                    <a href="{{ route('tenant.properties') }}" class="inline-flex items-center px-8 py-4 bg-white/10 backdrop-blur-sm text-white rounded-xl font-semibold border border-white/20 hover:bg-white/20 transition">
                        View Listings
                    </a>
                </div>
            </div>
            <div class="hidden lg:block">
                @if($site?->headshot)
                    <div class="relative">
                        <div class="absolute -inset-4 bg-primary/20 rounded-3xl blur-2xl"></div>
                        <img src="{{ Storage::url($site->headshot) }}" alt="{{ $site?->site_name ?? $tenant->name }}" class="relative rounded-2xl shadow-2xl w-full max-w-md mx-auto ring-4 ring-white/20">
                    </div>
                @else
                    <div class="relative">
                        <div class="absolute -inset-4 bg-primary/20 rounded-3xl blur-2xl"></div>
                        <div class="relative bg-white/10 backdrop-blur-sm rounded-2xl p-12 text-center">
                            <svg class="w-32 h-32 text-white/50 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="relative -mt-8 z-10">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-xl p-8 grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="text-4xl font-bold text-primary">{{ $site?->years_experience ?? '10' }}+</div>
                <div class="text-gray-600 mt-1">Years Experience</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-primary">500+</div>
                <div class="text-gray-600 mt-1">Properties Sold</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-primary">$150M+</div>
                <div class="text-gray-600 mt-1">In Sales Volume</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-primary">99%</div>
                <div class="text-gray-600 mt-1">Client Satisfaction</div>
            </div>
        </div>
    </div>
</section>

<!-- About Content -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div class="lg:hidden">
                @if($site?->headshot)
                    <img src="{{ Storage::url($site->headshot) }}" alt="{{ $site?->site_name ?? $tenant->name }}" class="rounded-2xl shadow-xl w-full">
                @endif
            </div>
            <div>
                <span class="inline-block px-4 py-1 rounded-full text-sm font-medium bg-primary/10 text-primary mb-6">
                    My Story
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                    Passionate About Real Estate, Dedicated to You
                </h2>

                @if($site?->bio)
                    <div class="prose prose-lg max-w-none text-gray-600 mb-8">
                        {!! $site->bio !!}
                    </div>
                @else
                    <div class="prose prose-lg max-w-none text-gray-600 mb-8">
                        <p>Welcome! I'm a dedicated real estate professional committed to helping you navigate the complex world of buying and selling property. With years of experience in this market, I understand what it takes to find the perfect home or get top dollar for your property.</p>
                        <p>My approach is simple: listen to your needs, provide expert guidance, and work tirelessly to exceed your expectations. Whether you're a first-time buyer, looking to upgrade, or ready to sell, I'm here to make the process as smooth and successful as possible.</p>
                    </div>
                @endif

                <!-- Credentials -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                    @if($site?->license_number)
                        <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                            <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">License Number</p>
                                <p class="font-semibold text-gray-900">{{ $site->license_number }}</p>
                            </div>
                        </div>
                    @endif
                    @if($site?->brokerage)
                        <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                            <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Brokerage</p>
                                <p class="font-semibold text-gray-900">{{ $site->brokerage }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                @if($site?->specialties)
                    <div class="mb-8">
                        <h3 class="font-semibold text-gray-900 mb-3">Specialties</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach(explode(',', $site->specialties) as $specialty)
                                <span class="px-4 py-2 bg-primary/10 text-primary rounded-full text-sm font-medium">{{ trim($specialty) }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            <div class="hidden lg:block">
                <div class="grid grid-cols-2 gap-4">
                    <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Property" class="rounded-2xl shadow-lg">
                    <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Property" class="rounded-2xl shadow-lg mt-8">
                    <img src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Property" class="rounded-2xl shadow-lg -mt-4">
                    <img src="https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Property" class="rounded-2xl shadow-lg mt-4">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Me -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-1 rounded-full text-sm font-medium bg-primary/10 text-primary mb-4">
                Why Work With Me
            </span>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">The Difference Is in the Details</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">Here's what sets my service apart from the rest</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition group">
                <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-primary group-hover:scale-110 transition">
                    <svg class="w-8 h-8 text-primary group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Local Market Expert</h3>
                <p class="text-gray-600">Deep knowledge of neighborhoods, schools, and market trends to help you make informed decisions.</p>
            </div>

            <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition group">
                <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-primary group-hover:scale-110 transition">
                    <svg class="w-8 h-8 text-primary group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">24/7 Availability</h3>
                <p class="text-gray-600">Real estate doesn't wait. I'm always available to answer questions and address concerns promptly.</p>
            </div>

            <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition group">
                <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-primary group-hover:scale-110 transition">
                    <svg class="w-8 h-8 text-primary group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Trusted Advisor</h3>
                <p class="text-gray-600">Your interests always come first. I provide honest advice and guidance throughout the entire process.</p>
            </div>

            <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition group">
                <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-primary group-hover:scale-110 transition">
                    <svg class="w-8 h-8 text-primary group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Proven Results</h3>
                <p class="text-gray-600">A track record of successful transactions and satisfied clients speaks for itself.</p>
            </div>

            <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition group">
                <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-primary group-hover:scale-110 transition">
                    <svg class="w-8 h-8 text-primary group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Clear Communication</h3>
                <p class="text-gray-600">Regular updates and transparent communication keep you informed every step of the way.</p>
            </div>

            <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-xl transition group">
                <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-primary group-hover:scale-110 transition">
                    <svg class="w-8 h-8 text-primary group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Skilled Negotiator</h3>
                <p class="text-gray-600">Expert negotiation skills to get you the best possible price and terms for your transaction.</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
@if($testimonials->count())
<section class="py-20 bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-1 rounded-full text-sm font-medium bg-primary/20 text-primary border border-primary/30 mb-4">
                Client Testimonials
            </span>
            <h2 class="text-3xl md:text-4xl font-bold mb-4">What My Clients Say</h2>
            <p class="text-xl text-gray-400 max-w-2xl mx-auto">Real stories from real clients</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($testimonials as $testimonial)
                <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-8 border border-white/10">
                    <div class="flex items-center mb-4">
                        @for($i = 0; $i < $testimonial->rating; $i++)
                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <blockquote class="text-gray-300 mb-6">"{{ $testimonial->content }}"</blockquote>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-primary/20 rounded-full flex items-center justify-center mr-4">
                            <span class="text-primary font-bold text-lg">{{ substr($testimonial->client_name, 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-semibold text-white">{{ $testimonial->client_name }}</p>
                            <p class="text-sm text-gray-400">Happy Client</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Contact CTA -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-r from-primary to-primary/80 rounded-3xl p-8 md:p-16 text-center relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <defs>
                        <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                            <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
                        </pattern>
                    </defs>
                    <rect width="100" height="100" fill="url(#grid)"/>
                </svg>
            </div>
            <div class="relative">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Ready to Get Started?</h2>
                <p class="text-xl text-white/80 mb-8 max-w-2xl mx-auto">
                    Whether you're buying, selling, or just have questions, I'm here to help. Let's connect and discuss your real estate goals.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('tenant.contact') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white text-primary rounded-xl font-semibold hover:bg-gray-100 transition shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        Contact Me Today
                    </a>
                    @if($site?->phone)
                        <a href="tel:{{ $site->phone }}" class="inline-flex items-center justify-center px-8 py-4 bg-white/20 text-white rounded-xl font-semibold border border-white/30 hover:bg-white/30 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            {{ $site->phone }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

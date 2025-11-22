@extends('templates.modern.layouts.app')

@section('content')
<!-- Header -->
<section class="bg-gray-900 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold">About Me</h1>
        <p class="text-gray-300 mt-2">Get to know your trusted real estate professional</p>
    </div>
</section>

<!-- About Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
            <div>
                @if($site?->headshot)
                    <img src="{{ Storage::url($site->headshot) }}" alt="{{ $site?->site_name ?? $tenant->name }}" class="rounded-2xl shadow-xl w-full">
                @else
                    <div class="bg-primary/10 rounded-2xl p-12 text-center aspect-square flex items-center justify-center">
                        <svg class="w-48 h-48 text-primary/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                @endif
            </div>
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ $site?->site_name ?? $tenant->name }}</h2>
                @if($site?->tagline)
                    <p class="text-xl text-primary mb-6">{{ $site->tagline }}</p>
                @endif
                @if($site?->license_number)
                    <p class="text-sm text-gray-500 mb-6">{{ $site->license_number }}</p>
                @endif

                @if($site?->bio)
                    <div class="prose prose-lg max-w-none text-gray-600">
                        {!! $site->bio !!}
                    </div>
                @else
                    <p class="text-gray-600 mb-6">
                        Welcome! I'm dedicated to helping you navigate the real estate market with confidence. Whether you're buying your first home, selling a property, or looking for the perfect investment, I'm here to guide you every step of the way.
                    </p>
                @endif

                <!-- Contact Info -->
                <div class="mt-8 space-y-3">
                    @if($site?->email)
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-primary mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <a href="mailto:{{ $site->email }}" class="text-gray-700 hover:text-primary">{{ $site->email }}</a>
                        </div>
                    @endif
                    @if($site?->phone)
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-primary mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <a href="tel:{{ $site->phone }}" class="text-gray-700 hover:text-primary">{{ $site->phone }}</a>
                        </div>
                    @endif
                    @if($site?->address)
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-primary mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="text-gray-700">{{ $site->address }}</span>
                        </div>
                    @endif
                </div>

                <a href="{{ route('tenant.contact') }}" class="inline-flex items-center mt-8 px-6 py-3 bg-primary text-white rounded-lg font-semibold hover:opacity-90 transition">
                    Get in Touch
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
@if($testimonials->count())
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900">Client Testimonials</h2>
            <p class="text-gray-600 mt-2">What my clients are saying</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($testimonials as $testimonial)
                <div class="bg-white rounded-xl shadow-md p-6">
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
@endsection

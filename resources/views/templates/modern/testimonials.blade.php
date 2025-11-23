@extends('templates.modern.layouts.app')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gray-900 overflow-hidden">
    <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" alt="Real Estate" class="w-full h-full object-cover opacity-20">
        <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-gray-900/95 to-primary/20"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="text-center max-w-3xl mx-auto">
            <span class="inline-block px-4 py-1 rounded-full text-sm font-medium bg-primary/20 text-primary border border-primary/30 mb-6">
                Client Reviews
            </span>
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">What My <span class="text-primary">Clients Say</span></h1>
            <p class="text-xl text-gray-300 mb-8">Real stories from real clients who trusted me with their most important real estate decisions.</p>

            <!-- Stats -->
            <div class="flex justify-center gap-8 md:gap-16">
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-bold text-white">{{ $totalTestimonials }}</div>
                    <div class="text-gray-400">Happy Clients</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl md:text-5xl font-bold text-primary flex items-center justify-center">
                        {{ $averageRating }}
                        <svg class="w-8 h-8 ml-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                    <div class="text-gray-400">Average Rating</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Testimonials -->
@if($featuredTestimonials->count())
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-primary font-semibold tracking-wider uppercase text-sm">Featured Reviews</span>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-2">Highlighted Success Stories</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            @foreach($featuredTestimonials as $testimonial)
                <div class="bg-gradient-to-br from-gray-50 to-white rounded-3xl p-8 border border-gray-100 shadow-xl relative overflow-hidden">
                    <!-- Featured Badge -->
                    <div class="absolute top-4 right-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            Featured
                        </span>
                    </div>

                    <div class="flex flex-col md:flex-row gap-6">
                        <!-- Client Photo & Info -->
                        <div class="flex-shrink-0">
                            @if($testimonial->client_photo)
                                <img src="{{ Storage::url($testimonial->client_photo) }}" alt="{{ $testimonial->client_name }}" class="w-20 h-20 rounded-2xl object-cover ring-4 ring-primary/20">
                            @else
                                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-primary to-primary/70 flex items-center justify-center">
                                    <span class="text-white font-bold text-2xl">{{ $testimonial->client_initials }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="flex-1">
                            <!-- Rating -->
                            <div class="flex mb-3">
                                @for($i = 0; $i < ($testimonial->rating ?? 5); $i++)
                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>

                            <!-- Quote -->
                            <blockquote class="text-gray-700 text-lg leading-relaxed mb-4">
                                "{{ $testimonial->content }}"
                            </blockquote>

                            <!-- Client Details -->
                            <div class="flex items-center justify-between flex-wrap gap-4">
                                <div>
                                    <p class="font-bold text-gray-900">{{ $testimonial->client_name }}</p>
                                    @if($testimonial->client_location)
                                        <p class="text-gray-500 text-sm">{{ $testimonial->client_location }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-3">
                                    @if($testimonial->transaction_type)
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $testimonial->transaction_type === 'bought' ? 'bg-green-100 text-green-800' : ($testimonial->transaction_type === 'sold' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800') }}">
                                            {{ $testimonial->transaction_type_label }}
                                        </span>
                                    @endif
                                    @if($testimonial->transaction_date)
                                        <span class="text-gray-400 text-sm">{{ $testimonial->transaction_date->format('M Y') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Property Card -->
                    @if($testimonial->property)
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <p class="text-xs text-gray-500 uppercase tracking-wider mb-3">Related Property</p>
                            <a href="{{ route('tenant.property', $testimonial->property->slug) }}" class="flex items-center gap-4 p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition group">
                                @if($testimonial->property->featured_image)
                                    <img src="{{ Storage::url($testimonial->property->featured_image) }}" alt="{{ $testimonial->property->title }}" class="w-16 h-12 rounded-lg object-cover">
                                @else
                                    <div class="w-16 h-12 rounded-lg bg-gray-200 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                        </svg>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-900 group-hover:text-primary transition truncate">{{ $testimonial->property->title }}</p>
                                    <p class="text-sm text-gray-500">{{ $testimonial->property->city }}, {{ $testimonial->property->state }}</p>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-primary transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    @endif

                    <!-- Video Testimonial -->
                    @if($testimonial->video_url)
                        <div class="mt-4">
                            <a href="{{ $testimonial->video_url }}" target="_blank" rel="noopener" class="inline-flex items-center text-primary font-semibold hover:underline">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                                </svg>
                                Watch Video Testimonial
                            </a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- All Testimonials Grid -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-primary font-semibold tracking-wider uppercase text-sm">All Reviews</span>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-2">More Happy Clients</h2>
        </div>

        @if($testimonials->count())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($testimonials as $testimonial)
                    <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition-all border border-gray-100 flex flex-col">
                        <!-- Header -->
                        <div class="flex items-start gap-4 mb-4">
                            @if($testimonial->client_photo)
                                <img src="{{ Storage::url($testimonial->client_photo) }}" alt="{{ $testimonial->client_name }}" class="w-14 h-14 rounded-xl object-cover">
                            @else
                                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-primary/80 to-primary flex items-center justify-center">
                                    <span class="text-white font-bold text-lg">{{ $testimonial->client_initials }}</span>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-gray-900">{{ $testimonial->client_name }}</p>
                                @if($testimonial->client_location)
                                    <p class="text-gray-500 text-sm">{{ $testimonial->client_location }}</p>
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
                        <blockquote class="text-gray-600 leading-relaxed flex-1 mb-4">
                            "{{ Str::limit($testimonial->content, 200) }}"
                        </blockquote>

                        <!-- Footer -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <div class="flex items-center gap-2">
                                @if($testimonial->transaction_type)
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $testimonial->transaction_type === 'bought' ? 'bg-green-100 text-green-700' : ($testimonial->transaction_type === 'sold' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700') }}">
                                        {{ $testimonial->transaction_type_label }}
                                    </span>
                                @endif
                            </div>
                            @if($testimonial->transaction_date)
                                <span class="text-gray-400 text-xs">{{ $testimonial->transaction_date->format('M Y') }}</span>
                            @endif
                        </div>

                        <!-- Property Link -->
                        @if($testimonial->property)
                            <a href="{{ route('tenant.property', $testimonial->property->slug) }}" class="mt-4 flex items-center gap-3 p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition group">
                                @if($testimonial->property->featured_image)
                                    <img src="{{ Storage::url($testimonial->property->featured_image) }}" alt="{{ $testimonial->property->title }}" class="w-12 h-10 rounded-lg object-cover">
                                @else
                                    <div class="w-12 h-10 rounded-lg bg-gray-200 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                        </svg>
                                    </div>
                                @endif
                                <span class="text-sm font-medium text-gray-700 group-hover:text-primary truncate">{{ $testimonial->property->title }}</span>
                            </a>
                        @endif

                        <!-- Video Link -->
                        @if($testimonial->video_url)
                            <a href="{{ $testimonial->video_url }}" target="_blank" rel="noopener" class="mt-3 inline-flex items-center text-sm text-primary font-medium hover:underline">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                                </svg>
                                Watch Video
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12">
                {{ $testimonials->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <p class="text-gray-500 text-lg">No testimonials yet.</p>
            </div>
        @endif
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-gradient-to-r from-primary to-primary/80">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Ready to Become My Next Success Story?</h2>
        <p class="text-xl text-white/80 mb-8">Let's work together to achieve your real estate goals.</p>
        <a href="{{ route('tenant.contact') }}" class="inline-flex items-center px-8 py-4 bg-white text-primary rounded-xl font-bold hover:bg-gray-100 transition shadow-lg">
            Start Your Journey
            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
        </a>
    </div>
</section>
@endsection

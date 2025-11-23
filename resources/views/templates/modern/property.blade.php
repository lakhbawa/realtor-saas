@extends('templates.modern.layouts.app')

@section('content')
<!-- Property Hero -->
<section class="relative bg-gray-900">
    <!-- Image Gallery -->
    <div x-data="{ activeImage: 0, showGallery: false }" class="relative">
        @php
            // Build array of all images (featured_image first, then gallery images)
            $allImages = collect();
            if ($property->featured_image) {
                $allImages->push((object)['image_path' => $property->featured_image]);
            }
            if ($property->images->count()) {
                $allImages = $allImages->merge($property->images);
            }
            $imageCount = $allImages->count();
        @endphp
        <!-- Main Image -->
        <div class="relative h-[50vh] md:h-[60vh]">
            @if($imageCount > 0)
                @foreach($allImages as $index => $image)
                    <img x-show="activeImage === {{ $index }}"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         src="{{ Storage::url($image->image_path) }}"
                         alt="{{ $property->title }}"
                         class="absolute inset-0 w-full h-full object-cover">
                @endforeach
                <div class="absolute inset-0 bg-gradient-to-t from-gray-900/80 via-transparent to-transparent"></div>
            @else
                <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80"
                     alt="{{ $property->title }}"
                     class="w-full h-full object-cover opacity-50">
                <div class="absolute inset-0 bg-gradient-to-t from-gray-900/80 via-transparent to-transparent"></div>
            @endif

            <!-- Navigation Arrows -->
            @if($imageCount > 1)
                <button @click="activeImage = activeImage === 0 ? {{ $imageCount - 1 }} : activeImage - 1"
                        class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-white hover:bg-white/30 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <button @click="activeImage = activeImage === {{ $imageCount - 1 }} ? 0 : activeImage + 1"
                        class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-white hover:bg-white/30 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            @endif

            <!-- Image Counter -->
            @if($imageCount > 1)
                <div class="absolute bottom-24 left-1/2 -translate-x-1/2 flex gap-2">
                    @for($i = 0; $i < $imageCount; $i++)
                        <button @click="activeImage = {{ $i }}"
                                :class="activeImage === {{ $i }} ? 'bg-white' : 'bg-white/40'"
                                class="w-2 h-2 rounded-full transition"></button>
                    @endfor
                </div>
            @endif
        </div>

        <!-- Property Info Overlay -->
        <div class="absolute bottom-0 inset-x-0 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
                <nav class="flex items-center text-sm text-white/70 mb-4">
                    <a href="{{ route('tenant.properties') }}" class="hover:text-white transition">Properties</a>
                    <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-white">{{ $property->title }}</span>
                </nav>
                <div class="flex flex-wrap justify-between items-end gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ ($property->listing_status ?? 'for_sale') === 'for_sale' ? 'bg-green-500' : 'bg-blue-500' }}">
                                {{ ($property->listing_status ?? 'for_sale') === 'for_sale' ? 'For Sale' : 'For Rent' }}
                            </span>
                            @if($property->is_featured)
                                <span class="px-3 py-1 text-sm font-semibold bg-yellow-500 rounded-full">Featured</span>
                            @endif
                        </div>
                        <h1 class="text-3xl md:text-4xl font-bold mb-2">{{ $property->title }}</h1>
                        <p class="text-lg text-white/80 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $property->address }}, {{ $property->city }}, {{ $property->state }} {{ $property->zip_code }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-4xl md:text-5xl font-bold text-primary">${{ number_format($property->price) }}</p>
                        @if(($property->listing_status ?? 'for_sale') === 'for_rent')
                            <span class="text-white/60">per month</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Stats Bar -->
<section class="bg-white border-b sticky top-16 z-30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between py-4 overflow-x-auto">
            <div class="flex items-center gap-8">
                @if($property->bedrooms)
                    <div class="flex items-center gap-2 text-gray-700">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span class="font-semibold">{{ $property->bedrooms }}</span>
                        <span class="text-gray-500">Beds</span>
                    </div>
                @endif
                @if($property->bathrooms)
                    <div class="flex items-center gap-2 text-gray-700">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                        </svg>
                        <span class="font-semibold">{{ $property->bathrooms }}</span>
                        <span class="text-gray-500">Baths</span>
                    </div>
                @endif
                @if($property->square_feet)
                    <div class="flex items-center gap-2 text-gray-700">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                        </svg>
                        <span class="font-semibold">{{ number_format($property->square_feet) }}</span>
                        <span class="text-gray-500">Sq Ft</span>
                    </div>
                @endif
                @if($property->year_built)
                    <div class="flex items-center gap-2 text-gray-700">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="font-semibold">{{ $property->year_built }}</span>
                        <span class="text-gray-500">Built</span>
                    </div>
                @endif
            </div>
            <div class="hidden md:flex items-center gap-3">
                <button class="p-2 text-gray-500 hover:text-primary transition" title="Share">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                </button>
                <button class="p-2 text-gray-500 hover:text-red-500 transition" title="Save">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Property Details -->
<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Description -->
                <div class="bg-white rounded-2xl shadow-sm p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <svg class="w-6 h-6 text-primary mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        About This Property
                    </h2>
                    <div class="prose prose-lg max-w-none text-gray-600">
                        {!! nl2br(e($property->description)) !!}
                    </div>
                </div>

                <!-- Features -->
                @if($property->features && count($property->features))
                    <div class="bg-white rounded-2xl shadow-sm p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 text-primary mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Property Features
                        </h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($property->features as $feature)
                                <div class="flex items-center p-3 bg-gray-50 rounded-xl">
                                    <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <span class="text-gray-700">{{ $feature }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Location Map Placeholder -->
                <div class="bg-white rounded-2xl shadow-sm p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <svg class="w-6 h-6 text-primary mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Location
                    </h2>
                    <div class="bg-gray-100 rounded-xl h-64 flex items-center justify-center">
                        <div class="text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                            <p>{{ $property->address }}, {{ $property->city }}, {{ $property->state }} {{ $property->zip_code }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Contact Form -->
                <div class="bg-white rounded-2xl shadow-sm p-6 sticky top-36">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Interested in This Property?</h3>
                    <p class="text-gray-600 text-sm mb-6">Fill out the form below and I'll get back to you shortly.</p>

                    @if(session('success'))
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-green-700 font-medium">{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-red-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-red-700 font-medium">{{ session('error') }}</p>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('tenant.contact.submit') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="property_id" value="{{ $property->id }}">

                        <div>
                            <input type="text" name="name" placeholder="Your Name" required
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition">
                        </div>
                        <div>
                            <input type="email" name="email" placeholder="Your Email" required
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition">
                        </div>
                        <div>
                            <input type="tel" name="phone" placeholder="Your Phone"
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition">
                        </div>
                        <div>
                            <textarea name="message" rows="4" placeholder="I'm interested in this property..." required
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition">I would like more information about {{ $property->title }}</textarea>
                        </div>
                        <button type="submit" class="w-full px-6 py-4 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90 transition shadow-lg shadow-primary/25">
                            Send Message
                        </button>
                    </form>

                    @if($site?->phone)
                        <div class="mt-6 pt-6 border-t border-gray-100 text-center">
                            <p class="text-sm text-gray-500 mb-2">Or call directly</p>
                            <a href="tel:{{ $site->phone }}" class="text-xl font-bold text-primary hover:underline">{{ $site->phone }}</a>
                        </div>
                    @endif
                </div>

                <!-- Agent Card -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl shadow-sm p-6 text-white">
                    <div class="flex items-center gap-4 mb-4">
                        @if($site?->headshot)
                            <img src="{{ Storage::url($site->headshot) }}" alt="{{ $site?->site_name ?? $tenant->name }}" class="w-16 h-16 rounded-full object-cover ring-2 ring-primary">
                        @else
                            <div class="w-16 h-16 rounded-full bg-primary/20 flex items-center justify-center">
                                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        @endif
                        <div>
                            <h3 class="font-bold text-lg">{{ $site?->site_name ?? $tenant->name }}</h3>
                            @if($site?->license_number)
                                <p class="text-sm text-gray-400">{{ $site->license_number }}</p>
                            @endif
                        </div>
                    </div>
                    @if($site?->brokerage)
                        <p class="text-sm text-gray-400 mb-4">{{ $site->brokerage }}</p>
                    @endif
                    <div class="space-y-2">
                        @if($site?->phone)
                            <a href="tel:{{ $site->phone }}" class="flex items-center gap-2 text-white/80 hover:text-primary transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                {{ $site->phone }}
                            </a>
                        @endif
                        @if($site?->email)
                            <a href="mailto:{{ $site->email }}" class="flex items-center gap-2 text-white/80 hover:text-primary transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                {{ $site->email }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Properties -->
@if($relatedProperties->count())
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Similar Properties</h2>
                <p class="text-gray-600 mt-1">You might also be interested in these listings</p>
            </div>
            <a href="{{ route('tenant.properties') }}" class="hidden md:inline-flex items-center text-primary font-semibold hover:underline">
                View All Properties
                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($relatedProperties as $related)
                @include('templates.modern.partials.property-card', ['property' => $related])
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection

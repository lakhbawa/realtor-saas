@extends('templates.modern.layouts.app')

@section('content')
<!-- Property Header -->
<section class="bg-white border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <nav class="flex items-center text-sm text-gray-500 mb-4">
            <a href="{{ route('tenant.properties') }}" class="hover:text-primary">Properties</a>
            <svg class="w-4 h-4 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900">{{ $property->title }}</span>
        </nav>
        <div class="flex flex-wrap justify-between items-start gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $property->title }}</h1>
                <p class="text-gray-600 mt-1">{{ $property->address }}, {{ $property->city }}, {{ $property->state }} {{ $property->zip_code }}</p>
            </div>
            <div class="text-right">
                <p class="text-3xl font-bold text-primary">${{ number_format($property->price) }}</p>
                @if($property->listing_status === 'for_rent')
                    <span class="text-gray-500">/month</span>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Property Images -->
<section class="bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($property->images->count())
            <div x-data="{ activeImage: 0 }" class="space-y-4">
                <div class="aspect-video rounded-xl overflow-hidden bg-white">
                    @foreach($property->images as $index => $image)
                        <img x-show="activeImage === {{ $index }}" src="{{ Storage::url($image->image_path) }}" alt="{{ $property->title }}" class="w-full h-full object-cover">
                    @endforeach
                </div>
                @if($property->images->count() > 1)
                    <div class="flex gap-2 overflow-x-auto pb-2">
                        @foreach($property->images as $index => $image)
                            <button @click="activeImage = {{ $index }}" :class="{ 'ring-2 ring-primary': activeImage === {{ $index }} }" class="flex-shrink-0 w-24 h-16 rounded-lg overflow-hidden">
                                <img src="{{ Storage::url($image->image_path) }}" alt="" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        @else
            <div class="aspect-video bg-gray-200 rounded-xl flex items-center justify-center">
                <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
        @endif
    </div>
</section>

<!-- Property Details -->
<section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Quick Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @if($property->bedrooms)
                        <div class="bg-gray-50 rounded-xl p-4 text-center">
                            <p class="text-2xl font-bold text-gray-900">{{ $property->bedrooms }}</p>
                            <p class="text-sm text-gray-500">Bedrooms</p>
                        </div>
                    @endif
                    @if($property->bathrooms)
                        <div class="bg-gray-50 rounded-xl p-4 text-center">
                            <p class="text-2xl font-bold text-gray-900">{{ $property->bathrooms }}</p>
                            <p class="text-sm text-gray-500">Bathrooms</p>
                        </div>
                    @endif
                    @if($property->square_feet)
                        <div class="bg-gray-50 rounded-xl p-4 text-center">
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($property->square_feet) }}</p>
                            <p class="text-sm text-gray-500">Sq. Ft.</p>
                        </div>
                    @endif
                    @if($property->year_built)
                        <div class="bg-gray-50 rounded-xl p-4 text-center">
                            <p class="text-2xl font-bold text-gray-900">{{ $property->year_built }}</p>
                            <p class="text-sm text-gray-500">Year Built</p>
                        </div>
                    @endif
                </div>

                <!-- Description -->
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Description</h2>
                    <div class="prose max-w-none text-gray-600">
                        {!! nl2br(e($property->description)) !!}
                    </div>
                </div>

                <!-- Features -->
                @if($property->features)
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Features</h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($property->features as $feature)
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-primary mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-gray-600">{{ $feature }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Contact Form -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Interested in this property?</h3>
                    <form action="{{ route('tenant.contact.submit') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="property_id" value="{{ $property->id }}">

                        <div>
                            <input type="text" name="name" placeholder="Your Name" required class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                        <div>
                            <input type="email" name="email" placeholder="Your Email" required class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                        <div>
                            <input type="tel" name="phone" placeholder="Your Phone" class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                        <div>
                            <textarea name="message" rows="4" placeholder="I'm interested in this property..." required class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-primary focus:border-primary">I would like more information about {{ $property->title }}</textarea>
                        </div>
                        <button type="submit" class="w-full px-6 py-3 bg-primary text-white rounded-lg font-semibold hover:opacity-90 transition">
                            Send Message
                        </button>
                    </form>
                </div>

                <!-- Agent Card -->
                <div class="bg-gray-50 rounded-xl p-6 text-center">
                    @if($site?->headshot)
                        <img src="{{ Storage::url($site->headshot) }}" alt="{{ $site?->site_name ?? $tenant->name }}" class="w-24 h-24 rounded-full mx-auto mb-4 object-cover">
                    @endif
                    <h3 class="font-bold text-gray-900">{{ $site?->site_name ?? $tenant->name }}</h3>
                    @if($site?->license_number)
                        <p class="text-sm text-gray-500">{{ $site->license_number }}</p>
                    @endif
                    <div class="mt-4 space-y-2">
                        @if($site?->phone)
                            <a href="tel:{{ $site->phone }}" class="block text-primary hover:underline">{{ $site->phone }}</a>
                        @endif
                        @if($site?->email)
                            <a href="mailto:{{ $site->email }}" class="block text-primary hover:underline">{{ $site->email }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Properties -->
@if($relatedProperties->count())
<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-8">Similar Properties</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($relatedProperties as $related)
                @include('templates.modern.partials.property-card', ['property' => $related])
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection

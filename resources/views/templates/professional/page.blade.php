@extends('templates.professional.layouts.app')

@section('content')
<!-- Page Header -->
<section class="bg-gray-900 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold">{{ $page->title }}</h1>
    </div>
</section>

<!-- Page Content -->
<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($page->featured_image)
            <img src="{{ Storage::url($page->featured_image) }}" alt="{{ $page->title }}" class="w-full rounded-xl mb-8">
        @endif

        <div class="prose prose-lg max-w-none">
            {!! $page->content !!}
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Ready to Get Started?</h2>
        <p class="text-gray-600 mb-8 max-w-2xl mx-auto">
            Contact me today to discuss your real estate needs.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('tenant.properties') }}" class="px-6 py-3 bg-primary text-white rounded-lg font-semibold hover:opacity-90 transition">
                View Properties
            </a>
            <a href="{{ route('tenant.contact') }}" class="px-6 py-3 border-2 border-primary text-primary rounded-lg font-semibold hover:bg-primary hover:text-white transition">
                Contact Me
            </a>
        </div>
    </div>
</section>
@endsection

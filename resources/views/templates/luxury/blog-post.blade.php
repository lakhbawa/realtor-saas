@extends('templates.luxury.layouts.app')

@section('content')
<!-- Article Header -->
<section class="bg-gray-900 text-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-sm text-gray-400 mb-4">
            <a href="{{ route('tenant.blog') }}" class="hover:text-white">Blog</a>
            <span class="mx-2">/</span>
            <span>{{ $post->title }}</span>
        </div>
        <h1 class="text-4xl font-bold mb-4">{{ $post->title }}</h1>
        <div class="flex items-center text-gray-300">
            <span>{{ $post->published_at->format('F j, Y') }}</span>
            <span class="mx-2">&#8226;</span>
            <span>{{ ceil(str_word_count(strip_tags($post->content)) / 200) }} min read</span>
        </div>
    </div>
</section>

<!-- Article Content -->
<article class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($post->featured_image)
            <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full rounded-xl mb-8">
        @endif

        <div class="prose prose-lg max-w-none">
            {!! $post->content !!}
        </div>

        <!-- Share -->
        <div class="border-t mt-12 pt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Share this article</h3>
            <div class="flex gap-4">
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($post->title) }}" target="_blank" rel="noopener" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    Twitter
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" rel="noopener" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    Facebook
                </a>
                <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->url()) }}&title={{ urlencode($post->title) }}" target="_blank" rel="noopener" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    LinkedIn
                </a>
            </div>
        </div>
    </div>
</article>

<!-- Related Posts -->
@if($recentPosts->count())
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-8">More Articles</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($recentPosts as $recentPost)
                <article class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition">
                    @if($recentPost->featured_image)
                        <a href="{{ route('tenant.blog.post', $recentPost->slug) }}">
                            <img src="{{ Storage::url($recentPost->featured_image) }}" alt="{{ $recentPost->title }}" class="w-full h-40 object-cover">
                        </a>
                    @endif
                    <div class="p-6">
                        <div class="text-sm text-gray-500 mb-2">
                            {{ $recentPost->published_at->format('F j, Y') }}
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            <a href="{{ route('tenant.blog.post', $recentPost->slug) }}" class="hover:text-primary transition">
                                {{ $recentPost->title }}
                            </a>
                        </h3>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- CTA -->
<section class="py-16 bg-primary">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold text-white mb-4">Have Questions?</h2>
        <p class="text-white/80 mb-8 max-w-2xl mx-auto">
            I'm here to help with all your real estate needs. Get in touch today!
        </p>
        <a href="{{ route('tenant.contact') }}" class="inline-flex items-center px-8 py-4 bg-white text-primary rounded-lg font-semibold hover:bg-gray-100 transition">
            Contact Me
        </a>
    </div>
</section>
@endsection

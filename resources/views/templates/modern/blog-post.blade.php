@extends('templates.modern.layouts.app')

@section('content')
<!-- Article Header -->
<section class="relative bg-gray-900 overflow-hidden">
    @if($post->featured_image)
        <div class="absolute inset-0">
            <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover opacity-30">
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/80 to-gray-900/60"></div>
        </div>
    @else
        <div class="absolute inset-0 bg-gradient-to-r from-gray-900 via-gray-900/95 to-primary/20"></div>
    @endif
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <nav class="flex items-center text-sm text-white/60 mb-6">
            <a href="{{ route('tenant.blog') }}" class="hover:text-white transition flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Blog
            </a>
        </nav>
        <span class="inline-block px-4 py-1 rounded-full text-sm font-medium bg-primary/20 text-primary border border-primary/30 mb-6">
            Real Estate Insights
        </span>
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-6 leading-tight">{{ $post->title }}</h1>
        <div class="flex flex-wrap items-center gap-6 text-white/70">
            <span class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ $post->published_at->format('F j, Y') }}
            </span>
            <span class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ ceil(str_word_count(strip_tags($post->content)) / 200) }} min read
            </span>
        </div>
    </div>
</section>

<!-- Article Content -->
<article class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($post->featured_image)
            <div class="relative -mt-32 mb-12">
                <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full rounded-2xl shadow-2xl">
            </div>
        @endif

        <div class="prose prose-lg max-w-none prose-headings:text-gray-900 prose-p:text-gray-600 prose-a:text-primary prose-strong:text-gray-900">
            {!! $post->content !!}
        </div>

        <!-- Share Section -->
        <div class="border-t border-gray-200 mt-12 pt-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Share this article</h3>
                    <div class="flex gap-3">
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($post->title) }}" target="_blank" rel="noopener" class="w-10 h-10 bg-gray-100 text-gray-600 rounded-xl flex items-center justify-center hover:bg-[#1DA1F2] hover:text-white transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" rel="noopener" class="w-10 h-10 bg-gray-100 text-gray-600 rounded-xl flex items-center justify-center hover:bg-[#1877F2] hover:text-white transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->url()) }}&title={{ urlencode($post->title) }}" target="_blank" rel="noopener" class="w-10 h-10 bg-gray-100 text-gray-600 rounded-xl flex items-center justify-center hover:bg-[#0A66C2] hover:text-white transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                        <button onclick="navigator.clipboard.writeText(window.location.href)" class="w-10 h-10 bg-gray-100 text-gray-600 rounded-xl flex items-center justify-center hover:bg-primary hover:text-white transition" title="Copy link">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <a href="{{ route('tenant.blog') }}" class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Blog
                </a>
            </div>
        </div>
    </div>
</article>

<!-- Author Card -->
<section class="py-12 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-sm p-8">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                @if($site?->headshot)
                    <img src="{{ Storage::url($site->headshot) }}" alt="{{ $site?->site_name ?? $tenant->name }}" class="w-24 h-24 rounded-2xl object-cover ring-4 ring-primary/20 flex-shrink-0">
                @else
                    <div class="w-24 h-24 rounded-2xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-12 h-12 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                @endif
                <div class="text-center sm:text-left">
                    <p class="text-sm text-primary font-medium mb-1">Written by</p>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $site?->site_name ?? $tenant->name }}</h3>
                    @if($site?->tagline)
                        <p class="text-gray-600 mb-4">{{ $site->tagline }}</p>
                    @endif
                    <div class="flex flex-wrap justify-center sm:justify-start gap-4">
                        <a href="{{ route('tenant.about') }}" class="inline-flex items-center text-primary font-semibold hover:underline">
                            About Me
                            <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                        <a href="{{ route('tenant.contact') }}" class="inline-flex items-center text-primary font-semibold hover:underline">
                            Contact
                            <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Posts -->
@if($recentPosts->count())
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">More Articles</h2>
                <p class="text-gray-600 mt-1">Continue exploring real estate insights</p>
            </div>
            <a href="{{ route('tenant.blog') }}" class="hidden md:inline-flex items-center text-primary font-semibold hover:underline">
                View All Articles
                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($recentPosts as $recentPost)
                <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition group">
                    @if($recentPost->featured_image)
                        <a href="{{ route('tenant.blog.post', $recentPost->slug) }}" class="block">
                            <div class="relative h-44 overflow-hidden">
                                <img src="{{ Storage::url($recentPost->featured_image) }}" alt="{{ $recentPost->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            </div>
                        </a>
                    @endif
                    <div class="p-6">
                        <div class="text-sm text-gray-500 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $recentPost->published_at->format('M j, Y') }}
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 group-hover:text-primary transition">
                            <a href="{{ route('tenant.blog.post', $recentPost->slug) }}">
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
<section class="py-20 bg-gradient-to-r from-primary to-primary/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Have Questions?</h2>
        <p class="text-xl text-white/80 mb-8 max-w-2xl mx-auto">
            I'm here to help with all your real estate needs. Get in touch today!
        </p>
        <a href="{{ route('tenant.contact') }}" class="inline-flex items-center px-8 py-4 bg-white text-primary rounded-xl font-semibold hover:bg-gray-100 transition shadow-lg">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            Contact Me
        </a>
    </div>
</section>
@endsection

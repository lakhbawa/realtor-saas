@extends('templates.modern.layouts.app')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gray-900 overflow-hidden">
    <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" alt="Real Estate Blog" class="w-full h-full object-cover opacity-20">
        <div class="absolute inset-0 bg-gradient-to-r from-gray-900 via-gray-900/95 to-gray-900/80"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="max-w-3xl">
            <span class="inline-block px-4 py-1 rounded-full text-sm font-medium bg-primary/20 text-primary border border-primary/30 mb-6">
                Real Estate Insights
            </span>
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Expert <span class="text-primary">Advice & Tips</span></h1>
            <p class="text-xl text-gray-300">Stay informed with the latest market trends, home buying tips, and real estate news.</p>
        </div>
    </div>
</section>

<!-- Search Section -->
<section class="bg-white border-b sticky top-16 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <form action="{{ route('tenant.blog') }}" method="GET" class="max-w-xl">
            <div class="flex gap-3">
                <div class="relative flex-1">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search articles..."
                        class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition">
                </div>
                <button type="submit" class="px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90 transition shadow-lg shadow-primary/25">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('tenant.blog') }}" class="px-4 py-3 text-gray-600 hover:text-gray-900 border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                @endif
            </div>
        </form>
    </div>
</section>

<!-- Blog Posts -->
<section class="py-16 bg-gradient-to-b from-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($posts->count())
            @if(request('search'))
                <p class="text-gray-600 mb-8">
                    Found <span class="font-semibold text-gray-900">{{ $posts->total() }}</span> articles matching "<span class="text-primary">{{ request('search') }}</span>"
                </p>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($posts as $post)
                    <article class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-xl transition group">
                        <a href="{{ route('tenant.blog.post', $post->slug) }}" class="block">
                            @if($post->featured_image)
                                <div class="relative h-52 overflow-hidden">
                                    <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                                </div>
                            @else
                                <div class="relative h-52 bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center overflow-hidden">
                                    <svg class="w-16 h-16 text-primary/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                    </svg>
                                </div>
                            @endif
                        </a>
                        <div class="p-6">
                            <div class="flex items-center gap-3 text-sm text-gray-500 mb-3">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $post->published_at->format('M j, Y') }}
                                </span>
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ ceil(str_word_count(strip_tags($post->content)) / 200) }} min read
                                </span>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-primary transition">
                                <a href="{{ route('tenant.blog.post', $post->slug) }}">
                                    {{ $post->title }}
                                </a>
                            </h2>
                            @if($post->excerpt)
                                <p class="text-gray-600 mb-4 line-clamp-3">{{ $post->excerpt }}</p>
                            @endif
                            <a href="{{ route('tenant.blog.post', $post->slug) }}" class="inline-flex items-center text-primary font-semibold hover:underline">
                                Read Article
                                <svg class="ml-2 w-4 h-4 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12 flex justify-center">
                {{ $posts->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-20">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                </div>
                @if(request('search'))
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">No Results Found</h3>
                    <p class="text-gray-600 mb-6 max-w-md mx-auto">No articles match your search for "{{ request('search') }}". Try a different search term.</p>
                    <a href="{{ route('tenant.blog') }}" class="inline-flex items-center px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90 transition">
                        View All Articles
                    </a>
                @else
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">No Blog Posts Yet</h3>
                    <p class="text-gray-600 max-w-md mx-auto">Check back soon for helpful real estate articles, market updates, and home buying tips!</p>
                @endif
            </div>
        @endif
    </div>
</section>

<!-- Newsletter CTA -->
<section class="py-20 bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center">
            <span class="inline-block px-4 py-1 rounded-full text-sm font-medium bg-primary/20 text-primary border border-primary/30 mb-6">
                Stay Updated
            </span>
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Get Market Insights Delivered</h2>
            <p class="text-xl text-gray-400 mb-8">
                Stay ahead of the market with expert advice and the latest real estate trends.
            </p>
            <a href="{{ route('tenant.contact') }}" class="inline-flex items-center px-8 py-4 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90 transition shadow-lg shadow-primary/25">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Get in Touch
            </a>
        </div>
    </div>
</section>
@endsection

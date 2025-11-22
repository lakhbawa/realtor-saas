@extends('templates.modern.layouts.app')

@section('content')
<!-- Page Header -->
<section class="bg-gray-900 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold mb-4">Blog</h1>
        <p class="text-gray-300">Stay updated with the latest real estate news and tips</p>
    </div>
</section>

<!-- Search -->
<section class="py-8 bg-white border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <form action="{{ route('tenant.blog') }}" method="GET" class="max-w-md">
            <div class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search articles..."
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:opacity-90 transition">
                    Search
                </button>
            </div>
        </form>
    </div>
</section>

<!-- Blog Posts -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($posts->count())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($posts as $post)
                    <article class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition">
                        @if($post->featured_image)
                            <a href="{{ route('tenant.blog.post', $post->slug) }}">
                                <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover">
                            </a>
                        @else
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                </svg>
                            </div>
                        @endif
                        <div class="p-6">
                            <div class="text-sm text-gray-500 mb-2">
                                {{ $post->published_at->format('F j, Y') }}
                            </div>
                            <h2 class="text-xl font-semibold text-gray-900 mb-2">
                                <a href="{{ route('tenant.blog.post', $post->slug) }}" class="hover:text-primary transition">
                                    {{ $post->title }}
                                </a>
                            </h2>
                            @if($post->excerpt)
                                <p class="text-gray-600 mb-4 line-clamp-3">{{ $post->excerpt }}</p>
                            @endif
                            <a href="{{ route('tenant.blog.post', $post->slug) }}" class="inline-flex items-center text-primary font-semibold hover:underline">
                                Read More
                                <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12">
                {{ $posts->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No blog posts yet</h3>
                <p class="text-gray-600">Check back soon for updates!</p>
            </div>
        @endif
    </div>
</section>
@endsection

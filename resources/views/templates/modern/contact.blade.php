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
                Get In Touch
            </span>
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Let's Start a <span class="text-primary">Conversation</span></h1>
            <p class="text-xl text-gray-300">Have questions about buying, selling, or the market? I'm here to help you every step of the way.</p>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
            <!-- Contact Form -->
            <div>
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 lg:p-10">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Send Me a Message</h2>
                    <p class="text-gray-600 mb-8">Fill out the form below and I'll respond within 24 hours.</p>

                    @if(session('success'))
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700">
                            <div class="flex items-center mb-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="font-semibold">Please fix the following errors:</span>
                            </div>
                            <ul class="list-disc list-inside ml-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('tenant.contact.submit') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">Your Name *</label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition"
                                    placeholder="John Smith">
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-semibold text-gray-900 mb-2">Phone Number</label>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition"
                                    placeholder="(555) 123-4567">
                            </div>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-900 mb-2">Email Address *</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition"
                                placeholder="john@example.com">
                        </div>
                        <div>
                            <label for="subject" class="block text-sm font-semibold text-gray-900 mb-2">Subject</label>
                            <select id="subject" name="subject" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition bg-white">
                                <option value="">Select a topic...</option>
                                <option value="buying" {{ old('subject') === 'buying' ? 'selected' : '' }}>I'm looking to buy</option>
                                <option value="selling" {{ old('subject') === 'selling' ? 'selected' : '' }}>I'm looking to sell</option>
                                <option value="property" {{ old('subject') === 'property' ? 'selected' : '' }}>Property inquiry</option>
                                <option value="market" {{ old('subject') === 'market' ? 'selected' : '' }}>Market analysis</option>
                                <option value="other" {{ old('subject') === 'other' ? 'selected' : '' }}>Other question</option>
                            </select>
                        </div>
                        <div>
                            <label for="message" class="block text-sm font-semibold text-gray-900 mb-2">Message *</label>
                            <textarea id="message" name="message" rows="5" required
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition resize-none"
                                placeholder="Tell me about your real estate needs...">{{ old('message') }}</textarea>
                        </div>
                        <button type="submit" class="w-full px-8 py-4 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90 transition shadow-lg shadow-primary/25 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Send Message
                        </button>
                    </form>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="space-y-8">
                <!-- Agent Card -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl p-8 text-white">
                    <div class="flex items-start gap-6">
                        @if($site?->headshot)
                            <img src="{{ Storage::url($site->headshot) }}" alt="{{ $site?->site_name ?? $tenant->name }}" class="w-24 h-24 rounded-2xl object-cover ring-4 ring-primary/30 flex-shrink-0">
                        @else
                            <div class="w-24 h-24 rounded-2xl bg-primary/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-12 h-12 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        @endif
                        <div>
                            <h3 class="text-2xl font-bold mb-1">{{ $site?->site_name ?? $tenant->name }}</h3>
                            @if($site?->tagline)
                                <p class="text-primary mb-2">{{ $site->tagline }}</p>
                            @endif
                            @if($site?->license_number)
                                <p class="text-sm text-gray-400">{{ $site->license_number }}</p>
                            @endif
                            @if($site?->brokerage)
                                <p class="text-sm text-gray-400">{{ $site->brokerage }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Contact Methods -->
                <div class="grid grid-cols-1 gap-4">
                    @if($site?->email)
                        <a href="mailto:{{ $site->email }}" class="flex items-center p-6 bg-gray-50 rounded-2xl hover:bg-gray-100 transition group">
                            <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mr-5 group-hover:bg-primary group-hover:scale-110 transition">
                                <svg class="w-6 h-6 text-primary group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-1">Email</h3>
                                <p class="text-primary">{{ $site->email }}</p>
                            </div>
                        </a>
                    @endif

                    @if($site?->phone)
                        <a href="tel:{{ $site->phone }}" class="flex items-center p-6 bg-gray-50 rounded-2xl hover:bg-gray-100 transition group">
                            <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mr-5 group-hover:bg-primary group-hover:scale-110 transition">
                                <svg class="w-6 h-6 text-primary group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-1">Phone</h3>
                                <p class="text-primary">{{ $site->phone }}</p>
                            </div>
                        </a>
                    @endif

                    @if($site?->address)
                        <div class="flex items-center p-6 bg-gray-50 rounded-2xl">
                            <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mr-5">
                                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-1">Office</h3>
                                <p class="text-gray-600">
                                    {{ $site->address }}
                                    @if($site->city || $site->state || $site->zip)
                                        <br>{{ $site->city }}{{ $site->city && $site->state ? ', ' : '' }}{{ $site->state }} {{ $site->zip }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Social Links -->
                @if($site?->facebook || $site?->instagram || $site?->linkedin || $site?->twitter || $site?->youtube)
                    <div class="bg-gray-50 rounded-2xl p-6">
                        <h3 class="font-semibold text-gray-900 mb-4">Connect With Me</h3>
                        <div class="flex flex-wrap gap-3">
                            @if($site->facebook)
                                <a href="{{ $site->facebook }}" target="_blank" rel="noopener" class="w-12 h-12 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:bg-primary hover:text-white hover:border-primary transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                </a>
                            @endif
                            @if($site->instagram)
                                <a href="{{ $site->instagram }}" target="_blank" rel="noopener" class="w-12 h-12 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:bg-primary hover:text-white hover:border-primary transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                </a>
                            @endif
                            @if($site->linkedin)
                                <a href="{{ $site->linkedin }}" target="_blank" rel="noopener" class="w-12 h-12 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:bg-primary hover:text-white hover:border-primary transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                                </a>
                            @endif
                            @if($site->twitter)
                                <a href="{{ $site->twitter }}" target="_blank" rel="noopener" class="w-12 h-12 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:bg-primary hover:text-white hover:border-primary transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                </a>
                            @endif
                            @if($site->youtube)
                                <a href="{{ $site->youtube }}" target="_blank" rel="noopener" class="w-12 h-12 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:bg-primary hover:text-white hover:border-primary transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Quick Response Promise -->
                <div class="bg-primary/5 border border-primary/20 rounded-2xl p-6">
                    <div class="flex items-start">
                        <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-1">Quick Response Guaranteed</h3>
                            <p class="text-gray-600 text-sm">I typically respond to all inquiries within 2-4 hours during business hours. Your time is valuable, and I respect that.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="inline-block px-4 py-1 rounded-full text-sm font-medium bg-primary/10 text-primary mb-4">
                Common Questions
            </span>
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
            <p class="text-xl text-gray-600">Quick answers to questions you may have</p>
        </div>

        <div class="space-y-4" x-data="{ open: null }">
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <button @click="open = open === 1 ? null : 1" class="w-full px-6 py-5 text-left flex items-center justify-between">
                    <span class="font-semibold text-gray-900">What areas do you serve?</span>
                    <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open === 1 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 1" x-collapse class="px-6 pb-5 text-gray-600">
                    I serve the greater metropolitan area and surrounding communities. Contact me to discuss your specific location needs.
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <button @click="open = open === 2 ? null : 2" class="w-full px-6 py-5 text-left flex items-center justify-between">
                    <span class="font-semibold text-gray-900">How long does it take to buy a home?</span>
                    <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open === 2 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 2" x-collapse class="px-6 pb-5 text-gray-600">
                    The home buying process typically takes 30-60 days from accepted offer to closing. Finding the right home can take anywhere from a few weeks to several months depending on your needs and the market conditions.
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <button @click="open = open === 3 ? null : 3" class="w-full px-6 py-5 text-left flex items-center justify-between">
                    <span class="font-semibold text-gray-900">What should I do before selling my home?</span>
                    <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open === 3 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 3" x-collapse class="px-6 pb-5 text-gray-600">
                    I recommend a consultation to discuss your goals and timeline. We'll review your property, discuss any improvements that could increase value, and create a customized marketing strategy.
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <button @click="open = open === 4 ? null : 4" class="w-full px-6 py-5 text-left flex items-center justify-between">
                    <span class="font-semibold text-gray-900">Do you work with first-time home buyers?</span>
                    <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open === 4 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 4" x-collapse class="px-6 pb-5 text-gray-600">
                    Absolutely! I love helping first-time buyers navigate the process. I'll guide you through every step, from getting pre-approved to closing on your new home.
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $metaTitle ?? $site?->site_name ?? $tenant->name }} | Luxury Real Estate</title>
    <meta name="description" content="{{ $metaDescription ?? $site?->meta_description ?? 'Luxury real estate services' }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cormorant-garamond:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://fonts.bunny.net/css?family=montserrat:300,400,500,600&display=swap" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '{{ $site?->primary_color ?? "#1A1A1A" }}',
                        secondary: '{{ $site?->secondary_color ?? "#B8860B" }}',
                    },
                    fontFamily: {
                        sans: ['Montserrat', 'sans-serif'],
                        serif: ['Cormorant Garamond', 'serif'],
                    },
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="font-sans antialiased bg-white">
    @include('templates.luxury.partials.nav')
    <main>@yield('content')</main>
    @include('templates.luxury.partials.footer')
    @stack('scripts')
</body>
</html>

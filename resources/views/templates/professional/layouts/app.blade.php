<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $metaTitle ?? $site?->site_name ?? $tenant->name }} | Real Estate</title>
    <meta name="description" content="{{ $metaDescription ?? $site?->meta_description ?? 'Professional real estate services' }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=playfair-display:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://fonts.bunny.net/css?family=source-sans-3:400,500,600,700&display=swap" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '{{ $site?->primary_color ?? "#1E3A5F" }}',
                        secondary: '{{ $site?->secondary_color ?? "#C9A962" }}',
                    },
                    fontFamily: {
                        sans: ['Source Sans 3', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    },
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="font-sans antialiased bg-white">
    @include('templates.professional.partials.nav')
    <main>@yield('content')</main>
    @include('templates.professional.partials.footer')
    @stack('scripts')
</body>
</html>

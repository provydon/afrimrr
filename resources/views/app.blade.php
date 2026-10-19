<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }
        </style>

        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="icon" href="/favicon-96.png" sizes="96x96" type="image/png">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @php
            $metaTitle = $ogTitle ?? 'AfricanMRR — Verified MRR for African startups';
            $metaDescription = $ogDescription ?? 'Real recurring revenue from Paystack, Flutterwave and Stripe. No screenshots, no vanity metrics.';
            $metaImage = $ogImage ?? config('app.url').'/og-image.png';
        @endphp
        <meta name="description" content="{{ $metaDescription }}">
        <meta name="theme-color" content="#059669">
        <link rel="canonical" href="{{ config('app.url') }}{{ request()->getPathInfo() }}">
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="{{ config('app.name') }}">
        <meta property="og:title" content="{{ $metaTitle }}">
        <meta property="og:description" content="{{ $metaDescription }}">
        <meta property="og:image" content="{{ $metaImage }}">
        <meta property="og:url" content="{{ config('app.url') }}{{ request()->getPathInfo() }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $metaTitle }}">
        <meta name="twitter:description" content="{{ $metaDescription }}">
        <meta name="twitter:image" content="{{ $metaImage }}">

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        <x-inertia::head>
            <title>{{ config('app.name', 'Laravel') }}</title>
        </x-inertia::head>
    </head>
    <body class="font-sans antialiased">
        <x-inertia::app />
    </body>
</html>

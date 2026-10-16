<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { BadgeCheck, Check } from '@lucide/vue';

const tiers = [
    {
        name: 'Digital badge',
        price: 'Free',
        blurb: 'Live, embeddable SVG that always shows your current verified MRR.',
        features: ['Auto-updating SVG', 'Public profile page', 'Leaderboard ranking', 'Public API access'],
        cta: 'Get started',
        href: '/register',
        highlight: false,
    },
    {
        name: 'Plexiglass badge',
        price: '$49',
        blurb: 'A premium frosted-acrylic trophy of your verified milestone. Shipped across Africa.',
        features: ['Handcrafted acrylic plaque', 'Your MRR milestone engraved', 'QR to your verified profile', 'Worldwide shipping'],
        cta: 'Order mine',
        href: '/register',
        highlight: true,
    },
    {
        name: 'Pro',
        price: '$12/mo',
        blurb: 'For founders who want the full growth toolkit and premium placement.',
        features: ['Featured on leaderboard', 'Custom badge themes', 'Private MRR (verified, hidden amount)', 'Priority sync'],
        cta: 'Go Pro',
        href: '/register',
        highlight: false,
    },
];
</script>

<template>
    <Head title="Badges & pricing" />

    <div class="mx-auto max-w-6xl px-4 py-16">
        <div class="text-center">
            <span class="inline-flex items-center gap-2 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-600">
                <BadgeCheck class="size-3.5" /> Flex your real numbers
            </span>
            <h1 class="mt-5 text-4xl font-extrabold tracking-tight">Wear your MRR like a badge of honour</h1>
            <p class="mx-auto mt-3 max-w-2xl text-muted-foreground">
                Start free with a live embeddable badge. Celebrate a milestone with a physical plaque.
            </p>
        </div>

        <div class="mt-12 grid gap-6 lg:grid-cols-3">
            <div
                v-for="t in tiers"
                :key="t.name"
                class="flex flex-col rounded-2xl border p-6"
                :class="t.highlight ? 'border-emerald-500 bg-emerald-500/5 shadow-lg' : 'border-border bg-card'"
            >
                <h2 class="text-lg font-bold">{{ t.name }}</h2>
                <p class="mt-2 text-3xl font-extrabold">{{ t.price }}</p>
                <p class="mt-2 text-sm text-muted-foreground">{{ t.blurb }}</p>
                <ul class="mt-5 flex-1 space-y-2 text-sm">
                    <li v-for="f in t.features" :key="f" class="flex items-center gap-2">
                        <Check class="size-4 text-emerald-600" /> {{ f }}
                    </li>
                </ul>
                <Link
                    :href="t.href"
                    class="mt-6 rounded-lg px-4 py-2.5 text-center text-sm font-semibold transition"
                    :class="t.highlight ? 'bg-emerald-600 text-white hover:bg-emerald-500' : 'border border-border hover:bg-accent'"
                >{{ t.cta }}</Link>
            </div>
        </div>
    </div>
</template>

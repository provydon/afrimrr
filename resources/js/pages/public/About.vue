<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="About" />
    <div class="mx-auto max-w-3xl px-4 py-16">
        <h1 class="text-3xl font-extrabold tracking-tight">Why AfriMRR exists</h1>
        <div class="mt-6 space-y-4 text-muted-foreground">
            <p>
                African founders build real, revenue-generating businesses every day — but the ecosystem
                still runs on screenshots, rounded-up numbers and vibes. Investors can't tell signal from
                noise, and honest founders don't get the credit they've earned.
            </p>
            <p>
                AfriMRR fixes that. We plug straight into the payment rails African startups actually use —
                Paystack, Flutterwave and Stripe — and turn live subscription data into a verified,
                always-current MRR figure. No manual entry. No cheating.
            </p>
            <p>
                The result is a public leaderboard, embeddable trust badges, and a marketplace where a
                revenue multiple actually means something. Build in public, prove your numbers, and let
                the work speak.
            </p>
        </div>
        <Link href="/register" class="mt-8 inline-block rounded-lg bg-emerald-600 px-6 py-3 font-semibold text-white hover:bg-emerald-500">
            Verify your startup
        </Link>
    </div>
</template>

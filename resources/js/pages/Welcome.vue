<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { BadgeCheck, LineChart, ShieldCheck, Zap } from '@lucide/vue';
import type { StartupCardData } from '@/types/mrr';

defineProps<{
    topStartups: StartupCardData[];
    stats: { verified_count: number; total_mrr_display: string; country_count: number };
}>();

const providers = ['Paystack', 'Flutterwave', 'Stripe'];
</script>

<template>
    <Head title="Verified MRR for African startups" />

    <!-- Hero -->
    <section class="relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(60%_60%_at_50%_0%,rgba(16,185,129,0.12),transparent)]" />
        <div class="mx-auto max-w-6xl px-4 py-20 text-center">
            <span class="inline-flex items-center gap-2 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-600">
                <BadgeCheck class="size-3.5" /> No fake MRR. Pulled from the source.
            </span>
            <h1 class="mx-auto mt-6 max-w-3xl text-4xl font-extrabold tracking-tight sm:text-6xl">
                The verified revenue board for
                <span class="text-emerald-600">African startups</span>
            </h1>
            <p class="mx-auto mt-5 max-w-2xl text-lg text-muted-foreground">
                Connect Paystack, Flutterwave or Stripe and show the world your real,
                verified MRR — not a screenshot. Climb the leaderboard, earn a badge, list to sell.
            </p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <Link href="/register" class="rounded-lg bg-emerald-600 px-6 py-3 font-semibold text-white transition hover:bg-emerald-500">
                    Verify my startup
                </Link>
                <Link href="/leaderboard" class="rounded-lg border border-border bg-background px-6 py-3 font-semibold transition hover:bg-accent">
                    See the leaderboard
                </Link>
            </div>

            <div class="mx-auto mt-14 grid max-w-2xl grid-cols-3 gap-4">
                <div>
                    <p class="text-3xl font-bold">{{ stats.total_mrr_display }}</p>
                    <p class="text-sm text-muted-foreground">Verified MRR tracked</p>
                </div>
                <div>
                    <p class="text-3xl font-bold">{{ stats.verified_count }}</p>
                    <p class="text-sm text-muted-foreground">Verified startups</p>
                </div>
                <div>
                    <p class="text-3xl font-bold">{{ stats.country_count }}</p>
                    <p class="text-sm text-muted-foreground">Countries</p>
                </div>
            </div>

            <div class="mt-12 flex flex-wrap items-center justify-center gap-x-8 gap-y-2 text-sm font-medium text-muted-foreground">
                <span>Verifies with</span>
                <span v-for="p in providers" :key="p" class="font-semibold text-foreground">{{ p }}</span>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section class="border-y border-border bg-muted/30">
        <div class="mx-auto grid max-w-6xl gap-6 px-4 py-16 md:grid-cols-3">
            <div class="rounded-xl border border-border bg-card p-6">
                <Zap class="size-6 text-emerald-600" />
                <h3 class="mt-4 font-semibold">1. Connect your processor</h3>
                <p class="mt-2 text-sm text-muted-foreground">
                    Paste a read-only API key from Paystack, Flutterwave or Stripe. We read your
                    active subscriptions — nothing else.
                </p>
            </div>
            <div class="rounded-xl border border-border bg-card p-6">
                <LineChart class="size-6 text-emerald-600" />
                <h3 class="mt-4 font-semibold">2. We normalize your MRR</h3>
                <p class="mt-2 text-sm text-muted-foreground">
                    Every plan interval and currency is normalized to a monthly figure in USD, refreshed
                    hourly and snapshotted for growth tracking.
                </p>
            </div>
            <div class="rounded-xl border border-border bg-card p-6">
                <ShieldCheck class="size-6 text-emerald-600" />
                <h3 class="mt-4 font-semibold">3. Flex your verified badge</h3>
                <p class="mt-2 text-sm text-muted-foreground">
                    Embed a live badge, rank on the leaderboard, and list your startup for sale with a
                    credible revenue multiple.
                </p>
            </div>
        </div>
    </section>

    <!-- Top startups -->
    <section class="mx-auto max-w-6xl px-4 py-16">
        <div class="flex items-end justify-between">
            <h2 class="text-2xl font-bold">🏆 Top verified startups</h2>
            <Link href="/leaderboard" class="text-sm font-semibold text-emerald-600 hover:underline">View all →</Link>
        </div>

        <div v-if="topStartups.length" class="mt-6 overflow-hidden rounded-xl border border-border">
            <Link
                v-for="s in topStartups"
                :key="s.slug"
                :href="`/startups/${s.slug}`"
                class="flex items-center gap-4 border-b border-border bg-card px-4 py-3 last:border-0 hover:bg-accent"
            >
                <span class="w-6 text-center font-bold text-muted-foreground">{{ s.rank }}</span>
                <span class="flex size-9 items-center justify-center rounded-lg bg-emerald-600/10 text-sm font-bold text-emerald-600">{{ s.name.charAt(0) }}</span>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-1 font-semibold">
                        {{ s.name }}
                        <BadgeCheck v-if="s.is_verified" class="size-4 text-emerald-600" />
                    </div>
                    <p class="truncate text-xs text-muted-foreground">{{ s.category_label }} · {{ s.country_label }}</p>
                </div>
                <span class="font-bold">{{ s.mrr_usd_display }}</span>
            </Link>
        </div>
        <p v-else class="mt-6 rounded-xl border border-dashed border-border p-10 text-center text-muted-foreground">
            No startups yet. <Link href="/register" class="font-semibold text-emerald-600">Be the first to verify →</Link>
        </p>
    </section>

    <!-- CTA -->
    <section class="mx-auto max-w-6xl px-4 pb-20">
        <div class="rounded-2xl bg-emerald-600 px-8 py-12 text-center text-white">
            <h2 class="text-3xl font-bold">Stop screenshotting your Paystack &amp; Flutterwave dashboards.</h2>
            <p class="mx-auto mt-3 max-w-xl text-emerald-50">
                Get a tamper-proof, always-live MRR badge the whole ecosystem can trust.
            </p>
            <Link href="/register" class="mt-6 inline-block rounded-lg bg-white px-6 py-3 font-semibold text-emerald-700 transition hover:bg-emerald-50">
                Verify my startup free
            </Link>
        </div>
    </section>
</template>

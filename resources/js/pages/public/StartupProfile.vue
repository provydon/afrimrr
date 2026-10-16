<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { toast } from 'vue-sonner';
import { BadgeCheck, ExternalLink, Globe, TrendingDown, TrendingUp } from '@lucide/vue';
import type { StartupProfileData } from '@/types/mrr';

const props = defineProps<{
    startup: StartupProfileData;
    badgeUrl: string;
    embedCode: string;
}>();

const sourceLabel = computed(() => {
    const s = props.startup.verification_source;
    if (!s) return null;
    if (s === 'multiple') return 'Multiple providers';
    if (s === 'manual') return 'Self-reported';
    return s.charAt(0).toUpperCase() + s.slice(1);
});

// Build a simple sparkline path from snapshot history.
const spark = computed(() => {
    const pts = props.startup.history;
    if (pts.length < 2) return null;
    const vals = pts.map((p) => p.mrr_usd);
    const min = Math.min(...vals);
    const max = Math.max(...vals);
    const range = max - min || 1;
    const w = 600;
    const h = 120;
    return pts
        .map((p, i) => {
            const x = (i / (pts.length - 1)) * w;
            const y = h - ((p.mrr_usd - min) / range) * h;
            return `${i === 0 ? 'M' : 'L'}${x.toFixed(1)},${y.toFixed(1)}`;
        })
        .join(' ');
});

function copyEmbed() {
    navigator.clipboard.writeText(props.embedCode).then(() => toast.success('Embed code copied'));
}
</script>

<template>
    <Head :title="startup.name" />

    <div class="mx-auto max-w-4xl px-4 py-12">
        <!-- Header -->
        <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex items-center gap-4">
                <img v-if="startup.logo_url" :src="startup.logo_url" :alt="startup.name" class="size-16 rounded-xl object-cover" />
                <span v-else class="flex size-16 items-center justify-center rounded-xl bg-emerald-600/10 text-2xl font-bold text-emerald-600">{{ startup.name.charAt(0) }}</span>
                <div>
                    <h1 class="flex items-center gap-2 text-2xl font-extrabold">
                        {{ startup.name }}
                        <BadgeCheck v-if="startup.is_verified" class="size-6 text-emerald-600" />
                    </h1>
                    <p class="text-muted-foreground">{{ startup.tagline }}</p>
                    <div class="mt-1 flex flex-wrap items-center gap-2 text-xs">
                        <span class="rounded-full bg-muted px-2 py-0.5 font-medium">{{ startup.category_label }}</span>
                        <span class="rounded-full bg-muted px-2 py-0.5 font-medium">{{ startup.country_label }}</span>
                        <span v-if="startup.founded_year" class="text-muted-foreground">Est. {{ startup.founded_year }}</span>
                    </div>
                </div>
            </div>
            <a v-if="startup.website" :href="startup.website" target="_blank" rel="noopener"
               class="inline-flex items-center gap-1.5 self-start rounded-lg border border-border px-3 py-2 text-sm font-medium hover:bg-accent">
                <Globe class="size-4" /> Visit site <ExternalLink class="size-3.5" />
            </a>
        </div>

        <!-- Verified banner -->
        <div class="mt-6 flex items-center gap-2 rounded-lg border px-4 py-3 text-sm"
             :class="startup.is_verified ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-700 dark:text-emerald-400' : 'border-border bg-muted/40 text-muted-foreground'">
            <BadgeCheck class="size-4" />
            <span v-if="startup.is_verified">
                Revenue verified via <strong>{{ sourceLabel }}</strong>
                <template v-if="startup.last_synced_at"> · updated {{ startup.last_synced_at }}</template>
            </span>
            <span v-else>Self-reported — not yet verified by a payment provider.</span>
        </div>

        <!-- Stat tiles -->
        <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div class="rounded-xl border border-border bg-card p-4">
                <p class="text-xs uppercase tracking-wide text-muted-foreground">MRR</p>
                <p class="mt-1 text-2xl font-bold">{{ startup.mrr_usd_display }}</p>
                <p v-if="startup.display_currency !== 'USD'" class="text-xs text-muted-foreground">{{ startup.mrr_local_display }}</p>
            </div>
            <div class="rounded-xl border border-border bg-card p-4">
                <p class="text-xs uppercase tracking-wide text-muted-foreground">ARR</p>
                <p class="mt-1 text-2xl font-bold">{{ startup.arr_usd_display }}</p>
            </div>
            <div class="rounded-xl border border-border bg-card p-4">
                <p class="text-xs uppercase tracking-wide text-muted-foreground">MoM growth</p>
                <p class="mt-1 flex items-center gap-1 text-2xl font-bold" :class="(startup.mom_growth ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-500'">
                    <template v-if="startup.mom_growth !== null">
                        <TrendingUp v-if="startup.mom_growth >= 0" class="size-5" />
                        <TrendingDown v-else class="size-5" />
                        {{ startup.mom_growth >= 0 ? '+' : '' }}{{ startup.mom_growth }}%
                    </template>
                    <template v-else>—</template>
                </p>
            </div>
            <div class="rounded-xl border border-border bg-card p-4">
                <p class="text-xs uppercase tracking-wide text-muted-foreground">Active subs</p>
                <p class="mt-1 text-2xl font-bold">{{ startup.active_subscriptions.toLocaleString() }}</p>
            </div>
        </div>

        <!-- Sparkline -->
        <div v-if="spark" class="mt-6 rounded-xl border border-border bg-card p-5">
            <p class="text-sm font-semibold">MRR over time</p>
            <svg viewBox="0 0 600 120" preserveAspectRatio="none" class="mt-3 h-28 w-full">
                <path :d="spark" fill="none" stroke="#16a34a" stroke-width="2.5" vector-effect="non-scaling-stroke" />
            </svg>
        </div>

        <!-- For sale -->
        <div v-if="startup.is_for_sale && startup.asking_price_display" class="mt-6 rounded-xl border border-amber-500/40 bg-amber-500/10 p-5">
            <p class="text-sm font-bold uppercase tracking-wide text-amber-600">For sale</p>
            <div class="mt-2 flex items-baseline gap-3">
                <span class="text-3xl font-extrabold">{{ startup.asking_price_display }}</span>
                <span v-if="startup.revenue_multiple" class="text-sm text-muted-foreground">{{ startup.revenue_multiple }}× ARR</span>
            </div>
        </div>

        <!-- Description -->
        <div v-if="startup.description" class="mt-6 rounded-xl border border-border bg-card p-5">
            <h2 class="font-semibold">About</h2>
            <p class="mt-2 whitespace-pre-line text-sm text-muted-foreground">{{ startup.description }}</p>
        </div>

        <!-- Founder -->
        <Link v-if="startup.founder.username" :href="`/founders/${startup.founder.username}`"
              class="mt-6 flex items-center gap-3 rounded-xl border border-border bg-card p-5 hover:bg-accent">
            <img v-if="startup.founder.avatar_url" :src="startup.founder.avatar_url" class="size-10 rounded-full" :alt="startup.founder.name ?? ''" />
            <span v-else class="flex size-10 items-center justify-center rounded-full bg-muted font-bold">{{ (startup.founder.name ?? '?').charAt(0) }}</span>
            <div>
                <p class="text-xs text-muted-foreground">Founder</p>
                <p class="font-semibold">{{ startup.founder.name }}</p>
            </div>
        </Link>

        <!-- Embed badge -->
        <div class="mt-6 rounded-xl border border-border bg-card p-5">
            <h2 class="font-semibold">Embed your verified badge</h2>
            <p class="mt-1 text-sm text-muted-foreground">Drop this on your site. It updates automatically.</p>
            <div class="mt-4 flex items-center gap-4 rounded-lg bg-muted/40 p-4">
                <img :src="badgeUrl" :alt="`${startup.name} verified MRR`" height="56" />
            </div>
            <div class="mt-3 flex items-center gap-2">
                <code class="flex-1 overflow-x-auto rounded-md bg-muted px-3 py-2 text-xs">{{ embedCode }}</code>
                <button class="shrink-0 rounded-md bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-500" @click="copyEmbed">Copy</button>
            </div>
        </div>
    </div>
</template>

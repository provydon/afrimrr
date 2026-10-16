<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { BadgeCheck, TrendingDown, TrendingUp } from '@lucide/vue';
import type { StartupCardData } from '@/types/mrr';

const props = defineProps<{
    startups: (StartupCardData & { rank: number })[];
    categories: Record<string, string>;
    activeCategory: string;
}>();

function filterBy(cat: string) {
    router.get('/leaderboard', cat === 'all' ? {} : { category: cat }, { preserveScroll: true });
}

const medal = (rank: number) => (rank === 1 ? '🥇' : rank === 2 ? '🥈' : rank === 3 ? '🥉' : null);
</script>

<template>
    <Head title="Leaderboard" />

    <div class="mx-auto max-w-5xl px-4 py-12">
        <div class="text-center">
            <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Top 100 African startups by verified MRR</h1>
            <p class="mt-3 text-muted-foreground">Ranked by monthly recurring revenue, normalized to USD. Refreshed hourly.</p>
        </div>

        <!-- Category filter -->
        <div class="mt-8 flex flex-wrap justify-center gap-2">
            <button
                class="rounded-full px-3 py-1.5 text-sm font-medium transition"
                :class="props.activeCategory === 'all' ? 'bg-emerald-600 text-white' : 'border border-border hover:bg-accent'"
                @click="filterBy('all')"
            >All</button>
            <button
                v-for="(label, key) in props.categories"
                :key="key"
                class="rounded-full px-3 py-1.5 text-sm font-medium transition"
                :class="props.activeCategory === key ? 'bg-emerald-600 text-white' : 'border border-border hover:bg-accent'"
                @click="filterBy(key)"
            >{{ label }}</button>
        </div>

        <div class="mt-8 overflow-hidden rounded-xl border border-border">
            <div class="hidden grid-cols-[48px_1fr_140px_120px] gap-4 border-b border-border bg-muted/50 px-4 py-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground sm:grid">
                <span>#</span>
                <span>Startup</span>
                <span class="text-right">MRR</span>
                <span class="text-right">Growth</span>
            </div>

            <Link
                v-for="s in props.startups"
                :key="s.slug"
                :href="`/startups/${s.slug}`"
                class="grid grid-cols-[32px_1fr_auto] items-center gap-4 border-b border-border bg-card px-4 py-3 last:border-0 hover:bg-accent sm:grid-cols-[48px_1fr_140px_120px]"
            >
                <span class="text-center text-lg font-bold">{{ medal(s.rank) ?? s.rank }}</span>
                <div class="flex min-w-0 items-center gap-3">
                    <span class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-emerald-600/10 text-sm font-bold text-emerald-600">{{ s.name.charAt(0) }}</span>
                    <div class="min-w-0">
                        <div class="flex items-center gap-1 font-semibold">
                            <span class="truncate">{{ s.name }}</span>
                            <BadgeCheck v-if="s.is_verified" class="size-4 shrink-0 text-emerald-600" />
                        </div>
                        <p class="truncate text-xs text-muted-foreground">{{ s.category_label }} · {{ s.country_label }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-bold">{{ s.mrr_usd_display }}</p>
                    <p v-if="s.display_currency !== 'USD'" class="text-xs text-muted-foreground">{{ s.mrr_local_display }}</p>
                </div>
                <div class="hidden text-right sm:block">
                    <span
                        v-if="s.mom_growth !== null"
                        class="inline-flex items-center gap-1 text-sm font-semibold"
                        :class="s.mom_growth >= 0 ? 'text-emerald-600' : 'text-red-500'"
                    >
                        <TrendingUp v-if="s.mom_growth >= 0" class="size-4" />
                        <TrendingDown v-else class="size-4" />
                        {{ s.mom_growth >= 0 ? '+' : '' }}{{ s.mom_growth }}%
                    </span>
                    <span v-else class="text-sm text-muted-foreground">—</span>
                </div>
            </Link>

            <p v-if="!props.startups.length" class="bg-card p-12 text-center text-muted-foreground">
                No startups in this category yet.
            </p>
        </div>
    </div>
</template>

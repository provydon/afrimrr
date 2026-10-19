<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { toast } from 'vue-sonner';
import { ExternalLink, Globe, Share2 } from '@lucide/vue';
import StartupCard from '@/components/StartupCard.vue';
import type { Founder, FounderStats, StartupCardData } from '@/types/mrr';

const props = defineProps<{
    founder: Founder;
    startups: StartupCardData[];
    stats: FounderStats;
}>();

const verifiedLabel = computed(() => {
    const n = props.stats.verified_startups;
    return `${n} ${n === 1 ? 'startup' : 'startups'} with verified revenue`;
});

function share() {
    const url = window.location.href;
    const nav = navigator as Navigator & { share?: (data: { title?: string; url?: string }) => Promise<void> };
    if (nav.share) {
        nav.share({ title: props.founder.name ?? 'Founder', url }).catch(() => {});
        return;
    }
    navigator.clipboard.writeText(url).then(() => toast.success('Profile link copied'));
}
</script>

<template>
    <Head :title="founder.name ?? 'Founder'" />

    <div class="mx-auto max-w-5xl px-4 py-8">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-1.5 text-sm text-muted-foreground">
            <Link href="/" class="hover:text-foreground">AfricanMRR</Link>
            <span>/</span>
            <Link href="/startups" class="hover:text-foreground">Founder</Link>
            <span>/</span>
            <span class="font-medium text-foreground">{{ founder.name }}</span>
        </nav>

        <!-- Header -->
        <div class="mt-6 flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex items-center gap-4">
                <img v-if="founder.avatar_url" :src="founder.avatar_url" :alt="founder.name ?? ''" class="size-20 rounded-full object-cover" />
                <span v-else class="flex size-20 items-center justify-center rounded-full bg-emerald-600/10 text-3xl font-bold text-emerald-600">{{ (founder.name ?? '?').charAt(0) }}</span>
                <div>
                    <h1 class="flex flex-wrap items-baseline gap-x-2 text-2xl font-extrabold">
                        {{ founder.name }}
                        <a v-if="founder.twitter" :href="founder.twitter_url ?? undefined" target="_blank" rel="noopener" class="text-base font-normal text-muted-foreground hover:text-foreground">@{{ founder.twitter.replace(/^@/, '') }}</a>
                    </h1>
                    <p class="mt-0.5 text-sm text-muted-foreground">{{ verifiedLabel }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" @click="share"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-border px-3 py-2 text-sm font-medium hover:bg-accent">
                    <Share2 class="size-4" /> Share
                </button>
                <a v-if="founder.twitter_url" :href="founder.twitter_url" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1.5 rounded-lg bg-foreground px-3 py-2 text-sm font-medium text-background hover:opacity-90">
                    Visit
                    <svg viewBox="0 0 24 24" class="size-3.5" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24h-6.66l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231 5.45-6.231Zm-1.161 17.52h1.833L7.084 4.126H5.117L17.083 19.77Z" /></svg>
                    profile
                    <ExternalLink class="size-3.5" />
                </a>
            </div>
        </div>

        <!-- Stat cards -->
        <div class="mt-8 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="rounded-xl border border-border bg-card p-5">
                <p class="text-sm text-muted-foreground">MRR</p>
                <p class="mt-2 text-2xl font-bold tabular-nums">{{ stats.total_mrr_display }}</p>
                <p class="mt-1 text-xs text-muted-foreground">Across all startups</p>
            </div>
            <div class="rounded-xl border border-border bg-card p-5">
                <p class="text-sm text-muted-foreground">Annual run-rate</p>
                <p class="mt-2 text-2xl font-bold tabular-nums">{{ stats.arr_display }}</p>
                <p class="mt-1 text-xs text-muted-foreground">MRR × 12</p>
            </div>
            <div class="rounded-xl border border-border bg-card p-5">
                <p class="flex items-center gap-1.5 text-sm text-muted-foreground">
                    <svg viewBox="0 0 24 24" class="size-3.5" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24h-6.66l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231 5.45-6.231Zm-1.161 17.52h1.833L7.084 4.126H5.117L17.083 19.77Z" /></svg>
                    followers
                </p>
                <p class="mt-2 text-2xl font-bold tabular-nums">{{ stats.followers_display ?? '—' }}</p>
                <p class="mt-1 text-xs text-muted-foreground">
                    <a v-if="founder.twitter" :href="founder.twitter_url ?? undefined" target="_blank" rel="noopener" class="hover:text-foreground">@{{ founder.twitter.replace(/^@/, '') }}</a>
                    <span v-else>Not connected</span>
                </p>
            </div>
            <div class="rounded-xl border border-border bg-card p-5">
                <p class="text-sm text-muted-foreground">Total startups</p>
                <p class="mt-2 text-2xl font-bold tabular-nums">{{ stats.total_startups }}</p>
                <p class="mt-1 text-xs text-muted-foreground">{{ stats.verified_startups }} verified</p>
            </div>
        </div>

        <!-- Optional bio + website -->
        <div v-if="founder.bio || founder.website" class="mt-6 flex flex-col gap-2 text-sm">
            <p v-if="founder.bio" class="max-w-2xl text-muted-foreground">{{ founder.bio }}</p>
            <a v-if="founder.website" :href="founder.website" target="_blank" rel="noopener" class="inline-flex w-fit items-center gap-1.5 text-muted-foreground hover:text-foreground">
                <Globe class="size-4" /> {{ founder.website.replace(/^https?:\/\//, '') }}
            </a>
        </div>

        <!-- Startups -->
        <h2 class="mt-10 text-lg font-bold">Startups</h2>
        <div v-if="startups.length" class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <StartupCard v-for="(s, i) in startups" :key="s.slug ?? `anon-${i}`" :startup="s" />
        </div>
        <p v-else class="mt-4 rounded-xl border border-dashed border-border p-10 text-center text-muted-foreground">No public startups yet.</p>
    </div>
</template>

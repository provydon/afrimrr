<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { AtSign, Globe } from '@lucide/vue';
import StartupCard from '@/components/StartupCard.vue';
import type { Founder, StartupCardData } from '@/types/mrr';

defineProps<{
    founder: Founder;
    startups: StartupCardData[];
    totalMrrDisplay: string;
}>();
</script>

<template>
    <Head :title="founder.name ?? 'Founder'" />

    <div class="mx-auto max-w-5xl px-4 py-12">
        <div class="flex flex-col items-center gap-4 text-center">
            <img v-if="founder.avatar_url" :src="founder.avatar_url" class="size-20 rounded-full" :alt="founder.name ?? ''" />
            <span v-else class="flex size-20 items-center justify-center rounded-full bg-emerald-600/10 text-3xl font-bold text-emerald-600">{{ (founder.name ?? '?').charAt(0) }}</span>
            <div>
                <h1 class="text-2xl font-extrabold">{{ founder.name }}</h1>
                <p v-if="founder.headline" class="text-muted-foreground">{{ founder.headline }}</p>
            </div>
            <p v-if="founder.bio" class="max-w-xl text-sm text-muted-foreground">{{ founder.bio }}</p>
            <div class="flex items-center gap-3">
                <a v-if="founder.twitter" :href="`https://twitter.com/${founder.twitter}`" target="_blank" rel="noopener" class="text-muted-foreground hover:text-foreground"><AtSign class="size-5" /></a>
                <a v-if="founder.website" :href="founder.website" target="_blank" rel="noopener" class="text-muted-foreground hover:text-foreground"><Globe class="size-5" /></a>
            </div>
            <div class="rounded-xl border border-border bg-card px-6 py-3">
                <p class="text-xs uppercase tracking-wide text-muted-foreground">Total verified MRR</p>
                <p class="text-2xl font-bold">{{ totalMrrDisplay }}</p>
            </div>
        </div>

        <h2 class="mt-12 text-lg font-bold">Startups</h2>
        <div v-if="startups.length" class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <StartupCard v-for="s in startups" :key="s.slug" :startup="s" />
        </div>
        <p v-else class="mt-4 rounded-xl border border-dashed border-border p-10 text-center text-muted-foreground">No public startups yet.</p>
    </div>
</template>

<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import StartupCard from '@/components/StartupCard.vue';
import type { StartupCardData } from '@/types/mrr';

const props = defineProps<{
    listings: StartupCardData[];
    sort: string;
}>();

function sortBy(e: Event) {
    router.get('/marketplace', { sort: (e.target as HTMLSelectElement).value }, { preserveScroll: true });
}
</script>

<template>
    <Head title="Marketplace" />

    <div class="mx-auto max-w-6xl px-4 py-12">
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight">Startup marketplace</h1>
                <p class="mt-2 text-muted-foreground">Acquire African startups with verified revenue. Every multiple is backed by real MRR.</p>
            </div>
            <select :value="props.sort" class="rounded-lg border border-border bg-background px-3 py-2 text-sm" @change="sortBy">
                <option value="recent">Recently listed</option>
                <option value="price">Highest price</option>
                <option value="mrr">Highest MRR</option>
            </select>
        </div>

        <div v-if="props.listings.length" class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <StartupCard v-for="s in props.listings" :key="s.slug" :startup="s" :show-price="true" />
        </div>
        <p v-else class="mt-12 rounded-xl border border-dashed border-border p-12 text-center text-muted-foreground">
            No startups are for sale right now. Own one? List it from your dashboard.
        </p>
    </div>
</template>

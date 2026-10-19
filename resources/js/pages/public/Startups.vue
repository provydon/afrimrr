<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive, watch } from 'vue';
import { Search } from '@lucide/vue';
import StartupCard from '@/components/StartupCard.vue';
import type { StartupCardData } from '@/types/mrr';

interface Paginated {
    data: StartupCardData[];
    links: { url: string | null; label: string; active: boolean }[];
    total: number;
}

const props = defineProps<{
    startups: Paginated;
    filters: { q: string; category: string; country: string; sort: string };
    categories: Record<string, string>;
    countries: Record<string, string>;
}>();

const form = reactive({ ...props.filters });

let timer: ReturnType<typeof setTimeout>;
watch(form, () => {
    clearTimeout(timer);
    timer = setTimeout(() => {
        router.get('/startups', { ...form }, { preserveState: true, preserveScroll: true, replace: true });
    }, 300);
});
</script>

<template>
    <Head title="All startups" />

    <div class="mx-auto max-w-6xl px-4 py-12">
        <h1 class="text-3xl font-extrabold tracking-tight">Browse startups</h1>
        <p class="mt-2 text-muted-foreground">{{ props.startups.total }} startups building in public across Africa.</p>

        <!-- Filters -->
        <div class="mt-6 flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                <input
                    v-model="form.q"
                    type="search"
                    placeholder="Search startups…"
                    class="w-full rounded-lg border border-border bg-background py-2 pl-9 pr-3 text-sm outline-none focus:border-emerald-500"
                />
            </div>
            <select v-model="form.category" class="rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none focus:border-emerald-500">
                <option value="all">All categories</option>
                <option v-for="(label, key) in props.categories" :key="key" :value="key">{{ label }}</option>
            </select>
            <select v-model="form.country" class="rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none focus:border-emerald-500">
                <option value="all">All countries</option>
                <option v-for="(label, key) in props.countries" :key="key" :value="key">{{ label }}</option>
            </select>
            <select v-model="form.sort" class="rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none focus:border-emerald-500">
                <option value="mrr">Top MRR</option>
                <option value="growth">Fastest growing</option>
                <option value="recent">Recently added</option>
            </select>
        </div>

        <div v-if="props.startups.data.length" class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <StartupCard v-for="(s, i) in props.startups.data" :key="s.slug ?? `anon-${i}`" :startup="s" />
        </div>
        <p v-else class="mt-12 rounded-xl border border-dashed border-border p-12 text-center text-muted-foreground">
            No startups match your filters.
        </p>

        <!-- Pagination -->
        <div v-if="props.startups.links.length > 3" class="mt-8 flex flex-wrap justify-center gap-1">
            <component
                :is="link.url ? Link : 'span'"
                v-for="link in props.startups.links"
                :key="link.label"
                :href="link.url"
                class="min-w-9 rounded-md px-3 py-2 text-center text-sm"
                :class="link.active ? 'bg-emerald-600 text-white' : link.url ? 'border border-border hover:bg-accent' : 'text-muted-foreground'"
                v-html="link.label"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Download, Search } from '@lucide/vue';

interface Subscriber {
    id: number;
    email: string;
    source: string | null;
    created_at: string;
}
interface Paginator<T> {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
    from: number | null;
    to: number | null;
    total: number;
}

const props = defineProps<{
    subscribers: Paginator<Subscriber>;
    total: number;
    filters: { q: string };
}>();

const q = ref(props.filters.q ?? '');

function search() {
    router.get('/admin/subscribers', { q: q.value }, { preserveState: true, replace: true });
}
</script>

<template>
    <Head title="Subscribers · Admin" />

    <div class="mx-auto max-w-5xl px-4 py-8">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <Link href="/admin" class="text-sm text-muted-foreground hover:text-foreground">← Admin</Link>
                <h1 class="text-2xl font-extrabold">Subscribers <span class="text-muted-foreground">({{ total }})</span></h1>
            </div>
            <a href="/admin/subscribers/export" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                <Download class="size-4" /> Export CSV
            </a>
        </div>

        <form class="mt-5 flex gap-2" @submit.prevent="search">
            <div class="relative flex-1">
                <Search class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                <input v-model="q" type="search" placeholder="Search email…" class="w-full rounded-lg border border-border bg-background py-2 pl-9 pr-3 text-sm outline-none focus:border-emerald-500" />
            </div>
            <button type="submit" class="rounded-lg border border-border px-4 py-2 text-sm font-medium hover:bg-accent">Search</button>
        </form>

        <div class="mt-5 overflow-hidden rounded-xl border border-border">
            <table class="w-full text-sm">
                <thead class="bg-muted/50 text-left text-xs uppercase tracking-wide text-muted-foreground">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Email</th>
                        <th class="px-4 py-3 font-semibold">Source</th>
                        <th class="px-4 py-3 text-right font-semibold">Subscribed</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <tr v-for="s in subscribers.data" :key="s.id" class="bg-card">
                        <td class="px-4 py-3">{{ s.email }}</td>
                        <td class="px-4 py-3 text-muted-foreground">{{ s.source ?? '—' }}</td>
                        <td class="px-4 py-3 text-right text-muted-foreground">{{ s.created_at }}</td>
                    </tr>
                    <tr v-if="!subscribers.data.length">
                        <td colspan="3" class="bg-card px-4 py-12 text-center text-muted-foreground">No subscribers found.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="subscribers.links.length > 3" class="mt-4 flex flex-wrap justify-center gap-1">
            <template v-for="link in subscribers.links" :key="link.label">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    preserve-state
                    class="rounded-md px-3 py-1.5 text-sm"
                    :class="link.active ? 'bg-emerald-600 text-white' : 'border border-border hover:bg-accent'"
                    v-html="link.label"
                />
                <span v-else class="rounded-md px-3 py-1.5 text-sm text-muted-foreground" v-html="link.label" />
            </template>
        </div>
    </div>
</template>

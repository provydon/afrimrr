<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { BadgeCheck, EyeOff, Search, Trash2 } from '@lucide/vue';

interface AdminStartup {
    id: number;
    name: string;
    slug: string;
    mrr_usd_display: string;
    is_verified: boolean;
    is_public: boolean;
    is_anonymous: boolean;
    is_demo: boolean;
    owner: string | null;
}
interface Paginator<T> {
    data: T[];
    links: { url: string | null; label: string; active: boolean }[];
    total: number;
}

const props = defineProps<{
    startups: Paginator<AdminStartup>;
    filters: { q: string };
}>();

const q = ref(props.filters.q ?? '');

function search() {
    router.get('/admin/startups', { q: q.value }, { preserveState: true, replace: true });
}

function togglePublic(s: AdminStartup) {
    router.put(`/admin/startups/${s.id}`, { is_public: !s.is_public }, { preserveScroll: true });
}

function remove(s: AdminStartup) {
    if (confirm(`Delete "${s.name}"? This cannot be undone.`)) {
        router.delete(`/admin/startups/${s.id}`, { preserveScroll: true });
    }
}
</script>

<template>
    <Head title="Startups · Admin" />

    <div class="mx-auto max-w-6xl px-4 py-8">
        <div>
            <Link href="/admin" class="text-sm text-muted-foreground hover:text-foreground">← Admin</Link>
            <h1 class="text-2xl font-extrabold">Startups <span class="text-muted-foreground">({{ startups.total }})</span></h1>
        </div>

        <form class="mt-5 flex gap-2" @submit.prevent="search">
            <div class="relative flex-1">
                <Search class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                <input v-model="q" type="search" placeholder="Search name…" class="w-full rounded-lg border border-border bg-background py-2 pl-9 pr-3 text-sm outline-none focus:border-emerald-500" />
            </div>
            <button type="submit" class="rounded-lg border border-border px-4 py-2 text-sm font-medium hover:bg-accent">Search</button>
        </form>

        <div class="mt-5 overflow-x-auto rounded-xl border border-border">
            <table class="w-full text-sm">
                <thead class="bg-muted/50 text-left text-xs uppercase tracking-wide text-muted-foreground">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Startup</th>
                        <th class="px-4 py-3 font-semibold">Owner</th>
                        <th class="px-4 py-3 text-right font-semibold">MRR</th>
                        <th class="px-4 py-3 text-center font-semibold">Flags</th>
                        <th class="px-4 py-3 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <tr v-for="s in startups.data" :key="s.id" class="bg-card">
                        <td class="px-4 py-3">
                            <span class="flex items-center gap-1.5 font-medium">
                                <span :class="s.is_anonymous ? 'select-none blur-[3px]' : ''">{{ s.name }}</span>
                                <BadgeCheck v-if="s.is_verified" class="size-3.5 text-emerald-600" />
                                <EyeOff v-if="s.is_anonymous" class="size-3.5 text-muted-foreground" />
                            </span>
                        </td>
                        <td class="px-4 py-3 text-muted-foreground">{{ s.owner ?? '—' }}</td>
                        <td class="px-4 py-3 text-right font-semibold tabular-nums">{{ s.mrr_usd_display }}</td>
                        <td class="px-4 py-3 text-center">
                            <span v-if="s.is_demo" class="rounded-full bg-muted px-2 py-0.5 text-[10px] font-bold uppercase text-muted-foreground">Demo</span>
                            <span v-if="!s.is_public" class="rounded-full bg-amber-500/15 px-2 py-0.5 text-[10px] font-bold uppercase text-amber-600">Private</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <button type="button" class="rounded-md border border-border px-2 py-1 text-xs font-medium hover:bg-accent" @click="togglePublic(s)">
                                    {{ s.is_public ? 'Hide' : 'Show' }}
                                </button>
                                <button type="button" class="rounded-md border border-red-500/30 p-1.5 text-red-500 hover:bg-red-500/10" @click="remove(s)">
                                    <Trash2 class="size-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!startups.data.length">
                        <td colspan="5" class="bg-card px-4 py-12 text-center text-muted-foreground">No startups found.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="startups.links.length > 3" class="mt-4 flex flex-wrap justify-center gap-1">
            <template v-for="link in startups.links" :key="link.label">
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

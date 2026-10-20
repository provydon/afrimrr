<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { BadgeCheck, ExternalLink, Plug, Plus, RefreshCw, Settings2 } from '@lucide/vue';

defineOptions({
    layout: { breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }] },
});

interface DashStartup {
    id: number;
    name: string;
    slug: string;
    category_label: string;
    is_verified: boolean;
    verification_source: string | null;
    mrr_usd_display: string;
    mrr_local_display: string;
    display_currency: string;
    mom_growth: number | null;
    is_public: boolean;
    is_for_sale: boolean;
    last_synced_at: string | null;
    connections: { provider: string; provider_label: string; status: string; last_error: string | null; last_synced_at: string | null }[];
}

defineProps<{ startups: DashStartup[]; totalMrrDisplay: string }>();

function sync(slug: string) {
    router.post(`/dashboard/startups/${slug}/sync`, {}, { preserveScroll: true });
}

function destroy(slug: string, name: string) {
    if (confirm(`Delete ${name}? This removes its connections and metrics.`)) {
        router.delete(`/dashboard/startups/${slug}`, { preserveScroll: true });
    }
}
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex flex-1 flex-col gap-6 p-4">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold">Your startups</h1>
                <p class="text-sm text-muted-foreground">Total verified MRR: <span class="font-semibold text-foreground">{{ totalMrrDisplay }}</span></p>
            </div>
            <Link href="/dashboard/startups/create" class="inline-flex items-center gap-2 self-start rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                <Plus class="size-4" /> New startup
            </Link>
        </div>

        <div v-if="!startups.length" class="rounded-xl border border-dashed border-border p-12 text-center">
            <p class="font-semibold">No startups yet</p>
            <p class="mt-1 text-sm text-muted-foreground">Add your startup and connect a payment provider to verify your MRR.</p>
            <Link href="/dashboard/startups/create" class="mt-4 inline-block rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">Add your startup</Link>
        </div>

        <div v-else class="grid gap-4">
            <div v-for="s in startups" :key="s.id" class="rounded-xl border border-border bg-card p-5">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-semibold">{{ s.name }}</h2>
                            <BadgeCheck v-if="s.is_verified" class="size-5 text-emerald-600" />
                            <span v-if="!s.is_public" class="rounded-full bg-muted px-2 py-0.5 text-xs text-muted-foreground">Private</span>
                            <span v-if="s.is_for_sale" class="rounded-full bg-amber-500/15 px-2 py-0.5 text-xs font-semibold text-amber-600">For sale</span>
                        </div>
                        <p class="text-sm text-muted-foreground">{{ s.category_label }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xl font-bold">{{ s.mrr_usd_display }}</p>
                        <p v-if="s.display_currency !== 'USD'" class="text-sm text-muted-foreground">{{ s.mrr_local_display }}</p>
                        <p v-if="s.mom_growth !== null" class="text-xs" :class="s.mom_growth >= 0 ? 'text-emerald-600' : 'text-red-500'">
                            {{ s.mom_growth >= 0 ? '+' : '' }}{{ s.mom_growth }}% MoM
                        </p>
                    </div>
                </div>

                <!-- Connections -->
                <div class="mt-4 flex flex-wrap gap-2">
                    <span v-if="!s.connections.length" class="text-sm text-muted-foreground">No provider connected — MRR is self-reported.</span>
                    <span
                        v-for="c in s.connections"
                        :key="c.provider"
                        class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs"
                        :class="c.status === 'active' ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-600' : c.status === 'error' ? 'border-red-500/30 bg-red-500/10 text-red-500' : 'border-border text-muted-foreground'"
                        :title="c.last_error ?? ''"
                    >
                        <Plug class="size-3" /> {{ c.provider_label }}: {{ c.status }}
                    </span>
                    <span v-if="s.last_synced_at" class="text-xs text-muted-foreground">· synced {{ s.last_synced_at }}</span>
                </div>

                <!-- Actions -->
                <div class="mt-4 flex flex-wrap gap-2">
                    <Link :href="`/dashboard/startups/${s.slug}/connections`" class="inline-flex items-center gap-1.5 rounded-md border border-border px-3 py-1.5 text-sm hover:bg-accent">
                        <Plug class="size-4" /> Providers
                    </Link>
                    <button v-if="s.connections.length" class="inline-flex items-center gap-1.5 rounded-md border border-border px-3 py-1.5 text-sm hover:bg-accent" @click="sync(s.slug)">
                        <RefreshCw class="size-4" /> Sync now
                    </button>
                    <Link :href="`/dashboard/startups/${s.slug}/edit`" class="inline-flex items-center gap-1.5 rounded-md border border-border px-3 py-1.5 text-sm hover:bg-accent">
                        <Settings2 class="size-4" /> Edit
                    </Link>
                    <Link :href="`/startups/${s.slug}`" class="inline-flex items-center gap-1.5 rounded-md border border-border px-3 py-1.5 text-sm hover:bg-accent">
                        <ExternalLink class="size-4" /> View public
                    </Link>
                    <button class="ml-auto rounded-md px-3 py-1.5 text-sm text-red-500 hover:bg-red-500/10" @click="destroy(s.slug, s.name)">Delete</button>
                </div>
            </div>
        </div>
    </div>
</template>

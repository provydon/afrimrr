<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { BadgeCheck, Mail, Store, Users } from '@lucide/vue';

defineProps<{
    stats: {
        subscribers: number;
        startups: number;
        verified: number;
        anonymous: number;
        for_sale: number;
        total_mrr_display: string;
    };
    recentSubscribers: { email: string; source: string | null; created_at: string | null }[];
    recentStartups: { name: string; mrr_usd_display: string; is_verified: boolean; owner: string | null; created_at: string | null }[];
}>();
</script>

<template>
    <Head title="Admin" />

    <div class="mx-auto max-w-6xl px-4 py-8">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-extrabold">Admin overview</h1>
            <div class="flex gap-2 text-sm">
                <Link href="/admin/subscribers" class="inline-flex items-center gap-1.5 rounded-lg border border-border px-3 py-2 font-medium hover:bg-accent"><Mail class="size-4" /> Subscribers</Link>
                <Link href="/admin/startups" class="inline-flex items-center gap-1.5 rounded-lg border border-border px-3 py-2 font-medium hover:bg-accent"><Store class="size-4" /> Startups</Link>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="rounded-xl border border-border bg-card p-5">
                <p class="flex items-center gap-1.5 text-sm text-muted-foreground"><Users class="size-4" /> Subscribers</p>
                <p class="mt-2 text-2xl font-bold tabular-nums">{{ stats.subscribers }}</p>
            </div>
            <div class="rounded-xl border border-border bg-card p-5">
                <p class="flex items-center gap-1.5 text-sm text-muted-foreground"><Store class="size-4" /> Startups</p>
                <p class="mt-2 text-2xl font-bold tabular-nums">{{ stats.startups }}</p>
                <p class="mt-1 text-xs text-muted-foreground">{{ stats.verified }} verified · {{ stats.anonymous }} anonymous</p>
            </div>
            <div class="rounded-xl border border-border bg-card p-5">
                <p class="flex items-center gap-1.5 text-sm text-muted-foreground"><BadgeCheck class="size-4" /> Verified MRR</p>
                <p class="mt-2 text-2xl font-bold tabular-nums">{{ stats.total_mrr_display }}</p>
                <p class="mt-1 text-xs text-muted-foreground">Across non-demo startups</p>
            </div>
            <div class="rounded-xl border border-border bg-card p-5">
                <p class="text-sm text-muted-foreground">For sale</p>
                <p class="mt-2 text-2xl font-bold tabular-nums">{{ stats.for_sale }}</p>
            </div>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-border bg-card p-5">
                <div class="flex items-center justify-between">
                    <h2 class="font-bold">Recent subscribers</h2>
                    <Link href="/admin/subscribers" class="text-sm text-emerald-600 hover:underline">View all →</Link>
                </div>
                <ul class="mt-3 divide-y divide-border text-sm">
                    <li v-for="s in recentSubscribers" :key="s.email" class="flex items-center justify-between py-2">
                        <span class="truncate">{{ s.email }}</span>
                        <span class="shrink-0 text-xs text-muted-foreground">{{ s.created_at }}</span>
                    </li>
                    <li v-if="!recentSubscribers.length" class="py-6 text-center text-muted-foreground">No subscribers yet.</li>
                </ul>
            </div>

            <div class="rounded-xl border border-border bg-card p-5">
                <div class="flex items-center justify-between">
                    <h2 class="font-bold">Recent startups</h2>
                    <Link href="/admin/startups" class="text-sm text-emerald-600 hover:underline">View all →</Link>
                </div>
                <ul class="mt-3 divide-y divide-border text-sm">
                    <li v-for="(s, i) in recentStartups" :key="i" class="flex items-center justify-between py-2">
                        <span class="flex items-center gap-1.5 truncate">
                            {{ s.name }}
                            <BadgeCheck v-if="s.is_verified" class="size-3.5 shrink-0 text-emerald-600" />
                        </span>
                        <span class="shrink-0 font-semibold tabular-nums">{{ s.mrr_usd_display }}</span>
                    </li>
                    <li v-if="!recentStartups.length" class="py-6 text-center text-muted-foreground">No startups yet.</li>
                </ul>
            </div>
        </div>
    </div>
</template>

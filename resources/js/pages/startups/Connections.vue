<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { BadgeCheck, Plug, RefreshCw, Trash2 } from '@lucide/vue';

const props = defineProps<{
    startup: { id: number; name: string; slug: string; is_verified: boolean; mrr_usd_display: string };
    providers: { key: string; label: string; key_hint: string }[];
    connections: { id: number; provider: string; provider_label: string; status: string; last_error: string | null; last_synced_at: string | null }[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Providers', href: '#' },
        ],
    },
});

const form = useForm({ provider: 'paystack', secret: '' });

function connect() {
    form.post(`/dashboard/startups/${props.startup.slug}/connections`, {
        preserveScroll: true,
        onSuccess: () => form.reset('secret'),
    });
}

function sync() {
    router.post(`/dashboard/startups/${props.startup.slug}/sync`, {}, { preserveScroll: true });
}

function disconnect(provider: string) {
    if (confirm(`Disconnect ${provider}?`)) {
        router.delete(`/dashboard/startups/${props.startup.slug}/connections/${provider}`, { preserveScroll: true });
    }
}

const activeHint = () => props.providers.find((p) => p.key === form.provider)?.key_hint ?? '';
</script>

<template>
    <Head :title="`${props.startup.name} · Providers`" />

    <div class="mx-auto w-full max-w-2xl p-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="flex items-center gap-2 text-2xl font-bold">
                    {{ props.startup.name }}
                    <BadgeCheck v-if="props.startup.is_verified" class="size-5 text-emerald-600" />
                </h1>
                <p class="text-sm text-muted-foreground">Verified MRR: {{ props.startup.mrr_usd_display }}</p>
            </div>
            <Link href="/dashboard" class="rounded-lg border border-border px-3 py-1.5 text-sm hover:bg-accent">Back</Link>
        </div>

        <!-- Connected providers -->
        <div class="mt-6 space-y-3">
            <div v-for="c in props.connections" :key="c.id" class="flex items-center justify-between rounded-xl border border-border bg-card p-4">
                <div>
                    <p class="flex items-center gap-2 font-semibold"><Plug class="size-4" /> {{ c.provider_label }}</p>
                    <p class="text-xs" :class="c.status === 'active' ? 'text-emerald-600' : c.status === 'error' ? 'text-red-500' : 'text-muted-foreground'">
                        {{ c.status }}<template v-if="c.last_synced_at"> · synced {{ c.last_synced_at }}</template>
                    </p>
                    <p v-if="c.last_error" class="mt-1 text-xs text-red-500">{{ c.last_error }}</p>
                </div>
                <button class="rounded-md p-2 text-red-500 hover:bg-red-500/10" @click="disconnect(c.provider)"><Trash2 class="size-4" /></button>
            </div>

            <button v-if="props.connections.length" class="inline-flex items-center gap-2 rounded-lg border border-border px-4 py-2 text-sm font-medium hover:bg-accent" @click="sync">
                <RefreshCw class="size-4" /> Sync now
            </button>
        </div>

        <!-- Add provider -->
        <div class="mt-8 rounded-xl border border-border bg-card p-5">
            <h2 class="font-semibold">Connect a payment provider</h2>
            <p class="mt-1 text-sm text-muted-foreground">We read active subscriptions only. Use a read-only key where possible.</p>

            <form class="mt-4 space-y-4" @submit.prevent="connect">
                <div>
                    <label class="mb-1 block text-sm font-medium">Provider</label>
                    <select v-model="form.provider" class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm">
                        <option v-for="p in props.providers" :key="p.key" :value="p.key">{{ p.label }}</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Secret key</label>
                    <input v-model="form.secret" type="password" autocomplete="off" class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm font-mono outline-none focus:border-emerald-500" placeholder="sk_live_..." />
                    <p class="mt-1 text-xs text-muted-foreground">{{ activeHint() }}</p>
                    <p v-if="form.errors.secret" class="mt-1 text-xs text-red-500">{{ form.errors.secret }}</p>
                    <p v-if="form.errors.provider" class="mt-1 text-xs text-red-500">{{ form.errors.provider }}</p>
                </div>
                <button type="submit" :disabled="form.processing" class="rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-500 disabled:opacity-60">
                    {{ form.processing ? 'Verifying…' : 'Connect & verify' }}
                </button>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';

interface StartupData {
    slug?: string;
    name?: string;
    tagline?: string | null;
    description?: string | null;
    website?: string | null;
    logo_url?: string | null;
    category?: string;
    country?: string;
    founded_year?: number | null;
    is_public?: boolean;
    is_anonymous?: boolean;
    is_for_sale?: boolean;
    asking_price_usd?: number | null;
    has_connections?: boolean;
}

const props = defineProps<{
    mode: 'create' | 'edit';
    startup?: StartupData;
    categories: Record<string, string>;
    countries: Record<string, string>;
    currencies: string[];
}>();

const form = useForm({
    name: props.startup?.name ?? '',
    tagline: props.startup?.tagline ?? '',
    description: props.startup?.description ?? '',
    website: props.startup?.website ?? '',
    logo_url: props.startup?.logo_url ?? '',
    category: props.startup?.category ?? 'saas',
    country: props.startup?.country ?? 'NG',
    founded_year: props.startup?.founded_year ?? null,
    is_public: props.startup?.is_public ?? true,
    is_anonymous: props.startup?.is_anonymous ?? false,
    is_for_sale: props.startup?.is_for_sale ?? false,
    asking_price_usd: props.startup?.asking_price_usd ?? null,
    manual_mrr_amount: null as number | null,
    manual_mrr_currency: 'NGN',
});

function submit() {
    if (props.mode === 'create') {
        form.post('/dashboard/startups');
    } else {
        form.put(`/dashboard/startups/${props.startup?.slug}`);
    }
}

const inputClass =
    'w-full rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none focus:border-emerald-500';
</script>

<template>
    <form class="space-y-5" @submit.prevent="submit">
        <div>
            <label class="mb-1 block text-sm font-medium">Startup name</label>
            <input v-model="form.name" type="text" :class="inputClass" required />
            <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium">Tagline</label>
            <input v-model="form.tagline" type="text" :class="inputClass" placeholder="One line about what you do" />
            <p v-if="form.errors.tagline" class="mt-1 text-xs text-red-500">{{ form.errors.tagline }}</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium">Category</label>
                <select v-model="form.category" :class="inputClass">
                    <option v-for="(label, key) in categories" :key="key" :value="key">{{ label }}</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Country</label>
                <select v-model="form.country" :class="inputClass">
                    <option v-for="(label, key) in countries" :key="key" :value="key">{{ label }}</option>
                </select>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium">Website</label>
                <input v-model="form.website" type="url" :class="inputClass" placeholder="https://" />
                <p v-if="form.errors.website" class="mt-1 text-xs text-red-500">{{ form.errors.website }}</p>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Founded year</label>
                <input v-model.number="form.founded_year" type="number" :class="inputClass" placeholder="2024" />
            </div>
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium">Logo URL</label>
            <input v-model="form.logo_url" type="url" :class="inputClass" placeholder="https://.../logo.png" />
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium">Description</label>
            <textarea v-model="form.description" rows="4" :class="inputClass" placeholder="Tell the ecosystem what you're building"></textarea>
        </div>

        <!-- Manual MRR (only when no provider connected yet) -->
        <div v-if="!startup?.has_connections" class="rounded-lg border border-dashed border-border p-4">
            <p class="text-sm font-medium">Self-reported MRR <span class="font-normal text-muted-foreground">(optional, until you connect a provider)</span></p>
            <div class="mt-2 flex gap-3">
                <input v-model.number="form.manual_mrr_amount" type="number" step="0.01" :class="inputClass" placeholder="Monthly amount" />
                <select v-model="form.manual_mrr_currency" class="rounded-lg border border-border bg-background px-3 py-2 text-sm">
                    <option v-for="c in currencies" :key="c" :value="c">{{ c }}</option>
                </select>
            </div>
            <p class="mt-2 text-xs text-muted-foreground">This shows as “self-reported” until verified via Paystack, Flutterwave or Stripe.</p>
        </div>

        <!-- Visibility + marketplace -->
        <div class="space-y-3 rounded-lg border border-border p-4">
            <label class="flex items-center gap-2 text-sm">
                <input v-model="form.is_public" type="checkbox" class="size-4 rounded border-border" />
                Show on public leaderboard &amp; directory
            </label>
            <label v-if="form.is_public" class="flex items-center gap-2 text-sm">
                <input v-model="form.is_anonymous" type="checkbox" class="size-4 rounded border-border" />
                Stay anonymous (rank by MRR, hide name &amp; logo)
            </label>
            <label class="flex items-center gap-2 text-sm">
                <input v-model="form.is_for_sale" type="checkbox" class="size-4 rounded border-border" />
                List on the marketplace (for sale)
            </label>
            <div v-if="form.is_for_sale">
                <label class="mb-1 block text-sm font-medium">Asking price (USD)</label>
                <input v-model.number="form.asking_price_usd" type="number" step="1" :class="inputClass" placeholder="50000" />
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" :disabled="form.processing" class="rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-500 disabled:opacity-60">
                {{ mode === 'create' ? 'Create startup' : 'Save changes' }}
            </button>
        </div>
    </form>
</template>

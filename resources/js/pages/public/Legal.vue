<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{ kind: 'terms' | 'privacy' }>();

const title = computed(() => (props.kind === 'terms' ? 'Terms of Service' : 'Privacy Policy'));
</script>

<template>
    <Head :title="title" />
    <div class="mx-auto max-w-3xl px-4 py-16">
        <h1 class="text-3xl font-extrabold tracking-tight">{{ title }}</h1>
        <p class="mt-2 text-sm text-muted-foreground">Last updated {{ new Date().getFullYear() }}</p>

        <div v-if="kind === 'terms'" class="mt-8 space-y-4 text-sm text-muted-foreground">
            <p>By using AfriMRR you agree to connect only payment-provider credentials you are authorized to use, and to display revenue figures that represent your own business.</p>
            <p>We provide verification on a best-effort basis using third-party provider APIs. Figures are informational and not financial advice or a guarantee of any kind.</p>
            <p>Marketplace listings are between buyers and sellers directly; AfriMRR is not a party to any transaction and makes no warranty as to any sale.</p>
            <p>We may suspend accounts that attempt to misrepresent revenue, abuse the API, or violate provider terms.</p>
        </div>

        <div v-else class="mt-8 space-y-4 text-sm text-muted-foreground">
            <p>We store the payment-provider API keys you choose to connect, encrypted at rest. These keys are used solely to read subscription data for computing your MRR.</p>
            <p>We store the resulting revenue metrics and snapshots to power your profile, the leaderboard and growth tracking. You control whether a startup is public.</p>
            <p>We never sell your data. You can disconnect a provider or delete a startup at any time, which removes the associated credentials and metrics.</p>
            <p>For any data request, contact us from the email on your account.</p>
        </div>
    </div>
</template>

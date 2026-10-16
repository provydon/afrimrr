<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

const faqs = [
    {
        q: 'How is my MRR verified?',
        a: 'You connect a read-only API key from Paystack, Flutterwave or Stripe. We read your active subscriptions directly from the provider, normalize every plan interval to a monthly figure, and convert to USD. Nothing is self-typed, so the number is tamper-proof.',
    },
    {
        q: 'Which payment providers are supported?',
        a: 'Paystack, Flutterwave and Stripe — the three that expose subscription objects with amounts and intervals. Monnify is not supported because it has no native subscription product (recurring billing there is merchant-managed), so there is nothing to verify against.',
    },
    {
        q: 'Is my API key safe?',
        a: 'Keys are encrypted at rest and only ever used to read subscription data. We recommend using a restricted/read-only key where your provider supports it.',
    },
    {
        q: 'How often does my MRR update?',
        a: 'Automatically every hour, plus any time you hit “Sync now” in your dashboard.',
    },
    {
        q: 'Can I hide my exact revenue?',
        a: 'Yes — Pro lets you stay verified while hiding the exact amount, showing only a verified checkmark and growth. You can also keep a startup private entirely.',
    },
    {
        q: 'What if I bill in Naira, Cedis or Shillings?',
        a: 'We keep your local currency for display and also normalize to USD so you can be ranked fairly against startups billing in other currencies.',
    },
];
</script>

<template>
    <Head title="FAQ" />
    <div class="mx-auto max-w-3xl px-4 py-16">
        <h1 class="text-3xl font-extrabold tracking-tight">Frequently asked questions</h1>
        <div class="mt-8 divide-y divide-border rounded-xl border border-border bg-card">
            <details v-for="f in faqs" :key="f.q" class="group p-5">
                <summary class="cursor-pointer list-none font-semibold [&::-webkit-details-marker]:hidden">
                    <span class="flex items-center justify-between">
                        {{ f.q }}
                        <span class="text-muted-foreground transition group-open:rotate-45">+</span>
                    </span>
                </summary>
                <p class="mt-3 text-sm text-muted-foreground">{{ f.a }}</p>
            </details>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import StartupForm from '@/components/StartupForm.vue';

defineProps<{
    categories: Record<string, string>;
    countries: Record<string, string>;
    currencies: string[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'New startup', href: '/dashboard/startups/create' },
        ],
    },
});
</script>

<template>
    <Head title="New startup" />
    <div class="mx-auto w-full max-w-2xl p-4">
        <h1 class="text-2xl font-bold">Add your startup</h1>
        <p class="mt-1 text-sm text-muted-foreground">You'll connect a payment provider next to verify your MRR.</p>
        <div class="mt-6">
            <StartupForm mode="create" :categories="categories" :countries="countries" :currencies="currencies" />
        </div>
    </div>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import StartupForm from '@/components/StartupForm.vue';

const props = defineProps<{
    startup: {
        slug: string;
        name: string;
        has_connections: boolean;
        [key: string]: unknown;
    };
    categories: Record<string, string>;
    countries: Record<string, string>;
    currencies: string[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Edit', href: '#' },
        ],
    },
});
</script>

<template>
    <Head :title="`Edit ${props.startup.name}`" />
    <div class="mx-auto w-full max-w-2xl p-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Edit {{ props.startup.name }}</h1>
            <Link :href="`/dashboard/startups/${props.startup.slug}/connections`" class="rounded-lg border border-border px-3 py-1.5 text-sm hover:bg-accent">Providers</Link>
        </div>
        <div class="mt-6">
            <StartupForm
                mode="edit"
                :startup="props.startup as never"
                :categories="categories"
                :countries="countries"
                :currencies="currencies"
            />
        </div>
    </div>
</template>

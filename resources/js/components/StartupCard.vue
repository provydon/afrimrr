<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { BadgeCheck, EyeOff, TrendingDown, TrendingUp } from '@lucide/vue';
import type { StartupCardData } from '@/types/mrr';

const props = defineProps<{ startup: StartupCardData; showPrice?: boolean }>();

// Anonymous startups are shown but not linkable (no identity to reveal).
const root = computed(() => (props.startup.is_anonymous ? 'div' : Link));
const linkProps = computed(() => (props.startup.is_anonymous ? {} : { href: `/startups/${props.startup.slug}` }));
</script>

<template>
    <component
        :is="root"
        v-bind="linkProps"
        class="group flex flex-col rounded-xl border border-border bg-card p-5 transition"
        :class="startup.is_anonymous ? 'cursor-default' : 'hover:border-emerald-500/50 hover:shadow-lg'"
    >
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-3">
                <span
                    v-if="startup.is_anonymous"
                    class="flex size-10 items-center justify-center rounded-lg bg-muted text-muted-foreground"
                >
                    <EyeOff class="size-4" />
                </span>
                <img
                    v-else-if="startup.logo_url"
                    :src="startup.logo_url"
                    :alt="startup.name"
                    class="size-10 rounded-lg object-cover"
                />
                <span
                    v-else
                    class="flex size-10 items-center justify-center rounded-lg bg-emerald-600/10 text-sm font-bold text-emerald-600"
                >
                    {{ startup.name.charAt(0) }}
                </span>
                <div>
                    <div class="flex items-center gap-1 font-semibold leading-tight">
                        <span :class="startup.is_anonymous ? 'select-none blur-[4px]' : ''">{{ startup.name }}</span>
                        <BadgeCheck v-if="startup.is_verified" class="size-4 text-emerald-600" />
                    </div>
                    <p class="text-xs text-muted-foreground">{{ startup.category_label }} · {{ startup.country_label }}</p>
                </div>
            </div>
            <span
                v-if="startup.is_for_sale"
                class="rounded-full bg-amber-500/15 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-amber-600"
            >For sale</span>
        </div>

        <p v-if="startup.is_anonymous" class="mt-3 text-sm text-muted-foreground">Identity hidden by the founder.</p>
        <p v-else-if="startup.tagline" class="mt-3 line-clamp-2 text-sm text-muted-foreground">{{ startup.tagline }}</p>

        <div class="mt-4 flex items-end justify-between border-t border-border pt-4">
            <div>
                <p class="text-[11px] uppercase tracking-wide text-muted-foreground">MRR</p>
                <p class="text-xl font-bold">{{ startup.mrr_usd_display }}</p>
                <p v-if="startup.display_currency !== 'USD'" class="text-xs text-muted-foreground">
                    ≈ {{ startup.mrr_local_display }}
                </p>
            </div>
            <div class="text-right">
                <span
                    v-if="startup.mom_growth !== null"
                    class="inline-flex items-center gap-1 text-sm font-semibold"
                    :class="startup.mom_growth >= 0 ? 'text-emerald-600' : 'text-red-500'"
                >
                    <TrendingUp v-if="startup.mom_growth >= 0" class="size-4" />
                    <TrendingDown v-else class="size-4" />
                    {{ startup.mom_growth >= 0 ? '+' : '' }}{{ startup.mom_growth }}%
                </span>
                <template v-if="showPrice && startup.asking_price_display">
                    <p class="mt-1 text-sm font-semibold">{{ startup.asking_price_display }}</p>
                    <p v-if="startup.revenue_multiple" class="text-xs text-muted-foreground">{{ startup.revenue_multiple }}× ARR</p>
                </template>
            </div>
        </div>
    </component>
</template>

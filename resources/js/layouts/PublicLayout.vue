<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { BadgeCheck, Moon, Sun, Menu } from '@lucide/vue';
import { Toaster } from '@/components/ui/sonner';
import { useAppearance } from '@/composables/useAppearance';
import { ref } from 'vue';

const page = usePage();
const user = computed(() => (page.props.auth as { user: { username: string } | null })?.user ?? null);

const { resolvedAppearance, updateAppearance } = useAppearance();
function toggleTheme() {
    updateAppearance(resolvedAppearance.value === 'dark' ? 'light' : 'dark');
}

const nav = [
    { label: 'Leaderboard', href: '/leaderboard' },
    { label: 'Startups', href: '/startups' },
    { label: 'Marketplace', href: '/marketplace' },
    { label: 'Get a badge', href: '/badge-store' },
];

const mobileOpen = ref(false);
const current = computed(() => page.url);
const isActive = (href: string) => current.value === href || current.value.startsWith(href + '?');
</script>

<template>
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <header class="sticky top-0 z-40 border-b border-border bg-background/80 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4">
                <div class="flex items-center gap-6">
                    <Link href="/" class="flex items-center gap-2 font-bold">
                        <span class="flex size-8 items-center justify-center rounded-lg bg-emerald-600 text-white">
                            <BadgeCheck class="size-5" />
                        </span>
                        <span class="text-lg tracking-tight">AfricanMRR</span>
                    </Link>
                    <nav class="hidden items-center gap-1 md:flex">
                        <Link
                            v-for="item in nav"
                            :key="item.href"
                            :href="item.href"
                            class="rounded-md px-3 py-1.5 text-sm font-medium text-muted-foreground transition hover:bg-accent hover:text-foreground"
                            :class="{ 'bg-accent text-foreground': isActive(item.href) }"
                        >
                            {{ item.label }}
                        </Link>
                    </nav>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        aria-label="Toggle theme"
                        class="flex size-9 items-center justify-center rounded-md text-muted-foreground transition hover:bg-accent hover:text-foreground"
                        @click="toggleTheme"
                    >
                        <Moon v-if="resolvedAppearance === 'light'" class="size-4" />
                        <Sun v-else class="size-4" />
                    </button>

                    <template v-if="user">
                        <Link href="/dashboard" class="rounded-md bg-primary px-4 py-1.5 text-sm font-semibold text-primary-foreground transition hover:bg-primary/90">
                            Dashboard
                        </Link>
                    </template>
                    <template v-else>
                        <Link href="/login" class="hidden rounded-md px-3 py-1.5 text-sm font-medium text-muted-foreground transition hover:text-foreground sm:block">
                            Log in
                        </Link>
                        <Link href="/register" class="rounded-md bg-emerald-600 px-4 py-1.5 text-sm font-semibold text-white transition hover:bg-emerald-500">
                            Get listed
                        </Link>
                    </template>

                    <button
                        type="button"
                        class="flex size-9 items-center justify-center rounded-md text-muted-foreground hover:bg-accent md:hidden"
                        @click="mobileOpen = !mobileOpen"
                    >
                        <Menu class="size-5" />
                    </button>
                </div>
            </div>

            <nav v-if="mobileOpen" class="border-t border-border px-4 py-2 md:hidden">
                <Link
                    v-for="item in nav"
                    :key="item.href"
                    :href="item.href"
                    class="block rounded-md px-3 py-2 text-sm font-medium text-muted-foreground hover:bg-accent hover:text-foreground"
                    @click="mobileOpen = false"
                >
                    {{ item.label }}
                </Link>
            </nav>
        </header>

        <main class="flex-1">
            <slot />
        </main>

        <footer class="border-t border-border bg-muted/30">
            <div class="mx-auto max-w-6xl px-4 py-10">
                <div class="flex flex-col gap-6 md:flex-row md:items-start md:justify-between">
                    <div class="max-w-xs">
                        <div class="flex items-center gap-2 font-bold">
                            <span class="flex size-7 items-center justify-center rounded-lg bg-emerald-600 text-white">
                                <BadgeCheck class="size-4" />
                            </span>
                            AfricanMRR
                        </div>
                        <p class="mt-3 text-sm text-muted-foreground">
                            Verified revenue for African startups. MRR pulled straight from Paystack,
                            Flutterwave &amp; Stripe — no screenshots, no vanity metrics.
                        </p>
                    </div>
                    <div class="grid grid-cols-2 gap-8 text-sm sm:grid-cols-3">
                        <div class="space-y-2">
                            <p class="font-semibold">Explore</p>
                            <Link href="/leaderboard" class="block text-muted-foreground hover:text-foreground">Leaderboard</Link>
                            <Link href="/startups" class="block text-muted-foreground hover:text-foreground">All startups</Link>
                            <Link href="/marketplace" class="block text-muted-foreground hover:text-foreground">Marketplace</Link>
                        </div>
                        <div class="space-y-2">
                            <p class="font-semibold">Product</p>
                            <Link href="/badge-store" class="block text-muted-foreground hover:text-foreground">Badges</Link>
                            <Link href="/developers" class="block text-muted-foreground hover:text-foreground">API &amp; MCP</Link>
                            <Link href="/faq" class="block text-muted-foreground hover:text-foreground">FAQ</Link>
                        </div>
                        <div class="space-y-2">
                            <p class="font-semibold">Company</p>
                            <Link href="/about" class="block text-muted-foreground hover:text-foreground">About</Link>
                            <Link href="/terms" class="block text-muted-foreground hover:text-foreground">Terms</Link>
                            <Link href="/privacy" class="block text-muted-foreground hover:text-foreground">Privacy</Link>
                        </div>
                    </div>
                </div>
                <p class="mt-8 text-xs text-muted-foreground">
                    © {{ new Date().getFullYear() }} AfricanMRR. Revenue figures are verified via payment-provider APIs.
                    <span class="block sm:inline sm:before:content-['_·_']">AfricanMRR is a product of <a href="https://stevenmalog.com" target="_blank" rel="noopener" class="underline hover:text-foreground">Steven Malog, Inc.</a></span>
                </p>
            </div>
        </footer>

        <Toaster rich-colors position="top-center" />
    </div>
</template>

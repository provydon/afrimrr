<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { X } from '@lucide/vue';

const STORAGE_KEY = 'amrr_news_v1';

const visible = ref(false);
const done = ref(false);

const form = useForm({ email: '', source: 'popup' });

function dismissedBefore(): boolean {
    try {
        return localStorage.getItem(STORAGE_KEY) !== null;
    } catch {
        return false;
    }
}

function remember(value: string) {
    try {
        localStorage.setItem(STORAGE_KEY, value);
    } catch {
        // ignore (private mode / blocked storage)
    }
}

function close() {
    visible.value = false;
    remember('dismissed');
}

function submit() {
    form.post('/subscribe', {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            done.value = true;
            remember('subscribed');
            form.reset('email');
            setTimeout(() => (visible.value = false), 2600);
        },
    });
}

onMounted(() => {
    if (dismissedBefore()) return;
    // Give the visitor a moment to read before the invite slides in.
    setTimeout(() => (visible.value = true), 7000);
});
</script>

<template>
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="translate-y-4 opacity-0"
        enter-to-class="translate-y-0 opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="translate-y-0 opacity-100"
        leave-to-class="translate-y-4 opacity-0"
    >
        <div
            v-if="visible"
            class="fixed inset-x-4 bottom-4 z-50 sm:inset-x-auto sm:right-6 sm:bottom-6 sm:w-[380px]"
        >
            <div class="relative overflow-hidden rounded-2xl border border-emerald-500/30 bg-card p-5 shadow-2xl ring-1 ring-black/5">
                <div class="pointer-events-none absolute -right-10 -top-10 size-28 rounded-full bg-emerald-500/20 blur-2xl"></div>

                <button
                    type="button"
                    aria-label="Close"
                    class="absolute right-3 top-3 text-muted-foreground transition hover:text-foreground"
                    @click="close"
                >
                    <X class="size-4" />
                </button>

                <div v-if="!done" class="relative">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">🦄</span>
                        <h3 class="text-lg font-extrabold">Spot the next big thing</h3>
                    </div>
                    <p class="mt-2 text-sm text-muted-foreground">
                        Get the weekly data report: top 5 fastest-growing African startups, best 3 for sale,
                        and 1 underdog spotlight.
                    </p>

                    <form class="mt-4 space-y-2" @submit.prevent="submit">
                        <input
                            v-model="form.email"
                            type="email"
                            required
                            placeholder="you@example.com"
                            class="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                        />
                        <p v-if="form.errors.email" class="text-xs text-red-500">{{ form.errors.email }}</p>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full rounded-lg bg-gradient-to-r from-emerald-600 to-teal-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:opacity-95 disabled:opacity-60"
                        >
                            {{ form.processing ? 'Sending…' : 'Send me the data →' }}
                        </button>
                    </form>

                    <p class="mt-3 text-xs text-muted-foreground">Join founders tracking African SaaS revenue. No spam.</p>
                </div>

                <div v-else class="relative py-2 text-center">
                    <p class="text-3xl">🎉</p>
                    <h3 class="mt-1 text-lg font-extrabold">You're in!</h3>
                    <p class="mt-1 text-sm text-muted-foreground">The next report is on its way to your inbox.</p>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';

defineProps({
    appName: {
        type: String,
        required: true,
    },
    documentation: {
        type: String,
        required: true,
    },
    starterChecklist: {
        type: Array,
        required: true,
    },
    tagline: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head :title="appName" />

    <main class="relative isolate min-h-screen overflow-hidden bg-[radial-gradient(circle_at_top,_rgba(251,191,36,0.18),_transparent_32%),linear-gradient(180deg,_#0c0a09_0%,_#1c1917_100%)]">
        <div class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.04)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.04)_1px,transparent_1px)] bg-[size:3rem_3rem] opacity-30" />

        <div class="relative mx-auto flex min-h-screen max-w-6xl flex-col justify-center px-6 py-16 lg:px-10">
            <div class="max-w-3xl">
                <p class="mb-6 inline-flex rounded-full border border-amber-300/20 bg-amber-300/10 px-4 py-1 text-sm font-medium tracking-[0.2em] text-amber-200 uppercase">
                    Laravel + Inertia + Vue
                </p>

                <h1 class="text-4xl font-semibold tracking-tight text-white sm:text-6xl">
                    {{ appName }}
                </h1>

                <p class="mt-6 max-w-2xl text-lg leading-8 text-stone-300">
                    {{ tagline }}
                </p>
            </div>

            <div class="mt-12 grid gap-6 lg:grid-cols-[minmax(0,1.4fr)_minmax(22rem,1fr)]">
                <section class="rounded-3xl border border-white/10 bg-white/[0.06] p-8 shadow-2xl shadow-black/20 backdrop-blur">
                    <h2 class="text-sm font-semibold tracking-[0.2em] text-stone-400 uppercase">
                        First steps
                    </h2>

                    <ul class="mt-6 space-y-4">
                        <li
                            v-for="item in starterChecklist"
                            :key="item"
                            class="rounded-2xl border border-white/[0.08] bg-black/20 px-4 py-4 text-stone-200"
                        >
                            {{ item }}
                        </li>
                    </ul>
                </section>

                <aside class="rounded-3xl border border-amber-200/[0.15] bg-amber-200/[0.08] p-8 backdrop-blur">
                    <h2 class="text-sm font-semibold tracking-[0.2em] text-amber-100 uppercase">
                        Reference
                    </h2>

                    <p class="mt-4 text-sm leading-7 text-stone-200">
                        Your backend routes can now return Vue pages through Inertia responses instead of Blade views.
                    </p>

                    <a
                        :href="documentation"
                        target="_blank"
                        rel="noreferrer"
                        class="mt-6 inline-flex items-center rounded-full bg-amber-300 px-5 py-3 text-sm font-semibold text-stone-950 transition hover:bg-amber-200"
                    >
                        Open Inertia Docs
                    </a>
                </aside>
            </div>
        </div>
    </main>
</template>

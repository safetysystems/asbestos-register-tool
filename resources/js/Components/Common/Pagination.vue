<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    paginator: {
        type: Object,
        required: true,
    },
    noun: {
        type: String,
        default: "results",
    },
});
</script>

<template>
    <div
        class="p-6 bg-surface-container-low/30 border-t border-surface-container flex flex-col sm:flex-row items-center justify-between gap-4"
    >
        <div class="flex items-center gap-4">
            <p class="app-text text-on-surface-variant">
                Showing
                <span class="font-bold text-on-surface"
                    >{{ paginator.from ?? 0 }}-{{
                        paginator.to ?? 0
                    }}</span
                >
                of
                <span class="font-bold text-on-surface">{{
                    paginator.total
                }}</span>
                {{ noun }}
            </p>
        </div>

        <div class="flex items-center gap-1">
            <template
                v-for="(link, index) in paginator.links"
                :key="index"
            >
                <!-- Previous button -->
                <template v-if="index === 0">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="p-2 rounded-lg hover:bg-surface-container-high text-on-surface-variant cursor-pointer group"
                        title="Previous Page"
                    >
                        <span
                            class="material-symbols-outlined transition-transform group-active:-translate-x-1"
                            data-icon="chevron_left"
                            >chevron_left</span
                        >
                    </Link>
                    <span
                        v-else
                        class="p-2 rounded-lg text-on-surface-variant/30"
                    >
                        <span
                            class="material-symbols-outlined"
                            data-icon="chevron_left"
                            >chevron_left</span
                        >
                    </span>
                </template>

                <!-- Next button -->
                <template
                    v-else-if="index === paginator.links.length - 1"
                >
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="p-2 rounded-lg hover:bg-surface-container-high text-on-surface-variant cursor-pointer group"
                        title="Next Page"
                    >
                        <span
                            class="material-symbols-outlined transition-transform group-active:translate-x-1"
                            data-icon="chevron_right"
                            >chevron_right</span
                        >
                    </Link>
                    <span
                        v-else
                        class="p-2 rounded-lg text-on-surface-variant/30"
                    >
                        <span
                            class="material-symbols-outlined"
                            data-icon="chevron_right"
                            >chevron_right</span
                        >
                    </span>
                </template>

                <!-- Page numbers -->
                <template v-else>
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="w-9 h-9 flex items-center justify-center rounded-lg app-text font-bold transition-colors cursor-pointer"
                        :class="
                            link.active
                                ? 'bg-primary-container text-on-primary-container font-black shadow-sm'
                                : 'hover:bg-surface-container-high text-on-surface-variant'
                        "
                    >
                        {{ link.label }}
                    </Link>
                    <span
                        v-else
                        class="w-9 h-9 flex items-center justify-center rounded-lg app-text font-bold text-on-surface-variant/30"
                    >
                        {{ link.label }}
                    </span>
                </template>
            </template>
        </div>
    </div>
</template>

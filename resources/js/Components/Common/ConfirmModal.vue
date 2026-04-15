<script setup>
import { ref } from "vue";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: "Confirm Delete",
    },
    message: {
        type: String,
        default: "Are you sure? This action cannot be undone.",
    },
    confirmLabel: {
        type: String,
        default: "Delete",
    },
    cancelLabel: {
        type: String,
        default: "Cancel",
    },
    processing: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["confirm", "cancel"]);
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="show"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
            >
                <!-- Backdrop -->
                <div
                    class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                    @click="emit('cancel')"
                />

                <!-- Modal -->
                <Transition
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="opacity-0 scale-95"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition duration-150 ease-in"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-95"
                >
                    <div
                        v-if="show"
                        class="relative bg-surface-container-lowest rounded-xl shadow-2xl border border-outline-variant/10 w-full max-w-md overflow-hidden"
                    >
                        <div class="p-6">
                            <div class="flex items-start gap-4">
                                <div
                                    class="flex-shrink-0 w-10 h-10 rounded-full bg-error/10 flex items-center justify-center"
                                >
                                    <span
                                        class="material-symbols-outlined text-error text-xl"
                                        data-icon="warning"
                                        >warning</span
                                    >
                                </div>
                                <div>
                                    <h3
                                        class="text-base font-bold text-on-surface"
                                    >
                                        {{ title }}
                                    </h3>
                                    <p
                                        class="mt-1 text-sm text-on-surface-variant/70"
                                    >
                                        {{ message }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div
                            class="px-6 py-4 bg-surface-container-low/30 border-t border-surface-container flex items-center justify-end gap-3"
                        >
                            <button
                                class="cursor-pointer px-4 py-2 app-text font-bold uppercase tracking-[0.15em] bg-on-surface-variant/20 text-on-surface-variant rounded-lg hover:bg-surface-container-high transition-all"
                                :disabled="processing"
                                @click="emit('cancel')"
                            >
                                {{ cancelLabel }}
                            </button>
                            <button
                                class="cursor-pointer px-5 py-2 bg-error text-white app-text font-bold uppercase tracking-[0.15em] rounded-lg hover:bg-red-700 transition-all shadow-sm disabled:opacity-50"
                                :disabled="processing"
                                @click="emit('confirm')"
                            >
                                {{ processing ? "Deleting..." : confirmLabel }}
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close-sidebar']);

const currentPath = computed(() => usePage().url.split('?')[0]);

function isActive(path) {
    return currentPath.value === path || currentPath.value.startsWith(`${path}/`);
}

function itemClasses(path, activeClasses, inactiveClasses) {
    return isActive(path) ? activeClasses : inactiveClasses;
}

const sidebarClasses = computed(() =>
    props.isOpen
        ? 'translate-x-0'
        : '-translate-x-full lg:translate-x-0',
);
</script>

<template>
    <aside
        id="sidebar"
        class="h-screen w-66 fixed left-0 top-0 bg-gray-100 dark:bg-gray-950 flex flex-col p-4 space-y-2 z-[60] transition-transform duration-300"
        :class="sidebarClasses"
    >

        <div class="flex items-center justify-between px-4 py-6 mb-4">
            <div class="flex items-center min-w-0 flex-1">
            <div class="w-10 h-10 rounded-lg bg-primary-container flex items-center justify-center mr-3 shadow-sm">
                <span class="material-symbols-outlined text-on-primary-container" data-icon="token">token</span>
            </div>
            <div>
                <h2 class="text-lg font-black text-gray-900 dark:text-white leading-tight">Curated Canvas</h2>
                <p class="app-text font-medium tracking-widest uppercase text-on-surface-variant/60">Enterprise Admin</p>
            </div>
            </div>
            <button
                type="button"
                class="lg:hidden rounded-lg text-gray-600 hover:bg-gray-200/60 dark:text-gray-300 dark:hover:bg-gray-800/60"
                @click="emit('close-sidebar')"
            >
                <span class="material-symbols-outlined" data-icon="arrow_back">arrow_back</span>
            </button>
        </div>

        <nav class="flex-grow space-y-1">
            <a
                class="flex items-center px-4 py-3 rounded-lg transition-transform scale-95 active:scale-100"
                :class="itemClasses('/dashboard', 'bg-white dark:bg-gray-900 text-orange-600 dark:text-orange-400 shadow-sm font-bold', 'text-gray-600 dark:text-gray-400 hover:bg-gray-200/50 dark:hover:bg-gray-800/50')"
                href="/dashboard"
            >
                <span class="material-symbols-outlined mr-3" data-icon="dashboard">dashboard</span>
                <span class="text-sm font-medium tracking-wide uppercase">Dashboard</span>
            </a>

            <a
                class="flex items-center px-4 py-3 rounded-lg transition-transform scale-95 active:scale-100"
                :class="itemClasses('/customers', 'bg-white dark:bg-gray-900 text-orange-600 dark:text-orange-400 shadow-sm font-bold', 'text-gray-600 dark:text-gray-400 hover:bg-gray-200/50 dark:hover:bg-gray-800/50')"
                href="/customers"
            >
                <span class="material-symbols-outlined mr-3" data-icon="group">group</span>
                <span class="text-sm font-medium tracking-wide uppercase">Customer</span>
            </a>

            <a
                class="flex items-center px-4 py-3 rounded-lg transition-transform scale-95 active:scale-100"
                :class="itemClasses('/asbestos-audits', 'bg-white dark:bg-gray-900 text-orange-600 dark:text-orange-400 shadow-sm font-bold', 'text-gray-600 dark:text-gray-400 hover:bg-gray-200/50 dark:hover:bg-gray-800/50')"
                href="/asbestos-audits"
            >
                <span class="material-symbols-outlined mr-3" data-icon="clinical_notes">clinical_notes</span>
                <span class="text-sm font-medium tracking-wide uppercase">Asbestos Audit</span>
            </a>
            <a
                class="flex items-center px-4 py-3 rounded-lg transition-transform scale-95 active:scale-100"
                :class="itemClasses('/asbestos-inspector', 'bg-white dark:bg-gray-900 text-orange-600 dark:text-orange-400 shadow-sm font-bold', 'text-gray-600 dark:text-gray-400 hover:bg-gray-200/50 dark:hover:bg-gray-800/50')"
                href="/asbestos-inspector"
            >
                <span class="material-symbols-outlined mr-3" data-icon="groups_2">groups_2</span>
                <span class="text-sm font-medium tracking-wide uppercase">Asbestos Inspector</span>
            </a>
        </nav>

        <div class="mt-auto pt-4 space-y-1">
            <a class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-400 hover:bg-gray-200/50 dark:hover:bg-gray-800/50 rounded-lg" href="#">
                <span class="material-symbols-outlined mr-3" data-icon="help">help</span>
                <span class="text-sm font-medium tracking-wide uppercase">Help Center</span>
            </a>
            <a class="flex items-center px-4 py-3 text-gray-600 dark:text-gray-400 hover:bg-gray-200/50 dark:hover:bg-gray-800/50 rounded-lg" href="#">
                <span class="material-symbols-outlined mr-3" data-icon="logout">logout</span>
                <span class="text-sm font-medium tracking-wide uppercase">Logout</span>
            </a>
        </div>
    </aside>
</template>
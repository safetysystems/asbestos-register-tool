<script setup>
import { ref } from 'vue';
import Sidebar from '@/Components/Layout/Sidebar.vue';
import Header from '@/Components/Layout/Header.vue';

const isSidebarOpen = ref(false);

function toggleSidebar() {
    isSidebarOpen.value = !isSidebarOpen.value;
}

function closeSidebar() {
    isSidebarOpen.value = false;
}
</script>

<template>
    <div class="min-h-screen bg-background text-on-surface antialiased">
        <Sidebar :is-open="isSidebarOpen" @close-sidebar="closeSidebar" />

        <main class="ml-0 lg:ml-64 min-h-screen flex flex-col">
            <Header @toggle-sidebar="toggleSidebar" />

            <slot />
        </main>
    </div>
</template>
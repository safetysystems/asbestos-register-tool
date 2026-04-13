<script setup>
import { Head } from '@inertiajs/vue3';
import AppShell from '@/Components/Layout/AppShell.vue';
import Footer from '@/Components/Layout/Footer.vue';
</script>

<template>
    <Head title="Customer" />
    <AppShell>
        <div class="flex-1">
            <h1 class="text-2xl font-bold">Customer Page</h1>
            <p>Welcome to the customer page!</p>
        </div>
        <Footer />
    </AppShell>
</template>
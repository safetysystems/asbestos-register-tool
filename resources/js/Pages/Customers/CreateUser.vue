<script setup>
import { Head } from '@inertiajs/vue3';
import AppShell from '@/Components/Layout/AppShell.vue';
import Footer from '@/Components/Layout/Footer.vue';
</script>

<template>
    <Head title="Asbestos Audits | Create" />
    <AppShell>
        <div class="flex-1 pt-24 px-4 md:px-8 pb-12">
            <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/10 flex flex-col w-full">
                <div class="px-4 md:px-8 py-5 border-b border-surface-container flex justify-between items-center bg-[#fdfdfd] rounded-t-xl">
                    <h3 class="text-lg font-bold text-on-surface tracking-tight uppercase text-[15px]">Create New Customer</h3>
                    <button class="px-6 py-2 bg-primary-container text-on-primary-container text-[11px] font-semibold rounded-lg uppercase hover:bg-orange-500 transition-all shadow-sm cursor-pointer">
                        Save
                    </button>
                </div>

                <div class="p-4 md:p-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols- gap-x-8 gap-y-6">
                        <div class="space-y-2">
                            <label class="text-[11px] font-bold tracking-widest text-on-surface-variant uppercase ml-1">Full Name</label>
                            <input class="w-full bg-surface-container-highest border-none rounded-lg text-sm px-4 py-3 focus:ring-2 focus:ring-primary-container outline-none transition-all" placeholder="e.g. Alex Harrison" type="text" />
                            </div>

                            <div class="space-y-2">
                            <label class="text-[11px] font-bold tracking-widest text-on-surface-variant uppercase ml-1">Email Address</label>
                            <input class="w-full bg-surface-container-highest border-none rounded-lg text-sm px-4 py-3 focus:ring-2 focus:ring-primary-container outline-none transition-all" placeholder="alex@ambergrid.com" type="email" />
                            </div>

                            <div class="space-y-2 md:col-span-2">
                            <label class="text-[11px] font-bold tracking-widest text-on-surface-variant uppercase ml-1">Role Selection</label>
                            <div class="relative">
                                <select class="w-full bg-surface-container-highest border-none rounded-lg text-sm px-4 py-3 focus:ring-2 focus:ring-primary-container outline-none appearance-none transition-all cursor-pointer">
                                <option>Viewer</option>
                                <option>Editor</option>
                                <option>Admin</option>
                                </select>
                                <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant/50" data-icon="expand_more">expand_more</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Footer />
    </AppShell>
</template>

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
                    <h3 class="text-lg font-semibold text-on-surface tracking-tight uppercase text-[15px]">Site details</h3>
                    <button class="px-6 py-2 bg-primary-container text-on-primary-container text-[11px] font-semibold rounded-lg uppercase hover:bg-orange-500 transition-all shadow-sm cursor-pointer">
                        Save
                    </button>
                </div>

                <div class="p-4 md:p-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-x-8 gap-y-6">
                        <div class="space-y-1">
                            <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">Audit Date</label>
                            <input type="date" value="2026-12-04" class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all">
                        </div>

                        <div class="space-y-1">
                            <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">Audit Hours</label>
                            <input type="text" value="08:30 - 15:45" class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all">
                        </div>

                        <div class="space-y-1">
                            <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">Job Type</label>
                            <select class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all appearance-none cursor-pointer">
                                <option>Div 5 update</option>
                                <option>Initial Survey</option>
                                <option>Re-inspection</option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">Labelling</label>
                            <select class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all appearance-none">
                                <option>Select labelling status</option>
                                <option>Completed</option>
                                <option>Partial</option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">QR Number</label>
                            <input type="text" value="QR-ABT-240304" class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all">
                        </div>

                        <div class="space-y-1">
                            <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">Installed / Pre-existing</label>
                            <select class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all appearance-none">
                                <option>Select installation status</option>
                                <option>Confirmed</option>
                                <option>Not Found</option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">Lead Identified</label>
                            <select class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all appearance-none">
                                <option>Select lead status</option>
                                <option>Yes</option>
                                <option>No</option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">Samples Taken</label>
                            <input type="number" value="0" class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all">
                        </div>

                        <div class="space-y-1">
                            <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">PCB's Identified</label>
                            <select class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all appearance-none">
                                <option>Select PCB status</option>
                                <option>Yes</option>
                                <option>No</option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">SMF Identified</label>
                            <select class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all appearance-none">
                                <option>Select SMF status</option>
                                <option>Yes</option>
                                <option>No</option>
                            </select>
                        </div>

                        <div class="md:col-span-2 xl:col-span-3 space-y-1">
                            <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">SMF Notes</label>
                            <input type="text" placeholder="Optional note about SMF location or condition." class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all">
                        </div>
                    </div>
                </div>
            </div>

            <Footer />
        </div>
    </AppShell>
</template>

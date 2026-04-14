<script setup>
import { Head } from '@inertiajs/vue3';
import { onBeforeUnmount, ref, watch } from 'vue';
import AppShell from '@/Components/Layout/AppShell.vue';
import Footer from '@/Components/Layout/Footer.vue';

const isSampleModalOpen = ref(false);

function openSampleModal() {
    isSampleModalOpen.value = true;
    }

function closeSampleModal() {
    isSampleModalOpen.value = false;
}

watch(isSampleModalOpen, (isOpen) => {
    document.body.style.overflow = isOpen ? 'hidden' : '';
});

onBeforeUnmount(() => {
    document.body.style.overflow = '';
});
</script>

<template>
    <Head title="Asbestos Audits | title" />
    <AppShell>
        <div class="flex-1">
                <div class="pt-24 px-4 md:px-8 pb-12 transition-all duration-300">

                    <!-- Top Section: Site Details & Info Cards -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
                        
                        <!-- Site Details Form -->
                        <div class="lg:col-span-2 bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/10 flex flex-col h-full">
                        <div class="px-4 md:px-8 py-5 border-b border-surface-container flex justify-between items-center bg-[#fdfdfd] rounded-t-xl">
                            <h3 class="text-lg font-bold text-on-surface tracking-tight uppercase text-[15px]">Site details</h3>
                            <button class="px-6 py-2 bg-primary-container text-on-primary-container text-[11px] font-semibold rounded-lg uppercase hover:bg-orange-500 transition-all shadow-sm">Edit</button>
                        </div>
                        <div class="p-4 md:p-10 flex-grow">

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-6">
                            <!-- Audit Date -->
                            <div class="space-y-1">
                                <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">Audit Date</label>
                                <input type="date" value="2026-12-04" class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all">
                            </div>
                            <!-- Audit Hours -->
                            <div class="space-y-1">
                                <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">Audit Hours</label>
                                <input type="text" value="08:30 - 15:45" class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all">
                            </div>
                            <!-- Job Type -->
                            <div class="space-y-1">
                                <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">Job Type</label>
                                <select class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all appearance-none cursor-pointer">
                                <option>Div 5 update</option>
                                <option>Initial Survey</option>
                                <option>Re-inspection</option>
                                </select>
                            </div>
                            <!-- Labelling -->
                            <div class="space-y-1">
                                <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">Labelling</label>
                                <select class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all appearance-none">
                                <option>Select labelling status</option>
                                <option>Completed</option>
                                <option>Partial</option>
                                </select>
                            </div>
                            <!-- QR Number -->
                            <div class="space-y-1">
                                <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">QR Number</label>
                                <input type="text" value="QR-ABT-240304" class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all">
                            </div>
                            <!-- Installed / Pre-existing -->
                            <div class="space-y-1">
                                <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">Installed / Pre-existing</label>
                                <select class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all appearance-none">
                                <option>Select installation status</option>
                                <option>Confirmed</option>
                                <option>Not Found</option>
                                </select>
                            </div>
                            <!-- Lead Identified -->
                            <div class="space-y-1">
                                <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">Lead Identified</label>
                                <select class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all appearance-none">
                                <option>Select lead status</option>
                                <option>Yes</option>
                                <option>No</option>
                                </select>
                            </div>
                            <!-- Samples Taken -->
                            <div class="space-y-1">
                                <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">Samples Taken</label>
                                <input type="number" value="0" class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all">
                            </div>
                            <!-- PCB's Identified -->
                            <div class="space-y-1">
                                <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">PCB's Identified</label>
                                <select class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all appearance-none">
                                <option>Select PCB status</option>
                                <option>Yes</option>
                                <option>No</option>
                                </select>
                            </div>
                            <!-- SMF Identified -->
                            <div class="space-y-1">
                                <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">SMF Identified</label>
                                <select class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all appearance-none">
                                <option>Select SMF status</option>
                                <option>Yes</option>
                                <option>No</option>
                                </select>
                            </div>
                            <!-- SMF Notes -->
                            <div class="md:col-span-2 space-y-1">
                                <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">SMF Notes</label>
                                <input type="text" placeholder="Optional note about SMF location or condition." class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all">
                            </div>
                        </div>
                        </div>
                        </div>

                        <!-- Right Cards: Audits & Attachments -->
                        <div class="space-y-6">
                        <!-- Audits Info -->
                        <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/10">
                        <div class="px-4 md:px-8 py-5 border-b border-surface-container flex justify-between items-center bg-[#fdfdfd] rounded-t-xl">
                            <h3 class="py-[5px] text-lg font-bold text-on-surface tracking-tight uppercase text-[15px]">Audits</h3>
                        </div>
                            <div class="p-6 space-y-6 max-h-[220px] overflow-y-auto scrollbar-thin">
                            <!-- Asbestos Item -->
                            <div class="p-4 bg-[#f8f9fa] border border-stone-100 rounded-xl">
                                <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <p class="text-[9px] font-semibold text-stone-400 uppercase tracking-widest mb-1">Name</p>
                                    <p class="text-sm font-bold text-on-surface">Asbestos</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-semibold text-stone-400 uppercase tracking-widest mb-1">Last audit</p>
                                    <p class="text-sm font-bold text-on-surface">12/04/2026</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-semibold text-stone-400 uppercase tracking-widest mb-1">Samples taken</p>
                                    <p class="text-sm font-bold text-on-surface">3</p>
                                </div>
                                </div>
                            </div>
                            <!-- Hazardous materials Item -->
                            <div class="p-4 bg-[#f8f9fa] border border-stone-100 rounded-xl overflow-x-auto scrollbar-thin">
                                <div class="grid grid-cols-3 gap-4 min-w-[320px]">
                                <div>
                                    <p class="text-[9px] font-semibold text-stone-400 uppercase tracking-widest mb-1">Name</p>
                                    <p class="text-sm font-bold text-on-surface">Hazardous materials</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-semibold text-stone-400 uppercase tracking-widest mb-1">Last audit</p>
                                    <p class="text-sm font-bold text-on-surface">06/04/2026</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-semibold text-stone-400 uppercase tracking-widest mb-1">Samples taken</p>
                                    <p class="text-sm font-bold text-on-surface">0</p>
                                </div>
                                </div>
                            </div>
                            <!-- Lead Item -->
                            <div class="p-4 bg-[#f8f9fa] border border-stone-100 rounded-xl overflow-x-auto scrollbar-thin">
                                <div class="grid grid-cols-3 gap-4 min-w-[320px]">
                                <div>
                                    <p class="text-[9px] font-semibold text-stone-400 uppercase tracking-widest mb-1">Name</p>
                                    <p class="text-sm font-bold text-on-surface">Lead Paint</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-semibold text-stone-400 uppercase tracking-widest mb-1">Last audit</p>
                                    <p class="text-sm font-bold text-on-surface">10/04/2026</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-semibold text-stone-400 uppercase tracking-widest mb-1">Samples taken</p>
                                    <p class="text-sm font-bold text-on-surface">1</p>
                                </div>
                                </div>
                            </div>
                            </div>
                        </div>

                        <!-- Primary Attachments -->
                        <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/10">
                            <div class="px-6 py-4 border-b border-surface-container">
                            <h3 class="text-sm font-semibold text-on-surface tracking-tight">Primary Attachments</h3>
                            </div>
                            <div class="p-6 space-y-4 max-h-[210px] overflow-y-auto scrollbar-thin">
                            <!-- Site Image -->
                            <div class="flex items-center justify-between p-3 hover:bg-[#f8f9fa] rounded-lg transition-colors group">
                                <div>
                                <p class="text-sm font-bold text-on-surface group-hover:text-orange-400 transition-colors">Site image</p>
                                <p class="text-[10px] text-stone-400 font-medium">19/10/2022</p>
                                </div>
                                <button class="px-3 py-1 bg-primary-container text-on-primary-container text-[10px] font-semibold rounded-lg uppercase shadow-sm hover:bg-orange-500 transition-all">View</button>
                            </div>
                            <!-- Clearance certificate -->
                            <div class="flex items-center justify-between p-3 hover:bg-[#f8f9fa] rounded-lg transition-colors group">
                                <div>
                                <p class="text-sm font-bold text-on-surface group-hover:text-orange-400 transition-colors">Clearance certificate</p>
                                <p class="text-[10px] text-stone-400 font-medium">14/11/2022</p>
                                </div>
                                <button class="px-3 py-1 bg-primary-container text-on-primary-container text-[10px] font-semibold rounded-lg uppercase shadow-sm hover:bg-orange-500 transition-all">View</button>
                            </div>
                            <!-- Asbestos report -->
                            <div class="flex items-center justify-between p-3 hover:bg-[#f8f9fa] rounded-lg transition-colors group">
                                <div>
                                <p class="text-sm font-bold text-on-surface group-hover:text-orange-400 transition-colors">Asbestos report</p>
                                <p class="text-[10px] text-stone-400 font-medium">13/02/2023</p>
                                </div>
                                <button class="px-3 py-1 bg-primary-container text-on-primary-container text-[10px] font-semibold rounded-lg uppercase shadow-sm hover:bg-orange-500 transition-all">View</button>
                            </div>
                            <!-- Sample Certificate -->
                            <div class="flex items-center justify-between p-3 hover:bg-[#f8f9fa] rounded-lg transition-colors group">
                                <div>
                                <p class="text-sm font-bold text-on-surface group-hover:text-orange-400 transition-colors">Sample Certificate</p>
                                <p class="text-[10px] text-stone-400 font-medium">15/03/2023</p>
                                </div>
                                <button class="px-3 py-1 bg-primary-container text-on-primary-container text-[10px] font-semibold rounded-lg uppercase shadow-sm hover:bg-orange-500 transition-all">View</button>
                            </div>
                            </div>
                        </div>
                        </div>
                    </div>

                    <!-- Bottom Section: Sample Register -->
                    <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/10 overflow-hidden">
                        <div class="px-4 md:px-8 py-6 border-b border-surface-container bg-[#fcfcfc] rounded-t-xl">
                        <p class="text-[10px] font-semibold text-stone-400 uppercase tracking-[0.2em] mb-1">SAMPLE REGISTER</p>
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
                            <h2 class="text-2xl md:text-[2rem] font-semibold text-[#1c1e21] leading-none">Asbestos Samples</h2>
                            <div class="flex gap-4 w-full md:w-auto">
                            <div class="flex-1 md:flex-none px-4 py-2 bg-orange-50 text-orange-600 text-xs font-semibold rounded-lg border border-orange-100 flex items-center justify-center gap-2">
                                3 samples
                            </div>
                            <button type="button" @click="openSampleModal" class="flex-1 md:flex-none px-6 py-2 bg-primary-container text-on-primary-container text-[11px] font-semibold rounded-lg uppercase hover:bg-orange-500 transition-all shadow-sm">Add sample</button>
                            </div>
                        </div>
                        </div>
                        
                        <!-- Register Content -->
                        <div class="p-4 md:p-8">
                        <div class="p-4 bg-[#f8f9fa] border-l-4 border-stone-200 rounded-r-lg mb-8">
                            <p class="text-[10px] font-semibold text-stone-400 uppercase tracking-widest mb-1">SAMPLES TABLE</p>
                            <p class="text-xs text-stone-500 font-medium">Select edit to update a sample. New samples open in a modal so the register remains readable.</p>
                        </div>

                        <div class="overflow-x-auto scrollbar-thin">
                            <table class="w-full text-left order-collapse min-w-[600px]">

                            <thead>
                            <tr class="border-b border-stone-100">
                                <th class="px-4 py-4 text-[11px] font-semibold text-stone-400 uppercase tracking-widest">Sample</th>
                                <th class="px-4 py-4 text-[11px] font-semibold text-stone-400 uppercase tracking-widest">Area</th>
                                <th class="px-4 py-4 text-[11px] font-semibold text-stone-400 uppercase tracking-widest">Material</th>
                                <th class="px-4 py-4 text-[11px] font-semibold text-stone-400 uppercase tracking-widest">Priority</th>
                                <th class="px-4 py-4 text-[11px] font-semibold text-stone-400 uppercase tracking-widest text-right">Action</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-50">
                            <!-- Sample 01 -->
                            <tr class="group hover:bg-[#fbfbfb] transition-colors">
                                <td class="px-4 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center text-orange-700 font-semibold text-xs border border-orange-100">01</div>
                                    <div>
                                    <p class="text-sm font-semibold text-[#1c1e21]">Sample 01</p>
                                    <p class="text-[10px] font-semibold text-stone-400 uppercase tracking-widest mt-0.5">MODAL EDITING</p>
                                    </div>
                                </div>
                                </td>
                                <td class="px-4 py-6 text-sm font-medium text-stone-500 italic">Area pending</td>
                                <td class="px-4 py-6 text-sm font-medium text-stone-500 italic">Material pending</td>
                                <td class="px-4 py-6">
                                <span class="px-3 py-1 bg-stone-100 text-stone-500 text-[9px] font-semibold rounded-full uppercase tracking-tighter">PENDING</span>
                                </td>
                                <td class="px-4 py-6 text-right">
                                <div class="flex justify-end gap-2">
                                    <button type="button" @click="openSampleModal" class="w-9 h-9 flex items-center justify-center bg-white text-[#1c1e21] rounded-lg border border-stone-200 shadow-sm hover:bg-stone-50 transition-all active:scale-95">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>
                                    <button class="w-9 h-9 flex items-center justify-center bg-white text-error rounded-lg border border-stone-200 shadow-sm hover:bg-red-50 transition-all active:scale-95">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </div>
                                </td>
                            </tr>
                            <!-- Sample 02 -->
                            <tr class="group hover:bg-[#fbfbfb] transition-colors">
                                <td class="px-4 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center text-orange-700 font-semibold text-xs border border-orange-100">02</div>
                                    <div>
                                    <p class="text-sm font-semibold text-[#1c1e21]">Sample 02</p>
                                    <p class="text-[10px] font-semibold text-stone-400 uppercase tracking-widest mt-0.5">MODAL EDITING</p>
                                    </div>
                                </div>
                                </td>
                                <td class="px-4 py-6 text-sm font-medium text-stone-500 italic">Area pending</td>
                                <td class="px-4 py-6 text-sm font-medium text-stone-500 italic">Material pending</td>
                                <td class="px-4 py-6">
                                <span class="px-3 py-1 bg-stone-100 text-stone-500 text-[9px] font-semibold rounded-lg uppercase tracking-tighter">PENDING</span>
                                </td>
                                <td class="px-4 py-6 text-right">
                                <div class="flex justify-end gap-2">
                                    <button type="button" @click="openSampleModal" class="w-9 h-9 flex items-center justify-center bg-white text-[#1c1e21] rounded-lg border border-stone-200 shadow-sm hover:bg-stone-50 transition-all active:scale-95">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>
                                    <button class="w-9 h-9 flex items-center justify-center bg-white text-error rounded-lg border border-stone-200 shadow-sm hover:bg-red-50 transition-all active:scale-95">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </div>
                                </td>
                            </tr>
                            <!-- Sample 03 -->
                            <tr class="group hover:bg-[#fbfbfb] transition-colors">
                                <td class="px-4 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center text-orange-700 font-semibold text-xs border border-orange-100">03</div>
                                    <div>
                                    <p class="text-sm font-semibold text-[#1c1e21]">Sample 03</p>
                                    <p class="text-[10px] font-semibold text-stone-400 uppercase tracking-widest mt-0.5">MODAL EDITING</p>
                                    </div>
                                </div>
                                </td>
                                <td class="px-4 py-6 text-sm font-medium text-stone-500 italic">Area pending</td>
                                <td class="px-4 py-6 text-sm font-medium text-stone-500 italic">Material pending</td>
                                <td class="px-4 py-6">
                                <span class="px-3 py-1 bg-stone-100 text-stone-500 text-[9px] font-semibold rounded-lg uppercase tracking-tighter">PENDING</span>
                                </td>
                                <td class="px-4 py-6 text-right">
                                <div class="flex justify-end gap-2">
                                    <button type="button" @click="openSampleModal" class="w-9 h-9 flex items-center justify-center bg-white text-[#1c1e21] rounded-lg border border-stone-200 shadow-sm hover:bg-stone-50 transition-all active:scale-95">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>
                                    <button class="w-9 h-9 flex items-center justify-center bg-white text-error rounded-lg border border-stone-200 shadow-sm hover:bg-red-50 transition-all active:scale-95">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        </div>
                    </div>
                    </div>
                </div>
            <Footer />
        </div>
    </AppShell>
        <div v-if="isSampleModalOpen" id="sampleModal" class="fixed inset-0 z-[100]">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/40 backdrop-blur-[2px]" @click="closeSampleModal"></div>
            
            <!-- Modal Container -->
            <div class="absolute inset-0 flex items-center justify-center p-4 md:p-8 pointer-events-none">
            <div class="w-full max-w-[900px] bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-200 pointer-events-auto flex flex-col max-h-[90vh]">
                
                <!-- Modal Header -->
                <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-start bg-[#fdfdfd] shrink-0">
                <div>
                    <p class="text-[10px] font-semibold text-stone-400 uppercase tracking-[0.2em] mb-1">Add Sample</p>
                    <h2 class="text-[2rem] font-semibold text-[#1c1e21] leading-none">Sample 04</h2>
                </div>
                <button type="button" @click="closeSampleModal" class="px-6 py-2 bg-white border border-stone-200 text-[#1c1e21] text-xs font-semibold rounded-lg hover:bg-gray-50 transition-all shadow-sm">
                    Close
                </button>
                </div>

                <!-- Modal Scrollable Content -->
                <div class="p-8 overflow-y-auto scrollbar-thin">
                <div class="border border-stone-100 rounded-xl p-8 bg-white shadow-sm">
                    <div class="mb-8">
                    <p class="text-[10px] font-semibold text-stone-400 uppercase tracking-widest mb-1">SAMPLE DETAILS</p>
                    <h3 class="text-xl font-semibold text-[#1c1e21]">Edit Sample Information</h3>
                    </div>

                    <form class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <!-- Row 1: Sample & Photo -->
                    <div class="space-y-1">
                        <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">SAMPLE</label>
                        <input type="text" placeholder="AB-001" class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">PHOTO</label>
                        <input type="text" placeholder="4 photos or file ref" class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all">
                    </div>

                    <!-- Row 2: Building / Area -->
                    <div class="md:col-span-2 space-y-1">
                        <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">BUILDING / AREA</label>
                        <input type="text" placeholder="Building A / Boiler Room" class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all">
                    </div>

                    <!-- Row 3: Location -->
                    <div class="md:col-span-2 space-y-1">
                        <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">LOCATION</label>
                        <input type="text" placeholder="North wall service chase" class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all">
                    </div>

                    <!-- Row 4: Surface & Material -->
                    <div class="space-y-1">
                        <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">SURFACE</label>
                        <input type="text" placeholder="Lagged pipe elbow" class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">MATERIAL</label>
                        <input type="text" placeholder="Thermal insulation" class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all">
                    </div>

                    <!-- Row 5: Hazardous Material & Approx Qty -->
                    <div class="space-y-1">
                        <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">HAZARDOUS MATERIAL</label>
                        <input type="text" placeholder="Amosite" class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">APPROX. QTY (M2)</label>
                        <input type="text" placeholder="6.2" class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all">
                    </div>

                    <!-- Row 6: Condition & Readily Accessible -->
                    <div class="space-y-1">
                        <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">CONDITION</label>
                        <div class="relative">
                        <select class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none appearance-none cursor-pointer">
                            <option>Select condition</option>
                            <option>Good</option>
                            <option>Fair</option>
                            <option>Poor</option>
                        </select>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">READILY ACCESSIBLE</label>
                        <div class="relative">
                        <select class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none appearance-none cursor-pointer">
                            <option>Select access level</option>
                            <option>Yes</option>
                            <option>No</option>
                        </select>
                        </div>
                    </div>

                    <!-- Row 7: Friable & Hazard Priority -->
                    <div class="space-y-1">
                        <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">FRIABLE</label>
                        <div class="relative">
                        <select class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none appearance-none cursor-pointer">
                            <option>Select friability</option>
                            <option>Friable</option>
                            <option>Non-friable</option>
                        </select>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">HAZARD PRIORITY</label>
                        <div class="relative">
                        <select class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none appearance-none cursor-pointer">
                            <option>Select priority</option>
                            <option>Low</option>
                            <option>Medium</option>
                            <option>High</option>
                        </select>
                        </div>
                    </div>

                    <!-- Row 8: Comments -->
                    <div class="md:col-span-2 space-y-1">
                        <label class="block text-[10px] font-semibold text-stone-400 uppercase tracking-widest ml-1">COMMENTS</label>
                        <textarea rows="4" placeholder="Describe access risk, condition, or maintenance disturbance..." class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all resize-none"></textarea>
                    </div>
                    </form>
                </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-8 py-6 border-t border-gray-100 flex justify-end items-center bg-[#fdfdfd] shrink-0">
                <button type="button" @click="closeSampleModal" class="px-12 py-3 bg-primary-container text-white text-xs font-semibold rounded-lg uppercase tracking-widest hover:bg-orange-500 transition-all shadow-lg active:scale-95">
                    Save Changes
                </button>
                </div>
            </div>
            </div>
        </div>
</template>
<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import { ref, computed, watch, onMounted, onBeforeUnmount } from "vue";
import AppShell from "@/Components/Layout/AppShell.vue";
import Footer from "@/Components/Layout/Footer.vue";
import Button from "@/Components/Common/Button.vue";

const form = useForm({
    property_id: null,
    audit_date: '',
    audit_hours: '',
    job_type: '',
    labelling_status: '',
    qr_number: '',
    installation_status: '',
    lead_status: '',
    samples_taken: '',
    smf_status: '',
    smf_notes: '',
    samples: [],
});

function submitForm() {
    form.property_id = selectedPropertyId.value;
    form.post('/asbestos-audits', {
        preserveScroll: true,
    });
}

const props = defineProps({
    customers: Array,
    selectedProperty: Object,
});

const selectedCustomer = ref(null);
const selectedPropertyRef = ref(null);
const selectedPropertyId = ref(null);

// Modal state
const isChangeModalOpen = ref(false);
const customerSearch = ref("");
const propertySearch = ref("");
const selectedCustomerInModal = ref(null);
const selectedPropertyInModal = ref(null);
const isCustomerDropdownOpen = ref(false);
const isPropertyDropdownOpen = ref(false);

onMounted(() => {
    if (props.selectedProperty) {
        const property = props.selectedProperty;
        selectedPropertyRef.value = property;
        selectedPropertyId.value = property.id;

        if (property.customer) {
            const customer = props.customers.find(c => c.id === property.customer.id);
            if (customer) {
                selectedCustomer.value = customer;
            }
        }
    }
});

const filteredCustomers = computed(() => {
    if (!customerSearch.value) return props.customers;
    const search = customerSearch.value.toLowerCase();
    return props.customers.filter(
        (c) =>
            c.name.toLowerCase().includes(search) ||
            (c.email && c.email.toLowerCase().includes(search))
    );
});

const modalCustomerProperties = computed(() => {
    if (!selectedCustomerInModal.value) return [];
    return selectedCustomerInModal.value.properties || [];
});

const filteredProperties = computed(() => {
    if (!propertySearch.value) return modalCustomerProperties.value;
    const search = propertySearch.value.toLowerCase();
    return modalCustomerProperties.value.filter(
        (p) =>
            (p.name && p.name.toLowerCase().includes(search)) ||
            (p.address && p.address.toLowerCase().includes(search)) ||
            (p.suburb && p.suburb.toLowerCase().includes(search)) ||
            (p.property_type && p.property_type.toLowerCase().includes(search))
    );
});

function openChangeModal() {
    customerSearch.value = selectedCustomer.value?.name || "";
    propertySearch.value = selectedPropertyRef.value?.name || "";
    selectedCustomerInModal.value = selectedCustomer.value;
    selectedPropertyInModal.value = selectedPropertyRef.value;
    isCustomerDropdownOpen.value = false;
    isPropertyDropdownOpen.value = false;
    isChangeModalOpen.value = true;
}

function closeChangeModal() {
    isChangeModalOpen.value = false;
}

function selectCustomerInModal(customer) {
    selectedCustomerInModal.value = customer;
    customerSearch.value = customer.name;
    selectedPropertyInModal.value = null;
    propertySearch.value = "";
    isCustomerDropdownOpen.value = false;
}

function clearCustomerInModal() {
    selectedCustomerInModal.value = null;
    selectedPropertyInModal.value = null;
    customerSearch.value = "";
    propertySearch.value = "";
}

function selectPropertyInModal(property) {
    selectedPropertyInModal.value = property;
    propertySearch.value = property.name || 'Unnamed Property';
    isPropertyDropdownOpen.value = false;
}

function clearPropertyInModal() {
    selectedPropertyInModal.value = null;
    propertySearch.value = "";
}

function confirmSelection() {
    if (!selectedPropertyInModal.value) return;
    const customer = selectedCustomerInModal.value;
    const property = selectedPropertyInModal.value;
    selectedCustomer.value = customer;
    selectedPropertyRef.value = property;
    selectedPropertyId.value = property.id;
    isChangeModalOpen.value = false;

    // Update URL to reflect new property
    const url = new URL(window.location);
    url.searchParams.set('property_id', property.id);
    window.history.replaceState({}, '', url);
}

function onCustomerSearchFocus() {
    isCustomerDropdownOpen.value = true;
}

function onCustomerSearchBlur() {
    setTimeout(() => {
        isCustomerDropdownOpen.value = false;
    }, 200);
}

function onPropertySearchFocus() {
    isPropertyDropdownOpen.value = true;
}

function onPropertySearchBlur() {
    setTimeout(() => {
        isPropertyDropdownOpen.value = false;
    }, 200);
}

watch(isChangeModalOpen, (isOpen) => {
    document.body.style.overflow = isOpen ? "hidden" : "";
});

onBeforeUnmount(() => {
    document.body.style.overflow = "";
});
</script>

<template>
    <Head title="Asbestos Audits | Create" />
    <AppShell>
        <div class="flex-1 pt-24 px-4 md:px-8 pb-12 space-y-6">
            <!-- Customer & Property Display -->
            <div
                class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/10 flex flex-col w-full"
            >
                <div
                    class="px-4 md:px-8 py-5 border-b border-surface-container flex justify-between items-center bg-[#fdfdfd] rounded-t-xl"
                >
                    <h3
                        class="text-lg font-bold text-on-surface tracking-tight uppercase app-text-medium"
                    >
                        Customer & Property
                    </h3>
                </div>

                <div class="p-4 md:p-10">
                    <div class="flex flex-col md:flex-row gap-4 md:gap-6">
                        <!-- Customer (click to change) -->
                        <div class="w-full md:flex-1">
                            <label
                                class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1 mb-1"
                            >Customer</label>
                            <div class="relative cursor-pointer" @click="openChangeModal">
                                <span
                                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant/40 text-lg"
                                >person</span>
                                <input
                                    type="text"
                                    readonly
                                    :value="selectedCustomer?.name || 'Select Customer'"
                                    class="w-full bg-surface-container-low border-none rounded-lg py-4 md:py-5 pl-11 md:pl-12 pr-10 md:pr-12 text-sm outline-none transition-all cursor-pointer text-on-surface hover:bg-surface-container-low/80"
                                />
                                <span
                                    class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant/30 text-lg pointer-events-none"
                                >edit</span>
                            </div>
                        </div>

                        <!-- Property (click to change) -->
                        <div class="w-full md:flex-1">
                            <label
                                class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1 mb-1"
                            >Property</label>
                            <div class="relative cursor-pointer" @click="openChangeModal">
                                <span
                                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant/40 text-lg"
                                >home_work</span>
                                <input
                                    type="text"
                                    readonly
                                    :value="selectedPropertyRef?.name || 'Select Property'"
                                    class="w-full bg-surface-container-low border-none rounded-lg py-4 md:py-5 pl-11 md:pl-12 pr-10 md:pr-12 text-sm outline-none transition-all cursor-pointer text-on-surface hover:bg-surface-container-low/80"
                                />
                                <span
                                    class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant/30 text-lg pointer-events-none"
                                >edit</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Site Details Form (only visible after property is selected) -->
            <div
                v-if="selectedPropertyId"
                class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/10 flex flex-col w-full"
            >
                <div
                    class="px-4 md:px-8 py-5 border-b border-surface-container flex justify-between items-center bg-[#fdfdfd] rounded-t-xl"
                >
                    <h3
                        class="text-lg font-bold text-on-surface tracking-tight uppercase app-text-medium"
                    >
                        Site details
                    </h3>
                    <Button @click="submitForm" :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Save' }}
                    </Button>
                </div>

                <!-- Validation Errors -->
                <div v-if="Object.keys(form.errors).length" class="px-4 md:px-10 py-3">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-sm font-bold text-red-800 mb-2">Please fix the following errors:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li v-for="(error, field) in form.errors" :key="field" class="text-sm text-red-700">{{ error }}</li>
                        </ul>
                    </div>
                </div>

                <div class="p-4 md:p-10">
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-x-8 gap-y-6"
                    >
                        <div class="space-y-1">
                            <label
                                class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1"
                                >Audit Date</label
                            >
                            <input
                                v-model="form.audit_date"
                                type="date"
                                class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all"
                            />
                        </div>

                        <div class="space-y-1">
                            <label
                                class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1"
                                >Audit Hours</label
                            >
                            <input
                                v-model="form.audit_hours"
                                type="text"
                                placeholder="e.g. 08:30 - 15:45"
                                class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all"
                            />
                        </div>

                        <div class="space-y-1">
                            <label
                                class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1"
                                >Job Type</label
                            >
                            <select
                                v-model="form.job_type"
                                class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all appearance-none cursor-pointer"
                            >
                                <option value="">Job Type</option>
                                <option value="Management Plan Review">Management Plan Review</option>
                                <option value="Reinspection">Reinspection</option>
                                <option value="Sampling Audit">Sampling Audit</option>
                                <option value="Refurbishment Review">Refurbishment Review</option>
                                <option value="Demolition Survey">Demolition Survey</option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label
                                class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1"
                                >Labelling</label
                            >
                            <select
                                v-model="form.labelling_status"
                                class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all appearance-none"
                            >
                                <option value="">Labelling Status</option>
                                <option value="Installed">Installed</option>
                                <option value="Pre-existing">Pre-existing</option>
                                <option value="Mixed">Mixed</option>
                                <option value="Unknown">Unknown</option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label
                                class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1"
                                >QR Number</label
                            >
                            <input
                                v-model="form.qr_number"
                                type="text"
                                placeholder="e.g. QR-ABT-240304"
                                class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all"
                            />
                        </div>

                        <div class="space-y-1">
                            <label
                                class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1"
                                >Installed / Pre-existing</label
                            >
                            <select
                                v-model="form.installation_status"
                                class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all appearance-none"
                            >
                                <option value="">Installation Status</option>
                                <option value="Installed">Installed</option>
                                <option value="Pre-existing">Pre-existing</option>
                                <option value="Mixed">Mixed</option>
                                <option value="Unknown">Unknown</option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label
                                class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1"
                                >Lead Identified</label
                            >
                            <select
                                v-model="form.lead_status"
                                class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all appearance-none"
                            >
                                <option value="">Lead Status</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label
                                class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1"
                                >Samples Taken</label
                            >
                            <input
                                v-model="form.samples_taken"
                                type="text"
                                value="0"
                                class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all"
                            />
                        </div>

                        <div class="space-y-1">
                            <label
                                class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1"
                                >PCB's Identified</label
                            >
                            <select
                                v-model="form.smf_status"
                                class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all appearance-none"
                            >
                                <option value="">SMF Status</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label
                                class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1"
                                >SMF Identified</label
                            >
                            <select
                                class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all appearance-none"
                            >
                                <option>SMF Status</option>
                                <option>Yes</option>
                                <option>No</option>
                            </select>
                        </div>

                        <div class="md:col-span-2 xl:col-span-2 space-y-1">
                            <label
                                class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1"
                                >SMF Notes</label
                            >
                            <input
                                v-model="form.smf_notes"
                                type="text"
                                placeholder="Optional note about SMF location or condition."
                                class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Footer />
    </AppShell>

    <!-- Customer & Property Change Modal -->
    <div
        v-if="isChangeModalOpen"
        class="fixed inset-0 z-[100]"
    >
        <div
            class="absolute inset-0 bg-black/40 backdrop-blur-[2px]"
            @click="closeChangeModal"
        ></div>

        <div
            class="absolute inset-0 flex items-center justify-center p-4 md:p-8 pointer-events-none"
        >
            <div
                class="w-full max-w-[700px] bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-200 pointer-events-auto flex flex-col max-h-[92vh]"
            >
                <!-- Modal Header -->
                <div
                    class="px-6 md:px-8 py-5 md:py-6 border-b border-gray-100 flex justify-between items-center bg-[#fdfdfd] shrink-0"
                >
                    <div>
                        <p class="app-text font-semibold text-stone-400 uppercase tracking-[0.2em] mb-1">Change</p>
                        <h2 class="text-xl md:text-2xl font-semibold text-[#1c1e21] leading-none">
                            Select Customer & Property
                        </h2>
                    </div>
                    <button
                        type="button"
                        class="cursor-pointer px-4 md:px-6 py-2 bg-white border border-stone-200 text-[#1c1e21] text-xs font-semibold rounded-lg hover:bg-gray-50 transition-all shadow-sm"
                        @click="closeChangeModal"
                    >
                        Close
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="px-6 md:px-8 py-5 overflow-visible flex-1">
                    <!-- Customer Search -->
                    <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1 mb-1">Customer</label>
                    <div class="mb-6">
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant/40 text-lg">search</span>
                            <input
                                v-model="customerSearch"
                                type="text"
                                placeholder="Search Customer"
                                class="w-full bg-surface-container-low border-none rounded-lg py-4 pl-11 md:pl-12 pr-10 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all"
                                @focus="onCustomerSearchFocus"
                                @blur="onCustomerSearchBlur"
                            />
                            <button
                                v-if="selectedCustomerInModal"
                                type="button"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant/40 hover:text-error transition-colors cursor-pointer"
                                @click="clearCustomerInModal"
                            >
                                <span class="material-symbols-outlined text-lg">close</span>
                            </button>
                        </div>

                        <!-- Customer Dropdown -->
                        <div
                            v-if="isCustomerDropdownOpen && !selectedCustomerInModal"
                            class="z-10 mt-1 bg-surface-container-lowest rounded-xl shadow-lg border border-outline-variant/10 max-h-52 overflow-y-auto scrollbar-thin overflow-hidden"
                        >
                            <div
                                v-if="filteredCustomers.length === 0"
                                class="px-6 py-4 text-sm text-on-surface-variant/50 italic text-center"
                            >
                                No customers found.
                            </div>
                            <button
                                v-for="customer in filteredCustomers"
                                :key="customer.id"
                                type="button"
                                class="w-full text-left px-4 py-3 md:px-6 md:py-4 hover:bg-surface-container-low transition-colors cursor-pointer flex items-center gap-3 md:gap-4 border-b border-surface-container last:border-b-0"
                                @click="selectCustomerInModal(customer)"
                            >
                                <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-primary-container/20 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-primary text-base md:text-lg">person</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs md:text-sm font-bold text-on-surface truncate">{{ customer.name }}</p>
                                    <p class="app-text text-on-surface-variant/60 truncate">
                                        {{ customer.email }}
                                        <span v-if="customer.properties?.length" class="ml-1 md:ml-2 text-primary font-semibold">
                                            · {{ customer.properties.length }} {{ customer.properties.length === 1 ? 'property' : 'properties' }}
                                        </span>
                                    </p>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Property Selection -->
                    <template v-if="selectedCustomerInModal">
                        <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1 mb-1">Property</label>
                        <div class="mb-6">
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant/40 text-lg">home_work</span>
                                <input
                                    v-model="propertySearch"
                                    type="text"
                                    placeholder="Search Property"
                                    class="w-full bg-surface-container-low border-none rounded-lg py-4 pl-11 md:pl-12 pr-10 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all"
                                    @focus="onPropertySearchFocus"
                                    @blur="onPropertySearchBlur"
                                />
                                <button
                                    v-if="selectedPropertyInModal"
                                    type="button"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant/40 hover:text-error transition-colors cursor-pointer"
                                    @click="clearPropertyInModal"
                                >
                                    <span class="material-symbols-outlined text-lg">close</span>
                                </button>
                            </div>

                            <!-- Property Dropdown -->
                            <div
                                v-if="isPropertyDropdownOpen && !selectedPropertyInModal"
                                class="z-10 mt-1 bg-surface-container-lowest rounded-xl shadow-lg border border-outline-variant/10 max-h-52 overflow-y-auto scrollbar-thin overflow-hidden"
                            >
                                <div
                                    v-if="modalCustomerProperties.length === 0"
                                    class="px-6 py-4 text-sm text-on-surface-variant/50 italic text-center"
                                >
                                    This customer has no properties.
                                </div>
                                <div
                                    v-else-if="filteredProperties.length === 0"
                                    class="px-6 py-4 text-sm text-on-surface-variant/50 italic text-center"
                                >
                                    No properties match your search.
                                </div>
                                <button
                                    v-for="property in filteredProperties"
                                    :key="property.id"
                                    type="button"
                                    class="w-full text-left px-4 py-3 md:px-6 md:py-4 hover:bg-surface-container-low transition-colors cursor-pointer flex items-center gap-3 md:gap-4 border-b border-surface-container last:border-b-0"
                                    @click="selectPropertyInModal(property)"
                                >
                                    <div class="w-8 h-8 md:w-10 md:h-10 rounded-lg bg-primary-container/15 flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-primary text-base md:text-lg">home_work</span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs md:text-sm font-bold text-on-surface truncate">{{ property.name || 'Unnamed Property' }}</p>
                                        <p class="app-text text-on-surface-variant/60 truncate">
                                            {{ [property.address, property.suburb, property.state, property.postcode].filter(Boolean).join(', ') || 'No address' }}
                                        </p>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <!-- Confirm Button -->
                        <button
                            v-if="selectedPropertyInModal"
                            type="button"
                            class="w-full cursor-pointer flex items-center justify-center gap-2 px-6 py-3 bg-primary-container text-on-primary-container rounded-lg font-black app-text uppercase tracking-[0.2em] hover:bg-orange-500 transition-all shadow-sm"
                            @click="confirmSelection"
                        >
                            <span class="material-symbols-outlined text-sm">check</span>
                            Confirm Selection
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { ref, computed, watch, onBeforeUnmount } from 'vue';
import AppShell from '@/Components/Layout/AppShell.vue';
import Footer from '@/Components/Layout/Footer.vue';
import Button from '@/Components/Common/Button.vue';
import Pagination from '@/Components/Common/Pagination.vue';

const props = defineProps({
    audits: Object,
    filters: Object,
    customers: Array,
});

const isSampleModalOpen = ref(false);
const activeRowId = ref(null);

// Customer & Property modal state
const isAddModalOpen = ref(false);
const customerSearch = ref("");
const propertySearch = ref("");
const selectedCustomer = ref(null);
const selectedPropertyId = ref(null);
const selectedPropertyInModal = ref(null);
const isCustomerDropdownOpen = ref(false);
const isPropertyDropdownOpen = ref(false);

const filteredCustomers = computed(() => {
    if (!customerSearch.value) return props.customers;
    const search = customerSearch.value.toLowerCase();
    return props.customers.filter(
        (c) =>
            c.name.toLowerCase().includes(search) ||
            (c.email && c.email.toLowerCase().includes(search))
    );
});

const selectedCustomerProperties = computed(() => {
    if (!selectedCustomer.value) return [];
    return selectedCustomer.value.properties || [];
});

const filteredProperties = computed(() => {
    if (!propertySearch.value) return selectedCustomerProperties.value;
    const search = propertySearch.value.toLowerCase();
    return selectedCustomerProperties.value.filter(
        (p) =>
            (p.name && p.name.toLowerCase().includes(search)) ||
            (p.address && p.address.toLowerCase().includes(search)) ||
            (p.suburb && p.suburb.toLowerCase().includes(search)) ||
            (p.property_type && p.property_type.toLowerCase().includes(search))
    );
});

function openAddModal() {
    customerSearch.value = "";
    propertySearch.value = "";
    selectedCustomer.value = null;
    selectedPropertyId.value = null;
    selectedPropertyInModal.value = null;
    isAddModalOpen.value = true;
}

function closeAddModal() {
    isAddModalOpen.value = false;
}

function selectCustomer(customer) {
    selectedCustomer.value = customer;
    customerSearch.value = customer.name;
    selectedPropertyId.value = null;
    selectedPropertyInModal.value = null;
    propertySearch.value = "";
    isCustomerDropdownOpen.value = false;
}

function clearCustomerSelection() {
    selectedCustomer.value = null;
    selectedPropertyId.value = null;
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

function confirmAndProceed() {
    if (!selectedPropertyInModal.value) return;
    selectedPropertyId.value = selectedPropertyInModal.value.id;
    isAddModalOpen.value = false;
    router.visit(`/asbestos-audits/create?property_id=${selectedPropertyInModal.value.id}`);
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

function openSampleModal() {
  isSampleModalOpen.value = true;
}

function closeSampleModal() {
  isSampleModalOpen.value = false;
}

function toggleRow(id) {
  activeRowId.value = activeRowId.value === id ? null : id;
}

function isRowActive(id) {
  return activeRowId.value === id;
}

function deleteAudit(id) {
    if (confirm('Are you sure you want to delete this audit?')) {
        router.delete(`/asbestos-audits/${id}`);
    }
}

watch(isAddModalOpen, (isOpen) => {
    document.body.style.overflow = isOpen ? "hidden" : "";
});

onBeforeUnmount(() => {
    document.body.style.overflow = "";
});
</script>

<template>
    <Head title="Asbestos Audits | title" />
    <AppShell>
    <div class="pt-24 px-8 pb-12">
      <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden border border-outline-variant/10">
        <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-surface-container">
          <div>
            <h2 class="text-lg font-bold text-on-surface uppercase tracking-tight">Asbestos Audit</h2>
            <p class="text-xs text-on-surface-variant/60 font-medium">Manage customer data and track job order history.</p>
          </div>
          <div class="flex items-center gap-3">
            <div class="relative">
              <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant/40 text-lg" data-icon="search">search</span>
              <input
                type="text"
                placeholder="Search"
                class="pl-10 pr-4 py-3 w-64 bg-surface-container-highest border-none rounded-lg app-text focus:ring-2 focus:ring-primary-container outline-none transition-all"
              />
            </div>
            <button type="button" @click="openAddModal" class="cursor-pointer flex items-center gap-2 px-4 py-2 bg-primary-container text-on-primary-container rounded-lg font-black app-text uppercase tracking-[0.2em] hover:bg-orange-500 transition-all shadow-sm">
              <span class="material-symbols-outlined text-sm" data-icon="add">add</span>
              Add New
            </button>
          </div>
        </div>
        
        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="border-b border-surface-container">
                <th class="w-10 px-6 py-4"></th>
                <th class="px-6 py-4 app-text font-bold uppercase tracking-widest text-on-surface-variant/60">job id</th>
                <th class="px-6 py-4 app-text font-bold uppercase tracking-widest text-on-surface-variant/60">audit date</th>
                <th class="px-6 py-4 app-text font-bold uppercase tracking-widest text-on-surface-variant/60">customer</th>
                <th class="px-6 py-4 app-text font-bold uppercase tracking-widest text-on-surface-variant/60">property</th>
                <th class="px-6 py-4 app-text font-bold uppercase tracking-widest text-on-surface-variant/60">address</th>
                <th class="px-6 py-4 app-text font-bold uppercase tracking-widest text-on-surface-variant/60 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-surface-container">
              <template v-if="audits?.data?.length">
                <template v-for="audit in audits.data" :key="audit.id">
                  <tr
                    class="parent-row group transition-colors duration-150"
                    :class="isRowActive(`audit-${audit.id}`) ? 'active bg-surface-container-low' : 'hover:bg-surface-container-low'"
                    @click="toggleRow(`audit-${audit.id}`)"
                  >
                    <td class="px-6 py-5">
                      <span
                        class="material-symbols-outlined transition-transform chevron-icon"
                        :class="isRowActive(`audit-${audit.id}`) ? 'rotate-90 text-primary' : 'text-on-surface-variant/40'"
                      >
                        chevron_right
                      </span>
                    </td>
                    <td class="px-6 py-5">
                      <p class="text-sm font-bold text-on-surface">{{ audit.qr_number || `#${audit.id}` }}</p>
                    </td>
                    <td class="px-6 py-5">
                      <p class="text-sm text-on-surface">{{ audit.audit_date ? new Date(audit.audit_date).toLocaleDateString('en-AU', { day: '2-digit', month: 'short', year: 'numeric' }) : '—' }}</p>
                    </td>
                    <td class="px-6 py-5">
                      <p class="text-sm font-semibold text-on-surface">{{ audit.property?.customer?.name || '—' }}</p>
                    </td>
                    <td class="px-6 py-5">
                      <p class="text-sm text-on-surface">{{ audit.property?.name || '—' }}</p>
                    </td>
                    <td class="px-6 py-5">
                      <p class="text-sm text-on-surface-variant/70">{{ [audit.property?.address, audit.property?.suburb, audit.property?.state, audit.property?.postcode].filter(Boolean).join(', ') || '—' }}</p>
                    </td>
                    <td class="px-6 py-5 text-right">
                      <div class="flex items-center justify-end gap-2" @click.stop>
                        <button
                          class="cursor-pointer p-2 hover:bg-surface-container-high rounded-lg transition-colors text-on-surface-variant"
                          title="Edit Audit"
                          @click="router.visit(`/asbestos-audits/${audit.id}/edit`)"
                        >
                          <span class="material-symbols-outlined text-lg">edit</span>
                        </button>
                        <button
                          class="cursor-pointer p-2 hover:bg-error-container/20 rounded-lg transition-colors text-error"
                          title="Delete Audit"
                          @click="deleteAudit(audit.id)"
                        >
                          <span class="material-symbols-outlined text-lg">delete</span>
                        </button>
                      </div>
                    </td>
                  </tr>
                  <!-- Expanded Row -->
                  <tr
                    v-show="isRowActive(`audit-${audit.id}`)"
                    class="expanded-row bg-surface-container-low"
                  >
                    <td colspan="7" class="px-12 py-6">
                      <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 p-6 shadow-inner">
                        <h3 class="text-xs font-black uppercase tracking-[0.2em] text-on-surface-variant/60 mb-4 flex items-center gap-2">
                          <span class="material-symbols-outlined text-sm">list_alt</span>
                          Audit Details
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                          <div>
                            <p class="app-text font-semibold text-on-surface-variant/50 uppercase tracking-widest">Job Type</p>
                            <p class="text-sm font-bold text-on-surface mt-1">{{ audit.job_type || '—' }}</p>
                          </div>
                          <div>
                            <p class="app-text font-semibold text-on-surface-variant/50 uppercase tracking-widest">Audit Hours</p>
                            <p class="text-sm font-bold text-on-surface mt-1">{{ audit.audit_hours || '—' }}</p>
                          </div>
                          <div>
                            <p class="app-text font-semibold text-on-surface-variant/50 uppercase tracking-widest">Labelling</p>
                            <p class="text-sm font-bold text-on-surface mt-1">{{ audit.labelling_status || '—' }}</p>
                          </div>
                          <div>
                            <p class="app-text font-semibold text-on-surface-variant/50 uppercase tracking-widest">Samples</p>
                            <p class="text-sm font-bold text-on-surface mt-1">{{ audit.samples?.length ?? audit.samples_taken ?? 0 }}</p>
                          </div>
                        </div>
                      </div>
                    </td>
                  </tr>
                </template>
              </template>
              <tr v-else>
                <td colspan="7" class="px-6 py-12 text-center">
                  <p class="text-sm text-on-surface-variant/50 italic">No audits found.</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <Pagination
          v-if="$page.props.audits"
          :paginator="$page.props.audits"
          noun="audits"
        />
      </div>
    </div>
        <Footer />
    </AppShell>

    <!-- Customer & Property Selection Modal -->
    <div
        v-if="isAddModalOpen"
        class="fixed inset-0 z-[100]"
    >
        <div
            class="absolute inset-0 bg-black/40 backdrop-blur-[2px]"
            @click="closeAddModal"
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
                        <p class="app-text font-semibold text-stone-400 uppercase tracking-[0.2em] mb-1">New Audit</p>
                        <h2 class="text-xl md:text-2xl font-semibold text-[#1c1e21] leading-none">
                            Select Customer & Property
                        </h2>
                    </div>
                    <button
                        type="button"
                        class="cursor-pointer px-4 md:px-6 py-2 bg-white border border-stone-200 text-[#1c1e21] text-xs font-semibold rounded-lg hover:bg-gray-50 transition-all shadow-sm"
                        @click="closeAddModal"
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
                                v-if="selectedCustomer"
                                type="button"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-on-surface-variant/40 hover:text-error transition-colors cursor-pointer"
                                @click="clearCustomerSelection"
                            >
                                <span class="material-symbols-outlined text-lg">close</span>
                            </button>
                        </div>

                        <!-- Customer Dropdown -->
                        <div
                            v-if="isCustomerDropdownOpen && !selectedCustomer"
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
                                @click="selectCustomer(customer)"
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
                    <template v-if="selectedCustomer">
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
                                    v-if="selectedCustomerProperties.length === 0"
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
                            @click="confirmAndProceed"
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
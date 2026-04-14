<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppShell from '@/Components/Layout/AppShell.vue';
import Footer from '@/Components/Layout/Footer.vue';
import ConfirmModal from '@/Components/Common/ConfirmModal.vue';
import Button from '@/Components/Common/Button.vue';
import Pagination from '@/Components/Common/Pagination.vue';

const props = defineProps({
    customers: Object,
    filters: Object,
});

const search = ref(props.filters?.search ?? '');
let searchTimeout = null;

watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get('/customers', { search: value || undefined }, {
            preserveState: true,
            replace: true,
        });
    }, 300);
});

const activeRowId = ref(null);
const showDeleteModal = ref(false);
const customerToDelete = ref(null);
const deleting = ref(false);

function toggleRow(id) {
    activeRowId.value = activeRowId.value === id ? null : id;
}

function isRowActive(id) {
    return activeRowId.value === id;
}

function confirmDelete(customer) {
    customerToDelete.value = customer;
    showDeleteModal.value = true;
}

function cancelDelete() {
    showDeleteModal.value = false;
    customerToDelete.value = null;
}

function deleteCustomer() {
    deleting.value = true;
    router.visit(`/customers/${customerToDelete.value.id}`, {
        method: 'delete',
        onFinish: () => {
            deleting.value = false;
            showDeleteModal.value = false;
            customerToDelete.value = null;
        },
    });
}
</script>

<template>
    <Head title="Customer" />
    <AppShell>
    <div class="pt-24 px-8 pb-12">
      <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden border border-outline-variant/10">
        <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-surface-container">
          <div>
            <h2 class="text-lg font-bold text-on-surface uppercase tracking-tight">Customer</h2>
            <p class="text-xs text-on-surface-variant/60 font-medium">Manage customer data and track subscription history.</p>
          </div>
          <div class="flex items-center gap-3">
            <div class="relative">
              <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant/40 text-lg" data-icon="search">search</span>
              <input
                v-model="search"
                type="text"
                placeholder="Search customers..."
                class="pl-10 pr-4 py-2 w-64 bg-surface-container-highest border-none rounded-lg app-text focus:ring-2 focus:ring-primary-container outline-none transition-all"
              />
            </div>
            <Link href="/customers/create" class="flex items-center gap-2 px-4 py-2 bg-primary-container text-on-primary-container rounded-lg font-black text-[10px] uppercase tracking-[0.2em] hover:bg-orange-500 transition-all shadow-sm">
              <span class="material-symbols-outlined text-sm" data-icon="add">add</span>
              Add New
            </Link>
          </div>
        </div>
        
        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="border-b border-surface-container">
                <th class="w-10 px-6 py-4"></th>
                <th class="px-6 py-4 app-text font-bold uppercase tracking-widest text-on-surface-variant/60">Customer Name</th>
                <th class="px-6 py-4 app-text font-bold uppercase tracking-widest text-on-surface-variant/60">Email</th>
                <th class="px-6 py-4 app-text font-bold uppercase tracking-widest text-on-surface-variant/60">Phone</th>
                <th class="px-6 py-4 app-text font-bold uppercase tracking-widest text-on-surface-variant/60">Address</th>
                <th class="px-6 py-4 app-text font-bold uppercase tracking-widest text-on-surface-variant/60 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-surface-container">
              <template v-if="customers.data.length">
                <template v-for="customer in customers.data" :key="customer.id">
                  <!-- Customer Row -->
                  <tr
                    class="parent-row group transition-colors duration-150 cursor-pointer"
                    :class="isRowActive(customer.id) ? 'active bg-surface-container-low' : 'hover:bg-surface-container-low'"
                    @click="toggleRow(customer.id)"
                  >
                    <td class="px-6 py-5">
                      <span
                        class="material-symbols-outlined transition-transform chevron-icon"
                        :class="isRowActive(customer.id) ? 'rotate-90 text-primary' : 'text-on-surface-variant/40'"
                        data-icon="chevron_right"
                      >
                        chevron_right
                      </span>
                    </td>
                    <td class="px-6 py-5">
                      <p class="text-on-surface leading-tight font-bold">{{ customer.name }}</p>
                    </td>
                    <td class="px-6 py-5">
                      <p class="text-on-surface leading-tight">{{ customer.email }}</p>
                    </td>
                    <td class="px-6 py-5">
                      <p class="text-on-surface leading-tight">{{ customer.phone || '—' }}</p>
                    </td>
                    <td class="px-6 py-5">
                      <p class="text-on-surface leading-tight">{{ customer.address || '—' }}</p>
                    </td>
                    <td class="px-6 py-5 text-right" @click.stop>
                      <div class="flex items-center justify-end gap-2">
                        <!-- <button class="p-2 hover:bg-surface-container-high rounded-lg transition-colors text-on-surface-variant" title="View Details">
                          <span class="material-symbols-outlined text-lg" data-icon="visibility">visibility</span>
                        </button> -->
                        <Link :href="`/customers/${customer.id}/edit`" class="p-2 hover:bg-surface-container-high rounded-lg transition-colors text-on-surface-variant" title="Edit User">
                          <span class="material-symbols-outlined text-lg" data-icon="edit">edit</span>
                        </Link>
                        <button class="p-2 hover:bg-error-container/20 rounded-lg transition-colors text-error" title="Delete User" @click="confirmDelete(customer)">
                          <span class="material-symbols-outlined text-lg" data-icon="delete">delete</span>
                        </button>
                      </div>
                    </td>
                  </tr>

                  <!-- Expanded Row - Properties -->
                  <tr
                    v-show="isRowActive(customer.id)"
                    class="expanded-row bg-surface-container-low"
                    :class="isRowActive(customer.id) ? 'active' : ''"
                  >
                    <td colspan="6" class="px-12 py-6">
                      <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 p-6 shadow-inner">
                        <h3 class="text-xs font-black uppercase tracking-[0.2em] text-on-surface-variant/60 mb-4 flex items-center gap-2">
                          <span class="material-symbols-outlined text-sm" data-icon="home_work">home_work</span>
                          Properties
                          <span class="text-[10px] font-bold text-on-surface-variant/40 normal-case tracking-normal">({{ customer.properties?.length || 0 }})</span>
                        </h3>

                        <div v-if="customer.properties?.length" class="flex flex-col gap-3">
                          <div
                            v-for="property in customer.properties"
                            :key="property.id"
                            class="p-4 rounded-lg bg-surface-container border border-outline-variant/10 flex items-center justify-between group/item hover:border-primary/30 transition-colors"
                          >
                            <div class="flex items-center gap-4">
                              <div class="w-10 h-10 rounded bg-orange-50 flex items-center justify-center">
                                <span class="material-symbols-outlined text-orange-600 text-lg" data-icon="location_on">location_on</span>
                              </div>
                              <div>
                                <p class="text-sm font-bold text-on-surface">{{ property.name || 'Unnamed Property' }}</p>
                                <p class="text-[10px] font-black text-on-surface-variant/40 uppercase tracking-widest leading-none mt-1">
                                  {{ [property.address, property.suburb, property.state, property.postcode].filter(Boolean).join(', ') || 'No address' }}
                                </p>
                              </div>
                            </div>
                            <div class="flex items-center gap-3">
                              <span v-if="property.property_type" class="px-3 py-1 bg-orange-50 text-orange-700 text-[9px] font-black rounded-full border border-orange-200 uppercase tracking-widest">
                                {{ property.property_type }}
                              </span>
                              <div v-if="property.contact_name" class="text-right hidden md:block">
                                <p class="text-[10px] font-bold text-on-surface-variant">{{ property.contact_name }}</p>
                                <p class="text-[10px] text-on-surface-variant/50">{{ property.contact_phone }}</p>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div v-else class="text-center py-6">
                          <p class="text-sm font-medium text-on-surface-variant/50 italic">No properties associated with this customer.</p>
                        </div>
                      </div>
                    </td>
                  </tr>
                </template>
              </template>
              <tr v-else>
                <td colspan="6" class="px-6 py-12 text-center text-on-surface-variant/60">
                  No customers found.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <Pagination :paginator="customers" noun="customers" />
      </div>
    </div>
        <Footer />

    <ConfirmModal
        :show="showDeleteModal"
        title="Delete Customer"
        :message="`Are you sure you want to delete ${customerToDelete?.name}? This action cannot be undone.`"
        :processing="deleting"
        @confirm="deleteCustomer"
        @cancel="cancelDelete"
    />
    </AppShell>
</template>
<script setup>
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppShell from '@/Components/Layout/AppShell.vue';
import Footer from '@/Components/Layout/Footer.vue';

const isSampleModalOpen = ref(false);
const activeRowId = ref(null);

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
</script>

<template>
    <Head title="Asbestos Audits | title" />
    <AppShell>
    <div class="pt-24 px-8 pb-12">
      <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden border border-outline-variant/10">
        <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-surface-container">
          <div>
            <h2 class="text-lg font-bold text-on-surface uppercase tracking-tight">Audit Register</h2>
            <p class="text-xs text-on-surface-variant/60 font-medium">Manage customer data and track job order history.</p>
          </div>
          <div class="flex items-center gap-2">
            <button class="flex items-center gap-2 px-3 py-2 bg-surface-container-high text-on-surface-variant rounded-lg font-bold text-[10px] uppercase tracking-[0.2em] hover:bg-surface-container-highest transition-all">
              <span class="material-symbols-outlined text-sm" data-icon="filter_list">filter_list</span>
              Filters
            </button>
            <a href="/asbestos-audits/create" class="flex items-center gap-2 px-4 py-2 bg-primary-container text-on-primary-container rounded-lg font-black text-[10px] uppercase tracking-[0.2em] hover:bg-orange-500 transition-all shadow-sm">
              <span class="material-symbols-outlined text-sm" data-icon="add">add</span>
              Add New
            </a>
          </div>
        </div>
        
        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="border-b border-surface-container">
                <th class="w-10 px-6 py-4"></th>
                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant/60">job id</th>
                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant/60">audit date</th>
                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant/60">location</th>
                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant/60">status</th>
                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant/60 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-surface-container">
              <!-- Row 1 -->
              <tr
                class="parent-row group transition-colors duration-150"
                :class="isRowActive('jobs-1') ? 'active bg-surface-container-low' : 'hover:bg-surface-container-low'"
                @click="toggleRow('jobs-1')"
              >
                <td class="px-6 py-5">
                  <span
                    class="material-symbols-outlined transition-transform chevron-icon"
                    :class="isRowActive('jobs-1') ? 'rotate-90 text-primary' : 'text-on-surface-variant/40'"
                    data-icon="chevron_right"
                  >
                    chevron_right
                  </span>
                </td>
                <td class="px-6 py-5">
                  <div class="flex items-center gap-4">
                    <div>
                      <p class="font-bold text-on-surface leading-tight">Elena Vance</p>
                      <p class="text-xs text-on-surface-variant/70">elena.v@admincanvas.io</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-5">
                  <span class="px-3 py-1 bg-blue-50 text-blue-700 text-xs font-bold rounded-full uppercase tracking-tighter">Enterprise</span>
                </td>
                <td class="px-6 py-5">
                  <div class="flex items-center gap-1.5 text-green-600 font-bold text-xs uppercase">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                    Active
                  </div>
                </td>
                <td class="px-6 py-5">
                  <div class="text-xs">
                    <p class="font-semibold text-on-surface">Oct 24, 2023</p>
                    <p class="text-on-surface-variant/60">09:42 AM</p>
                  </div>
                </td>
                <td class="px-6 py-5 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <button class="p-2 hover:bg-surface-container-high rounded-lg transition-colors text-on-surface-variant" title="View Details">
                      <span class="material-symbols-outlined text-lg" data-icon="visibility">visibility</span>
                    </button>
                    <a href="/asbestos-audits/edit">
                      <button class="cursor-pointer p-2 hover:bg-surface-container-high rounded-lg transition-colors text-on-surface-variant" title="Edit User">
                        <span class="material-symbols-outlined text-lg" data-icon="edit">edit</span>
                      </button>
                    </a>
                    <button class="p-2 hover:bg-error-container/20 rounded-lg transition-colors text-error" title="Delete User">
                      <span class="material-symbols-outlined text-lg" data-icon="delete">delete</span>
                    </button>
                  </div>
                </td>
              </tr>
              <!-- Expanded Row 1 -->
              <tr
                v-show="isRowActive('jobs-1')"
                class="expanded-row bg-surface-container-low"
                :class="isRowActive('jobs-1') ? 'active' : ''"
                id="jobs-1"
              >
                <td colspan="6" class="px-12 py-6">
                  <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 p-6 shadow-inner">
                    <h3 class="text-xs font-black uppercase tracking-[0.2em] text-on-surface-variant/60 mb-4 flex items-center gap-2">
                      <span class="material-symbols-outlined text-sm" data-icon="list_alt">list_alt</span>
                      Job Order History
                    </h3>
                    <div class="flex flex-col gap-3">
                      <div class="p-4 rounded-lg bg-surface-container border border-outline-variant/10 flex items-center justify-between group/item hover:border-primary/30 transition-colors">
                        <div class="flex items-center gap-4">
                          <div class="w-10 h-10 rounded bg-green-50 flex items-center justify-center">
                            <span class="material-symbols-outlined text-green-600 text-lg" data-icon="task_alt">task_alt</span>
                          </div>
                          <div>
                            <p class="text-sm font-bold text-on-surface">Asbestos Survey - Site A</p>
                            <p class="text-[10px] font-black text-on-surface-variant/40 uppercase tracking-widest leading-none mt-1">#JO-8821 • Oct 12, 2023</p>
                          </div>
                        </div>
                        <span class="px-3 py-1 bg-green-50 text-green-700 text-[9px] font-black rounded-full border border-green-200 uppercase tracking-widest">Completed</span>
                      </div>

                      <div class="p-4 rounded-lg bg-surface-container border border-outline-variant/10 flex items-center justify-between group/item hover:border-primary/30 transition-colors">
                        <div class="flex items-center gap-4">
                          <div class="w-10 h-10 rounded bg-orange-50 flex items-center justify-center">
                            <span class="material-symbols-outlined text-orange-600 text-lg" data-icon="pending">pending</span>
                          </div>
                          <div>
                            <p class="text-sm font-bold text-on-surface">Material Analysis</p>
                            <p class="text-[10px] font-black text-on-surface-variant/40 uppercase tracking-widest leading-none mt-1">#JO-8845 • Oct 22, 2023</p>
                          </div>
                        </div>
                        <span class="px-3 py-1 bg-orange-50 text-orange-700 text-[9px] font-black rounded-full border border-orange-200 uppercase tracking-widest">In Review</span>
                      </div>

                      <div class="p-4 rounded-lg border border-dashed border-outline-variant/30 flex items-center justify-center cursor-pointer hover:bg-surface-container/50 hover:border-primary/50 transition-all group/add">
                        <div class="flex items-center gap-2">
                          <span class="material-symbols-outlined text-primary group-hover/add:scale-110 transition-transform" data-icon="add_circle">add_circle</span>
                          <p class="text-[10px] font-black uppercase tracking-[0.2em] text-primary">Initialize New Job Order</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>

              <!-- Row 2 -->
              <tr
                class="parent-row group transition-colors duration-150"
                :class="isRowActive('jobs-2') ? 'active bg-surface-container-low' : 'hover:bg-surface-container-low'"
                @click="toggleRow('jobs-2')"
              >
                <td class="px-6 py-5">
                  <span
                    class="material-symbols-outlined transition-transform chevron-icon"
                    :class="isRowActive('jobs-2') ? 'rotate-90 text-primary' : 'text-on-surface-variant/40'"
                    data-icon="chevron_right"
                  >
                    chevron_right
                  </span>
                </td>
                <td class="px-6 py-5">
                  <div class="flex items-center gap-4">
                    <div>
                      <p class="font-bold text-on-surface leading-tight">Elena Vance</p>
                      <p class="text-xs text-on-surface-variant/70">elena.v@admincanvas.io</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-5">
                  <span class="px-3 py-1 bg-purple-50 text-purple-700 text-xs font-bold rounded-full uppercase tracking-tighter">Pro Plan</span>
                </td>
                <td class="px-6 py-5">
                  <div class="flex items-center gap-1.5 text-orange-600 font-bold text-xs uppercase">
                    <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                    Pending
                  </div>
                </td>
                <td class="px-6 py-5">
                  <div class="text-xs">
                    <p class="font-semibold text-on-surface">Oct 23, 2023</p>
                    <p class="text-on-surface-variant/60">11:15 PM</p>
                  </div>
                </td>
                <td class="px-6 py-5 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <button class="p-2 hover:bg-surface-container-high rounded-lg transition-colors text-on-surface-variant" title="View Details">
                      <span class="material-symbols-outlined text-lg" data-icon="visibility">visibility</span>
                    </button>
                    <button class="p-2 hover:bg-surface-container-high rounded-lg transition-colors text-on-surface-variant" title="Edit User">
                      <span class="material-symbols-outlined text-lg" data-icon="edit">edit</span>
                    </button>
                    <button class="p-2 hover:bg-error-container/20 rounded-lg transition-colors text-error" title="Delete User">
                      <span class="material-symbols-outlined text-lg" data-icon="delete">delete</span>
                    </button>
                  </div>
                </td>
              </tr>
              <!-- Expanded Row 2 -->
              <tr
                v-show="isRowActive('jobs-2')"
                class="expanded-row bg-surface-container-low"
                :class="isRowActive('jobs-2') ? 'active' : ''"
                id="jobs-2"
              >
                <td colspan="6" class="px-12 py-6">
                  <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 p-6 shadow-inner">
                    <p class="text-sm font-medium text-on-surface-variant text-center py-4 italic">No active job orders found for this customer.</p>
                  </div>
                </td>
              </tr>

              <!-- Row 3 -->
              <tr
                class="parent-row group transition-colors duration-150"
                :class="isRowActive('jobs-3') ? 'active bg-surface-container-low' : 'hover:bg-surface-container-low'"
                @click="toggleRow('jobs-3')"
              >
                <td class="px-6 py-5">
                  <span
                    class="material-symbols-outlined transition-transform chevron-icon"
                    :class="isRowActive('jobs-3') ? 'rotate-90 text-primary' : 'text-on-surface-variant/40'"
                    data-icon="chevron_right"
                  >
                    chevron_right
                  </span>
                </td>
                <td class="px-6 py-5">
                  <div class="flex items-center gap-4">
                    <div>
                      <p class="font-bold text-on-surface leading-tight">Sarah Chen</p>
                      <p class="text-xs text-on-surface-variant/70">schen@admincanvas.io</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-5">
                  <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs font-bold rounded-full uppercase tracking-tighter">Free</span>
                </td>
                <td class="px-6 py-5">
                  <div class="flex items-center gap-1.5 text-on-surface-variant/50 font-bold text-xs uppercase">
                    <span class="w-2 h-2 rounded-full bg-gray-300"></span>
                    Inactive
                  </div>
                </td>
                <td class="px-6 py-5">
                  <div class="text-xs">
                    <p class="font-semibold text-on-surface">Oct 20, 2023</p>
                    <p class="text-on-surface-variant/60">04:30 PM</p>
                  </div>
                </td>
                <td class="px-6 py-5 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <button class="p-2 hover:bg-surface-container-high rounded-lg transition-colors text-on-surface-variant" title="View Details">
                      <span class="material-symbols-outlined text-lg" data-icon="visibility">visibility</span>
                    </button>
                    <button class="p-2 hover:bg-surface-container-high rounded-lg transition-colors text-on-surface-variant" title="Edit User">
                      <span class="material-symbols-outlined text-lg" data-icon="edit">edit</span>
                    </button>
                    <button class="p-2 hover:bg-error-container/20 rounded-lg transition-colors text-error" title="Delete User">
                      <span class="material-symbols-outlined text-lg" data-icon="delete">delete</span>
                    </button>
                  </div>
                </td>
              </tr>
              <!-- Expanded Row 3 -->
              <tr
                v-show="isRowActive('jobs-3')"
                class="expanded-row bg-surface-container-low"
                :class="isRowActive('jobs-3') ? 'active' : ''"
                id="jobs-3"
              >
                <td colspan="6" class="px-12 py-6">
                  <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 p-6 shadow-inner">
                    <p class="text-sm font-medium text-on-surface-variant text-center py-4 italic">Customer account is currently inactive.</p>
                  </div>
                </td>
              </tr>

              <!-- Row 4 -->
              <tr
                class="parent-row group transition-colors duration-150"
                :class="isRowActive('jobs-4') ? 'active bg-surface-container-low' : 'hover:bg-surface-container-low'"
                @click="toggleRow('jobs-4')"
              >
                <td class="px-6 py-5">
                  <span
                    class="material-symbols-outlined transition-transform chevron-icon"
                    :class="isRowActive('jobs-4') ? 'rotate-90 text-primary' : 'text-on-surface-variant/40'"
                    data-icon="chevron_right"
                  >
                    chevron_right
                  </span>
                </td>
                <td class="px-6 py-5">
                  <div class="flex items-center gap-4">
                    <div>
                      <p class="font-bold text-on-surface leading-tight">Marcus Wright</p>
                      <p class="text-xs text-on-surface-variant/70">m.wright@admincanvas.io</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-5">
                  <span class="px-3 py-1 bg-purple-50 text-purple-700 text-xs font-bold rounded-full uppercase tracking-tighter">Pro Plan</span>
                </td>
                <td class="px-6 py-5">
                  <div class="flex items-center gap-1.5 text-green-600 font-bold text-xs uppercase">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                    Active
                  </div>
                </td>
                <td class="px-6 py-5">
                  <div class="text-xs">
                    <p class="font-semibold text-on-surface">Oct 24, 2023</p>
                    <p class="text-on-surface-variant/60">01:55 PM</p>
                  </div>
                </td>
                <td class="px-6 py-5 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <button class="p-2 hover:bg-surface-container-high rounded-lg transition-colors text-on-surface-variant" title="View Details">
                      <span class="material-symbols-outlined text-lg" data-icon="visibility">visibility</span>
                    </button>
                    <button class="p-2 hover:bg-surface-container-high rounded-lg transition-colors text-on-surface-variant" title="Edit User">
                      <span class="material-symbols-outlined text-lg" data-icon="edit">edit</span>
                    </button>
                    <button class="p-2 hover:bg-error-container/20 rounded-lg transition-colors text-error" title="Delete User">
                      <span class="material-symbols-outlined text-lg" data-icon="delete">delete</span>
                    </button>
                  </div>
                </td>
              </tr>
              <!-- Expanded Row 4 -->
              <tr
                v-show="isRowActive('jobs-4')"
                class="expanded-row bg-surface-container-low"
                :class="isRowActive('jobs-4') ? 'active' : ''"
                id="jobs-4"
              >
                <td colspan="6" class="px-12 py-6">
                  <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 p-6 shadow-inner text-center">
                    <p class="text-sm font-medium text-on-surface-variant py-4 italic">Fetching latest job orders...</p>
                  </div>
                </td>
              </tr>

              <!-- Row 5 -->
              <tr
                class="parent-row group transition-colors duration-150"
                :class="isRowActive('jobs-5') ? 'active bg-surface-container-low' : 'hover:bg-surface-container-low'"
                @click="toggleRow('jobs-5')"
              >
                <td class="px-6 py-5">
                  <span
                    class="material-symbols-outlined transition-transform chevron-icon"
                    :class="isRowActive('jobs-5') ? 'rotate-90 text-primary' : 'text-on-surface-variant/40'"
                    data-icon="chevron_right"
                  >
                    chevron_right
                  </span>
                </td>
                <td class="px-6 py-5">
                  <div class="flex items-center gap-4">
                    <div>
                      <p class="font-bold text-on-surface leading-tight">Isabella Rossi</p>
                      <p class="text-xs text-on-surface-variant/70">i.rossi@admincanvas.io</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-5">
                  <span class="px-3 py-1 bg-blue-50 text-blue-700 text-xs font-bold rounded-full uppercase tracking-tighter">Enterprise</span>
                </td>
                <td class="px-6 py-5">
                  <div class="flex items-center gap-1.5 text-green-600 font-bold text-xs uppercase">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                    Active
                  </div>
                </td>
                <td class="px-6 py-5">
                  <div class="text-xs">
                    <p class="font-semibold text-on-surface">Just now</p>
                    <p class="text-on-surface-variant/60">Active Session</p>
                  </div>
                </td>
                <td class="px-6 py-5 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <button class="p-2 hover:bg-surface-container-high rounded-lg transition-colors text-on-surface-variant" title="View Details">
                      <span class="material-symbols-outlined text-lg" data-icon="visibility">visibility</span>
                    </button>
                    <button class="p-2 hover:bg-surface-container-high rounded-lg transition-colors text-on-surface-variant" title="Edit User">
                      <span class="material-symbols-outlined text-lg" data-icon="edit">edit</span>
                    </button>
                    <button class="p-2 hover:bg-error-container/20 rounded-lg transition-colors text-error" title="Delete User">
                      <span class="material-symbols-outlined text-lg" data-icon="delete">delete</span>
                    </button>
                  </div>
                </td>
              </tr>
              <!-- Expanded Row 5 -->
              <tr
                v-show="isRowActive('jobs-5')"
                class="expanded-row bg-surface-container-low"
                :class="isRowActive('jobs-5') ? 'active' : ''"
                id="jobs-5"
              >
                <td colspan="6" class="px-12 py-6">
                  <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/20 p-6 shadow-inner">
                    <div class="flex justify-between items-center mb-4">
                       <h3 class="text-xs font-black uppercase tracking-[0.2em] text-on-surface-variant/60 flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm" data-icon="pending_actions">pending_actions</span>
                        Open Job Orders
                      </h3>
                      <button class="text-[10px] font-bold text-primary uppercase tracking-widest hover:underline">View All</button>
                    </div>
                    <div class="p-4 rounded-lg bg-red-50 border border-red-100 flex items-center justify-between">
                      <div>
                        <p class="text-xs font-bold text-red-900 leading-none">URGENT: Sample Batch #772</p>
                        <p class="text-[10px] text-red-700/70 mt-1 uppercase font-black">Awaiting Lab Approval</p>
                      </div>
                      <button class="px-3 py-1.5 bg-red-600 text-white text-[10px] font-black rounded uppercase shadow-sm hover:bg-red-700 transition-all">Prioritize</button>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="p-6 bg-surface-container-low/30 border-t border-surface-container flex flex-col sm:flex-row items-center justify-between gap-4">
          <div class="flex items-center gap-4">
            <p class="text-sm text-on-surface-variant">Showing <span class="font-bold text-on-surface">1-5</span> of <span class="font-bold text-on-surface">1,284</span> customers</p>
            <div class="h-4 w-[1px] bg-outline-variant/30 mx-2 hidden sm:block"></div>
            <div class="flex items-center gap-2">
              <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant/60" for="rows-per-page">Rows:</label>
              <select class="bg-transparent border-none text-sm font-bold text-on-surface focus:ring-0 cursor-pointer" id="rows-per-page">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
              </select>
            </div>
          </div>
          
          <div class="flex items-center gap-1">
            <button class="p-2 rounded-lg hover:bg-surface-container-high text-on-surface-variant disabled:opacity-30 group" disabled="" title="Previous Page">
              <span class="material-symbols-outlined transition-transform group-active:-translate-x-1" data-icon="chevron_left">chevron_left</span>
            </button>
            <div class="flex items-center gap-1 mx-2">
              <button class="w-9 h-9 flex items-center justify-center rounded-lg bg-primary-container text-on-primary-container font-black text-sm shadow-sm">1</button>
              <button class="w-9 h-9 flex items-center justify-center rounded-lg hover:bg-surface-container-high text-on-surface-variant font-bold text-sm transition-colors">2</button>
              <button class="w-9 h-9 flex items-center justify-center rounded-lg hover:bg-surface-container-high text-on-surface-variant font-bold text-sm transition-colors">3</button>
              <span class="px-2 text-on-surface-variant/40 font-bold self-end mb-2">...</span>
              <button class="w-9 h-9 flex items-center justify-center rounded-lg hover:bg-surface-container-high text-on-surface-variant font-bold text-sm transition-colors">257</button>
            </div>
            <button class="p-2 rounded-lg hover:bg-surface-container-high text-on-surface-variant group" title="Next Page">
              <span class="material-symbols-outlined transition-transform group-active:translate-x-1" data-icon="chevron_right">chevron_right</span>
            </button>
          </div>
        </div>
      </div>
    </div>
        <Footer />
    </AppShell>
</template>
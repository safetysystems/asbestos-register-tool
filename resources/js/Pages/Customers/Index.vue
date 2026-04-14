<script setup>
import { Head } from '@inertiajs/vue3';
import AppShell from '@/Components/Layout/AppShell.vue';
import Footer from '@/Components/Layout/Footer.vue';
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
          <div class="flex items-center gap-2">
            <button class="flex items-center gap-2 px-3 py-2 bg-surface-container-high text-on-surface-variant rounded-lg font-bold text-[10px] uppercase tracking-[0.2em] hover:bg-surface-container-highest transition-all">
              <span class="material-symbols-outlined text-sm" data-icon="filter_list">filter_list</span>
              Filters
            </button>
            <a href="/customers/create" class="flex items-center gap-2 px-4 py-2 bg-primary-container text-on-primary-container rounded-lg font-black text-[10px] uppercase tracking-[0.2em] hover:bg-orange-500 transition-all shadow-sm">
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
                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant/60">Customer Details</th>
                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant/60">Subscription</th>
                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant/60">Account Status</th>
                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant/60">Last Activity</th>
                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant/60 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-surface-container">
                <tr class="parent-row group hover:bg-surface-container-low transition-colors duration-150">
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
                    <button class="p-2 hover:bg-surface-container-high rounded-lg transition-colors text-on-surface-variant" title="Edit User">
                      <span class="material-symbols-outlined text-lg" data-icon="edit">edit</span>
                    </button>
                    <button class="p-2 hover:bg-error-container/20 rounded-lg transition-colors text-error" title="Delete User">
                      <span class="material-symbols-outlined text-lg" data-icon="delete">delete</span>
                    </button>
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
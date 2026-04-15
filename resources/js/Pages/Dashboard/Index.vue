<script setup>
import { Head } from "@inertiajs/vue3";
import AppShell from "@/Components/Layout/AppShell.vue";
import Footer from "@/Components/Layout/Footer.vue";

const summaryCards = [
    {
        label: "Total Customers",
        value: "1,284",
        delta: "+8.4% this month",
        icon: "group",
    },
    {
        label: "Active Audits",
        value: "42",
        delta: "6 due this week",
        icon: "fact_check",
    },
    {
        label: "Samples Awaiting Review",
        value: "19",
        delta: "3 marked urgent",
        icon: "science",
    },
    {
        label: "Completion Rate",
        value: "92%",
        delta: "+2.1% vs last month",
        icon: "query_stats",
    },
];

const recentCustomers = [
    {
        name: "Elena Vance",
        email: "elena.v@admincanvas.io",
        plan: "Enterprise",
        status: "Active",
        lastActivity: "Oct 24, 2023",
    },
    {
        name: "Marcus Wright",
        email: "m.wright@admincanvas.io",
        plan: "Pro Plan",
        status: "Pending",
        lastActivity: "Oct 23, 2023",
    },
    {
        name: "Sarah Chen",
        email: "schen@admincanvas.io",
        plan: "Free",
        status: "Inactive",
        lastActivity: "Oct 20, 2023",
    },
];

const recentAudits = [
    {
        jobId: "JO-8821",
        date: "Oct 12, 2023",
        location: "Site A - Boiler Room",
        status: "Completed",
    },
    {
        jobId: "JO-8845",
        date: "Oct 22, 2023",
        location: "Site B - Service Corridor",
        status: "In Review",
    },
    {
        jobId: "JO-8860",
        date: "Oct 24, 2023",
        location: "Site C - Roof Plant",
        status: "Scheduled",
    },
];
</script>

<template>
    <Head title="Dashboard | title" />
    <AppShell>
        <div class="pt-24 px-8 pb-12 space-y-6">
            <section
                class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden border border-outline-variant/10"
            >
                <div
                    class="px-6 py-5 border-b border-surface-container flex flex-col sm:flex-row sm:items-center justify-between gap-4"
                >
                    <div>
                        <h2
                            class="text-lg font-black text-on-surface uppercase tracking-tight"
                        >
                            Dashboard
                        </h2>
                        <p
                            class="text-xs text-on-surface-variant/60 font-medium"
                        >
                            Operational overview across customers and asbestos
                            audits.
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a
                            href="/customers"
                            class="flex items-center gap-2 px-4 py-2 bg-surface-container-high text-on-surface-variant rounded-lg font-bold app-text uppercase tracking-[0.2em] hover:bg-surface-container-highest transition-all"
                        >
                            <span
                                class="material-symbols-outlined text-sm"
                                data-icon="group"
                                >group</span
                            >
                            Customers
                        </a>
                        <a
                            href="/asbestos-audits"
                            class="flex items-center gap-2 px-4 py-2 bg-primary-container text-on-primary-container rounded-lg font-black app-text uppercase tracking-[0.2em] hover:bg-orange-500 transition-all shadow-sm"
                        >
                            <span
                                class="material-symbols-outlined text-sm"
                                data-icon="fact_check"
                                >fact_check</span
                            >
                            Audits
                        </a>
                    </div>
                </div>

                <div
                    class="p-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4"
                >
                    <article
                        v-for="card in summaryCards"
                        :key="card.label"
                        class="rounded-xl border border-outline-variant/20 bg-surface-container-low p-4"
                    >
                        <div class="flex items-center justify-between mb-3">
                            <p
                                class="app-text font-bold uppercase tracking-[0.18em] text-on-surface-variant/70"
                            >
                                {{ card.label }}
                            </p>
                            <span
                                class="material-symbols-outlined text-on-surface-variant/60"
                                :data-icon="card.icon"
                                >{{ card.icon }}</span
                            >
                        </div>
                        <p
                            class="text-3xl font-black text-on-surface leading-none mb-2"
                        >
                            {{ card.value }}
                        </p>
                        <p
                            class="app-text font-semibold text-on-surface-variant/70"
                        >
                            {{ card.delta }}
                        </p>
                    </article>
                </div>
            </section>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <section
                    class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden border border-outline-variant/10"
                >
                    <div class="px-6 py-4 border-b border-surface-container">
                        <h3
                            class="text-sm font-black uppercase tracking-[0.16em] text-on-surface"
                        >
                            Recent Customers
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-surface-container">
                                    <th
                                        class="px-6 py-3 app-text font-bold uppercase tracking-widest text-on-surface-variant/60"
                                    >
                                        Customer
                                    </th>
                                    <th
                                        class="px-6 py-3 app-text font-bold uppercase tracking-widest text-on-surface-variant/60"
                                    >
                                        Plan
                                    </th>
                                    <th
                                        class="px-6 py-3 app-text font-bold uppercase tracking-widest text-on-surface-variant/60"
                                    >
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 app-text font-bold uppercase tracking-widest text-on-surface-variant/60"
                                    >
                                        Last Activity
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-container">
                                <tr
                                    v-for="customer in recentCustomers"
                                    :key="customer.email"
                                    class="hover:bg-surface-container-low transition-colors"
                                >
                                    <td class="px-6 py-4">
                                        <p
                                            class="text-sm font-bold text-on-surface leading-tight"
                                        >
                                            {{ customer.name }}
                                        </p>
                                        <p
                                            class="text-xs text-on-surface-variant/70"
                                        >
                                            {{ customer.email }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-3 py-1 bg-blue-50 text-blue-700 app-text font-bold rounded-full uppercase tracking-tighter"
                                            >{{ customer.plan }}</span
                                        >
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-3 py-1 app-text font-bold rounded-full uppercase tracking-tighter"
                                            :class="
                                                customer.status === 'Active'
                                                    ? 'bg-green-50 text-green-700'
                                                    : customer.status ===
                                                        'Pending'
                                                      ? 'bg-orange-50 text-orange-700'
                                                      : 'bg-gray-100 text-gray-700'
                                            "
                                        >
                                            {{ customer.status }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 text-xs font-semibold text-on-surface"
                                    >
                                        {{ customer.lastActivity }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section
                    class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden border border-outline-variant/10"
                >
                    <div class="px-6 py-4 border-b border-surface-container">
                        <h3
                            class="text-sm font-black uppercase tracking-[0.16em] text-on-surface"
                        >
                            Recent Asbestos Audits
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-surface-container">
                                    <th
                                        class="px-6 py-3 app-text font-bold uppercase tracking-widest text-on-surface-variant/60"
                                    >
                                        Job ID
                                    </th>
                                    <th
                                        class="px-6 py-3 app-text font-bold uppercase tracking-widest text-on-surface-variant/60"
                                    >
                                        Date
                                    </th>
                                    <th
                                        class="px-6 py-3 app-text font-bold uppercase tracking-widest text-on-surface-variant/60"
                                    >
                                        Location
                                    </th>
                                    <th
                                        class="px-6 py-3 app-text font-bold uppercase tracking-widest text-on-surface-variant/60"
                                    >
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-container">
                                <tr
                                    v-for="audit in recentAudits"
                                    :key="audit.jobId"
                                    class="hover:bg-surface-container-low transition-colors"
                                >
                                    <td
                                        class="px-6 py-4 text-sm font-black text-on-surface"
                                    >
                                        {{ audit.jobId }}
                                    </td>
                                    <td
                                        class="px-6 py-4 text-xs font-semibold text-on-surface"
                                    >
                                        {{ audit.date }}
                                    </td>
                                    <td
                                        class="px-6 py-4 text-xs text-on-surface-variant/80"
                                    >
                                        {{ audit.location }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-3 py-1 app-text font-bold rounded-full uppercase tracking-tighter"
                                            :class="
                                                audit.status === 'Completed'
                                                    ? 'bg-green-50 text-green-700'
                                                    : audit.status ===
                                                        'In Review'
                                                      ? 'bg-orange-50 text-orange-700'
                                                      : 'bg-blue-50 text-blue-700'
                                            "
                                        >
                                            {{ audit.status }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
        <Footer />
    </AppShell>
</template>

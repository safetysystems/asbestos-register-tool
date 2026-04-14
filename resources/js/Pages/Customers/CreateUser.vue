<script setup>
import { Head } from "@inertiajs/vue3";
import { useForm } from "@inertiajs/vue3";
import { computed, reactive } from "vue";
import AppShell from "@/Components/Layout/AppShell.vue";
import Footer from "@/Components/Layout/Footer.vue";
import MapPicker from "@/Components/Common/MapPicker.vue";
import Button from "@/Components/Common/Button.vue";

const props = defineProps({
    customer: {
        type: Object,
        default: null,
    },
});

const isEditing = computed(() => !!props.customer);

const form = useForm({
    name: props.customer?.name ?? "",
    email: props.customer?.email ?? "",
    phone: props.customer?.phone ?? "",
    address: props.customer?.address ?? "",
    suburb: props.customer?.suburb ?? "",
    state: props.customer?.state ?? "",
    postcode: props.customer?.postcode ?? "",
    properties:
        props.customer?.properties?.map((p) => ({
            ...p,
            _location: { lat: p.latitude || null, lng: p.longitude || null },
        })) ?? [],
});

const useCustomerContact = reactive({});

function addProperty() {
    const index = form.properties.length;
    form.properties.push({
        id: null,
        name: "",
        address: "",
        suburb: "",
        state: "",
        postcode: "",
        property_type: "",
        contact_name: "",
        contact_phone: "",
        contact_email: "",
        latitude: null,
        longitude: null,
        _location: { lat: null, lng: null },
    });
    useCustomerContact[index] = false;
}

function removeProperty(index) {
    form.properties.splice(index, 1);
    delete useCustomerContact[index];
    // Re-index the flags
    const updated = {};
    form.properties.forEach((_, i) => {
        updated[i] = useCustomerContact[i >= index ? i + 1 : i] ?? false;
    });
    Object.keys(useCustomerContact).forEach(
        (k) => delete useCustomerContact[k],
    );
    Object.assign(useCustomerContact, updated);
}

function toggleCustomerContact(index) {
    if (useCustomerContact[index]) {
        form.properties[index].contact_name = form.name;
        form.properties[index].contact_phone = form.phone;
        form.properties[index].contact_email = form.email;
    } else {
        form.properties[index].contact_name = "";
        form.properties[index].contact_phone = "";
        form.properties[index].contact_email = "";
    }
}

function submit() {
    if (isEditing.value) {
        form.put(`/customers/${props.customer.id}`);
    } else {
        form.post("/customers", {
            onSuccess: () => form.reset(),
        });
    }
}
</script>

<template>
    <Head :title="isEditing ? 'Edit Customer' : 'Create Customer'" />
    <AppShell>
        <div class="flex-1 pt-24 px-4 md:px-8 pb-12">
            <div
                class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/10 flex flex-col w-full"
            >
                <div
                    class="px-4 md:px-8 py-5 border-b border-surface-container flex justify-between items-center bg-[#fdfdfd] rounded-t-xl"
                >
                    <h3
                        class="font-bold text-on-surface tracking-tight uppercase app-text-medium"
                    >
                        {{
                            isEditing ? "Edit Customer" : "Create New Customer"
                        }}
                    </h3>
                    <Button
                        :disabled="form.processing"
                        @click="submit"
                    >
                        {{
                            form.processing
                                ? "Saving..."
                                : isEditing
                                  ? "Update"
                                  : "Save"
                        }}
                    </Button>
                </div>

                <div class="p-4 md:p-10">
                    <div
                        v-if="form.errors.error"
                        class="mb-6 p-4 bg-error-container text-error rounded-lg text-sm font-semibold flex items-center gap-2"
                    >
                        <span
                            class="material-symbols-outlined text-lg"
                            data-icon="error"
                            >error</span
                        >
                        {{ form.errors.error }}
                    </div>
                    <form @submit.prevent="submit">
                        <!-- Basic Information -->
                        <div class="mb-8">
                            <h4
                                class="app-text-medium font-bold tracking-[0.2em] text-on-surface-variant/50 uppercase mb-5"
                            >
                                Basic Information
                            </h4>
                            <div
                                class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-3 gap-x-8 gap-y-6"
                            >
                                <div class="space-y-2">
                                    <label
                                        class="app-text font-bold tracking-widest text-on-surface-variant uppercase ml-1"
                                        >Full Name</label
                                    >
                                    <input
                                        v-model="form.name"
                                        class="w-full bg-surface-container-highest border-none rounded-lg text-sm px-4 py-3 focus:ring-2 focus:ring-primary-container outline-none transition-all"
                                        placeholder="e.g. Alex Harrison"
                                        type="text"
                                    />
                                    <p
                                        v-if="form.errors.name"
                                        class="text-xs text-error mt-1"
                                    >
                                        {{ form.errors.name }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <label
                                        class="app-text font-bold tracking-widest text-on-surface-variant uppercase ml-1"
                                        >Email Address</label
                                    >
                                    <input
                                        v-model="form.email"
                                        class="w-full bg-surface-container-highest border-none rounded-lg text-sm px-4 py-3 focus:ring-2 focus:ring-primary-container outline-none transition-all"
                                        placeholder="alex@ambergrid.com"
                                        type="email"
                                    />
                                    <p
                                        v-if="form.errors.email"
                                        class="text-xs text-error mt-1"
                                    >
                                        {{ form.errors.email }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <label
                                        class="app-text font-bold tracking-widest text-on-surface-variant uppercase ml-1"
                                        >Phone Number</label
                                    >
                                    <input
                                        v-model="form.phone"
                                        class="w-full bg-surface-container-highest border-none rounded-lg text-sm px-4 py-3 focus:ring-2 focus:ring-primary-container outline-none transition-all"
                                        placeholder="e.g. +1 (555) 123-4567"
                                        type="tel"
                                    />
                                    <p
                                        v-if="form.errors.phone"
                                        class="text-xs text-error mt-1"
                                    >
                                        {{ form.errors.phone }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <label
                                        class="app-text font-bold tracking-widest text-on-surface-variant uppercase ml-1"
                                        >Suburb</label
                                    >
                                    <input
                                        v-model="form.suburb"
                                        class="w-full bg-surface-container-highest border-none rounded-lg text-sm px-4 py-3 focus:ring-2 focus:ring-primary-container outline-none transition-all"
                                        placeholder="e.g. Sydney"
                                        type="text"
                                    />
                                    <p
                                        v-if="form.errors.suburb"
                                        class="text-xs text-error mt-1"
                                    >
                                        {{ form.errors.suburb }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <label
                                        class="app-text font-bold tracking-widest text-on-surface-variant uppercase ml-1"
                                        >State</label
                                    >
                                    <input
                                        v-model="form.state"
                                        class="w-full bg-surface-container-highest border-none rounded-lg text-sm px-4 py-3 focus:ring-2 focus:ring-primary-container outline-none transition-all"
                                        placeholder="e.g. NSW"
                                        type="text"
                                    />
                                    <p
                                        v-if="form.errors.state"
                                        class="text-xs text-error mt-1"
                                    >
                                        {{ form.errors.state }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <label
                                        class="app-text font-bold tracking-widest text-on-surface-variant uppercase ml-1"
                                        >Postcode</label
                                    >
                                    <input
                                        v-model="form.postcode"
                                        class="w-full bg-surface-container-highest border-none rounded-lg text-sm px-4 py-3 focus:ring-2 focus:ring-primary-container outline-none transition-all"
                                        placeholder="e.g. 2000"
                                        type="text"
                                    />
                                    <p
                                        v-if="form.errors.postcode"
                                        class="text-xs text-error mt-1"
                                    >
                                        {{ form.errors.postcode }}
                                    </p>
                                </div>

                                <div class="space-y-2 col-span-3">
                                    <label
                                        class="app-text font-bold tracking-widest text-on-surface-variant uppercase ml-1"
                                        >Complete Address</label
                                    >
                                    <input
                                        v-model="form.address"
                                        class="w-full bg-surface-container-highest border-none rounded-lg text-sm px-4 py-3 focus:ring-2 focus:ring-primary-container outline-none transition-all"
                                        placeholder="e.g. 123 Main St"
                                        type="text"
                                    />
                                    <p
                                        v-if="form.errors.address"
                                        class="text-xs text-error mt-1"
                                    >
                                        {{ form.errors.address }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Properties Section -->
                        <div class="border-t border-surface-container pt-8">
                            <div class="flex items-center justify-between mb-5">
                                <div>
                                    <h4
                                        class="app-text-medium font-bold tracking-[0.2em] text-on-surface-variant/50 uppercase mb-5"
                                    >
                                        Properties ({{ form.properties.length }})
                                    </h4>
                                    <p
                                        class="app-text-medium text-on-surface-variant/40 mt-1"
                                    >
                                        Add properties associated with this
                                        customer (optional)
                                    </p>
                                </div>
                                <Button
                                    type="button"
                                    @click="addProperty"
                                >
                                    Add New Property
                                </Button>
                            </div>

                            <!-- Empty state -->
                            <div
                                v-if="form.properties.length === 0"
                                class="text-center py-10 text-on-surface-variant/40 border border-dashed border-outline-variant/20 rounded-xl"
                            >
                                <span
                                    class="material-symbols-outlined text-3xl mb-2 block"
                                    data-icon="home_work"
                                    >home_work</span
                                >
                                <p class="text-sm font-medium">
                                    No properties added yet
                                </p>
                                <p class="text-xs mt-1">
                                    Click "Add New Property" to associate a
                                    property with this customer
                                </p>
                            </div>

                            <!-- Property cards -->
                            <div
                                v-for="(property, index) in form.properties"
                                :key="index"
                                class="mb-6 rounded-xl border border-outline-variant/10 overflow-hidden"
                            >
                                <div
                                    class="px-5 py-3 bg-[#fdfdfd] flex items-center justify-between border-b border-outline-variant/10"
                                >
                                    <span
                                        class="app-text-medium font-bold tracking-[0.15em] text-on-surface-variant uppercase"
                                    >
                                        Property {{ index + 1 }}
                                    </span>
                                    <button
                                        type="button"
                                        class="p-1.5 hover:bg-error-container/20 rounded-lg transition-colors text-error cursor-pointer"
                                        title="Remove Property"
                                        @click="removeProperty(index)"
                                    >
                                        <span
                                            class="material-symbols-outlined text-lg"
                                            data-icon="close"
                                            >close</span
                                        >
                                    </button>
                                </div>

                                <div class="p-5">
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-5"
                                    >
                                        <div class="space-y-2">
                                            <label
                                                class="app-text font-bold tracking-widest text-on-surface-variant uppercase ml-1"
                                                >Property Name</label
                                            >
                                            <input
                                                v-model="property.name"
                                                class="w-full bg-surface-container-highest border-none rounded-lg text-sm px-4 py-3 focus:ring-2 focus:ring-primary-container outline-none transition-all"
                                                placeholder="e.g. Head Office"
                                                type="text"
                                            />
                                            <p
                                                v-if="
                                                    form.errors[
                                                        `properties.${index}.name`
                                                    ]
                                                "
                                                class="text-xs text-error mt-1"
                                            >
                                                {{
                                                    form.errors[
                                                        `properties.${index}.name`
                                                    ]
                                                }}
                                            </p>
                                        </div>

                                        <div class="space-y-2">
                                            <label
                                                class="app-text font-bold tracking-widest text-on-surface-variant uppercase ml-1"
                                                >Property Type</label
                                            >
                                            <input
                                                v-model="property.property_type"
                                                class="w-full bg-surface-container-highest border-none rounded-lg text-sm px-4 py-3 focus:ring-2 focus:ring-primary-container outline-none transition-all"
                                                placeholder="e.g. Commercial, Residential"
                                                type="text"
                                            />
                                            <p
                                                v-if="
                                                    form.errors[
                                                        `properties.${index}.property_type`
                                                    ]
                                                "
                                                class="text-xs text-error mt-1"
                                            >
                                                {{
                                                    form.errors[
                                                        `properties.${index}.property_type`
                                                    ]
                                                }}
                                            </p>
                                        </div>

                                        <div
                                            class="space-y-2 col-span-3 md:col-span-1"
                                        >
                                            <label
                                                class="app-text font-bold tracking-widest text-on-surface-variant uppercase ml-1"
                                                >Complete Address</label
                                            >
                                            <input
                                                v-model="property.address"
                                                class="w-full bg-surface-container-highest border-none rounded-lg text-sm px-4 py-3 focus:ring-2 focus:ring-primary-container outline-none transition-all"
                                                placeholder="e.g. 456 George St"
                                                type="text"
                                            />
                                            <p
                                                v-if="
                                                    form.errors[
                                                        `properties.${index}.address`
                                                    ]
                                                "
                                                class="text-xs text-error mt-1"
                                            >
                                                {{
                                                    form.errors[
                                                        `properties.${index}.address`
                                                    ]
                                                }}
                                            </p>
                                        </div>

                                        <div class="space-y-2">
                                            <label
                                                class="app-text font-bold tracking-widest text-on-surface-variant uppercase ml-1"
                                                >Suburb</label
                                            >
                                            <input
                                                v-model="property.suburb"
                                                class="w-full bg-surface-container-highest border-none rounded-lg text-sm px-4 py-3 focus:ring-2 focus:ring-primary-container outline-none transition-all"
                                                placeholder="e.g. Sydney"
                                                type="text"
                                            />
                                            <p
                                                v-if="
                                                    form.errors[
                                                        `properties.${index}.suburb`
                                                    ]
                                                "
                                                class="text-xs text-error mt-1"
                                            >
                                                {{
                                                    form.errors[
                                                        `properties.${index}.suburb`
                                                    ]
                                                }}
                                            </p>
                                        </div>

                                        <div class="space-y-2">
                                            <label
                                                class="app-text font-bold tracking-widest text-on-surface-variant uppercase ml-1"
                                                >State</label
                                            >
                                            <input
                                                v-model="property.state"
                                                class="w-full bg-surface-container-highest border-none rounded-lg text-sm px-4 py-3 focus:ring-2 focus:ring-primary-container outline-none transition-all"
                                                placeholder="e.g. NSW"
                                                type="text"
                                            />
                                            <p
                                                v-if="
                                                    form.errors[
                                                        `properties.${index}.state`
                                                    ]
                                                "
                                                class="text-xs text-error mt-1"
                                            >
                                                {{
                                                    form.errors[
                                                        `properties.${index}.state`
                                                    ]
                                                }}
                                            </p>
                                        </div>

                                        <div class="space-y-2">
                                            <label
                                                class="app-text font-bold tracking-widest text-on-surface-variant uppercase ml-1"
                                                >Postcode</label
                                            >
                                            <input
                                                v-model="property.postcode"
                                                class="w-full bg-surface-container-highest border-none rounded-lg text-sm px-4 py-3 focus:ring-2 focus:ring-primary-container outline-none transition-all"
                                                placeholder="e.g. 2000"
                                                type="text"
                                            />
                                            <p
                                                v-if="
                                                    form.errors[
                                                        `properties.${index}.postcode`
                                                    ]
                                                "
                                                class="text-xs text-error mt-1"
                                            >
                                                {{
                                                    form.errors[
                                                        `properties.${index}.postcode`
                                                    ]
                                                }}
                                            </p>
                                        </div>

                                        <!-- Use Customer Contact Checkbox -->
                                        <div
                                            class="col-span-3 flex items-center gap-3 pt-2 border-t border-outline-variant/10 mt-1"
                                        >
                                            <input
                                                :id="`use-customer-contact-${index}`"
                                                v-model="
                                                    useCustomerContact[index]
                                                "
                                                type="checkbox"
                                                class="w-4 h-4 rounded border-outline-variant/30 text-primary focus:ring-primary-container cursor-pointer"
                                                @change="
                                                    toggleCustomerContact(index)
                                                "
                                            />
                                            <label
                                                :for="`use-customer-contact-${index}`"
                                                class="text-xs font-semibold text-on-surface-variant cursor-pointer select-none"
                                            >
                                                Use customer's name, phone and
                                                email as contact
                                            </label>
                                        </div>

                                        <div class="space-y-2">
                                            <label
                                                class="app-text font-bold tracking-widest text-on-surface-variant uppercase ml-1"
                                                >Contact Name</label
                                            >
                                            <input
                                                v-model="property.contact_name"
                                                class="w-full bg-surface-container-highest border-none rounded-lg text-sm px-4 py-3 focus:ring-2 focus:ring-primary-container outline-none transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                                placeholder="e.g. John Smith"
                                                type="text"
                                                :disabled="
                                                    useCustomerContact[index]
                                                "
                                            />
                                            <p
                                                v-if="
                                                    form.errors[
                                                        `properties.${index}.contact_name`
                                                    ]
                                                "
                                                class="text-xs text-error mt-1"
                                            >
                                                {{
                                                    form.errors[
                                                        `properties.${index}.contact_name`
                                                    ]
                                                }}
                                            </p>
                                        </div>

                                        <div class="space-y-2">
                                            <label
                                                class="app-text font-bold tracking-widest text-on-surface-variant uppercase ml-1"
                                                >Contact Phone</label
                                            >
                                            <input
                                                v-model="property.contact_phone"
                                                class="w-full bg-surface-container-highest border-none rounded-lg text-sm px-4 py-3 focus:ring-2 focus:ring-primary-container outline-none transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                                placeholder="e.g. 0412 345 678"
                                                type="tel"
                                                :disabled="
                                                    useCustomerContact[index]
                                                "
                                            />
                                            <p
                                                v-if="
                                                    form.errors[
                                                        `properties.${index}.contact_phone`
                                                    ]
                                                "
                                                class="text-xs text-error mt-1"
                                            >
                                                {{
                                                    form.errors[
                                                        `properties.${index}.contact_phone`
                                                    ]
                                                }}
                                            </p>
                                        </div>

                                        <div class="space-y-2">
                                            <label
                                                class="app-text font-bold tracking-widest text-on-surface-variant uppercase ml-1"
                                                >Contact Email</label
                                            >
                                            <input
                                                v-model="property.contact_email"
                                                class="w-full bg-surface-container-highest border-none rounded-lg text-sm px-4 py-3 focus:ring-2 focus:ring-primary-container outline-none transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                                placeholder="e.g. john@example.com"
                                                type="email"
                                                :disabled="
                                                    useCustomerContact[index]
                                                "
                                            />
                                            <p
                                                v-if="
                                                    form.errors[
                                                        `properties.${index}.contact_email`
                                                    ]
                                                "
                                                class="text-xs text-error mt-1"
                                            >
                                                {{
                                                    form.errors[
                                                        `properties.${index}.contact_email`
                                                    ]
                                                }}
                                            </p>
                                        </div>

                                        <!-- map picker -->
                                        <div class="col-span-3">
                                            <MapPicker
                                                :model-value="
                                                    property._location
                                                "
                                                @update:model-value="
                                                    (val) => {
                                                        property._location =
                                                            val;
                                                        property.latitude =
                                                            val.lat;
                                                        property.longitude =
                                                            val.lng;
                                                    }
                                                "
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <Footer />
    </AppShell>
</template>

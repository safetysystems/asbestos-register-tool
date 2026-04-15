<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, nextTick, onBeforeUnmount, ref, watch } from 'vue';
import Button from '@/Components/Common/Button.vue';

const props = defineProps({
    audit: Object,
});

function normalizeDate(dateValue) {
    if (!dateValue) {
        return '';
    }

    if (typeof dateValue === 'string' && dateValue.includes('T')) {
        return dateValue.split('T')[0];
    }

    return dateValue;
}

const customerName = computed(() => props.audit?.property?.customer?.name || '—');
const propertyName = computed(() => props.audit?.property?.name || '—');
const propertyAddress = computed(() => {
    const p = props.audit?.property;
    if (!p) {
        return '—';
    }

    return [p.address, p.suburb, p.state, p.postcode].filter(Boolean).join(', ') || '—';
});

const jobId = computed(() => props.audit?.qr_number || (props.audit?.id ? `#${props.audit.id}` : '—'));

const form = useForm({
    property_id: props.audit?.property_id ?? null,
    audit_date: normalizeDate(props.audit?.audit_date),
    audit_hours: props.audit?.audit_hours ?? '',
    job_type: props.audit?.job_type ?? '',
    labelling_status: props.audit?.labelling_status ?? '',
    qr_number: props.audit?.qr_number ?? '',
    installation_status: props.audit?.installation_status ?? '',
    lead_status: props.audit?.lead_status ?? '',
    samples_taken: props.audit?.samples_taken ?? '',
    smf_status: props.audit?.smf_status ?? '',
    smf_notes: props.audit?.smf_notes ?? '',
    samples: (props.audit?.samples ?? []).map((sample) => ({ ...sample, images: [] })),
});

function toSamplePayload(sample) {
    return {
        id: sample.id ?? null,
        sample_number: sample.sample_number ?? null,
        building_area: sample.building_area ?? null,
        location: sample.location ?? null,
        surface: sample.surface ?? null,
        material: sample.material ?? null,
        hazardous_material: sample.hazardous_material ?? null,
        quantity: sample.quantity ?? null,
        condition: sample.condition ?? null,
        access_level: sample.access_level ?? null,
        friability: sample.friability ?? null,
        hazard_priority: sample.hazard_priority ?? null,
        comments: sample.comments ?? null,
        images: (sample.images ?? []).map((image) => image?.file ?? image),
    };
}

function submitAudit({ onSuccess } = {}) {
    form.transform((data) => ({
        ...data,
        samples: (data.samples ?? []).map(toSamplePayload),
    }));

    form.put(`/asbestos-audits/${props.audit.id}`, {
        preserveScroll: true,
        onSuccess,
    });
}

function submitAuditAndReturn() {
    submitAudit({
        onSuccess: () => {
            router.visit('/asbestos-audits');
        },
    });
}

const isSampleModalOpen = ref(false);
const editingSampleIndex = ref(null);
const sampleDraft = ref(null);
const isCameraModalOpen = ref(false);
const videoEl = ref(null);
const cameraStream = ref(null);
const isCameraActive = ref(false);
const cameraError = ref('');
const facingMode = ref('environment');

function emptySampleDraft() {
    return {
        id: null,
        sample_number: '',
        images: [],
        building_area: '',
        location: '',
        surface: '',
        material: '',
        hazardous_material: '',
        quantity: '',
        condition: '',
        access_level: '',
        friability: '',
        hazard_priority: '',
        comments: '',
    };
}

async function startCamera() {
    cameraError.value = '';

    if (!navigator.mediaDevices?.getUserMedia) {
        cameraError.value = 'Camera is not supported on this device/browser.';
        return;
    }

    stopCamera();

    try {
        const stream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: { ideal: facingMode.value },
            },
            audio: false,
        });

        cameraStream.value = stream;
        isCameraActive.value = true;

        await nextTick();

        if (videoEl.value) {
            videoEl.value.srcObject = stream;
            await videoEl.value.play();
        }
    } catch (error) {
        isCameraActive.value = false;
        cameraStream.value = null;
        cameraError.value = error?.message || 'Unable to access camera. Please allow camera permissions.';
    }
}

function stopCamera() {
    if (cameraStream.value) {
        for (const track of cameraStream.value.getTracks()) {
            track.stop();
        }
    }

    cameraStream.value = null;
    isCameraActive.value = false;
}

async function toggleFacingMode() {
    facingMode.value = facingMode.value === 'environment' ? 'user' : 'environment';

    if (isCameraActive.value) {
        await startCamera();
    }
}

async function capturePhoto() {
    cameraError.value = '';

    const video = videoEl.value;
    if (!video || !isCameraActive.value) {
        cameraError.value = 'Start the camera first.';
        return;
    }

    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth || 1280;
    canvas.height = video.videoHeight || 720;

    const ctx = canvas.getContext('2d');
    if (!ctx) {
        cameraError.value = 'Unable to capture image.';
        return;
    }

    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', 0.9));
    if (!blob) {
        cameraError.value = 'Unable to capture image.';
        return;
    }

    const file = new File([blob], `sample-${Date.now()}.jpg`, { type: 'image/jpeg' });
    const previewUrl = URL.createObjectURL(file);

    sampleDraft.value.images.push({
        file,
        previewUrl,
        source: 'capture',
    });
}

async function openAddSampleModal() {
    editingSampleIndex.value = null;
    sampleDraft.value = emptySampleDraft();
    isSampleModalOpen.value = true;
}

async function openEditSampleModal(index) {
    editingSampleIndex.value = index;
    sampleDraft.value = {
        ...emptySampleDraft(),
        ...(form.samples?.[index] ?? {}),
    };
    isSampleModalOpen.value = true;
}

function closeSampleModal() {
    isSampleModalOpen.value = false;
    isCameraModalOpen.value = false;
}

function saveSample() {
    const next = {
        ...toSamplePayload(sampleDraft.value),
    };

    if (editingSampleIndex.value === null) {
        form.samples.push(next);
    } else {
        form.samples[editingSampleIndex.value] = next;
    }

    closeSampleModal();
}

function addDraftImages(event) {
    const files = Array.from(event.target.files || []);
    if (!files.length) {
        return;
    }

    sampleDraft.value.images.push(
        ...files.map((file) => ({
            file,
            previewUrl: URL.createObjectURL(file),
            source: 'upload',
        }))
    );

    event.target.value = '';
}

function removeDraftImage(index) {
    const item = sampleDraft.value.images[index];
    if (item?.previewUrl) {
        URL.revokeObjectURL(item.previewUrl);
    }

    sampleDraft.value.images.splice(index, 1);
}

async function openCameraModal() {
    isCameraModalOpen.value = true;
    await startCamera();
}

function closeCameraModal() {
    isCameraModalOpen.value = false;
    stopCamera();
}

function deleteSample(index) {
    if (!window.confirm('Delete this sample?')) {
        return;
    }

    form.samples.splice(index, 1);
}

watch(isSampleModalOpen, (isOpen) => {
    document.body.style.overflow = isOpen ? 'hidden' : '';

    if (!isOpen) {
        closeCameraModal();
    }
});

onBeforeUnmount(() => {
    document.body.style.overflow = '';
    stopCamera();
});
</script>

<template>
    <Head :title="`Auditing | ${jobId}`" />
    <div class="min-h-screen bg-surface-container-low">
        <div class="px-4 sm:px-6 lg:px-8 py-6 sm:py-10">
            <div class="max-w-6xl mx-auto space-y-6">
                <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/10 flex flex-col w-full mb-6">
                    <div class="px-4 md:px-8 py-5 border-b border-surface-container flex justify-between items-center bg-[#fdfdfd] rounded-t-xl">
                        <div>
                            <p class="app-text font-semibold text-stone-400 uppercase tracking-[0.2em] mb-1">Auditing</p>
                            <h3 class="text-lg font-bold text-on-surface tracking-tight uppercase app-text-medium">Audit Details</h3>
                        </div>
                        <Button type="button" @click="submitAuditAndReturn" :disabled="form.processing">
                            {{ form.processing ? 'Saving...' : 'Save Audit' }}
                        </Button>
                    </div>
                    <div class="p-4 md:p-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-6">
                            <div class="space-y-1">
                                <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1">JOB ID</label>
                                <input
                                    :value="jobId"
                                    type="text"
                                    disabled
                                    class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm outline-none opacity-70 cursor-not-allowed"
                                />
                            </div>
                            <div class="space-y-1">
                                <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1">AUDIT DATE</label>
                                <input
                                    v-model="form.audit_date"
                                    type="date"
                                    disabled
                                    class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm outline-none opacity-70 cursor-not-allowed"
                                />
                            </div>
                            <div class="space-y-1">
                                <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1">CUSTOMER</label>
                                <input
                                    :value="customerName"
                                    type="text"
                                    disabled
                                    class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm outline-none opacity-70 cursor-not-allowed"
                                />
                            </div>
                            <div class="space-y-1">
                                <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1">PROPERTY</label>
                                <input
                                    :value="propertyName"
                                    type="text"
                                    disabled
                                    class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm outline-none opacity-70 cursor-not-allowed"
                                />
                            </div>
                            <div class="md:col-span-2 space-y-1">
                                <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1">ADDRESS</label>
                                <input
                                    :value="propertyAddress"
                                    type="text"
                                    disabled
                                    class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm outline-none opacity-70 cursor-not-allowed"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/10 overflow-hidden">
                    <div class="px-4 md:px-8 py-6 border-b border-surface-container bg-[#fcfcfc] rounded-t-xl">
                        <p class="app-text font-semibold text-stone-400 uppercase tracking-[0.2em] mb-1">SAMPLE REGISTER</p>
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
                            <h2 class="text-2xl md:text-[2rem] font-semibold text-[#1c1e21] leading-none">Asbestos Samples</h2>
                            <div class="flex gap-4 w-full md:w-auto">
                                <div class="flex-1 md:flex-none px-4 py-2 bg-orange-50 text-orange-600 text-xs font-semibold rounded-lg border border-orange-100 flex items-center justify-center gap-2">
                                    {{ form.samples.length }} samples
                                </div>
                                <Button type="button" @click="openAddSampleModal">
                                    Add sample
                                </Button>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 md:p-8">
                        <div v-if="Object.keys(form.errors).length" class="mb-6">
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <p class="text-sm font-bold text-red-800 mb-2">Please fix the following errors:</p>
                                <ul class="list-disc list-inside space-y-1">
                                    <li v-for="(error, field) in form.errors" :key="field" class="text-sm text-red-700">{{ error }}</li>
                                </ul>
                            </div>
                        </div>

                        <div class="overflow-x-auto scrollbar-thin">
                            <table class="w-full text-left order-collapse min-w-[760px]">
                                <thead>
                                    <tr class="border-b border-stone-100">
                                        <th class="px-4 py-4 app-text font-semibold text-stone-400 uppercase tracking-widest">Sample</th>
                                        <th class="px-4 py-4 app-text font-semibold text-stone-400 uppercase tracking-widest">Area</th>
                                        <th class="px-4 py-4 app-text font-semibold text-stone-400 uppercase tracking-widest">Material</th>
                                        <th class="px-4 py-4 app-text font-semibold text-stone-400 uppercase tracking-widest">Priority</th>
                                        <th class="px-4 py-4 app-text font-semibold text-stone-400 uppercase tracking-widest text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-stone-50">
                                    <tr v-if="!form.samples.length">
                                        <td colspan="5" class="px-4 py-10 text-center text-sm text-stone-500">
                                            No samples yet. Click “Add sample” to start.
                                        </td>
                                    </tr>
                                    <tr v-for="(sample, index) in form.samples" :key="sample.id ?? `new-${index}`" class="group hover:bg-[#fbfbfb] transition-colors">
                                        <td class="px-4 py-6">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-full bg-orange-50 flex items-center justify-center text-orange-700 font-semibold text-xs border border-orange-100">
                                                    {{ String(index + 1).padStart(2, '0') }}
                                                </div>
                                                <div>
                                                    <p class="text-sm font-semibold text-[#1c1e21]">
                                                        {{ sample.sample_number || `Sample ${String(index + 1).padStart(2, '0')}` }}
                                                    </p>
                                                    <p class="app-text font-semibold text-stone-400 uppercase tracking-widest mt-0.5">
                                                        {{ sample.id ? 'SAVED' : 'NEW' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-6 text-sm text-on-surface-variant/70">
                                            {{ sample.building_area || '—' }}
                                        </td>
                                        <td class="px-4 py-6 text-sm text-on-surface-variant/70">
                                            {{ sample.material || '—' }}
                                        </td>
                                        <td class="px-4 py-6">
                                            <span class="px-3 py-1 bg-stone-100 text-stone-500 text-[9px] font-semibold rounded-full uppercase tracking-tighter">
                                                {{ sample.hazard_priority || '—' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-6 text-right">
                                            <div class="flex justify-end gap-2">
                                                <button
                                                    type="button"
                                                    :disabled="form.processing"
                                                    @click="openEditSampleModal(index)"
                                                    class="w-9 h-9 flex items-center justify-center bg-white text-[#1c1e21] rounded-lg border border-stone-200 shadow-sm hover:bg-stone-50 transition-all active:scale-95 disabled:opacity-60 disabled:cursor-not-allowed"
                                                >
                                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                                </button>
                                                <button
                                                    type="button"
                                                    :disabled="form.processing"
                                                    @click="deleteSample(index)"
                                                    class="w-9 h-9 flex items-center justify-center bg-white text-error rounded-lg border border-stone-200 shadow-sm hover:bg-red-50 transition-all active:scale-95 disabled:opacity-60 disabled:cursor-not-allowed"
                                                >
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
        </div>
    </div>

    <div v-if="isSampleModalOpen" id="sampleModal" class="fixed inset-0 z-[100]">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-[2px]" @click="closeSampleModal"></div>

        <div class="absolute inset-0 flex items-center justify-center p-4 sm:p-6 md:p-8 pointer-events-none">
            <div class="w-full max-w-[900px] bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-200 pointer-events-auto flex flex-col max-h-[90vh]">
                <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-start bg-[#fdfdfd] shrink-0">
                    <div>
                        <p class="app-text font-semibold text-stone-400 uppercase tracking-[0.2em] mb-1">
                            {{ editingSampleIndex === null ? 'Add Sample' : 'Edit Sample' }}
                        </p>
                        <h2 class="text-[2rem] font-semibold text-[#1c1e21] leading-none">
                            {{ sampleDraft?.sample_number || `Sample ${String((editingSampleIndex ?? form.samples.length) + 1).padStart(2, '0')}` }}
                        </h2>
                    </div>
                    <button
                        type="button"
                        @click="closeSampleModal"
                        class="px-6 py-2 bg-white border border-stone-200 text-[#1c1e21] text-xs font-semibold rounded-lg hover:bg-gray-50 transition-all shadow-sm"
                    >
                        Close
                    </button>
                </div>

                <div class="p-8 overflow-y-auto scrollbar-thin">
                    <div class="border border-stone-100 rounded-xl p-8 bg-white shadow-sm">
                        <div class="mb-8">
                            <p class="app-text font-semibold text-stone-400 uppercase tracking-widest mb-1">SAMPLE DETAILS</p>
                            <h3 class="text-xl font-semibold text-[#1c1e21]">Edit Sample Information</h3>
                        </div>

                        <form class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6" @submit.prevent="saveSample">
                            <div class="space-y-1">
                                <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1">SAMPLE</label>
                                <input
                                    v-model="sampleDraft.sample_number"
                                    type="text"
                                    placeholder="AB-001"
                                    class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all"
                                />
                            </div>
                            <div class="space-y-1">
                                <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1">PHOTOS</label>
                                <div class="flex flex-wrap items-center gap-2">
                                    <button
                                        type="button"
                                        @click="openCameraModal"
                                        class="px-3 py-2 rounded-lg border border-stone-200 bg-white text-xs font-semibold text-[#1c1e21] shadow-sm hover:bg-stone-50 transition-all"
                                    >
                                        Open camera
                                    </button>
                                </div>

                                <input
                                    type="file"
                                    accept="image/*"
                                    capture="environment"
                                    multiple
                                    @change="addDraftImages"
                                    class="w-full bg-surface-container-low border-none rounded-lg py-4 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all file:mr-4 file:rounded-md file:border-0 file:bg-white file:px-4 file:py-2 file:text-xs file:font-semibold file:text-[#1c1e21] file:shadow-sm file:ring-1 file:ring-stone-200 hover:file:bg-stone-50"
                                />
                                <p class="text-xs text-stone-500 font-medium">
                                    Tap to use your camera or upload from your device.
                                </p>

                                <div v-if="(sampleDraft.media?.length ?? 0) || sampleDraft.images.length" class="flex flex-wrap gap-3 pt-2">
                                    <div v-for="media in (sampleDraft.media ?? [])" :key="`media-${media.id}`" class="w-16 h-16 rounded-lg overflow-hidden border border-stone-200 bg-stone-50">
                                        <img :src="media.url" :alt="media.name" class="w-full h-full object-cover" />
                                    </div>

                                    <div v-for="(imageItem, fileIndex) in sampleDraft.images" :key="`file-${fileIndex}-${imageItem.file?.name}`" class="relative w-16 h-16 rounded-lg overflow-hidden border border-stone-200 bg-stone-50">
                                        <img :src="imageItem.previewUrl" :alt="imageItem.file?.name" class="w-full h-full object-cover" />
                                        <button
                                            type="button"
                                            @click="removeDraftImage(fileIndex)"
                                            class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-white shadow-sm ring-1 ring-stone-200 text-xs font-bold text-stone-700 hover:bg-stone-50"
                                        >
                                            ×
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="md:col-span-2 space-y-1">
                                <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1">BUILDING / AREA</label>
                                <input
                                    v-model="sampleDraft.building_area"
                                    type="text"
                                    placeholder="Building A / Boiler Room"
                                    class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all"
                                />
                            </div>

                            <div class="md:col-span-2 space-y-1">
                                <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1">LOCATION</label>
                                <input
                                    v-model="sampleDraft.location"
                                    type="text"
                                    placeholder="North wall service chase"
                                    class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all"
                                />
                            </div>

                            <div class="space-y-1">
                                <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1">SURFACE</label>
                                <input
                                    v-model="sampleDraft.surface"
                                    type="text"
                                    placeholder="Lagged pipe elbow"
                                    class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all"
                                />
                            </div>
                            <div class="space-y-1">
                                <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1">MATERIAL</label>
                                <input
                                    v-model="sampleDraft.material"
                                    type="text"
                                    placeholder="Thermal insulation"
                                    class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all"
                                />
                            </div>

                            <div class="space-y-1">
                                <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1">HAZARDOUS MATERIAL</label>
                                <input
                                    v-model="sampleDraft.hazardous_material"
                                    type="text"
                                    placeholder="Amosite"
                                    class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all"
                                />
                            </div>
                            <div class="space-y-1">
                                <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1">APPROX. QTY (M2)</label>
                                <input
                                    v-model="sampleDraft.quantity"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    placeholder="6.2"
                                    class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all"
                                />
                            </div>

                            <div class="space-y-1">
                                <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1">CONDITION</label>
                                <div class="relative">
                                    <select
                                        v-model="sampleDraft.condition"
                                        class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none appearance-none cursor-pointer"
                                    >
                                        <option value="">Select condition</option>
                                        <option value="Good">Good</option>
                                        <option value="Fair">Fair</option>
                                        <option value="Poor">Poor</option>
                                    </select>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1">READILY ACCESSIBLE</label>
                                <div class="relative">
                                    <select
                                        v-model="sampleDraft.access_level"
                                        class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none appearance-none cursor-pointer"
                                    >
                                        <option value="">Select access level</option>
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1">FRIABLE</label>
                                <div class="relative">
                                    <select
                                        v-model="sampleDraft.friability"
                                        class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none appearance-none cursor-pointer"
                                    >
                                        <option value="">Select friability</option>
                                        <option value="Friable">Friable</option>
                                        <option value="Non-friable">Non-friable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1">HAZARD PRIORITY</label>
                                <div class="relative">
                                    <select
                                        v-model="sampleDraft.hazard_priority"
                                        class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none appearance-none cursor-pointer"
                                    >
                                        <option value="">Select priority</option>
                                        <option value="Low">Low</option>
                                        <option value="Medium">Medium</option>
                                        <option value="High">High</option>
                                    </select>
                                </div>
                            </div>

                            <div class="md:col-span-2 space-y-1">
                                <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-1">COMMENTS</label>
                                <textarea
                                    v-model="sampleDraft.comments"
                                    rows="4"
                                    placeholder="Describe access risk, condition, or maintenance disturbance..."
                                    class="w-full bg-surface-container-low border-none rounded-lg py-5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all resize-none"
                                ></textarea>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="px-8 py-6 border-t border-gray-100 flex justify-end items-center bg-[#fdfdfd] shrink-0">
                    <Button type="button" @click="saveSample">
                        Save Changes
                    </Button>
                </div>
            </div>
        </div>
    </div>

    <div v-if="isCameraModalOpen" class="fixed inset-0 z-[110]">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-[2px]" @click="closeCameraModal"></div>

        <div class="absolute inset-0 flex items-center justify-center p-4 sm:p-6 md:p-8 pointer-events-none">
            <div class="w-full max-w-[900px] bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-200 pointer-events-auto flex flex-col max-h-[90vh]">
                <div class="px-6 sm:px-8 py-5 border-b border-gray-100 flex justify-between items-start bg-[#fdfdfd] shrink-0">
                    <div>
                        <p class="app-text font-semibold text-stone-400 uppercase tracking-[0.2em] mb-1">Camera</p>
                        <h2 class="text-xl sm:text-[1.6rem] font-semibold text-[#1c1e21] leading-none">Capture photos</h2>
                    </div>
                    <button
                        type="button"
                        @click="closeCameraModal"
                        class="px-6 py-2 bg-white border border-stone-200 text-[#1c1e21] text-xs font-semibold rounded-lg hover:bg-gray-50 transition-all shadow-sm"
                    >
                        Close
                    </button>
                </div>

                <div class="p-4 sm:p-6 md:p-8 overflow-y-auto scrollbar-thin space-y-4">
                    <div class="rounded-2xl overflow-hidden border border-stone-200 bg-black">
                        <div class="relative w-full aspect-video">
                            <video
                                ref="videoEl"
                                class="absolute inset-0 w-full h-full object-cover"
                                autoplay
                                playsinline
                                muted
                            ></video>
                            <div v-if="!isCameraActive" class="absolute inset-0 flex items-center justify-center text-xs font-semibold text-white/80">
                                Starting camera…
                            </div>
                        </div>
                    </div>

                    <p v-if="cameraError" class="text-xs text-error font-semibold">
                        {{ cameraError }}
                    </p>

                    <div class="flex flex-wrap items-center gap-2">
                        <Button type="button" @click="capturePhoto" :disabled="!isCameraActive">
                            Capture
                        </Button>
                        <button
                            type="button"
                            @click="toggleFacingMode"
                            class="px-3 py-2 rounded-lg border border-stone-200 bg-white text-xs font-semibold text-[#1c1e21] shadow-sm hover:bg-stone-50 transition-all"
                        >
                            Switch camera
                        </button>
                        <button
                            type="button"
                            @click="stopCamera"
                            class="px-3 py-2 rounded-lg border border-stone-200 bg-white text-xs font-semibold text-[#1c1e21] shadow-sm hover:bg-stone-50 transition-all"
                        >
                            Stop
                        </button>
                        <button
                            type="button"
                            @click="startCamera"
                            class="px-3 py-2 rounded-lg border border-stone-200 bg-white text-xs font-semibold text-[#1c1e21] shadow-sm hover:bg-stone-50 transition-all"
                        >
                            Start
                        </button>
                    </div>

                    <div v-if="sampleDraft.images.length" class="flex flex-wrap gap-3 pt-1">
                        <div v-for="(imageItem, fileIndex) in sampleDraft.images" :key="`cam-file-${fileIndex}-${imageItem.file?.name}`" class="relative w-20 h-20 rounded-xl overflow-hidden border border-stone-200 bg-stone-50">
                            <img :src="imageItem.previewUrl" :alt="imageItem.file?.name" class="w-full h-full object-cover" />
                            <button
                                type="button"
                                @click="removeDraftImage(fileIndex)"
                                class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-white shadow-sm ring-1 ring-stone-200 text-xs font-bold text-stone-700 hover:bg-stone-50"
                            >
                                ×
                            </button>
                        </div>
                    </div>
                </div>

                <div class="px-6 sm:px-8 py-5 border-t border-gray-100 flex justify-end items-center bg-[#fdfdfd] shrink-0">
                    <Button type="button" @click="closeCameraModal">
                        Done
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>

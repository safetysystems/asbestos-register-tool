<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, nextTick, onBeforeUnmount, ref, watch } from 'vue';
import Button from '@/Components/Common/Button.vue';

const props = defineProps({
    audit: Object,
});

function isFile(value) {
    return typeof File !== 'undefined' && value instanceof File;
}

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

const hasPendingSampleChanges = ref(false);
const showSaveSampleButton = computed(() => hasPendingSampleChanges.value);

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
        delete_media_ids: sample.delete_media_ids ?? [],
    };
}

function submitAudit({ onSuccess } = {}) {
    form.transform((data) => ({
        ...data,
        _method: 'put',
        redirect_to: 'auditing',
        samples: (data.samples ?? []).map(toSamplePayload),
    }));

    // Use POST + method spoofing so PHP properly parses uploaded files.
    form.post(`/asbestos-audits/${props.audit.id}`, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: (...args) => {
            hasPendingSampleChanges.value = false;
            onSuccess?.(...args);
        },
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
const sampleDraftOriginalPreviewUrls = ref(new Set());
const isCameraModalOpen = ref(false);
const videoEl = ref(null);
const takePhotoInputEl = ref(null);
const uploadPhotosInputEl = ref(null);
const cameraStream = ref(null);
const isCameraActive = ref(false);
const isCameraPreviewReady = ref(false);
const cameraError = ref('');
const facingMode = ref('environment');
const videoDevices = ref([]);
const selectedVideoDeviceId = ref('');
let removeDeviceChangeListener = null;
const isLightboxOpen = ref(false);
const lightboxUrl = ref('');
const lightboxAlt = ref('');
let removeLightboxKeydownListener = null;
let cameraReadyTimeout = null;
const cameraPermissionState = ref('');
const isDeleteSampleConfirmOpen = ref(false);
const pendingDeleteSampleIndex = ref(null);
let removeDeleteSampleKeydownListener = null;
const cameraDebug = computed(() => {
    if (typeof window === 'undefined') {
        return null;
    }

    return {
        origin: window.location?.origin || '',
        secureContext: Boolean(window.isSecureContext),
        hasMediaDevices: Boolean(navigator.mediaDevices),
        hasGetUserMedia: Boolean(navigator.mediaDevices?.getUserMedia),
    };
});

async function refreshCameraPermissionState() {
    cameraPermissionState.value = '';

    // Not supported in all browsers.
    if (!navigator.permissions?.query) {
        return;
    }

    try {
        const status = await navigator.permissions.query({ name: 'camera' });
        cameraPermissionState.value = status?.state ?? '';
    } catch (error) {
        cameraPermissionState.value = '';
    }
}

function isSecureOrigin() {
    if (typeof window === 'undefined') {
        return true;
    }

    if (window.isSecureContext) {
        return true;
    }

    const host = window.location?.hostname;

    return host === 'localhost' || host === '127.0.0.1';
}

function emptySampleDraft() {
    return {
        id: null,
        sample_number: '',
        images: [],
        delete_media_ids: [],
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

function collectPreviewUrls(images) {
    const urls = new Set();

    for (const image of images ?? []) {
        if (image?.previewUrl) {
            urls.add(image.previewUrl);
        }
    }

    return urls;
}

function normalizeDraftImages(images) {
    return (images ?? [])
        .map((image) => {
            if (isFile(image)) {
                return {
                    file: image,
                    previewUrl: URL.createObjectURL(image),
                    source: 'upload',
                };
            }

            if (image?.file) {
                return {
                    file: image.file,
                    previewUrl: image.previewUrl || URL.createObjectURL(image.file),
                    source: image.source || 'upload',
                };
            }

            return null;
        })
        .filter(Boolean);
}

function revokePreviewUrl(previewUrl) {
    if (!previewUrl) {
        return;
    }

    URL.revokeObjectURL(previewUrl);
}

function revokeDraftOnlyPreviewUrls() {
    const originalUrls = sampleDraftOriginalPreviewUrls.value ?? new Set();

    for (const image of sampleDraft.value?.images ?? []) {
        if (image?.previewUrl && !originalUrls.has(image.previewUrl)) {
            revokePreviewUrl(image.previewUrl);
        }
    }
}

function openLightbox(url, alt = '') {
    if (!url) {
        return;
    }

    lightboxUrl.value = url;
    lightboxAlt.value = alt;
    isLightboxOpen.value = true;
}

function closeLightbox() {
    isLightboxOpen.value = false;
    lightboxUrl.value = '';
    lightboxAlt.value = '';
}

function ensureLightboxKeyListener(isOpen) {
    if (isOpen && !removeLightboxKeydownListener) {
        const handler = (event) => {
            if (event.key === 'Escape') {
                closeLightbox();
            }
        };
        window.addEventListener('keydown', handler);
        removeLightboxKeydownListener = () => window.removeEventListener('keydown', handler);
    }

    if (!isOpen && removeLightboxKeydownListener) {
        removeLightboxKeydownListener();
        removeLightboxKeydownListener = null;
    }
}

function isTabletLike() {
    if (typeof window === 'undefined' || !window.matchMedia) {
        return false;
    }

    // iPad/tablet breakpoint.
    return window.matchMedia('(min-width: 768px)').matches;
}

function getIdealVideoConstraints() {
    const isTablet = isTabletLike();

    return {
        // Prefer a higher-res 4:3 stream on iPad/tablet, and 16:9 on phones.
        width: { ideal: isTablet ? 1440 : 1280 },
        height: { ideal: isTablet ? 1080 : 720 },
        // Many iPad/tablet cameras default to 4:3; prefer that on larger screens.
        aspectRatio: { ideal: isTablet ? 4 / 3 : 16 / 9 },
    };
}

function ensureDeleteSampleKeyListener(isOpen) {
    if (isOpen && !removeDeleteSampleKeydownListener) {
        const handler = (event) => {
            if (event.key === 'Escape') {
                closeDeleteSampleConfirm();
            }
        };
        window.addEventListener('keydown', handler);
        removeDeleteSampleKeydownListener = () => window.removeEventListener('keydown', handler);
    }

    if (!isOpen && removeDeleteSampleKeydownListener) {
        removeDeleteSampleKeydownListener();
        removeDeleteSampleKeydownListener = null;
    }
}

async function refreshVideoDevices() {
    if (!navigator.mediaDevices?.enumerateDevices) {
        videoDevices.value = [];
        return;
    }

    try {
        const devices = await navigator.mediaDevices.enumerateDevices();
        videoDevices.value = devices.filter((device) => device.kind === 'videoinput');
    } catch (error) {
        videoDevices.value = [];
    }
}

async function startCamera() {
    cameraError.value = '';
    isCameraPreviewReady.value = false;

    if (!isSecureOrigin()) {
        cameraError.value = 'Camera requires a secure (HTTPS) connection.';
        return;
    }

    stopCamera();

    try {
        await refreshCameraPermissionState();

        if (cameraPermissionState.value === 'denied') {
            const origin = cameraDebug.value?.origin || 'this site';
            cameraError.value = `Camera permission is denied for ${origin}. Update Chrome site settings to allow camera, then reload.`;
            return;
        }

        if (!navigator.mediaDevices?.getUserMedia) {
            cameraError.value = 'Camera API is unavailable in this browser context. Try HTTPS in normal Chrome (not an embedded/in-app browser), or use Take photo.';
            return;
        }

        const tryGetStream = async (constraints) => {
            return navigator.mediaDevices.getUserMedia(constraints);
        };

        const baseVideo = getIdealVideoConstraints();

        const preferredConstraints = selectedVideoDeviceId.value
            ? {
                  video: {
                      ...baseVideo,
                      deviceId: { exact: selectedVideoDeviceId.value },
                  },
                  audio: false,
              }
            : {
                  video: {
                      ...baseVideo,
                      facingMode: { ideal: facingMode.value },
                  },
                  audio: false,
              };

        let stream;

        try {
            stream = await tryGetStream(preferredConstraints);
        } catch (error) {
            // Some browsers are picky about constraints. Try a few progressively simpler options.
            if (selectedVideoDeviceId.value) {
                try {
                    stream = await tryGetStream({
                        video: {
                            ...baseVideo,
                            deviceId: selectedVideoDeviceId.value,
                        },
                        audio: false,
                    });
                } catch (innerError) {
                    // Ignore and continue to generic fallback.
                }
            } else {
                try {
                    stream = await tryGetStream({
                        video: {
                            ...baseVideo,
                            facingMode: { exact: facingMode.value },
                        },
                        audio: false,
                    });
                } catch (innerError) {
                    // Ignore and continue to generic fallback.
                }
            }

            if (!stream) {
                stream = await tryGetStream({ video: true, audio: false });
            }
        }

        cameraStream.value = stream;
        isCameraActive.value = true;

        await nextTick();

        if (videoEl.value) {
            videoEl.value.playsInline = true;
            videoEl.value.muted = true;
            videoEl.value.autoplay = true;
            videoEl.value.setAttribute('playsinline', '');
            videoEl.value.setAttribute('webkit-playsinline', '');
            videoEl.value.srcObject = stream;
            try {
                await videoEl.value.play();
            } catch (error) {
                // Some browsers (notably iOS Safari) can still reject play() under certain conditions.
                cameraError.value = 'Camera started. If preview is paused, tap Start.';
            }

            if (cameraReadyTimeout) {
                clearTimeout(cameraReadyTimeout);
            }

            cameraReadyTimeout = setTimeout(() => {
                const video = videoEl.value;
                if (video && isCameraActive.value && (!video.videoWidth || !video.videoHeight)) {
                    cameraError.value = 'No preview. Try another camera, tap Start, or use Take photo.';
                }
            }, 1500);
        }

        await refreshVideoDevices();

        if (!selectedVideoDeviceId.value && videoDevices.value.length === 1) {
            selectedVideoDeviceId.value = videoDevices.value[0].deviceId;
        }
    } catch (error) {
        isCameraActive.value = false;
        cameraStream.value = null;
        const name = error?.name;

        if (name === 'NotAllowedError' || name === 'PermissionDeniedError') {
            cameraError.value = 'Camera permission denied. Please allow camera access and try again.';
            return;
        }

        if (name === 'NotFoundError' || name === 'DevicesNotFoundError') {
            cameraError.value = 'No camera found on this device.';
            return;
        }

        cameraError.value = error?.message || 'Unable to access camera. Please allow camera permissions.';
    }
}

function stopCamera() {
    if (cameraReadyTimeout) {
        clearTimeout(cameraReadyTimeout);
        cameraReadyTimeout = null;
    }

    if (cameraStream.value) {
        for (const track of cameraStream.value.getTracks()) {
            track.stop();
        }
    }

    cameraStream.value = null;
    isCameraActive.value = false;
    isCameraPreviewReady.value = false;

    if (videoEl.value) {
        videoEl.value.srcObject = null;
    }
}

function handleVideoLoadedMetadata() {
    const video = videoEl.value;
    if (!video) {
        return;
    }

    if (video.videoWidth && video.videoHeight) {
        isCameraPreviewReady.value = true;
    }
}

async function toggleFacingMode() {
    facingMode.value = facingMode.value === 'environment' ? 'user' : 'environment';
    selectedVideoDeviceId.value = '';

    if (isCameraActive.value) {
        await startCamera();
    }
}

async function selectCameraDevice(event) {
    selectedVideoDeviceId.value = event.target.value;

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

    if (!video.videoWidth || !video.videoHeight) {
        cameraError.value = 'Camera preview is not ready yet. Try again in a moment.';
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
    sampleDraftOriginalPreviewUrls.value = new Set();
    isSampleModalOpen.value = true;
}

async function openEditSampleModal(index) {
    editingSampleIndex.value = index;
    const existing = form.samples?.[index] ?? {};
    sampleDraftOriginalPreviewUrls.value = collectPreviewUrls(existing.images);

    sampleDraft.value = {
        ...emptySampleDraft(),
        ...existing,
        delete_media_ids: [],
    };
    sampleDraft.value.images = normalizeDraftImages(sampleDraft.value.images);
    isSampleModalOpen.value = true;
}

function closeSampleModal({ preserveDraft = false } = {}) {
    if (!preserveDraft) {
        revokeDraftOnlyPreviewUrls();
    }

    isSampleModalOpen.value = false;
    isCameraModalOpen.value = false;
}

function saveSample() {
    const next = {
        ...emptySampleDraft(),
        ...sampleDraft.value,
        images: normalizeDraftImages(sampleDraft.value.images),
    };

    if (editingSampleIndex.value === null) {
        form.samples.push(next);
    } else {
        form.samples[editingSampleIndex.value] = next;
    }

    hasPendingSampleChanges.value = true;
    closeSampleModal({ preserveDraft: true });
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

function removeDraftMedia(index) {
    const media = sampleDraft.value.media?.[index];
    if (!media) {
        return;
    }

    if (!sampleDraft.value.delete_media_ids) {
        sampleDraft.value.delete_media_ids = [];
    }

    if (media.id) {
        sampleDraft.value.delete_media_ids.push(media.id);
    }

    sampleDraft.value.media.splice(index, 1);
}

function triggerTakePhotoFallback() {
    if (takePhotoInputEl.value) {
        takePhotoInputEl.value.click();
    }
}

function triggerUploadPhotos() {
    if (uploadPhotosInputEl.value) {
        uploadPhotosInputEl.value.click();
    }
}

function removeDraftImage(index) {
    const item = sampleDraft.value.images[index];
    if (item?.previewUrl) {
        revokePreviewUrl(item.previewUrl);
    }

    sampleDraft.value.images.splice(index, 1);
}

async function openCameraModal() {
    isCameraModalOpen.value = true;

    if (!removeDeviceChangeListener && navigator.mediaDevices?.addEventListener) {
        const handler = () => refreshVideoDevices();
        navigator.mediaDevices.addEventListener('devicechange', handler);
        removeDeviceChangeListener = () => navigator.mediaDevices.removeEventListener('devicechange', handler);
    }

    await refreshVideoDevices();
    await refreshCameraPermissionState();

    await startCamera();
}

function closeCameraModal() {
    isCameraModalOpen.value = false;
    if (removeDeviceChangeListener) {
        removeDeviceChangeListener();
        removeDeviceChangeListener = null;
    }
    stopCamera();
}

function deleteSample(index) {
    for (const image of form.samples?.[index]?.images ?? []) {
        if (image?.previewUrl) {
            revokePreviewUrl(image.previewUrl);
        }
    }

    form.samples.splice(index, 1);
    hasPendingSampleChanges.value = true;
}

function requestDeleteSample(index) {
    pendingDeleteSampleIndex.value = index;
    isDeleteSampleConfirmOpen.value = true;
}

function closeDeleteSampleConfirm() {
    isDeleteSampleConfirmOpen.value = false;
    pendingDeleteSampleIndex.value = null;
}

function confirmDeleteSample() {
    const index = pendingDeleteSampleIndex.value;
    if (index === null || index === undefined) {
        closeDeleteSampleConfirm();
        return;
    }

    deleteSample(index);
    closeDeleteSampleConfirm();
}

watch(isSampleModalOpen, (isOpen) => {
    document.body.style.overflow = isOpen ? 'hidden' : '';

    if (!isOpen) {
        closeCameraModal();
    }
});

watch(isLightboxOpen, (isOpen) => {
    ensureLightboxKeyListener(isOpen);
});

watch(isDeleteSampleConfirmOpen, (isOpen) => {
    ensureDeleteSampleKeyListener(isOpen);
});

onBeforeUnmount(() => {
    document.body.style.overflow = '';
    stopCamera();
    if (removeDeviceChangeListener) {
        removeDeviceChangeListener();
        removeDeviceChangeListener = null;
    }
    ensureLightboxKeyListener(false);
    ensureDeleteSampleKeyListener(false);

    revokeDraftOnlyPreviewUrls();

    for (const sample of form.samples ?? []) {
        for (const image of sample?.images ?? []) {
            if (image?.previewUrl) {
                revokePreviewUrl(image.previewUrl);
            }
        }
    }
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
                                    Add New
                                </Button>
                                <Button v-if="showSaveSampleButton" type="button" @click="submitAudit" :disabled="form.processing">
                                    {{ form.processing ? 'Saving...' : 'Save Sample' }}
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
                                                    @click="requestDeleteSample(index)"
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
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity duration-300" @click="closeSampleModal"></div>

        <div class="absolute inset-0 flex items-center justify-center p-3 sm:p-6 md:p-8 pointer-events-none">
            <div class="w-full max-w-[960px] bg-white rounded-2xl shadow-2xl overflow-hidden pointer-events-auto flex flex-col max-h-[92vh] ring-1 ring-black/5">
                <!-- Header with gradient accent -->
                <div class="shrink-0 relative">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r bg-primary-container rounded-t-2xl"></div>
                    <div class="px-6 sm:px-8 py-5 sm:py-6 flex justify-between items-center">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-container to-orange-400 flex items-center justify-center shadow-sm">
                                <span class="material-symbols-outlined text-white text-xl">science</span>
                            </div>
                            <div>
                                <p class="app-text font-bold text-primary uppercase tracking-[0.2em]">
                                    {{ editingSampleIndex === null ? 'New Sample' : 'Edit Sample' }}
                                </p>
                                <h2 class="text-xl sm:text-2xl font-bold text-[#1c1e21] leading-tight">
                                    {{ sampleDraft?.sample_number || `Sample ${String((editingSampleIndex ?? form.samples.length) + 1).padStart(2, '0')}` }}
                                </h2>
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="closeSampleModal"
                            class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface-container-highest hover:bg-stone-200 transition-all cursor-pointer"
                        >
                            <span class="material-symbols-outlined text-lg text-on-surface-variant">close</span>
                        </button>
                    </div>
                </div>

                <!-- Scrollable Content -->
                <div class="flex-1 overflow-y-auto scrollbar-thin">
                    <form class="px-6 sm:px-8 py-6 space-y-8" @submit.prevent="saveSample">

                        <!-- Section 1: Identity & Photos -->
                        <div class="rounded-xl border border-stone-100 bg-[#fefefe] overflow-hidden">
                            <div class="px-5 py-3 bg-surface-container-highest/40 border-b border-stone-100 flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-primary text-[18px]">badge</span>
                                <h3 class="app-text font-bold text-on-surface uppercase tracking-[0.15em]">Identity & Photos</h3>
                            </div>
                            <div class="p-5 space-y-5">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div class="space-y-1.5">
                                        <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-0.5">Sample Number</label>
                                        <input
                                            v-model="sampleDraft.sample_number"
                                            type="text"
                                            placeholder="AB-001"
                                            class="w-full bg-surface-container-low border border-transparent rounded-lg py-3.5 px-4 text-sm focus:ring-2 focus:ring-primary-container focus:border-primary-container outline-none transition-all hover:bg-surface-container"
                                        />
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-0.5">Photos</label>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <button
                                                type="button"
                                                @click="openCameraModal"
                                                class="cursor-pointer inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-lg border border-primary-container/30 bg-primary-container/5 text-xs font-semibold text-primary shadow-sm hover:bg-primary-container/15 transition-all"
                                            >
                                                <span class="material-symbols-outlined text-[14px]">photo_camera</span>
                                                Camera
                                            </button>
                                            <button
                                                type="button"
                                                @click="triggerUploadPhotos"
                                                class="cursor-pointer inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-lg border border-stone-200 bg-white text-xs font-semibold text-[#1c1e21] shadow-sm hover:bg-stone-50 transition-all"
                                            >
                                                <span class="material-symbols-outlined text-[14px]">upload</span>
                                                Upload photo
                                            </button>
                                        </div>

                                        <input
                                            ref="takePhotoInputEl"
                                            type="file"
                                            accept="image/*"
                                            capture="environment"
                                            class="hidden"
                                            @change="addDraftImages"
                                        />
                                        <input
                                            ref="uploadPhotosInputEl"
                                            type="file"
                                            accept="image/*"
                                            multiple
                                            class="hidden"
                                            @change="addDraftImages"
                                        />

                                        <!-- Photo Grid -->
                                        <div v-if="(sampleDraft.media?.length ?? 0) || sampleDraft.images.length" class="flex flex-wrap gap-2.5 pt-2">
                                            <div v-for="(media, mediaIndex) in (sampleDraft.media ?? [])" :key="`media-${media.id}`" class="relative w-[72px] h-[72px] rounded-xl overflow-hidden border border-stone-200 bg-stone-50 shadow-sm ring-1 ring-black/5 group">
                                                <img
                                                    :src="media.url"
                                                    :alt="media.name"
                                                    class="w-full h-full object-cover cursor-zoom-in hover:scale-110 transition-transform duration-200"
                                                    @click="openLightbox(media.url, media.name)"
                                                />
                                                <button
                                                    type="button"
                                                    @click.stop="removeDraftMedia(mediaIndex)"
                                                    class="cursor-pointer absolute top-1 right-1 w-7 h-7 rounded-full bg-white/95 flex items-center justify-center ring-1 ring-black/10 shadow-sm hover:bg-white transition-all"
                                                    aria-label="Remove photo"
                                                >
                                                    <span class="material-symbols-outlined text-black/70 text-[16px]">delete</span>
                                                </button>
                                            </div>

                                            <div v-for="(imageItem, fileIndex) in sampleDraft.images" :key="`file-${fileIndex}-${imageItem.file?.name}`" class="relative w-[72px] h-[72px] rounded-xl overflow-hidden border border-stone-200 bg-stone-50 shadow-sm ring-1 ring-black/5 group">
                                                <img
                                                    :src="imageItem.previewUrl"
                                                    :alt="imageItem.file?.name"
                                                    class="w-full h-full object-cover cursor-zoom-in hover:scale-110 transition-transform duration-200"
                                                    @click="openLightbox(imageItem.previewUrl, imageItem.file?.name)"
                                                />
                                                <button
                                                    type="button"
                                                    @click.stop="removeDraftImage(fileIndex)"
                                                    class="cursor-pointer absolute top-1 right-1 w-7 h-7 rounded-full bg-white/95 flex items-center justify-center ring-1 ring-black/10 shadow-sm hover:bg-white transition-all"
                                                    aria-label="Remove photo"
                                                >
                                                    <span class="material-symbols-outlined text-black/70 text-[16px]">delete</span>
                                                </button>
                                            </div>
                                        </div>
                                        <p v-else class="text-[10px] text-stone-400 font-medium italic pt-1">No photos yet — capture or upload to attach.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Location -->
                        <div class="rounded-xl border border-stone-100 bg-[#fefefe] overflow-hidden">
                            <div class="px-5 py-3 bg-surface-container-highest/40 border-b border-stone-100 flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-primary text-[18px]">location_on</span>
                                <h3 class="app-text font-bold text-on-surface uppercase tracking-[0.15em]">Location</h3>
                            </div>
                            <div class="p-5">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div class="md:col-span-2 space-y-1.5">
                                        <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-0.5">Building / Area</label>
                                        <input
                                            v-model="sampleDraft.building_area"
                                            type="text"
                                            placeholder="Building A / Boiler Room"
                                            class="w-full bg-surface-container-low border border-transparent rounded-lg py-3.5 px-4 text-sm focus:ring-2 focus:ring-primary-container focus:border-primary-container outline-none transition-all hover:bg-surface-container"
                                        />
                                    </div>
                                    <div class="md:col-span-2 space-y-1.5">
                                        <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-0.5">Specific Location</label>
                                        <input
                                            v-model="sampleDraft.location"
                                            type="text"
                                            placeholder="North wall service chase"
                                            class="w-full bg-surface-container-low border border-transparent rounded-lg py-3.5 px-4 text-sm focus:ring-2 focus:ring-primary-container focus:border-primary-container outline-none transition-all hover:bg-surface-container"
                                        />
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-0.5">Surface</label>
                                        <input
                                            v-model="sampleDraft.surface"
                                            type="text"
                                            placeholder="Lagged pipe elbow"
                                            class="w-full bg-surface-container-low border border-transparent rounded-lg py-3.5 px-4 text-sm focus:ring-2 focus:ring-primary-container focus:border-primary-container outline-none transition-all hover:bg-surface-container"
                                        />
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-0.5">Material</label>
                                        <input
                                            v-model="sampleDraft.material"
                                            type="text"
                                            placeholder="Thermal insulation"
                                            class="w-full bg-surface-container-low border border-transparent rounded-lg py-3.5 px-4 text-sm focus:ring-2 focus:ring-primary-container focus:border-primary-container outline-none transition-all hover:bg-surface-container"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Material Assessment -->
                        <div class="rounded-xl border border-stone-100 bg-[#fefefe] overflow-hidden">
                            <div class="px-5 py-3 bg-surface-container-highest/40 border-b border-stone-100 flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-primary text-[18px]">labs</span>
                                <h3 class="app-text font-bold text-on-surface uppercase tracking-[0.15em]">Material Assessment</h3>
                            </div>
                            <div class="p-5">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div class="space-y-1.5">
                                        <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-0.5">Hazardous Material</label>
                                        <input
                                            v-model="sampleDraft.hazardous_material"
                                            type="text"
                                            placeholder="Amosite"
                                            class="w-full bg-surface-container-low border border-transparent rounded-lg py-3.5 px-4 text-sm focus:ring-2 focus:ring-primary-container focus:border-primary-container outline-none transition-all hover:bg-surface-container"
                                        />
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-0.5">Approx. Qty (m²)</label>
                                        <input
                                            v-model="sampleDraft.quantity"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            placeholder="6.2"
                                            class="w-full bg-surface-container-low border border-transparent rounded-lg py-3.5 px-4 text-sm focus:ring-2 focus:ring-primary-container focus:border-primary-container outline-none transition-all hover:bg-surface-container"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 4: Risk Assessment -->
                        <div class="rounded-xl border border-stone-100 bg-[#fefefe] overflow-hidden">
                            <div class="px-5 py-3 bg-surface-container-highest/40 border-b border-stone-100 flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-primary text-[18px]">shield</span>
                                <h3 class="app-text font-bold text-on-surface uppercase tracking-[0.15em]">Risk Assessment</h3>
                            </div>
                            <div class="p-5">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div class="space-y-1.5">
                                        <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-0.5">Condition</label>
                                        <div class="relative">
                                            <select
                                                v-model="sampleDraft.condition"
                                                class="w-full bg-surface-container-low border border-transparent rounded-lg py-3.5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none appearance-none cursor-pointer hover:bg-surface-container transition-all"
                                            >
                                                <option value="">Select condition</option>
                                                <option value="Good">Good</option>
                                                <option value="Fair">Fair</option>
                                                <option value="Poor">Poor</option>
                                            </select>
                                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 text-[18px] pointer-events-none">expand_more</span>
                                        </div>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-0.5">Readily Accessible</label>
                                        <div class="relative">
                                            <select
                                                v-model="sampleDraft.access_level"
                                                class="w-full bg-surface-container-low border border-transparent rounded-lg py-3.5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none appearance-none cursor-pointer hover:bg-surface-container transition-all"
                                            >
                                                <option value="">Select access level</option>
                                                <option value="Yes">Yes</option>
                                                <option value="No">No</option>
                                            </select>
                                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 text-[18px] pointer-events-none">expand_more</span>
                                        </div>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-0.5">Friable</label>
                                        <div class="relative">
                                            <select
                                                v-model="sampleDraft.friability"
                                                class="w-full bg-surface-container-low border border-transparent rounded-lg py-3.5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none appearance-none cursor-pointer hover:bg-surface-container transition-all"
                                            >
                                                <option value="">Select friability</option>
                                                <option value="Friable">Friable</option>
                                                <option value="Non-friable">Non-friable</option>
                                            </select>
                                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 text-[18px] pointer-events-none">expand_more</span>
                                        </div>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-0.5">Hazard Priority</label>
                                        <div class="relative">
                                            <select
                                                v-model="sampleDraft.hazard_priority"
                                                class="w-full bg-surface-container-low border border-transparent rounded-lg py-3.5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none appearance-none cursor-pointer hover:bg-surface-container transition-all"
                                            >
                                                <option value="">Select priority</option>
                                                <option value="Low">Low</option>
                                                <option value="Medium">Medium</option>
                                                <option value="High">High</option>
                                            </select>
                                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 text-[18px] pointer-events-none">expand_more</span>
                                        </div>
                                    </div>
                                    <div class="md:col-span-2 space-y-1.5">
                                        <label class="block app-text font-semibold text-stone-400 uppercase tracking-widest ml-0.5">Comments</label>
                                        <textarea
                                            v-model="sampleDraft.comments"
                                            rows="3"
                                            placeholder="Describe access risk, condition, or maintenance disturbance..."
                                            class="w-full bg-surface-container-low border border-transparent rounded-lg py-3.5 px-4 text-sm focus:ring-2 focus:ring-primary-container outline-none transition-all resize-none hover:bg-surface-container"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div class="px-6 sm:px-8 py-4 border-t border-stone-100 flex justify-between items-center bg-[#fafafa] shrink-0">
                    <button
                        type="button"
                        @click="closeSampleModal"
                        class="cursor-pointer px-5 py-2.5 text-sm font-semibold text-stone-500 hover:text-stone-700 hover:bg-stone-100 rounded-lg transition-all"
                    >
                        Cancel
                    </button>
                    <Button type="button" @click="saveSample">
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px]">check</span>
                            {{ editingSampleIndex === null ? 'Add Sample' : 'Save Changes' }}
                        </span>
                    </Button>
                </div>
            </div>
        </div>
    </div>

    <div v-if="isCameraModalOpen" class="fixed inset-0 z-[110]">
        <div class="absolute inset-0 bg-white/70 backdrop-blur-sm" @click="closeCameraModal"></div>

        <div class="absolute inset-0 flex items-center justify-center p-3 sm:p-6 md:p-0 pointer-events-none">
            <div class="w-full max-w-[880px] bg-[#fdfdfd] rounded-2xl shadow-2xl overflow-hidden pointer-events-auto flex flex-col max-h-[92vh] ring-1 ring-black/10 md:w-screen md:h-screen md:max-w-none md:max-h-none md:rounded-none md:ring-0">
                <!-- Header -->
                <div class="px-6 sm:px-8 py-4 flex justify-between items-center shrink-0 border-b border-black/10">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-black/10 flex items-center justify-center">
                            <span class="material-symbols-outlined text-black text-lg">photo_camera</span>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-black leading-tight">Capture Photos</h2>
                            <p class="text-[10px] font-medium text-black/40 uppercase tracking-widest">Camera</p>
                        </div>
                    </div>
                    <button
                        type="button"
                        @click="closeCameraModal"
                        class="cursor-pointer w-9 h-9 flex items-center justify-center rounded-lg bg-black/10 hover:bg-black/20 transition-all"
                    >
                        <span class="material-symbols-outlined text-black/70 text-lg">close</span>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto scrollbar-thin md:flex md:flex-col md:min-h-0">
                    <!-- Video Preview -->
                    <div class="p-4 sm:p-6 md:p-6 md:pb-3 md:flex-1 md:min-h-0">
                        <div class="rounded-xl overflow-hidden bg-black ring-1 ring-white/10 min-h-[240px] md:h-full">
                            <div class="relative w-full aspect-video md:aspect-auto md:h-full">
                                <video
                                    ref="videoEl"
                                    class="absolute inset-0 w-full h-full object-cover bg-black transform-gpu"
                                    autoplay
                                    playsinline
                                    muted
                                    @loadedmetadata="handleVideoLoadedMetadata"
                                ></video>

                                <!-- Status Overlay -->
                                <div v-if="!isCameraActive" class="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-black/60">
                                    <div class="w-14 h-14 rounded-full bg-white/10 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-white/50 text-2xl">{{ cameraError ? 'videocam_off' : 'hourglass_top' }}</span>
                                    </div>
                                    <p class="text-xs font-semibold text-white/60">
                                        {{ cameraError ? 'Camera unavailable' : 'Starting camera…' }}
                                    </p>
                                </div>
                                <div v-else-if="!isCameraPreviewReady" class="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-black/40">
                                    <div class="w-10 h-10 rounded-full border-2 border-white/30 border-t-white animate-spin"></div>
                                    <p class="text-xs font-semibold text-white/60">Loading preview…</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- <div v-if="cameraError || cameraPermissionState || cameraDebug" class="px-4 sm:px-6 pb-2 space-y-1">
                        <p v-if="cameraError" class="text-xs text-red-600 font-semibold flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[14px]">warning</span>
                            {{ cameraError }}
                        </p>
                        <p v-if="cameraPermissionState" class="text-[10px] text-stone-500 font-medium">
                            Camera permission: {{ cameraPermissionState }}
                        </p>
                        <p v-if="cameraDebug" class="text-[10px] text-stone-400 font-medium">
                            {{ cameraDebug.origin }} · secure: {{ cameraDebug.secureContext ? 'yes' : 'no' }} · getUserMedia: {{ cameraDebug.hasGetUserMedia ? 'yes' : 'no' }}
                        </p>
                    </div> -->

                    <!-- Camera Selector -->
                    <div v-if="videoDevices.length > 1" class="px-4 sm:px-6 pb-3">
                        <div class="flex items-center gap-3 px-4 py-2.5 rounded-lg bg-surface-container-low border border-stone-200">
                            <span class="material-symbols-outlined text-stone-500 text-[16px]">videocam</span>
                            <select
                                :value="selectedVideoDeviceId"
                                @change="selectCameraDevice"
                                class="flex-1 bg-transparent text-[#1c1e21] text-sm outline-none appearance-none cursor-pointer border-none"
                            >
                                <option value="" class="bg-[#fdfdfd]">Auto</option>
                                <option v-for="(device, deviceIndex) in videoDevices" :key="device.deviceId" :value="device.deviceId" class="bg-[#fdfdfd]">
                                    {{ device.label || `Camera ${deviceIndex + 1}` }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Control Toolbar -->
                    <div class="px-4 sm:px-6 pb-4">
                        <div class="flex items-center justify-center gap-3 py-3">
                            <!-- Secondary controls -->
                            <button
                                type="button"
                                @click="triggerTakePhotoFallback"
                                class="cursor-pointer flex flex-col items-center gap-1 px-3 py-2 rounded-xl bg-black/5 hover:bg-black/10 transition-all group"
                            >
                                <span class="material-symbols-outlined text-black/60 group-hover:text-black text-lg transition-colors">add_photo_alternate</span>
                            </button>

                            <!-- Primary Capture Button -->
                            <button
                                type="button"
                                @click="capturePhoto"
                                :disabled="!isCameraActive"
                                class="cursor-pointer w-16 h-16 rounded-full bg-white flex items-center justify-center shadow-lg hover:scale-105 active:scale-95 transition-all disabled:opacity-30 disabled:cursor-not-allowed mx-2 ring-4 ring-black/20"
                            >
                                <div class="w-[52px] h-[52px] rounded-full border-2 border-black/20"></div>
                            </button>

                                                        <button
                                type="button"
                                @click="toggleFacingMode"
                                class="cursor-pointer flex flex-col items-center gap-1 px-3 py-2 rounded-xl bg-black/5 hover:bg-black/10 transition-all group"
                            >
                                <span class="material-symbols-outlined text-black/60 group-hover:text-black text-lg transition-colors">flip_camera_ios</span>
                            </button>
                        </div>
                    </div>

                    <!-- Captured Photos Strip -->
                    <div v-if="sampleDraft.images.length" class="px-4 sm:px-6 pb-4">
                        <div class="rounded-xl bg-black/5 border border-black/5 p-3">
                            <p class="text-[10px] font-bold text-black/30 uppercase tracking-widest mb-2">
                                Captured · {{ sampleDraft.images.length }} {{ sampleDraft.images.length === 1 ? 'photo' : 'photos' }}
                            </p>
                            <div class="flex flex-wrap gap-2">
                                <div v-for="(imageItem, fileIndex) in sampleDraft.images" :key="`cam-file-${fileIndex}-${imageItem.file?.name}`" class="relative w-[68px] h-[68px] rounded-lg overflow-hidden ring-1 ring-black/10 group">
                                    <img
                                        :src="imageItem.previewUrl"
                                        :alt="imageItem.file?.name"
                                        class="w-full h-full object-cover cursor-zoom-in hover:scale-110 transition-transform duration-200"
                                        @click="openLightbox(imageItem.previewUrl, imageItem.file?.name)"
                                    />
                                    <button
                                        type="button"
                                        @click.stop="removeDraftImage(fileIndex)"
                                        class="cursor-pointer absolute top-1 right-1 w-7 h-7 rounded-full bg-white/95 flex items-center justify-center ring-1 ring-black/10 shadow-sm hover:bg-white transition-all"
                                        aria-label="Remove photo"
                                    >
                                        <span class="material-symbols-outlined text-black/70 text-[16px]">delete</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 sm:px-8 py-4 border-t border-black/5 flex justify-between items-center shrink-0">
                    <p class="text-[10px] text-black/20 font-medium">
                        {{ sampleDraft.images.length }} {{ sampleDraft.images.length === 1 ? 'photo' : 'photos' }} captured
                    </p>
                    <Button type="button" @click="closeCameraModal">
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px]">check</span>
                            Done
                        </span>
                    </Button>
                </div>
            </div>
        </div>
    </div>

    <div v-if="isLightboxOpen" class="fixed inset-0 z-[200]">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-[2px]" @click="closeLightbox"></div>

        <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
            <div class="pointer-events-auto w-full max-w-5xl">
                <div class="flex justify-end mb-3">
                    <button
                        type="button"
                        @click="closeLightbox"
                        class="px-4 py-2 bg-white/90 border border-white/20 text-[#1c1e21] text-xs font-semibold rounded-lg hover:bg-white transition-all shadow-sm"
                    >
                        Close
                    </button>
                </div>
                <div class="rounded-2xl overflow-hidden border border-white/10 bg-black">
                    <img :src="lightboxUrl" :alt="lightboxAlt" class="w-full max-h-[80vh] object-contain bg-black" />
                </div>
            </div>
        </div>
    </div>

    <div v-if="isDeleteSampleConfirmOpen" class="fixed inset-0 z-[210]">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-[2px]" @click="closeDeleteSampleConfirm"></div>

        <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
            <div class="pointer-events-auto w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden border border-stone-200">
                <div class="px-6 py-5 border-b border-stone-100 bg-[#fdfdfd]">
                    <h3 class="text-base font-bold text-[#1c1e21]">Delete sample?</h3>
                    <p class="text-sm text-stone-500 mt-1">This will remove the sample from this audit when you save.</p>
                </div>

                <div class="px-6 py-5 flex justify-end gap-2">
                    <button
                        type="button"
                        @click="closeDeleteSampleConfirm"
                        class="px-4 py-2 rounded-lg border border-stone-200 bg-white text-sm font-semibold text-[#1c1e21] hover:bg-stone-50 transition-all"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="confirmDeleteSample"
                        class="px-4 py-2 rounded-lg bg-red-600 text-sm font-semibold text-white hover:bg-red-700 transition-all"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch, nextTick } from "vue";
import L from "leaflet";
import "leaflet/dist/leaflet.css";

const props = defineProps({
    modelValue: {
        type: Object,
        default: () => ({ lat: -36.757, lng: 144.2794 }),
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:modelValue"]);

const mapContainer = ref(null);
let map = null;
let marker = null;

onMounted(() => {
    nextTick(() => {
        initMap();
    });
});

function initMap() {
    if (!mapContainer.value) return;

    const lat = props.modelValue?.lat || -36.757;
    const lng = props.modelValue?.lng || 144.2794;

    map = L.map(mapContainer.value, {
        center: [lat, lng],
        zoom: 13,
        zoomControl: true,
    });

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution:
            '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
    }).addTo(map);

    // Custom marker icon to fix default icon issue
    const markerIcon = L.divIcon({
        className: "custom-map-marker",
        html: '<div class="marker-pin"></div>',
        iconSize: [30, 42],
        iconAnchor: [15, 42],
    });

    if (props.modelValue?.lat && props.modelValue?.lng) {
        marker = L.marker([lat, lng], {
            icon: markerIcon,
            draggable: !props.disabled,
        }).addTo(map);
        marker.on("dragend", onMarkerDrag);
    }

    if (!props.disabled) {
        map.on("click", onMapClick);
    }
}

function onMapClick(e) {
    const { lat, lng } = e.latlng;
    setMarker(lat, lng);
    emit("update:modelValue", {
        lat: parseFloat(lat.toFixed(7)),
        lng: parseFloat(lng.toFixed(7)),
    });
}

function onMarkerDrag() {
    const { lat, lng } = marker.getLatLng();
    emit("update:modelValue", {
        lat: parseFloat(lat.toFixed(7)),
        lng: parseFloat(lng.toFixed(7)),
    });
}

function setMarker(lat, lng) {
    const markerIcon = L.divIcon({
        className: "custom-map-marker",
        html: '<div class="marker-pin"></div>',
        iconSize: [30, 42],
        iconAnchor: [15, 42],
    });

    if (marker) {
        marker.setLatLng([lat, lng]);
    } else {
        marker = L.marker([lat, lng], {
            icon: markerIcon,
            draggable: !props.disabled,
        }).addTo(map);
        marker.on("dragend", onMarkerDrag);
    }
}

function clearMarker() {
    if (marker && map) {
        map.removeLayer(marker);
        marker = null;
    }
    emit("update:modelValue", { lat: null, lng: null });
}

watch(
    () => props.modelValue,
    (val) => {
        if (val?.lat && val?.lng && map) {
            setMarker(val.lat, val.lng);
            map.setView([val.lat, val.lng], map.getZoom());
        }
    },
    { deep: true },
);

defineExpose({ clearMarker });
</script>

<template>
    <div class="space-y-2">
        <div class="flex items-center justify-between">
            <label
                class="app-text font-bold tracking-widest text-on-surface-variant uppercase ml-1"
                >Pin Location</label
            >
            <button
                v-if="modelValue?.lat && modelValue?.lng"
                type="button"
                class="text-[10px] font-bold text-error uppercase tracking-widest hover:underline"
                @click="clearMarker"
            >
                Clear Pin
            </button>
        </div>
        <div
            ref="mapContainer"
            class="w-full h-80 rounded-lg overflow-hidden border border-outline-variant/20"
        ></div>
        <div
            v-if="modelValue?.lat && modelValue?.lng"
            class="flex items-center gap-3 text-[10px] text-on-surface-variant/60 font-mono"
        >
            <span>Lat: {{ modelValue.lat }}</span>
            <span>Lng: {{ modelValue.lng }}</span>
        </div>
        <p v-else class="text-[10px] text-on-surface-variant/40 italic">
            Click on the map to pin a location
        </p>
    </div>
</template>

<style>
.custom-map-marker {
    background: none;
    border: none;
}
.marker-pin {
    width: 30px;
    height: 30px;
    border-radius: 50% 50% 50% 0;
    background: #ea580c;
    position: absolute;
    transform: rotate(-45deg);
    left: 50%;
    top: 50%;
    margin: -15px 0 0 -15px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}
.marker-pin::after {
    content: "";
    width: 14px;
    height: 14px;
    margin: 8px 0 0 8px;
    background: #fff;
    position: absolute;
    border-radius: 50%;
}
</style>

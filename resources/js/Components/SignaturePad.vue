<script setup>
import { ref, onMounted, watch, nextTick } from "vue";

const props = defineProps({
    modelValue: {
        type: String,
        default: null,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    height: {
        type: Number,
        default: 150,
    },
});

const emit = defineEmits(["update:modelValue"]);

const canvasRef = ref(null);
const isDrawing = ref(false);
const lastX = ref(0);
const lastY = ref(0);

let ctx = null;

const getCoordinates = (e) => {
    const rect = canvasRef.value.getBoundingClientRect();
    if (e.touches) {
        return {
            x: e.touches[0].clientX - rect.left,
            y: e.touches[0].clientY - rect.top,
        };
    }
    return {
        x: e.clientX - rect.left,
        y: e.clientY - rect.top,
    };
};

const startDrawing = (e) => {
    if (props.disabled) return;
    e.preventDefault();
    isDrawing.value = true;
    const { x, y } = getCoordinates(e);
    lastX.value = x;
    lastY.value = y;
};

const draw = (e) => {
    if (!isDrawing.value || props.disabled) return;
    e.preventDefault();
    const { x, y } = getCoordinates(e);
    ctx.beginPath();
    ctx.moveTo(lastX.value, lastY.value);
    ctx.lineTo(x, y);
    ctx.stroke();
    lastX.value = x;
    lastY.value = y;
};

const stopDrawing = () => {
    if (!isDrawing.value) return;
    isDrawing.value = false;
    emitSignature();
};

const emitSignature = () => {
    if (!canvasRef.value || props.disabled) return;
    const dataUrl = canvasRef.value.toDataURL("image/png");
    emit("update:modelValue", dataUrl);
};

const clear = () => {
    if (!ctx || props.disabled) return;
    ctx.clearRect(0, 0, canvasRef.value.width, canvasRef.value.height);
    emit("update:modelValue", null);
};

const loadFromValue = () => {
    if (!ctx || !canvasRef.value || !props.modelValue) return;
    const img = new Image();
    img.onload = () => {
        ctx.clearRect(0, 0, canvasRef.value.width, canvasRef.value.height);
        ctx.drawImage(img, 0, 0, canvasRef.value.width, canvasRef.value.height);
    };
    img.src = props.modelValue;
};

onMounted(() => {
    nextTick(() => {
        if (!canvasRef.value) return;
        ctx = canvasRef.value.getContext("2d");
        ctx.strokeStyle = "rgb(31, 41, 55)"; // gray-800 - visible on white bg
        ctx.lineWidth = 2;
        ctx.lineCap = "round";
        ctx.lineJoin = "round";

        const parent = canvasRef.value.parentElement;
        const width = parent ? parent.offsetWidth : 400;
        const h = props.height;
        canvasRef.value.width = width;
        canvasRef.value.height = h;

        if (props.modelValue) {
            loadFromValue();
        }
    });
});

watch(() => props.modelValue, (val) => {
    if (val && ctx) loadFromValue();
    else if (!val && ctx && canvasRef.value) {
        ctx.clearRect(0, 0, canvasRef.value.width, canvasRef.value.height);
    }
});
</script>

<template>
    <div class="signature-pad-wrapper">
        <div
            class="relative w-full border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 overflow-hidden"
            :class="{ 'opacity-60 cursor-not-allowed': disabled }"
            :style="{ height: height + 'px' }"
        >
            <canvas
                ref="canvasRef"
                class="block w-full h-full touch-none"
                @mousedown="startDrawing"
                @mousemove="draw"
                @mouseup="stopDrawing"
                @mouseleave="stopDrawing"
                @touchstart.prevent="startDrawing"
                @touchmove.prevent="draw"
                @touchend.prevent="stopDrawing"
            ></canvas>
        </div>
        <button
            v-if="!disabled"
            type="button"
            @click="clear"
            class="mt-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200"
        >
            Clear signature
        </button>
    </div>
</template>

<template>
    <transition name="fade">
        <div v-if="show" class="toast-notification">
            <slot />
        </div>
    </transition>
</template>

<script setup>
import { ref, watch, onMounted } from "vue";
const props = defineProps({
    modelValue: Boolean,
    duration: { type: Number, default: 3000 },
});
const emit = defineEmits(["update:modelValue"]);
const show = ref(props.modelValue);

watch(
    () => props.modelValue,
    (val) => {
        show.value = val;
        if (val) {
            setTimeout(() => emit("update:modelValue", false), props.duration);
        }
    }
);
</script>

<style scoped>
.toast-notification {
    position: fixed;
    top: 30px;
    right: 30px;
    background: #19a061;
    color: #fff;
    padding: 16px 32px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    z-index: 9999;
    font-weight: bold;
    font-size: 1rem;
}
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>

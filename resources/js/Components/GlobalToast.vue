<template>
  <transition name="fade">
    <div v-if="show" :class="['global-toast', typeClass]" role="alert" aria-live="assertive">
      <slot />
    </div>
  </transition>
</template>
<script setup>
import { computed, watch, ref } from 'vue';
const props = defineProps({
  show: Boolean,
  type: { type: String, default: 'success' }, // success, error, info
  duration: { type: Number, default: 3000 },
});
const emit = defineEmits(['update:show']);
const typeClass = computed(() => {
  if (props.type === 'error') return 'toast-error';
  if (props.type === 'info') return 'toast-info';
  return 'toast-success';
});
const timer = ref(null);
watch(() => props.show, val => {
  if (val) {
    clearTimeout(timer.value);
    timer.value = setTimeout(() => emit('update:show', false), props.duration);
  }
});
</script>
<style scoped>
.global-toast {
  position: fixed;
  top: 30px;
  right: 30px;
  min-width: 220px;
  max-width: 350px;
  color: #fff;
  padding: 16px 32px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
  z-index: 9999;
  font-weight: bold;
  font-size: 1rem;
  display: flex;
  align-items: center;
}
.toast-success { background: #19a061; }
.toast-error { background: #d33; }
.toast-info { background: #3498db; }
.fade-enter-active, .fade-leave-active { transition: opacity 0.5s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style> 
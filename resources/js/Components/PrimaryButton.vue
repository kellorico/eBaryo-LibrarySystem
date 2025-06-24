<script setup>
defineProps({
  disabled: {
    type: Boolean,
    default: false
  },
  isLoading: {
    type: Boolean,
    default: false
  },
  type: {
    type: String,
    default: 'submit'
  }
});
</script>

<template>
  <button
    :type="type"
    class="btn btn-success-green w-100 d-flex justify-content-center align-items-center btn-ripple"
    :disabled="disabled"
  >
    <span v-if="isLoading" class="spinner-border spinner-border-sm me-2 spinner-green" role="status" aria-hidden="true"></span>
    <slot />
  </button>
</template>

<style scoped>
.btn-success-green {
  background: linear-gradient(90deg, #198754 60%, #43c59e 100%);
  color: #fff;
  border: none;
  font-weight: 700;
  border-radius: 0.9rem;
  box-shadow: 0 2px 12px rgba(25, 135, 84, 0.10);
  transition: background 0.2s, box-shadow 0.2s, transform 0.2s;
  padding: 0.85rem 1.4rem;
  font-size: 1.13rem;
  letter-spacing: 0.01em;
}
.btn-success-green:hover, .btn-success-green:focus {
  background: linear-gradient(90deg, #157347 60%, #198754 100%);
  box-shadow: 0 6px 24px #43c59e33, 0 2px 8px #19875422;
  transform: translateY(-2px) scale(1.04);
}
.btn-ripple {
  position: relative;
  overflow: hidden;
}
.btn-ripple:active::after {
  content: '';
  position: absolute;
  left: 50%; top: 50%;
  width: 200%; height: 200%;
  background: rgba(25,135,84,0.13);
  border-radius: 50%;
  transform: translate(-50%, -50%) scale(0);
  animation: ripple 0.5s linear;
  z-index: 1;
}
@keyframes ripple {
  to { transform: translate(-50%, -50%) scale(1); opacity: 0; }
}
.spinner-green {
  color: #198754;
  border-width: 0.18em;
}
</style>
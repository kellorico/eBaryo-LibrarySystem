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
    class="btn btn-success w-100 d-flex justify-content-center align-items-center"
    :disabled="disabled"
  >
    <span v-if="isLoading" class="spinner-border spinner-border-sm me-2" role="status" area-hidden="true"></span>
    <slot />
  </button>
</template>
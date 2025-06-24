<template>
  <div class="input-group mb-3" style="max-width: 350px;">
    <input
      type="text"
      class="form-control"
      :placeholder="placeholder"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
    />
    <button
      v-if="modelValue"
      class="btn btn-outline-secondary"
      type="button"
      @click="$emit('update:modelValue', '')"
      title="Clear"
    >
      <i class="fas fa-times"></i>
    </button>
  </div>
</template>

<script setup>
defineProps({
  modelValue: String,
  placeholder: {
    type: String,
    default: 'Search users...'
  }
});
</script> 
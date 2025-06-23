<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  id: {
    type: String,
    required: true
  },
  label: {
    type: String,
    required: true
  },
  type: {
    type: String,
    default: 'text'
  },
  modelValue: {
    type: [String, Number],
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: ''
  },
  icon: {
    type: String,
    default: '' // Bootstrap icon class, e.g. 'bi bi-person'
  }
});

const emit = defineEmits(['update:modelValue']);

// Show/hide password logic
const showPassword = ref(false);
const inputType = computed(() => {
  if (props.type === 'password') {
    return showPassword.value ? 'text' : 'password';
  }
  return props.type;
});

const toggleShowPassword = () => {
  showPassword.value = !showPassword.value;
};
</script>

<template>
  <div class="mb-3">
    <div class="input-group" :class="{ 'has-validation': error, 'input-group-error': error }">
      <span v-if="icon" class="input-group-text bg-white border-end-0">
        <i :class="icon"></i>
      </span>
      <input
        :id="id"
        :type="type === 'password' ? inputType : type"
        :value="modelValue"
        @input="emit('update:modelValue', $event.target.value)"
        :required="required"
        class="form-control"
        :class="[icon ? 'border-start-0' : '', error ? 'is-invalid' : '']"
        :placeholder="label"
        autocomplete="on"
      />
      <button
        v-if="type === 'password'"
        class="btn btn-outline-secondary"
        type="button"
        tabindex="-1"
        @click="toggleShowPassword"
        :aria-label="showPassword ? 'Hide password' : 'Show password'"
        :class="{ 'btn-error': error }"
      >
        <span v-if="showPassword">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M13.359 11.238l2.147 2.147a.5.5 0 0 1-.708.708l-2.147-2.147A7.027 7.027 0 0 1 8 14c-4.418 0-8-4-8-6s3.582-6 8-6c1.61 0 3.09.488 4.359 1.238l2.147-2.147a.5.5 0 1 1 .708.708l-2.147 2.147A7.027 7.027 0 0 1 16 8c0 2-3.582 6-8 6a7.027 7.027 0 0 1-4.359-1.238zM8 13c3.866 0 7-3.134 7-5s-3.134-5-7-5-7 3.134-7 5 3.134 5 7 5zm0-9a4 4 0 1 1 0 8 4 4 0 0 1 0-8zm0 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/></svg>
        </span>
        <span v-else>
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8s-3.582 6-8 6-8-4-8-6 3.582-6 8-6 8 4 8 6zm-8 5c3.866 0 7-3.134 7-5s-3.134-5-7-5-7 3.134-7 5 3.134 5 7 5zm0-9a4 4 0 1 1 0 8 4 4 0 0 1 0-8zm0 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/></svg>
        </span>
      </button>
    </div>
    <div v-if="error" class="input-error">
      <small>{{ error }}</small>
    </div>
  </div>
</template>

<style scoped>
.input-group-error {
  border: 1.5px solid #dc3545 !important;
  border-radius: 0.375rem;
}
.input-group-error .input-group-text,
.input-group-error .form-control,
.input-group-error .btn {
  border-color: #dc3545 !important;
  box-shadow: none !important;
}
.input-group-error .form-control:focus {
  border-color: #dc3545 !important;
  box-shadow: 0 0 0 0.2rem rgba(220,53,69,.15) !important;
}
.btn-error {
  border-color: #dc3545 !important;
  color: #dc3545 !important;
}
.input-error {
  color: #dc3545;
  font-size: 0.85rem;
  margin-top: 0.25rem;
  display: block;
}
</style>
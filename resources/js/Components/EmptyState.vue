<template>
  <div class="empty-state text-center py-5">
    <i :class="[icon, 'fa-3x', 'mb-3', 'text-muted']" aria-hidden="true"></i>
    <div class="fw-bold mb-2">{{ message }}</div>
    <button v-if="actionLabel" class="btn btn-primary mt-2" @click="onAction">{{ actionLabel }}</button>
  </div>
</template>
<script setup>
const props = defineProps({
  icon: { type: String, default: 'fa fa-box-open' },
  message: String,
  actionLabel: String,
  onAction: Function,
});
</script>
<style scoped>
.empty-state {
  color: #888;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 180px;
}
</style> 
<template>
  <div>
    <template v-if="type === 'card'">
      <div v-for="n in count" :key="n" class="skeleton-card mb-3"></div>
    </template>
    <template v-else-if="type === 'list'">
      <div v-for="n in count" :key="n" class="skeleton-list mb-2"></div>
    </template>
    <template v-else>
      <div v-for="n in count" :key="n" class="skeleton-generic mb-2"></div>
    </template>
  </div>
</template>
<script setup>
const props = defineProps({
  type: { type: String, default: 'card' },
  count: { type: Number, default: 4 },
});
</script>
<style scoped>
.skeleton-card {
  height: 260px;
  width: 100%;
  border-radius: 12px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 37%, #f0f0f0 63%);
  background-size: 400% 100%;
  animation: shimmer 1.2s infinite linear;
}
.skeleton-list {
  height: 32px;
  width: 100%;
  border-radius: 6px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 37%, #f0f0f0 63%);
  background-size: 400% 100%;
  animation: shimmer 1.2s infinite linear;
}
.skeleton-generic {
  height: 20px;
  width: 100%;
  border-radius: 4px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 37%, #f0f0f0 63%);
  background-size: 400% 100%;
  animation: shimmer 1.2s infinite linear;
}
@keyframes shimmer {
  0% { background-position: -400px 0; }
  100% { background-position: 400px 0; }
}
</style> 
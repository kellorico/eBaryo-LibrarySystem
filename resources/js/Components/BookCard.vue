<script setup>
defineProps({
  title: String,
  author: String,
  cover_image: String,
  published_year: [String, Number]
});

const emit = defineEmits(['click']);
</script>

<template>
  <div class="card h-100 shadow-sm cursor-pointer" @click="emit('click')">
    <img :src="cover_image" class="card-img-top" alt="Book Cover" style="height: 200px; object-fit: cover;">
    <div class="card-body">
      <h5 class="card-title mb-1">{{ title }}</h5>
      <p class="card-text text-muted mb-0">by {{ author }}</p>
      <small class="text-secondary">Published: {{ published_year }}</small>
    </div>
  </div>
</template>

<style scoped>
.cursor-pointer {
  cursor: pointer;
}
</style>

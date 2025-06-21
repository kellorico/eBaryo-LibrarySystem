<!-- resources/js/Components/BookReader.vue -->
<template>
  <div class="reader-container" ref="readerContainer">
    <div id="epub-reader" class="h-100 w-100"></div>
    <div class="controls">
      <button class="btn btn-secondary" @click="prevPage">‹ Prev</button>
      <button class="btn btn-secondary" @click="nextPage">Next ›</button>
      <button class="btn btn-danger" @click="$emit('close')">Close</button>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import ePub from 'epubjs';

const props = defineProps({
  file: String, // EPUB file path (relative to /storage/)
});

const readerContainer = ref(null);
let rendition = null;

const nextPage = () => {
  rendition?.next();
};

const prevPage = () => {
  rendition?.prev();
};

onMounted(() => {
  const book = ePub(`/storage/${props.file}`);
  rendition = book.renderTo('epub-reader', {
    width: '100%',
    height: '100%',
  });
  rendition.display();
});
</script>

<style scoped>
.reader-container {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  z-index: 2000;
  background: #fff;
  display: flex;
  flex-direction: column;
}
.controls {
  position: absolute;
  top: 1rem;
  right: 1rem;
  display: flex;
  gap: 0.5rem;
}
</style>

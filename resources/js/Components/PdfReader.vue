
<script setup>
import { computed } from 'vue';

const props = defineProps({
  bookId: Number,
  file: String,
});

const emit = defineEmits(['close', 'bookmark']);

const handleBookmark = () => {
  emit('bookmark', props.bookId);
};

const pdfUrl = computed(() => {
  if (!props.file) return '';

  if (props.file.startsWith('http') || props.file.startsWith('/storage/')) {
    return props.file;
  }

  return `/storage/${props.file}`;
});

</script>

<style scoped>
iframe {
  border: none;
}
</style>


<template>
  <div class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-75 d-flex justify-content-center align-items-center" style="z-index: 1050;">
    <div class="bg-white rounded shadow w-100 h-100 position-relative">
      <button class="btn btn-danger position-absolute top-0 end-0 m-3 z-50" @click="$emit('close')">
        <i class="fas fa-times"></i> Close
      </button>
      <button class="btn btn-primary position-absolute top-0 start-0 m-3 z-50" @click="handleBookmark">
        <i class="fas fa-bookmark"></i> Bookmark
      </button>
      <iframe
        v-if="file"
        :src="pdfUrl"
        class="w-100 h-100 border-0 rounded-bottom"
      ></iframe>

      <div v-else class="text-center mt-5 text-muted">
        PDF file not found.
      </div>
    </div>
  </div>
</template>

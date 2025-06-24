<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue';

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

const fileName = computed(() => {
  if (!props.file) return '';
  const parts = props.file.split('/');
  return parts[parts.length - 1];
});

const loading = ref(true);

const onLoad = () => {
  loading.value = false;
};

watch(() => props.file, () => {
  loading.value = true;
});

const handleDownload = () => {
  const link = document.createElement('a');
  link.href = pdfUrl.value;
  link.download = fileName.value || 'file.pdf';
  link.target = '_blank';
  link.click();
};

const handleBackdrop = (e) => {
  if (e.target.classList.contains('pdf-modal-bg')) {
    emit('close');
  }
};

const handleKeydown = (e) => {
  if (e.key === 'Escape') {
    emit('close');
  } else if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'd') {
    e.preventDefault();
    handleDownload();
  } else if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'b') {
    e.preventDefault();
    handleBookmark();
  }
};

onMounted(() => {
  window.addEventListener('keydown', handleKeydown);
});
onBeforeUnmount(() => {
  window.removeEventListener('keydown', handleKeydown);
});
</script>

<style scoped>
.pdf-modal-bg {
  position: fixed;
  top: 0; left: 0; width: 100vw; height: 100vh;
  background: rgba(30, 41, 59, 0.7);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1050;
  backdrop-filter: blur(6px);
  transition: background 0.3s;
}
.pdf-modal {
  background: rgba(255,255,255,0.92);
  box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
  border-radius: 0;
  width: 100vw;
  max-width: 100vw;
  height: 100vh;
  max-height: 100vh;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  position: relative;
  animation: modalIn 0.35s cubic-bezier(.4,2,.6,1) both;
}
@keyframes modalIn {
  0% { opacity: 0; transform: scale(0.98) translateY(30px); }
  100% { opacity: 1; transform: scale(1) translateY(0); }
}
.pdf-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 1.5rem;
  background: rgba(255,255,255,0.7);
  border-bottom: 1px solid #e5e7eb;
  box-shadow: 0 2px 8px rgba(0,0,0,0.03);
  z-index: 2;
  position: sticky;
  top: 0;
}
.pdf-title {
  font-weight: 600;
  font-size: 1.1rem;
  color: #1e293b;
  letter-spacing: 0.01em;
  max-width: 60vw;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.pdf-actions {
  display: flex;
  gap: 0.5rem;
}
.pdf-btn {
  background: rgba(255,255,255,0.8);
  border: none;
  border-radius: 0.5rem;
  padding: 0.7rem 1rem;
  font-size: 1.1rem;
  color: #334155;
  cursor: pointer;
  transition: background 0.2s, box-shadow 0.2s, color 0.2s;
  box-shadow: 0 1px 4px rgba(0,0,0,0.04);
  display: flex;
  align-items: center;
  gap: 0.4rem;
  outline: none;
}
.pdf-btn:focus {
  box-shadow: 0 0 0 2px #60a5fa;
}
.pdf-btn:hover {
  background: #f1f5f9;
  color: #0f172a;
}
.pdf-content {
  flex: 1;
  position: relative;
  background: #f8fafc;
  display: flex;
  justify-content: center;
  align-items: center;
}
iframe {
  border: none;
  width: 100%;
  height: 100%;
  border-radius: 0;
  background: #f8fafc;
}
.pdf-loading {
  position: absolute;
  top: 0; left: 0; width: 100%; height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  background: rgba(255,255,255,0.7);
  z-index: 10;
}
@media (max-width: 600px) {
  .pdf-modal {
    width: 100vw;
    height: 100vh;
    border-radius: 0;
    max-width: 100vw;
  }
  .pdf-header {
    padding: 0.7rem 1rem;
  }
  .pdf-btn {
    padding: 1rem 1.2rem;
    font-size: 1.3rem;
  }
  .pdf-title {
    font-size: 1rem;
    max-width: 40vw;
  }
}
</style>

<template>
  <div class="pdf-modal-bg" @click="handleBackdrop">
    <div class="pdf-modal">
      <div class="pdf-header">
        <span class="pdf-title" :title="fileName">{{ fileName || 'PDF Viewer' }}</span>
        <div class="pdf-actions">
          <button class="pdf-btn" @click="handleDownload" title="Download (Ctrl+D)" tabindex="0">
            <i class="fas fa-download"></i>
          </button>
          <button class="pdf-btn" @click="handleBookmark" title="Bookmark (Ctrl+B)" tabindex="0">
            <i class="fas fa-bookmark"></i>
          </button>
          <button class="pdf-btn" @click="$emit('close')" title="Close (Esc)" tabindex="0">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
      <div class="pdf-content">
        <div v-if="loading" class="pdf-loading">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
        <iframe
          v-if="file"
          :src="pdfUrl"
          @load="onLoad"
        ></iframe>
        <div v-else class="text-center text-muted w-100">
          PDF file not found.
        </div>
      </div>
    </div>
  </div>
</template>

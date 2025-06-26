<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import BookCard from '@/Components/BookCard.vue';
import SearchBar from '@/Components/SearchBar.vue';
import BookDetailsModal from '@/Components/Modals/BookDetailsModal.vue';
import BookReader from '@/Components/BookReader.vue';
import PdfReader from '@/Components/PdfReader.vue';
import SkeletonLoader from '@/Components/SkeletonLoader.vue';
import { ref, computed, inject } from 'vue';

defineOptions({ layout: UserLayout });

const props = defineProps({ books: Array });
const search = ref('');
const selectedBook = ref(null);
const showReader = ref(false);
const readerType = ref('');
const bookmarking = ref(false);
const showToast = inject('showToast');
const loading = ref(false);

const filteredBooks = computed(() => {
  if (!search.value) return props.books;
  return props.books.filter(b =>
    b.title.toLowerCase().includes(search.value.toLowerCase()) ||
    b.author.toLowerCase().includes(search.value.toLowerCase())
  );
});

function viewBook(book) {
  selectedBook.value = book;
}
function closeModal() {
  selectedBook.value = null;
}
function handleRead(book) {
  readerType.value = book.file_path.endsWith('.pdf') ? 'pdf' : 'epub';
  showReader.value = true;
}
function closeReader() {
  showReader.value = false;
  readerType.value = '';
}
function handleBookmark(book) {
  bookmarking.value = true;
  fetch('/bookmarks', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ book_id: book.id })
  })
    .then(async res => {
      if (res.ok) {
        showToast('Bookmarked!', 'success');
      } else {
        const data = await res.json();
        showToast(data.message || 'Bookmark failed.', 'error');
      }
    })
    .catch(() => showToast('Bookmark failed.', 'error'))
    .finally(() => {
      bookmarking.value = false;
    });
}
</script>
<template>
  <div class="container py-4">
    <h2 class="fw-bold mb-4">
      <i class="fa fa-landmark text-success me-2"></i>Cultural Heritage
    </h2>
    <SearchBar v-model="search" placeholder="Search cultural heritage books..." />
    <div v-if="loading">
      <SkeletonLoader type="card" :count="4" />
    </div>
    <div v-else class="row g-3 mt-2">
      <div v-for="book in filteredBooks" :key="book.id" class="col-12 col-md-4 col-lg-3">
        <BookCard
          :title="book.title"
          :author="book.author"
          :cover_image="book.cover_image"
          :published_year="book.published_year"
          @click="viewBook(book)"
        />
      </div>
      <div v-if="filteredBooks.length === 0" class="text-muted text-center mt-4">No books found.</div>
    </div>
    <BookDetailsModal
      v-if="selectedBook"
      :book="selectedBook"
      :bookmarking="bookmarking"
      @close="closeModal"
      @read="handleRead"
      @bookmark="handleBookmark"
    />
    <div v-if="showReader">
      <BookReader v-if="readerType === 'epub'" :file="selectedBook.file_path" :bookId="selectedBook.id" @close="closeReader" />
      <PdfReader v-else-if="readerType === 'pdf'" :file="selectedBook.file_path" @close="closeReader" />
    </div>
  </div>
</template> 
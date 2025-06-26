<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import BookCard from '@/Components/BookCard.vue';
import EmptyState from '@/Components/EmptyState.vue';
import { router } from '@inertiajs/vue3';
import { inject } from 'vue';

defineOptions({ layout: UserLayout });

const props = defineProps({
  books: Array
});
const showToast = inject('showToast');

function removeBookmark(book) {
  router.delete(`/bookmarks/${book.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      showToast('Bookmark removed.', 'success');
      // Optionally, reload or filter out the book from the list
      // router.reload({ only: ['books'] });
    },
    onError: () => {
      showToast('Failed to remove bookmark.', 'error');
    }
  });
}
</script>
<template>
  <div class="container py-4">
    <h2 class="fw-bold mb-4">
      <i class="fa fa-bookmark text-warning me-2"></i>My Bookmarks
    </h2>
    <div v-if="props.books.length === 0">
      <EmptyState icon="fa fa-bookmark" message="You have no bookmarks yet." />
    </div>
    <div v-else class="row g-3 mt-2">
      <div v-for="book in props.books" :key="book.id" class="col-12 col-md-4 col-lg-3">
        <BookCard
          :title="book.title"
          :author="book.author"
          :cover_image="book.cover_image"
          :published_year="book.published_year"
          @click="() => {}"
        />
        <button class="btn btn-sm btn-danger mt-2 w-100" @click="removeBookmark(book)">
          <i class="fa fa-trash"></i> Remove
        </button>
      </div>
    </div>
  </div>
</template> 
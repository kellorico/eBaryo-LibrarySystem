<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import BookCard from '@/Components/BookCard.vue';
import SearchBar from '@/Components/SearchBar.vue';
import { ref, computed, inject } from 'vue';
import SkeletonLoader from '@/Components/SkeletonLoader.vue';

const props = defineProps({ books: Array });
const search = ref('');
const showToast = inject('showToast');

const filteredBooks = computed(() => {
  if (!search.value) return props.books;
  return props.books.filter(b =>
    b.title.toLowerCase().includes(search.value.toLowerCase()) ||
    b.author.toLowerCase().includes(search.value.toLowerCase())
  );
});

function viewBook(book) {
  // TODO: Open book details modal or page
}
</script>
<template>
  <UserLayout>
    <div class="container py-4">
      <h2 class="fw-bold mb-4">
        <i class="fa fa-seedling text-success me-2"></i>Agriculture & Livelihood
      </h2>
      <SearchBar v-model="search" placeholder="Search agriculture books..." />
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
    </div>
  </UserLayout>
</template> 
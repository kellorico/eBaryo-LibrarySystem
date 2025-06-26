<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const books = page.props.books || [];

function viewBook(book) {
  // Implement navigation or modal to view/read the book
  window.open(book.file_path.startsWith('http') ? book.file_path : `/storage/${book.file_path}`, '_blank');
}
</script>
<template>
  <UserLayout>
    <div class="container py-4">
      <h2 class="fw-bold mb-4">
        <i class="fa fa-bookmark text-success me-2"></i>My Saved Books
      </h2>
      <div v-if="books.length === 0" class="text-center text-muted py-5">
        <i class="fa fa-book-open fa-2x mb-3"></i>
        <div>No saved books yet.</div>
      </div>
      <div v-else class="row g-3">
        <div v-for="book in books" :key="book.id" class="col-12 col-md-4 col-lg-3">
          <div class="card shadow-sm h-100">
            <img :src="book.cover_image || '/assets/images/image.png'" class="card-img-top" style="height:180px;object-fit:cover;" alt="Book Cover" />
            <div class="card-body d-flex flex-column">
              <div class="fw-bold mb-1">{{ book.title }}</div>
              <div class="text-muted mb-2">by {{ book.author }}</div>
              <button class="btn btn-outline-success mt-auto" @click="viewBook(book)">
                <i class="fa fa-book-open me-1"></i>Read Book
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </UserLayout>
</template>
<style scoped>
.card-img-top {
  border-top-left-radius: 0.5rem;
  border-top-right-radius: 0.5rem;
}
</style> 
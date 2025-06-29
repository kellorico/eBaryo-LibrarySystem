<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { usePage, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import BookCard from '@/Components/BookCard.vue';

const page = usePage();
const categories = page.props.categories || [];
const books = page.props.books || [];
const categoriesWithBooks = page.props.categoriesWithBooks || [];
const search = ref(page.props.search || '');
const searchAuthor = ref(page.props.searchAuthor || '');
const searchCategory = ref(page.props.searchCategory || '');

function goToCategory(category) {
  router.visit(`/books/${category.id}`);
}
function submitSearch() {
  router.visit('/books/browse', {
    data: {
      search: search.value,
      author: searchAuthor.value,
      category: searchCategory.value,
    },
    preserveState: true,
    preserveScroll: true,
  });
}
function openBookDetails(book) {
  router.visit(`/books/${book.id}`);
}
function continueReading(book) {
  // Placeholder: implement if needed
}
function saveBook(book) {
  // Placeholder: implement if needed
}
function bookmarkBook(book) {
  // Placeholder: implement if needed
}
function reportBook(book) {
  // Placeholder: implement if needed
}
</script>
<template>
  <UserLayout>
    <div class="container py-4">
      <h2 class="fw-bold mb-4">
        <i class="fa fa-book text-success me-2"></i>Browse Books
      </h2>
      <form class="row g-2 align-items-end mb-4" @submit.prevent="submitSearch">
        <div class="col-md-4">
          <label class="form-label">Keyword</label>
          <input v-model="search" type="text" class="form-control" placeholder="Title, description, etc." />
        </div>
        <div class="col-md-3">
          <label class="form-label">Author</label>
          <input v-model="searchAuthor" type="text" class="form-control" placeholder="Author name" />
        </div>
        <div class="col-md-3">
          <label class="form-label">Category</label>
          <select v-model="searchCategory" class="form-select">
            <option value="">All Categories</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
          </select>
        </div>
        <div class="col-md-2">
          <button class="btn btn-success w-100" type="submit"><i class="fa fa-search me-1"></i>Search</button>
        </div>
      </form>
      <div v-if="categoriesWithBooks.length">
        <template v-for="cat in categoriesWithBooks" :key="cat?.id">
          <div v-if="cat && (!searchCategory || String(cat.id) === String(searchCategory))" class="mb-5">
            <h3 class="fw-bold mb-3 mt-4"><i class="fa fa-folder-open text-primary me-2"></i>{{ cat.name }}</h3>
            <!-- Recommended Books -->
            <div v-if="cat.recommendedBooks.length" class="mb-4">
              <div class="d-flex align-items-center mb-2">
                <span class="section-accent bg-warning me-2"></span>
                <h5 class="fw-bold mb-0">Recommended Books</h5>
              </div>
              <div class="row g-3">
                <div v-for="book in cat.recommendedBooks" :key="book.id" class="col-12 col-sm-6 col-md-3">
                  <BookCard
                    :title="book.title"
                    :author="book.author"
                    :cover_image="book.cover_image"
                    :published_year="book.published_year"
                    :badge="'Recommended'"
                    @details="() => openBookDetails(book)"
                    @read="() => continueReading(book)"
                    @save="() => saveBook(book)"
                    @bookmark="() => bookmarkBook(book)"
                    @report="() => reportBook(book)"
                  />
                </div>
              </div>
            </div>
            <!-- New Arrivals -->
            <div v-if="cat.latestBooks.length" class="mb-4">
              <div class="d-flex align-items-center mb-2">
                <span class="section-accent bg-info me-2"></span>
                <h5 class="fw-bold mb-0">New Arrivals</h5>
              </div>
              <div class="row g-3">
                <div v-for="book in cat.latestBooks" :key="book.id" class="col-12 col-sm-6 col-md-3">
                  <BookCard
                    :title="book.title"
                    :author="book.author"
                    :cover_image="book.cover_image"
                    :published_year="book.published_year"
                    :badge="'New'"
                    @details="() => openBookDetails(book)"
                    @read="() => continueReading(book)"
                    @save="() => saveBook(book)"
                    @bookmark="() => bookmarkBook(book)"
                    @report="() => reportBook(book)"
                  />
                </div>
              </div>
            </div>
            <!-- Top Rated Books -->
            <div v-if="cat.topRatedBooks.length" class="mb-4">
              <div class="d-flex align-items-center mb-2">
                <span class="section-accent bg-success me-2"></span>
                <h5 class="fw-bold mb-0">Top Rated Books</h5>
              </div>
              <div class="row g-3">
                <div v-for="book in cat.topRatedBooks" :key="book.id" class="col-12 col-sm-6 col-md-3">
                  <BookCard
                    :title="book.title"
                    :author="book.author"
                    :cover_image="book.cover_image"
                    :published_year="book.published_year"
                    :badge="book.avg_rating ? (book.avg_rating + '★ (' + book.ratings_count + ')') : 'No ratings yet'"
                    @details="() => openBookDetails(book)"
                    @read="() => continueReading(book)"
                    @save="() => saveBook(book)"
                    @bookmark="() => bookmarkBook(book)"
                    @report="() => reportBook(book)"
                  />
                </div>
              </div>
            </div>
          </div>
        </template>
      </div>
      <div v-else-if="books.length" class="row g-4">
        <div v-for="book in books" :key="book.id" class="col-12 col-md-6 col-lg-4">
          <div class="card shadow-sm h-100">
            <img :src="book.cover_image || '/assets/images/image.png'" class="card-img-top" style="height:180px;object-fit:cover;" alt="Book Cover" />
            <div class="card-body d-flex flex-column">
              <div class="fw-bold mb-1">{{ book.title }}</div>
              <div class="text-muted mb-2">by {{ book.author }}</div>
              <div class="small text-muted mb-2">Category: {{ book.category?.name || 'N/A' }}</div>
              <button class="btn btn-outline-success mt-auto" @click="router.visit(`/books/${book.id}`)"><i class="fa fa-book-open me-1"></i>View Details</button>
            </div>
          </div>
        </div>
      </div>
      <div v-else-if="search || searchAuthor || searchCategory" class="text-center text-muted py-5">
        <i class="fa fa-book-open fa-2x mb-3"></i>
        <div>No books found for your search.</div>
      </div>
      <div v-else class="row g-4">
        <div v-for="cat in categories" :key="cat.id" class="col-12 col-md-6 col-lg-4">
          <div class="card shadow-sm h-100 category-card" @click="goToCategory(cat)" style="cursor:pointer;">
            <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
              <i class="fa fa-book fa-3x mb-3 text-success"></i>
              <div class="fw-bold fs-5 mb-1">{{ cat.name }}</div>
              <button class="btn btn-outline-success mt-auto">View Books</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </UserLayout>
</template>
<style scoped>
.category-card:hover {
  box-shadow: 0 8px 32px #19875433, 0 2px 8px #43c59e22;
  transform: translateY(-2px) scale(1.01);
}
.section-accent {
  display: inline-block;
  width: 8px;
  height: 24px;
  border-radius: 8px;
}
</style> 
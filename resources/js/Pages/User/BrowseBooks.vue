<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { usePage, router } from '@inertiajs/vue3';

const page = usePage();
const categories = page.props.categories || [];

function goToCategory(category) {
  // You may want to map category name/id to the correct route
  router.visit(`/books/${category.id}`);
}
</script>
<template>
  <UserLayout>
    <div class="container py-4">
      <h2 class="fw-bold mb-4">
        <i class="fa fa-book text-success me-2"></i>Browse Book Categories
      </h2>
      <div v-if="categories.length === 0" class="text-center text-muted py-5">
        <i class="fa fa-book-open fa-2x mb-3"></i>
        <div>No categories found.</div>
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
</style> 
<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import SearchBar from '@/Components/SearchBar.vue';
import { ref, onMounted, computed } from 'vue';
import SkeletonLoader from '@/Components/SkeletonLoader.vue';
import EmptyState from '@/Components/EmptyState.vue';

const archives = ref([]);
const loading = ref(true);
const search = ref('');

function fetchArchives() {
  loading.value = true;
  fetch('/public-archive')
    .then(res => res.json())
    .then(data => {
      archives.value = data.archives || [];
    })
    .finally(() => (loading.value = false));
}

const filteredArchives = computed(() => {
  if (!search.value) return archives.value;
  return archives.value.filter(a =>
    (a.title && a.title.toLowerCase().includes(search.value.toLowerCase())) ||
    (a.category && a.category.toLowerCase().includes(search.value.toLowerCase()))
  );
});

onMounted(fetchArchives);
</script>
<template>
  <UserLayout>
    <div class="container py-4">
      <h2 class="fw-bold mb-4">
        <i class="fa fa-archive text-secondary me-2"></i>Digital Archive
      </h2>
      <SearchBar v-model="search" placeholder="Search archives..." />
      <div v-if="loading">
        <SkeletonLoader type="card" :count="4" />
      </div>
      <div v-else>
        <div v-if="filteredArchives.length === 0">
          <EmptyState icon="fa fa-archive" message="No archives found." />
        </div>
        <div v-else class="row g-3 mt-2">
          <div v-for="a in filteredArchives" :key="a.id" class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm h-100">
              <div class="card-body">
                <h5 class="card-title mb-1">{{ a.title }}</h5>
                <div class="mb-2 text-muted small">{{ a.category }}</div>
                <a :href="`/public-archive/${a.id}/download`" class="btn btn-outline-primary w-100" target="_blank">
                  <i class="fa fa-download"></i> Download
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </UserLayout>
</template> 
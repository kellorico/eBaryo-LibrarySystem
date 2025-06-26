<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { ref, onMounted } from 'vue';
import SkeletonLoader from '@/Components/SkeletonLoader.vue';
import EmptyState from '@/Components/EmptyState.vue';

const announcements = ref([]);
const loading = ref(true);
const selected = ref(null);

function fetchAnnouncements() {
  loading.value = true;
  fetch('/announcements/list')
    .then(res => res.json())
    .then(data => {
      announcements.value = data.announcements || [];
    })
    .finally(() => (loading.value = false));
}

onMounted(fetchAnnouncements);
</script>
<template>
  <UserLayout>
    <div class="container py-4">
      <h2 class="fw-bold mb-4">
        <i class="fa fa-bullhorn text-primary me-2"></i>Announcements
      </h2>
      <div v-if="loading" class="text-center my-4">
        <span class="spinner-border"></span>
      </div>
      <div v-else>
        <div v-if="announcements.length === 0">
          <EmptyState icon="fa fa-bullhorn" message="No announcements yet." />
        </div>
        <div v-else class="list-group">
          <button v-for="a in announcements" :key="a.id" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" @click="selected = a">
            <div>
              <span class="fw-bold">{{ a.title }}</span>
              <span class="ms-2 text-muted small">{{ a.created_at }}</span>
            </div>
            <i class="fa fa-chevron-right"></i>
          </button>
        </div>
      </div>
      <div v-if="selected" class="modal-backdrop" style="z-index:2000;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.3);display:flex;align-items:center;justify-content:center;">
        <div class="modal-dialog" style="background:#fff;border-radius:8px;max-width:400px;width:100%;box-shadow:0 8px 32px rgba(0,0,0,0.18);">
          <div class="modal-content p-3">
            <div class="modal-header d-flex align-items-center justify-content-between">
              <h5 class="modal-title">{{ selected.title }}</h5>
              <button type="button" class="btn-close" @click="selected = null"></button>
            </div>
            <div class="modal-body">
              <div class="mb-2 text-muted small">{{ selected.created_at }}</div>
              <div>{{ selected.message }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </UserLayout>
</template> 
<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { ref, onMounted } from 'vue';
import SkeletonLoader from '@/Components/SkeletonLoader.vue';
import EmptyState from '@/Components/EmptyState.vue';

const notifications = ref([]);
const loading = ref(true);

function fetchNotifications() {
  loading.value = true;
  fetch('/notifications')
    .then(res => res.json())
    .then(data => {
      notifications.value = data.notifications || [];
    })
    .finally(() => (loading.value = false));
}

function markAsRead(notification) {
  fetch(`/notifications/${notification.id}/read`, { method: 'PUT' })
    .then(() => {
      notification.read_at = new Date().toISOString();
    });
}

function deleteNotification(notification) {
  fetch(`/notifications/${notification.id}`, { method: 'DELETE' })
    .then(() => {
      notifications.value = notifications.value.filter(n => n.id !== notification.id);
    });
}

onMounted(fetchNotifications);
</script>
<template>
  <UserLayout>
    <div class="container py-4">
      <h2 class="fw-bold mb-4">
        <i class="fa fa-bell text-info me-2"></i>Notifications
      </h2>
      <div v-if="loading">
        <SkeletonLoader type="list" :count="5" />
      </div>
      <div v-else>
        <div v-if="notifications.length === 0">
          <EmptyState icon="fa fa-bell" message="You have no notifications." />
        </div>
        <div v-else class="list-group">
          <div v-for="n in notifications" :key="n.id" class="list-group-item d-flex align-items-center justify-content-between" :class="{'bg-light': !n.read_at}">
            <div>
              <i :class="n.icon || 'fa fa-info-circle'" :style="n.color_class ? `color:${n.color_class}` : ''"></i>
              <span class="fw-bold ms-2">{{ n.title }}</span>
              <span class="ms-2">{{ n.message }}</span>
              <span class="text-muted ms-2 small">{{ n.time_ago || n.created_at }}</span>
            </div>
            <div>
              <button v-if="!n.read_at" class="btn btn-sm btn-outline-success me-2" @click="markAsRead(n)"><i class="fa fa-check"></i> Mark as read</button>
              <button class="btn btn-sm btn-outline-danger" @click="deleteNotification(n)"><i class="fa fa-trash"></i></button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </UserLayout>
</template> 
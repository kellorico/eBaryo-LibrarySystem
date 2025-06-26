<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';

defineOptions({ layout: UserLayout });

const props = defineProps({
  leaderboard: Array
});
</script>

<template>
  <div class="container py-4">
    <h2 class="fw-bold mb-4">
      <i class="fa fa-trophy text-warning me-2"></i>Challenge Leaderboard
    </h2>
    <div v-if="props.leaderboard.length === 0" class="text-muted text-center">
      No participants yet.
    </div>
    <div v-else class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Books Read</th>
            <th>Completed At</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(user, i) in props.leaderboard" :key="user.id">
            <td>{{ i + 1 }}</td>
            <td>{{ user.name }}</td>
            <td>{{ user.pivot_progress }}</td>
            <td>{{ user.pivot_completed_at ? user.pivot_completed_at : '-' }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template> 
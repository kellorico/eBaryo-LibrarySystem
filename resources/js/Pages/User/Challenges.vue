<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';

defineOptions({ layout: UserLayout });

const props = defineProps({
  challenges: Array
});
</script>

<template>
  <div class="container py-4">
    <h2 class="fw-bold mb-4">
      <i class="fa fa-trophy text-success me-2"></i>Active Reading Challenges
    </h2>
    <div v-if="props.challenges.length === 0" class="text-muted text-center">
      No active challenges.
    </div>
    <div v-else class="row g-3">
      <div v-for="challenge in props.challenges" :key="challenge.id" class="col-12 col-md-6 col-lg-4">
        <div class="card shadow-sm mb-3">
          <div class="card-body">
            <div class="fw-bold">{{ challenge.title }}</div>
            <div class="text-muted small mb-2">
              {{ challenge.start_date }} - {{ challenge.end_date }}
            </div>
            <div>{{ challenge.description }}</div>
            <div class="mt-2">
              <span class="badge bg-primary">Target: {{ challenge.target_books }} books</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template> 
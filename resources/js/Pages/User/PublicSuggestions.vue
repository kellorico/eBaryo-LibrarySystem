<script setup>
import { ref, inject } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import EmptyState from '@/Components/EmptyState.vue';

// Use Inertia props for SSR/SPA
const props = defineProps({
    suggestions: Array,
});

const suggestions = ref(props.suggestions);
const showToast = inject('showToast');

function upvote(suggestion) {
  router.post(
    `/suggestions/${suggestion.id}/upvote`,
    {},
    {
      preserveScroll: true,
      onSuccess: () => {
        suggestion.upvotes_count++;
        suggestion.has_upvoted = true;
        showToast('Upvoted!', 'success');
      },
      onError: () => {
        showToast('Could not upvote.', 'error');
      },
    }
  );
}
</script>

<template>
  <UserLayout>
    <div class="max-w-2xl mx-auto py-8">
      <h1 class="text-2xl font-bold mb-6">Public Suggestions</h1>
      <div v-if="suggestions.length === 0">
        <EmptyState message="No suggestions yet. Be the first to suggest!" />
      </div>
      <div v-else class="space-y-4">
        <div v-for="suggestion in suggestions" :key="suggestion.id" class="p-4 bg-white rounded shadow flex items-center justify-between">
          <div>
            <div class="font-semibold">{{ suggestion.title }}</div>
            <div class="text-gray-600 text-sm">{{ suggestion.description }}</div>
          </div>
          <div class="flex items-center space-x-2">
            <button
              class="px-3 py-1 rounded bg-blue-500 text-white disabled:bg-gray-300"
              :disabled="suggestion.has_upvoted"
              @click="upvote(suggestion)"
            >
              <span v-if="suggestion.has_upvoted">Upvoted</span>
              <span v-else>Upvote</span>
            </button>
            <span class="text-lg">👍 {{ suggestion.upvotes_count }}</span>
          </div>
        </div>
      </div>
    </div>
  </UserLayout>
</template>

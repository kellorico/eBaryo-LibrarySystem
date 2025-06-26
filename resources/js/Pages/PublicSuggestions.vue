<script setup>
// Use Inertia props for SSR/SPA
const props = defineProps({
    suggestions: Array,
});
</script>
<template>
    <div class="container py-4">
        <h2 class="fw-bold mb-4">
            <i class="fa fa-lightbulb text-info me-2"></i>Approved Suggestions
        </h2>
        <div v-if="!props.suggestions.length" class="text-muted text-center">
            No approved suggestions yet.
        </div>
        <div v-else class="row g-3">
            <div v-for="s in props.suggestions" :key="s.id" class="col-12">
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="fw-bold">{{ s.name }}</span>
                            <span class="badge bg-success ms-2">Approved</span>
                            <span class="ms-auto text-muted small">{{
                                new Date(
                                    s.responded_at || s.updated_at
                                ).toLocaleString()
                            }}</span>
                        </div>
                        <div class="mb-2">{{ s.suggestion }}</div>
                        <div
                            v-if="s.admin_response"
                            class="alert alert-info p-2 mb-0"
                        >
                            <strong>Admin Response:</strong>
                            {{ s.admin_response }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

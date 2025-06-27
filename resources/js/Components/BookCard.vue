<script setup>
defineProps({
    title: String,
    author: String,
    cover_image: String,
    published_year: [String, Number],
    badge: { type: [String, Object], default: null },
});

const emit = defineEmits(["details", "read", "save", "bookmark", "report"]);
</script>

<template>
    <div class="card h-100 border-0 shadow-sm hover-shadow transition-all position-relative bookcard-admin-grid">
        <div class="card-body p-4">
            <div class="position-relative mb-3">
                <img
                    :src="cover_image || '/assets/images/image.png'"
                    :alt="title"
                    class="img-fluid rounded-3 w-100"
                    style="height: 200px; object-fit: cover"
                />
                <div class="position-absolute top-0 end-0 m-2">
                    <span v-if="badge" class="book-badge position-relative">{{ badge }}</span>
                    <span v-else-if="published_year" class="badge bg-success bg-opacity-75">{{ published_year }}</span>
                </div>
                <button class="btn btn-sm btn-light position-absolute top-0 start-0 m-2" @click.stop="emit('details')" title="View Details"><i class="fa fa-info-circle"></i></button>
            </div>
            <h5 class="card-title fw-semibold text-dark mb-2 line-clamp-2">
                {{ title }}
            </h5>
            <p class="text-muted small mb-0">
                <i class="fas fa-user me-1"></i>
                {{ author }}
            </p>
        </div>
        <div class="card-footer bg-white border-0 d-flex flex-wrap gap-2 justify-content-between pt-0">
            <button class="btn btn-outline-success btn-sm flex-fill" @click.stop="emit('read')"><i class="fa fa-book-open"></i> Read</button>
            <button class="btn btn-outline-primary btn-sm flex-fill" @click.stop="emit('save')"><i class="fa fa-save"></i> Save</button>
            <button class="btn btn-outline-warning btn-sm flex-fill" @click.stop="emit('bookmark')"><i class="fa fa-bookmark"></i> Bookmark</button>
            <button class="btn btn-outline-danger btn-sm flex-fill" @click.stop="emit('report')"><i class="fa fa-flag"></i> Report</button>
        </div>
    </div>
</template>

<style scoped>
.hover-shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
.transition-all {
    transition: all 0.2s ease-in-out;
}
.card {
    border-radius: 0.75rem;
}
.rounded-3 {
    border-radius: 0.5rem !important;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.book-badge {
    background: linear-gradient(90deg, #198754 60%, #43c59e 100%);
    color: #fff;
    font-weight: bold;
    font-size: 0.95em;
    padding: 0.35em 0.8em;
    border-radius: 1em;
    box-shadow: 0 2px 8px rgba(25,135,84,0.12);
    z-index: 2;
    pointer-events: none;
}
.card:hover .text-success {
    color: #146c43 !important;
}
.card:hover img {
    transform: scale(1.02);
    transition: transform 0.2s ease-in-out;
}
</style>

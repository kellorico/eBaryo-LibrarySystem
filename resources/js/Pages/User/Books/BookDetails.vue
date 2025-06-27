<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
const props = defineProps({
    book: Object,
    avgRating: Number,
    userReview: Object,
    reviews: Array,
    suggestions: Array,
});
const reviewForm = ref({ rating: props.userReview?.rating || 0, review: props.userReview?.review || '' });
const suggestionForm = ref({ suggestion: '' });
const submittingReview = ref(false);
const submittingSuggestion = ref(false);
const categoryName = computed(() => props.book.category?.name || '');
function submitReview() {
    submittingReview.value = true;
    router.post(`/books/${props.book.id}/rate`, reviewForm.value, {
        preserveScroll: true,
        onFinish: () => submittingReview.value = false,
    });
}
function submitSuggestion() {
    submittingSuggestion.value = true;
    router.post(`/books/${props.book.id}/suggest`, suggestionForm.value, {
        preserveScroll: true,
        onFinish: () => submittingSuggestion.value = false,
    });
}
</script>
<template>
    <UserLayout>
        <div class="container py-4">
            <div class="row g-4">
                <!-- Book Info Sidebar -->
                <div class="col-lg-4">
                    <div class="card shadow-lg border sticky-top" style="top: 120px; z-index: 0;">
                        <div class="card-body text-center">
                            <img :src="props.book.cover_image || '/assets/images/image.png'" class="img-fluid rounded shadow mb-3" style="max-height:320px;object-fit:cover;" />
                            <h3 class="fw-bold mb-1">{{ props.book.title }}</h3>
                            <div class="mb-2 text-muted">by {{ props.book.author }}</div>
                            <div class="mb-2">
                                <span class="badge bg-success me-2">{{ props.book.published_year }}</span>
                                <span v-if="categoryName" class="badge bg-info text-dark">{{ categoryName }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="fs-5 text-warning">
                                    <i class="fa fa-star me-1"></i>
                                    {{ props.avgRating ? props.avgRating.toFixed(2) : 'N/A' }}
                                </span>
                                <span class="text-muted small">Avg. Rating</span>
                            </div>
                            <div class="d-grid gap-2 mt-4">
                                <button class="btn btn-success btn-sm" @click="readBook"><i class="fa fa-book-open me-1"></i> Read</button>
                                <button class="btn btn-primary btn-sm" @click="saveBook"><i class="fa fa-save me-1"></i> Save</button>
                                <button class="btn btn-warning btn-sm text-dark" @click="bookmarkBook"><i class="fa fa-bookmark me-1"></i> Bookmark</button>
                                <button class="btn btn-danger btn-sm" @click="reportBook"><i class="fa fa-flag me-1"></i> Report</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Main Content -->
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h4 class="fw-bold mb-2"><i class="fa fa-book-open text-primary me-2"></i>Book Description</h4>
                            <p class="mb-0">{{ props.book.description }}</p>
                        </div>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="fw-bold mb-3"><i class="fa fa-star text-warning me-2"></i>Leave a Review</h5>
                                    <form @submit.prevent="submitReview">
                                        <div class="mb-2">
                                            <label class="form-label">Rating</label>
                                            <div class="d-flex align-items-center gap-1 mb-2">
                                                <span v-for="n in 5" :key="n" class="fs-4" style="cursor:pointer" @click="reviewForm.rating = n">
                                                    <i :class="['fa-star', reviewForm.rating >= n ? 'fas text-warning' : 'far text-secondary']"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Review</label>
                                            <textarea v-model="reviewForm.review" class="form-control" rows="3" placeholder="Write your review..."></textarea>
                                        </div>
                                        <button class="btn btn-success w-100" :disabled="submittingReview">Submit Review</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="fw-bold mb-3"><i class="fa fa-lightbulb text-info me-2"></i>Suggest an Improvement</h5>
                                    <form @submit.prevent="submitSuggestion">
                                        <div class="mb-2">
                                            <textarea v-model="suggestionForm.suggestion" class="form-control" rows="3" placeholder="Your suggestion..."></textarea>
                                        </div>
                                        <button class="btn btn-primary w-100" :disabled="submittingSuggestion">Submit Suggestion</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="fw-bold mb-3"><i class="fa fa-comments text-success me-2"></i>Reviews</h5>
                                    <div v-if="props.reviews.length === 0" class="text-muted">No reviews yet.</div>
                                    <div v-for="r in props.reviews" :key="r.id" class="mb-3 border-bottom pb-2">
                                        <div class="d-flex align-items-center mb-1">
                                            <span class="fw-bold me-2"><i class="fa fa-user-circle me-1"></i>{{ r.user?.name || 'User' }}</span>
                                            <span class="badge bg-warning text-dark ms-2"><i class="fa fa-star"></i> {{ r.rating }}</span>
                                        </div>
                                        <div class="text-muted small mb-1"><i class="fa fa-calendar-alt me-1"></i>{{ new Date(r.created_at).toLocaleDateString() }}</div>
                                        <div>{{ r.review }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h5 class="fw-bold mb-3"><i class="fa fa-lightbulb text-info me-2"></i>Suggestions</h5>
                                    <div v-if="props.suggestions.length === 0" class="text-muted">No suggestions yet.</div>
                                    <div v-for="s in props.suggestions" :key="s.id" class="mb-3 border-bottom pb-2">
                                        <div class="d-flex align-items-center mb-1">
                                            <span class="fw-bold me-2"><i class="fa fa-user-circle me-1"></i>{{ s.user?.name || 'User' }}</span>
                                        </div>
                                        <div class="text-muted small mb-1"><i class="fa fa-calendar-alt me-1"></i>{{ new Date(s.created_at).toLocaleDateString() }}</div>
                                        <div>{{ s.suggestion }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template> 
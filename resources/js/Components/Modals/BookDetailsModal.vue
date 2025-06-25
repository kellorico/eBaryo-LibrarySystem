<script setup>
import { onMounted, watch, ref } from "vue";
import * as bootstrap from "bootstrap";

const props = defineProps({
    show: Boolean,
    book: Object,
});

const emit = defineEmits(["close", "read", "edit", "delete", "report-review"]);

const modalInstance = ref(null);

onMounted(() => {
    const modalEl = document.getElementById("bookDetailsModal");
    modalInstance.value = new bootstrap.Modal(modalEl, { backdrop: 'static' });
    modalEl.addEventListener("hidden.bs.modal", () => {
        emit("close");
    });
});

watch(() => props.show, (val) => {
    if (modalInstance.value) {
        if (val) {
            modalInstance.value.show();
        } else {
            modalInstance.value.hide();
        }
    }
});

const onRead = () => {
    emit('read', props.book);
}

const onEdit = () => {
    emit('edit', props.book);
}

const onDelete = (event) => {
    if (event && event.target) {
        event.target.blur();
    }
    emit('delete', props.book);
}

</script>

<template>
    <div
        class="modal fade"
        id="bookDetailsModal"
        tabindex="-1"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-gradient-success text-white border-0">
                    <div class="d-flex align-items-center">
                        <div class="book-avatar me-3">
                            <div class="avatar-circle">
                                <i class="fas fa-book"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0 fw-bold">{{ book?.title || 'Book Details' }}</h5>
                            <small class="text-white-50">{{ book?.author || 'Unknown Author' }}</small>
                        </div>
                    </div>
                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                    ></button>
                </div>
                <div class="modal-body p-4">
                    <div v-if="!book" class="text-center py-5">
                        <div class="empty-state">
                            <i class="fas fa-book-open text-muted mb-3" style="font-size: 3rem;"></i>
                            <p class="text-muted">No book selected</p>
                        </div>
                    </div>
                    <div v-else class="book-details">
                        <!-- Book Cover and Basic Info -->
                        <div class="row mb-4">
                            <div class="col-md-4 mb-3">
                                <div class="book-cover-container">
                                    <img
                                        v-if="book?.cover_image"
                                        :src="book.cover_image"
                                        alt="Book Cover"
                                        class="book-cover"
                                    />
                                    <div v-else class="book-cover-placeholder">
                                        <i class="fas fa-book"></i>
                                        <p>No cover image</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="book-status-cards">
                                    <div class="status-card available">
                                        <div class="status-icon">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                        <div class="status-content">
                                            <h6 class="status-title">Availability</h6>
                                            <span class="status-badge bg-success">Available</span>
                                        </div>
                                    </div>
                                    <div class="status-card file-type">
                                        <div class="status-icon">
                                            <i :class="book.file_path?.endsWith('.pdf') ? 'fas fa-file-pdf' : 'fas fa-book-open'"></i>
                                        </div>
                                        <div class="status-content">
                                            <h6 class="status-title">File Type</h6>
                                            <span class="status-badge" :class="book.file_path?.endsWith('.pdf') ? 'bg-danger' : 'bg-info'">
                                                {{ book.file_path?.endsWith('.pdf') ? 'PDF' : 'EPUB' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Book Information -->
                        <div class="book-info-section">
                            <h6 class="section-title mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                Book Information
                            </h6>
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-book me-2"></i>
                                        Title
                                    </div>
                                    <div class="info-value">{{ book.title }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-user me-2"></i>
                                        Author
                                    </div>
                                    <div class="info-value">{{ book.author }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-calendar me-2"></i>
                                        Published Year
                                    </div>
                                    <div class="info-value">{{ book.published_year || 'Not specified' }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-tag me-2"></i>
                                        Category
                                    </div>
                                    <div class="info-value">
                                        <span class="category-badge">{{ book.category?.name || 'Uncategorized' }}</span>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-upload me-2"></i>
                                        Date Uploaded
                                    </div>
                                    <div class="info-value">
                                        {{ new Date(book.created_at).toLocaleDateString("en-PH", {
                                            year: "numeric",
                                            month: "long",
                                            day: "numeric",
                                        }) }}
                                    </div>
                                </div>
                                <div class="info-item full-width">
                                    <div class="info-label">
                                        <i class="fas fa-align-left me-2"></i>
                                        Description
                                    </div>
                                    <div class="info-value description-text">
                                        {{ book.description || "No description available." }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Book Reviews -->
                        <div class="book-reviews-section mt-4">
                            <h6 class="section-title mb-3">
                                <i class="fas fa-star me-2"></i>
                                Book Reviews
                            </h6>
                            <div v-if="book.reviews && book.reviews.length">
                                <div v-for="review in book.reviews" :key="review.id" class="review-item mb-3 p-2 border rounded">
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="fw-bold me-2">{{ review.user?.name || 'User' }}</span>
                                        <span class="badge bg-secondary ms-auto">{{ review.rating }}★</span>
                                    </div>
                                    <div class="review-text mb-1">{{ review.review }}</div>
                                    <button class="btn btn-sm btn-outline-danger" @click="$emit('report-review', review)">Report</button>
                                </div>
                            </div>
                            <div v-else class="text-muted">No reviews yet.</div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons mt-4">
                            <div class="d-flex flex-wrap gap-2">
                                <button
                                    v-if="book?.file_path && (book.file_path.endsWith('.epub') || book.file_path.endsWith('.pdf'))"
                                    class="btn btn-primary action-btn"
                                    @click="onRead"
                                >
                                    <i class="fas fa-book-reader me-2"></i>
                                    Read Book
                                </button>
                                <button
                                    class="btn btn-warning action-btn"
                                    @click="onEdit"
                                >
                                    <i class="fas fa-edit me-2"></i>
                                    Edit Book
                                </button>
                                <button
                                    class="btn btn-danger action-btn"
                                    @click="onDelete($event)"
                                >
                                    <i class="fas fa-trash me-2"></i>
                                    Delete Book
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.book-avatar .avatar-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.book-cover-container {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.book-cover {
    width: 100%;
    height: 300px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.book-cover:hover {
    transform: scale(1.05);
}

.book-cover-placeholder {
    width: 100%;
    height: 300px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 3rem;
}

.book-cover-placeholder p {
    margin: 0;
    font-size: 0.875rem;
    margin-top: 0.5rem;
}

.book-status-cards {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.status-card {
    display: flex;
    align-items: center;
    background: #fff;
    border-radius: 8px;
    padding: 0.5rem 0.75rem;
    border: 1px solid #e9ecef;
    margin-bottom: 0.5rem;
    transition: all 0.3s ease;
}

.status-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}

.status-card.available {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border-color: #c3e6cb;
}

.status-card.file-type {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    border-color: #bee5eb;
}

.status-icon {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.5rem;
    font-size: 1rem;
}

.status-card.available .status-icon {
    background: #28a745;
    color: white;
}

.status-card.file-type .status-icon {
    background: #17a2b8;
    color: white;
}

.status-content {
    flex: 1;
}

.status-title {
    margin: 0;
    font-size: 0.8rem;
    font-weight: 600;
    color: #495057;
}

.status-badge {
    display: inline-block;
    padding: 0.15rem 0.5rem;
    border-radius: 14px;
    font-size: 0.7rem;
    font-weight: 600;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.section-title {
    color: #495057;
    font-weight: 600;
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 0.25rem;
    font-size: 1rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 0.5rem;
}

.info-item {
    background: #f8f9fa;
    border-radius: 6px;
    padding: 0.5rem;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.info-item.full-width {
    grid-column: 1 / -1;
}

.info-item:hover {
    background: #e9ecef;
    transform: translateX(2px);
}

.info-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 0.2rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 0.9rem;
    font-weight: 500;
    color: #212529;
}

.description-text {
    line-height: 1.5;
    max-height: 70px;
    overflow-y: auto;
}

.category-badge {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    padding: 0.15rem 0.5rem;
    border-radius: 14px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.action-buttons {
    border-top: 1px solid #e9ecef;
    padding-top: 0.75rem;
}

.action-btn {
    border-radius: 6px;
    font-weight: 500;
    padding: 0.5rem 1rem;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.8rem;
}

.action-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
}

.empty-state {
    color: #6c757d;
}

.modal-content {
    border-radius: 10px;
    overflow: hidden;
}

.modal-header {
    padding: 0.75rem 1rem;
}

.modal-body {
    padding: 0.75rem 1rem;
}

.modal-footer {
    padding: 0.5rem 1rem;
}

.btn {
    border-radius: 6px;
    font-weight: 500;
    padding: 0.4rem 1rem;
    transition: all 0.3s ease;
    font-size: 0.8rem;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
}

.modal-dialog {
    max-width: 540px;
}

@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
    .status-card {
        flex-direction: column;
        text-align: center;
    }
    .status-icon {
        margin-right: 0;
        margin-bottom: 0.3rem;
    }
    .action-buttons .d-flex {
        flex-direction: column;
    }
    .action-btn {
        width: 100%;
        margin-bottom: 0.3rem;
    }
    .modal-dialog {
        margin: 0.5rem;
        max-width: 98vw;
    }
}
</style> 
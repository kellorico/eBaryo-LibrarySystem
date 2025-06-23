<script setup>
import { useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import * as bootstrap from "bootstrap";
import { onMounted, watch, ref } from "vue";

const props = defineProps({
    show: Boolean,
    category_id: {
        type: Number,
        required: true,
    },
});

const emit = defineEmits(["close", "book-added"]);

const form = useForm({
    title: "",
    author: "",
    description: "",
    file_path: null,
    cover_image: null,
    published_year: "",
    category_id: props.category_id,
});

watch(() => props.category_id, (newVal) => {
    form.category_id = newVal;
});

const modalInstance = ref(null);

const addBook = () => {
    form.post(route("storybooks"), {
        preserveScroll: true,
        onSuccess: () => {
            const modalEl = document.getElementById("addBookModal");
            if (modalEl) {
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                modalInstance?.hide();
            }

            Swal.fire({
                title: "Success!",
                text: "Book added successfully.",
                icon: "success",
                confirmButtonText: "OK",
            });

            form.reset();
            emit("book-added");
            emit("close");
        },
        onError: () => {
            Swal.fire({
                title: "Error!",
                text: "Failed to add book. Please check the input fields.",
                icon: "error",
                confirmButtonText: "OK",
            });
        },
    });
};

onMounted(() => {
    const modalEl = document.getElementById("addBookModal");
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
</script>

<template>
    <div
        class="modal fade"
        id="addBookModal"
        tabindex="-1"
        aria-labelledby="addBookModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form @submit.prevent="addBook">
                    <div class="modal-header bg-gradient-success text-white border-0">
                        <div class="d-flex align-items-center">
                            <div class="book-avatar me-3">
                                <div class="avatar-circle">
                                    <i class="fas fa-plus"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="modal-title mb-0 fw-bold">Add New Book</h5>
                                <small class="text-white-50">Fill in the book details below</small>
                            </div>
                        </div>
                        <button
                            type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"
                        ></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="form-sections">
                            <!-- Basic Information Section -->
                            <div class="form-section mb-4">
                                <h6 class="section-title mb-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Basic Information
                                </h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="fas fa-book me-2"></i>
                                                Book Title
                                            </label>
                                            <input
                                                v-model="form.title"
                                                type="text"
                                                class="form-control custom-input"
                                                placeholder="Enter book title"
                                            />
                                            <div class="error-message" v-if="form.errors.title">
                                                <i class="fas fa-exclamation-circle me-1"></i>
                                                {{ form.errors.title }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="fas fa-user me-2"></i>
                                                Author
                                            </label>
                                            <input
                                                v-model="form.author"
                                                type="text"
                                                class="form-control custom-input"
                                                placeholder="Enter author name"
                                            />
                                            <div class="error-message" v-if="form.errors.author">
                                                <i class="fas fa-exclamation-circle me-1"></i>
                                                {{ form.errors.author }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="fas fa-calendar me-2"></i>
                                                Published Year
                                            </label>
                                            <input
                                                v-model="form.published_year"
                                                type="number"
                                                class="form-control custom-input"
                                                placeholder="e.g., 2024"
                                                min="1900"
                                                :max="new Date().getFullYear()"
                                            />
                                            <div class="error-message" v-if="form.errors.published_year">
                                                <i class="fas fa-exclamation-circle me-1"></i>
                                                {{ form.errors.published_year }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="fas fa-tag me-2"></i>
                                                Category
                                            </label>
                                            <div class="category-display">
                                                <span class="category-badge">
                                                    {{ 
                                                        category_id === 1 ? 'Story Books' :
                                                        category_id === 2 ? 'Educational Books' :
                                                        category_id === 3 ? 'Agriculture & Livelihood' :
                                                        category_id === 4 ? 'Cultural Heritage' : 'Unknown'
                                                    }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-align-left me-2"></i>
                                        Description
                                    </label>
                                    <textarea
                                        v-model="form.description"
                                        class="form-control custom-input"
                                        placeholder="Enter book description..."
                                        rows="4"
                                    ></textarea>
                                    <div class="error-message" v-if="form.errors.description">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ form.errors.description }}
                                    </div>
                                </div>
                            </div>

                            <!-- File Upload Section -->
                            <div class="form-section">
                                <h6 class="section-title mb-3">
                                    <i class="fas fa-upload me-2"></i>
                                    File Uploads
                                </h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="upload-group">
                                            <label class="upload-label">
                                                <i class="fas fa-file-pdf me-2"></i>
                                                Book File (PDF/EPUB)
                                            </label>
                                            <div class="file-upload-area" :class="{ 'has-file': form.file_path }">
                                                <input
                                                    type="file"
                                                    class="file-input"
                                                    @change="form.file_path = $event.target.files[0]"
                                                    accept=".pdf,.epub"
                                                />
                                                <div class="upload-content">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                    <p v-if="!form.file_path">Click to upload book file</p>
                                                    <p v-else class="file-name">{{ form.file_path?.name }}</p>
                                                    <small>PDF or EPUB files only</small>
                                                </div>
                                            </div>
                                            <div class="error-message" v-if="form.errors.file_path">
                                                <i class="fas fa-exclamation-circle me-1"></i>
                                                {{ form.errors.file_path }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="upload-group">
                                            <label class="upload-label">
                                                <i class="fas fa-image me-2"></i>
                                                Cover Image
                                            </label>
                                            <div class="file-upload-area" :class="{ 'has-file': form.cover_image }">
                                                <input
                                                    type="file"
                                                    class="file-input"
                                                    @change="form.cover_image = $event.target.files[0]"
                                                    accept="image/*"
                                                />
                                                <div class="upload-content">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                    <p v-if="!form.cover_image">Click to upload cover image</p>
                                                    <p v-else class="file-name">{{ form.cover_image?.name }}</p>
                                                    <small>JPG, PNG, JPEG files only</small>
                                                </div>
                                            </div>
                                            <div class="error-message" v-if="form.errors.cover_image">
                                                <i class="fas fa-exclamation-circle me-1"></i>
                                                {{ form.errors.cover_image }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" v-model="form.category_id" />
                    </div>

                    <div class="modal-footer border-0 bg-light">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                        >
                            <i class="fas fa-times me-2"></i>
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="btn btn-success action-btn"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing">
                                <i class="fas fa-spinner fa-spin me-2"></i>
                                Saving Book...
                            </span>
                            <span v-else>
                                <i class="fas fa-save me-2"></i>
                                Save Book
                            </span>
                        </button>
                    </div>
                </form>
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

.section-title {
    color: #495057;
    font-weight: 600;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 0.5rem;
    margin-bottom: 1.5rem;
}

.form-section {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid #e9ecef;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.custom-input {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 0.75rem;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    background: white;
}

.custom-input:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    outline: none;
}

.custom-input::placeholder {
    color: #6c757d;
    opacity: 0.7;
}

.error-message {
    color: #dc3545;
    font-size: 0.8rem;
    margin-top: 0.25rem;
    display: flex;
    align-items: center;
}

.category-display {
    padding: 0.75rem;
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    min-height: 45px;
    display: flex;
    align-items: center;
}

.category-badge {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.upload-group {
    margin-bottom: 1rem;
}

.upload-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.file-upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    background: white;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    min-height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.file-upload-area:hover {
    border-color: #28a745;
    background: #f8fff9;
}

.file-upload-area.has-file {
    border-color: #28a745;
    background: linear-gradient(135deg, #f8fff9 0%, #e8f5e8 100%);
}

.file-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.upload-content {
    pointer-events: none;
}

.upload-content i {
    font-size: 2rem;
    color: #28a745;
    margin-bottom: 0.5rem;
}

.upload-content p {
    margin: 0.5rem 0;
    font-weight: 500;
    color: #495057;
}

.upload-content .file-name {
    color: #28a745;
    font-weight: 600;
}

.upload-content small {
    color: #6c757d;
    font-size: 0.75rem;
}

.action-btn {
    border-radius: 8px;
    font-weight: 500;
    padding: 0.75rem 1.5rem;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.875rem;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.modal-content {
    border-radius: 16px;
    overflow: hidden;
}

.modal-header {
    padding: 1.5rem;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    padding: 1rem 1.5rem;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    padding: 0.5rem 1.5rem;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

@media (max-width: 768px) {
    .modal-dialog {
        margin: 1rem;
    }
    
    .form-section {
        padding: 1rem;
    }
    
    .file-upload-area {
        padding: 1.5rem;
        min-height: 120px;
    }
    
    .upload-content i {
        font-size: 1.5rem;
    }
}
</style> 
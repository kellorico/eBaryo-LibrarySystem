<script setup>
import { useForm, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import * as bootstrap from "bootstrap";
import { onMounted, watch, ref } from "vue";

const props = defineProps({
    show: Boolean,
    book: Object,
    id: {
        type: String,
        default: "editBookModal"
    }
});

const emit = defineEmits(["close", "book-updated"]);

const form = useForm({
    title: "",
    author: "",
    description: "",
    file_path: null,
    cover_image: null,
    published_year: "",
    category_id: 1,
});

watch(() => props.book, (newBook) => {
    if (newBook) {
        form.reset({
            ...newBook,
            file_path: null,
            cover_image: null,
        });
        form.clearErrors();
    }
});

const modalInstance = ref(null);

const updateBook = () => {
    const hasChanges = form.isDirty || form.file_path !== null || form.cover_image !== null;

    if (!hasChanges) {
        Swal.fire({
            icon: "info",
            title: "No Changes Detected",
            text: "You haven't made any changes to the book details.",
        });
        return;
    }

    form.transform(data => ({
        ...data,
        _method: 'put'
    })).post(`/books/storybooks/${props.book.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            const modalEl = document.getElementById(props.id);
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal?.hide();

            Swal.fire({
                icon: "success",
                title: "Updated!",
                text: "Book updated successfully.",
            });

            form.reset();
            emit("book-updated");
            emit("close");

            setTimeout(() => {
                router.reload({ only: ['books'] });
            }, 500);
        },
        onError: (errors) => {
            console.error('Update errors:', errors);
            Swal.fire({
                icon: "error",
                title: "Update Failed",
                text: "Please check your inputs and try again.",
            });
        },
    });
};

onMounted(() => {
    const modalEl = document.getElementById(props.id);
    // console.log('[EditBookModal] Mounted. ModalEl:', modalEl);
    modalInstance.value = new bootstrap.Modal(modalEl, { backdrop: 'static' });
    // console.log('[EditBookModal] Modal instance created:', modalInstance.value);
    modalEl.addEventListener("hidden.bs.modal", () => {
        form.clearErrors();
        emit("close");
    });
});

watch(() => props.show, (val) => {
    // console.log('[EditBookModal] show prop changed:', val);
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
        :id="id"
        tabindex="-1"
        aria-labelledby="editBookModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form @submit.prevent="updateBook">
                    <div class="modal-header bg-gradient-success text-white border-0">
                        <div class="d-flex align-items-center">
                            <div class="book-avatar me-3">
                                <div class="avatar-circle">
                                    <i class="fas fa-edit"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="modal-title mb-0 fw-bold">Edit Book</h5>
                                <small class="text-white-50">Update the book details below</small>
                            </div>
                        </div>
                        <button
                            type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"
                        ></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="form-section mb-4">
                            <h6 class="section-title mb-3">
                                <i class="fas fa-info-circle me-2"></i> Basic Information
                            </h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Book Title</label>
                                    <input v-model="form.title" type="text" class="form-control custom-input" placeholder="Enter book title" />
                                    <div class="error-message" v-if="form.errors.title">{{ form.errors.title }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Author</label>
                                    <input v-model="form.author" type="text" class="form-control custom-input" placeholder="Enter author name" />
                                    <div class="error-message" v-if="form.errors.author">{{ form.errors.author }}</div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea v-model="form.description" class="form-control custom-input" placeholder="Enter description..." rows="3"></textarea>
                                <div class="error-message" v-if="form.errors.description">{{ form.errors.description }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Published Year</label>
                                <input v-model="form.published_year" type="number" class="form-control custom-input" min="1900" :max="new Date().getFullYear()" placeholder="e.g., 2024" />
                                <div class="error-message" v-if="form.errors.published_year">{{ form.errors.published_year }}</div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h6 class="section-title mb-3">
                                <i class="fas fa-upload me-2"></i> Replace Files (Optional)
                            </h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="upload-label">Book File</label>
                                    <div class="file-upload-area" :class="{ 'has-file': form.file_path }">
                                        <input type="file" class="file-input" @change="form.file_path = $event.target.files[0]" accept=".pdf,.epub" />
                                        <div class="upload-content">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <p v-if="!form.file_path">Click to upload file</p>
                                            <p v-else class="file-name">{{ form.file_path.name }}</p>
                                            <small>PDF or EPUB files only</small>
                                        </div>
                                    </div>
                                    <div class="error-message" v-if="form.errors.file_path">{{ form.errors.file_path }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="upload-label">Cover Image</label>
                                    <div class="file-upload-area" :class="{ 'has-file': form.cover_image }">
                                        <input type="file" class="file-input" @change="form.cover_image = $event.target.files[0]" accept="image/*" />
                                        <div class="upload-content">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <p v-if="!form.cover_image">Click to upload image</p>
                                            <p v-else class="file-name">{{ form.cover_image.name }}</p>
                                            <small>JPG, PNG, JPEG files only</small>
                                        </div>
                                    </div>
                                    <div class="error-message" v-if="form.errors.cover_image">{{ form.errors.cover_image }}</div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" v-model="form.category_id" />
                    </div>

                    <div class="modal-footer border-0 bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-success action-btn" :disabled="form.processing">
                            <span v-if="form.processing">
                                <i class="fas fa-spinner fa-spin me-2"></i> Updating...
                            </span>
                            <span v-else>
                                <i class="fas fa-save me-2"></i> Update Book
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
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
}

.section-title {
    color: #495057;
    font-weight: 600;
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 0.25rem;
    margin-bottom: 0.75rem;
    font-size: 1rem;
}

.form-section {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 0.75rem;
    border: 1px solid #e9ecef;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.25rem;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.custom-input {
    border: 1px solid #e9ecef;
    border-radius: 6px;
    padding: 0.5rem;
    font-size: 0.85rem;
    transition: all 0.3s ease;
    background: white;
}

.custom-input:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.1rem rgba(40, 167, 69, 0.15);
    outline: none;
}

.custom-input::placeholder {
    color: #6c757d;
    opacity: 0.7;
}

.error-message {
    color: #dc3545;
    font-size: 0.75rem;
    margin-top: 0.15rem;
    display: flex;
    align-items: center;
}

.upload-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.25rem;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.file-upload-area {
    border: 1px dashed #dee2e6;
    border-radius: 8px;
    padding: 1rem;
    text-align: center;
    background: white;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    min-height: 80px;
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
    font-size: 1.2rem;
    color: #28a745;
    margin-bottom: 0.25rem;
}

.upload-content p {
    margin: 0.25rem 0;
    font-weight: 500;
    color: #495057;
    font-size: 0.85rem;
}

.upload-content .file-name {
    color: #28a745;
    font-weight: 600;
    font-size: 0.85rem;
}

.upload-content small {
    color: #6c757d;
    font-size: 0.7rem;
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
    .modal-dialog {
        margin: 0.5rem;
        max-width: 98vw;
    }
    .form-section {
        padding: 0.5rem;
    }
    .file-upload-area {
        padding: 0.5rem;
        min-height: 60px;
    }
    .upload-content i {
        font-size: 1rem;
    }
}
</style>

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
        <div class="modal-dialog">
            <div class="modal-content">
                <form @submit.prevent="updateBook">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Book</h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                        ></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-2">
                            <input
                                v-model="form.title"
                                type="text"
                                class="form-control"
                                placeholder="Title"
                            />
                            <div class="text-danger small" v-if="form.errors.title">
                                {{ form.errors.title }}
                            </div>
                        </div>

                        <div class="mb-2">
                            <input
                                v-model="form.author"
                                type="text"
                                class="form-control"
                                placeholder="Author"
                            />
                            <div class="text-danger small" v-if="form.errors.author">
                                {{ form.errors.author }}
                            </div>
                        </div>

                        <div class="mb-2">
                            <textarea
                                v-model="form.description"
                                class="form-control"
                                placeholder="Description"
                                rows="3"
                            ></textarea>
                            <div class="text-danger small" v-if="form.errors.description">
                                {{ form.errors.description }}
                            </div>
                        </div>

                        <div class="mb-2">
                            <input
                                v-model="form.published_year"
                                type="number"
                                class="form-control"
                                placeholder="Published Year"
                            />
                            <div class="text-danger small" v-if="form.errors.published_year">
                                {{ form.errors.published_year }}
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Replace Book File (Optional)</label>
                            <input
                                type="file"
                                class="form-control"
                                @change="form.file_path = $event.target.files[0]"
                            />
                            <div class="text-danger small" v-if="form.errors.file_path">
                                {{ form.errors.file_path }}
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Replace Cover Image (Optional)</label>
                            <input
                                type="file"
                                class="form-control"
                                @change="form.cover_image = $event.target.files[0]"
                            />
                            <div class="text-danger small" v-if="form.errors.cover_image">
                                {{ form.errors.cover_image }}
                            </div>
                        </div>

                        <input type="hidden" v-model="form.category_id" />
                    </div>

                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="btn btn-primary"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing">
                                <i class="fas fa-spinner fa-spin me-1"></i>
                                Updating...
                            </span>
                            <span v-else>Update</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template> 
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
        <div class="modal-dialog">
            <div class="modal-content">
                <form @submit.prevent="addBook">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Book</h5>
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
                            <label class="form-label">Upload Book File</label>
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
                            <label class="form-label">Upload Cover Image</label>
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
                            class="btn btn-success"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing">
                                <i class="fas fa-spinner fa-spin me-1"></i>
                                Saving...
                            </span>
                            <span v-else> Save </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template> 
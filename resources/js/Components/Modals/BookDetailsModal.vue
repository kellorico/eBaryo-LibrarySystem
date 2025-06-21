<script setup>
import { onMounted, watch, ref } from "vue";
import * as bootstrap from "bootstrap";

const props = defineProps({
    show: Boolean,
    book: Object,
});

const emit = defineEmits(["close", "read", "edit", "delete"]);

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
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ book?.title }}</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                    ></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Cover Image -->
                        <div class="col-md-4 mb-3">
                            <img
                                v-if="book?.cover_image"
                                :src="book.cover_image"
                                alt="Cover"
                                class="img-fluid rounded shadow"
                                style="height: 250px; object-fit: cover"
                            />
                            <p v-else class="text-muted">
                                No cover image available.
                            </p>
                        </div>

                        <!-- Book Info -->
                        <div class="col-md-8">
                            <h5>
                                Author:
                                <span class="text-secondary">{{
                                    book?.author
                                }}</span>
                            </h5>
                            <p class="mb-2">
                                <strong>Published Year:</strong>
                                {{ book?.published_year }}
                            </p>
                            <p>
                                <strong>Description:</strong>
                                {{
                                    book?.description ||
                                    "No description."
                                }}
                            </p>
                            <p class="mb-2">
                                <strong>Date Uploaded:</strong>
                                {{
                                    new Date(
                                        book?.created_at
                                    ).toLocaleDateString("en-PH", {
                                        year: "numeric",
                                        month: "long",
                                        day: "numeric",
                                    })
                                }}
                            </p>

                            <div class="d-flex flex-wrap gap-2 mt-3">
                                <!-- Read Button -->
                                <button
                                    v-if="
                                        book?.file_path &&
                                        (book.file_path.endsWith(
                                            '.epub'
                                        ) ||
                                            book.file_path.endsWith(
                                                '.pdf'
                                            ))
                                    "
                                    class="btn btn-primary"
                                    @click="onRead"
                                >
                                    <i class="fas fa-book-reader me-1"></i>
                                    Read
                                </button>

                                <!-- Edit Button -->
                                <button
                                    class="btn btn-warning text-white"
                                    @click="onEdit"
                                >
                                    <i class="fas fa-edit me-1"></i> Edit
                                </button>

                                <!-- Delete Button -->
                                <button
                                    class="btn btn-danger"
                                    @click="onDelete($event)"
                                >
                                    <i class="fas fa-trash me-1"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template> 
<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import BookCard from "@/Components/BookCard.vue";
import BookReader from "@/Components/BookReader.vue";
import PdfReader from "@/Components/PdfReader.vue";
import { router } from "@inertiajs/vue3";
import { ref } from "vue";
import Swal from "sweetalert2";
import * as bootstrap from "bootstrap";
import AddBookModal from "@/Components/Modals/AddBookModal.vue";
import EditBookModal from "@/Components/Modals/EditBookModal.vue";
import BookDetailsModal from "@/Components/Modals/BookDetailsModal.vue";

const isReading = ref(false);
const readerFileUrl = ref("");

const showAddModal = ref(false);
const showEditModal = ref(false);
const showDetailsModal = ref(false);

const selectedBook = ref(null);

const showBookDetails = (book) => {
    selectedBook.value = book;
    showDetailsModal.value = true;
};

const openAddModal = () => {
    showAddModal.value = true;
};

const openEditModal = (book) => {
    selectedBook.value = { ...book };
    showEditModal.value = true;
};

const closeAddModal = () => {
    showAddModal.value = false;
};
const closeEditModal = () => {
    showEditModal.value = false;
};
const closeDetailsModal = () => {
    showDetailsModal.value = false;
};

const handleBookAdded = () => {
    router.reload({ only: ['books'] });
};
const handleBookUpdated = () => {
    router.reload({ only: ['books'] });
};
const handleBookDeleted = () => {
    router.reload({ only: ['books'] });
    closeDetailsModal();
};

const handleReadBook = (book) => {
    if (!book || !book.file_path) return;
    const file = book.file_path;
    const isAbsolute = file.startsWith("http");
    const fileUrl = isAbsolute
        ? file
        : file.startsWith("/storage/")
        ? file
        : `/storage/${file}`;
    readerFileUrl.value = fileUrl;
    isReading.value = true;
    closeDetailsModal();
};

const handleEditBook = (book) => {
    // Move focus away from the modal before hiding
    if (document.activeElement) {
        document.activeElement.blur();
    }

    closeDetailsModal();

    const detailsModalEl = document.getElementById("bookDetailsModal");
    if (detailsModalEl) {
        detailsModalEl.addEventListener(
            "hidden.bs.modal",
            () => {
                openEditModal(book);
            },
            { once: true }
        );
    } else {
        openEditModal(book);
    }
};

const handleDeleteBook = (book) => {
    selectedBook.value = book;

    // Explicitly hide the Book Details modal using Bootstrap's API
    const detailsModalEl = document.getElementById("bookDetailsModal");
    if (detailsModalEl) {
        const modalInstance = window.bootstrap
            ? window.bootstrap.Modal.getInstance(detailsModalEl)
            : bootstrap.Modal.getInstance(detailsModalEl);
        if (modalInstance) {
            modalInstance.hide();
        }
        detailsModalEl.addEventListener(
            "hidden.bs.modal",
            () => {
                // Now show SweetAlert after modal is fully hidden
                Swal.fire({
                    title: "Are you sure?",
                    text: "This action cannot be undone!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, delete it!",
                }).then((result) => {
                    if (result.isConfirmed) {
                        router.delete(`/books/storybooks/${book.id}`, {
                            preserveScroll: true,
                            onSuccess: () => {
                                Swal.fire({
                                    icon: "success",
                                    title: "Deleted!",
                                    text: "Book has been deleted.",
                                });
                                handleBookDeleted();
                            },
                            onError: () => {
                                Swal.fire({
                                    icon: "error",
                                    title: "Delete Failed",
                                    text: "Could not delete the book. Please try again.",
                                });
                            },
                        });
                    }
                });
            },
            { once: true }
        );
    } else {
        // Fallback if modal element not found
        Swal.fire({
            title: "Are you sure?",
            text: "This action cannot be undone!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete it!",
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(`/books/storybooks/${book.id}`, {
                    preserveScroll: true,
                    onSuccess: () => {
                        Swal.fire({
                            icon: "success",
                            title: "Deleted!",
                            text: "Book has been deleted.",
                        });
                        handleBookDeleted();
                    },
                    onError: () => {
                        Swal.fire({
                            icon: "error",
                            title: "Delete Failed",
                            text: "Could not delete the book. Please try again.",
                        });
                    },
                });
            }
        });
    }
};

const bookmarkBook = (bookId) => {
    router.post(
        "/bookmarks",
        { book_id: bookId },
        {
            preserveScroll: true,
            onSuccess: () => {
                alert("Bookmarked successfully!");
            },
            onError: (err) => {
                alert("Bookmark failed.");
                console.error(err);
            },
        }
    );
};

defineProps({
    books: {
        type: Array,
        default: () => [],
    },
});


</script>

<template>
    <AdminLayout>
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <!-- Icon and Title Group -->
                <div class="d-flex align-items-center">
                    <img
                        src="/book_icons/story_books.png"
                        alt="story books"
                        style="width: 40px; height: 40px"
                        class="me-2"
                    />
                    <h2 class="mb-0">Story Books</h2>
                </div>

                <!-- Button -->
                <button
                    class="btn btn-success"
                    @click="openAddModal"
                >
                    <i class="fa-solid fa-plus me-1"></i> Add New Book
                </button>
            </div>

            <div class="row">
                <div v-for="book in books" :key="book.id" class="col-md-3 mb-4">
                    <BookCard
                        :title="book.title"
                        :author="book.author"
                        :cover_image="book.cover_image"
                        :published_year="book.published_year"
                        @click="() => showBookDetails(book)"
                    />
                </div>
            </div>
        </div>

        <AddBookModal
            :show="showAddModal"
            :category_id="1"
            @close="closeAddModal"
            @book-added="handleBookAdded"
        />
        <EditBookModal
            :show="showEditModal"
            :book="selectedBook"
            @close="closeEditModal"
            @book-updated="handleBookUpdated"
        />
        <BookDetailsModal
            :show="showDetailsModal"
            :book="selectedBook"
            @close="closeDetailsModal"
            @read="handleReadBook"
            @edit="handleEditBook"
            @delete="handleDeleteBook"
        />

        <BookReader
            v-if="isReading && selectedBook?.file_path?.endsWith('.epub')"
            :file="readerFileUrl"
            @close="isReading = false"
        />
        <PdfReader
            v-if="isReading && selectedBook?.file_path?.endsWith('.pdf')"
            :file="readerFileUrl"
            :book-id="selectedBook?.id"
            @close="isReading = false"
            @bookmark="bookmarkBook"
        />
    </AdminLayout>
</template>
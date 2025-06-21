<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import BookCard from "@/Components/BookCard.vue";
import BookReader from "@/Components/BookReader.vue";
import PdfReader from "@/Components/PdfReader.vue";
import { router } from "@inertiajs/vue3";
import { ref } from "vue";
import Swal from "sweetalert2";
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
    if (document.activeElement) {
        document.activeElement.blur();
    }
    closeDetailsModal();
    openEditModal(book);
};

const handleDeleteBook = (book) => {
    selectedBook.value = book;
    closeDetailsModal();
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
            router.delete(`/books/agricultureandlivelihood/${book.id}`, {
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
                <div class="d-flex align-items-center">
                    <img
                        src="/book_icons/agriculture.png"
                        alt="story books"
                        style="width: 40px; height: 40px"
                        class="me-2"
                    />
                    <h2 class="mb-0">Agriculture & Livelihood Books</h2>
                </div>
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
            :category_id="3"
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
            @close="isReading = false"
        />
    </AdminLayout>
</template>

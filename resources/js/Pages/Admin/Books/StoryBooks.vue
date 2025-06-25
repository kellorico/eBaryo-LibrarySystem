<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
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

const handleReportReview = (review) => {
    Swal.fire({
        title: 'Report Review',
        input: 'text',
        inputLabel: 'Reason for reporting (optional)',
        showCancelButton: true,
        confirmButtonText: 'Report',
        inputPlaceholder: 'Enter reason (optional)',
    }).then(result => {
        if (result.isConfirmed) {
            router.post(`/books/storybooks/reviews/${review.id}/report`, { reason: result.value }, {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({ icon: 'success', title: 'Reported', text: 'Review has been reported.' });
                },
                onError: () => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to report review.' });
                }
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
        <Head title="Story Books" />
        <div class="container-fluid">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <div class="mb-3 mb-md-0">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-success bg-opacity-10 rounded-3 p-2 me-3">
                                    <img
                                        src="/book_icons/story_books.png"
                                        alt="Story Books"
                                        style="width: 32px; height: 32px"
                                    />
                                </div>
                                <h1 class="h2 fw-bold text-dark mb-0">Story Books</h1>
                            </div>
                            <p class="text-muted mb-0">Discover and manage your collection of story books</p>
                        </div>
                        <button 
                            class="btn btn-success btn-lg shadow-sm"
                            @click="openAddModal"
                        >
                            <i class="fas fa-plus me-2"></i>
                            Add New Book
                        </button>
                    </div>
                </div>
            </div>

            <!-- Books Grid -->
            <div class="row g-4">
                <div v-for="book in books" :key="book.id" class="col-12 col-sm-6 col-lg-4 col-xl-3">
                    <div class="card h-100 border-0 shadow-sm hover-shadow transition-all" @click="() => showBookDetails(book)" style="cursor: pointer;">
                        <div class="card-body p-4">
                            <div class="position-relative mb-3">
                                <img 
                                    :src="book.cover_image || '/default-cover.jpg'" 
                                    :alt="book.title"
                                    class="img-fluid rounded-3 w-100"
                                    style="height: 200px; object-fit: cover;"
                                />
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="badge bg-success bg-opacity-75">{{ book.published_year }}</span>
                                </div>
                            </div>
                            <h5 class="card-title fw-semibold text-dark mb-2 line-clamp-2">{{ book.title }}</h5>
                            <p class="text-muted small mb-0">
                                <i class="fas fa-user me-1"></i>
                                {{ book.author }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="books.length === 0" class="text-center py-5">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                    <i class="fas fa-book fa-2x text-muted"></i>
                </div>
                <h3 class="h5 fw-semibold text-dark mb-2">No story books yet</h3>
                <p class="text-muted mb-4">Get started by adding your first story book to the collection.</p>
                <button 
                    class="btn btn-success"
                    @click="openAddModal"
                >
                    <i class="fas fa-plus me-2"></i>
                    Add First Book
                </button>
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
            @report-review="handleReportReview"
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

<style scoped>
/* Custom styles for modern Bootstrap appearance with green theme */
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

/* Custom green theme hover effects */
.card:hover .text-success {
    color: #146c43 !important;
}

.bg-success.bg-opacity-10:hover {
    background-color: rgba(25, 135, 84, 0.15) !important;
}

/* Image hover effect */
.card:hover img {
    transform: scale(1.02);
    transition: transform 0.2s ease-in-out;
}
</style>
<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm } from '@inertiajs/vue3'
import { watch } from 'vue'

defineProps({
    categories: Array
})

// Create a form with Inertia
const form = useForm({
    name: ''
})

// Submit function
const addCategory = () => {
    form.post(route('categories'), {
        onSuccess: () => {
            // Clear form and close modal
            form.reset()
            document.getElementById('closeModalBtn')?.click()
        }
    })
}
</script>

<template>
    <AdminLayout>
        <div class="container-fluid">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <div class="mb-3 mb-md-0">
                            <h1 class="h2 fw-bold text-dark mb-2">Categories</h1>
                            <p class="text-muted mb-0">Manage your book categories and organize your library</p>
                        </div>
                        <button 
                            class="btn btn-success btn-lg shadow-sm"
                            data-bs-toggle="modal" 
                            data-bs-target="#addCategoryModal"
                        >
                            <i class="fas fa-plus me-2"></i>
                            Add New Category
                        </button>
                    </div>
                </div>
            </div>

            <!-- Categories Grid -->
            <div class="row g-4">
                <div 
                    v-for="category in categories" 
                    :key="category.id" 
                    class="col-12 col-sm-6 col-lg-4 col-xl-3"
                >
                    <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="flex-grow-1">
                                    <h5 class="card-title fw-semibold text-dark mb-2">{{ category.name }}</h5>
                                    <p class="text-muted small mb-0">
                                        {{ category.books_count ?? 0 }} {{ category.books_count === 1 ? 'book' : 'books' }}
                                    </p>
                                </div>
                                <div class="ms-3">
                                    <div class="bg-success bg-opacity-10 rounded-3 p-2">
                                        <i class="fas fa-folder text-success"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Progress bar -->
                            <div class="progress" style="height: 6px;">
                                <div 
                                    class="progress-bar bg-gradient-success" 
                                    role="progressbar" 
                                    :style="{ width: Math.min((category.books_count ?? 0) * 10, 100) + '%' }"
                                    :aria-valuenow="category.books_count ?? 0"
                                    aria-valuemin="0" 
                                    aria-valuemax="100"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="categories.length === 0" class="text-center py-5">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                    <i class="fas fa-folder fa-2x text-muted"></i>
                </div>
                <h3 class="h5 fw-semibold text-dark mb-2">No categories yet</h3>
                <p class="text-muted mb-4">Get started by creating your first category to organize your books.</p>
                <button 
                    class="btn btn-success"
                    data-bs-toggle="modal" 
                    data-bs-target="#addCategoryModal"
                >
                    <i class="fas fa-plus me-2"></i>
                    Create First Category
                </button>
            </div>
        </div>

        <!-- Modern Add Category Modal -->
        <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-gradient-success text-white border-0">
                        <div class="d-flex align-items-center">
                            <div class="bg-opacity-20 rounded-3 p-2 me-3">
                                <i class="fas fa-plus text-white"></i>
                            </div>
                            <h5 class="modal-title fw-semibold" id="addCategoryModalLabel">
                                Add New Category
                            </h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body p-4">
                        <div class="mb-4">
                            <label for="categoryName" class="form-label fw-semibold text-dark">
                                Category Name
                            </label>
                            <input 
                                id="categoryName"
                                type="text" 
                                class="form-control form-control-lg" 
                                :class="{ 'is-invalid': form.errors.name }"
                                placeholder="Enter category name..." 
                                v-model="form.name"
                            />
                            <div v-if="form.errors.name" class="invalid-feedback d-flex align-items-center">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ form.errors.name }}
                            </div>
                        </div>
                        
                        <div class="alert alert-success border-0 bg-success bg-opacity-10">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-success"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="mb-0 small">
                                        Categories help organize your books and make them easier to find for users.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer border-0 bg-light">
                        <button 
                            type="button" 
                            class="btn btn-outline-secondary" 
                            data-bs-dismiss="modal" 
                            id="closeModalBtn"
                        >
                            Cancel
                        </button>
                        <button 
                            type="button" 
                            class="btn btn-success px-4" 
                            @click="addCategory" 
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing" class="d-flex align-items-center">
                                <div class="spinner-border spinner-border-sm me-2" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                Creating...
                            </span>
                            <span v-else class="d-flex align-items-center">
                                <i class="fas fa-check me-2"></i>
                                Create Category
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
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

.bg-gradient-success {
    background: linear-gradient(135deg, #198754 0%, #157347 100%);
}

.progress-bar.bg-gradient-success {
    background: linear-gradient(90deg, #198754 0%, #20c997 100%);
}

.modal-content {
    border-radius: 1rem;
}

.modal-header {
    border-top-left-radius: 1rem;
    border-top-right-radius: 1rem;
}

.modal-footer {
    border-bottom-left-radius: 1rem;
    border-bottom-right-radius: 1rem;
}

.form-control:focus {
    border-color: #198754;
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
}

.btn-success {
    background: linear-gradient(135deg, #198754 0%, #157347 100%);
    border: none;
}

.btn-success:hover {
    background: linear-gradient(135deg, #157347 0%, #146c43 100%);
}

.card {
    border-radius: 0.75rem;
}

.rounded-3 {
    border-radius: 0.5rem !important;
}

/* Custom green theme hover effects */
.card:hover .text-success {
    color: #146c43 !important;
}

.bg-success.bg-opacity-10:hover {
    background-color: rgba(25, 135, 84, 0.15) !important;
}
</style>
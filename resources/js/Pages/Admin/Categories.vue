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
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Categories</h2>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="fa-solid fa-plus me-1"></i> Add New Category
                </button>
            </div>

            <div class="row">
                <div v-for="category in categories" :key="category.id" class="col-md-3 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="card-title">{{ category.name }}</h5>
                            <p class="text-muted mb-0">
                                {{ category.books_count ?? 0 }} {{ category.books_count === 1 ? 'book' : 'books' }}
                            </p>

                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Add Category Modal -->
        <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCategoryModalLabel">Add New Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" />
                    </div>
                    <div class="modal-body">
                        <input type="text" class="form-control" placeholder="Category Name" v-model="form.name" />
                        <div v-if="form.errors.name" class="text-danger small mt-1">
                            {{ form.errors.name }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="closeModalBtn">
                            Cancel
                        </button>
                        <button type="button" class="btn btn-success" @click="addCategory" :disabled="form.processing">
                            <span v-if="form.processing">
                                <i class="fas fa-spinner fa-spin me-1"></i> Saving...
                            </span>
                            <span v-else>
                                Save
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
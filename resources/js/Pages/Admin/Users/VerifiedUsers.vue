<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { router } from "@inertiajs/vue3";
import { ref } from "vue";
import Swal from "sweetalert2";
import UserDetailsModal from "@/Components/Modals/UserDetailsModal.vue";

const confirmDelete = (userId) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You are about to delete this verified user.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(`/admin/users/${userId}`);
        }
    });
};

const confirmVerify = (userId) => {
    Swal.fire({
        title: "Verify this user?",
        text: "This user will be marked as verified.",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#198754",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, verify",
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(`/admin/users/${userId}/verify`);
        }
    });
};

const selectedUser = ref(null);
const showModal = ref(false);

const openUserModal = (user) => {
    selectedUser.value = user;
    showModal.value = true;
};
</script>

<template>
    <AdminLayout>
        <div class="container-fluid">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <div class="mb-3 mb-md-0">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-success bg-opacity-10 rounded-3 p-2 me-3">
                                    <i class="fas fa-user-check text-success" style="font-size: 1.5rem;"></i>
                                </div>
                                <h1 class="h2 fw-bold text-dark mb-0">Verified Users</h1>
                            </div>
                            <p class="text-muted mb-0">Manage users who have been verified and approved</p>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-success bg-opacity-10 text-success me-3 px-3 py-2">
                                <i class="fas fa-check-circle me-1"></i>
                                {{ $page.props.verifiedUsers?.length || 0 }} Verified
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Grid -->
            <div v-if="$page.props.verifiedUsers && $page.props.verifiedUsers.length" class="row g-4">
                <div v-for="user in $page.props.verifiedUsers" :key="user.id" class="col-12 col-lg-6 col-xl-4">
                    <div class="card border-0 shadow-sm hover-shadow transition-all">
                        <div class="card-body p-4">
                            <!-- User Header -->
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                    <i class="fas fa-user-check text-success"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="card-title fw-semibold text-dark mb-1">{{ user.name }}</h5>
                                    <p class="text-muted small mb-0">{{ user.email }}</p>
                                </div>
                                <div class="dropdown">
                                    <ul class="dropdown-menu">
                                        <li><button class="dropdown-item" @click="openUserModal(user)">
                                            <i class="fas fa-eye me-2"></i>View Details
                                        </button></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><button class="dropdown-item text-danger" @click="confirmDelete(user.id)">
                                            <i class="fas fa-trash me-2"></i>Delete User
                                        </button></li>
                                    </ul>
                                </div>
                            </div>

                            <!-- User Info -->
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-id-badge text-muted me-2"></i>
                                        <span class="small text-muted">ID: {{ user.id }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-tag text-muted me-2"></i>
                                        <span class="small text-muted">{{ user.role }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                        <span class="small text-muted">{{ user.barangay || 'N/A' }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-graduation-cap text-muted me-2"></i>
                                        <span class="small text-muted">{{ user.school_name || 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Badges -->
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span :class="user.is_student == 1 ? 'badge bg-success' : 'badge bg-secondary'">
                                    <i class="fas fa-user-graduate me-1"></i>
                                    {{ user.is_student == 1 ? "Student" : "Not a Student" }}
                                </span>
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Verified
                                </span>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-success btn-sm flex-fill" @click="openUserModal(user)">
                                    <i class="fas fa-eye me-1"></i>View
                                </button>
                                <button class="btn btn-outline-danger btn-sm" @click="confirmDelete(user.id)">
                                    <i class="fas fa-trash me-1"></i>Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-5">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                    <i class="fas fa-user-check fa-2x text-muted"></i>
                </div>
                <h3 class="h5 fw-semibold text-dark mb-2">No verified users yet</h3>
                <p class="text-muted mb-4">There are no verified users in the system yet.</p>
                <div class="alert alert-info border-0 bg-info bg-opacity-10 d-inline-block">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        <span class="small">Users need to be verified before they appear here.</span>
                    </div>
                </div>
            </div>
        </div>

        <UserDetailsModal
            :user="selectedUser"
            :show="showModal"
            @close="showModal = false"
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

/* Custom green theme hover effects */
.card:hover .text-success {
    color: #146c43 !important;
}

.bg-success.bg-opacity-10:hover {
    background-color: rgba(25, 135, 84, 0.15) !important;
}

/* Dropdown styling */
.dropdown-item:hover {
    background-color: rgba(25, 135, 84, 0.1);
}

.dropdown-item.text-success:hover {
    background-color: rgba(25, 135, 84, 0.15);
}

/* Badge styling */
.badge {
    font-size: 0.75rem;
    padding: 0.5rem 0.75rem;
}

/* Button styling */
.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

/* Alert styling */
.alert {
    border-radius: 0.75rem;
}
</style>

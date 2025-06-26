<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { router, usePage } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import Swal from "sweetalert2";
import UserDetailsModal from "@/Components/Modals/UserDetailsModal.vue";
import SearchBar from "@/Components/SearchBar.vue";

const page = usePage();

const confirmDelete = (userId) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You are about to delete this unverified user.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(`/users/unverifiedusers/${userId}`);
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
            router.put(
                `/users/unverifiedusers/${userId}`,
                {},
                {
                    onSuccess: () => {
                        Swal.fire({
                            icon: "success",
                            title: "Verified!",
                            text: "User has been verified successfully.",
                            confirmButtonColor: "#198754",
                        });
                    },
                }
            );
        }
    });
};

const selectedUser = ref(null);
const showModal = ref(false);

const openUserModal = (user) => {
    selectedUser.value = user;
    showModal.value = true;
};

const search = ref("");

const filteredUsers = computed(() => {
    if (!search.value) return page.props.users;
    return page.props.users.filter((user) => {
        const term = search.value.toLowerCase();
        return (
            user.name.toLowerCase().includes(term) ||
            user.email.toLowerCase().includes(term)
        );
    });
});
</script>

<template>
    <AdminLayout>
        <Head title="Unverified Users" />
        <div class="container-fluid">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div
                        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center"
                    >
                        <div class="mb-3 mb-md-0">
                            <div class="d-flex align-items-center mb-2">
                                <div
                                    class="bg-warning bg-opacity-10 rounded-3 p-2 me-3"
                                >
                                    <i
                                        class="fas fa-user-clock text-warning"
                                        style="font-size: 1.5rem"
                                    ></i>
                                </div>
                                <h1 class="h2 fw-bold text-dark mb-0">
                                    Unverified Users
                                </h1>
                            </div>
                            <p class="text-muted mb-0">
                                Review and approve pending user registrations
                            </p>
                        </div>
                        <div class="d-flex align-items-center">
                            <span
                                class="badge bg-warning bg-opacity-10 text-warning me-3 px-3 py-2"
                            >
                                <i class="fas fa-clock me-1"></i>
                                {{ page.props.users?.length || 0 }} Pending
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <SearchBar
                v-model="search"
                placeholder="Search by name or email..."
            />
            <table
                class="table align-middle table-hover bg-white rounded shadow-sm"
            >
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Status</th>
                        <th>Email</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="filteredUsers.length === 0">
                        <td colspan="5" class="text-center text-muted">
                            No users found.
                        </td>
                    </tr>
                    <tr v-for="user in filteredUsers" :key="user.id">
                        <td>{{ user.id }}</td>
                        <td class="d-flex align-items-center gap-2">
                            <img
                                v-if="user.avatar_url"
                                :src="user.avatar_url"
                                alt="Avatar"
                                style="
                                    width: 40px;
                                    height: 40px;
                                    border-radius: 50%;
                                    object-fit: cover;
                                "
                            />
                            <i
                                v-else
                                class="fas fa-user-clock text-warning bg-light p-2 rounded-circle"
                                style="
                                    width: 40px;
                                    height: 40px;
                                    font-size: 1.5rem;
                                "
                            ></i>
                            <div>
                                <div class="fw-semibold">{{ user.name }}</div>
                                <div class="text-muted small">
                                    {{ user.role }}
                                </div>
                            </div>
                        </td>
                        <td>
                            <span
                                class="badge bg-warning text-dark"
                                style="font-size: 0.85rem"
                                >Pending</span
                            >
                            <span
                                v-if="user.is_student == 1"
                                class="badge bg-info ms-1"
                                style="font-size: 0.85rem"
                                >Student</span
                            >
                            <span
                                v-else
                                class="badge bg-secondary ms-1"
                                style="font-size: 0.85rem"
                                >Not a Student</span
                            >
                            <span
                                :class="
                                    user.email_verified_at
                                        ? 'badge bg-success ms-1'
                                        : 'badge bg-danger ms-1'
                                "
                                style="font-size: 0.85rem"
                            >
                                {{
                                    user.email_verified_at
                                        ? "Email Verified"
                                        : "Email Not Verified"
                                }}
                            </span>
                        </td>
                        <td>{{ user.email }}</td>
                        <td class="text-end">
                            <button
                                class="btn btn-link text-primary p-0 me-2"
                                @click="openUserModal(user)"
                                title="View"
                            >
                                <i class="fas fa-search"></i>
                            </button>
                            <button
                                class="btn btn-link text-success p-0 me-2"
                                @click="confirmVerify(user.id)"
                                title="Verify"
                            >
                                <i class="fas fa-check"></i>
                            </button>
                            <button
                                class="btn btn-link text-danger p-0"
                                @click="confirmDelete(user.id)"
                                title="Delete"
                            >
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <nav class="mt-3">
                <ul class="pagination justify-content-end mb-0">
                    <li class="page-item disabled">
                        <a class="page-link" href="#">&laquo;</a>
                    </li>
                    <li class="page-item active">
                        <a class="page-link" href="#">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">3</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">&raquo;</a>
                    </li>
                </ul>
            </nav>
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

/* Warning theme for unverified users */
.bg-warning.bg-opacity-10:hover {
    background-color: rgba(255, 193, 7, 0.15) !important;
}

.card:hover .text-warning {
    color: #b45309 !important;
}
</style>

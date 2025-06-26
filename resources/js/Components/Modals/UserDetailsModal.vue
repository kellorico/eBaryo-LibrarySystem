<script setup>
import { ref, watch, nextTick, onMounted, onUnmounted } from "vue";
import { Modal } from "bootstrap";

const props = defineProps({
    user: Object,
    show: Boolean,
});

const emit = defineEmits(["close"]);

let modalInstance = null;
const modalRef = ref(null);

const initializeModal = () => {
    if (modalRef.value && !modalInstance) {
        modalInstance = new Modal(modalRef.value);

        modalRef.value.addEventListener("hidden.bs.modal", () => {
            emit("close");
        });
    }
};

const showModal = () => {
    if (modalInstance) {
        modalInstance.show();
    }
};

const hideModal = () => {
    if (modalInstance) {
        modalInstance.hide();
    }
};

watch(
    () => props.show,
    async (visible) => {
        if (visible && props.user) {
            await nextTick();
            initializeModal();
            showModal();
        } else {
            hideModal();
        }
    }
);

onMounted(() => {
    if (props.show && props.user) {
        nextTick(() => {
            initializeModal();
        });
    }
});

onUnmounted(() => {
    if (modalInstance) {
        modalInstance.dispose();
        modalInstance = null;
    }
});

const openUserModal = (user) => {
    selectedUser.value = user;
    showModal.value = true;
};
</script>

<template>
    <div class="modal fade" tabindex="-1" ref="modalRef">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div
                    class="modal-header bg-gradient-primary text-white border-0"
                >
                    <div class="d-flex align-items-center">
                        <div class="user-avatar me-3">
                            <div class="avatar-circle">
                                <img
                                    v-if="user && user.avatar_url"
                                    :src="user.avatar_url"
                                    alt="Avatar"
                                    style="
                                        width: 36px;
                                        height: 36px;
                                        border-radius: 50%;
                                        object-fit: cover;
                                    "
                                />
                                <i v-else class="fas fa-user"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0 fw-bold">
                                {{ user?.name || "User Details" }}
                            </h5>
                            <small class="text-white-50">{{
                                user?.email || "No email available"
                            }}</small>
                        </div>
                    </div>
                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body p-4">
                    <div v-if="!user" class="text-center py-5">
                        <div class="empty-state">
                            <i
                                class="fas fa-user-slash text-muted mb-3"
                                style="font-size: 3rem"
                            ></i>
                            <p class="text-muted">No user selected</p>
                        </div>
                    </div>
                    <div v-else class="user-details">
                        <!-- User Status Badges Section (add after modal-title or in user-details) -->
                        <div class="mb-3 d-flex flex-wrap gap-2">
                            <span
                                :class="
                                    user.verified == 1
                                        ? 'badge bg-success'
                                        : 'badge bg-warning text-dark'
                                "
                            >
                                <i class="fas fa-check-circle me-1"></i>
                                {{
                                    user.verified == 1
                                        ? "Verified"
                                        : "Not Verified"
                                }}
                            </span>
                            <span
                                :class="
                                    user.is_student
                                        ? 'badge bg-info'
                                        : 'badge bg-secondary'
                                "
                            >
                                <i
                                    :class="
                                        user.is_student
                                            ? 'fas fa-user-graduate me-1'
                                            : 'fas fa-user-tie me-1'
                                    "
                                />
                                {{
                                    user.is_student
                                        ? "Student"
                                        : "Not a Student"
                                }}
                            </span>
                            <span
                                :class="
                                    user.email_verified_at
                                        ? 'badge bg-success'
                                        : 'badge bg-danger'
                                "
                            >
                                <i class="fas fa-envelope me-1"></i>
                                {{
                                    user.email_verified_at
                                        ? "Email Verified"
                                        : "Email Not Verified"
                                }}
                            </span>
                        </div>

                        <!-- User Information -->
                        <div class="user-info-section">
                            <h6 class="section-title mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                Personal Information
                            </h6>
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-id-card me-2"></i>
                                        User ID
                                    </div>
                                    <div class="info-value">{{ user.id }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-user me-2"></i>
                                        Full Name
                                    </div>
                                    <div class="info-value">
                                        {{ user.name }}
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-envelope me-2"></i>
                                        Email Address
                                    </div>
                                    <div class="info-value">
                                        {{ user.email }}
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-user-shield me-2"></i>
                                        Role
                                    </div>
                                    <div class="info-value">
                                        <span class="role-badge">{{
                                            user.role
                                        }}</span>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i
                                            class="fas fa-map-marker-alt me-2"
                                        ></i>
                                        Barangay
                                    </div>
                                    <div class="info-value">
                                        {{ user.barangay || "Not specified" }}
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-school me-2"></i>
                                        School Name
                                    </div>
                                    <div class="info-value">
                                        {{
                                            user.school_name || "Not specified"
                                        }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        <i class="fas fa-times me-2"></i>
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.bg-gradient-primary {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.user-avatar .avatar-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
}

.status-card {
    display: flex;
    align-items: center;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
    margin-bottom: 0.5rem;
}

.status-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}

.status-card.verified {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border-color: #c3e6cb;
}

.status-card.unverified {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border-color: #ffeaa7;
}

.status-card.student {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    border-color: #bee5eb;
}

.status-card.non-student {
    background: linear-gradient(135deg, #e2e3e5 0%, #d6d8db 100%);
    border-color: #d6d8db;
}

.status-icon {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.5rem;
    font-size: 1rem;
}

.status-card.verified .status-icon {
    background: #28a745;
    color: white;
}

.status-card.unverified .status-icon {
    background: #ffc107;
    color: #212529;
}

.status-card.student .status-icon {
    background: #17a2b8;
    color: white;
}

.status-card.non-student .status-icon {
    background: #6c757d;
    color: white;
}

.status-content {
    flex: 1;
}

.status-title {
    margin: 0;
    font-size: 0.8rem;
    font-weight: 600;
    color: #495057;
}

.status-badge {
    display: inline-block;
    padding: 0.15rem 0.5rem;
    border-radius: 14px;
    font-size: 0.7rem;
    font-weight: 600;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.section-title {
    color: #495057;
    font-weight: 600;
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 0.25rem;
    font-size: 1rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 0.5rem;
}

.info-item {
    background: #f8f9fa;
    border-radius: 6px;
    padding: 0.5rem;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.info-item:hover {
    background: #e9ecef;
    transform: translateX(2px);
}

.info-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 0.2rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 0.9rem;
    font-weight: 500;
    color: #212529;
}

.role-badge {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    padding: 0.15rem 0.5rem;
    border-radius: 14px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.empty-state {
    color: #6c757d;
}

.modal-content {
    border-radius: 10px;
    overflow: hidden;
}

.modal-header {
    padding: 0.75rem 1rem;
}

.modal-body {
    padding: 0.75rem 1rem;
}

.modal-footer {
    padding: 0.5rem 1rem;
}

.btn {
    border-radius: 6px;
    font-weight: 500;
    padding: 0.4rem 1rem;
    transition: all 0.3s ease;
    font-size: 0.8rem;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
}

.modal-dialog {
    max-width: 540px;
}

@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
    .status-card {
        flex-direction: column;
        text-align: center;
    }
    .status-icon {
        margin-right: 0;
        margin-bottom: 0.3rem;
    }
    .modal-dialog {
        margin: 0.5rem;
        max-width: 98vw;
    }
}
</style>

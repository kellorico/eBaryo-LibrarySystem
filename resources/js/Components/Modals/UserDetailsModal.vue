<script setup>
import { ref, watch, nextTick, onMounted, onUnmounted } from 'vue';
import { Modal } from 'bootstrap';

const props = defineProps({
    user: Object,
    show: Boolean,
});

const emit = defineEmits(['close']);

let modalInstance = null;
const modalRef = ref(null);

const initializeModal = () => {
    if (modalRef.value && !modalInstance) {
        modalInstance = new Modal(modalRef.value);
        
        modalRef.value.addEventListener('hidden.bs.modal', () => {
            emit('close');
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

watch(() => props.show, async (visible) => {
    if (visible && props.user) {
        await nextTick();
        initializeModal();
        showModal();
    } else {
        hideModal();
    }
});

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
                <div class="modal-header bg-gradient-primary text-white border-0">
                    <div class="d-flex align-items-center">
                        <div class="user-avatar me-3">
                            <div class="avatar-circle">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0 fw-bold">{{ user?.name || 'User Details' }}</h5>
                            <small class="text-white-50">{{ user?.email || 'No email available' }}</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div v-if="!user" class="text-center py-5">
                        <div class="empty-state">
                            <i class="fas fa-user-slash text-muted mb-3" style="font-size: 3rem;"></i>
                            <p class="text-muted">No user selected</p>
                        </div>
                    </div>
                    <div v-else class="user-details">
                        <!-- User Status Cards -->
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <div class="status-card" :class="user.verified ? 'verified' : 'unverified'">
                                    <div class="status-icon">
                                        <i :class="user.verified == 1 ? 'fas fa-check-circle' : 'fas fa-clock'"></i>
                                    </div>
                                    <div class="status-content">
                                        <h6 class="status-title">Verification Status</h6>
                                        <span class="status-badge" :class="user.verified == 1 ? 'bg-success' : 'bg-warning'">
                                            {{ user.verified == 1 ? 'Verified' : 'Not Verified' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="status-card" :class="user.is_student ? 'student' : 'non-student'">
                                    <div class="status-icon">
                                        <i :class="user.is_student ? 'fas fa-graduation-cap' : 'fas fa-user-tie'"></i>
                                    </div>
                                    <div class="status-content">
                                        <h6 class="status-title">User Type</h6>
                                        <span class="status-badge" :class="user.is_student ? 'bg-info' : 'bg-secondary'">
                                            {{ user.is_student ? 'Student' : 'Not a Student' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
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
                                    <div class="info-value">{{ user.name }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-envelope me-2"></i>
                                        Email Address
                                    </div>
                                    <div class="info-value">{{ user.email }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-user-shield me-2"></i>
                                        Role
                                    </div>
                                    <div class="info-value">
                                        <span class="role-badge">{{ user.role }}</span>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        Barangay
                                    </div>
                                    <div class="info-value">{{ user.barangay || 'Not specified' }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="fas fa-school me-2"></i>
                                        School Name
                                    </div>
                                    <div class="info-value">{{ user.school_name || 'Not specified' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
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
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.status-card {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-radius: 12px;
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.status-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-size: 1.2rem;
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
    font-size: 0.875rem;
    font-weight: 600;
    color: #495057;
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.section-title {
    color: #495057;
    font-weight: 600;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 0.5rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
}

.info-item {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.info-item:hover {
    background: #e9ecef;
    transform: translateX(4px);
}

.info-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 1rem;
    font-weight: 500;
    color: #212529;
}

.role-badge {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.empty-state {
    color: #6c757d;
}

.modal-content {
    border-radius: 16px;
    overflow: hidden;
}

.modal-header {
    padding: 1.5rem;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    padding: 1rem 1.5rem;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    padding: 0.5rem 1.5rem;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
        margin-bottom: 0.5rem;
    }
}
</style>

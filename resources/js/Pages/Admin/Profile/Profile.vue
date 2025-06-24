<script setup>
import { ref, computed, onMounted } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";

const page = usePage();
const user = computed(() => page.props.auth.user || {});

const showEditModal = ref(false);
const avatarHover = ref(false);
const avatarInput = ref(null);
const stats = ref({ books_managed: 12, users_verified: 5 });

const activeTab = ref("overview");
const tabs = [
    { id: "overview", name: "Overview", icon: "fa fa-user-circle" },
    { id: "security", name: "Security", icon: "fa fa-shield-alt" },
    { id: "activity", name: "Activity", icon: "fa fa-history" },
];

const activityLog = ref([
    {
        id: 1,
        icon: "fa fa-book",
        description: 'Added new book "The Alchemist"',
        timestamp: "2 hours ago",
    },
    {
        id: 2,
        icon: "fa fa-user-check",
        description: 'Verified user "Jane Doe"',
        timestamp: "1 day ago",
    },
    {
        id: 3,
        icon: "fa fa-key",
        description: "Password was changed",
        timestamp: "3 days ago",
    },
    {
        id: 4,
        icon: "fa fa-sign-in-alt",
        description: "Logged in from new device",
        timestamp: "3 days ago",
    },
]);

const form = ref({
    name: "",
    email: "",
    barangay: "",
    is_student: false,
    school_name: "",
    verified: false,
});

const passwordForm = ref({
    current: "",
    new: "",
    confirm: "",
});

const toast = ref({ show: false, message: "", success: true });

function openEditModal() {
    form.value = {
        name: user.value.name || "",
        email: user.value.email || "",
        barangay: user.value.barangay || "",
        is_student: !!user.value.is_student,
        school_name: user.value.school_name || "",
        verified: user.value.verified == "1" || user.value.verified === true,
    };
    showEditModal.value = true;
}

function closeEditModal() {
    showEditModal.value = false;
}

function submitEditProfile() {
    router.put(
        '/profile',
        form.value,
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.value = {
                    show: true,
                    message: "Profile updated successfully!",
                    success: true,
                };
                showEditModal.value = false;
                setTimeout(() => (toast.value.show = false), 3000);
            },
            onError: (errors) => {
                toast.value = {
                    show: true,
                    message: errors && Object.values(errors)[0] ? Object.values(errors)[0] : "Failed to update profile.",
                    success: false,
                };
                setTimeout(() => (toast.value.show = false), 3000);
            },
        }
    );
}

function submitChangePassword() {
    if (passwordForm.value.new !== passwordForm.value.confirm) {
        toast.value = {
            show: true,
            message: "Passwords do not match.",
            success: false,
        };
        return setTimeout(() => (toast.value.show = false), 3000);
    }
    router.put(
        '/profile/password',
        {
            current: passwordForm.value.current,
            new: passwordForm.value.new,
            new_confirmation: passwordForm.value.confirm,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.value = {
                    show: true,
                    message: "Password changed successfully!",
                    success: true,
                };
                passwordForm.value = { current: "", new: "", confirm: "" };
                setTimeout(() => (toast.value.show = false), 3000);
            },
            onError: (errors) => {
                toast.value = {
                    show: true,
                    message: errors && Object.values(errors)[0] ? Object.values(errors)[0] : "Failed to change password.",
                    success: false,
                };
                setTimeout(() => (toast.value.show = false), 3000);
            },
        }
    );
}

function triggerAvatarUpload() {
    avatarInput.value && avatarInput.value.click();
}

function handleAvatarChange(e) {
    const file = e.target.files[0];
    if (file) {
        const formData = new FormData();
        formData.append('avatar', file);
        router.post('/profile/avatar', formData, {
            forceFormData: true,
            onSuccess: (page) => {
                if (page.props.avatar_url) {
                    user.value.avatar_url = page.props.avatar_url;
                }
                toast.value = {
                    show: true,
                    message: "Avatar updated!",
                    success: true,
                };
                setTimeout(() => (toast.value.show = false), 3000);
            },
            onError: (errors) => {
                toast.value = {
                    show: true,
                    message: errors && Object.values(errors)[0] ? Object.values(errors)[0] : "Failed to upload avatar.",
                    success: false,
                };
                setTimeout(() => (toast.value.show = false), 3000);
            },
        });
    }
}

function sendVerificationEmail() {
    router.post(
        '/profile/send-verification',
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.value = {
                    show: true,
                    message: "Verification email sent!",
                    success: true,
                };
                setTimeout(() => (toast.value.show = false), 3000);
            },
            onError: (errors) => {
                toast.value = {
                    show: true,
                    message: errors && Object.values(errors)[0] ? Object.values(errors)[0] : "Failed to send verification email.",
                    success: false,
                };
                setTimeout(() => (toast.value.show = false), 3000);
            },
        }
    );
}

onMounted(() => {
    if (page.props.status === 'verified') {
        toast.value = {
            show: true,
            message: "Your email is now verified!",
            success: true,
        };
        setTimeout(() => (toast.value.show = false), 3000);
    }
});
</script>

<template>
    <AdminLayout>
        <div class="container">
            <div class="row justify-content-center">
                
                    <div
                        class="profile-card card border-0 shadow-lg rounded-4 overflow-hidden"
                    >
                        <!-- Banner/Cover Image -->
                        <div class="profile-banner position-relative">
                            <img
                                src="/assets/images/image.png"
                                alt="Profile Banner"
                                class="w-100 h-100 object-fit-cover"
                            />
                            <div
                                class="profile-accent-bar bg-gradient-success position-absolute top-0 start-0 w-100"
                            ></div>
                            <button
                                class="btn btn-success position-absolute top-0 end-0 m-3 z-3"
                                style="border-radius: 50px;"
                                @click="openEditModal"
                            >
                                <i class="fa fa-edit me-1"></i> Edit Profile
                            </button>
                        </div>
                        <div
                            class="d-flex flex-column align-items-center p-4 pb-3 bg-white position-relative"
                        >
                            <div
                                class="profile-avatar-wrapper position-relative"
                                @mouseenter="avatarHover = true"
                                @mouseleave="avatarHover = false"
                            >
                                <div class="admin-avatar-lg position-relative">
                                    <img
                                        v-if="user.avatar_url"
                                        :src="user.avatar_url"
                                        alt="Avatar"
                                        class="avatar-img"
                                    />
                                    <i v-else class="fas fa-user"></i>
                                    <button
                                        v-if="avatarHover"
                                        class="btn btn-sm btn-light avatar-upload-btn position-absolute top-50 start-50 translate-middle"
                                        @click="triggerAvatarUpload"
                                        title="Change Avatar"
                                    >
                                        <i class="fas fa-camera"></i>
                                    </button>
                                    <input
                                        ref="avatarInput"
                                        type="file"
                                        class="d-none"
                                        accept="image/*"
                                        @change="handleAvatarChange"
                                    />
                                </div>
                            </div>
                            <h3 class="fw-bold mb-1 mt-2">{{ user.name }}</h3>
                            <p class="text-muted mb-2">
                                <i class="fa fa-envelope me-2"></i
                                >{{ user.email }}
                            </p>
                            <span class="badge bg-success text-uppercase mb-2"
                                ><i class="fa fa-user-shield me-1"></i
                                >{{ user.role }}</span
                            >
                        </div>

                        <!-- Tabs Navigation -->
                        <ul
                            class="nav nav-pills justify-content-center p-3 bg-white border-top border-bottom"
                        >
                            <li
                                class="nav-item"
                                v-for="tab in tabs"
                                :key="tab.id"
                            >
                                <button
                                    class="nav-link"
                                    :class="{ active: activeTab === tab.id }"
                                    @click="activeTab = tab.id"
                                >
                                    <i :class="tab.icon" class="me-2"></i>
                                    {{ tab.name }}
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="p-4 bg-light">
                            <!-- Overview Tab -->
                            <div v-if="activeTab === 'overview'">
                                <div class="row g-3">
                                    <div
                                        class="col-12 col-md-6 d-flex align-items-center"
                                    >
                                        <i
                                            class="fa fa-map-marker-alt text-success me-2"
                                            v-tooltip="'Barangay/Location'"
                                        />
                                        <div>
                                            <div class="small text-muted">
                                                Barangay
                                                <i
                                                    class="fa fa-info-circle text-secondary ms-1"
                                                    v-tooltip="
                                                        'Your registered barangay or location.'
                                                    "
                                                />
                                            </div>
                                            <div class="fw-semibold">
                                                {{ user.barangay || "N/A" }}
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="col-12 col-md-6 d-flex align-items-center"
                                    >
                                        <i
                                            class="fa fa-user-graduate text-primary me-2"
                                            v-tooltip="'Student Status'"
                                        />
                                        <div>
                                            <div class="small text-muted">
                                                Is Student
                                                <i
                                                    class="fa fa-info-circle text-secondary ms-1"
                                                    v-tooltip="
                                                        'Are you currently a student?'
                                                    "
                                                />
                                            </div>
                                            <div class="fw-semibold">
                                                <span
                                                    :class="
                                                        user.is_student
                                                            ? 'badge bg-primary'
                                                            : 'badge bg-secondary'
                                                    "
                                                >
                                                    {{
                                                        user.is_student
                                                            ? "Yes"
                                                            : "No"
                                                    }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="col-12 col-md-6 d-flex align-items-center"
                                    >
                                        <i
                                            class="fa fa-school text-info me-2"
                                            v-tooltip="'School Name'"
                                        />
                                        <div>
                                            <div class="small text-muted">
                                                School Name
                                                <i
                                                    class="fa fa-info-circle text-secondary ms-1"
                                                    v-tooltip="
                                                        'Your school, if applicable.'
                                                    "
                                                />
                                            </div>
                                            <div class="fw-semibold">
                                                {{ user.school_name || "N/A" }}
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="col-12 col-md-6 d-flex align-items-center"
                                    >
                                        <i
                                            class="fa fa-check-circle me-2"
                                            :class="
                                                user.verified == '1' ||
                                                user.verified === true
                                                    ? 'text-success'
                                                    : 'text-danger'
                                            "
                                            v-tooltip="'Account Verification'"
                                        />
                                        <div>
                                            <div class="small text-muted">
                                                Verified
                                                <i
                                                    class="fa fa-info-circle text-secondary ms-1"
                                                    v-tooltip="
                                                        'Is your account verified by admin?'
                                                    "
                                                />
                                            </div>
                                            <div class="fw-semibold">
                                                <span
                                                    :class="
                                                        user.verified == '1' ||
                                                        user.verified === true
                                                            ? 'badge bg-success'
                                                            : 'badge bg-danger'
                                                    "
                                                >
                                                    {{
                                                        user.verified == "1" ||
                                                        user.verified === true
                                                            ? "Yes"
                                                            : "No"
                                                    }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="col-12 col-md-6 d-flex align-items-center"
                                    >
                                        <i
                                            class="fa fa-envelope-open me-2"
                                            :class="
                                                user.email_verified_at
                                                    ? 'text-success'
                                                    : 'text-danger'
                                            "
                                            v-tooltip="'Email Verification'"
                                        />
                                        <div>
                                            <div class="small text-muted">
                                                Email Verified
                                                <i
                                                    class="fa fa-info-circle text-secondary ms-1"
                                                    v-tooltip="
                                                        'Is your email address verified?'
                                                    "
                                                />
                                            </div>
                                            <div class="fw-semibold d-flex align-items-center gap-2">
                                                <span
                                                    :class="
                                                        user.email_verified_at
                                                            ? 'badge bg-success'
                                                            : 'badge bg-danger'
                                                    "
                                                >
                                                    {{
                                                        user.email_verified_at
                                                            ? "Yes"
                                                            : "No"
                                                    }}
                                                </span>
                                                <button
                                                    v-if="!user.email_verified_at"
                                                    class="btn btn-sm btn-outline-success ms-2"
                                                    @click="sendVerificationEmail"
                                                    style="font-size: 0.85rem;"
                                                >
                                                    <i class="fa fa-envelope me-1"></i> Verify Email
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Quick Stats Section -->
                                <div class="row g-3 mt-4">
                                    <div class="col-12 col-md-6">
                                        <div
                                            class="stat-card bg-white shadow-sm rounded-3 p-3 d-flex align-items-center"
                                        >
                                            <i
                                                class="fa fa-book-open text-success fs-4 me-3"
                                            ></i>
                                            <div>
                                                <div class="small text-muted">
                                                    Books Managed
                                                </div>
                                                <div class="fw-bold fs-5">
                                                    {{
                                                        stats.books_managed || 0
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div
                                            class="stat-card bg-white shadow-sm rounded-3 p-3 d-flex align-items-center"
                                        >
                                            <i
                                                class="fa fa-users text-primary fs-4 me-3"
                                            ></i>
                                            <div>
                                                <div class="small text-muted">
                                                    Users Verified
                                                </div>
                                                <div class="fw-bold fs-5">
                                                    {{
                                                        stats.users_verified ||
                                                        0
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Security Tab -->
                            <div v-if="activeTab === 'security'">
                                <h5 class="fw-bold mb-4">Security Settings</h5>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="card shadow-sm">
                                            <div class="card-body">
                                                <h6 class="card-title">
                                                    Change Password
                                                </h6>
                                                <form
                                                    @submit.prevent="
                                                        submitChangePassword
                                                    "
                                                >
                                                    <div class="mb-3">
                                                        <label
                                                            class="form-label"
                                                            >Current
                                                            Password</label
                                                        >
                                                        <input
                                                            v-model="
                                                                passwordForm.current
                                                            "
                                                            type="password"
                                                            class="form-control custom-input"
                                                            required
                                                        />
                                                    </div>
                                                    <div class="mb-3">
                                                        <label
                                                            class="form-label"
                                                            >New Password</label
                                                        >
                                                        <input
                                                            v-model="
                                                                passwordForm.new
                                                            "
                                                            type="password"
                                                            class="form-control custom-input"
                                                            required
                                                        />
                                                    </div>
                                                    <div class="mb-3">
                                                        <label
                                                            class="form-label"
                                                            >Confirm New
                                                            Password</label
                                                        >
                                                        <input
                                                            v-model="
                                                                passwordForm.confirm
                                                            "
                                                            type="password"
                                                            class="form-control custom-input"
                                                            required
                                                        />
                                                    </div>
                                                    <button
                                                        type="submit"
                                                        class="btn btn-success action-btn"
                                                    >
                                                        <i
                                                            class="fas fa-key me-2"
                                                        ></i>
                                                        Update Password
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mt-3 mt-lg-0">
                                        <div class="card shadow-sm">
                                            <div class="card-body">
                                                <h6 class="card-title">
                                                    Two-Factor Authentication
                                                </h6>
                                                <p class="text-muted small">
                                                    Add an additional layer of
                                                    security to your account by
                                                    enabling two-factor
                                                    authentication.
                                                </p>
                                                <button
                                                    class="btn btn-outline-secondary"
                                                    disabled
                                                >
                                                    Enable 2FA (Coming Soon)
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Activity Tab -->
                            <div v-if="activeTab === 'activity'">
                                <h5 class="fw-bold mb-4">Recent Activity</h5>
                                <ul class="list-group list-group-flush">
                                    <li
                                        v-for="item in activityLog"
                                        :key="item.id"
                                        class="list-group-item d-flex justify-content-between align-items-center"
                                    >
                                        <div>
                                            <i
                                                :class="item.icon"
                                                class="me-2 text-secondary"
                                            ></i>
                                            <span>{{ item.description }}</span>
                                        </div>
                                        <small class="text-muted">{{
                                            item.timestamp
                                        }}</small>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
            </div>

            <!-- Toast/Alert for Success/Error -->
            <div
                v-if="toast.show"
                :class="[
                    'toast show align-items-center position-fixed bottom-0 end-0 m-4',
                    toast.success
                        ? 'bg-success text-white'
                        : 'bg-danger text-white',
                ]"
                style="z-index: 9999; min-width: 220px"
                role="alert"
            >
                <div class="d-flex">
                    <div class="toast-body">
                        <i
                            :class="
                                toast.success
                                    ? 'fa fa-check-circle me-2'
                                    : 'fa fa-times-circle me-2'
                            "
                        ></i>
                        {{ toast.message }}
                    </div>
                    <button
                        type="button"
                        class="btn-close btn-close-white me-2 m-auto"
                        @click="toast.show = false"
                    ></button>
                </div>
            </div>

            <!-- Edit Profile Modal (retained for editing) -->
            <div
                class="modal fade"
                :class="{ show: showEditModal }"
                tabindex="-1"
                :style="{
                    display: showEditModal ? 'block' : 'none',
                    background: 'rgba(0,0,0,0.2)',
                }"
                v-if="showEditModal"
            >
                <div class="modal-dialog">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        <form @submit.prevent="submitEditProfile">
                            <div
                                class="modal-header bg-gradient-success text-white border-0 rounded-top-4"
                            >
                                <div>
                                    <h5
                                        class="modal-title mb-0 fw-bold text-uppercase"
                                    >
                                        Edit Profile
                                    </h5>
                                    <small class="text-white-50"
                                        >Update your profile details
                                        below</small
                                    >
                                </div>
                                <button
                                    type="button"
                                    class="btn-close btn-close-white"
                                    @click="closeEditModal"
                                ></button>
                            </div>
                            <div class="modal-body p-4 bg-light">
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        class="form-control custom-input"
                                        required
                                    />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        class="form-control custom-input"
                                        required
                                    />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Barangay</label>
                                    <input
                                        v-model="form.barangay"
                                        type="text"
                                        class="form-control custom-input"
                                    />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Is Student</label>
                                    <select
                                        v-model="form.is_student"
                                        class="form-select custom-input"
                                    >
                                        <option :value="true">Yes</option>
                                        <option :value="false">No</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"
                                        >School Name</label
                                    >
                                    <input
                                        v-model="form.school_name"
                                        type="text"
                                        class="form-control custom-input"
                                    />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Verified</label>
                                    <select
                                        v-model="form.verified"
                                        class="form-select custom-input"
                                    >
                                        <option :value="true">Yes</option>
                                        <option :value="false">No</option>
                                    </select>
                                </div>
                            </div>
                            <div
                                class="modal-footer border-0 bg-light rounded-bottom-4"
                            >
                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    @click="closeEditModal"
                                >
                                    <i class="fas fa-times me-2"></i> Cancel
                                </button>
                                <button
                                    type="submit"
                                    class="btn btn-success action-btn"
                                >
                                    <i class="fas fa-save me-2"></i> Save
                                    Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div v-if="showEditModal" class="modal-backdrop fade show"></div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.profile-card {
    background: #fff;
    border-radius: 1.25rem;
    box-shadow: 0 4px 24px rgba(40, 167, 69, 0.08);
    overflow: hidden;
}
.profile-banner {
    height: 120px;
    width: 100%;
    background: #e9ecef;
    position: relative;
}
.profile-banner img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}
.profile-accent-bar {
    height: 8px;
    width: 100%;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    opacity: 0.7;
    z-index: 2;
}
.profile-avatar-wrapper {
    margin-top: -48px;
    margin-bottom: 8px;
    z-index: 3;
}
.admin-avatar-lg {
    width: 96px;
    height: 96px;
    background: #198754;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 2.5rem;
    border: 4px solid #fff;
    box-shadow: 0 2px 8px rgba(40, 167, 69, 0.1);
    position: relative;
    overflow: hidden;
}
.avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}
.avatar-upload-btn {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid #e9ecef;
    color: #198754;
    font-size: 1.1rem;
    padding: 0.3rem 0.5rem;
    border-radius: 50%;
    transition: background 0.2s;
    z-index: 4;
}
.avatar-upload-btn:hover {
    background: #20c997;
    color: #fff;
}
.stat-card {
    min-height: 80px;
}
.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}
.custom-input {
    border: 1px solid #e9ecef;
    border-radius: 6px;
    padding: 0.5rem;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background: white;
}
.custom-input:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.1rem rgba(40, 167, 69, 0.15);
    outline: none;
}
.action-btn {
    border-radius: 6px;
    font-weight: 500;
    padding: 0.5rem 1.2rem;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.9rem;
}
.action-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
}
.nav-pills .nav-link {
    font-weight: 600;
    color: #6c757d;
}
.nav-pills .nav-link.active {
    background-color: #198754 !important;
    color: #fff;
    box-shadow: 0 2px 8px rgba(25, 135, 84, 0.3);
}
</style>

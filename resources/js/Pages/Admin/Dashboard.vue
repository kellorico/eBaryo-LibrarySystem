<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { ref, onMounted, computed, watch } from "vue";
import { router } from "@inertiajs/vue3";
import { Bar } from "vue-chartjs";
import {
    Chart,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
} from "chart.js";
Chart.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);

const props = defineProps({
    totalBooks: Number,
    totalUsers: Number,
    activeUsers: Number,
    totalArchives: Number,
    publicArchives: Number,
    archivesByCategory: Object,
    recentArchives: Array,
    flash: String,
});

const showWelcome = ref(!!props.flash);
const showUploadModal = ref(false);
const uploadForm = ref({
    title: "",
    description: "",
    file: null,
    type: "document",
    category: "",
    is_public: false,
    errors: {},
});

// Recent activity state
const activities = ref([]);
const loadingActivity = ref(true);

const safeRecentArchives = computed(() =>
    Array.isArray(props.recentArchives) ? props.recentArchives : []
);


onMounted(() => {
    if (showWelcome.value) {
        setTimeout(() => (showWelcome.value = false), 5000);
    }
    // Fetch recent activity
    fetch("/recent-activity")
        .then((res) => res.json())
        .then((data) => {
            activities.value = data.activities || [];
        })
        .finally(() => (loadingActivity.value = false));
});

const resetForm = () => {
    uploadForm.value = {
        title: "",
        description: "",
        file: null,
        type: "document",
        category: "",
        is_public: false,
        errors: {},
    };
};
const openUploadModal = () => {
    resetForm();
    showUploadModal.value = true;
};
const closeUploadModal = () => {
    showUploadModal.value = false;
};
const handleFileChange = (e) => {
    uploadForm.value.file = e.target.files[0];
};
const handleUpload = () => {
    uploadForm.value.errors = {};
    const formData = new FormData();
    formData.append("title", uploadForm.value.title);
    formData.append("description", uploadForm.value.description);
    formData.append("file", uploadForm.value.file);
    formData.append("type", uploadForm.value.type);
    formData.append("category", uploadForm.value.category);
    formData.append("is_public", uploadForm.value.is_public ? 1 : 0);
    router.post(route("admin.archive"), formData, {
        forceFormData: true,
        onSuccess: () => {
            closeUploadModal();
        },
        onError: (errors) => {
            uploadForm.value.errors = errors;
        },
        preserveScroll: true,
    });
};
const handleDownload = (archive) => {
    window.open(route("admin.archive", archive.id), "_blank");
};

const activityRange = ref("7d");
const activityLoading = ref(false);
const activityLabels = ref([]);
const activityBookReads = ref([]);
const activityArchiveUploads = ref([]);
const activityNewUsers = ref([]);

const fetchActivityOverview = async () => {
    activityLoading.value = true;
    const res = await fetch(
        `/dashboard/activity-overview?range=${activityRange.value}`
    );
    const data = await res.json();
    activityLabels.value = data.labels;
    activityBookReads.value = data.bookReads;
    activityArchiveUploads.value = data.archiveUploads;
    activityNewUsers.value = data.newUsers;
    activityLoading.value = false;
};
onMounted(fetchActivityOverview);
watch(activityRange, fetchActivityOverview);

const activityChartData = computed(() => ({
    labels: activityLabels.value,
    datasets: [
        {
            label: "Book Reads",
            backgroundColor: "#0d6efd",
            data: activityBookReads.value,
        },
        {
            label: "Archive Uploads",
            backgroundColor: "#28a745",
            data: activityArchiveUploads.value,
        },
        {
            label: "New Users",
            backgroundColor: "#fd7e14",
            data: activityNewUsers.value,
        },
    ],
}));
const activityChartOptions = {
    responsive: true,
    plugins: {
        legend: { position: "top" },
        title: { display: false },
    },
    scales: { y: { beginAtZero: true } },
};
</script>

<template>
    <AdminLayout>
        <Head title="Dashboard" />
        <!-- Floating Welcome Message -->
        <div
            v-if="showWelcome"
            class="floating-flash-message alert alert-success alert-dismissible fade show"
            role="alert"
        >
            <i class="fa-solid fa-smile-beam me-2"></i>
            {{ props.flash || "Welcome back, Admin!" }}
            <button
                type="button"
                class="btn-close"
                @click="showWelcome = false"
                aria-label="Close"
            ></button>
        </div>
        <div class="dashboard-container">
            <!-- Page Header -->
            <div class="page-header mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="page-title mb-1">Dashboard Overview</h2>
                        <p class="text-muted mb-0">
                            Welcome to your eBaryo Library management dashboard
                        </p>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row g-4 mb-5">
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card stat-card-primary">
                        <div class="stat-card-content">
                            <div class="stat-card-icon">
                                <i class="fa-solid fa-book-open"></i>
                            </div>
                            <div class="stat-card-info">
                                <h3 class="stat-card-number">
                                    {{ totalBooks }}
                                </h3>
                                <p class="stat-card-label">Total Books</p>
                                <div class="stat-card-trend">
                                    <i
                                        class="fa-solid fa-arrow-up text-success"
                                    ></i>
                                    <span class="text-success">+12%</span>
                                    <span class="text-muted"
                                        >from last month</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="stat-card stat-card-success">
                        <div class="stat-card-content">
                            <div class="stat-card-icon">
                                <i class="fa-solid fa-users"></i>
                            </div>
                            <div class="stat-card-info">
                                <h3 class="stat-card-number">
                                    {{ totalUsers }}
                                </h3>
                                <p class="stat-card-label">Total Users</p>
                                <div class="stat-card-trend">
                                    <i
                                        class="fa-solid fa-arrow-up text-success"
                                    ></i>
                                    <span class="text-success">+8%</span>
                                    <span class="text-muted"
                                        >from last month</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="stat-card stat-card-info">
                        <div class="stat-card-content">
                            <div class="stat-card-icon">
                                <i class="fa-solid fa-user-check"></i>
                            </div>
                            <div class="stat-card-info">
                                <h3 class="stat-card-number">
                                    {{ activeUsers }}
                                </h3>
                                <p class="stat-card-label">Active Users</p>
                                <div class="stat-card-trend">
                                    <i
                                        class="fa-solid fa-arrow-up text-success"
                                    ></i>
                                    <span class="text-success">+15%</span>
                                    <span class="text-muted"
                                        >from last month</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="stat-card stat-card-warning">
                        <div class="stat-card-content">
                            <div class="stat-card-icon">
                                <i class="fa-solid fa-download"></i>
                            </div>
                            <div class="stat-card-info">
                                <h3 class="stat-card-number">1,247</h3>
                                <p class="stat-card-label">Downloads</p>
                                <div class="stat-card-trend">
                                    <i
                                        class="fa-solid fa-arrow-up text-success"
                                    ></i>
                                    <span class="text-success">+23%</span>
                                    <span class="text-muted"
                                        >from last month</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="stat-card stat-card-archive">
                        <div class="stat-card-content">
                            <div class="stat-card-icon bg-archive">
                                <i class="fa-solid fa-archive"></i>
                            </div>
                            <div class="stat-card-info">
                                <h3 class="stat-card-number">
                                    {{ totalArchives }}
                                </h3>
                                <p class="stat-card-label">Total Archives</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="stat-card stat-card-public">
                        <div class="stat-card-content">
                            <div class="stat-card-icon bg-public">
                                <i class="fa-solid fa-globe"></i>
                            </div>
                            <div class="stat-card-info">
                                <h3 class="stat-card-number">
                                    {{ publicArchives }}
                                </h3>
                                <p class="stat-card-label">Public Archives</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-md-12">
                    <div class="stat-card stat-card-category">
                        <div class="stat-card-content">
                            <div class="stat-card-info">
                                <h5 class="mb-2">Archives by Category</h5>
                                <div class="d-flex flex-wrap gap-3">
                                    <div
                                        v-for="(
                                            count, cat
                                        ) in archivesByCategory"
                                        :key="cat"
                                        class="badge bg-secondary px-3 py-2 fs-6"
                                    >
                                        <i class="fa fa-folder me-1"></i>
                                        {{ cat }}: <b>{{ count }}</b>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Analytics Row -->
            <div class="row g-4 mb-5">
                <div class="col-xl-8">
                    <div class="dashboard-card">
                        <div
                            class="dashboard-card-header d-flex justify-content-between align-items-center"
                        >
                            <h5 class="dashboard-card-title mb-0">
                                <i class="fa-solid fa-chart-line me-2"></i>
                                Library Activity Overview
                            </h5>
                            <select
                                v-model="activityRange"
                                class="form-select form-select-sm"
                                style="width: 160px"
                            >
                                <option value="7d">Last 7 days</option>
                                <option value="30d">Last 30 days</option>
                                <option value="90d">Last 90 days</option>
                            </select>
                        </div>
                        <div class="dashboard-card-body">
                            <div
                                v-if="activityLoading"
                                class="text-center py-5"
                            >
                                <div
                                    class="spinner-border text-success"
                                    role="status"
                                >
                                    <span class="visually-hidden"
                                        >Loading...</span
                                    >
                                </div>
                            </div>
                            <div v-else>
                                <Bar
                                    :data="activityChartData"
                                    :options="activityChartOptions"
                                    style="min-height: 320px"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fa-solid fa-pie-chart me-2"></i>
                                Book Categories
                            </h5>
                        </div>
                        <div class="dashboard-card-body">
                            <div class="category-stats">
                                <div class="category-item">
                                    <div class="category-info">
                                        <div
                                            class="category-color"
                                            style="background: #198754"
                                        ></div>
                                        <span class="category-name"
                                            >Story Books</span
                                        >
                                    </div>
                                    <span class="category-percentage">35%</span>
                                </div>
                                <div class="category-item">
                                    <div class="category-info">
                                        <div
                                            class="category-color"
                                            style="background: #0d6efd"
                                        ></div>
                                        <span class="category-name"
                                            >Educational</span
                                        >
                                    </div>
                                    <span class="category-percentage">28%</span>
                                </div>
                                <div class="category-item">
                                    <div class="category-info">
                                        <div
                                            class="category-color"
                                            style="background: #fd7e14"
                                        ></div>
                                        <span class="category-name"
                                            >Agriculture</span
                                        >
                                    </div>
                                    <span class="category-percentage">22%</span>
                                </div>
                                <div class="category-item">
                                    <div class="category-info">
                                        <div
                                            class="category-color"
                                            style="background: #6f42c1"
                                        ></div>
                                        <span class="category-name"
                                            >Cultural Heritage</span
                                        >
                                    </div>
                                    <span class="category-percentage">15%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity and Quick Actions -->
            <div class="row g-4 mb-5">
                <div class="col-xl-6">
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fa-solid fa-clock me-2"></i>
                                Recent Activity
                            </h5>
                            <a href="#" class="btn btn-sm btn-outline-primary"
                                >View All</a
                            >
                        </div>
                        <div class="dashboard-card-body">
                            <div class="activity-list">
                                <div
                                    v-if="loadingActivity"
                                    class="text-center py-4"
                                >
                                    <div
                                        class="spinner-border text-success"
                                        role="status"
                                    >
                                        <span class="visually-hidden"
                                            >Loading...</span
                                        >
                                    </div>
                                </div>
                                <template v-else>
                                    <div
                                        v-if="activities.length === 0"
                                        class="text-muted text-center py-4"
                                    >
                                        <i
                                            class="fa-regular fa-bell-slash fa-2x mb-2"
                                        ></i>
                                        <div>No recent activity</div>
                                    </div>
                                    <div
                                        v-else
                                        v-for="activity in activities"
                                        :key="activity.id"
                                        class="activity-item"
                                    >
                                        <div
                                            class="activity-icon"
                                            :class="activity.color_class"
                                        >
                                            <i
                                                :class="[
                                                    'fa-solid',
                                                    activity.icon || 'fa-bell',
                                                ]"
                                            ></i>
                                        </div>
                                        <div class="activity-content">
                                            <p class="activity-text">
                                                <strong
                                                    >{{
                                                        activity.title
                                                    }}:</strong
                                                >
                                                {{ activity.message }}
                                            </p>
                                            <small class="activity-time">{{
                                                activity.time_ago
                                            }}</small>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="dashboard-card">
                        <div
                            class="dashboard-card-header d-flex justify-content-between align-items-center"
                        >
                            <h5 class="dashboard-card-title mb-0">
                                <i class="fa-solid fa-clock me-2"></i> Recent
                                Archive Uploads
                            </h5>
                            <button
                                class="btn btn-success"
                                @click="openUploadModal"
                            >
                                <i class="fa fa-upload me-2"></i> Quick Upload
                            </button>
                        </div>
                        <div class="dashboard-card-body p-0">
                            <div
                                v-if="safeRecentArchives.length === 0"
                                class="text-center py-4 text-muted"
                            >
                                No recent uploads.
                            </div>
                            <div v-else class="table-responsive">
                                <table
                                    class="table table-hover align-middle mb-0"
                                >
                                    <thead class="table-light">
                                        <tr>
                                            <th>Title</th>
                                            <th>Uploader</th>
                                            <th>Category</th>
                                            <th>Public</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="archive in safeRecentArchives"
                                            :key="archive.id"
                                        >
                                            <td>{{ archive.title }}</td>
                                            <td>
                                                {{
                                                    archive.uploader?.name ||
                                                    "Unknown"
                                                }}
                                            </td>
                                            <td>
                                                {{ archive.category || "-" }}
                                            </td>
                                            <td>
                                                <span
                                                    v-if="archive.is_public"
                                                    class="badge bg-success"
                                                    >Yes</span
                                                >
                                                <span
                                                    v-else
                                                    class="badge bg-secondary"
                                                    >No</span
                                                >
                                            </td>
                                            <td>
                                                {{
                                                    new Date(
                                                        archive.created_at
                                                    ).toLocaleString()
                                                }}
                                            </td>
                                            <td>
                                                <button
                                                    class="btn btn-outline-success btn-sm me-2"
                                                    @click="
                                                        handleDownload(archive)
                                                    "
                                                >
                                                    <i
                                                        class="fas fa-download"
                                                    ></i>
                                                </button>
                                                <a
                                                    :href="route('admin.archive')"
                                                    class="btn btn-outline-primary btn-sm"
                                                >
                                                    <i
                                                        class="fas fa-folder-open"
                                                    ></i>
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload Modal (reuse from DigitalArchive.vue) -->
            <div
                v-if="showUploadModal"
                class="modal fade show d-block"
                tabindex="-1"
                style="background: rgba(0, 0, 0, 0.3)"
            >
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-upload me-2 text-success"></i>
                                Upload Archive
                            </h5>
                            <button
                                type="button"
                                class="btn-close"
                                @click="closeUploadModal"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input
                                    v-model="uploadForm.title"
                                    type="text"
                                    class="form-control"
                                    placeholder="Document title"
                                />
                                <div
                                    v-if="uploadForm.errors.title"
                                    class="text-danger small"
                                >
                                    {{ uploadForm.errors.title }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea
                                    v-model="uploadForm.description"
                                    class="form-control"
                                    placeholder="Description (optional)"
                                ></textarea>
                                <div
                                    v-if="uploadForm.errors.description"
                                    class="text-danger small"
                                >
                                    {{ uploadForm.errors.description }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Type</label>
                                <select
                                    v-model="uploadForm.type"
                                    class="form-select"
                                >
                                    <option value="document">Document</option>
                                    <option value="photo">Photo</option>
                                    <option value="record">
                                        Historical Record
                                    </option>
                                </select>
                                <div
                                    v-if="uploadForm.errors.type"
                                    class="text-danger small"
                                >
                                    {{ uploadForm.errors.type }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select
                                    v-model="uploadForm.category"
                                    class="form-select"
                                >
                                    <option value="">Select Category</option>
                                    <option value="Resolution">
                                        Resolution
                                    </option>
                                    <option value="Photo">Photo</option>
                                    <option value="Event">Event</option>
                                    <option value="History">History</option>
                                    <option value="Misc">Misc</option>
                                </select>
                                <div
                                    v-if="uploadForm.errors.category"
                                    class="text-danger small"
                                >
                                    {{ uploadForm.errors.category }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">File</label>
                                <input
                                    type="file"
                                    class="form-control"
                                    @change="handleFileChange"
                                    accept=".pdf,.jpg,.jpeg,.png,.docx,.xlsx,.zip"
                                />
                                <div
                                    v-if="uploadForm.errors.file"
                                    class="text-danger small"
                                >
                                    {{ uploadForm.errors.file }}
                                </div>
                            </div>
                            <div class="form-check mb-3">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    v-model="uploadForm.is_public"
                                    id="isPublicCheck"
                                />
                                <label
                                    class="form-check-label"
                                    for="isPublicCheck"
                                    >Make Public (visible to all users)</label
                                >
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button
                                class="btn btn-secondary"
                                @click="closeUploadModal"
                            >
                                Cancel
                            </button>
                            <button
                                class="btn btn-success"
                                @click="handleUpload"
                            >
                                Upload
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.dashboard-container {
    max-width: 1400px;
    margin: 0 auto;
}

/* Page Header */
.page-header {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.page-title {
    color: #2c3e50;
    font-weight: 700;
    font-size: 1.75rem;
}

/* Statistics Cards */
.stat-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    border: none;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(
        90deg,
        var(--card-color),
        var(--card-color-light)
    );
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.stat-card-primary {
    --card-color: #0d6efd;
    --card-color-light: #6ea8fe;
}

.stat-card-success {
    --card-color: #198754;
    --card-color-light: #75b798;
}

.stat-card-info {
    --card-color: #0dcaf0;
    --card-color-light: #6edff6;
}

.stat-card-warning {
    --card-color: #fd7e14;
    --card-color-light: #ffb366;
}

.stat-card-content {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-card-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    background: linear-gradient(
        135deg,
        var(--card-color),
        var(--card-color-light)
    );
}

.stat-card-info {
    flex: 1;
}

.stat-card-number {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
    line-height: 1;
}

.stat-card-label {
    color: #6c757d;
    font-weight: 500;
    margin: 0.25rem 0;
}

.stat-card-trend {
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

/* Dashboard Cards */
.dashboard-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: none;
    overflow: hidden;
}

.dashboard-card-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: between;
    align-items: center;
}

.dashboard-card-title {
    margin: 0;
    font-weight: 600;
    color: #2c3e50;
    font-size: 1.1rem;
}

.dashboard-card-body {
    padding: 1.5rem;
}

/* Chart Placeholder */
.chart-placeholder {
    height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.chart-container {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.chart-bars {
    display: flex;
    align-items: end;
    justify-content: space-between;
    height: 200px;
    gap: 1rem;
}

.chart-bar {
    flex: 1;
    background: linear-gradient(180deg, #198754, #75b798);
    border-radius: 4px 4px 0 0;
    min-height: 20px;
    transition: all 0.3s ease;
}

.chart-bar:hover {
    background: linear-gradient(180deg, #146c43, #198754);
    transform: scaleY(1.05);
}

.chart-labels {
    display: flex;
    justify-content: space-between;
    color: #6c757d;
    font-size: 0.875rem;
    font-weight: 500;
}

/* Category Stats */
.category-stats {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.category-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    border-radius: 8px;
    background: #f8f9fa;
    transition: all 0.2s ease;
}

.category-item:hover {
    background: #e9ecef;
    transform: translateX(5px);
}

.category-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.category-color {
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.category-name {
    font-weight: 500;
    color: #2c3e50;
}

.category-percentage {
    font-weight: 600;
    color: #198754;
}

/* Activity List */
.activity-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem;
    border-radius: 8px;
    background: #f8f9fa;
    transition: all 0.2s ease;
}

.activity-item:hover {
    background: #e9ecef;
    transform: translateX(5px);
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.875rem;
    flex-shrink: 0;
}

.activity-icon-success {
    background: #198754;
}

.activity-icon-info {
    background: #0dcaf0;
}

.activity-icon-warning {
    background: #fd7e14;
}

.activity-icon-primary {
    background: #0d6efd;
}

.activity-content {
    flex: 1;
}

.activity-text {
    margin: 0;
    color: #2c3e50;
    font-size: 0.9rem;
}

.activity-time {
    color: #6c757d;
    font-size: 0.8rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-header {
        padding: 1rem;
    }

    .page-title {
        font-size: 1.5rem;
    }

    .stat-card {
        padding: 1rem;
    }

    .stat-card-number {
        font-size: 1.5rem;
    }

    .dashboard-card-header {
        padding: 1rem;
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }

    .dashboard-card-body {
        padding: 1rem;
    }
}

.floating-flash-message {
    position: fixed;
    top: 32px;
    right: 32px;
    z-index: 1055;
    min-width: 320px;
    max-width: 90vw;
    box-shadow: 0 8px 32px rgba(40, 167, 69, 0.15),
        0 1.5px 6px rgba(0, 0, 0, 0.08);
    border-radius: 12px;
    font-size: 1.05rem;
    pointer-events: auto;
    animation: floatIn 0.5s cubic-bezier(0.4, 2, 0.6, 1) both;
}
@keyframes floatIn {
    from {
        opacity: 0;
        transform: translateY(-30px) scale(0.98);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}
@media (max-width: 768px) {
    .floating-flash-message {
        top: 12px;
        right: 12px;
        min-width: 200px;
        font-size: 0.95rem;
    }
}

.bg-archive {
    background: #6c757d;
    color: #fff;
}
.bg-public {
    background: #28a745;
    color: #fff;
}
.stat-card-archive .stat-card-icon {
    background: #6c757d;
    color: #fff;
}
.stat-card-public .stat-card-icon {
    background: #28a745;
    color: #fff;
}
.stat-card-category .stat-card-content {
    background: #f8f9fa;
    border-radius: 12px;
}
</style>

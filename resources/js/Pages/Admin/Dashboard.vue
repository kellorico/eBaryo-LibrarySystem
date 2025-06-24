<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, onMounted } from 'vue';

defineProps({
    totalBooks: Number,
    totalUsers: Number,
    activeUsers: Number,
    flash: String
})

const showWelcome = ref(!!__props.flash);

onMounted(() => {
  if (showWelcome.value) {
    setTimeout(() => showWelcome.value = false, 5000);
  }
});
</script>

<template>
    <AdminLayout>
        <!-- Floating Welcome Message -->
        <div v-if="showWelcome" class="floating-flash-message alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-smile-beam me-2"></i>
            {{ flash || 'Welcome back, Admin!' }}
            <button type="button" class="btn-close" @click="showWelcome = false" aria-label="Close"></button>
        </div>
        <div class="dashboard-container">
            <!-- Page Header -->
            <div class="page-header mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="page-title mb-1">Dashboard Overview</h2>
                        <p class="text-muted mb-0">Welcome to your eBaryo Library management dashboard</p>
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
                                <h3 class="stat-card-number">{{ totalBooks }}</h3>
                                <p class="stat-card-label">Total Books</p>
                                <div class="stat-card-trend">
                                    <i class="fa-solid fa-arrow-up text-success"></i>
                                    <span class="text-success">+12%</span>
                                    <span class="text-muted">from last month</span>
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
                                <h3 class="stat-card-number">{{ totalUsers }}</h3>
                                <p class="stat-card-label">Total Users</p>
                                <div class="stat-card-trend">
                                    <i class="fa-solid fa-arrow-up text-success"></i>
                                    <span class="text-success">+8%</span>
                                    <span class="text-muted">from last month</span>
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
                                <h3 class="stat-card-number">{{ activeUsers }}</h3>
                                <p class="stat-card-label">Active Users</p>
                                <div class="stat-card-trend">
                                    <i class="fa-solid fa-arrow-up text-success"></i>
                                    <span class="text-success">+15%</span>
                                    <span class="text-muted">from last month</span>
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
                                    <i class="fa-solid fa-arrow-up text-success"></i>
                                    <span class="text-success">+23%</span>
                                    <span class="text-muted">from last month</span>
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
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fa-solid fa-chart-line me-2"></i>
                                Library Activity Overview
                            </h5>
                            <div class="dashboard-card-actions">
                                <select class="form-select form-select-sm">
                                    <option>Last 7 days</option>
                                    <option>Last 30 days</option>
                                    <option>Last 3 months</option>
                                </select>
                            </div>
                        </div>
                        <div class="dashboard-card-body">
                            <div class="chart-placeholder">
                                <div class="chart-container">
                                    <div class="chart-bars">
                                        <div class="chart-bar" style="height: 60%"></div>
                                        <div class="chart-bar" style="height: 80%"></div>
                                        <div class="chart-bar" style="height: 45%"></div>
                                        <div class="chart-bar" style="height: 90%"></div>
                                        <div class="chart-bar" style="height: 70%"></div>
                                        <div class="chart-bar" style="height: 85%"></div>
                                        <div class="chart-bar" style="height: 55%"></div>
                                    </div>
                                    <div class="chart-labels">
                                        <span>Mon</span>
                                        <span>Tue</span>
                                        <span>Wed</span>
                                        <span>Thu</span>
                                        <span>Fri</span>
                                        <span>Sat</span>
                                        <span>Sun</span>
                                    </div>
                                </div>
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
                                        <div class="category-color" style="background: #198754"></div>
                                        <span class="category-name">Story Books</span>
                                    </div>
                                    <span class="category-percentage">35%</span>
                                </div>
                                <div class="category-item">
                                    <div class="category-info">
                                        <div class="category-color" style="background: #0d6efd"></div>
                                        <span class="category-name">Educational</span>
                                    </div>
                                    <span class="category-percentage">28%</span>
                                </div>
                                <div class="category-item">
                                    <div class="category-info">
                                        <div class="category-color" style="background: #fd7e14"></div>
                                        <span class="category-name">Agriculture</span>
                                    </div>
                                    <span class="category-percentage">22%</span>
                                </div>
                                <div class="category-item">
                                    <div class="category-info">
                                        <div class="category-color" style="background: #6f42c1"></div>
                                        <span class="category-name">Cultural Heritage</span>
                                    </div>
                                    <span class="category-percentage">15%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity and Quick Actions -->
            <div class="row g-4">
                <div class="col-xl-6">
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fa-solid fa-clock me-2"></i>
                                Recent Activity
                            </h5>
                            <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="dashboard-card-body">
                            <div class="activity-list">
                                <div class="activity-item">
                                    <div class="activity-icon activity-icon-success">
                                        <i class="fa-solid fa-user-plus"></i>
                                    </div>
                                    <div class="activity-content">
                                        <p class="activity-text"><strong>New user registered:</strong> Maria Santos</p>
                                        <small class="activity-time">2 minutes ago</small>
                                    </div>
                                </div>
                                <div class="activity-item">
                                    <div class="activity-icon activity-icon-info">
                                        <i class="fa-solid fa-download"></i>
                                    </div>
                                    <div class="activity-content">
                                        <p class="activity-text"><strong>Book downloaded:</strong> "The Farmer's Guide"</p>
                                        <small class="activity-time">15 minutes ago</small>
                                    </div>
                                </div>
                                <div class="activity-item">
                                    <div class="activity-icon activity-icon-warning">
                                        <i class="fa-solid fa-book"></i>
                                    </div>
                                    <div class="activity-content">
                                        <p class="activity-text"><strong>New book added:</strong> "Cultural Stories"</p>
                                        <small class="activity-time">1 hour ago</small>
                                    </div>
                                </div>
                                <div class="activity-item">
                                    <div class="activity-icon activity-icon-primary">
                                        <i class="fa-solid fa-user-check"></i>
                                    </div>
                                    <div class="activity-content">
                                        <p class="activity-text"><strong>User verified:</strong> Juan Dela Cruz</p>
                                        <small class="activity-time">2 hours ago</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h5 class="dashboard-card-title">
                                <i class="fa-solid fa-bolt me-2"></i>
                                Quick Actions
                            </h5>
                        </div>
                        <div class="dashboard-card-body">
                            <div class="quick-actions">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="quick-action-item">
                                            <div class="quick-action-icon">
                                                <i class="fa-solid fa-plus"></i>
                                            </div>
                                            <span class="quick-action-text">Add Book</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="quick-action-item">
                                            <div class="quick-action-icon">
                                                <i class="fa-solid fa-users"></i>
                                            </div>
                                            <span class="quick-action-text">Manage Users</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="quick-action-item">
                                            <div class="quick-action-icon">
                                                <i class="fa-solid fa-list"></i>
                                            </div>
                                            <span class="quick-action-text">Categories</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="quick-action-item">
                                            <div class="quick-action-icon">
                                                <i class="fa-solid fa-chart-bar"></i>
                                            </div>
                                            <span class="quick-action-text">Reports</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--card-color), var(--card-color-light));
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
    background: linear-gradient(135deg, var(--card-color), var(--card-color-light));
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

/* Quick Actions */
.quick-actions {
    padding: 0.5rem 0;
}

.quick-action-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
    padding: 1.5rem;
    border-radius: 12px;
    background: #f8f9fa;
    transition: all 0.3s ease;
    cursor: pointer;
    text-decoration: none;
    color: inherit;
}

.quick-action-item:hover {
    background: #e9ecef;
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    color: inherit;
    text-decoration: none;
}

.quick-action-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: linear-gradient(135deg, #198754, #75b798);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}

.quick-action-text {
    font-weight: 500;
    color: #2c3e50;
    text-align: center;
    font-size: 0.9rem;
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
    box-shadow: 0 8px 32px rgba(40, 167, 69, 0.15), 0 1.5px 6px rgba(0,0,0,0.08);
    border-radius: 12px;
    font-size: 1.05rem;
    pointer-events: auto;
    animation: floatIn 0.5s cubic-bezier(.4,2,.6,1) both;
}
@keyframes floatIn {
    from { opacity: 0; transform: translateY(-30px) scale(0.98); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
@media (max-width: 768px) {
    .floating-flash-message {
        top: 12px;
        right: 12px;
        min-width: 200px;
        font-size: 0.95rem;
    }
}
</style>
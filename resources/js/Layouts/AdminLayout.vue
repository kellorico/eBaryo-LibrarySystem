<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from "vue";
import Toast from "../Components/Toast.vue";

const profileDropdownOpen = ref(false);
const notificationDropdownOpen = ref(false);
const notifications = ref([]);
const unreadCount = ref(0);
const loading = ref(false);
const showToast = ref(false);
const toastMessage = ref("");
const toastTitle = ref("");
const audioRef = ref(null);
let lastUnreadCount = 0;
const selectedNotification = ref(null);
const showNotificationModal = ref(false);
const sidebarOpen = ref(false);
const isMobile = ref(false);

// Fetch notifications from the backend
async function fetchNotifications() {
    try {
        loading.value = true;
        const response = await fetch("/notifications");
        const data = await response.json();
        notifications.value = data.notifications.data || [];
        unreadCount.value = data.unread_count || 0;
    } catch (error) {
        console.error("Error fetching notifications:", error);
    } finally {
        loading.value = false;
    }
}

// Fetch unread count
async function fetchUnreadCount() {
    try {
        const response = await fetch("/notifications/unread-count");
        const data = await response.json();
        unreadCount.value = data.count || 0;
    } catch (error) {
        console.error("Error fetching unread count:", error);
    }
}

// Mark notification as read
async function markAsRead(notificationId) {
    try {
        const response = await fetch(`/notifications/${notificationId}/read`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
        });
        const data = await response.json();
        if (data.success) {
            unreadCount.value = data.unread_count;
            // Update the notification in the list
            const notification = notifications.value.find(
                (n) => n.id === notificationId
            );
            if (notification) {
                notification.is_read = true;
                notification.read_at = new Date().toISOString();
            }
        }
    } catch (error) {
        console.error("Error marking notification as read:", error);
    }
}

// Mark all notifications as read
async function markAllAsRead() {
    try {
        const response = await fetch("/notifications/mark-all-read", {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
        });
        const data = await response.json();
        if (data.success) {
            unreadCount.value = 0;
            notifications.value.forEach((notification) => {
                notification.is_read = true;
                notification.read_at = new Date().toISOString();
            });
        }
    } catch (error) {
        console.error("Error marking all notifications as read:", error);
    }
}

// Clear all notifications
async function clearAllNotifications() {
    try {
        const response = await fetch("/notifications", {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
        });
        const data = await response.json();
        if (data.success) {
            notifications.value = [];
            unreadCount.value = 0;
            notificationDropdownOpen.value = false;
        }
    } catch (error) {
        console.error("Error clearing notifications:", error);
    }
}

// Delete single notification
async function deleteNotification(notificationId) {
    try {
        const response = await fetch(`/notifications/${notificationId}`, {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
        });
        const data = await response.json();
        if (data.success) {
            unreadCount.value = data.unread_count;
            notifications.value = notifications.value.filter(
                (n) => n.id !== notificationId
            );
        }
    } catch (error) {
        console.error("Error deleting notification:", error);
    }
}

function toggleProfileDropdown() {
    profileDropdownOpen.value = !profileDropdownOpen.value;
}

function toggleNotificationDropdown() {
    notificationDropdownOpen.value = !notificationDropdownOpen.value;
    if (notificationDropdownOpen.value) {
        fetchNotifications();
    }
}

function closeDropdowns(e) {
    if (!e.target.closest(".profile-dropdown-wrapper")) {
        profileDropdownOpen.value = false;
    }
    if (!e.target.closest(".notification-dropdown-wrapper")) {
        notificationDropdownOpen.value = false;
    }
}

// Auto-refresh unread count every 30 seconds
let unreadCountInterval;

onMounted(() => {
    document.addEventListener("click", closeDropdowns);
    fetchUnreadCount();

    // Set up auto-refresh for unread count
    unreadCountInterval = setInterval(fetchUnreadCount, 30000);
    handleResize();
    window.addEventListener("resize", handleResize);

    if (showNotificationModal.value) {
        document.body.classList.add("modal-open");
    }
});

onBeforeUnmount(() => {
    document.removeEventListener("click", closeDropdowns);
    if (unreadCountInterval) {
        clearInterval(unreadCountInterval);
    }
    window.removeEventListener("resize", handleResize);
    document.body.classList.remove("modal-open");
});

// Watch for new notifications
watch(unreadCount, (newVal, oldVal) => {
    if (newVal > oldVal && oldVal !== 0) {
        // Play sound
        if (audioRef.value) audioRef.value.play();
        // Show toast for the latest notification
        if (notifications.value.length > 0) {
            const latest = notifications.value[0];
            toastTitle.value = latest.title;
            toastMessage.value = latest.message;
            showToast.value = true;
        }
    }
    lastUnreadCount = newVal;
});

function openNotification(notification) {
    selectedNotification.value = notification;
    showNotificationModal.value = true;
    if (!notification.is_read) {
        markAsRead(notification.id);
    }
}

function handleResize() {
    isMobile.value = window.innerWidth < 768;
    if (!isMobile.value) sidebarOpen.value = false;
}

// Add/remove 'modal-open' class to body when modal is open
watch(showNotificationModal, (val) => {
    if (val) {
        document.body.classList.add("modal-open");
    } else {
        document.body.classList.remove("modal-open");
    }
});
</script>

<template>
    <div class="min-vh-100 d-flex">
        <!-- Sidebar Toggle Button for Mobile -->
        <button
            class="sidebar-toggle d-md-none btn btn-success position-fixed"
            @click="sidebarOpen = !sidebarOpen"
            style="top: 16px; left: 16px; z-index: 1201"
        >
            <i class="fa fa-bars"></i>
        </button>
        <!-- Enhanced Sidebar -->
        <aside
            :class="[
                'sidebar',
                { show: sidebarOpen, 'sidebar-mobile': isMobile },
            ]"
            @click.self="sidebarOpen = false"
        >
            <div class="sidebar-header p-4 border-bottom border-light-subtle">
                <div class="d-flex align-items-center justify-content-center">
                    <div class="logo-container me-3">
                        <i class="fa-solid fa-book-open text-white fs-3"></i>
                    </div>
                    <div>
                        <h4 class="text-white mb-0 fw-bold">eBaryo Library</h4>
                        <small class="text-light opacity-75">Admin Panel</small>
                    </div>
                </div>
            </div>
            <div class="sidebar-content p-3">
                <nav class="nav flex-column gap-2">
                    <!-- Dashboard -->
                    <Link
                        :href="route('admin.dashboard')"
                        class="nav-item d-flex align-items-center text-white p-3 rounded-3"
                        :class="{
                            'nav-item-active': $page.url === '/dashboard',
                        }"
                    >
                        <div class="nav-icon me-3">
                            <i class="fa-solid fa-house"></i>
                        </div>
                        <span class="nav-text fw-medium">Dashboard</span>
                    </Link>

                    <!-- Books Section -->
                    <div class="nav-section">
                        <button
                            class="nav-item d-flex justify-content-between align-items-center text-white p-3 rounded-3 w-100"
                            data-bs-toggle="collapse"
                            data-bs-target="#booksMenu"
                            aria-expanded="false"
                            aria-controls="booksMenu"
                            :class="{
                                'nav-item-active':
                                    $page.url.startsWith('/books/'),
                            }"
                        >
                            <div class="d-flex align-items-center">
                                <div class="nav-icon me-3">
                                    <i class="fa-solid fa-book"></i>
                                </div>
                                <span class="nav-text fw-medium">Books</span>
                            </div>
                            <i
                                class="fa-solid fa-chevron-down nav-arrow"
                                :class="{
                                    rotated: $page.url.startsWith('/books/'),
                                }"
                            ></i>
                        </button>

                        <div
                            class="collapse"
                            :class="{ show: $page.url.startsWith('/books/') }"
                            id="booksMenu"
                        >
                            <div class="submenu">
                                <Link
                                    :href="route('admin.storybooks')"
                                    class="submenu-item d-flex align-items-center text-white p-2 rounded-2"
                                    :class="{
                                        'submenu-item-active':
                                            $page.url === '/books/storybooks',
                                    }"
                                >
                                    <div class="submenu-icon me-2">
                                        <i class="fa-solid fa-book-open"></i>
                                    </div>
                                    <span class="submenu-text"
                                        >Story Books</span
                                    >
                                </Link>
                                <Link
                                    :href="route('admin.educationalbooks')"
                                    class="submenu-item d-flex align-items-center text-white p-2 rounded-2"
                                    :class="{
                                        'submenu-item-active':
                                            $page.url === '/books/educational',
                                    }"
                                >
                                    <div class="submenu-icon me-2">
                                        <i
                                            class="fa-solid fa-graduation-cap"
                                        ></i>
                                    </div>
                                    <span class="submenu-text"
                                        >Educational</span
                                    >
                                </Link>
                                <Link
                                    :href="route('admin.agricultureandlivelihood')"
                                    class="submenu-item d-flex align-items-center text-white p-2 rounded-2"
                                    :class="{
                                        'submenu-item-active':
                                            $page.url ===
                                            '/books/agricultureandlivelihood',
                                    }"
                                >
                                    <div class="submenu-icon me-2">
                                        <i class="fa-solid fa-seedling"></i>
                                    </div>
                                    <span class="submenu-text"
                                        >Agriculture & Livelihood</span
                                    >
                                </Link>
                                <Link
                                    :href="route('admin.culturalheritage')"
                                    class="submenu-item d-flex align-items-center text-white p-2 rounded-2"
                                    :class="{
                                        'submenu-item-active':
                                            $page.url ===
                                            '/books/culturalheritage',
                                    }"
                                >
                                    <div class="submenu-icon me-2">
                                        <i class="fa-solid fa-landmark"></i>
                                    </div>
                                    <span class="submenu-text"
                                        >Cultural Heritage</span
                                    >
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Users Section -->
                    <div class="nav-section">
                        <button
                            class="nav-item d-flex justify-content-between align-items-center text-white p-3 rounded-3 w-100"
                            data-bs-toggle="collapse"
                            data-bs-target="#usersMenu"
                            aria-expanded="false"
                            aria-controls="usersMenu"
                            :class="{
                                'nav-item-active':
                                    $page.url.startsWith('/users/'),
                            }"
                        >
                            <div class="d-flex align-items-center">
                                <div class="nav-icon me-3">
                                    <i class="fa-solid fa-users"></i>
                                </div>
                                <span class="nav-text fw-medium">Users</span>
                            </div>
                            <i
                                class="fa-solid fa-chevron-down nav-arrow"
                                :class="{
                                    rotated: $page.url.startsWith('/users/'),
                                }"
                            ></i>
                        </button>

                        <div
                            class="collapse"
                            :class="{ show: $page.url.startsWith('/users/') }"
                            id="usersMenu"
                        >
                            <div class="submenu">
                                <Link
                                    :href="route('admin.allusers')"
                                    class="submenu-item d-flex align-items-center text-white p-2 rounded-2"
                                    :class="{
                                        'submenu-item-active':
                                            $page.url === '/users/allusers',
                                    }"
                                >
                                    <div class="submenu-icon me-2">
                                        <i class="fa-solid fa-users"></i>
                                    </div>
                                    <span class="submenu-text">All Users</span>
                                </Link>
                                <Link
                                    :href="route('admin.verifiedusers')"
                                    class="submenu-item d-flex align-items-center text-white p-2 rounded-2"
                                    :class="{
                                        'submenu-item-active':
                                            $page.url ===
                                            '/users/verifiedusers',
                                    }"
                                >
                                    <div class="submenu-icon me-2">
                                        <i class="fa-solid fa-user-check"></i>
                                    </div>
                                    <span class="submenu-text"
                                        >Verified Users</span
                                    >
                                </Link>
                                <Link
                                    :href="route('admin.unverifiedusers')"
                                    class="submenu-item d-flex align-items-center text-white p-2 rounded-2"
                                    :class="{
                                        'submenu-item-active':
                                            $page.url ===
                                            '/users/unverifiedusers',
                                    }"
                                >
                                    <div class="submenu-icon me-2">
                                        <i class="fa-solid fa-user-clock"></i>
                                    </div>
                                    <span class="submenu-text"
                                        >Unverified Users</span
                                    >
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Digital Archive -->
                    <Link
                        :href="route('admin.archive')"
                        class="nav-item d-flex align-items-center text-white p-3 rounded-3 mt-3"
                        :class="{ 'nav-item-active': $page.url === '/archive' }"
                    >
                        <div class="nav-icon me-3">
                            <i class="fa-solid fa-archive"></i>
                        </div>
                        <span class="nav-text fw-medium">Digital Archive</span>
                    </Link>
                    <!-- Analytics Link -->
                    <Link
                        :href="route('admin.analytics')"
                        class="nav-item d-flex align-items-center text-white p-3 rounded-3 mt-3"
                        :class="{
                            'nav-item-active': $page.url === '/analytics',
                        }"
                    >
                        <div class="nav-icon me-3">
                            <i class="fa-solid fa-chart-bar"></i>
                        </div>
                        <span class="nav-text fw-medium">Analytics</span>
                    </Link>
                    <!-- Announcements Link -->
                    <Link
                        :href="route('admin.announcements')"
                        class="nav-item d-flex align-items-center text-white p-3 rounded-3"
                        :class="{
                            'nav-item-active': $page.url === '/announcements',
                        }"
                    >
                        <div class="nav-icon me-3">
                            <i class="fa-solid fa-bullhorn"></i>
                        </div>
                        <span class="nav-text fw-medium">Announcements</span>
                    </Link>
                    <!-- Suggestions Link -->
                    <Link
                        :href="route('admin.suggestions')"
                        class="nav-item d-flex align-items-center text-white p-3 rounded-3"
                        :class="{
                            'nav-item-active': $page.url === '/suggestions',
                        }"
                    >
                        <div class="nav-icon me-3">
                            <i class="fa-solid fa-lightbulb"></i>
                        </div>
                        <span class="nav-text fw-medium">Suggestions</span>
                    </Link>
                    <!-- Review Moderation Link -->
                    <Link
                        :href="route('admin.reviews')"
                        class="nav-item d-flex align-items-center text-white p-3 rounded-3"
                        :class="{ 'nav-item-active': $page.url === '/reviews' }"
                    >
                        <div class="nav-icon me-3">
                            <i class="fa-solid fa-gavel"></i>
                        </div>
                        <span class="nav-text fw-medium"
                            >Review Moderation</span
                        >
                    </Link>
                </nav>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-grow-1 d-flex flex-column">
            <!-- Enhanced Header -->
            <header class="fixed-header">
                <div class="container-fluid px-4">
                    <div
                        class="d-flex justify-content-between align-items-center flex-wrap gap-3"
                    >
                        <!-- Sidebar Toggle for Mobile (inside header for better UX) -->
                        <button
                            class="sidebar-toggle d-md-none btn btn-outline-success me-2"
                            @click="sidebarOpen = !sidebarOpen"
                            style="z-index: 1202"
                        >
                            <i class="fa fa-bars"></i>
                        </button>
                        <!-- Page Title Slot or Fallback -->

                        <div class="d-flex align-items-center gap-3 ms-auto">
                            <!-- Notification Bell -->
                            <div
                                class="notification-dropdown-wrapper"
                                style="position: relative"
                            >
                                <button
                                    class="btn btn-outline-secondary position-relative d-flex align-items-center"
                                    type="button"
                                    @click="toggleNotificationDropdown"
                                >
                                    <i class="fa fa-bell"></i>
                                    <span
                                        v-if="unreadCount > 0"
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger animate__animated animate__bounceIn"
                                        style="font-size: 0.7rem"
                                        >{{ unreadCount }}</span
                                    >
                                </button>
                                <transition name="fade">
                                    <ul
                                        v-show="notificationDropdownOpen"
                                        class="dropdown-menu dropdown-menu-end shadow border-0 show notification-dropdown-list"
                                        style="
                                            display: block;
                                            position: fixed;
                                            z-index: 9999;
                                            right: 32px;
                                            top: 72px;
                                            min-width: 340px;
                                            max-width: 400px;
                                            max-height: 420px;
                                            overflow-y: auto;
                                        "
                                    >
                                        <li
                                            class="dropdown-header d-flex justify-content-between align-items-center fw-bold text-success py-2 px-3"
                                        >
                                            <span>Notifications</span>
                                            <div class="d-flex gap-1">
                                                <button
                                                    v-if="
                                                        notifications.length > 0
                                                    "
                                                    class="btn btn-sm btn-outline-success"
                                                    @click="markAllAsRead"
                                                    title="Mark all as read"
                                                >
                                                    <i
                                                        class="fa-solid fa-check-double"
                                                    ></i>
                                                </button>
                                                <button
                                                    v-if="
                                                        notifications.length > 0
                                                    "
                                                    class="btn btn-sm btn-outline-danger"
                                                    @click="
                                                        clearAllNotifications
                                                    "
                                                    title="Clear all"
                                                >
                                                    <i
                                                        class="fa-solid fa-trash"
                                                    ></i>
                                                </button>
                                            </div>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider my-1" />
                                        </li>
                                        <li
                                            v-if="loading"
                                            class="dropdown-item text-center py-4"
                                        >
                                            <div
                                                class="spinner-border spinner-border-sm text-success"
                                                role="status"
                                            >
                                                <span class="visually-hidden"
                                                    >Loading...</span
                                                >
                                            </div>
                                            <span class="ms-2"
                                                >Loading notifications...</span
                                            >
                                        </li>
                                        <li
                                            v-else-if="
                                                notifications.length === 0
                                            "
                                            class="dropdown-item text-muted text-center py-5"
                                        >
                                            <div class="mb-2">
                                                <i
                                                    class="fa-solid fa-bell-slash fa-2x text-secondary"
                                                ></i>
                                            </div>
                                            <div>No notifications</div>
                                        </li>
                                        <li
                                            v-else
                                            v-for="notification in notifications"
                                            :key="notification.id"
                                            class="dropdown-item p-0"
                                        >
                                            <div
                                                class="notification-item p-3 d-flex gap-3 align-items-start"
                                                :class="{
                                                    unread: !notification.is_read,
                                                    'animate__animated animate__fadeInDown':
                                                        !notification.is_read,
                                                }"
                                                style="cursor: pointer"
                                                @click="
                                                    openNotification(
                                                        notification
                                                    )
                                                "
                                            >
                                                <div
                                                    class="notification-icon flex-shrink-0 d-flex align-items-center justify-content-center"
                                                    :class="
                                                        notification.color_class ||
                                                        'text-muted'
                                                    "
                                                >
                                                    <i
                                                        :class="[
                                                            'fa-solid',
                                                            notification.icon ||
                                                                'fa-bell',
                                                        ]"
                                                    ></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div
                                                        class="d-flex align-items-center gap-2 mb-1"
                                                    >
                                                        <strong
                                                            class="notification-title"
                                                            >{{
                                                                notification.title
                                                            }}</strong
                                                        >
                                                        <span
                                                            v-if="
                                                                !notification.is_read
                                                            "
                                                            class="badge bg-primary animate__animated animate__pulse animate__infinite"
                                                            >New</span
                                                        >
                                                    </div>
                                                    <p
                                                        class="notification-message mb-1"
                                                    >
                                                        {{
                                                            notification.message
                                                        }}
                                                    </p>
                                                    <small class="text-muted"
                                                        ><i
                                                            class="fa-regular fa-clock me-1"
                                                        ></i
                                                        >{{
                                                            notification.time_ago
                                                        }}</small
                                                    >
                                                </div>
                                                <div
                                                    class="notification-actions d-flex flex-column gap-1 ms-2 align-items-end"
                                                >
                                                    <button
                                                        v-if="
                                                            !notification.is_read
                                                        "
                                                        class="btn btn-sm btn-outline-primary mb-1"
                                                        @click="
                                                            markAsRead(
                                                                notification.id
                                                            )
                                                        "
                                                        title="Mark as read"
                                                    >
                                                        <i
                                                            class="fa-solid fa-check"
                                                        ></i>
                                                    </button>
                                                    <button
                                                        class="btn btn-sm btn-outline-danger"
                                                        @click="
                                                            deleteNotification(
                                                                notification.id
                                                            )
                                                        "
                                                        title="Delete"
                                                    >
                                                        <i
                                                            class="fa-solid fa-times"
                                                        ></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </transition>
                            </div>
                            <!-- Profile Dropdown -->
                            <div
                                class="profile-dropdown-wrapper"
                                style="position: relative"
                            >
                                <button
                                    class="btn btn-outline-success d-flex align-items-center gap-2"
                                    type="button"
                                    @click="toggleProfileDropdown"
                                >
                                    <div class="admin-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <span>Admin</span>
                                    <i class="fa fa-caret-down"></i>
                                </button>
                                <ul
                                    v-show="profileDropdownOpen"
                                    class="dropdown-menu dropdown-menu-end shadow border-0 show"
                                    style="
                                        display: block;
                                        position: absolute;
                                        right: 0;
                                        top: 110%;
                                        min-width: 180px;
                                    "
                                >
                                    <li>
                                        <Link
                                            class="dropdown-item d-flex align-items-center gap-2"
                                            :href="route('admin.profile')"
                                        >
                                            <i class="fa-solid fa-user"></i>
                                            Profile
                                        </Link>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider" />
                                    </li>
                                    <li>
                                        <Link
                                            class="dropdown-item d-flex align-items-center gap-2 text-danger"
                                            :href="route('logout')"
                                            method="post"
                                        >
                                            <i
                                                class="fa-solid fa-sign-out-alt"
                                            ></i>
                                            Logout
                                        </Link>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-grow-1 bg-light py-4 px-4">
                <slot />
            </main>
        </div>
        <Toast v-model="showToast" :duration="3500">
            <template #default>
                <div class="d-flex align-items-center gap-2">
                    <i class="fa fa-bell text-success"></i>
                    <div>
                        <div class="fw-bold">{{ toastTitle }}</div>
                        <div>{{ toastMessage }}</div>
                    </div>
                </div>
            </template>
        </Toast>
        <!-- Notification Modal -->
        <div
            v-if="showNotificationModal"
            class="modal-backdrop"
            @click.self="showNotificationModal = false"
            style="
                z-index: 2000;
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(0, 0, 0, 0.3);
                display: flex;
                align-items: center;
                justify-content: center;
            "
        >
            <div
                class="modal-dialog"
                style="
                    background: #fff;
                    border-radius: 8px;
                    max-width: 400px;
                    width: 100%;
                    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.18);
                "
            >
                <div class="modal-content p-3">
                    <div
                        class="modal-header d-flex align-items-center justify-content-between"
                    >
                        <h5 class="modal-title d-flex align-items-center gap-2">
                            <i
                                :class="[
                                    'fa-solid',
                                    selectedNotification?.icon || 'fa-bell',
                                    selectedNotification?.color_class,
                                ]"
                                style="font-size: 1.3em"
                            ></i>
                            {{ selectedNotification?.title }}
                        </h5>
                        <button
                            type="button"
                            class="btn-close"
                            @click="showNotificationModal = false"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ selectedNotification?.message }}</p>
                        <small class="text-muted"
                            ><i class="fa-regular fa-clock me-1"></i
                            >{{ selectedNotification?.time_ago }}</small
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@import "https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css";

/* Sidebar Styles */
.sidebar {
    background: linear-gradient(135deg, #198754 0%, #146c43 100%);
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: 280px;
    overflow-y: auto;
    transition: transform 0.3s ease, left 0.3s;
    z-index: 1200;
}

.sidebar-mobile {
    position: fixed !important;
    left: 0;
    top: 0;
    height: 100vh;
    width: 80vw;
    max-width: 320px;
    transform: translateX(-100%);
    box-shadow: 2px 0 16px rgba(0, 0, 0, 0.18);
    z-index: 1202;
}

.sidebar.show.sidebar-mobile {
    transform: translateX(0);
}

.sidebar-toggle {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    font-size: 1.3rem;
    box-shadow: 0 2px 8px rgba(30, 41, 59, 0.1);
}

@media (max-width: 991.98px) {
    .sidebar {
        width: 220px;
    }
}

@media (max-width: 767.98px) {
    .sidebar {
        width: 80vw;
        max-width: 320px;
        left: 0;
        position: fixed;
        transform: translateX(-100%);
        z-index: 1202;
    }

    .sidebar.show {
        transform: translateX(0);
    }
}

.flex-grow-1.d-flex.flex-column {
    margin-left: 280px;
    transition: margin-left 0.3s;
}

@media (max-width: 991.98px) {
    .flex-grow-1.d-flex.flex-column {
        margin-left: 220px;
    }
}

@media (max-width: 767.98px) {
    .flex-grow-1.d-flex.flex-column {
        margin-left: 0;
    }
}

.fixed-header {
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(8px);
    border-bottom: 1px solid #e9ecef;
    box-shadow: 0 2px 8px rgba(30, 41, 59, 0.06);
    display: flex;
    align-items: center;
    padding: 0 2rem;
    height: 64px;
    transition: background 0.2s, box-shadow 0.2s;
    z-index: 1000 !important;
}
@media (max-width: 991.98px) {
    .fixed-header {
        padding: 0 1rem;
    }
}
@media (max-width: 767.98px) {
    .fixed-header {
        height: 56px;
        padding: 0 0.5rem;
    }
}
.header-title h3 {
    font-size: 1.3rem;
    color: #198754;
    font-weight: 700;
    margin-bottom: 0;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
}
.header-title i {
    font-size: 1.2em;
    margin-right: 0.5rem;
}

.main-content-with-header {
    margin-top: 0;
}

.main-content-inner {
    width: 100%;
    margin: 0;
    padding: 2rem 2rem 0 2rem;
    box-sizing: border-box;
}

@media (max-width: 767.98px) {
    .main-content-with-header,
    .main-content-inner {
        padding: 1rem 0.5rem 0 0.5rem;
    }
}

.sidebar-header {
    background: rgba(255, 255, 255, 0.05);
}

.logo-container {
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
}

.sidebar-content {
    padding-top: 1rem;
}

/* Navigation Items */
.nav-item {
    background: none;
    border: none;
    transition: all 0.3s ease;
    text-decoration: none;
    position: relative;
    overflow: hidden;
}

.nav-item:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateX(5px);
}

.nav-item-active {
    background: rgba(255, 255, 255, 0.15) !important;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transform: translateX(5px);
}

.nav-icon {
    width: 20px;
    text-align: center;
    font-size: 16px;
}

.nav-text {
    font-size: 15px;
}

.nav-arrow {
    transition: transform 0.3s ease;
    font-size: 12px;
}

.nav-arrow.rotated {
    transform: rotate(180deg);
}

/* Submenu Styles */
.submenu {
    margin-left: 1rem;
    margin-top: 0.5rem;
    padding-left: 1rem;
    border-left: 2px solid rgba(255, 255, 255, 0.2);
}

.submenu-item {
    background: none;
    border: none;
    transition: all 0.3s ease;
    text-decoration: none;
    margin-bottom: 0.25rem;
}

.submenu-item:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateX(3px);
}

.submenu-item-active {
    background: rgba(255, 255, 255, 0.15) !important;
    transform: translateX(3px);
}

.submenu-icon {
    width: 16px;
    text-align: center;
    font-size: 14px;
}

.submenu-text {
    font-size: 14px;
    font-weight: 500;
}

/* Header Styles */
.admin-avatar {
    width: 32px;
    height: 32px;
    background: #198754;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
}

.dropdown-menu {
    border-radius: 12px;
    padding: 0.5rem;
}

.dropdown-item {
    border-radius: 8px;
    padding: 0.75rem 1rem;
    transition: all 0.2s ease;
}

.dropdown-item:hover {
    background: #f8f9fa;
    transform: translateX(3px);
}

/* Notification Styles */
.notification-item {
    border-bottom: 1px solid #f0f0f0;
    transition: background 0.2s, box-shadow 0.2s;
    border-radius: 10px;
    margin-bottom: 2px;
    background: #fff;
    position: relative;
    cursor: pointer;
    overflow-x: hidden;
    word-break: break-word;
    max-width: 100%;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-item.unread {
    background: #e8f5e8;
    border-left: 4px solid #198754;
    box-shadow: 0 2px 8px rgba(25, 135, 84, 0.07);
}

.notification-item:hover {
    background: #f8f9fa;
    box-shadow: 0 2px 8px rgba(25, 135, 84, 0.1);
}

.notification-icon {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #f3f7f3;
    font-size: 1.3rem;
    align-items: center;
    justify-content: center;
    display: flex;
}

.text-primary {
    color: #0d6efd !important;
}

.text-success {
    color: #198754 !important;
}

.text-info {
    color: #0dcaf0 !important;
}

.text-warning {
    color: #ffc107 !important;
}

.text-secondary {
    color: #6c757d !important;
}

.text-muted {
    color: #adb5bd !important;
}

.notification-title {
    font-size: 1rem;
    color: #222;
    font-weight: 600;
    word-break: break-word;
    white-space: normal;
    max-width: 100%;
}

.notification-message {
    font-size: 0.95rem;
    color: #555;
    line-height: 1.4;
    margin-bottom: 0.2rem;
    word-break: break-word;
    white-space: normal;
    max-width: 100%;
}

.notification-actions {
    opacity: 0;
    transition: opacity 0.2s;
}

.notification-item:hover .notification-actions {
    opacity: 1;
}

/* Main Content */
main {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

/* Responsive Design */
@media (max-width: 768px) {
    .sidebar {
        width: 100% !important;
        position: fixed;
        z-index: 1000;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }

    .sidebar.show {
        transform: translateX(0);
    }
}

/* Scrollbar Styling */
.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
}

.sidebar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 3px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}

.notification-dropdown-wrapper .btn {
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.notification-dropdown-wrapper .badge {
    pointer-events: none;
}

.modal-backdrop {
    z-index: 2000 !important;
}
.modal-open .sidebar {
    pointer-events: none;
    filter: blur(2px);
    transition: filter 0.2s;
}

.notification-dropdown-list {
    position: fixed !important;
    z-index: 9999 !important;
    right: 32px;
    top: 72px;
}
</style>

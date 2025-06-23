<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-vh-100 d-flex">
        <!-- Enhanced Sidebar -->
        <aside class="sidebar bg-gradient-primary border-end" style="width: 280px;">
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
                        href="/dashboard" 
                        class="nav-item d-flex align-items-center text-white p-3 rounded-3"
                        :class="{ 'nav-item-active': $page.url === '/dashboard' }"
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
                            :class="{ 'nav-item-active': $page.url.startsWith('/books/') }"
                        >
                            <div class="d-flex align-items-center">
                                <div class="nav-icon me-3">
                                    <i class="fa-solid fa-book"></i>
                                </div>
                                <span class="nav-text fw-medium">Books</span>
                            </div>
                            <i class="fa-solid fa-chevron-down nav-arrow" :class="{ 'rotated': $page.url.startsWith('/books/') }"></i>
                        </button>

                        <div 
                            class="collapse"
                            :class="{ show: $page.url.startsWith('/books/') }"
                            id="booksMenu"
                        >
                            <div class="submenu">
                                <Link 
                                    href="/books/storybooks"
                                    class="submenu-item d-flex align-items-center text-white p-2 rounded-2"
                                    :class="{ 'submenu-item-active': $page.url === '/books/storybooks' }"
                                >
                                    <div class="submenu-icon me-2">
                                        <i class="fa-solid fa-book-open"></i>
                                    </div>
                                    <span class="submenu-text">Story Books</span>
                                </Link>
                                <Link 
                                    href="/books/educational"
                                    class="submenu-item d-flex align-items-center text-white p-2 rounded-2"
                                    :class="{ 'submenu-item-active': $page.url === '/books/educational' }"
                                >
                                    <div class="submenu-icon me-2">
                                        <i class="fa-solid fa-graduation-cap"></i>
                                    </div>
                                    <span class="submenu-text">Educational</span>
                                </Link>
                                <Link 
                                    href="/books/agricultureandlivelihood"
                                    class="submenu-item d-flex align-items-center text-white p-2 rounded-2"
                                    :class="{ 'submenu-item-active': $page.url === '/books/agricultureandlivelihood' }"
                                >
                                    <div class="submenu-icon me-2">
                                        <i class="fa-solid fa-seedling"></i>
                                    </div>
                                    <span class="submenu-text">Agriculture & Livelihood</span>
                                </Link>
                                <Link 
                                    href="/books/culturalheritage"
                                    class="submenu-item d-flex align-items-center text-white p-2 rounded-2"
                                    :class="{ 'submenu-item-active': $page.url === '/books/culturalheritage' }"
                                >
                                    <div class="submenu-icon me-2">
                                        <i class="fa-solid fa-landmark"></i>
                                    </div>
                                    <span class="submenu-text">Cultural Heritage</span>
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
                            :class="{ 'nav-item-active': $page.url.startsWith('/users/') }"
                        >
                            <div class="d-flex align-items-center">
                                <div class="nav-icon me-3">
                                    <i class="fa-solid fa-users"></i>
                                </div>
                                <span class="nav-text fw-medium">Users</span>
                            </div>
                            <i class="fa-solid fa-chevron-down nav-arrow" :class="{ 'rotated': $page.url.startsWith('/users/') }"></i>
                        </button>
                        
                        <div class="collapse"
                            :class="{ show: $page.url.startsWith('/users/') }"
                            id="usersMenu">
                            <div class="submenu">
                                <Link 
                                    href="/users/allusers"
                                    class="submenu-item d-flex align-items-center text-white p-2 rounded-2"
                                    :class="{ 'submenu-item-active': $page.url === '/users/allusers' }"
                                >
                                    <div class="submenu-icon me-2">
                                        <i class="fa-solid fa-users"></i>
                                    </div>
                                    <span class="submenu-text">All Users</span>
                                </Link>
                                <Link 
                                    href="/users/verifiedusers"
                                    class="submenu-item d-flex align-items-center text-white p-2 rounded-2"
                                    :class="{ 'submenu-item-active': $page.url === '/users/verifiedusers' }"
                                >
                                    <div class="submenu-icon me-2">
                                        <i class="fa-solid fa-user-check"></i>
                                    </div>
                                    <span class="submenu-text">Verified Users</span>
                                </Link>
                                <Link 
                                    href="/users/unverifiedusers"
                                    class="submenu-item d-flex align-items-center text-white p-2 rounded-2"
                                    :class="{ 'submenu-item-active': $page.url === '/users/unverifiedusers' }"
                                >
                                    <div class="submenu-icon me-2">
                                        <i class="fa-solid fa-user-clock"></i>
                                    </div>
                                    <span class="submenu-text">Unverified Users</span>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Categories -->
                    <Link 
                        href="/categories" 
                        class="nav-item d-flex align-items-center text-white p-3 rounded-3"
                        :class="{ 'nav-item-active': $page.url === '/categories' }"
                    >
                        <div class="nav-icon me-3">
                            <i class="fa-solid fa-list"></i>
                        </div>
                        <span class="nav-text fw-medium">Categories</span>
                    </Link>
                </nav> 
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-grow-1 d-flex flex-column">
            <!-- Enhanced Header -->
            <header class="bg-white shadow-sm py-3 border-bottom">
                <div class="container-fluid px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3 ms-auto">
                            <div class="dropdown">
                                <button class="btn btn-outline-success dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                                    <div class="admin-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <span>Admin</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                    <li><Link class="dropdown-item d-flex align-items-center gap-2" href="/profile">
                                        <i class="fa-solid fa-user"></i> Profile
                                    </Link></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><Link class="dropdown-item d-flex align-items-center gap-2 text-danger" :href="route('logout')" method="post">
                                        <i class="fa-solid fa-sign-out-alt"></i> Logout
                                    </Link></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-grow-1 p-4 bg-light">
                <div class="container-fluid">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

<style scoped>
/* Sidebar Styles */
.sidebar {
    background: linear-gradient(135deg, #198754 0%, #146c43 100%);
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 0;
    height: 100vh;
    overflow-y: auto;
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
</style>
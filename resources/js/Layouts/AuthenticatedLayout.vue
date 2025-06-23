<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-vh-100 d-flex">
        <!-- Sidebar -->
        <aside class="bg-success border-end" style="width: 250px;">
            <div class="p-3">
                <h4 class="text-center mb-4 text-white"><i class="fa-solid fa-book"></i> eBaryo Library</h4>
                <nav class="nav flex-column">
                    <Link 
                        href="/dashboard" 
                        class="nav-link text-white" 
                        :class="{ 'active': $page.url === '/dashboard' }"
                    >
                        <i class="fa-solid fa-house"></i> Home
                    </Link>
                    <Link 
                        href="/books" 
                        class="nav-link text-white" 
                        :class="{ 'active': $page.url === '/books' }"
                    >
                        <i class="bi bi-book me-2"></i>Books
                    </Link>
                    <Link 
                        href="/authors" 
                        class="nav-link text-white" 
                        :class="{ 'active': $page.url === '/authors' }"
                    >
                        <i class="bi bi-person me-2"></i>Authors
                    </Link>
                    <Link 
                        href="/categories" 
                        class="nav-link text-white" 
                        :class="{ 'active': $page.url === '/categories' }"
                    >
                        <i class="bi bi-tags me-2"></i>Categories
                    </Link>
                </nav>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-grow-1 d-flex flex-column">
            <!-- Header -->
            <header class="bg-white shadow-sm py-3">
                <div class="container-fluid">
                    <div class="d-flex justify-content-end align-items-center">
                        <!-- User dropdown or other header content can go here -->
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                User Menu
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><Link class="dropdown-item" href="/profile">Profile</Link></li>
                                <li><Link class="dropdown-item" :href="route('logout')" method="post">Logout</Link></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-grow-1 p-4 bg-body-tertiary">
                <div class="container-fluid">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

<style scoped>
.nav-link {
    border-radius: 4px;
    margin-bottom: 4px;
    transition: all 0.2s;
}

.nav-link:hover {
    background-color: rgba(0, 0, 0, 0.05);
}

.nav-link.active {
    background-color: #0d6efd;
    color: white !important;
}

aside {
    position: sticky;
    top: 0;
    height: 100vh;
}
</style>
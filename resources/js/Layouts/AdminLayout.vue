<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-vh-100 d-flex">
        <!-- Sidebar -->
        <aside class="bg-success border-end" style="width: 250px;">
            <div class="p-3">
                <h4 class="text-center mb-4 text-white">
                    <i class="fa-solid fa-book"></i> eBaryo Library Admin
                </h4>
                <nav class="nav flex-column gap-1">

                    <!-- Dashboard -->
                    <Link 
                        href="/dashboard" 
                        class="nav-link d-flex align-items-center text-white ps-2"
                        :class="{ 'active': $page.url === '/dashboard' }"
                    >
                        <i class="fa-solid fa-house me-2"></i> Dashboard
                    </Link>

                    <!-- Books Dropdown -->
                    <button
                        class="nav-link d-flex justify-content-between align-items-center text-white ps-2"
                        data-bs-toggle="collapse"
                        data-bs-target="#booksMenu"
                        aria-expanded="false"
                        aria-controls="booksMenu"
                        style="background: none; border: none;"
                    >
                        <span class="d-flex align-items-center">
                            <i class="fa-solid fa-book me-2"></i> Books
                        </span>
                        <i class="fa-solid fa-chevron-down small"></i>
                    </button>

                    <div 
                        class="collapse"
                        :class="{ show: $page.url.startsWith('/books/') }"
                        id="booksMenu"
                    >
                        <Link 
                            href="/books/storybooks"
                            class="nav-link text-white small ps-4"
                            :class="{ 'active': $page.url === '/books/storybooks' }"
                        >
                            • Story Books
                        </Link>
                        <Link 
                            href="/books/educational"
                            class="nav-link text-white small ps-4"
                            :class="{ 'active': $page.url === '/books/educational' }"
                        >
                            • Educational
                        </Link>
                        <Link 
                            href="/books/agricultureandlivelihood"
                            class="nav-link text-white small ps-4"
                            :class="{ 'active': $page.url === '/books/agricultureandlivelihood' }"
                        >
                            • Agriculture & Livelihood
                        </Link>
                        <Link 
                            href="/books/culturalheritage"
                            class="nav-link text-white small ps-4"
                            :class="{ 'active': $page.url === '/books/culturalheritage' }"
                        >
                            • Cultural Heritage
                        </Link>
                    </div>

                    <!-- Authors -->
                    <Link 
                        href="/users" 
                        class="nav-link d-flex align-items-center text-white ps-2"
                        :class="{ 'active': $page.url === '/users' }"
                    >
                        <i class="fa-solid fa-at me-2"></i> Users
                    </Link>

                    <!-- Categories -->
                    <Link 
                        href="/categories" 
                        class="nav-link d-flex align-items-center text-white ps-2"
                        :class="{ 'active': $page.url === '/categories' }"
                    >
                        <i class="fa-solid fa-list me-2"></i> Categories
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
    margin-bottom: 2px;
    transition: all 0.2s ease;
    font-size: 15px;
}

.nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.nav-link.active {
    background-color: #138d75;
    color: white !important;
}

aside {
    position: sticky;
    top: 0;
    height: 100vh;
}
</style>
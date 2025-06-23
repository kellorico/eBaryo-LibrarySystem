<script setup>
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <div class="min-vh-100 d-flex flex-column">
        <!-- Header -->
        <header class="bg-success shadow-sm fixed-top py-3">
            <div class="container">
                <nav
                    class="navbar navbar-expand-lg navbar-dark justify-content-between"
                >
                    <Link
                        href="/"
                        class="navbar-brand d-flex align-items-center gap-2"
                    >
                        <i class="fa-solid fa-book fa-lg"></i>
                        <span class="fw-bold">eBaryo Library</span>
                    </Link>
                    <div class="d-flex gap-2">
                        <Link
                            href="/login"
                            class="btn btn-outline-light rounded-pill px-4"
                            :class="{ active: $page.url === '/login' }"
                        >
                            Login
                        </Link>
                        <Link
                            href="/register"
                            class="btn btn-light text-success rounded-pill px-4"
                            :class="{ active: $page.url === '/register' }"
                        >
                            Register
                        </Link>
                    </div>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main
            class="flex-grow-1 py-5"
            style="padding-top: 100px; padding-bottom: 60px"
        >
            <div class="container">
                <slot />
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-light py-4 text-center text-muted small border-top">
            <div class="container">
                &copy; {{ new Date().getFullYear() }} eBaryo Library. All rights
                reserved.
            </div>
        </footer>
    </div>
</template>

<style scoped>
.active {
    font-weight: 600;
    box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.4);
}
</style>

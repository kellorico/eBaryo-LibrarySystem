<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-vh-100 d-flex flex-column" style="min-height: 100vh;">
        <!-- Header/Navigation -->
        <header class="bg-success py-3 shadow-sm fixed-top">
            <div class="container">
                <nav class="d-flex justify-content-between align-items-center">
                    <Link href="/" class="text-decoration-none">
                        <h1 class="h4 mb-0 text-white"><i class="fa-solid fa-book"></i> eBaryo Library</h1>
                    </Link>
                    <div class="d-flex gap-3">
                        <Link 
                            href="/login" 
                            class="btn btn-outline-primary text-white"
                            :class="{ 'active': $page.url === '/login' }"
                        >
                            Login
                        </Link>
                        <Link 
                            href="/register" 
                            class="btn btn-primary text-white"
                            :class="{ 'active': $page.url === '/register' }"
                        >
                            Register
                        </Link>
                    </div>
                </nav>
            </div>
        </header>

        <!-- Main Content Slot -->
        <main class="flex-grow-1" style="padding-top: 90px; padding-bottom: 60px;">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <!-- This slot will contain either login or register form -->
                        <slot />
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-light py-4 text-center text-muted small">
            <div class="container">
                &copy; {{ new Date().getFullYear() }} eBaryo Library. All rights reserved.
            </div>
        </footer>
    </div>
</template>

<style scoped>
.active {
    font-weight: 600;
}
</style>
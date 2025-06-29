<script setup>
import { usePage, Link, router } from "@inertiajs/vue3";
import { computed, ref, provide } from "vue";
import GlobalToast from "@/Components/GlobalToast.vue";
import SearchBar from '@/Components/SearchBar.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);

const toast = ref({ show: false, message: "", type: "success" });
function showToast(message, type = "success") {
    toast.value = { show: true, message, type };
}
provide("showToast", showToast);

const bookSearch = ref('');
function handleBookSearch() {
    if (bookSearch.value.trim()) {
        router.visit(`/books/browse?search=${encodeURIComponent(bookSearch.value.trim())}`);
    }
}
</script>
<template>
    <div>
        <!-- Navigation Bar -->
        <nav
            class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top"
        >
            <div class="container-fluid">
                <Link href="/home" class="navbar-brand fw-bold text-success">
                    <i class="fa fa-book me-2"></i>eBaryo Library
                </Link>
                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarNav"
                    aria-controls="navbarNav"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <Link class="user-nav-link" :class="{ 'user-nav-link-active': $page.url === '/home' }" :href="route('home')">Home</Link>
                        </li>
                        <li class="nav-item">
                            <Link class="user-nav-link" :class="{ 'user-nav-link-active': $page.url.startsWith('/books/browse') }" href="/books/browse">Browse Books</Link>
                        </li>
                    </ul>
                    <div class="d-none d-lg-flex align-items-center ms-3" style="height:48px;">
                        <SearchBar
                            v-model="bookSearch"
                            :placeholder="'Quick search books...'"
                            @keyup.enter="handleBookSearch"
                        />
                        <button
                            class="btn btn-success ms-2"
                            @click="handleBookSearch"
                            :disabled="!bookSearch.trim()"
                            style="height:40px;"
                        >
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item dropdown">
                            <button
                                class="nav-link dropdown-toggle d-flex align-items-center btn btn-link border-0"
                                id="userDropdown"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                            >
                                <img
                                    :src="
                                        user?.avatar ||
                                        '/assets/images/image.png'
                                    "
                                    alt="Avatar"
                                    class="rounded-circle me-2"
                                    style="
                                        width: 32px;
                                        height: 32px;
                                        object-fit: cover;
                                    "
                                />
                                <span>{{ user?.name || "User" }}</span>
                            </button>
                            <ul
                                class="dropdown-menu dropdown-menu-end"
                                aria-labelledby="userDropdown"
                            >
                                <li>
                                    <Link class="dropdown-item" :href="route('user.savedbooks')">
                                        <i class="fa fa-bookmark me-2 text-success"></i>My Saved Books
                                    </Link>
                                </li>
                                <li>
                                    <Link class="dropdown-item" :href="route('user.bookmarks')">
                                        <i class="fa fa-star me-2 text-warning"></i>My Bookmarks
                                    </Link>
                                </li>
                                <li>
                                    <Link class="dropdown-item" href="/suggestions">
                                        <i class="fa fa-lightbulb me-2 text-info"></i>Suggestions
                                    </Link>
                                </li>
                                <li>
                                    <Link class="dropdown-item" href="/announcements">
                                        <i class="fa fa-bullhorn me-2 text-primary"></i>Announcements
                                    </Link>
                                </li>
                                <li><hr class="dropdown-divider" /></li>
                                <li>
                                    <Link class="dropdown-item" :href="route('user.profile')">
                                        <i class="fa fa-user me-2"></i>Profile
                                    </Link>
                                </li>
                                <li><hr class="dropdown-divider" /></li>
                                <li>
                                    <Link
                                        class="dropdown-item text-danger"
                                        method="post"
                                        :href="route('logout')"
                                        as="button"
                                    >
                                        <i class="fa fa-sign-out-alt me-2"></i>Logout
                                    </Link>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Main Content -->
        <main class="py-4">
            <slot />
        </main>
        <footer class="footer-modern bg-white-green border-top py-4">
            <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa fa-book text-success fs-4"></i>
                    <span class="fw-bold text-success">eBaryo Library</span>
                </div>
                <div class="text-muted small">&copy; {{ new Date().getFullYear() }} eBaryo Library. All rights reserved.</div>
                <div class="d-flex gap-3">
                    <a href="#" class="text-success" title="Facebook"><i class="fab fa-facebook fa-lg"></i></a>
                    <a href="#" class="text-success" title="Twitter"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="#" class="text-success" title="Email"><i class="fa fa-envelope fa-lg"></i></a>
                </div>
            </div>
        </footer>
        <GlobalToast
            :show="toast.show"
            :type="toast.type"
            @update:show="toast.show = $event"
        >
            {{ toast.message }}
        </GlobalToast>
    </div>
</template>

<style scoped>
/* Green gradient navbar like AdminLayout */
.navbar {
    background: linear-gradient(135deg, #198754 0%, #146c43 100%) !important;
    box-shadow: 0 2px 8px rgba(30, 41, 59, 0.08);
    border-bottom: 1px solid #e9ecef;
}

.navbar-brand {
    font-size: 1.5rem;
    color: #fff !important;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
}

.user-nav-link {
    background: none;
    border: none;
    color: #fff !important;
    font-weight: 500;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    transition: all 0.3s;
    text-decoration: none;
    position: relative;
    overflow: hidden;
    display: inline-block;
}
.user-nav-link:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #ffc107 !important;
    transform: translateY(-2px);
}
.user-nav-link-active {
    background: rgba(255, 255, 255, 0.15) !important;
    color: #ffc107 !important;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.navbar .nav-link {
    color: #fff !important;
    font-weight: 500;
    transition: color 0.2s, background 0.2s;
    border-radius: 8px;
    padding: 0.5rem 1rem;
}

.navbar .nav-link:hover,
.navbar .nav-link.active {
    background: rgba(255, 255, 255, 0.12);
    color: #ffc107 !important;
}

.navbar .dropdown-menu {
    border-radius: 12px;
    padding: 0.5rem;
    box-shadow: 0 4px 16px rgba(30, 41, 59, 0.1);
    background: #fff;
    min-width: 180px;
}

.navbar .dropdown-item {
    border-radius: 8px;
    padding: 0.75rem 1rem;
    transition: all 0.2s ease;
}

.navbar .dropdown-item:hover {
    background: #f8f9fa;
    transform: translateX(3px);
    color: #198754;
}

.rounded-circle {
    border: 2px solid #198754;
    box-shadow: 0 2px 8px rgba(25, 135, 84, 0.08);
}

main {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    min-height: 90vh;
    border-radius: 0 0 16px 16px;
    box-shadow: 0 2px 8px rgba(30, 41, 59, 0.04);
}

/* Responsive tweaks */
@media (max-width: 991.98px) {
    .navbar .nav-link {
        padding: 0.5rem 0.75rem;
    }
}

.footer-modern {
    background: linear-gradient(135deg, #f8fafc 0%, #e9f7ef 100%);
    border-top: 1px solid #e9ecef;
    font-size: 1rem;
    z-index: 1;
}

.footer-modern a {
    transition: color 0.2s;
}

.footer-modern a:hover {
    color: #145c32 !important;
}

.bg-white-green {
    background: linear-gradient(135deg, #f8fafc 0%, #e9f7ef 100%);
}

.searchbar-navbar {
    background: #fff;
    border-radius: 999px;
    box-shadow: 0 2px 8px rgba(30,41,59,0.08);
    padding: 0 0.5rem;
    min-width: 260px;
    align-items: center;
}
.searchbar-navbar .form-control {
    background: #fff;
    border: none;
    box-shadow: none;
    border-radius: 999px;
    font-size: 1rem;
}
.searchbar-navbar .input-group-text {
    background: #fff;
    border: none;
    border-radius: 999px 0 0 999px;
    padding-right: 0.25rem;
}
.searchbar-navbar .btn-link {
    color: #dc3545;
    border: none;
    box-shadow: none;
}
</style>

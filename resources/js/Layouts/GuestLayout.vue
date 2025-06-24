<script setup>
import { Link } from "@inertiajs/vue3";
</script>

<template>
  <div class="min-vh-100 d-flex flex-column bg-main-gradient">
    <!-- Header -->
    <header class="navbar-green-gradient shadow-lg fixed-top px-0 rounded-bottom-4">
      <div class="container">
        <nav class="navbar navbar-expand-lg navbar-dark justify-content-between align-items-center" style="min-height: 70px;">
          <Link
            href="/"
            class="navbar-brand d-flex align-items-center gap-2 fs-3 fw-bold brand-glow"
            style="letter-spacing: 1px; transition: color 0.2s, text-shadow 0.2s;"
          >
            <i class="fa-solid fa-book fa-2x brand-icon-glow"></i>
            <span class="d-none d-sm-inline text-white">eBaryo Library</span>
          </Link>
          <div class="d-flex gap-2">
            <Link
              href="/login"
              class="btn btn-outline-light rounded-pill px-4 btn-nav"
              :class="{ active: $page.url === '/login' }"
            >
              Login
            </Link>
            <Link
              href="/register"
              class="btn btn-light text-success rounded-pill px-4 btn-nav"
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
      class="flex-grow-1 d-flex align-items-center justify-content-center py-5"
      style="padding-top: 100px; padding-bottom: 60px; min-height: 70vh;"
    >
      <div class="container">
        <slot />
      </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white py-4 text-center text-muted small border-top border-2 mt-auto footer-modern">
      <div class="container">
        <hr class="mb-3 mt-0" />
        <span>&copy; {{ new Date().getFullYear() }} <span class="fw-semibold text-success">eBaryo Library</span>. All rights reserved.</span>
      </div>
    </footer>
  </div>
</template>

<style scoped>
.bg-main-gradient {
  background: linear-gradient(135deg, #f8fafc 0%, #e9f7ef 100%);
}

.navbar-green-gradient {
  background: linear-gradient(90deg, #198754 70%, #43c59e 100%);
  border-bottom: 2.5px solid #157347;
  box-shadow: 0 6px 32px 0 rgba(25, 135, 84, 0.13);
  z-index: 1040;
  border-bottom-left-radius: 1.2rem;
  border-bottom-right-radius: 1.2rem;
  transition: box-shadow 0.2s, background 0.2s;
}

.brand-glow:hover, .brand-glow:focus {
  color: #fff !important;
  text-shadow: 0 0 8px #43c59e, 0 0 2px #fff;
}

.brand-icon-glow {
  filter: drop-shadow(0 0 8px #43c59e88);
  transition: filter 0.2s;
}

.btn-nav {
  transition: box-shadow 0.2s, transform 0.2s, background 0.2s, color 0.2s;
  font-weight: 500;
}
.btn-nav:hover, .btn-nav.active {
  box-shadow: 0 0 0 0.18rem rgba(255,255,255,0.18);
  transform: translateY(-2px) scale(1.04);
  background: #e9f7ef;
  color: #198754 !important;
}

.footer-modern {
  background: rgba(255,255,255,0.98);
  border-top: 1.5px solid #e9ecef;
}

@media (max-width: 767.98px) {
  .navbar-green-gradient {
    padding-left: 0.5rem;
    padding-right: 0.5rem;
    border-bottom-left-radius: 0.7rem;
    border-bottom-right-radius: 0.7rem;
  }
  .footer-modern {
    font-size: 0.95rem;
  }
  .navbar-brand .d-none.d-sm-inline {
    display: inline !important;
  }
}
</style>

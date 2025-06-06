<template>
  <div>
    <nav class="navbar navbar-expand-lg navbar-dark">
      <div class="container">
        <Link class="navbar-brand fw-bold" href="/"><i class="fas fa-book me-2"></i>eBaryo Library</Link>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <template v-if="!$page.props.auth.user">
              <li class="nav-item"><Link class="nav-link px-3" :href="route('login')">Login</Link></li>
              <li class="nav-item"><Link class="nav-link px-3" :href="route('register')">Register</Link></li>
            </template>
            <template v-else>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle px-3" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="fas fa-user-circle me-1"></i>{{ $page.props.auth.user.name }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li><Link class="dropdown-item" :href="route('logout')" method="post" as="button"><i class="fas fa-sign-out-alt me-2"></i>Logout</Link></li>
                </ul> 
              </li>
            </template>
          </ul>
        </div>
      </div>
    </nav>
    <main class="container py-4"><slot>

    </slot>
</main>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<style>
:root {
  --primary-green: #2E7D32;
  --light-green: #81C784;
  --dark-green: #1B5E20;
  --hover-green: #43A047;
  --background-green: #F1F8E9;
  --text-green: #1B5E20;
}
body {
  background-color: var(--background-green) !important;
  color: var(--text-green);
}
.navbar {
  background: linear-gradient(135deg, var(--primary-green), var(--dark-green)) !important;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.navbar-brand {
  font-size: 1.5rem;
  transition: transform 0.2s;
}
.navbar-brand:hover {
  transform: scale(1.05);
}
.btn-primary {
  background-color: var(--primary-green) !important;
  border-color: var(--primary-green) !important;
  transition: all 0.3s ease;
}
.btn-primary:hover {
  background-color: var(--hover-green) !important;
  border-color: var(--hover-green) !important;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.dropdown-item {
  transition: all 0.2s ease;
}
.dropdown-item:hover {
  background-color: var(--light-green) !important;
  color: white !important;
  transform: translateX(5px);
}
.nav-link {
  position: relative;
  transition: color 0.3s ease;
}
.nav-link:hover {
  color: var(--light-green) !important;
}
.nav-link::after {
  content: '';
  position: absolute;
  width: 0;
  height: 2px;
  bottom: 0;
  left: 50%;
  background-color: var(--light-green);
  transition: all 0.3s ease;
}
.nav-link:hover::after {
  width: 100%;
  left: 0;
}
.card {
  border: none !important;
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}
.card-header {
  background: linear-gradient(135deg, var(--primary-green), var(--dark-green)) !important;
  color: white !important;
  border-radius: 10px 10px 0 0 !important;
}
.form-control {
  border-radius: 8px;
  border: 2px solid #E0E0E0;
  transition: all 0.3s ease;
}
.form-control:focus {
  border-color: var(--primary-green) !important;
  box-shadow: 0 0 0 0.25rem rgba(46, 125, 50, 0.25) !important;
}
@media (max-width: 768px) {
  .navbar-brand { font-size: 1.2rem; }
  .nav-link { padding: 0.5rem 1rem !important; }
  .container { padding-left: 15px; padding-right: 15px; }
}
</style>

<template>
    <div>
      <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
          <button class="btn btn-link text-white me-3 sidebar-toggle" @click="toggleSidebar">
            <i class="fas" :class="sidebarOpen ? 'fa-times' : 'fa-bars'"></i>
          </button>
          <a class="navbar-brand fw-bold" href="/"><i class="fas fa-book me-2"></i>eBaryo Library</a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle px-3" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-circle me-1"></i>{{ $page.props.auth.user.name }}
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <Link class="dropdown-item" :href="route('logout')" method="post" as="button"><i class="fas fa-sign-out-alt me-2"></i>Logout</Link>
                    </li>
                  </ul>
                </li>
            </ul>
          </div>
        </div>
      </nav>

      <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar" :class="{ 'collapsed': !sidebarOpen }">
          <div class="sidebar-header">
            <h5 class="text-white mb-0"><i class="fas fa-shield-alt me-2"></i>Admin Panel</h5>
          </div>
          <ul class="nav flex-column">
            <li class="nav-item">
              <Link class="nav-link" :class="{ 'active': $page.url.startsWith('/admin/dashboard') }" href="/admin/dashboard">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
              </Link>
            </li>
            <li class="nav-item">
              <Link class="nav-link" :class="{ 'active': $page.url.startsWith('/admin/books') }" href="/admin/books">
                <i class="fas fa-book me-2"></i>Books
              </Link>
            </li>
            <li class="nav-item">
              <Link class="nav-link" :class="{ 'active': $page.url.startsWith('/admin/borrowers') }" href="/admin/borrowers">
                <i class="fas fa-users me-2"></i>Borrowers
              </Link>
            </li>
            <li class="nav-item">
              <Link class="nav-link" :class="{ 'active': $page.url.startsWith('/admin/borrowings') }" href="/admin/borrowings">
                <i class="fas fa-hand-holding me-2"></i>Borrowings
              </Link>
            </li>
            <li class="nav-item">
              <Link class="nav-link" :class="{ 'active': $page.url.startsWith('/admin/reports') }" href="/admin/reports">
                <i class="fas fa-chart-bar me-2"></i>Reports
              </Link>
            </li>
            <li class="nav-item">
              <Link class="nav-link" :class="{ 'active': $page.url.startsWith('/admin/settings') }" href="/admin/settings">
                <i class="fas fa-cog me-2"></i>Settings
              </Link>
            </li>
          </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content" :class="{ 'expanded': !sidebarOpen }">
          <main class="container-fluid py-4">
            <slot></slot>
          </main>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { Link } from '@inertiajs/vue3';
  import { ref } from 'vue';

  const sidebarOpen = ref(true);

  const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
  };
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
    padding: 0.8rem 1rem;
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
  
  .dropdown-item:hover {
    background-color: var(--light-green) !important;
    color: white !important;
    transform: translateX(5px);
  }
  
  .nav-link:hover {
    color: var(--light-green) !important;
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
  
  .form-control:focus {
    border-color: var(--primary-green) !important;
    box-shadow: 0 0 0 0.25rem rgba(46, 125, 50, 0.25) !important;
  }
  
  @media (max-width: 768px) {
    .navbar-brand { font-size: 1.2rem; }
    .nav-link { padding: 0.5rem 1rem !important; }
    .container { padding-left: 15px; padding-right: 15px; }
  }
  
  /* Custom styles can be added here */

  /* Enhanced Navbar Styles */
  .navbar-brand {
    font-size: 1.4rem;
    transition: transform 0.2s;
  }

  .navbar-brand:hover {
    transform: scale(1.05);
  }

  .sidebar-toggle {
    font-size: 1.2rem;
    padding: 0.5rem;
    transition: all 0.3s ease;
  }

  .sidebar-toggle:hover {
    transform: scale(1.1);
  }

  /* Enhanced Sidebar Styles */
  .sidebar {
    width: 250px;
    min-height: calc(100vh - 50px);
    background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
    transition: all 0.3s ease;
    position: fixed;
    top: 70px;
    left: 0;
    z-index: 1000;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
  }

  .sidebar.collapsed {
    transform: translateX(-250px);
  }

  .sidebar-header {
    padding: 1.2rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(0, 0, 0, 0.1);
  }

  .sidebar .nav-link {
    color: rgba(255, 255, 255, 0.8);
    padding: 0.9rem 1.2rem;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
  }

  .sidebar .nav-link:hover {
    color: white;
    background-color: rgba(255, 255, 255, 0.1);
    transform: translateX(5px);
    border-left: 3px solid var(--light-green);
  }

  .sidebar .nav-link.active {
    color: white;
    background-color: rgba(255, 255, 255, 0.2);
    border-left: 3px solid var(--light-green);
  }

  .main-content {
    margin-left: 250px;
    transition: all 0.3s ease;
    width: calc(100% - 250px);
    min-height: calc(100vh - 56px);
    background-color: var(--background-green);
  }

  .main-content.expanded {
    margin-left: 0;
    width: 100%;
  }

  @media (max-width: 768px) {
    .sidebar {
      transform: translateX(-250px);
    }
    
    .sidebar.collapsed {
      transform: translateX(0);
    }

    .main-content {
      margin-left: 0;
      width: 100%;
    }

    .navbar-brand {
      font-size: 1.2rem;
    }
  }
  </style>
  
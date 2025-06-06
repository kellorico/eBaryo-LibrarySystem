<template>
  <Main>
    <main class="d-flex flex-column min-vh-100">
      <!-- Hero Section -->
      <div class="hero-section">
        <div class="hero-overlay"></div>
        <div class="container position-relative">
          <div class="row align-items-center" style="min-height: 60vh;">
            <div class="col-lg-8 mx-auto text-center">
              <h1 class="display-4 mb-3 fw-bold text-white">Welcome to eBaryo Library</h1>
              <p class="lead mb-4 text-white">Discover a world of knowledge at your fingertips</p>
              <div class="d-flex justify-content-center gap-3 flex-wrap">
                <Link :href="route('login')" class="btn btn-light btn-lg px-4">
                  <i class="fas fa-sign-in-alt me-2"></i>Login
                </Link>
                <Link :href="route('register')" class="btn btn-outline-light btn-lg px-4">
                  <i class="fas fa-user-plus me-2"></i>Register
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Features Section -->
      <div class="container py-4">
        <div class="row g-3">
          <div class="col-md-4">
            <div class="card h-100 feature-card">
              <div class="card-body text-center p-3">
                <i class="fas fa-book fs-1 mb-2 text-primary"></i>
                <h3 class="card-title h5 mb-2">Extensive Collection</h3>
                <p class="card-text small">Access thousands of books, journals, and digital resources.</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card h-100 feature-card">
              <div class="card-body text-center p-3">
                <i class="fas fa-clock fs-1 mb-2 text-primary"></i>
                <h3 class="card-title h5 mb-2">24/7 Access</h3>
                <p class="card-text small">Browse and manage your library account anytime, anywhere.</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card h-100 feature-card">
              <div class="card-body text-center p-3">
                <i class="fas fa-users fs-1 mb-2 text-primary"></i>
                <h3 class="card-title h5 mb-2">Community</h3>
                <p class="card-text small">Join our community of readers and knowledge seekers.</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Call to Action -->
      <div class="cta-section py-4 mt-auto">
        <div class="container text-center">
          <h2 class="h3 mb-3 fw-bold">Ready to Start Your Journey?</h2>
          <p class="lead mb-3">Join our library today and unlock a world of possibilities.</p>
          <Link :href="route('register')" class="btn btn-light btn-lg">
            <i class="fas fa-arrow-right me-2"></i>Get Started
          </Link>
        </div>
      </div>
    </main>
  </Main>
</template>

<script setup>
import Main from '@/Layouts/Main.vue';
import { Link } from '@inertiajs/vue3';
</script>

<style scoped>

.hero-section {
  position: relative;
  background-size: cover;
  background-image: url('assets/images/image.png');
  background-position: center;
  background-repeat: no-repeat;
  min-height: 60vh;
  display: flex;
  align-items: center;
}

.hero-overlay {
  position: absolute;
  border-radius: 5px;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, 
    rgba(46, 125, 50, 0.9), 
    rgba(27, 94, 32, 0.85)
  );
}

.hero-section .container {
  z-index: 1;
}

.hero-section h1 {
  text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.hero-section .lead {
  text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
}

.btn-light {
  background-color: white;
  border-color: white;
  color: var(--primary-green);
  transition: all 0.3s ease;
}

.btn-light:hover {
  background-color: var(--primary-green);
  border-color: var(--primary-green);
  color: white;
  transform: translateY(-2px);
}

.btn-outline-light {
  border-width: 2px;
  transition: all 0.3s ease;
}

.btn-outline-light:hover {
  background-color: white;
  color: var(--primary-green);
  transform: translateY(-2px);
}

.feature-card {
  border: none;
  background: white;
  transition: all 0.3s ease;
}

.feature-card:hover {
  transform: translateY(-5px);
}

.feature-card .bi {
  color: var(--primary-green);
  transition: transform 0.3s ease;
}

.feature-card:hover .bi {
  transform: scale(1.2);
}

.cta-section {
  background: linear-gradient(135deg, var(--primary-green), var(--dark-green));;
  color: white;
  border-radius: 5px;
}

@media (max-width: 768px) {
  .hero-section {
    min-height: 50vh;
  }

  .display-4 {
    font-size: 2rem;
  }

  .lead {
    font-size: 1rem;
  }

  .feature-card {
    margin-bottom: 0.5rem;
  }
}
</style>

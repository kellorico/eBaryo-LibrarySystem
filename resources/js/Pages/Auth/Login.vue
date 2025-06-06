<script setup>
import Main from '@/Layouts/Main.vue'
import { useForm, Link } from '@inertiajs/vue3'

const form = useForm({
    email: '',
    password: ''
})

const login = () => {
  form.post('/login', {
    onError: () => {
        form.password = ''
        setTimeout(() => {
          form.errors.email = null;
          form.errors.password = null;
        }, 2000);
    }
  })
}
</script>

<template>
    <Main>
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-lg">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <i class="fas fa-user-circle display-4 text-primary mb-3"></i>
                            <h2 class="h3 mb-3">Welcome Back</h2>
                            <p class="text-muted">Please sign in to continue</p>
                        </div>
                        <form @submit.prevent="login">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input
                                        type="email"
                                        class="form-control"
                                        id="email"
                                        v-model="form.email"
                                        placeholder="Enter your email"
                                        :class="{ 'is-invalid': form.errors.email }"
                                    >
                                </div>
                                <div v-if="form.errors.email" class="invalid-feedback d-block">
                                    {{ form.errors.email }}
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input
                                        type="password"
                                        class="form-control"
                                        id="password"
                                        v-model="form.password"
                                        placeholder="Enter your password"
                                        :class="{ 'is-invalid': form.errors.password }"
                                    >
                                </div>
                                <div v-if="form.errors.password" class="invalid-feedback d-block">
                                    {{ form.errors.password }}
                                </div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg" :disabled="form.processing">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    {{ form.processing ? 'Signing in...' : 'Sign In' }}
                                </button>
                            </div>
                        </form>
                        <div class="text-center mt-4">
                            <p class="mb-0">Don't have an account? 
                                <Link :href="route('register')" class="text-primary fw-bold">Register here</Link>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Main>
</template>

<style scoped>
.card {
    border-radius: 15px;
    border: none;
}

.input-group-text {
    background-color: var(--background-green);
    border: 2px solid #E0E0E0;
    border-right: none;
}

.input-group .form-control {
    border-left: none;
}

.input-group .form-control:focus {
    border-color: #E0E0E0;
    box-shadow: none;
}

.input-group:focus-within {
    box-shadow: 0 0 0 0.25rem rgba(46, 125, 50, 0.25);
    border-radius: 8px;
}

.input-group:focus-within .input-group-text,
.input-group:focus-within .form-control {
    border-color: var(--primary-green);
}

.btn-primary {
    padding: 0.8rem;
}

@media (max-width: 768px) {
    .card {
        margin: 1rem;
    }
}
</style>



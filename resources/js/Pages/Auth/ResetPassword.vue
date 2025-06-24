<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputField from '@/Components/InputField.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm, Link, usePage } from '@inertiajs/vue3';

const page = usePage();

const form = useForm({
    email: page.props.email || '',
    token: page.props.token || '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onSuccess: () => form.reset(),
        preserveScroll: true,
        onError: () => {
            form.reset('password', 'password_confirmation');
            form.processing = false;
        },
    });
};
</script>

<template>
    <GuestLayout>
        <div class="container py-4">
        <div class="card shadow-lg border-0 rounded-4 bg-white-green reset-card-animate position-relative small-card">
            <div class="accent-bar-green"></div>
            <div class="card-body p-4">
            <h2 class="text-center mb-3 fw-bold text-success" style="font-size:1.5rem;"><i class="fa-solid fa-lock me-2"></i>Reset Password</h2>
            <p class="text-center mb-3">Enter your new password below.</p>
            <form @submit.prevent="submit">
                <InputField
                id="email"
                label="Email"
                type="email"
                v-model="form.email"
                :error="form.errors.email"
                >
                    <template #icon>
                        <i class="fa-solid fa-envelope"></i>
                    </template>
                </InputField>
                <InputField
                id="new-password"
                label="New Password"
                type="password"
                v-model="form.password"
                :error="form.errors.password"
                >
                    <template #icon>
                        <i class="fa-solid fa-lock"></i>
                    </template>
                </InputField>
                <InputField
                id="confirm-password"
                label="Confirm Password"
                type="password"
                v-model="form.password_confirmation"
                :error="form.errors.password_confirmation"
                >
                    <template #icon>
                        <i class="fa-solid fa-lock"></i>
                    </template>
                </InputField>
                <PrimaryButton
                class="w-100 mt-2 btn-success-green btn-ripple small-btn"
                :disabled="form.processing"
                :isLoading="form.processing"
                >
                Reset Password
                </PrimaryButton>
            </form>
            <div class="text-center mt-3" style="font-size:0.97rem;">
                <p>Remembered your password? <Link href="/login" class="link-green">Login</Link></p>
            </div>
            </div>
        </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
.bg-white-green {
    background: linear-gradient(135deg, #f8fafc 0%, #e9f7ef 100%);
}
.reset-card-animate {
    opacity: 0;
    transform: translateY(40px);
    animation: cardFadeIn 0.8s 0.1s forwards;
    transition: box-shadow 0.22s cubic-bezier(.4,2,.3,1), transform 0.22s cubic-bezier(.4,2,.3,1);
}
.reset-card-animate:hover, .reset-card-animate:focus-within {
    box-shadow: 0 8px 32px #19875433, 0 2px 8px #43c59e22;
    transform: translateY(-2px) scale(1.01);
}
@keyframes cardFadeIn {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.accent-bar-green {
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 6px;
    background: linear-gradient(90deg, #198754 60%, #43c59e 100%);
    border-top-left-radius: 1rem;
    border-top-right-radius: 1rem;
    z-index: 2;
}
.link-green {
    color: #198754;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s;
}
.link-green:hover {
    text-decoration: underline;
    color: #145c32;
}
.btn-success-green.small-btn {
    font-size: 1rem;
    padding: 0.6rem 1rem;
}
.small-card {
    max-width: 370px;
    margin: 0 auto;
}
.card-body {
    padding: 1.5rem !important;
}
.container {
    max-width: 400px;
}
@media (max-width: 575.98px) {
    .small-card {
        max-width: 98vw;
    }
    .container {
        max-width: 98vw;
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    .card-body {
        padding: 1rem !important;
    }
}
</style>
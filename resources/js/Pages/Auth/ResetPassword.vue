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
        <div class="container py-5">
        <div class="card shadow border-0 rounded-4">
            <div class="card-body p-5">
            <h2 class="text-center mb-4 fw-bold">Reset Password</h2>
            <p class="text-center mb-4">Enter your new password below.</p>
            <form @submit.prevent="submit">
                <InputField
                id="email"
                label="Email"
                type="email"
                v-model="form.email"
                :error="form.errors.email"
                />
                <InputField
                id="new password"
                label="New Password"
                type="password"
                v-model="form.password"
                :error="form.errors.password"
                />
                <InputField
                id="confirm password"
                label="Confirm Password"
                type="password"
                v-model="form.password_confirmation"
                :error="form.errors.password_confirmation"
                />
                <PrimaryButton
                class="w-100 mt-3"
                :disabled="form.processing"
                :isLoading="form.processing"
                >
                Reset Password
                </PrimaryButton>
            </form>
            <div class="text-center mt-4">
                <p>Remembered your password? <Link href="/login" class="link">Login</Link></p>
            </div>
            </div>
        </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
.container {
    max-width: 600px;
}

.link {
    color: #198754;
    text-decoration: none;
}
.link:hover {
    text-decoration: underline;
}
</style>
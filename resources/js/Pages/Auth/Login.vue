<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputField from "@/Components/InputField.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { useForm, Link, usePage } from "@inertiajs/vue3";

const formData = useForm({
    email: "",
    password: "",
});

const page = usePage();

if (page.props.google_error) {
    console.error("Google login error:", page.props.google_error);
}

const submitForm = () => {
    formData.post(route("login"), {
        onSuccess: () => {
            formData.processing = false;
            formData.reset("password");
        },
        onError: (errors) => {
            formData.processing = false;
            formData.reset("password");
            formData.errors = errors;
        },
    });
};
</script>

<template>
    <GuestLayout>
        <div
            class="container min-vh-100 d-flex align-items-center justify-content-center py-5"
        >
            <div class="w-100" style="max-width: 420px">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-5">
                        <div
                            v-if="page.props.google_error"
                            class="alert alert-danger mt-3"
                        >
                            {{ page.props.google_error }}
                        </div>

                        <div class="d-flex flex-column align-items-center mb-4">
                            <!-- Logo Placeholder -->
                            <div class="mb-3">
                                <img
                                    src="/img/image.png"
                                    alt="Logo"
                                    width="48"
                                    height="48"
                                    class="rounded-circle shadow-sm bg-light"
                                />
                            </div>
                            <h2 class="fw-bold mb-0 text-success">Sign In</h2>
                            <p class="text-muted mt-1 mb-0 small">
                                Welcome back! Please login to your account.
                            </p>
                        </div>
                        <form @submit.prevent="submitForm" autocomplete="on">
                            <InputField
                                id="email"
                                label="Email"
                                type="email"
                                v-model="formData.email"
                                :error="formData.errors.email"
                                class="mb-3"
                                icon="bi bi-envelope"
                            />
                            <InputField
                                id="password"
                                label="Password"
                                type="password"
                                v-model="formData.password"
                                :error="
                                    formData.errors.password ||
                                    (page.props.error ? page.props.error : '')
                                "
                                class="mb-2"
                                icon="bi bi-lock"
                            />
                            <div class="d-flex justify-content-end mb-3">
                                <Link href="/forgot-password" class="link small"
                                    >Forgot Password?</Link
                                >
                            </div>
                            <PrimaryButton
                                class="w-100 py-2 fs-5 rounded-pill"
                                :disabled="formData.processing"
                                :isLoading="formData.processing"
                            >
                                Login
                            </PrimaryButton>

                            <!-- Divider -->
                            <div class="d-flex align-items-center my-3">
                                <hr class="flex-grow-1" />
                                <span class="mx-2 text-muted small">or</span>
                                <hr class="flex-grow-1" />
                            </div>

                            <!-- Social Login Buttons -->
                            <div class="d-flex flex-column align-items-center">
                                <div
                                    class="d-flex justify-content-center gap-2 my-2"
                                >
                                    <a
                                        href="/auth/google"
                                        class="btn btn-outline-danger btn-sm d-flex align-items-center justify-content-center rounded-pill p-2"
                                        aria-label="Sign in with Google"
                                    >
                                        <i class="bi bi-google fs-5"></i>
                                    </a>
                                </div>
                                <div class="text-center">
                                    <small class="text-muted"
                                        >Sign in with</small
                                    >
                                </div>
                            </div>
                            <div class="text-center mt-4">
                                <span class="text-muted"
                                    >Don't have an account?</span
                                >
                                <Link
                                    href="/register"
                                    class="link fw-semibold ms-1"
                                    >Register</Link
                                >
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
.link {
    color: #198754;
    text-decoration: none;
    transition: color 0.2s;
}

.link:hover {
    text-decoration: underline;
    color: #145c32;
}
</style>

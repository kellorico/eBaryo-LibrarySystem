<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputField from "@/Components/InputField.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { useForm, Link } from "@inertiajs/vue3";

const formData = useForm({
    email: "",
    password: "",
});

const submitForm = () => {
    formData.post("/login", {
        onFinish: () => formData.reset(),
        preserveScroll: true,
        errorBag: "login",
    });
};
</script>

<template>
    <GuestLayout>
        <div class="container py-5">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4 fw-bold">
                        Login to Your Account
                    </h2>
                    <form @submit.prevent="submitForm">
                        <InputField
                            id="email"
                            label="Email"
                            type="email"
                            v-model="formData.email"
                            required
                            :error="formData.errors.email"
                        />
                        <InputField
                            id="password"
                            label="Password"
                            type="password"
                            v-model="formData.password"
                            required
                            :error="formData.errors.password"
                        />

                        <PrimaryButton
                            class="w-100 mt-3"
                            :disabled="formData.processing"
                            :isLoading="formData.processing"
                        >
                            Login
                        </PrimaryButton>

                        <div class="text-center mt-4">
                            <p>
                                Don't have an account?
                                <Link href="/register" class="link"
                                    >Register</Link
                                >
                            </p>
                            <p>
                                <Link href="/forgot-password" class="link"
                                    >Forgot Password?</Link
                                >
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
.link {
    color: #198754;
    text-decoration: none;
}
.link:hover {
    text-decoration: underline;
}

.container {
    max-width: 600px;
}
</style>

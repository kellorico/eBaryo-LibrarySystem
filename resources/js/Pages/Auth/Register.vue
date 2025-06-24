<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputField from "@/Components/InputField.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { useForm, Link } from "@inertiajs/vue3";

const formData = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const submitForm = () => {
    formData.processing = true;
    formData.post(route("register"), {
        onSuccess: () => formData.reset(),
        preserveScroll: true,
        onError: (error) => {
            formData.reset("email", "password", "password_confirmation");
            formData.processing = false;
        },
    });
};
</script>

<template>
    <GuestLayout>
        <div class="container py-5">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4 fw-bold">Create an Account</h2>
                    <form @submit.prevent="submitForm">
                        <InputField
                            id="name"
                            label="Name"
                            type="text"
                            v-model="formData.name"
                            :error="formData.errors.name"
                        />
                        <InputField
                            id="email"
                            label="Email"
                            type="email"
                            v-model="formData.email"
                            :error="formData.errors.email"
                        />
                        <InputField
                            id="password"
                            label="Password"
                            type="password"
                            v-model="formData.password"
                            :error="formData.errors.password"
                        />
                        <InputField
                            id="password_confirmation"
                            label="Confirm Password"
                            type="password"
                            v-model="formData.password_confirmation"
                            :error="formData.errors.password_confirmation"
                        />

                        <PrimaryButton
                            class="w-100 mt-3"
                            :disabled="formData.processing"
                            :isLoading="formData.processing"
                        >
                            Register
                        </PrimaryButton>

                        <div class="text-center mt-4">
                            <p>
                                Already have an account?
                                <Link href="/login" class="link">Login</Link>
                            </p>
                            <p>
                                <Link :href="route('password.request')" class="link"
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

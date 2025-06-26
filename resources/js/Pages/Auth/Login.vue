<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputField from "@/Components/InputField.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { useForm } from "@inertiajs/vue3";
import Toast from "@/Components/Toast.vue";
import { ref, watch, computed, onMounted } from "vue";

const formData = useForm({
    email: "",
    password: "",
});

const props = defineProps({
    success: String,
});

const showToast = ref(false);

const successMessage = computed(
    () =>
        props.success ||
        new URLSearchParams(window.location.search).get("success")
);

watch(
    () => successMessage.value,
    (val) => {
        if (val) showToast.value = true;
    }
);

watch(showToast, (val) => {
    if (val) {
        setTimeout(() => {
            showToast.value = false;
        }, 3000);
    }
});

const submitForm = () => {
    formData.post(route("login"), {
        onSuccess: () => formData.reset(),
        preserveScroll: true,
        onError: (error) => {
            formData.reset("password");
        },
    });
};

onMounted(() => {
    if (successMessage.value) {
        showToast.value = true;
        const url = new URL(window.location.href);
        url.searchParams.delete("success");
        window.history.replaceState(
            {},
            document.title,
            url.pathname + url.search
        );
    }
});
</script>

<template>
    <GuestLayout>
        <Head title="Login" />
        <Toast v-model="showToast" v-if="successMessage" class="toast-green">{{
            successMessage
        }}</Toast>
        <div class="container py-5">
            <div
                class="card shadow-lg border-0 rounded-4 bg-white-green login-card-animate position-relative small-card"
            >
                <div class="accent-bar-green"></div>
                <div class="card-body p-4">
                    <h2
                        class="text-center mb-3 fw-bold text-success"
                        style="font-size: 1.5rem"
                    >
                        <i class="fa-solid fa-right-to-bracket me-2"></i>Login
                        to Your Account
                    </h2>
                    <form @submit.prevent="submitForm">
                        <InputField
                            id="email"
                            label="Email"
                            type="email"
                            v-model="formData.email"
                            :error="formData.errors.email"
                        >
                            <template #icon>
                                <i class="fa-solid fa-envelope"></i>
                            </template>
                        </InputField>
                        <InputField
                            id="password"
                            label="Password"
                            type="password"
                            v-model="formData.password"
                            :error="formData.errors.password"
                        >
                            <template #icon>
                                <i class="fa-solid fa-lock"></i>
                            </template>
                        </InputField>

                        <PrimaryButton
                            class="w-100 mt-2 btn-success-green btn-ripple small-btn"
                            :disabled="formData.processing"
                            :isLoading="formData.processing"
                        >
                            Login
                        </PrimaryButton>

                        <div
                            class="text-center mt-3"
                            style="font-size: 0.97rem"
                        >
                            <p>
                                Don't have an account?
                                <Link href="/register" class="link-green"
                                    >Register</Link
                                >
                            </p>
                            <p>
                                <Link
                                    :href="route('password.request')"
                                    class="link-green"
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
.toast-green {
    background: #e9f7ef;
    color: #198754;
    font-weight: 600;
    border-radius: 0.7rem;
    box-shadow: 0 2px 12px #19875422;
}
.bg-white-green {
    background: linear-gradient(135deg, #f8fafc 0%, #e9f7ef 100%);
}
.login-card-animate {
    opacity: 0;
    transform: translateY(40px);
    animation: cardFadeIn 0.8s 0.1s forwards;
    transition: box-shadow 0.22s cubic-bezier(0.4, 2, 0.3, 1),
        transform 0.22s cubic-bezier(0.4, 2, 0.3, 1);
}
.login-card-animate:hover,
.login-card-animate:focus-within {
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
    top: 0;
    left: 0;
    right: 0;
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

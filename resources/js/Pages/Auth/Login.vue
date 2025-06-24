<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputField from "@/Components/InputField.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { useForm, Link,usePage } from "@inertiajs/vue3";
import Toast from '@/Components/Toast.vue';
import { ref, watch, computed, onMounted } from 'vue';

const formData = useForm({
    email: "",
    password: "",
});

const props = defineProps({
    success: String
});

const showToast = ref(false);

const successMessage = computed(() => props.success || (new URLSearchParams(window.location.search)).get('success'));

watch(() => successMessage.value, (val) => {
  if (val) showToast.value = true;
});

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
    url.searchParams.delete('success');
    window.history.replaceState({}, document.title, url.pathname + url.search);
  }
});
</script>

<template>
    <GuestLayout>
        <Toast v-model="showToast" v-if="successMessage">{{ successMessage }}</Toast>
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
                            :error="formData.errors.email"
                        />
                        <InputField
                            id="password"
                            label="Password"
                            type="password"
                            v-model="formData.password"
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

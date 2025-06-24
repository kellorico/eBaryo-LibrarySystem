<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputField from "@/Components/InputField.vue";
import { useForm,Link } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Toast from '@/Components/Toast.vue';
import { ref, watch } from 'vue';

const form = useForm({
    email: "",
});

const props = defineProps({
    status: {
        type: String,
        default: "",
    },
});

const showToast = ref(false);

watch(() => props.status, (val) => {
  if (val) showToast.value = true;
});

watch(showToast, (val) => {
  if (val) {
    setTimeout(() => {
      showToast.value = false;
    }, 3000);
  }
});

const submit = () => {
    form.post(route('password.email'), {
        onSuccess: () => form.reset(),
        preserveScroll: true,
        onError: () => {
            form.reset("email");
            form.processing = false;
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Toast v-model="showToast" v-if="props.status">{{ props.status }}</Toast>
        <div class="container py-5">
        <div class="card shadow border-0 rounded-4">
            <div class="card-body p-5">
            <h2 class="text-center mb-4 fw-bold">Forgot Password</h2>
            <p class="text-center mb-4">Enter your email to reset your password.</p>
            <form @submit.prevent="submit">
                <div class="mb-3">
                <InputField
                    id="email"
                    label="Email"
                    type="email"
                    v-model="form.email"
                    :error="form.errors.email"
                    />
                </div>
                <PrimaryButton class="w-100 mt-3" :disabled="form.processing" :isLoading="form.processing">
                    Send Password Reset Link
                </PrimaryButton>
            </form>
            <div class="text-center mt-4">
                <p>Remembered your password? <Link :href="route('login')" class="link">Login</Link></p>
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
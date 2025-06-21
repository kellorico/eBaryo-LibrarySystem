<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputField from '@/Components/InputField.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm,Link } from '@inertiajs/vue3';

const formData = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submitForm = () => {
    formData.processing = true;
    formData.post(route('register'), {
        onFinish: () => {
            formData.processing = false;
            formData.reset();
        },
        preserveScroll: true,
        errorBag: 'register',
    });
};
</script>

<template>
    <GuestLayout>
        <div class="card shadow-sm">
            <div class="card-body p-5">
                <h2 class="card-title text-center mb-4">Register</h2>
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
                    <PrimaryButton :disabled="formData.processing" :isLoading="formData.processing">
                        Register
                    </PrimaryButton>
                    <div>
                        <p class="mt-3 text-center">
                            Already have an account? <Link href="/login" class="link">Login</Link>
                        </p>
                    </div>
                    <div>
                        <p class="mt-3 text-center">
                            <Link href="/forgot-password" class="link">Forgot Password?</Link>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
.link {
  color: #007bff;
  text-decoration: none;
}

.link:hover {
  text-decoration: underline;
}
.link:focus {
  outline: none;
  box-shadow: none;
}
.link:active {
  color: #0056b3;
}
.link:visited {
  color: #0056b3;
}
.link:visited:hover {
  color: #004085;
}
.link:visited:focus {
  color: #004085;
}
.link:visited:active {
  color: #003366;
}
</style>
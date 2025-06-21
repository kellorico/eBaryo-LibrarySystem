<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputField from '@/Components/InputField.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm,Link } from '@inertiajs/vue3';

const formData = useForm({
  email: '',
  password: ''
});

const submitForm = () => {
  formData.post('/login', {
    onFinish: () => formData.reset(),
    preserveScroll: true,
    errorBag: 'login'
  });
};
</script>

<template>
  <GuestLayout>
    <div class="card shadow-sm">
      <div class="card-body p-5">
        <h2 class="card-title text-center mb-4">Login</h2>
        <form @submit.prevent="submitForm">
          <InputField
            id="email"
            label="Email"
            type="email"
            v-model="formData.email"
            required
          />
          <InputField
            id="password"
            label="Password"
            type="password"
            v-model="formData.password"
            required
          />
          <PrimaryButton :disabled="formData.processing" :isLoading="formData.processing">
            Login
          </PrimaryButton>

          <div>
            <p class="mt-3 text-center">
              Don't have an account? <Link href="/register" class="link">Register</Link>
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
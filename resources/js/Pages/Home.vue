<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
</script>

<template>
    <AuthenticatedLayout>
        <div class="container">
            <h1 class="text-center my-5">Welcome to the Home Page</h1>
            <p class="text-center">This is a protected page that only authenticated users can see.</p>
        </div>
    </AuthenticatedLayout>
</template>
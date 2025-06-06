<script setup>
import { Head } from '@inertiajs/vue3';
import Admin from '@/Layouts/Admin.vue';
</script>

<template>
    <Head title="Dashboard" />

    <Admin>
        <template #header>
            <h2 class="h4 fw-semibold text-dark mb-4">
                Dashboard
            </h2>
        </template>

        <div class="py-5">
            <div class="container">
                <div class="card shadow-sm">
                    <div class="card-body text-dark">
                        You're logged in!
                    </div>
                </div>
            </div>
        </div>
    </Admin>
</template>

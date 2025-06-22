<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    totalBooks: Number,
    totalUsers: Number,
    activeUsers: Number
})
</script>

<template>
    <AdminLayout>
         <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm border-primary">
                        <div class="card-body">
                            <h5 class="card-title">Total Books <i class="fa-solid fa-book"></i></h5>
                            <p class="display-6 fw-bold text-primary">{{ totalBooks }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm border-success">
                        <div class="card-body">
                            <h5 class="card-title">Total Users <i class="fa-solid fa-users"></i></h5>
                            <p class="display-6 fw-bold text-success">{{ totalUsers }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm border-info">
                        <div class="card-body">
                            <h5 class="card-title">Active Users <i class="fa fa-active"></i></h5>
                            <p class="display-6 fw-bold text-info">{{ activeUsers }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { computed, onMounted } from "vue";
import Swal from "sweetalert2";

const page = usePage();
const user = computed(() => page.props.auth?.user);

const form = useForm({
    name: "",
    email: "",
    suggestion: "",
});

onMounted(() => {
    if (user.value) {
        form.name = user.value.name;
        form.email = user.value.email;
    }
});

function submit() {
    form.post(route("suggestions.store"), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            Swal.fire({
                icon: "success",
                title: "Thank you!",
                text: "Your suggestion has been submitted.",
            });
        },
        onError: () => {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Failed to submit suggestion.",
            });
        },
    });
}
</script>
<template>
    <Head title="Submit Suggestion" />
    <component :is="user ? AuthenticatedLayout : GuestLayout">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white fw-bold">
                            <i class="fa fa-lightbulb me-2"></i>Submit a
                            Suggestion
                        </div>
                        <div class="card-body">
                            <form @submit.prevent="submit">
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input
                                        v-model="form.name"
                                        class="form-control"
                                        :readonly="!!user"
                                    />
                                    <div
                                        v-if="form.errors.name"
                                        class="text-danger small mt-1"
                                    >
                                        {{ form.errors.name }}
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input
                                        v-model="form.email"
                                        class="form-control"
                                        type="email"
                                        :readonly="!!user"
                                    />
                                    <div
                                        v-if="form.errors.email"
                                        class="text-danger small mt-1"
                                    >
                                        {{ form.errors.email }}
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Suggestion</label>
                                    <textarea
                                        v-model="form.suggestion"
                                        class="form-control"
                                        rows="4"
                                        placeholder="Suggest a new book or feature..."
                                    ></textarea>
                                    <div
                                        v-if="form.errors.suggestion"
                                        class="text-danger small mt-1"
                                    >
                                        {{ form.errors.suggestion }}
                                    </div>
                                </div>
                                <button
                                    class="btn btn-info text-white"
                                    :disabled="form.processing"
                                >
                                    <span
                                        v-if="form.processing"
                                        class="spinner-border spinner-border-sm"
                                    ></span>
                                    <i v-else class="fa fa-paper-plane"></i>
                                    Submit Suggestion
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </component>
</template>

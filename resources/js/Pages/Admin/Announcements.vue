<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { ref } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";

const props = defineProps({
    announcements: Object,
});

const form = useForm({
    title: "",
    content: "",
    type: "update",
    is_urgent: false,
    send_email: false,
    custom_subject: "",
    custom_intro: "",
});

function submit() {
    form.post(route("admin.announcements.store"), {
        onSuccess: () => {
            form.reset();
            Swal.fire({
                icon: "success",
                title: "Success!",
                text: "Announcement posted successfully.",
            });
        },
        onError: () => {
            Swal.fire({
                icon: "error",
                title: "Error!",
                text: "Failed to post announcement.",
            });
        }
    });
}

function deleteAnnouncement(id) {
    if (confirm("Delete this announcement?")) {
        router.delete(route("admin.announcements.destroy", id));
    }
}
</script>
<template>
    <AdminLayout>
        <div class="container">
            <h2 class="fw-bold mb-4">
                <i class="fa fa-bullhorn text-success me-2"></i>Manage
                Announcements
            </h2>
            <form @submit.prevent="submit" class="card p-4 mb-4 shadow-sm">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Title</label>
                        <input
                            v-model="form.title"
                            class="form-control"
                            maxlength="255"
                        />
                        <div v-if="form.errors.title" class="text-danger small mt-1">{{ form.errors.title }}</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Type</label>
                        <select v-model="form.type" class="form-select">
                            <option value="news">News</option>
                            <option value="event">Event</option>
                            <option value="update">Update</option>
                        </select>
                        <div v-if="form.errors.type" class="text-danger small mt-1">{{ form.errors.type }}</div>
                    </div>
                    <div class="col-md-3 d-flex align-items-center gap-3">
                        <div class="form-check">
                            <input
                                v-model="form.is_urgent"
                                class="form-check-input"
                                type="checkbox"
                                id="urgentCheck"
                            />
                            <label class="form-check-label" for="urgentCheck"
                                >Urgent</label
                            >
                        </div>
                        <div class="form-check">
                            <input
                                v-model="form.send_email"
                                class="form-check-input"
                                type="checkbox"
                                id="emailCheck"
                            />
                            <label class="form-check-label" for="emailCheck"
                                >Send Email</label
                            >
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Content</label>
                        <textarea
                            v-model="form.content"
                            class="form-control"
                            rows="3"
                        ></textarea>
                        <div v-if="form.errors.content" class="text-danger small mt-1">{{ form.errors.content }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Custom Email Subject <span class="text-muted small">(optional)</span></label>
                        <input v-model="form.custom_subject" class="form-control" maxlength="255" placeholder="Override default subject..." />
                        <div v-if="form.errors.custom_subject" class="text-danger small mt-1">{{ form.errors.custom_subject }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Custom Email Intro <span class="text-muted small">(optional)</span></label>
                        <input v-model="form.custom_intro" class="form-control" maxlength="255" placeholder="Add a custom intro to the email..." />
                        <div v-if="form.errors.custom_intro" class="text-danger small mt-1">{{ form.errors.custom_intro }}</div>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn btn-success" :disabled="form.processing">
                        <span
                            v-if="form.processing"
                            class="spinner-border spinner-border-sm"
                        ></span>
                        <i v-else class="fa fa-plus"></i> Post Announcement
                    </button>
                </div>
            </form>
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white fw-bold">
                    Recent Announcements
                </div>
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Urgent</th>
                                <th>Email</th>
                                <th>Posted By</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="a in props.announcements.data"
                                :key="a.id"
                            >
                                <td>{{ a.title }}</td>
                                <td>{{ a.type }}</td>
                                <td>
                                    <span
                                        v-if="a.is_urgent"
                                        class="badge bg-danger"
                                        >Urgent</span
                                    >
                                </td>
                                <td>
                                    <span
                                        v-if="a.send_email"
                                        class="badge bg-info"
                                        >Emailed</span
                                    >
                                </td>
                                <td>{{ a.creator?.name || "N/A" }}</td>
                                <td>
                                    {{
                                        new Date(a.created_at).toLocaleString()
                                    }}
                                </td>
                                <td>
                                    <button
                                        class="btn btn-sm btn-danger"
                                        @click="deleteAnnouncement(a.id)"
                                    >
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!props.announcements.data.length">
                                <td colspan="7" class="text-center text-muted">
                                    No announcements yet.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { ref } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";

const props = defineProps({
    suggestions: Object,
});

const showResponseModal = ref(false);
const selectedSuggestion = ref(null);
const responseForm = useForm({
    admin_response: "",
});

function openResponseModal(suggestion) {
    selectedSuggestion.value = suggestion;
    responseForm.admin_response = suggestion.admin_response || "";
    showResponseModal.value = true;
}
function closeResponseModal() {
    showResponseModal.value = false;
    selectedSuggestion.value = null;
    responseForm.reset();
}
function submitResponse() {
    if (!selectedSuggestion.value) return;
    responseForm.post(route("admin.suggestions", selectedSuggestion.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeResponseModal();
            Swal.fire({ icon: "success", title: "Response sent!" });
        },
        onError: () => {
            Swal.fire({ icon: "error", title: "Failed to send response." });
        },
    });
}
function updateStatus(suggestion, status) {
    router.post(
        route("admin.suggestions", suggestion.id),
        { status },
        {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({ icon: "success", title: `Suggestion ${status}.` });
            },
            onError: () => {
                Swal.fire({ icon: "error", title: "Failed to update status." });
            },
        }
    );
}
</script>
<template>
    <AdminLayout>
        <Head title="Suggestions" />
        <div class="container">
            <h2 class="fw-bold mb-4">
                <i class="fa fa-lightbulb text-info me-2"></i>User Suggestions
            </h2>
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white fw-bold">
                    Suggestions
                </div>
                <div class="table-responsive">
                    <table class="table table-striped mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Email</th>
                                <th>Suggestion</th>
                                <th>Status</th>
                                <th>Admin Response</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(s, i) in props.suggestions.data"
                                :key="s.id"
                            >
                                <td>{{ i + 1 }}</td>
                                <td>{{ s.user?.name || s.name }}</td>
                                <td>{{ s.email }}</td>
                                <td>{{ s.suggestion }}</td>
                                <td>
                                    <span
                                        :class="{
                                            'badge bg-secondary':
                                                s.status === 'pending',
                                            'badge bg-success':
                                                s.status === 'approved',
                                            'badge bg-danger':
                                                s.status === 'rejected',
                                        }"
                                        >{{
                                            s.status.charAt(0).toUpperCase() +
                                            s.status.slice(1)
                                        }}</span
                                    >
                                </td>
                                <td>{{ s.admin_response || "-" }}</td>
                                <td>
                                    <button
                                        v-if="s.status === 'pending'"
                                        class="btn btn-sm btn-success me-1"
                                        @click="updateStatus(s, 'approved')"
                                    >
                                        <i class="fa fa-check"></i>
                                    </button>
                                    <button
                                        v-if="s.status === 'pending'"
                                        class="btn btn-sm btn-danger me-1"
                                        @click="updateStatus(s, 'rejected')"
                                    >
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <button
                                        class="btn btn-sm btn-primary"
                                        @click="openResponseModal(s)"
                                    >
                                        <i class="fa fa-reply"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!props.suggestions.data.length">
                                <td colspan="7" class="text-center text-muted">
                                    No suggestions yet.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Response Modal -->
            <div
                v-if="showResponseModal"
                class="modal-backdrop"
                @click.self="closeResponseModal"
                style="
                    z-index: 2000;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100vw;
                    height: 100vh;
                    background: rgba(0, 0, 0, 0.3);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                "
            >
                <div
                    class="modal-dialog"
                    style="
                        background: #fff;
                        border-radius: 8px;
                        max-width: 400px;
                        width: 100%;
                        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.18);
                    "
                >
                    <div class="modal-content p-3">
                        <div
                            class="modal-header d-flex align-items-center justify-content-between"
                        >
                            <h5 class="modal-title">
                                <i class="fa fa-reply text-primary me-2"></i
                                >Respond to Suggestion
                            </h5>
                            <button
                                type="button"
                                class="btn-close"
                                @click="closeResponseModal"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <textarea
                                v-model="responseForm.admin_response"
                                class="form-control"
                                rows="4"
                                placeholder="Type your response..."
                            ></textarea>
                        </div>
                        <div class="modal-footer border-0">
                            <button
                                class="btn btn-secondary"
                                @click="closeResponseModal"
                            >
                                Cancel
                            </button>
                            <button
                                class="btn btn-primary"
                                @click="submitResponse"
                                :disabled="responseForm.processing"
                            >
                                <span
                                    v-if="responseForm.processing"
                                    class="spinner-border spinner-border-sm"
                                ></span>
                                <i v-else class="fa fa-paper-plane"></i> Send
                                Response
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import Swal from "sweetalert2";

const props = defineProps({
  challenges: Object,
});

const form = useForm({
  title: '',
  description: '',
  start_date: '',
  end_date: '',
  target_books: 1,
  send_email: false,
  custom_subject: '',
  custom_intro: '',
});

function submit() {
  form.post(route('admin.challenges.store'), {
    onSuccess: () => {
      form.reset();
      Swal.fire({
        icon: "success",
        title: "Success!",
        text: "Reading challenge posted successfully.",
      });
    },
    onError: () => {
      Swal.fire({
        icon: "error",
        title: "Error!",
        text: "Failed to post reading challenge.",
      });
    }
  });
}

function deleteChallenge(id) {
  Swal.fire({
    title: 'Are you sure?',
    text: 'This will delete the challenge for all users.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!'
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('admin.challenges.destroy', id));
    }
  });
}
</script>
<template>
  <AdminLayout>
    <div class="container">
      <h2 class="fw-bold mb-4"><i class="fa fa-trophy text-warning me-2"></i>Manage Reading Challenges</h2>
      <form @submit.prevent="submit" class="card p-4 mb-4 shadow-sm">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Title</label>
            <input v-model="form.title" class="form-control" maxlength="255" />
            <div v-if="form.errors.title" class="text-danger small mt-1">{{ form.errors.title }}</div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Target Books</label>
            <input v-model="form.target_books" type="number" min="1" class="form-control" />
            <div v-if="form.errors.target_books" class="text-danger small mt-1">{{ form.errors.target_books }}</div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Start Date</label>
            <input v-model="form.start_date" type="date" class="form-control" />
            <div v-if="form.errors.start_date" class="text-danger small mt-1">{{ form.errors.start_date }}</div>
          </div>
          <div class="col-md-6">
            <label class="form-label">End Date</label>
            <input v-model="form.end_date" type="date" class="form-control" />
            <div v-if="form.errors.end_date" class="text-danger small mt-1">{{ form.errors.end_date }}</div>
          </div>
          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea v-model="form.description" class="form-control" rows="2"></textarea>
            <div v-if="form.errors.description" class="text-danger small mt-1">{{ form.errors.description }}</div>
          </div>
          <div class="col-md-3 d-flex align-items-center gap-3">
            <div class="form-check">
              <input v-model="form.send_email" class="form-check-input" type="checkbox" id="emailCheck" />
              <label class="form-check-label" for="emailCheck">Send Email</label>
            </div>
          </div>
          <div v-if="form.send_email" class="col-md-6">
            <label class="form-label">Custom Email Subject <span class="text-muted small">(optional)</span></label>
            <input v-model="form.custom_subject" class="form-control" maxlength="255" placeholder="Override default subject..." />
            <div v-if="form.errors.custom_subject" class="text-danger small mt-1">{{ form.errors.custom_subject }}</div>
          </div>
          <div v-if="form.send_email" class="col-md-6">
            <label class="form-label">Custom Email Intro <span class="text-muted small">(optional)</span></label>
            <input v-model="form.custom_intro" class="form-control" maxlength="255" placeholder="Add a custom intro to the email..." />
            <div v-if="form.errors.custom_intro" class="text-danger small mt-1">{{ form.errors.custom_intro }}</div>
          </div>
        </div>
        <div class="mt-3">
          <button class="btn btn-warning text-white" :disabled="form.processing">
            <span v-if="form.processing" class="spinner-border spinner-border-sm"></span>
            <i v-else class="fa fa-plus"></i> Create Challenge
          </button>
        </div>
      </form>
      <div class="card shadow-sm mt-4">
        <div class="card-header bg-warning text-white fw-bold d-flex align-items-center gap-2">
          <i class="fa fa-list"></i> Current & Upcoming Challenges
        </div>
        <div class="table-responsive">
          <table class="table table-striped mb-0 align-middle">
            <thead>
              <tr>
                <th>#</th>
                <th>Title</th>
                <th>Target</th>
                <th>Start</th>
                <th>End</th>
                <th>Status</th>
                <th>Created By</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(c, i) in props.challenges.data" :key="c.id">
                <td>{{ i + 1 }}</td>
                <td>{{ c.title }}</td>
                <td>{{ c.target_books }}</td>
                <td>{{ new Date(c.start_date).toLocaleDateString() }}</td>
                <td>{{ new Date(c.end_date).toLocaleDateString() }}</td>
                <td>
                  <span v-if="new Date() < new Date(c.start_date)" class="badge bg-secondary">Upcoming</span>
                  <span v-else-if="new Date() > new Date(c.end_date)" class="badge bg-danger">Ended</span>
                  <span v-else class="badge bg-success">Ongoing</span>
                </td>
                <td>{{ c.creator?.name || 'N/A' }}</td>
                <td>
                  <button class="btn btn-sm btn-danger" @click="deleteChallenge(c.id)" title="Delete Challenge"><i class="fa fa-trash"></i></button>
                </td>
              </tr>
              <tr v-if="!props.challenges.data.length">
                <td colspan="8" class="text-center text-muted">No challenges yet.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template> 
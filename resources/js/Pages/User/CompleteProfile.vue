<script setup>
import { ref, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

const page = usePage();
const user = computed(() => page.props.user || {});

const form = ref({
  barangay: user.value.barangay || '',
  is_student: user.value.is_student === true || user.value.is_student === '1',
  school_name: user.value.school_name || '',
});

const toast = ref({ show: false, message: '', type: 'success' });
function showToast(message, type = 'success') {
  toast.value = { show: true, message, type };
  setTimeout(() => { toast.value.show = false; }, 3000);
}

function submit() {
  router.post('/user/profile/complete', {
    barangay: form.value.barangay,
    is_student: form.value.is_student ? 1 : 0,
    school_name: form.value.is_student ? form.value.school_name : '',
  }, {
    onSuccess: () => {
      showToast('Profile completed!', 'success');
      setTimeout(() => router.visit('/home'), 1000);
    },
    onError: (errors) => {
      showToast(errors && Object.values(errors)[0] ? Object.values(errors)[0] : 'Failed to complete profile.', 'error');
    },
  });
}

function verifyEmail() {
  router.post('/email/verification-notification', {}, {
    onSuccess: () => {
      showToast('Verification email sent!', 'success');
    },
    onError: () => {
      showToast('Failed to send verification email.', 'error');
    },
  });
}
function skip() {
  router.visit('/home');
}
</script>
<template>
  <UserLayout>
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
          <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-success text-white fw-bold fs-5">
              Complete Your Profile
            </div>
            <div class="card-body p-4">
              <form @submit.prevent="submit">
                <div class="mb-3">
                  <label class="form-label">Barangay</label>
                  <input v-model="form.barangay" class="form-control" required />
                </div>
                <div class="mb-3">
                  <label class="form-label">Are you a student?</label>
                  <select v-model="form.is_student" class="form-select">
                    <option :value="true">Yes</option>
                    <option :value="false">No</option>
                  </select>
                </div>
                <div class="mb-3" v-if="form.is_student === true || form.is_student === 'true'">
                  <label class="form-label">School Name</label>
                  <input v-model="form.school_name" class="form-control" />
                </div>
                <div class="d-flex gap-2 mt-4">
                  <button class="btn btn-success flex-grow-1" type="submit">Save & Continue</button>
                  <button class="btn btn-outline-primary flex-grow-1" type="button" @click="verifyEmail">Verify Email Now</button>
                  <button class="btn btn-secondary flex-grow-1" type="button" @click="skip">Skip for Now</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div
      v-if="toast.show"
      :class="[
        'toast show align-items-center position-fixed bottom-0 end-0 m-4',
        toast.type === 'success' ? 'bg-success text-white' : 'bg-danger text-white',
      ]"
      style="z-index: 9999; min-width: 220px"
      role="alert"
    >
      <div class="d-flex">
        <div class="toast-body">
          <i :class="toast.type === 'success' ? 'fa fa-check-circle me-2' : 'fa fa-times-circle me-2'"></i>
          {{ toast.message }}
        </div>
        <button
          type="button"
          class="btn-close btn-close-white me-2 m-auto"
          @click="toast.show = false"
        ></button>
      </div>
    </div>
  </UserLayout>
</template>
<style scoped>
.card-header {
  font-size: 1.2rem;
}
</style> 
<script setup>
import { ref, computed, inject, onMounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

const page = usePage();
const user = computed(() => page.props.auth.user || {});
const stats = computed(() => page.props.stats || {});

const showEditModal = ref(false);
const avatarInput = ref(null);

const form = ref({
  name: '',
  email: '',
  barangay: '',
  is_student: false,
  school_name: '',
});

const passwordForm = ref({
  current: '',
  new: '',
  confirm: '',
});

// Local toast state
const toast = ref({ show: false, message: '', type: 'success' });

function showToast(message, type = 'success') {
  toast.value = { show: true, message, type };
  setTimeout(() => { toast.value.show = false; }, 3000);
}

function openEditModal() {
  form.value = {
    name: user.value.name || '',
    email: user.value.email || '',
    barangay: user.value.barangay || '',
    is_student: user.value.is_student || false,
    school_name: user.value.school_name || '',
  };
  showEditModal.value = true;
}
function closeEditModal() {
  showEditModal.value = false;
}
function submitEditProfile() {
  router.put('/user/profile', {
    ...form.value,
    is_student: form.value.is_student === true || form.value.is_student === 'true' ? true : false,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      showToast('Profile updated!', 'success');
      showEditModal.value = false;
    },
    onError: (errors) => {
      showToast(errors && Object.values(errors)[0] ? Object.values(errors)[0] : 'Failed to update profile.', 'error');
    },
  });
}
function submitChangePassword() {
  if (passwordForm.value.new !== passwordForm.value.confirm) {
    showToast('Passwords do not match.', 'error');
    return;
  }
  router.put('/user/profile/password', {
    current: passwordForm.value.current,
    new: passwordForm.value.new,
    new_confirmation: passwordForm.value.confirm,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      showToast('Password changed!', 'success');
      passwordForm.value = { current: '', new: '', confirm: '' };
    },
    onError: (errors) => {
      showToast(errors && Object.values(errors)[0] ? Object.values(errors)[0] : 'Failed to change password.', 'error');
    },
  });
}
function triggerAvatarUpload() {
  avatarInput.value && avatarInput.value.click();
}
function handleAvatarChange(e) {
  const file = e.target.files[0];
  if (file) {
    const formData = new FormData();
    formData.append('avatar', file);
    router.post('/user/profile/avatar', formData, {
      forceFormData: true,
      onSuccess: () => {
        showToast('Avatar updated!', 'success');
      },
      onError: (errors) => {
        showToast(errors && Object.values(errors)[0] ? Object.values(errors)[0] : 'Failed to upload avatar.', 'error');
      },
    });
  }
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

onMounted(() => {
  if (page.props.status === 'verified') {
    showToast('Your email is now verified!', 'success');
  }
});
</script>
<template>
  <UserLayout>
    <div class="container py-4">
      <h2 class="fw-bold mb-4">
        <i class="fa fa-user-circle text-success me-2"></i>My Profile
      </h2>
      <div class="card shadow-sm mb-4">
        <div class="card-body d-flex align-items-center gap-4">
          <div>
            <img :src="user.avatar ? '/storage/' + user.avatar : '/assets/images/image.png'" alt="Avatar" class="rounded-circle" style="width: 96px; height: 96px; object-fit: cover; cursor:pointer;" @click="triggerAvatarUpload" />
            <input ref="avatarInput" type="file" accept="image/*" class="d-none" @change="handleAvatarChange" />
            <div class="text-center mt-2">
              <button class="btn btn-sm btn-outline-secondary" @click="triggerAvatarUpload">Change Avatar</button>
            </div>
          </div>
          <div>
            <h4 class="mb-1">{{ user.name }}</h4>
            <div class="mb-1"><i class="fa fa-envelope me-1"></i> {{ user.email }}</div>
            <div class="mb-1">
              <i class="fa fa-envelope-open me-1"></i> Email Status:
              <span v-if="user.email_verified_at" class="badge bg-success">Verified</span>
              <span v-else class="badge bg-danger">Not Verified</span>
              <button v-if="!user.email_verified_at" class="btn btn-sm btn-outline-primary ms-2" @click="verifyEmail">Verify Email</button>
            </div>
            <div class="mb-1">
              <i class="fa fa-map-marker-alt me-1"></i> Barangay: <span>{{ user.barangay || 'N/A' }}</span>
            </div>
            <div class="mb-1">
              <i class="fa fa-user-graduate me-1"></i> Student: 
              <span class="badge" :class="user.is_student ? 'bg-success' : 'bg-danger'">{{ user.is_student ? 'Yes' : 'No' }}</span>
            </div>
            <div class="mb-1" v-if="user.is_student">
              <i class="fa fa-school me-1"></i> School: <span>{{ user.school_name || 'N/A' }}</span>
            </div>
            <div class="mb-1">
              <i class="fa fa-check-circle me-1"></i> Verified: 
              <span class="badge" :class="user.verified ? 'bg-success' : 'bg-danger'">{{ user.verified ? 'Verified' : 'Not Verified' }}</span>
            </div>
            <button class="btn btn-outline-primary btn-sm mt-2" @click="openEditModal">Edit Profile</button>
          </div>
        </div>
      </div>
      <!-- User Stats Summary -->
      <div class="row mb-4 g-3">
        <div class="col-12 col-md-4">
          <div class="card shadow-sm h-100">
            <div class="card-body text-center">
              <div class="fw-bold fs-4 mb-1">{{ stats.bookmarks ?? 0 }}</div>
              <div class="text-muted">Saved Books</div>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="card shadow-sm h-100">
            <div class="card-body text-center">
              <div class="fw-bold fs-4 mb-1">{{ stats.readingProgress ?? 0 }}</div>
              <div class="text-muted">Books In Progress</div>
            </div>
          </div>
        </div>
      </div>
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-light fw-bold">Change Password</div>
        <div class="card-body">
          <form @submit.prevent="submitChangePassword">
            <div class="mb-3">
              <label class="form-label">Current Password</label>
              <input v-model="passwordForm.current" type="password" class="form-control" required />
            </div>
            <div class="mb-3">
              <label class="form-label">New Password</label>
              <input v-model="passwordForm.new" type="password" class="form-control" required />
            </div>
            <div class="mb-3">
              <label class="form-label">Confirm New Password</label>
              <input v-model="passwordForm.confirm" type="password" class="form-control" required />
            </div>
            <button class="btn btn-success">Change Password</button>
          </form>
        </div>
      </div>
      <div v-if="showEditModal" class="modal-backdrop" style="z-index:2000;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.3);display:flex;align-items:center;justify-content:center;">
        <div class="modal-dialog" style="background:#fff;border-radius:8px;max-width:400px;width:100%;box-shadow:0 8px 32px rgba(0,0,0,0.18);">
          <div class="modal-content p-3">
            <div class="modal-header d-flex align-items-center justify-content-between">
              <h5 class="modal-title">Edit Profile</h5>
              <button type="button" class="btn-close" @click="closeEditModal"></button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="submitEditProfile">
                <div class="mb-3">
                  <label class="form-label">Name</label>
                  <input v-model="form.name" class="form-control" required />
                  <div v-if="form.errors && form.errors.name" class="text-danger small mt-1">{{ form.errors.name }}</div>
                </div>
                <div class="mb-3">
                  <label class="form-label">Email</label>
                  <input v-model="form.email" class="form-control" type="email" required />
                  <div v-if="form.errors && form.errors.email" class="text-danger small mt-1">{{ form.errors.email }}</div>
                </div>
                <div class="mb-3">
                  <label class="form-label">Barangay</label>
                  <input v-model="form.barangay" class="form-control" />
                  <div v-if="form.errors && form.errors.barangay" class="text-danger small mt-1">{{ form.errors.barangay }}</div>
                </div>
                <div class="mb-3">
                  <label class="form-label">Are you a student?</label>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="studentYes" value="true" v-model="form.is_student" />
                    <label class="form-check-label" for="studentYes">Yes</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="studentNo" value="false" v-model="form.is_student" />
                    <label class="form-check-label" for="studentNo">No</label>
                  </div>
                  <div v-if="form.errors && form.errors.is_student" class="text-danger small mt-1">{{ form.errors.is_student }}</div>
                </div>
                <div class="mb-3" v-if="form.is_student === true || form.is_student === 'true'">
                  <label class="form-label">School Name</label>
                  <input v-model="form.school_name" class="form-control" />
                  <div v-if="form.errors && form.errors.school_name" class="text-danger small mt-1">{{ form.errors.school_name }}</div>
                </div>
                <button class="btn btn-primary w-100">Save Changes</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </UserLayout>
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
</template>
<style scoped>
.toast-notification {
  position: fixed;
  top: 30px;
  right: 30px;
  color: #fff;
  padding: 16px 32px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
  z-index: 9999;
  font-weight: bold;
  font-size: 1rem;
}
.bg-success { background: #19a061; }
.bg-danger { background: #d33; }
</style> 
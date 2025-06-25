<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Swal from 'sweetalert2';

const props = defineProps({
  archives: Array,
});

const showUploadModal = ref(false);
const uploadForm = ref({
  title: '',
  description: '',
  file: null,
  type: 'document',
  category: '',
  is_public: false,
  errors: {},
});

const selectedCategory = ref('');
const filteredArchives = computed(() => {
  if (!selectedCategory.value) return props.archives;
  return props.archives.filter(a => a.category === selectedCategory.value);
});

const resetForm = () => {
  uploadForm.value = {
    title: '',
    description: '',
    file: null,
    type: 'document',
    category: '',
    is_public: false,
    errors: {},
  };
};

const openUploadModal = () => {
  resetForm();
  showUploadModal.value = true;
};
const closeUploadModal = () => {
  showUploadModal.value = false;
};

const handleFileChange = (e) => {
  uploadForm.value.file = e.target.files[0];
};

const handleUpload = () => {
  uploadForm.value.errors = {};
  const formData = new FormData();
  formData.append('title', uploadForm.value.title);
  formData.append('description', uploadForm.value.description);
  formData.append('file', uploadForm.value.file);
  formData.append('type', uploadForm.value.type);
  formData.append('category', uploadForm.value.category);
  formData.append('is_public', uploadForm.value.is_public ? 1 : 0);
  router.post(route('archive'), formData, {
    forceFormData: true,
    onSuccess: () => {
      closeUploadModal();
      Swal.fire('Success', 'Archive uploaded successfully!', 'success');
    },
    onError: (errors) => {
      uploadForm.value.errors = errors;
    },
    preserveScroll: true,
  });
};

const handleDelete = (archive) => {
  Swal.fire({
    title: 'Delete Archive?',
    text: `Are you sure you want to delete "${archive.title}"?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#28a745',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!'
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route('archive', archive.id), {
        onSuccess: () => {
          Swal.fire('Deleted!', 'Archive has been deleted.', 'success');
        },
        preserveScroll: true,
      });
    }
  });
};

const handleDownload = (archive) => {
  window.open(route('archive', archive.id), '_blank');
};

const fileTypeIcon = (type) => {
  if (type === 'photo') return 'fas fa-image';
  if (type === 'record') return 'fas fa-landmark';
  return 'fas fa-file-alt';
};
</script>

<template>
  <AdminLayout>
    <div class="container py-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">
          <i class="fas fa-archive me-2 text-success"></i> Digital Archive
        </h2>
        <button class="btn btn-success" @click="openUploadModal">
          <i class="fas fa-upload me-2"></i> Upload New
        </button>
      </div>
      <div class="mb-3 d-flex align-items-center gap-3">
        <label class="form-label mb-0">Filter by Category:</label>
        <select v-model="selectedCategory" class="form-select w-auto">
          <option value="">All</option>
          <option value="Resolution">Resolution</option>
          <option value="Photo">Photo</option>
          <option value="Event">Event</option>
          <option value="History">History</option>
          <option value="Misc">Misc</option>
        </select>
      </div>
      <div class="card shadow-sm">
        <div class="card-body p-0">
          <div v-if="filteredArchives.length === 0" class="text-center py-5">
            <i class="fas fa-archive fa-3x text-muted mb-3"></i>
            <h5 class="fw-semibold">No archives yet</h5>
            <p class="text-muted">Upload barangay documents, photos, or records for the community.</p>
          </div>
          <div v-else class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-success">
                <tr>
                  <th>Type</th>
                  <th>Title</th>
                  <th>Description</th>
                  <th>Category</th>
                  <th>Public</th>
                  <th>Uploaded By</th>
                  <th>Date</th>
                  <th>Preview</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="archive in filteredArchives" :key="archive.id">
                  <td><i :class="fileTypeIcon(archive.type)" class="me-2"></i> {{ archive.type }}</td>
                  <td>{{ archive.title }}</td>
                  <td>{{ archive.description }}</td>
                  <td>{{ archive.category || '-' }}</td>
                  <td>
                    <span v-if="archive.is_public" class="badge bg-success">Yes</span>
                    <span v-else class="badge bg-secondary">No</span>
                  </td>
                  <td>{{ archive.uploader?.name || 'Unknown' }}</td>
                  <td>{{ new Date(archive.created_at).toLocaleString() }}</td>
                  <td>
                    <template v-if="archive.file_path && archive.file_path.match(/\.(jpg|jpeg|png)$/i)">
                      <img :src="`/storage/${archive.file_path}`" alt="Preview" style="width:40px;height:40px;object-fit:cover;border-radius:4px;" />
                    </template>
                    <template v-else-if="archive.file_path && archive.file_path.match(/\.pdf$/i)">
                      <a :href="`/storage/${archive.file_path}`" target="_blank" class="btn btn-outline-primary btn-sm">Preview</a>
                    </template>
                    <template v-else>
                      <i class="fas fa-file-alt text-muted"></i>
                    </template>
                  </td>
                  <td>
                    <button class="btn btn-outline-success btn-sm me-2" @click="handleDownload(archive)">
                      <i class="fas fa-download"></i>
                    </button>
                    <button class="btn btn-outline-danger btn-sm" @click="handleDelete(archive)">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Upload Modal -->
    <div v-if="showUploadModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.3);">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><i class="fas fa-upload me-2 text-success"></i> Upload Archive</h5>
            <button type="button" class="btn-close" @click="closeUploadModal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Title</label>
              <input v-model="uploadForm.title" type="text" class="form-control" placeholder="Document title" />
              <div v-if="uploadForm.errors.title" class="text-danger small">{{ uploadForm.errors.title }}</div>
            </div>
            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea v-model="uploadForm.description" class="form-control" placeholder="Description (optional)"></textarea>
              <div v-if="uploadForm.errors.description" class="text-danger small">{{ uploadForm.errors.description }}</div>
            </div>
            <div class="mb-3">
              <label class="form-label">Type</label>
              <select v-model="uploadForm.type" class="form-select">
                <option value="document">Document</option>
                <option value="photo">Photo</option>
                <option value="record">Historical Record</option>
              </select>
              <div v-if="uploadForm.errors.type" class="text-danger small">{{ uploadForm.errors.type }}</div>
            </div>
            <div class="mb-3">
              <label class="form-label">Category</label>
              <select v-model="uploadForm.category" class="form-select">
                <option value="">Select Category</option>
                <option value="Resolution">Resolution</option>
                <option value="Photo">Photo</option>
                <option value="Event">Event</option>
                <option value="History">History</option>
                <option value="Misc">Misc</option>
              </select>
              <div v-if="uploadForm.errors.category" class="text-danger small">{{ uploadForm.errors.category }}</div>
            </div>
            <div class="mb-3">
              <label class="form-label">File</label>
              <input type="file" class="form-control" @change="handleFileChange" accept=".pdf,.jpg,.jpeg,.png,.docx,.xlsx,.zip" />
              <div v-if="uploadForm.errors.file" class="text-danger small">{{ uploadForm.errors.file }}</div>
            </div>
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" v-model="uploadForm.is_public" id="isPublicCheck" />
              <label class="form-check-label" for="isPublicCheck">Make Public (visible to all users)</label>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" @click="closeUploadModal">Cancel</button>
            <button class="btn btn-success" @click="handleUpload">Upload</button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
.table-success th {
  background: #e8f5e8;
  color: #28a745;
}
.btn-success, .btn-outline-success {
  border-radius: 6px;
  font-weight: 500;
}
.btn-success {
  background: #28a745;
  border: none;
}
.btn-success:hover {
  background: #218838;
}
.btn-outline-success {
  border: 1px solid #28a745;
  color: #28a745;
}
.btn-outline-success:hover {
  background: #e8f5e8;
}
</style> 
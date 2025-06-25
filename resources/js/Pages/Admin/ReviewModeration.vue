<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
  reviews: Object, // paginated
  pendingReviews: Array,
  reportedReviews: Array,
  filters: Object,
});

const selected = ref([]);
const selectAll = ref(false);
const showModal = ref(false);
const modalReview = ref(null);
const modalNote = ref('');

// Search and filter states
const search = ref(props.filters.search || '');
const searchField = ref(props.filters.search_field || 'all');
const status = ref(props.filters.status || '');
const reported = ref(props.filters.reported || '');
const perPage = ref(props.filters.per_page || 10);
const dateFrom = ref(props.filters.date_from || '');
const dateTo = ref(props.filters.date_to || '');
const showAdvancedSearch = ref(false);
const searchHistory = ref([]);
const searchTimeout = ref(null);
const searchBarFocused = ref(false);

// Load search history from localStorage
onMounted(() => {
  const saved = localStorage.getItem('reviewSearchHistory');
  if (saved) {
    searchHistory.value = JSON.parse(saved);
  }
});

// Save search to history
const saveToHistory = (searchTerm) => {
  if (searchTerm && !searchHistory.value.includes(searchTerm)) {
    searchHistory.value.unshift(searchTerm);
    if (searchHistory.value.length > 10) {
      searchHistory.value.pop();
    }
    localStorage.setItem('reviewSearchHistory', JSON.stringify(searchHistory.value));
  }
};

// Debounced search
watch(search, (newVal) => {
  if (searchTimeout.value) clearTimeout(searchTimeout.value);
  searchTimeout.value = setTimeout(() => {
    if (newVal) saveToHistory(newVal);
    applyFilters();
  }, 500);
});

const openModal = (review) => {
  modalReview.value = review;
  modalNote.value = review.admin_note || '';
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  modalReview.value = null;
  modalNote.value = '';
};

const handleAction = (id, action, note = null) => {
  let url = '';
  let confirmText = '';
  if (action === 'approve') {
    url = `/reviews/${id}/approve`;
    confirmText = 'Approve this review?';
  } else if (action === 'reject') {
    url = `/reviews/${id}/reject`;
    confirmText = 'Reject this review?';
  } else if (action === 'dismiss') {
    url = `/reviews/${id}/dismiss-report`;
    confirmText = 'Dismiss this report?';
  }
  Swal.fire({
    title: 'Are you sure?',
    text: confirmText,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes',
    input: action === 'reject' ? 'text' : undefined,
    inputLabel: action === 'reject' ? 'Reason for rejection (optional)' : undefined,
    inputValue: note || '',
  }).then(result => {
    if (result.isConfirmed) {
      router.post(url, { note: result.value }, {
        preserveScroll: true,
        onSuccess: () => router.reload({ only: ['reviews'] })
      });
    }
  });
};

const handleModalAction = (action) => {
  if (!modalReview.value) return;
  let url = '';
  let confirmText = '';
  if (action === 'approve') {
    url = `/reviews/${modalReview.value.id}/approve`;
    confirmText = 'Approve this review?';
  } else if (action === 'reject') {
    url = `/reviews/${modalReview.value.id}/reject`;
    confirmText = 'Reject this review?';
  } else if (action === 'dismiss') {
    url = `/reviews/${modalReview.value.id}/dismiss-report`;
    confirmText = 'Dismiss this report?';
  }
  Swal.fire({
    title: 'Are you sure?',
    text: confirmText,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes',
    input: action === 'reject' ? 'text' : undefined,
    inputLabel: action === 'reject' ? 'Reason for rejection (optional)' : undefined,
    inputValue: modalNote.value,
  }).then(result => {
    if (result.isConfirmed) {
      router.post(url, { note: result.value }, {
        preserveScroll: true,
        onSuccess: () => {
          closeModal();
          router.reload({ only: ['reviews'] });
        }
      });
    }
  });
};

const handleBulkAction = (action) => {
  if (selected.value.length === 0) return;
  let confirmText = '';
  if (action === 'approve') confirmText = 'Approve selected reviews?';
  if (action === 'reject') confirmText = 'Reject selected reviews?';
  if (action === 'dismiss') confirmText = 'Dismiss reports for selected reviews?';
  Swal.fire({
    title: 'Are you sure?',
    text: confirmText,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes',
    input: action === 'reject' ? 'text' : undefined,
    inputLabel: action === 'reject' ? 'Reason for rejection (optional)' : undefined,
  }).then(result => {
    if (result.isConfirmed) {
      router.post('/reviews/bulk-action', {
        ids: selected.value,
        action,
        note: result.value,
      }, {
        preserveScroll: true,
        onSuccess: () => {
          selected.value = [];
          selectAll.value = false;
          router.reload({ only: ['reviews'] });
        }
      });
    }
  });
};

const toggleSelectAll = () => {
  if (selectAll.value) {
    selected.value = props.reviews.data.map(r => r.id);
  } else {
    selected.value = [];
  }
};

const handleSelect = (id) => {
  if (selected.value.includes(id)) {
    selected.value = selected.value.filter(i => i !== id);
  } else {
    selected.value.push(id);
  }
};

const applyFilters = () => {
  router.get('/reviews/moderation', {
    search: search.value,
    search_field: searchField.value,
    status: status.value,
    reported: reported.value,
    per_page: perPage.value,
    date_from: dateFrom.value,
    date_to: dateTo.value,
  }, { preserveScroll: true });
};

const clearSearch = () => {
  search.value = '';
  searchField.value = 'all';
};

const resetFilters = () => {
  search.value = '';
  searchField.value = 'all';
  status.value = '';
  reported.value = '';
  perPage.value = 10;
  dateFrom.value = '';
  dateTo.value = '';
  applyFilters();
};

const useHistoryItem = (item) => {
  search.value = item;
  searchField.value = 'all';
};

const highlightText = (text, searchTerm) => {
  if (!searchTerm || !text) return text;
  const regex = new RegExp(`(${searchTerm})`, 'gi');
  return text.replace(regex, '<mark>$1</mark>');
};

const exportToCSV = () => {
  const headers = ['User', 'Book', 'Rating', 'Review', 'Status', 'Reported', 'Report Reason', 'Admin Note', 'Created At'];
  const rows = props.reviews.data.map(review => [
    review.user?.name || 'Unknown',
    review.book?.title || 'Unknown',
    review.rating,
    review.review,
    review.status,
    review.reported ? 'Yes' : 'No',
    review.report_reason || '',
    review.admin_note || '',
    new Date(review.created_at).toLocaleString(),
  ]);
  
  const csv = [headers.join(',')].concat(
    rows.map(row => headers.map(h => '"' + (row[headers.indexOf(h)] || '') + '"').join(','))
  ).join('\n');
  
  const blob = new Blob([csv], { type: 'text/csv' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = `reviews-${new Date().toISOString().split('T')[0]}.csv`;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};

const removeHistoryItem = (item) => {
  searchHistory.value = searchHistory.value.filter(i => i !== item);
  localStorage.setItem('reviewSearchHistory', JSON.stringify(searchHistory.value));
};

const clearHistory = () => {
  searchHistory.value = [];
  localStorage.removeItem('reviewSearchHistory');
  searchBarFocused.value = false;
  setTimeout(() => { searchBarFocused.value = true; }, 10);
};

const blurSearchBar = () => {
  setTimeout(() => {
    searchBarFocused.value = false;
  }, 200);
};
</script>

<template>
  <AdminLayout title="Review Moderation">
    <div class="container">
      <h2 class="mb-4">Review Moderation</h2>
      
      <!-- Search Bar -->
      <div class="card mb-3">
        <div class="card-body">
          <div class="row align-items-end">
            <div class="col-md-4">
              <label class="form-label">Search</label>
              <div class="input-group">
                <input 
                  v-model="search" 
                  class="form-control" 
                  placeholder="Search reviews..." 
                  :class="{ 'is-loading': searchTimeout }"
                  @focus="searchBarFocused = true"
                  @blur="blurSearchBar"
                />
                <button 
                  v-if="search" 
                  class="btn btn-outline-secondary" 
                  @click="clearSearch"
                  title="Clear search"
                >
                  <i class="fas fa-times"></i>
                </button>
              </div>
              <!-- Search History Dropdown -->
              <div v-if="searchBarFocused && search && searchHistory.length > 0" class="dropdown-menu show" style="position: absolute; z-index: 1000;">
                <div class="dropdown-header d-flex justify-content-between align-items-center">
                  <span>Recent Searches</span>
                  <button class="btn btn-sm btn-link text-danger p-0" @click="clearHistory" title="Clear All"><i class="fas fa-trash"></i></button>
                </div>
                <a 
                  v-for="item in searchHistory.slice(0, 5)" 
                  :key="item"
                  class="dropdown-item d-flex justify-content-between align-items-center" 
                  href="#" 
                  @click.prevent="useHistoryItem(item)"
                >
                  <span><i class="fas fa-history me-2"></i>{{ item }}</span>
                  <button class="btn btn-xs btn-link text-danger p-0 ms-2" @click.stop="removeHistoryItem(item)" title="Remove"><i class="fas fa-times"></i></button>
                </a>
              </div>
            </div>
            <div class="col-md-2">
              <label class="form-label">Search In</label>
              <select v-model="searchField" class="form-select" @change="applyFilters">
                <option value="all">All Fields</option>
                <option value="user">User Name</option>
                <option value="book">Book Title</option>
                <option value="review">Review Content</option>
                <option value="report_reason">Report Reason</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">Status</label>
              <select v-model="status" class="form-select" @change="applyFilters">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">Reported</label>
              <select v-model="reported" class="form-select" @change="applyFilters">
                <option value="">All</option>
                <option value="1">Reported</option>
                <option value="0">Not Reported</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">Per Page</label>
              <select v-model="perPage" class="form-select" @change="applyFilters">
                <option :value="10">10</option>
                <option :value="25">25</option>
                <option :value="50">50</option>
              </select>
            </div>
          </div>
          
          <!-- Advanced Search Toggle -->
          <div class="mt-3">
            <button 
              class="btn btn-link btn-sm" 
              @click="showAdvancedSearch = !showAdvancedSearch"
            >
              <i class="fas fa-cog me-1"></i>
              {{ showAdvancedSearch ? 'Hide' : 'Show' }} Advanced Search
            </button>
          </div>
          
          <!-- Advanced Search Options -->
          <div v-if="showAdvancedSearch" class="row mt-3">
            <div class="col-md-3">
              <label class="form-label">Date From</label>
              <input v-model="dateFrom" type="date" class="form-control" @change="applyFilters" />
            </div>
            <div class="col-md-3">
              <label class="form-label">Date To</label>
              <input v-model="dateTo" type="date" class="form-control" @change="applyFilters" />
            </div>
          </div>
          
          <!-- Action Buttons -->
          <div class="mt-3 d-flex gap-2">
            <button class="btn btn-primary" @click="applyFilters">
              <i class="fas fa-search me-1"></i>Search
            </button>
            <button class="btn btn-outline-secondary" @click="resetFilters">
              <i class="fas fa-undo me-1"></i>Reset
            </button>
            <button class="btn btn-success" @click="exportToCSV">
              <i class="fas fa-download me-1"></i>Export CSV
            </button>
          </div>
        </div>
      </div>

      <!-- Bulk Actions -->
      <div class="mb-2">
        <button class="btn btn-success btn-sm me-2" @click="() => handleBulkAction('approve')" :disabled="selected.length === 0">Approve Selected</button>
        <button class="btn btn-danger btn-sm me-2" @click="() => handleBulkAction('reject')" :disabled="selected.length === 0">Reject Selected</button>
        <button class="btn btn-secondary btn-sm" @click="() => handleBulkAction('dismiss')" :disabled="selected.length === 0">Dismiss Reports</button>
      </div>

      <!-- Reviews Table -->
      <div class="card">
        <div class="card-body p-0">
          <table class="table table-striped mb-0">
            <thead>
              <tr>
                <th><input type="checkbox" v-model="selectAll" @change="toggleSelectAll" /></th>
                <th>User</th>
                <th>Book</th>
                <th>Rating</th>
                <th>Review</th>
                <th>Status</th>
                <th>Reported</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="review in reviews.data" :key="review.id">
                <td><input type="checkbox" :value="review.id" v-model="selected" @change="() => handleSelect(review.id)" /></td>
                <td v-html="highlightText(review.user?.name || 'Unknown', search)"></td>
                <td v-html="highlightText(review.book?.title || 'Unknown', search)"></td>
                <td>{{ review.rating }}★</td>
                <td v-html="highlightText(review.review, search)"></td>
                <td><span :class="{
                  'badge bg-warning': review.status === 'pending',
                  'badge bg-success': review.status === 'approved',
                  'badge bg-danger': review.status === 'rejected',
                }">{{ review.status }}</span></td>
                <td>
                  <span v-if="review.reported" class="badge bg-danger">Yes</span>
                  <span v-else class="badge bg-secondary">No</span>
                </td>
                <td>
                  <button class="btn btn-info btn-sm me-1" @click="() => openModal(review)">Details</button>
                  <button class="btn btn-success btn-sm me-1" @click="() => handleAction(review.id, 'approve')">Approve</button>
                  <button class="btn btn-danger btn-sm me-1" @click="() => handleAction(review.id, 'reject')">Reject</button>
                  <button class="btn btn-secondary btn-sm" @click="() => handleAction(review.id, 'dismiss')">Dismiss</button>
                </td>
              </tr>
              <tr v-if="reviews.data.length === 0"><td colspan="8" class="text-center text-muted">No reviews found</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pagination -->
      <nav v-if="reviews.meta && reviews.meta.last_page > 1" class="mt-3">
        <ul class="pagination justify-content-center">
          <li class="page-item" :class="{ disabled: !reviews.links.prev }">
            <a class="page-link" href="#" @click.prevent="router.get(reviews.links.prev)" tabindex="-1">Previous</a>
          </li>
          <li v-for="page in reviews.meta.last_page" :key="page" class="page-item" :class="{ active: page === reviews.meta.current_page }">
            <a class="page-link" href="#" @click.prevent="router.get(reviews.links.path + '?page=' + page)">{{ page }}</a>
          </li>
          <li class="page-item" :class="{ disabled: !reviews.links.next }">
            <a class="page-link" href="#" @click.prevent="router.get(reviews.links.next)">Next</a>
          </li>
        </ul>
      </nav>

      <!-- Review Details Modal -->
      <div v-if="showModal && modalReview" class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.4);">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Review Details</h5>
              <button type="button" class="btn-close" @click="closeModal"></button>
            </div>
            <div class="modal-body">
              <div class="mb-2"><strong>User:</strong> {{ modalReview.user?.name || 'Unknown' }}</div>
              <div class="mb-2"><strong>Book:</strong> {{ modalReview.book?.title || 'Unknown' }}</div>
              <div class="mb-2"><strong>Rating:</strong> {{ modalReview.rating }}★</div>
              <div class="mb-2"><strong>Review:</strong> {{ modalReview.review }}</div>
              <div class="mb-2"><strong>Status:</strong> <span :class="{
                'badge bg-warning': modalReview.status === 'pending',
                'badge bg-success': modalReview.status === 'approved',
                'badge bg-danger': modalReview.status === 'rejected',
              }">{{ modalReview.status }}</span></div>
              <div class="mb-2"><strong>Reported:</strong> <span v-if="modalReview.reported" class="badge bg-danger">Yes</span><span v-else class="badge bg-secondary">No</span></div>
              <div class="mb-2" v-if="modalReview.reported"><strong>Report Reason:</strong> {{ modalReview.report_reason }}</div>
              <div class="mb-2"><strong>Admin Note:</strong>
                <textarea v-model="modalNote" class="form-control" rows="2" placeholder="Add a note (optional)" :readonly="modalReview.status !== 'pending'"></textarea>
              </div>
              <div class="mb-2"><strong>Created At:</strong> {{ new Date(modalReview.created_at).toLocaleString() }}</div>
              <div class="mb-2"><strong>Updated At:</strong> {{ new Date(modalReview.updated_at).toLocaleString() }}</div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-success me-2" @click="() => handleModalAction('approve')" :disabled="modalReview.status !== 'pending'">Approve</button>
              <button class="btn btn-danger me-2" @click="() => handleModalAction('reject')" :disabled="modalReview.status !== 'pending'">Reject</button>
              <button class="btn btn-secondary me-2" @click="() => handleModalAction('dismiss')" :disabled="!modalReview.reported">Dismiss Report</button>
              <button class="btn btn-outline-dark" @click="closeModal">Close</button>
            </div>
          </div>
        </div>
      </div>
      <div v-if="showModal" class="modal-backdrop fade show"></div>
    </div>
  </AdminLayout>
</template>

<style scoped>
.is-loading {
  background-image: url("data:image/svg+xml,%3csvg width='100' height='100' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='m11 18 3.747-6.379L11 5.423V18Zm7 0V5.423L14.253 11.621 18 18Z'/%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 0.75rem center;
  background-size: 16px 12px;
}

mark {
  background-color: #fff3cd;
  color: #856404;
  padding: 0.1em 0.2em;
  border-radius: 0.2em;
}
</style> 
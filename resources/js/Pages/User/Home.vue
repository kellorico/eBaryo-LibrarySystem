<script setup>
import { ref, watch, onMounted, inject } from "vue";
import UserLayout from "@/Layouts/UserLayout.vue";
import BookReader from '@/Components/BookReader.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    stats: Object,
    recommendedBooks: Array,
    announcements: Array,
    readingProgress: Array,
    challenges: Array,
    leaderboard: Array,
});

const showLeaderboard = ref(false);
const selectedChallenge = ref(null);
const showReader = ref(false);
const readerBook = ref(null);
const readerType = ref('');

let showToast = inject('showToast');
if (!showToast) {
    showToast = (msg, type) => alert(msg);
}

const userProgress = ref({});

const loadingProgress = ref({});

function initializeUserProgress() {
    if (props.challenges && Array.isArray(props.challenges)) {
        props.challenges.forEach(challenge => {
            userProgress.value[challenge.id] = 0;
        });
    }
}

function updateProgress(challengeId, progress) {
    // Basic validation
    const challenge = props.challenges.find(c => c.id === challengeId);
    if (!challenge) return;
    if (progress < 0 || progress > challenge.target_books) {
        showToast('Progress must be between 0 and ' + challenge.target_books, 'error');
        return;
    }
    loadingProgress.value[challengeId] = true;
    router.post(`/challenges/${challengeId}/progress`, { progress }, {
        preserveScroll: true,
        onSuccess: () => {
            showToast('Progress updated!', 'success');
        },
        onError: () => {
            showToast('Failed to update progress.', 'error');
        },
        onFinish: () => {
            loadingProgress.value[challengeId] = false;
        }
    });
}

onMounted(() => {
    initializeUserProgress();
});
watch(() => props.challenges, initializeUserProgress, { immediate: true });
</script>

<template>
    <UserLayout>
        <Head title="Home" />
        <div class="container py-4">
            <!-- Dashboard Enhancements -->
            <div class="row mb-4 g-3">
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="fw-bold fs-4 mb-1">{{ props.stats?.bookmarks ?? 0 }}</div>
                            <div class="text-muted">Bookmarks</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="fw-bold fs-4 mb-1">{{ props.stats?.inProgress ?? 0 }}</div>
                            <div class="text-muted">Books In Progress</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="fw-bold fs-4 mb-1">{{ props.stats?.challenges ?? 0 }}</div>
                            <div class="text-muted">Challenges Joined</div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="props.recommendedBooks.length" class="mb-5">
                <h4 class="fw-bold mb-3"><i class="fa fa-star text-warning me-2"></i>Recommended Books</h4>
                <div class="d-flex flex-nowrap overflow-auto gap-3">
                    <div v-for="book in props.recommendedBooks" :key="book.id" class="card shadow-sm" style="min-width:180px;max-width:180px;">
                        <img :src="book.cover_image" alt="Cover" class="card-img-top" style="height:120px;object-fit:cover;" />
                        <div class="card-body p-2">
                            <div class="fw-bold small mb-1">{{ book.title }}</div>
                            <div class="text-muted small mb-2">by {{ book.author }}</div>
                            <button class="btn btn-sm btn-outline-success w-100" @click="continueReading(book)"><i class="fa fa-book-open"></i> Read</button>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="props.announcements.length" class="mb-5">
                <h4 class="fw-bold mb-3"><i class="fa fa-bullhorn text-primary me-2"></i>Recent Announcements</h4>
                <div class="list-group">
                    <div v-for="a in props.announcements" :key="a.id" class="list-group-item">
                        <div class="fw-bold">{{ a.title }}</div>
                        <div class="text-muted small">{{ a.created_at }}</div>
                        <div>{{ a.message }}</div>
                    </div>
                </div>
            </div>
            <!-- Continue Reading Section -->
            <div v-if="props.readingProgress.length" class="mb-5">
                <h4 class="fw-bold mb-3"><i class="fa fa-play-circle text-success me-2"></i>Continue Reading</h4>
                <div class="d-flex flex-nowrap overflow-auto gap-3">
                    <div v-for="p in props.readingProgress.filter(p => p.book)" :key="p.book.id" class="card shadow-sm" style="min-width:180px;max-width:180px;">
                        <img :src="p.book.cover_image" alt="Cover" class="card-img-top" style="height:120px;object-fit:cover;" />
                        <div class="card-body p-2">
                            <div class="fw-bold small mb-1">{{ p.book.title }}</div>
                            <div class="progress mb-2" style="height:6px;">
                                <div class="progress-bar bg-success" :style="`width:${Math.round(p.progress)}%`"></div>
                            </div>
                            <button class="btn btn-sm btn-outline-success w-100" @click="continueReading(p.book)"><i class="fa fa-play"></i> Continue</button>
                        </div>
                    </div>
                </div>
            </div>
            <h2 class="fw-bold mb-4">
                <i class="fa fa-trophy text-warning me-2"></i>Reading Challenges
            </h2>
            <div v-if="props.challenges.length === 0" class="text-muted text-center">
                No active challenges.
            </div>
            <div v-else class="row g-3">
                <div v-for="c in props.challenges" :key="c.id" class="col-12">
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <div
                                class="d-flex align-items-center gap-2 mb-2"
                            >
                                <span class="fw-bold fs-5">{{
                                    c.title
                                }}</span>
                                <span
                                    class="badge bg-warning text-dark ms-2"
                                    >Target:
                                    {{ c.target_books }} books</span
                                >
                                <span class="ms-auto text-muted small"
                                    >{{ c.start_date }} -
                                    {{ c.end_date }}</span
                                >
                            </div>
                            <div class="mb-2">{{ c.description }}</div>
                            <div v-if="userProgress[c.id] !== undefined">
                                <label>Progress:</label>
                                <input
                                    type="number"
                                    min="0"
                                    :max="c.target_books"
                                    v-model.number="userProgress[c.id]"
                                    :disabled="loadingProgress[c.id]"
                                    @change="updateProgress(c.id, userProgress[c.id])"
                                    style="width: 80px"
                                />
                                / {{ c.target_books }}
                                <span
                                    v-if="
                                        userProgress[c.id] >= c.target_books
                                    "
                                    class="badge bg-success ms-2"
                                    >Completed!</span
                                >
                                <span v-if="loadingProgress[c.id]" class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>
                            </div>
                            <div v-else>
                                <button
                                    class="btn btn-outline-warning"
                                    @click="joinChallenge(c.id)"
                                >
                                    <i class="fa fa-flag-checkered"></i>
                                    Join Challenge
                                </button>
                            </div>
                            <button
                                class="btn btn-link p-0 ms-3"
                                @click="openLeaderboard(c)"
                            >
                                <i class="fa fa-list-ol"></i> View
                                Leaderboard
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Leaderboard Modal -->
            <div
                v-if="showLeaderboard"
                class="modal-backdrop"
                @click.self="showLeaderboard = false"
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
                            <h5
                                class="modal-title d-flex align-items-center gap-2"
                            >
                                <i class="fa fa-list-ol text-warning"></i>
                                Leaderboard - {{ selectedChallenge?.title }}
                            </h5>
                            <button
                                type="button"
                                class="btn-close"
                                @click="showLeaderboard = false"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(u, i) in props.leaderboard"
                                        :key="u.id"
                                    >
                                        <td>{{ i + 1 }}</td>
                                        <td>{{ u.name }}</td>
                                        <td>{{ u.pivot_progress }}</td>
                                    </tr>
                                    <tr v-if="props.leaderboard.length === 0">
                                        <td
                                            colspan="3"
                                            class="text-center text-muted"
                                        >
                                            No participants yet.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="showReader">
                <BookReader v-if="readerType === 'epub'" :file="readerBook.file_path" :bookId="readerBook.id" @close="closeReader" />
                <!-- Add PdfReader for PDFs if needed -->
            </div>
        </div>
    </UserLayout>
</template>

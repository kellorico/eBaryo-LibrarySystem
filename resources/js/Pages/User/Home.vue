<script setup>
import { ref, watch, onMounted, inject, reactive } from "vue";
import UserLayout from "@/Layouts/UserLayout.vue";
import BookReader from '@/Components/BookReader.vue';
import { router } from '@inertiajs/vue3';
import BookCard from '@/Components/BookCard.vue';

const props = defineProps({
    stats: Object,
    recommendedBooks: Array,
    announcements: Array,
    readingProgress: Array,
    challenges: Array,
    leaderboard: Array,
    latestBooks: Array,
    mostReadBooks: Array,
    hotBooks: Array,
    highestRatedBooks: Array,
    success: String,
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

// Add state for each book section's window
const sectionWindows = reactive({
    recommended: 0,
    latest: 0,
    mostRead: 0,
    hot: 0,
    highestRated: 0,
});
const windowSize = 4;
function next(section, list) {
    if (sectionWindows[section] + windowSize < list.length) sectionWindows[section]++;
}
function prev(section) {
    if (sectionWindows[section] > 0) sectionWindows[section]--;
}

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

function continueReading(book) {
    readerBook.value = book;
    readerType.value = book.file_path && book.file_path.endsWith('.pdf') ? 'pdf' : 'epub';
    showReader.value = true;
}

function closeReader() {
    showReader.value = false;
    readerType.value = '';
    readerBook.value = null;
}

const selectedBook = ref(null);
const showBookDetails = ref(false);
function openBookDetails(book) {
    router.visit(`/books/${book.id}`);
}
function saveBook(book) {
    showToast('Book saved!', 'success');
}
function bookmarkBook(book) {
    showToast('Book bookmarked!', 'success');
}
function reportBook(book) {
    showToast('Book reported!', 'warning');
}

const showWelcome = ref(!!props.success);

onMounted(() => {
    initializeUserProgress();
    if (props.success) {
        showWelcome.value = true;
        setTimeout(() => (showWelcome.value = false), 4000);
    }
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
                    <div class="stat-card-modern h-100 d-flex flex-column align-items-center justify-content-center p-4 position-relative">
                        <span class="stat-icon bg-gradient-green mb-2"><i class="fa fa-bookmark"></i></span>
                        <div class="fw-bold fs-3 mb-1">{{ props.stats?.bookmarks ?? 0 }}</div>
                        <div class="text-muted">Bookmarks</div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="stat-card-modern h-100 d-flex flex-column align-items-center justify-content-center p-4 position-relative">
                        <span class="stat-icon bg-gradient-blue mb-2"><i class="fa fa-spinner"></i></span>
                        <div class="fw-bold fs-3 mb-1">{{ props.stats?.inProgress ?? 0 }}</div>
                        <div class="text-muted">Books In Progress</div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="stat-card-modern h-100 d-flex flex-column align-items-center justify-content-center p-4 position-relative">
                        <span class="stat-icon bg-gradient-yellow mb-2"><i class="fa fa-trophy"></i></span>
                        <div class="fw-bold fs-3 mb-1">{{ props.stats?.challenges ?? 0 }}</div>
                        <div class="text-muted">Challenges Joined</div>
                    </div>
                </div>
            </div>
            <!-- Reading Challenges Section (moved to top) -->
            <div class="book-section-card p-4 mb-4">
                <div class="d-flex align-items-center mb-1">
                    <span class="section-accent bg-warning me-3"></span>
                    <h2 class="fw-bold mb-0 d-flex align-items-center fs-3">
                        <i class="fa fa-trophy text-warning me-2"></i>Reading Challenges
                    </h2>
                </div>
                <div class="text-muted mb-3 ms-5">Track your progress and join new challenges</div>
                <div v-if="props.challenges.length === 0" class="text-muted text-center">
                    No active challenges.
                </div>
                <div v-else class="row g-3">
                    <div v-for="c in props.challenges" :key="c.id" class="col-12">
                        <div class="challenge-card-modern card shadow-sm mb-3 border-0 position-relative">
                            <div class="card-body">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="fw-bold fs-5 text-warning"><i class="fa fa-trophy me-2"></i>{{ c.title }}</span>
                                    <span class="badge bg-warning text-dark ms-2">Target: {{ c.target_books }} books</span>
                                    <span class="ms-auto text-muted small">{{ c.start_date }} - {{ c.end_date }}</span>
                                </div>
                                <div class="mb-2">{{ c.description }}</div>
                                <div v-if="userProgress[c.id] !== undefined">
                                    <label class="mb-1">Progress:</label>
                                    <div class="progress modern-progress mb-2" style="height:10px;">
                                        <div class="progress-bar bg-success" :style="`width:${Math.round(userProgress[c.id]/c.target_books*100)}%`"></div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <input
                                            type="number"
                                            min="0"
                                            :max="c.target_books"
                                            v-model.number="userProgress[c.id]"
                                            :disabled="loadingProgress[c.id]"
                                            @change="updateProgress(c.id, userProgress[c.id])"
                                            style="width: 80px"
                                            class="form-control form-control-sm d-inline-block"
                                        />
                                        <span>/ {{ c.target_books }}</span>
                                        <span v-if="userProgress[c.id] >= c.target_books" class="badge bg-success ms-2"><i class="fa fa-check-circle me-1"></i>Completed!</span>
                                        <span v-if="loadingProgress[c.id]" class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>
                                    </div>
                                </div>
                                <div v-else>
                                    <button class="btn btn-outline-warning" @click="joinChallenge(c.id)"><i class="fa fa-flag-checkered"></i> Join Challenge</button>
                                </div>
                                <button class="btn btn-link p-0 ms-3" @click="openLeaderboard(c)"><i class="fa fa-list-ol"></i> View Leaderboard</button>
                            </div>
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
            <div v-if="props.recommendedBooks.length" class="mb-5">
                <div class="book-section-card p-4 mb-4">
                    <div class="d-flex align-items-center mb-1">
                        <span class="section-accent bg-warning me-3"></span>
                        <h4 class="fw-bold mb-0 d-flex align-items-center fs-3">
                            <i class="fa fa-star text-warning me-2"></i>Recommended Books
                        </h4>
                        <div class="ms-auto">
                            <button class="btn btn-outline-secondary btn-sm me-2" :disabled="sectionWindows.recommended === 0" @click="prev('recommended')"><i class="fa fa-chevron-left"></i></button>
                            <button class="btn btn-outline-secondary btn-sm" :disabled="sectionWindows.recommended + windowSize >= props.recommendedBooks.length" @click="next('recommended', props.recommendedBooks)"><i class="fa fa-chevron-right"></i></button>
                        </div>
                    </div>
                    <div class="text-muted mb-3 ms-5">Handpicked just for you</div>
                    <transition-group name="fade-bookcard" tag="div" class="row g-3">
                        <div v-for="book in props.recommendedBooks.slice(sectionWindows.recommended, sectionWindows.recommended + windowSize)" :key="book.id" class="col-12 col-sm-6 col-md-3">
                            <BookCard
                                :title="book.title"
                                :author="book.author"
                                :cover_image="book.cover_image"
                                :published_year="book.published_year"
                                :badge="null"
                                @details="openBookDetails(book)"
                                @read="continueReading(book)"
                                @save="saveBook(book)"
                                @bookmark="bookmarkBook(book)"
                                @report="reportBook(book)"
                            />
                        </div>
                    </transition-group>
                </div>
            </div>
            <div v-if="props.latestBooks.length" class="mb-5">
                <div class="book-section-card p-4 mb-4">
                    <div class="d-flex align-items-center mb-1">
                        <span class="section-accent bg-info me-3"></span>
                        <h4 class="fw-bold mb-0 d-flex align-items-center fs-3">
                            <i class="fa fa-clock text-info me-2"></i>New Arrivals
                        </h4>
                        <div class="ms-auto">
                            <button class="btn btn-outline-secondary btn-sm me-2" :disabled="sectionWindows.latest === 0" @click="prev('latest')"><i class="fa fa-chevron-left"></i></button>
                            <button class="btn btn-outline-secondary btn-sm" :disabled="sectionWindows.latest + windowSize >= props.latestBooks.length" @click="next('latest', props.latestBooks)"><i class="fa fa-chevron-right"></i></button>
                        </div>
                    </div>
                    <div class="text-muted mb-3 ms-5">New books added every week</div>
                    <transition-group name="fade-bookcard" tag="div" class="row g-3">
                        <div v-for="book in props.latestBooks.slice(sectionWindows.latest, sectionWindows.latest + windowSize)" :key="book.id" class="col-12 col-sm-6 col-md-3">
                            <BookCard
                                :title="book.title"
                                :author="book.author"
                                :cover_image="book.cover_image"
                                :published_year="book.published_year"
                                :badge="(new Date(book.created_at)).toLocaleDateString()"
                                @details="openBookDetails(book)"
                                @read="continueReading(book)"
                                @save="saveBook(book)"
                                @bookmark="bookmarkBook(book)"
                                @report="reportBook(book)"
                            />
                        </div>
                    </transition-group>
                </div>
            </div>
            <div v-if="props.mostReadBooks.length" class="mb-5">
                <div class="book-section-card p-4 mb-4">
                    <div class="d-flex align-items-center mb-1">
                        <span class="section-accent bg-danger me-3"></span>
                        <h4 class="fw-bold mb-0 d-flex align-items-center fs-3">
                            <i class="fa fa-fire text-danger me-2"></i>Most Read Books
                        </h4>
                        <div class="ms-auto">
                            <button class="btn btn-outline-secondary btn-sm me-2" :disabled="sectionWindows.mostRead === 0" @click="prev('mostRead')"><i class="fa fa-chevron-left"></i></button>
                            <button class="btn btn-outline-secondary btn-sm" :disabled="sectionWindows.mostRead + windowSize >= props.mostReadBooks.length" @click="next('mostRead', props.mostReadBooks)"><i class="fa fa-chevron-right"></i></button>
                        </div>
                    </div>
                    <div class="text-muted mb-3 ms-5">Your top reads</div>
                    <transition-group name="fade-bookcard" tag="div" class="row g-3">
                        <div v-for="book in props.mostReadBooks.slice(sectionWindows.mostRead, sectionWindows.mostRead + windowSize)" :key="book.id" class="col-12 col-sm-6 col-md-3">
                            <BookCard
                                :title="book.title"
                                :author="book.author"
                                :cover_image="book.cover_image"
                                :published_year="book.published_year"
                                :badge="book.read_count + ' reads'"
                                @details="openBookDetails(book)"
                                @read="continueReading(book)"
                                @save="saveBook(book)"
                                @bookmark="bookmarkBook(book)"
                                @report="reportBook(book)"
                            />
                        </div>
                    </transition-group>
                </div>
            </div>
            <div v-if="props.hotBooks.length" class="mb-5">
                <div class="book-section-card p-4 mb-4">
                    <div class="d-flex align-items-center mb-1">
                        <span class="section-accent bg-warning me-3"></span>
                        <h4 class="fw-bold mb-0 d-flex align-items-center fs-3">
                            <i class="fa fa-bolt text-warning me-2"></i>Hot This Month
                        </h4>
                        <div class="ms-auto">
                            <button class="btn btn-outline-secondary btn-sm me-2" :disabled="sectionWindows.hot === 0" @click="prev('hot')"><i class="fa fa-chevron-left"></i></button>
                            <button class="btn btn-outline-secondary btn-sm" :disabled="sectionWindows.hot + windowSize >= props.hotBooks.length" @click="next('hot', props.hotBooks)"><i class="fa fa-chevron-right"></i></button>
                        </div>
                    </div>
                    <div class="text-muted mb-3 ms-5">Popular reads</div>
                    <transition-group name="fade-bookcard" tag="div" class="row g-3">
                        <div v-for="book in props.hotBooks.slice(sectionWindows.hot, sectionWindows.hot + windowSize)" :key="book.id" class="col-12 col-sm-6 col-md-3">
                            <BookCard
                                :title="book.title"
                                :author="book.author"
                                :cover_image="book.cover_image"
                                :published_year="book.published_year"
                                :badge="book.read_count + ' reads'"
                                @details="openBookDetails(book)"
                                @read="continueReading(book)"
                                @save="saveBook(book)"
                                @bookmark="bookmarkBook(book)"
                                @report="reportBook(book)"
                            />
                        </div>
                    </transition-group>
                </div>
            </div>
            <div v-if="props.highestRatedBooks.length" class="mb-5">
                <div class="book-section-card p-4 mb-4">
                    <div class="d-flex align-items-center mb-1">
                        <span class="section-accent bg-success me-3"></span>
                        <h4 class="fw-bold mb-0 d-flex align-items-center fs-3">
                            <i class="fa fa-star text-success me-2"></i>Top Rated Books
                        </h4>
                        <div class="ms-auto">
                            <button class="btn btn-outline-secondary btn-sm me-2" :disabled="sectionWindows.highestRated === 0" @click="prev('highestRated')"><i class="fa fa-chevron-left"></i></button>
                            <button class="btn btn-outline-secondary btn-sm" :disabled="sectionWindows.highestRated + windowSize >= props.highestRatedBooks.length" @click="next('highestRated', props.highestRatedBooks)"><i class="fa fa-chevron-right"></i></button>
                        </div>
                    </div>
                    <div class="text-muted mb-3 ms-5">Highly rated</div>
                    <transition-group name="fade-bookcard" tag="div" class="row g-3">
                        <div v-for="book in props.highestRatedBooks.slice(sectionWindows.highestRated, sectionWindows.highestRated + windowSize)" :key="book.id" class="col-12 col-sm-6 col-md-3">
                            <BookCard
                                :title="book.title"
                                :author="book.author"
                                :cover_image="book.cover_image"
                                :published_year="book.published_year"
                                :badge="book.avg_rating + '★ (' + book.ratings_count + ')'"
                                @details="openBookDetails(book)"
                                @read="continueReading(book)"
                                @save="saveBook(book)"
                                @bookmark="bookmarkBook(book)"
                                @report="reportBook(book)"
                            />
                        </div>
                    </transition-group>
                </div>
            </div>
            <div v-if="props.announcements.length" class="mb-5">
                <div class="book-section-card p-4 mb-4">
                    <div class="d-flex align-items-center mb-1">
                        <span class="section-accent bg-primary me-3"></span>
                        <h4 class="fw-bold mb-0 d-flex align-items-center fs-3">
                            <i class="fa fa-bullhorn text-primary me-2"></i>Recent Announcements
                        </h4>
                    </div>
                    <div class="text-muted mb-3 ms-5">Stay up to date with the latest news</div>
                    <div class="list-group announcement-list-modern">
                        <div v-for="a in props.announcements" :key="a.id" class="list-group-item border-0 mb-2 rounded-3 shadow-sm p-3 announcement-item-modern">
                            <div class="fw-bold mb-1 text-primary"><i class="fa fa-bullhorn me-2"></i>{{ a.title }}</div>
                            <div class="text-muted small mb-1"><i class="fa fa-calendar-alt me-1"></i>{{ a.created_at }}</div>
                            <div>{{ a.message }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Continue Reading Section -->
            <div v-if="props.readingProgress.length" class="mb-5">
                <div class="book-section-card p-4 mb-4">
                    <div class="d-flex align-items-center mb-1">
                        <span class="section-accent bg-success me-3"></span>
                        <h4 class="fw-bold mb-0 d-flex align-items-center fs-3">
                            <i class="fa fa-play-circle text-success me-2"></i>Continue Reading
                        </h4>
                    </div>
                    <div class="text-muted mb-3 ms-5">Pick up where you left off</div>
                    <div class="d-flex flex-nowrap overflow-auto gap-3 continue-reading-scroll pb-2">
                        <div v-for="p in props.readingProgress.filter(p => p.book)" :key="p.book.id" class="continue-card-modern card shadow-sm border-0">
                            <img :src="p.book.cover_image" alt="Cover" class="card-img-top rounded-top" style="height:120px;object-fit:cover;" />
                            <div class="card-body p-2">
                                <div class="fw-bold small mb-1 text-truncate">{{ p.book.title }}</div>
                                <div class="progress modern-progress mb-2" style="height:8px;">
                                    <div class="progress-bar bg-success" :style="`width:${Math.round(p.progress)}%`"></div>
                                </div>
                                <button class="btn btn-sm btn-success w-100" @click="continueReading(p.book)"><i class="fa fa-play"></i> Continue</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="showReader">
                <BookReader v-if="readerType === 'epub'" :file="readerBook.file_path" :bookId="readerBook.id" @close="closeReader" />
                <!-- Add PdfReader for PDFs if needed -->
            </div>
            <transition name="fade-toast">
                <div v-if="showWelcome" class="position-fixed start-50 translate-middle-x" style="top: 60px; min-width:260px; max-width:350px; z-index:20000;">
                    <div class="alert alert-success alert-dismissible fade show shadow d-flex align-items-center gap-2 px-4 py-3 position-relative" role="alert">
                        <i class="fa fa-smile-beam fa-lg me-2"></i>
                        <div class="flex-grow-1">
                            <span class="fw-bold">{{ props.success }}</span>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </UserLayout>
</template>

<style scoped>
.fade-toast-enter-active, .fade-toast-leave-active {
  transition: opacity 0.5s;
}
.fade-toast-enter-from, .fade-toast-leave-to {
  opacity: 0;
}
.stat-card-modern {
  background: linear-gradient(135deg, #f8fafc 0%, #e9f7ef 100%);
  border-radius: 1.2rem;
  box-shadow: 0 2px 12px #19875422;
  transition: box-shadow 0.22s cubic-bezier(0.4, 2, 0.3, 1), transform 0.22s cubic-bezier(0.4, 2, 0.3, 1);
  cursor: pointer;
}
.stat-card-modern:hover {
  box-shadow: 0 8px 32px #19875433, 0 2px 8px #43c59e22;
  transform: translateY(-2px) scale(1.03);
}
.stat-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  font-size: 1.7rem;
  color: #fff;
  box-shadow: 0 2px 8px rgba(25,135,84,0.12);
}
.bg-gradient-green {
  background: linear-gradient(135deg, #198754 60%, #43c59e 100%);
}
.bg-gradient-blue {
  background: linear-gradient(135deg, #0d6efd 60%, #43c5e9 100%);
}
.bg-gradient-yellow {
  background: linear-gradient(135deg, #ffc107 60%, #ffe066 100%);
  color: #856404 !important;
}
.book-section-card {
  background: #fff;
  border-radius: 1.2rem;
  box-shadow: 0 2px 12px #19875411;
  border: 1px solid #e9ecef;
}
.section-accent {
  display: inline-block;
  width: 8px;
  height: 32px;
  border-radius: 8px;
}
.fade-bookcard-enter-active, .fade-bookcard-leave-active {
  transition: opacity 0.4s, transform 0.4s;
}
.fade-bookcard-enter-from, .fade-bookcard-leave-to {
  opacity: 0;
  transform: translateY(16px);
}
.announcement-list-modern {
  background: none;
}
.announcement-item-modern {
  background: #f8fafc;
  border-left: 4px solid #0d6efd;
  transition: box-shadow 0.2s, border-color 0.2s;
}
.announcement-item-modern:hover {
  box-shadow: 0 4px 16px #0d6efd22;
  border-left-color: #198754;
}
.continue-reading-scroll {
  scrollbar-width: thin;
  scrollbar-color: #198754 #e9ecef;
}
.continue-card-modern {
  min-width: 180px;
  max-width: 180px;
  border-radius: 1rem;
  transition: box-shadow 0.2s, transform 0.2s;
}
.continue-card-modern:hover {
  box-shadow: 0 8px 32px #19875433, 0 2px 8px #43c59e22;
  transform: translateY(-2px) scale(1.03);
}
.modern-progress {
  background: #e9f7ef;
  border-radius: 6px;
}
.challenge-card-modern {
  border-left: 6px solid #ffc107;
  border-radius: 1.2rem;
  transition: box-shadow 0.2s, border-color 0.2s;
  background: #fffbe6;
}
.challenge-card-modern:hover {
  box-shadow: 0 8px 32px #ffc10733, 0 2px 8px #43c59e22;
  border-left-color: #198754;
}
</style>

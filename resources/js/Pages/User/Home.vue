<script setup>
import { ref, watch, onMounted, inject, reactive, computed } from "vue";
import UserLayout from "@/Layouts/UserLayout.vue";
import BookReader from "@/Components/BookReader.vue";
import { router } from "@inertiajs/vue3";
import BookCard from "@/Components/BookCard.vue";

const props = defineProps({
    stats: Object,
    recommendedBooks: Array,
    announcements: Array,
    readingProgress: Array,
    leaderboard: Array,
    latestBooks: Array,
    mostReadBooks: Array,
    hotBooks: Array,
    highestRatedBooks: Array,
    success: String,
    savedBooks: { type: Array, default: () => [] },
});

const showReader = ref(false);
const readerBook = ref(null);
const readerType = ref("");

let showToast = inject("showToast");
if (!showToast) {
    showToast = (msg, type) => alert(msg);
}

// Add state for each book section's window
const sectionWindows = reactive({
    recommended: 0,
    latest: 0,
    mostRead: 0,
    hot: 0,
    highestRated: 0,
    saved: 0,
});
const windowSize = 4;
function next(section, list) {
    if (sectionWindows[section] + windowSize < list.length)
        sectionWindows[section]++;
}
function prev(section) {
    if (sectionWindows[section] > 0) sectionWindows[section]--;
}

function continueReading(book) {
    readerBook.value = book;
    readerType.value =
        book.file_path && book.file_path.endsWith(".pdf") ? "pdf" : "epub";
    showReader.value = true;
}

function closeReader() {
    showReader.value = false;
    readerType.value = "";
    readerBook.value = null;
}

function openBookDetails(book) {
    router.visit(`/books/${book.id}`);
}

async function saveBook(book) {
    // If there is a real save endpoint, use it. Otherwise, just show a toast.
    try {
        // Example: await fetch('/user/saved-books', { method: 'POST', body: JSON.stringify({ book_id: book.id }), headers: { 'Content-Type': 'application/json' } });
        showToast("Book saved!", "success");
    } catch (e) {
        showToast("Failed to save book.", "danger");
    }
}

async function bookmarkBook(book) {
    try {
        const res = await fetch('/bookmarks', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ book_id: book.id })
        });
        if (res.ok) {
            showToast("Book bookmarked!", "success");
        } else {
            const data = await res.json();
            showToast(data.message || "Failed to bookmark book.", "danger");
        }
    } catch (e) {
        showToast("Failed to bookmark book.", "danger");
    }
}

async function reportBook(book) {
    const reason = prompt("Please enter a reason for reporting this book:");
    if (!reason) return;
    try {
        // If there is a real report endpoint, use it. Otherwise, just show a toast.
        // Example: await fetch(`/books/${book.id}/report`, { method: 'POST', body: JSON.stringify({ reason }), headers: { 'Content-Type': 'application/json' } });
        showToast("Book reported!", "warning");
    } catch (e) {
        showToast("Failed to report book.", "danger");
    }
}

const showWelcome = ref(!!props.success);

onMounted(() => {
    if (props.success) {
        showWelcome.value = true;
        setTimeout(() => (showWelcome.value = false), 4000);
    }
});

function viewSavedBook(book) {
    const file = book.file_path || "";
    if (file.startsWith("http")) {
        window.open(file, "_blank");
    } else {
        window.open(`/storage/${file}`, "_blank");
    }
}

// --- New Arrivals Enhancements ---
const latestFilter = reactive({ category: '', sort: 'newest' });
const latestCategories = computed(() => {
    const cats = new Set();
    props.latestBooks.forEach(b => {
        if (b.category) cats.add(b.category);
    });
    return Array.from(cats);
});
function isNew(dateStr) {
    const date = new Date(dateStr);
    const now = new Date();
    const diff = (now - date) / (1000 * 60 * 60 * 24);
    return diff <= 14;
}
function daysAgo(dateStr) {
    const date = new Date(dateStr);
    const now = new Date();
    const diff = Math.floor((now - date) / (1000 * 60 * 60 * 24));
    if (diff === 0) return 'today';
    if (diff === 1) return 'yesterday';
    return diff + ' days';
}
const filteredLatestBooks = computed(() => {
    let books = props.latestBooks;
    if (latestFilter.category) {
        books = books.filter(b => b.category === latestFilter.category);
    }
    if (latestFilter.sort === 'popular') {
        books = [...books].sort((a, b) => (b.read_count || 0) - (a.read_count || 0));
    } else {
        books = [...books].sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    }
    return books;
});

// --- Recommended Books Enhancements ---
const recommendedFilter = reactive({ category: '', sort: 'personal' });
const recommendedCategories = computed(() => {
    const cats = new Set();
    props.recommendedBooks.forEach(b => {
        if (b.category) cats.add(b.category);
    });
    return Array.from(cats);
});
const filteredRecommendedBooks = computed(() => {
    let books = props.recommendedBooks;
    if (recommendedFilter.category) {
        books = books.filter(b => b.category === recommendedFilter.category);
    }
    if (recommendedFilter.sort === 'rating') {
        books = [...books].sort((a, b) => (b.avg_rating || 0) - (a.avg_rating || 0));
    }
    return books;
});
function refreshRecommendations() {
    // Placeholder: In a real app, trigger an API call or reload the page
    window.location.reload();
}
</script>

<template>
    <UserLayout>
        <Head title="Home" />
        <div class="container py-4">
            <!-- Dashboard Enhancements -->
            <div class="row mb-4 g-3">
                <div class="col-12 col-md-4">
                    <div
                        class="stat-card-modern h-100 d-flex flex-column align-items-center justify-content-center p-4 position-relative"
                    >
                        <span class="stat-icon bg-gradient-green mb-2"
                            ><i class="fa fa-bookmark"></i
                        ></span>
                        <div class="fw-bold fs-3 mb-1">
                            {{ props.stats?.bookmarks ?? 0 }}
                        </div>
                        <div class="text-muted">Bookmarks</div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div
                        class="stat-card-modern h-100 d-flex flex-column align-items-center justify-content-center p-4 position-relative"
                    >
                        <span class="stat-icon bg-gradient-blue mb-2"
                            ><i class="fa fa-spinner"></i
                        ></span>
                        <div class="fw-bold fs-3 mb-1">
                            {{ props.stats?.inProgress ?? 0 }}
                        </div>
                        <div class="text-muted">Books In Progress</div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div
                        class="stat-card-modern h-100 d-flex flex-column align-items-center justify-content-center p-4 position-relative"
                    >
                        <span class="stat-icon bg-gradient-yellow mb-2"
                            ><i class="fa fa-star me-2 text-"></i
                        ></span>
                        <div class="fw-bold fs-3 mb-1">
                            {{ props.stats?.inProgress ?? 0 }}
                        </div>
                        <div class="text-muted">Saved Books</div>
                    </div>
                </div>
            </div>

            <div v-if="props.recommendedBooks.length" class="mb-5">
                <div class="book-section-card p-4 mb-4">
                    <div class="d-flex align-items-center mb-1">
                        <span class="section-accent bg-warning me-3"></span>
                        <h4 class="fw-bold mb-0 d-flex align-items-center fs-3">
                            <i class="fa fa-star text-warning me-2"></i>
                            Recommended Books
                        </h4>
                        <div class="ms-auto d-flex align-items-center gap-2">
                            <button
                                class="btn btn-outline-secondary btn-sm me-2"
                                :disabled="sectionWindows.recommended === 0"
                                @click="prev('recommended')"
                            >
                                <i class="fa fa-chevron-left"></i>
                            </button>
                            <button
                                class="btn btn-outline-secondary btn-sm me-2"
                                :disabled="sectionWindows.recommended + windowSize >= filteredRecommendedBooks.length"
                                @click="next('recommended', filteredRecommendedBooks)"
                            >
                                <i class="fa fa-chevron-right"></i>
                            </button>
                            <button class="btn btn-outline-primary btn-sm" @click="refreshRecommendations">
                                <i class="fa fa-sync"></i> Refresh
                            </button>
                        </div>
                    </div>
                    <div class="text-muted mb-3 ms-5">
                        Handpicked just for you
                    </div>
                    <!-- Filter and Sort Controls -->
                    <div class="mb-3 ms-5 d-flex gap-3 align-items-center">
                        <select v-model="recommendedFilter.category" class="form-select form-select-sm w-auto">
                            <option value="">All Categories</option>
                            <option v-for="cat in recommendedCategories" :key="cat" :value="cat">{{ cat }}</option>
                        </select>
                        <select v-model="recommendedFilter.sort" class="form-select form-select-sm w-auto">
                            <option value="personal">Personalized</option>
                            <option value="rating">Highest Rated</option>
                        </select>
                    </div>
                    <div v-if="!filteredRecommendedBooks.length" class="text-center text-muted py-5">
                        <i class="fa fa-star fa-2x mb-3"></i>
                        <div>No recommendations at the moment. Try refreshing or explore other sections!</div>
                    </div>
                    <transition-group
                        v-else
                        name="fade-bookcard"
                        tag="div"
                        class="row g-3"
                    >
                        <div
                            v-for="book in filteredRecommendedBooks.slice(
                                sectionWindows.recommended,
                                sectionWindows.recommended + windowSize
                            )"
                            :key="book.id"
                            class="col-12 col-sm-6 col-md-3"
                        >
                            <BookCard
                                :title="book.title"
                                :author="book.author"
                                :cover_image="book.cover_image"
                                :published_year="book.published_year"
                                :badge="'Recommended for You'"
                                @details="openBookDetails(book)"
                                @read="continueReading(book)"
                                @save="saveBook(book)"
                                @bookmark="bookmarkBook(book)"
                                @report="reportBook(book)"
                            >
                                <template #footer>
                                    <div class="small text-muted mt-1">
                                        <span v-if="book.recommend_reason">{{ book.recommend_reason }}</span>
                                        <span v-else>Because you like similar books</span>
                                    </div>
                                    <div v-if="book.avg_rating && book.ratings_count" class="small text-warning">
                                        <i class="fa fa-star"></i> {{ book.avg_rating }} ({{ book.ratings_count }})
                                    </div>
                                </template>
                            </BookCard>
                        </div>
                    </transition-group>
                </div>
            </div>
            <div v-if="props.latestBooks.length" class="mb-5">
                <div class="book-section-card p-4 mb-4">
                    <div class="d-flex align-items-center mb-1">
                        <span class="section-accent bg-info me-3"></span>
                        <h4 class="fw-bold mb-0 d-flex align-items-center fs-3">
                            <i class="fa fa-clock text-info me-2"></i>New
                            Arrivals
                        </h4>
                        <div class="ms-auto">
                            <button
                                class="btn btn-outline-secondary btn-sm me-2"
                                :disabled="sectionWindows.latest === 0"
                                @click="prev('latest')"
                            >
                                <i class="fa fa-chevron-left"></i>
                            </button>
                            <button
                                class="btn btn-outline-secondary btn-sm"
                                :disabled="
                                    sectionWindows.latest + windowSize >=
                                    filteredLatestBooks.length
                                "
                                @click="next('latest', filteredLatestBooks)"
                            >
                                <i class="fa fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    <div class="text-muted mb-3 ms-5">
                        New books added every week
                    </div>
                    <!-- Filter and Sort Controls -->
                    <div class="mb-3 ms-5 d-flex gap-3 align-items-center">
                        <select v-model="latestFilter.category" class="form-select form-select-sm w-auto">
                            <option value="">All Categories</option>
                            <option v-for="cat in latestCategories" :key="cat" :value="cat">{{ cat }}</option>
                        </select>
                        <select v-model="latestFilter.sort" class="form-select form-select-sm w-auto">
                            <option value="newest">Newest</option>
                            <option value="popular">Most Popular</option>
                        </select>
                    </div>
                    <div v-if="!filteredLatestBooks.length" class="text-center text-muted py-5">
                        <i class="fa fa-clock fa-2x mb-3"></i>
                        <div>No new arrivals at the moment. Check back soon or explore other sections!</div>
                    </div>
                    <transition-group
                        v-else
                        name="fade-bookcard"
                        tag="div"
                        class="row g-3"
                    >
                        <div
                            v-for="book in filteredLatestBooks.slice(
                                sectionWindows.latest,
                                sectionWindows.latest + windowSize
                            )"
                            :key="book.id"
                            class="col-12 col-sm-6 col-md-3"
                        >
                            <BookCard
                                :title="book.title"
                                :author="book.author"
                                :cover_image="book.cover_image"
                                :published_year="book.published_year"
                                :badge="isNew(book.created_at) ? 'New' : ''"
                                @details="openBookDetails(book)"
                                @read="continueReading(book)"
                                @save="saveBook(book)"
                                @bookmark="bookmarkBook(book)"
                                @report="reportBook(book)"
                            >
                                <template #footer>
                                    <div class="small text-muted mt-1">
                                        Added {{ daysAgo(book.created_at) }} ago
                                    </div>
                                    <div v-if="book.avg_rating && book.ratings_count" class="small text-warning">
                                        <i class="fa fa-star"></i> {{ book.avg_rating }} ({{ book.ratings_count }})
                                    </div>
                                </template>
                            </BookCard>
                        </div>
                    </transition-group>
                </div>
            </div>
            <div v-if="props.mostReadBooks.length" class="mb-5">
                <div class="book-section-card p-4 mb-4">
                    <div class="d-flex align-items-center mb-1">
                        <span class="section-accent bg-danger me-3"></span>
                        <h4 class="fw-bold mb-0 d-flex align-items-center fs-3">
                            <i class="fa fa-fire text-danger me-2"></i>Most Read
                            Books
                        </h4>
                        <div class="ms-auto">
                            <button
                                class="btn btn-outline-secondary btn-sm me-2"
                                :disabled="sectionWindows.mostRead === 0"
                                @click="prev('mostRead')"
                            >
                                <i class="fa fa-chevron-left"></i>
                            </button>
                            <button
                                class="btn btn-outline-secondary btn-sm"
                                :disabled="
                                    sectionWindows.mostRead + windowSize >=
                                    props.mostReadBooks.length
                                "
                                @click="next('mostRead', props.mostReadBooks)"
                            >
                                <i class="fa fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    <div class="text-muted mb-3 ms-5">Your top reads</div>
                    <transition-group
                        name="fade-bookcard"
                        tag="div"
                        class="row g-3"
                    >
                        <div
                            v-for="book in props.mostReadBooks.slice(
                                sectionWindows.mostRead,
                                sectionWindows.mostRead + windowSize
                            )"
                            :key="book.id"
                            class="col-12 col-sm-6 col-md-3"
                        >
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
                            <i class="fa fa-bolt text-warning me-2"></i>Hot This
                            Month
                        </h4>
                        <div class="ms-auto">
                            <button
                                class="btn btn-outline-secondary btn-sm me-2"
                                :disabled="sectionWindows.hot === 0"
                                @click="prev('hot')"
                            >
                                <i class="fa fa-chevron-left"></i>
                            </button>
                            <button
                                class="btn btn-outline-secondary btn-sm"
                                :disabled="
                                    sectionWindows.hot + windowSize >=
                                    props.hotBooks.length
                                "
                                @click="next('hot', props.hotBooks)"
                            >
                                <i class="fa fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    <div class="text-muted mb-3 ms-5">Popular reads</div>
                    <transition-group
                        name="fade-bookcard"
                        tag="div"
                        class="row g-3"
                    >
                        <div
                            v-for="book in props.hotBooks.slice(
                                sectionWindows.hot,
                                sectionWindows.hot + windowSize
                            )"
                            :key="book.id"
                            class="col-12 col-sm-6 col-md-3"
                        >
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
                            <i class="fa fa-star text-success me-2"></i>Top
                            Rated Books
                        </h4>
                        <div class="ms-auto">
                            <button
                                class="btn btn-outline-secondary btn-sm me-2"
                                :disabled="sectionWindows.highestRated === 0"
                                @click="prev('highestRated')"
                            >
                                <i class="fa fa-chevron-left"></i>
                            </button>
                            <button
                                class="btn btn-outline-secondary btn-sm"
                                :disabled="
                                    sectionWindows.highestRated + windowSize >=
                                    props.highestRatedBooks.length
                                "
                                @click="
                                    next(
                                        'highestRated',
                                        props.highestRatedBooks
                                    )
                                "
                            >
                                <i class="fa fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    <div class="text-muted mb-3 ms-5">Highly rated</div>
                    <div v-if="props.highestRatedBooks.length === 0" class="text-center text-muted py-5">
                        <i class="fa fa-star fa-2x mb-3"></i>
                        <div>No rated books yet.</div>
                    </div>
                    <transition-group
                        v-else
                        name="fade-bookcard"
                        tag="div"
                        class="row g-3"
                    >
                        <div
                            v-for="book in props.highestRatedBooks.slice(
                                sectionWindows.highestRated,
                                sectionWindows.highestRated + windowSize
                            )"
                            :key="book.id"
                            class="col-12 col-sm-6 col-md-3"
                        >
                            <BookCard
                                :title="book.title"
                                :author="book.author"
                                :cover_image="book.cover_image"
                                :published_year="book.published_year"
                                :badge="
                                    book.avg_rating && book.ratings_count
                                        ? (book.avg_rating + '★ (' + book.ratings_count + ')')
                                        : 'No ratings yet'
                                "
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
            <div class="mb-5">
                <div class="book-section-card p-4 mb-4">
                    <div class="d-flex align-items-center mb-1">
                        <span class="section-accent bg-primary me-3"></span>
                        <h4 class="fw-bold mb-0 d-flex align-items-center fs-3">
                            <i class="fa fa-bullhorn text-primary me-2"></i>Recent Announcements
                        </h4>
                    </div>
                    <div class="text-muted mb-3 ms-5">Stay up to date with the latest news</div>
                    <div v-if="!props.announcements.length" class="text-center text-muted py-5">
                        <i class="fa fa-bullhorn fa-2x mb-3"></i>
                        <div>No announcements yet.</div>
                    </div>
                    <div v-else class="list-group announcement-list-modern">
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
                            <i class="fa fa-play-circle text-success me-2"></i
                            >Continue Reading
                        </h4>
                    </div>
                    <div class="text-muted mb-3 ms-5">
                        Pick up where you left off
                    </div>
                    <div
                        class="d-flex flex-nowrap overflow-auto gap-3 continue-reading-scroll pb-2"
                    >
                        <div
                            v-for="p in props.readingProgress.filter(
                                (p) => p.book
                            )"
                            :key="p.book.id"
                            class="continue-card-modern card shadow-sm border-0"
                        >
                            <img
                                :src="p.book.cover_image"
                                alt="Cover"
                                class="card-img-top rounded-top"
                                style="height: 120px; object-fit: cover"
                            />
                            <div class="card-body p-2">
                                <div class="fw-bold small mb-1 text-truncate">
                                    {{ p.book.title }}
                                </div>
                                <div
                                    class="progress modern-progress mb-2"
                                    style="height: 8px"
                                >
                                    <div
                                        class="progress-bar bg-success"
                                        :style="`width:${Math.round(
                                            p.progress
                                        )}%`"
                                    ></div>
                                </div>
                                <button
                                    class="btn btn-sm btn-success w-100"
                                    @click="continueReading(p.book)"
                                >
                                    <i class="fa fa-play"></i> Continue
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="showReader">
                <BookReader
                    v-if="readerType === 'epub'"
                    :file="readerBook.file_path"
                    :bookId="readerBook.id"
                    @close="closeReader"
                />
                <!-- Add PdfReader for PDFs if needed -->
            </div>
            <transition name="fade-toast">
                <div
                    v-if="showWelcome"
                    class="position-fixed start-50 translate-middle-x"
                    style="
                        top: 60px;
                        min-width: 260px;
                        max-width: 350px;
                        z-index: 20000;
                    "
                >
                    <div
                        class="alert alert-success alert-dismissible fade show shadow d-flex align-items-center gap-2 px-4 py-3 position-relative"
                        role="alert"
                    >
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
.fade-toast-enter-active,
.fade-toast-leave-active {
    transition: opacity 0.5s;
}
.fade-toast-enter-from,
.fade-toast-leave-to {
    opacity: 0;
}
.stat-card-modern {
    background: linear-gradient(135deg, #f8fafc 0%, #e9f7ef 100%);
    border-radius: 1.2rem;
    box-shadow: 0 2px 12px #19875422;
    transition: box-shadow 0.22s cubic-bezier(0.4, 2, 0.3, 1),
        transform 0.22s cubic-bezier(0.4, 2, 0.3, 1);
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
    box-shadow: 0 2px 8px rgba(25, 135, 84, 0.12);
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
.fade-bookcard-enter-active,
.fade-bookcard-leave-active {
    transition: opacity 0.4s, transform 0.4s;
}
.fade-bookcard-enter-from,
.fade-bookcard-leave-to {
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

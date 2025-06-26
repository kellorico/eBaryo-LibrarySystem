<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { ref, computed } from "vue";
import { Bar } from "vue-chartjs";
import {
    Chart,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
} from "chart.js";

Chart.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);

const props = defineProps({
    topReadBooks: Array,
    topBookmarkedBooks: Array,
    topRatedBooks: Array,
    topBooksThisMonth: Array,
    mostActiveUsers: Array,
});

// State for live refresh
const topReadBooks = ref([...(props.topReadBooks ?? [])]);
const topBookmarkedBooks = ref([...(props.topBookmarkedBooks ?? [])]);
const topRatedBooks = ref([...(props.topRatedBooks ?? [])]);
const topBooksThisMonth = ref([...(props.topBooksThisMonth ?? [])]);
const mostActiveUsers = ref([...(props.mostActiveUsers ?? [])]);
const loading = ref(false);

// Date range filter
const dateRange = ref("month"); // 'month', '30days', 'all'
const dateRangeOptions = [
    { value: "month", label: "This Month" },
    { value: "30days", label: "Last 30 Days" },
    { value: "all", label: "All Time" },
];

async function refreshAnalytics() {
    loading.value = true;
    let url = "/analytics/data?range=" + dateRange.value;
    const res = await fetch(url);
    const data = await res.json();
    topReadBooks.value = data.topReadBooks || [];
    topBookmarkedBooks.value = data.topBookmarkedBooks || [];
    topRatedBooks.value = data.topRatedBooks || [];
    topBooksThisMonth.value = data.topBooksThisMonth || [];
    mostActiveUsers.value = data.mostActiveUsers || [];
    loading.value = false;
}

function exportCSV(rows, headers, filename) {
    rows = rows ?? [];
    const csv = [headers.join(",")]
        .concat(
            rows.map((row) =>
                headers.map((h) => '"' + (row[h] ?? "") + '"').join(",")
            )
        )
        .join("\n");
    const blob = new Blob([csv], { type: "text/csv" });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Summary cards
const totalReads = computed(() =>
    topReadBooks.value.reduce((a, b) => a + (b.read_count || 0), 0)
);
const totalBookmarks = computed(() =>
    topBookmarkedBooks.value.reduce((a, b) => a + (b.bookmarks || 0), 0)
);
const totalRatings = computed(() =>
    topRatedBooks.value.reduce((a, b) => a + (b.ratings_count || 0), 0)
);
const totalActiveUsers = computed(() => mostActiveUsers.value.length);

// Chart data/computed for each section
const readBooksChart = computed(() => ({
    labels: topReadBooks.value.map((b) => b.title),
    datasets: [
        {
            label: "Reads",
            backgroundColor: "#198754",
            data: topReadBooks.value.map((b) => b.read_count),
        },
    ],
}));
const bookmarkedBooksChart = computed(() => ({
    labels: topBookmarkedBooks.value.map((b) => b.title),
    datasets: [
        {
            label: "Bookmarks",
            backgroundColor: "#0d6efd",
            data: topBookmarkedBooks.value.map((b) => b.bookmarks),
        },
    ],
}));
const ratedBooksChart = computed(() => ({
    labels: topRatedBooks.value.map((b) => b.title),
    datasets: [
        {
            label: "Avg. Rating",
            backgroundColor: "#fd7e14",
            data: topRatedBooks.value.map((b) => b.avg_rating),
        },
    ],
}));
const booksThisMonthChart = computed(() => ({
    labels: topBooksThisMonth.value.map((b) => b.title),
    datasets: [
        {
            label: "Reads (This Month)",
            backgroundColor: "#0dcaf0",
            data: topBooksThisMonth.value.map((b) => b.read_count),
        },
    ],
}));
const activeUsersChart = computed(() => ({
    labels: mostActiveUsers.value.map((u) => u.name),
    datasets: [
        {
            label: "Activity Score",
            backgroundColor: "#6c757d",
            data: mostActiveUsers.value.map((u) => u.activity_score),
        },
    ],
}));

const chartOptions = {
    responsive: true,
    plugins: {
        legend: { display: false },
        title: { display: false },
    },
    scales: {
        y: { beginAtZero: true },
    },
};
</script>

<template>
    <AdminLayout>
        <Head title="Library Analytics" />
        <div class="container">
            <div
                class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2"
            >
                <h2 class="fw-bold mb-0 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-chart-bar text-success"></i>
                    Library Analytics Dashboard
                </h2>
                <div class="d-flex gap-2 align-items-center">
                    <select
                        v-model="dateRange"
                        class="form-select form-select-sm"
                        style="width: 160px"
                        @change="refreshAnalytics"
                    >
                        <option
                            v-for="opt in dateRangeOptions"
                            :key="opt.value"
                            :value="opt.value"
                        >
                            {{ opt.label }}
                        </option>
                    </select>
                    <button
                        class="btn btn-outline-success d-flex align-items-center gap-2"
                        :disabled="loading"
                        @click="refreshAnalytics"
                    >
                        <span
                            v-if="loading"
                            class="spinner-border spinner-border-sm"
                        ></span>
                        <i v-else class="fa fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
            <!-- Summary Cards -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card text-center shadow-sm">
                        <div class="card-body py-3">
                            <div class="fs-3 text-success">
                                <i class="fa fa-book-open"></i>
                            </div>
                            <div class="fw-bold fs-5">{{ totalReads }}</div>
                            <div class="text-muted small">Total Reads</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card text-center shadow-sm">
                        <div class="card-body py-3">
                            <div class="fs-3 text-primary">
                                <i class="fa fa-bookmark"></i>
                            </div>
                            <div class="fw-bold fs-5">{{ totalBookmarks }}</div>
                            <div class="text-muted small">Total Bookmarks</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card text-center shadow-sm">
                        <div class="card-body py-3">
                            <div class="fs-3 text-warning">
                                <i class="fa fa-star"></i>
                            </div>
                            <div class="fw-bold fs-5">{{ totalRatings }}</div>
                            <div class="text-muted small">Total Ratings</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card text-center shadow-sm">
                        <div class="card-body py-3">
                            <div class="fs-3 text-secondary">
                                <i class="fa fa-users"></i>
                            </div>
                            <div class="fw-bold fs-5">
                                {{ totalActiveUsers }}
                            </div>
                            <div class="text-muted small">Active Users</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card shadow-sm mb-4">
                        <div
                            class="card-header bg-success text-white fw-bold d-flex align-items-center gap-2"
                        >
                            <i
                                class="fa-solid fa-book-open"
                                title="Most read books"
                            ></i>
                            Top Read Books
                            <button
                                class="btn btn-sm btn-outline-light ms-auto"
                                @click="
                                    exportCSV(
                                        topReadBooks.value,
                                        ['title', 'read_count'],
                                        'top-read-books.csv'
                                    )
                                "
                                title="Export as CSV"
                            >
                                <i class="fa fa-download"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <Bar
                                :data="readBooksChart"
                                :options="chartOptions"
                                style="max-height: 260px"
                            />
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>
                                            <span title="Number of times read"
                                                >Reads</span
                                            >
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(book, i) in topReadBooks.value"
                                        :key="book.title"
                                    >
                                        <td>{{ i + 1 }}</td>
                                        <td>{{ book.title }}</td>
                                        <td>{{ book.read_count }}</td>
                                    </tr>
                                    <tr
                                        v-if="
                                            (topReadBooks.value || [])
                                                .length === 0
                                        "
                                    >
                                        <td
                                            colspan="3"
                                            class="text-center text-muted"
                                        >
                                            No data
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card shadow-sm mb-4">
                        <div
                            class="card-header bg-primary text-white fw-bold d-flex align-items-center gap-2"
                        >
                            <i
                                class="fa-solid fa-bookmark"
                                title="Most bookmarked books"
                            ></i>
                            Top Bookmarked Books
                            <button
                                class="btn btn-sm btn-outline-light ms-auto"
                                @click="
                                    exportCSV(
                                        topBookmarkedBooks.value,
                                        ['title', 'bookmarks'],
                                        'top-bookmarked-books.csv'
                                    )
                                "
                                title="Export as CSV"
                            >
                                <i class="fa fa-download"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <Bar
                                :data="bookmarkedBooksChart"
                                :options="chartOptions"
                                style="max-height: 260px"
                            />
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>
                                            <span title="Number of bookmarks"
                                                >Bookmarks</span
                                            >
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(
                                            book, i
                                        ) in topBookmarkedBooks.value"
                                        :key="book.title"
                                    >
                                        <td>{{ i + 1 }}</td>
                                        <td>{{ book.title }}</td>
                                        <td>{{ book.bookmarks }}</td>
                                    </tr>
                                    <tr
                                        v-if="
                                            (topBookmarkedBooks.value || [])
                                                .length === 0
                                        "
                                    >
                                        <td
                                            colspan="3"
                                            class="text-center text-muted"
                                        >
                                            No data
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow-sm mb-4">
                        <div
                            class="card-header bg-warning text-dark fw-bold d-flex align-items-center gap-2"
                        >
                            <i
                                class="fa-solid fa-star"
                                title="Highest rated books"
                            ></i>
                            Top Rated Books
                            <button
                                class="btn btn-sm btn-outline-light ms-auto"
                                @click="
                                    exportCSV(
                                        topRatedBooks.value,
                                        [
                                            'title',
                                            'avg_rating',
                                            'ratings_count',
                                        ],
                                        'top-rated-books.csv'
                                    )
                                "
                                title="Export as CSV"
                            >
                                <i class="fa fa-download"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <Bar
                                :data="ratedBooksChart"
                                :options="chartOptions"
                                style="max-height: 260px"
                            />
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>
                                            <span title="Average user rating"
                                                >Avg. Rating</span
                                            >
                                        </th>
                                        <th>
                                            <span title="Number of ratings"
                                                >Ratings</span
                                            >
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(book, i) in topRatedBooks.value"
                                        :key="book.title"
                                    >
                                        <td>{{ i + 1 }}</td>
                                        <td>{{ book.title }}</td>
                                        <td>{{ book.avg_rating }}</td>
                                        <td>{{ book.ratings_count }}</td>
                                    </tr>
                                    <tr
                                        v-if="
                                            (topRatedBooks.value || [])
                                                .length === 0
                                        "
                                    >
                                        <td
                                            colspan="4"
                                            class="text-center text-muted"
                                        >
                                            No data
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card shadow-sm mb-4">
                        <div
                            class="card-header bg-info text-white fw-bold d-flex align-items-center gap-2"
                        >
                            <i
                                class="fa-solid fa-calendar-alt"
                                title="Most read books this month"
                            ></i>
                            Top Books This Month
                            <button
                                class="btn btn-sm btn-outline-light ms-auto"
                                @click="
                                    exportCSV(
                                        topBooksThisMonth.value,
                                        ['title', 'read_count'],
                                        'top-books-this-month.csv'
                                    )
                                "
                                title="Export as CSV"
                            >
                                <i class="fa fa-download"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <Bar
                                :data="booksThisMonthChart"
                                :options="chartOptions"
                                style="max-height: 260px"
                            />
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>
                                            <span title="Reads this month"
                                                >Reads</span
                                            >
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(
                                            book, i
                                        ) in topBooksThisMonth.value"
                                        :key="book.title"
                                    >
                                        <td>{{ i + 1 }}</td>
                                        <td>{{ book.title }}</td>
                                        <td>{{ book.read_count }}</td>
                                    </tr>
                                    <tr
                                        v-if="
                                            (topBooksThisMonth.value || [])
                                                .length === 0
                                        "
                                    >
                                        <td
                                            colspan="3"
                                            class="text-center text-muted"
                                        >
                                            No data
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card shadow-sm mb-4">
                        <div
                            class="card-header bg-secondary text-white fw-bold d-flex align-items-center gap-2"
                        >
                            <i
                                class="fa-solid fa-users"
                                title="Most active users"
                            ></i>
                            Most Active Users
                            <button
                                class="btn btn-sm btn-outline-light ms-auto"
                                @click="
                                    exportCSV(
                                        mostActiveUsers.value,
                                        [
                                            'name',
                                            'downloads',
                                            'bookmarks',
                                            'ratings',
                                            'activity_score',
                                        ],
                                        'most-active-users.csv'
                                    )
                                "
                                title="Export as CSV"
                            >
                                <i class="fa fa-download"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <Bar
                                :data="activeUsersChart"
                                :options="chartOptions"
                                style="max-height: 260px"
                            />
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>
                                            <span title="Books downloaded"
                                                >Downloads</span
                                            >
                                        </th>
                                        <th>
                                            <span title="Books bookmarked"
                                                >Bookmarks</span
                                            >
                                        </th>
                                        <th>
                                            <span title="Books rated"
                                                >Ratings</span
                                            >
                                        </th>
                                        <th>
                                            <span title="Total activity score"
                                                >Activity Score</span
                                            >
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(
                                            user, i
                                        ) in mostActiveUsers.value"
                                        :key="user.id"
                                    >
                                        <td>{{ i + 1 }}</td>
                                        <td>{{ user.name }}</td>
                                        <td>{{ user.downloads }}</td>
                                        <td>{{ user.bookmarks }}</td>
                                        <td>{{ user.ratings }}</td>
                                        <td>{{ user.activity_score }}</td>
                                    </tr>
                                    <tr
                                        v-if="
                                            (mostActiveUsers.value || [])
                                                .length === 0
                                        "
                                    >
                                        <td
                                            colspan="6"
                                            class="text-center text-muted"
                                        >
                                            No data
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

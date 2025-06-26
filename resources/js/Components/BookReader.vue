<!-- resources/js/Components/BookReader.vue -->
<template>
    <div class="reader-container" ref="readerContainer">
        <div id="epub-reader" class="h-100 w-100"></div>
        <div class="controls">
            <button class="btn btn-secondary" @click="prevPage">‹ Prev</button>
            <button class="btn btn-secondary" @click="nextPage">Next ›</button>
            <button class="btn btn-danger" @click="$emit('close')">
                Close
            </button>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref, watch } from "vue";
import ePub from "epubjs";

const props = defineProps({
    file: String, // EPUB file path (relative to /storage/)
    bookId: Number,
});

const readerContainer = ref(null);
let rendition = null;
let book = null;
const currentPage = ref(0);

const nextPage = () => {
    rendition?.next();
};

const prevPage = () => {
    rendition?.prev();
};

function saveProgress(page) {
    if (!props.bookId) return;
    fetch(`/books/${props.bookId}/progress`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ progress: page })
    });
}

onMounted(() => {
    book = ePub(`/storage/${props.file}`);
    rendition = book.renderTo("epub-reader", {
        width: "100%",
        height: "100%",
    });
    // Fetch last progress
    if (props.bookId) {
        fetch('/books/progress')
            .then(res => res.json())
            .then(data => {
                const prog = (data.progress || []).find(p => p.book_id === props.bookId);
                if (prog && prog.progress) {
                    book.ready.then(() => {
                        rendition.display(prog.progress);
                    });
                } else {
                    rendition.display();
                }
            });
    } else {
        rendition.display();
    }
    // Save progress on page change
    rendition.on('relocated', (location) => {
        const cfi = location.start.cfi;
        saveProgress(cfi);
    });
});
</script>

<style scoped>
.reader-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    z-index: 2000;
    background: #fff;
    display: flex;
    flex-direction: column;
}
.controls {
    position: absolute;
    top: 1rem;
    right: 1rem;
    display: flex;
    gap: 0.5rem;
}
</style>

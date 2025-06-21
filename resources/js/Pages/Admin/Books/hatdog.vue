<template>
    <div
            class="modal fade"
            id="bookDetailsModal"
            tabindex="-1"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content shadow">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ selectedBook?.title }}</h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                        />
                    </div>
                    <div
                        class="modal-body d-flex gap-3 flex-column flex-md-row"
                    >
                        <div class="text-center">
                            <img
                                :src="selectedBook?.cover_image"
                                alt="Book Cover"
                                class="img-fluid rounded shadow"
                                style="max-height: 300px"
                            />
                        </div>
                        <div>
                            <p>
                                <strong>Author:</strong>
                                {{ selectedBook?.author }}
                            </p>
                            <p>
                                <strong>Published Year:</strong>
                                {{ selectedBook?.published_year }}
                            </p>
                            <p><strong>Description:</strong></p>
                            <p>{{ selectedBook?.description }}</p>

                            <div class="mt-3 d-flex flex-wrap gap-2">
                                <button class="btn btn-primary">
                                    <i class="fas fa-book-reader me-1"></i> Read
                                </button>
                                <a
                                    :href="
                                        '/storage/' + selectedBook?.file_path
                                    "
                                    class="btn btn-success"
                                    download
                                >
                                    <i class="fas fa-download me-1"></i>
                                    Download
                                </a>
                                <button class="btn btn-warning">
                                    <i class="fas fa-bookmark me-1"></i> Save
                                </button>
                                <button class="btn btn-info text-white">
                                    <i class="fas fa-share me-1"></i> Share
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</template>
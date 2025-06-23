<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Link } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";

const props = defineProps({
    success: String,
});

const showSuccessLogout = ref(!!props.success);


onMounted(() => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add("animate-fade-in");
                observer.unobserve(entry.target);
            }
        });
    });
    document
        .querySelectorAll(".animate-on-scroll")
        .forEach((el) => observer.observe(el));
});

onMounted(() => {
    if (showSuccessLogout.value) {
        setTimeout(() => {
            showSuccessLogout.value = false;
        }, 5000);
    }
});
</script>

<template>
    <GuestLayout>
        <!-- Floating Welcome Message -->
        <div
            v-if="showSuccessLogout"
            class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3 shadow zindex-tooltip"
            style="z-index: 1055; max-width: 90%"
            role="alert"
        >
            <i class="fa-solid fa-smile-beam me-2"></i>
            {{ success }}
            <button
                type="button"
                class="btn-close"
                @click="showSuccessLogout = false"
                aria-label="Close"
            ></button>
        </div>

        <!-- Hero Section -->
        <section
            class="welcome-hero d-flex align-items-center justify-content-center text-center position-relative overflow-hidden"
        >
            <div class="floating-icons">
                <i
                    class="fa-solid fa-book book-float book-float-1 text-success"
                    aria-hidden="true"
                ></i>
                <i
                    class="fa-solid fa-seedling book-float book-float-2 text-success"
                    aria-hidden="true"
                ></i>
                <i
                    class="fa-solid fa-graduation-cap book-float book-float-3 text-success"
                    aria-hidden="true"
                ></i>
                <i
                    class="fa-solid fa-landmark book-float book-float-4 text-success"
                    aria-hidden="true"
                ></i>
            </div>

            <div class="hero-content">
                <div class="mb-4">
                    <span
                        class="hero-icon bg-white bg-opacity-75 rounded-circle shadow-sm mb-3 animate-pop-in"
                    >
                        <i
                            class="fa-solid fa-book fa-3x text-success"
                            aria-hidden="true"
                        ></i>
                    </span>
                    <h1 class="display-3 fw-bold mb-2 animate-slide-up">
                        Welcome to
                        <span class="text-success">eBaryo Library</span>
                    </h1>
                    <p
                        class="lead mb-4 animate-slide-up"
                        style="animation-delay: 0.2s"
                    >
                        Empowering communities through accessible digital books
                        and resources.
                    </p>
                </div>

                <div
                    class="d-flex flex-column flex-sm-row justify-content-center gap-4 animate-slide-up"
                    style="animation-delay: 0.4s"
                >
                    <Link
                        href="/login"
                        class="btn btn-success btn-lg px-5 shadow-sm btn-glow"
                        >Login</Link
                    >
                    <Link
                        href="/register"
                        class="btn btn-outline-success btn-lg px-5 shadow-sm btn-glow"
                        >Register</Link
                    >
                </div>
            </div>

            <!-- Scroll Down Indicator -->
            <div class="scroll-indicator d-none d-md-block" aria-hidden="true">
                <span class="mouse"><span class="wheel"></span></span>
                <span class="arrow"></span>
            </div>
        </section>

        <!-- Features Section -->
        <section class="container-fluid py-5">
            <div
                class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4 text-center"
            >
                <div class="col animate-on-scroll">
                    <div
                        class="feature-card p-4 h-100 rounded-4 shadow-sm bg-white"
                    >
                        <img
                            src="/book_icons/story_books.png"
                            alt="Story Books"
                            class="mb-3"
                            style="width: 48px; height: 48px"
                        />
                        <h5 class="fw-semibold mb-2">Story Books</h5>
                        <p class="text-muted small mb-0">
                            Enjoy a variety of story books for all ages.
                        </p>
                    </div>
                </div>
                <div class="col animate-on-scroll">
                    <div
                        class="feature-card p-4 h-100 rounded-4 shadow-sm bg-white"
                    >
                        <img
                            src="/book_icons/educational.png"
                            alt="Educational Books"
                            class="mb-3"
                            style="width: 48px; height: 48px"
                        />
                        <h5 class="fw-semibold mb-2">Educational</h5>
                        <p class="text-muted small mb-0">
                            Access textbooks and learning materials.
                        </p>
                    </div>
                </div>
                <div class="col animate-on-scroll">
                    <div
                        class="feature-card p-4 h-100 rounded-4 shadow-sm bg-white"
                    >
                        <img
                            src="/book_icons/agriculture.png"
                            alt="Agriculture & Livelihood"
                            class="mb-3"
                            style="width: 48px; height: 48px"
                        />
                        <h5 class="fw-semibold mb-2">Agriculture</h5>
                        <p class="text-muted small mb-0">
                            Resources for sustainable farming and livelihood.
                        </p>
                    </div>
                </div>
                <div class="col animate-on-scroll">
                    <div
                        class="feature-card p-4 h-100 rounded-4 shadow-sm bg-white"
                    >
                        <img
                            src="/book_icons/cultural.png"
                            alt="Cultural Heritage"
                            class="mb-3"
                            style="width: 48px; height: 48px"
                        />
                        <h5 class="fw-semibold mb-2">Cultural Heritage</h5>
                        <p class="text-muted small mb-0">
                            Preserve and explore local culture and history.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Choose Us -->
        <section class="why-choose-us-section py-5 bg-light">
            <div class="container">
                <h2 class="text-center fw-bold mb-5 animate-on-scroll">
                    Why Choose <span class="text-success">eBaryo Library</span>?
                </h2>
                <div class="row g-4 justify-content-center">
                    <div class="col-12 col-md-4 animate-on-scroll">
                        <div
                            class="choose-card p-4 rounded-4 shadow-sm h-100 text-center bg-white"
                        >
                            <i
                                class="fa-solid fa-globe fa-2x text-success mb-3"
                                aria-hidden="true"
                            ></i>
                            <h5 class="fw-semibold mb-2">
                                Accessible Anywhere
                            </h5>
                            <p class="text-muted small mb-0">
                                Read and learn from any device, anytime,
                                anywhere.
                            </p>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 animate-on-scroll">
                        <div
                            class="choose-card p-4 rounded-4 shadow-sm h-100 text-center bg-white"
                        >
                            <i
                                class="fa-solid fa-users fa-2x text-success mb-3"
                                aria-hidden="true"
                            ></i>
                            <h5 class="fw-semibold mb-2">Community Driven</h5>
                            <p class="text-muted small mb-0">
                                Built for and by the community, supporting local
                                growth.
                            </p>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 animate-on-scroll">
                        <div
                            class="choose-card p-4 rounded-4 shadow-sm h-100 text-center bg-white"
                        >
                            <i
                                class="fa-solid fa-shield-halved fa-2x text-success mb-3"
                                aria-hidden="true"
                            ></i>
                            <h5 class="fw-semibold mb-2">Safe & Secure</h5>
                            <p class="text-muted small mb-0">
                                Your data and reading experience are always
                                protected.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>

<style scoped>
/* Root */
:root {
    --success: #198754;
    --bg-light: #f8f9fa;
}

/* Hero */
.welcome-hero {
    min-height: 65vh;
    background: linear-gradient(135deg, #e9f7ef 0%, #d4edda 100%);
    position: relative;
    padding-top: 120px;
    padding-bottom: 60px;
    overflow: hidden;
}
.welcome-hero::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(
        to bottom,
        rgba(255, 255, 255, 0.9),
        rgba(255, 255, 255, 0.6)
    );
    backdrop-filter: blur(4px);
    z-index: 0;
}
.hero-content {
    max-width: 1140px;
    margin: 0 auto;
    z-index: 2;
    position: relative;
    /* backdrop-filter: blur(6px); */
    padding: 3rem;
    border-radius: 1rem;
    background-color: rgba(255, 255, 255, 0.6);
}
.hero-icon {
    width: 80px;
    height: 80px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 16px rgba(40, 167, 69, 0.08);
}

/* Button Effects */
.btn-glow {
    transition: all 0.3s ease;
}
.btn-glow:hover {
    box-shadow: 0 0 20px rgba(40, 167, 69, 0.3);
    transform: translateY(-2px);
}

/* Floating Icons */
.floating-icons {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1;
}
.book-float {
    position: absolute;
    opacity: 0.13;
    font-size: 3rem;
    animation: floatY 7s ease-in-out infinite;
}
.book-float-1 {
    left: 10%;
    top: 20%;
    animation-delay: 0s;
}
.book-float-2 {
    left: 80%;
    top: 30%;
    animation-delay: 1.5s;
}
.book-float-3 {
    left: 20%;
    top: 70%;
    animation-delay: 3s;
}
.book-float-4 {
    left: 70%;
    top: 80%;
    animation-delay: 4.5s;
}
@keyframes floatY {
    0%,
    100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-30px);
    }
}

/* Scroll Indicator */
.scroll-indicator {
    position: absolute;
    left: 50%;
    bottom: 24px;
    transform: translateX(-50%);
    z-index: 3;
    display: flex;
    flex-direction: column;
    align-items: center;
    opacity: 0.7;
}
.mouse {
    width: 28px;
    height: 44px;
    border: 2px solid var(--success);
    border-radius: 16px;
    position: relative;
    margin-bottom: 6px;
}
.wheel {
    width: 4px;
    height: 10px;
    background: var(--success);
    border-radius: 2px;
    position: absolute;
    left: 50%;
    top: 8px;
    transform: translateX(-50%);
    animation: wheelMove 1.2s infinite;
}
@keyframes wheelMove {
    0% {
        opacity: 0;
        top: 8px;
    }
    30% {
        opacity: 1;
    }
    100% {
        opacity: 0;
        top: 22px;
    }
}
.arrow {
    width: 0;
    height: 0;
    border-left: 8px solid transparent;
    border-right: 8px solid transparent;
    border-top: 12px solid var(--success);
    animation: bounce 1.6s infinite;
}
@keyframes bounce {
    0%,
    100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(6px);
    }
}

/* Cards Hover */
.feature-card img {
    background: #eafaf1;
    padding: 10px;
    border-radius: 50%;
    transition: transform 0.3s ease;
}
.feature-card:hover img {
    transform: scale(1.1);
}
.feature-card,
.choose-card {
    transition: transform 0.2s, box-shadow 0.2s;
    min-height: 250px;
}
.feature-card:hover,
.choose-card:hover {
    transform: translateY(-6px) scale(1.03);
    box-shadow: 0 8px 24px rgba(40, 167, 69, 0.1);
    z-index: 2;
}

/* Animations */
.animate-fade-in {
    animation: fadeIn 1s both;
}
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}
.animate-pop-in {
    animation: popIn 0.7s both;
}
@keyframes popIn {
    0% {
        opacity: 0;
        transform: scale(0.7);
    }
    80% {
        opacity: 1;
        transform: scale(1.05);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}
.animate-slide-up {
    animation: slideUp 0.8s both;
}
@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(40px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Mobile Responsive */
@media (max-width: 767.98px) {
    .welcome-hero {
        padding-top: 80px;
        padding-bottom: 30px;
    }
    .display-3 {
        font-size: 3rem;
    }
    .hero-icon {
        width: 60px;
        height: 60px;
    }
}
</style>

<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { onMounted } from "vue";

// Animate on scroll
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
</script>

<template>
    <GuestLayout>
        <Head title="Welcome to eBaryo Library" />
        <!-- Hero Section -->
        <section
            class="welcome-hero d-flex align-items-center justify-content-center text-center position-relative overflow-hidden"
        >
            <div class="hero-bg-pattern"></div>
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

            <div
                class="hero-content shadow-lg rounded-5 bg-white-green animate-fade-in"
            >
                <div class="mb-4">
                    <span
                        class="hero-icon bg-white bg-opacity-75 rounded-circle shadow-sm mb-3 animate-pop-in hero-icon-glow"
                    >
                        <i
                            class="fa-solid fa-book fa-3x text-success"
                            aria-hidden="true"
                        ></i>
                    </span>
                    <h1
                        class="display-3 fw-bold mb-2 animate-slide-up text-success"
                    >
                        Welcome to
                        <span class="text-success">eBaryo Library</span>
                    </h1>
                    <p
                        class="lead mb-4 animate-slide-up text-green-muted"
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
                        class="btn btn-success btn-lg px-5 shadow-sm btn-glow-green btn-ripple"
                        >Login</Link
                    >
                    <Link
                        href="/register"
                        class="btn btn-outline-success btn-lg px-5 shadow-sm btn-glow-green btn-ripple"
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
                        class="feature-card p-4 h-100 rounded-5 shadow-lg bg-white-green card-animate"
                    >
                        <img
                            src="/book_icons/story_books.png"
                            alt="Story Books"
                            class="mb-3"
                            style="width: 48px; height: 48px"
                        />
                        <h5 class="fw-semibold mb-2 text-success">
                            Story Books
                        </h5>
                        <p class="text-green-muted small mb-0">
                            Enjoy a variety of story books for all ages.
                        </p>
                    </div>
                </div>
                <div class="col animate-on-scroll">
                    <div
                        class="feature-card p-4 h-100 rounded-5 shadow-lg bg-white-green card-animate"
                    >
                        <img
                            src="/book_icons/educational.png"
                            alt="Educational Books"
                            class="mb-3"
                            style="width: 48px; height: 48px"
                        />
                        <h5 class="fw-semibold mb-2 text-success">
                            Educational
                        </h5>
                        <p class="text-green-muted small mb-0">
                            Access textbooks and learning materials.
                        </p>
                    </div>
                </div>
                <div class="col animate-on-scroll">
                    <div
                        class="feature-card p-4 h-100 rounded-5 shadow-lg bg-white-green card-animate"
                    >
                        <img
                            src="/book_icons/agriculture.png"
                            alt="Agriculture & Livelihood"
                            class="mb-3"
                            style="width: 48px; height: 48px"
                        />
                        <h5 class="fw-semibold mb-2 text-success">
                            Agriculture
                        </h5>
                        <p class="text-green-muted small mb-0">
                            Resources for sustainable farming and livelihood.
                        </p>
                    </div>
                </div>
                <div class="col animate-on-scroll">
                    <div
                        class="feature-card p-4 h-100 rounded-5 shadow-lg bg-white-green card-animate"
                    >
                        <img
                            src="/book_icons/cultural.png"
                            alt="Cultural Heritage"
                            class="mb-3"
                            style="width: 48px; height: 48px"
                        />
                        <h5 class="fw-semibold mb-2 text-success">
                            Cultural Heritage
                        </h5>
                        <p class="text-green-muted small mb-0">
                            Preserve and explore local culture and history.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Choose Us -->
        <section class="why-choose-us-section py-5 bg-light">
            <div class="container">
                <h2
                    class="text-center fw-bold mb-5 animate-on-scroll text-success"
                >
                    Why Choose <span class="text-success">eBaryo Library</span>?
                </h2>
                <div class="row g-4 justify-content-center">
                    <div class="col-12 col-md-4 animate-on-scroll">
                        <div
                            class="choose-card p-4 rounded-5 shadow-lg h-100 text-center bg-white-green card-animate"
                        >
                            <i
                                class="fa-solid fa-globe fa-2x text-success mb-3"
                                aria-hidden="true"
                            ></i>
                            <h5 class="fw-semibold mb-2 text-success">
                                Accessible Anywhere
                            </h5>
                            <p class="text-green-muted small mb-0">
                                Read and learn from any device, anytime,
                                anywhere.
                            </p>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 animate-on-scroll">
                        <div
                            class="choose-card p-4 rounded-5 shadow-lg h-100 text-center bg-white-green card-animate"
                        >
                            <i
                                class="fa-solid fa-users fa-2x text-success mb-3"
                                aria-hidden="true"
                            ></i>
                            <h5 class="fw-semibold mb-2 text-success">
                                Community Driven
                            </h5>
                            <p class="text-green-muted small mb-0">
                                Built for and by the community, supporting local
                                growth.
                            </p>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 animate-on-scroll">
                        <div
                            class="choose-card p-4 rounded-5 shadow-lg h-100 text-center bg-white-green card-animate"
                        >
                            <i
                                class="fa-solid fa-shield-halved fa-2x text-success mb-3"
                                aria-hidden="true"
                            ></i>
                            <h5 class="fw-semibold mb-2 text-success">
                                Safe & Secure
                            </h5>
                            <p class="text-green-muted small mb-0">
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
:root {
    --success: #198754;
    --bg-light: #f8f9fa;
}

.bg-white-green {
    background: linear-gradient(135deg, #f8fafc 0%, #e9f7ef 100%);
}

.text-green-muted {
    color: #198754cc;
}

.btn-glow-green {
    transition: all 0.3s cubic-bezier(0.4, 2, 0.3, 1);
    box-shadow: 0 2px 12px rgba(25, 135, 84, 0.08);
}
.btn-glow-green:hover {
    box-shadow: 0 0 20px rgba(40, 167, 69, 0.18);
    transform: translateY(-2px) scale(1.04);
}
.btn-ripple {
    position: relative;
    overflow: hidden;
}
.btn-ripple:active::after {
    content: "";
    position: absolute;
    left: 50%;
    top: 50%;
    width: 200%;
    height: 200%;
    background: rgba(25, 135, 84, 0.13);
    border-radius: 50%;
    transform: translate(-50%, -50%) scale(0);
    animation: ripple 0.5s linear;
    z-index: 1;
}
@keyframes ripple {
    to {
        transform: translate(-50%, -50%) scale(1);
        opacity: 0;
    }
}

.hero-bg-pattern {
    position: absolute;
    inset: 0;
    background: repeating-linear-gradient(
            135deg,
            #e9f7ef 0 2px,
            transparent 2px 40px
        ),
        linear-gradient(135deg, #e9f7ef 0%, #d4edda 100%);
    opacity: 0.5;
    z-index: 0;
    pointer-events: none;
}

.hero-icon-glow {
    box-shadow: 0 0 24px 0 #43c59e88, 0 2px 8px #19875422;
}

/* Hero */
.welcome-hero {
    min-height: 65vh;
    background: transparent;
    position: relative;
    padding-top: 120px;
    padding-bottom: 60px;
    overflow: hidden;
}

.hero-content {
    max-width: 1140px;
    margin: 0 auto;
    z-index: 2;
    position: relative;
    padding: 3rem;
    border-radius: 2rem;
    background-color: rgba(255, 255, 255, 0.6);
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

/* Cards Hover & Animation */
.feature-card,
.choose-card {
    transition: transform 0.22s cubic-bezier(0.4, 2, 0.3, 1),
        box-shadow 0.22s cubic-bezier(0.4, 2, 0.3, 1);
    min-height: 250px;
}
.card-animate {
    opacity: 0;
    transform: translateY(40px);
    animation: cardFadeIn 0.8s forwards;
}
@keyframes cardFadeIn {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.feature-card:hover,
.choose-card:hover {
    transform: translateY(-8px) scale(1.045);
    box-shadow: 0 12px 32px rgba(40, 167, 69, 0.13);
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
    .hero-content {
        padding: 1.2rem;
        border-radius: 1.2rem;
    }
    .feature-card,
    .choose-card {
        min-height: 180px;
    }
}
</style>

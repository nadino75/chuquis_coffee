<template>
    <div class="welcome-page">
        <nav class="navbar navbar-expand-lg fixed-top" :class="{ scrolled: isScrolled }">
            <div class="container">
                <router-link class="navbar-brand" to="/">
                    <span class="brand-icon">☕</span> Chuquis Coffee
                </router-link>
                <button class="navbar-toggler" type="button" @click="toggleMenu" data-bs-toggle="collapse" data-bs-target="#navbarNav" :class="{ active: menuOpen }">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" :class="{ show: menuOpen }" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="#menu" @click="closeMenu">Menú</a></li>
                        <li class="nav-item"><a class="nav-link" href="#contact" @click="closeMenu">Contacto</a></li>
                        <li class="nav-item" v-if="!user">
                            <router-link class="nav-link" :to="{ name: 'login' }" @click="closeMenu">Login</router-link>
                        </li>
                        <li class="nav-item" v-if="!user && hasRegister">
                            <router-link class="nav-link" :to="{ name: 'register' }" @click="closeMenu">Registro</router-link>
                        </li>
                        <li class="nav-item" v-if="user">
                            <router-link class="nav-link" :to="{ name: 'dashboard' }" @click="closeMenu">Dashboard</router-link>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div id="coffeeCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#coffeeCarousel" data-bs-slide-to="0" class="active" aria-current="true"></button>
                <button type="button" data-bs-target="#coffeeCarousel" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#coffeeCarousel" data-bs-slide-to="2"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="carousel-image-wrapper">
                        <img src="https://images.unsplash.com/photo-1509042239860-f550ce710b93?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" class="d-block w-100" alt="Café">
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="carousel-image-wrapper">
                        <img src="https://images.unsplash.com/photo-1559056199-641a0ac8b55e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" class="d-block w-100" alt="Ambiente">
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="carousel-image-wrapper">
                        <img src="https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" class="d-block w-100" alt="Especialidades">
                    </div>
                </div>
            </div>

            <!-- Hero Content Overlay -->
            <div class="hero-overlay"></div>
            <div class="hero-particles">
                <span v-for="n in 20" :key="n" class="particle" :style="particleStyle(n)"></span>
            </div>
            <div class="carousel-caption-wrapper hero-overlay-content">
                <div class="container hero-content">
                    <div class="hero-badge">Artisanal Coffee Experience</div>
                    <h1 class="hero-title">Bienvenido a <span class="text-gold">Chuquis</span> Coffee</h1>
                    <p class="hero-subtitle">Descubre el aroma perfecto en cada taza. Café artesanal, ambiente acogedor y momentos inolvidables.</p>
                    <div class="hero-actions">
                        <a href="#menu" class="btn btn-gold btn-lg">Ver Menú</a>
                        <a href="#contact" class="btn btn-outline-gold btn-lg">Contáctanos</a>
                    </div>
                    <div class="hero-scroll-indicator">
                        <span>Descubre más</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#coffeeCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#coffeeCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
                <span class="visually-hidden">Siguiente</span>
            </button>
        </div>

        <section id="menu" class="section-menu">
            <div class="container">
                <div class="section-header" data-aos="fade-up">
                    <span class="section-tag">Especialidades</span>
                    <h2 class="section-title">Nuestro Menú</h2>
                    <p class="section-description">Selección artesanal preparada con pasión</p>
                </div>
                <div class="row g-4">
                    <div class="col-lg-4 col-md-6" v-for="(item, index) in menuItems" :key="item.name" :style="{ animationDelay: index * 0.15 + 's' }">
                        <div class="menu-card">
                            <div class="menu-card-image">
                                <img :src="item.image" :alt="item.name" loading="lazy">
                                <div class="menu-card-overlay">
                                    <span class="menu-price">{{ item.price }}</span>
                                </div>
                            </div>
                            <div class="menu-card-body">
                                <h3 class="menu-card-title">{{ item.name }}</h3>
                                <p class="menu-card-desc">{{ item.description }}</p>
                                <button class="btn-menu-order">Ordenar Ahora</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-features">
            <div class="container">
                <div class="row g-4">
                    <div class="col-md-4" v-for="feature in features" :key="feature.title">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i :class="feature.icon"></i>
                            </div>
                            <h3>{{ feature.title }}</h3>
                            <p>{{ feature.description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="contact" class="section-contact">
            <div class="container">
                <div class="section-header" data-aos="fade-up">
                    <span class="section-tag">Hablemos</span>
                    <h2 class="section-title">Contáctanos</h2>
                    <p class="section-description">Estamos aquí para servirte</p>
                </div>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="contact-form-wrapper">
                            <form @submit.prevent="handleContact">
                                <div v-if="contactSuccess" class="alert alert-success-custom">{{ contactSuccess }}</div>
                                <div class="form-group-custom">
                                    <input type="text" class="form-control-custom" id="name" v-model="contactForm.name" required placeholder=" ">
                                    <label for="name" class="form-label-custom">Nombre</label>
                                </div>
                                <div class="form-group-custom">
                                    <input type="email" class="form-control-custom" id="email" v-model="contactForm.email" required placeholder=" ">
                                    <label for="email" class="form-label-custom">Email</label>
                                </div>
                                <div class="form-group-custom">
                                    <textarea class="form-control-custom" id="message" v-model="contactForm.message" rows="4" required placeholder=" "></textarea>
                                    <label for="message" class="form-label-custom">Mensaje</label>
                                </div>
                                <button type="submit" class="btn btn-gold btn-submit">
                                    <span>Enviar Mensaje</span>
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="contact-info-wrapper">
                            <div class="contact-info-card">
                                <div class="contact-info-item">
                                    <div class="contact-icon">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div>
                                        <h4>Ubicación</h4>
                                        <p>Calle Principal #123<br>Ciudad, Estado 12345</p>
                                    </div>
                                </div>
                                <div class="contact-info-item">
                                    <div class="contact-icon">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                    <div>
                                        <h4>Teléfono</h4>
                                        <p>+1 (555) 123-4567</p>
                                    </div>
                                </div>
                                <div class="contact-info-item">
                                    <div class="contact-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div>
                                        <h4>Horario</h4>
                                        <p>Lunes a Viernes: 7:00 AM - 9:00 PM<br>Sábado y Domingo: 8:00 AM - 10:00 PM</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <footer class="footer">
            <div class="container">
                <div class="footer-top">
                    <div class="row g-4">
                        <div class="col-lg-4 col-md-6">
                            <div class="footer-brand">
                                <h3><span class="brand-icon">☕</span> Chuquis Coffee</h3>
                                <p>El mejor café artesanal con el toque perfecto. Donde cada taza cuenta una historia.</p>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <h4>Enlaces</h4>
                            <ul class="footer-links">
                                <li><a href="#menu">Menú</a></li>
                                <li><a href="#contact">Contacto</a></li>
                                <li><a href="/login">Login</a></li>
                            </ul>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <h4>Contacto</h4>
                            <ul class="footer-contact">
                                <li><i class="fas fa-map-marker-alt"></i> Calle Principal #123</li>
                                <li><i class="fas fa-phone"></i> +1 (555) 123-4567</li>
                                <li><i class="fas fa-envelope"></i> info@chuquis.com</li>
                            </ul>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <h4>Síguenos</h4>
                            <div class="social-links">
                                <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                                <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                                <a href="#" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footer-bottom">
                    <p>&copy; {{ new Date().getFullYear() }} Chuquis Coffee. Todos los derechos reservados.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const menuOpen = ref(false);
const contactSuccess = ref('');
const isScrolled = ref(false);
const user = window.__APP_DATA?.user || null;
const hasRegister = true;

const menuItems = [
    { name: 'Espresso Clásico', description: 'Un shot intenso y puro, tostado a la perfección para despertar tus sentidos.', price: '$2.50', image: 'https://images.unsplash.com/photo-1510591509098-f4fdc6d0ff04?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80' },
    { name: 'Latte de Vainilla', description: 'Suave y cremoso con un toque dulce de vainilla natural.', price: '$4.00', image: 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80' },
    { name: 'Pastel de Chocolate', description: 'Hecho en casa con cacao fino, una tentación irresistible.', price: '$3.50', image: 'https://images.unsplash.com/photo-1551024506-0bccd828d307?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80' },
];

const features = [
    { icon: 'fas fa-seedling', title: 'Granos Selectos', description: 'Café 100% arábica de origen único' },
    { icon: 'fas fa-fire', title: 'Tostado Artesanal', description: 'Proceso tradicional que realza el sabor' },
    { icon: 'fas fa-heart', title: 'Hecho con Amor', description: 'Cada taza preparada con dedicación' },
];

const contactForm = reactive({ name: '', email: '', message: '' });

function toggleMenu() {
    menuOpen.value = !menuOpen.value;
    const navbarCollapse = document.getElementById('navbarNav');
    if (navbarCollapse) {
        if (menuOpen.value) {
            navbarCollapse.classList.add('show');
        } else {
            navbarCollapse.classList.remove('show');
        }
    }
}

function closeMenu() {
    menuOpen.value = false;
    const navbarCollapse = document.getElementById('navbarNav');
    if (navbarCollapse) {
        navbarCollapse.classList.remove('show');
    }
}

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    // Cerrar menú al hacer clic fuera
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.navbar') && menuOpen.value) {
            closeMenu();
        }
    });
});

function handleScroll() {
    isScrolled.value = window.scrollY > 50;
}

function particleStyle(n) {
    const size = Math.random() * 4 + 2;
    return {
        width: size + 'px',
        height: size + 'px',
        left: Math.random() * 100 + '%',
        top: Math.random() * 100 + '%',
        animationDelay: Math.random() * 20 + 's',
        animationDuration: (Math.random() * 10 + 10) + 's',
    };
}

async function handleContact() {
    try {
        await axios.post('/contact', contactForm);
        contactSuccess.value = 'Mensaje enviado correctamente';
        contactForm.name = '';
        contactForm.email = '';
        contactForm.message = '';
        setTimeout(() => contactSuccess.value = '', 5000);
    } catch (e) {
    }
}

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});
</script>

<style scoped>
:root {
    --coffee-dark: #1a0f0a;
    --coffee-brown: #2c1810;
    --coffee-medium: #3e2723;
    --gold-primary: #e6c87c;
    --gold-light: #f0d08a;
    --gold-dark: #d4a742;
    --cream: #f5f0e8;
    --text-light: #ffffff;
}

.welcome-page {
    font-family: 'Work Sans', sans-serif;
    background: var(--coffee-dark);
    color: var(--text-light);
    overflow-x: hidden;
}

/* Navbar */
.navbar {
    background: transparent;
    padding: 1.5rem 0;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 1000;
}

.navbar.scrolled {
    background: rgba(26, 15, 10, 0.95);
    backdrop-filter: blur(15px);
    padding: 0.8rem 0;
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.4);
}

.navbar-brand {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 700;
    font-size: 1.8rem;
    color: #f0d08a !important;
    text-decoration: none;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
}

.brand-icon {
    font-size: 1.5rem;
    margin-right: 0.5rem;
}

.navbar-brand:hover {
    color: var(--gold-light) !important;
    transform: translateY(-1px);
}

.navbar-toggler {
    border: 1px solid rgba(201, 169, 110, 0.5);
    padding: 0.4rem 0.6rem;
}

.navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28201, 169, 110, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
}

.navbar-nav .nav-link {
    color: #ffffff !important;
    font-weight: 400;
    font-size: 0.95rem;
    padding: 0.5rem 1.2rem !important;
    margin: 0 0.2rem;
    border-radius: 25px;
    transition: all 0.3s ease;
    position: relative;
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
}

.navbar-nav .nav-link::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    width: 0;
    height: 2px;
    background: #f0d08a;
    transition: all 0.3s ease;
    transform: translateX(-50%);
}

.navbar-nav .nav-link:hover::after,
.navbar-nav .nav-link.active::after {
    width: 80%;
}

.navbar-nav .nav-link:hover {
    color: #f0d08a !important;
    background: rgba(240, 208, 138, 0.1);
}

/* Hero Carousel */
#coffeeCarousel {
    position: relative;
    min-height: 100vh;
    overflow: hidden;
}

#coffeeCarousel .carousel-inner {
    min-height: 100vh;
}

#coffeeCarousel .carousel-item {
    min-height: 100vh;
}

#coffeeCarousel .carousel-image-wrapper {
    height: 100vh;
    min-height: 100vh;
    max-height: none;
    border-radius: 0;
    box-shadow: none;
    position: relative;
}

#coffeeCarousel .carousel-image-wrapper img {
    height: 100vh;
    min-height: 100vh;
    object-fit: cover;
    filter: brightness(0.75);
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(26, 15, 10, 0.4) 0%, rgba(62, 39, 35, 0.3) 100%);
    pointer-events: none;
    z-index: 1;
}

.hero-overlay-content {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hero-overlay-content .hero-content {
    position: relative;
    z-index: 3;
}

.hero-particles {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
}

.particle {
    position: absolute;
    background: var(--gold-primary);
    border-radius: 50%;
    opacity: 0.3;
    animation: float-particle linear infinite;
}

@keyframes float-particle {
    0%, 100% { transform: translateY(0) translateX(0); opacity: 0; }
    10% { opacity: 0.3; }
    90% { opacity: 0.3; }
    50% { transform: translateY(-100px) translateX(50px); }
}

.hero-content {
    position: relative;
    z-index: 2;
    text-align: center;
    padding: 2rem;
}

.hero-badge {
    display: inline-block;
    padding: 0.5rem 1.5rem;
    background: rgba(240, 208, 138, 0.2);
    border: 1px solid rgba(240, 208, 138, 0.4);
    border-radius: 50px;
    color: #f0d08a;
    font-size: 0.85rem;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 1.5rem;
    animation: fadeInDown 0.8s ease;
    text-shadow: 0 1px 5px rgba(0, 0, 0, 0.5);
}

.hero-title {
    font-family: 'Cormorant Garamond', serif;
    font-size: 4.5rem;
    font-weight: 700;
    line-height: 1.1;
    margin-bottom: 1.5rem;
    animation: fadeInUp 1s ease;
    color: #ffffff;
    text-shadow: 0 2px 15px rgba(0, 0, 0, 0.8);
}

.text-gold {
    color: #f0d08a;
    font-style: italic;
    text-shadow: 0 0 10px rgba(240, 208, 138, 0.5);
}

.hero-subtitle {
    font-size: 1.2rem;
    color: #f5f0e8;
    max-width: 600px;
    margin: 0 auto 2.5rem;
    line-height: 1.8;
    animation: fadeInUp 1.2s ease;
    text-shadow: 0 1px 8px rgba(0, 0, 0, 0.8);
}

.hero-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
    animation: fadeInUp 1.4s ease;
}

.hero-scroll-indicator {
    position: absolute;
    bottom: 2rem;
    left: 50%;
    transform: translateX(-50%);
    text-align: center;
    color: rgba(248, 249, 250, 0.5);
    font-size: 0.85rem;
    animation: bounce 2s infinite;
}

.hero-scroll-indicator i {
    display: block;
    margin-top: 0.5rem;
}

@keyframes bounce {
    0%, 100% { transform: translateX(-50%) translateY(0); }
    50% { transform: translateX(-50%) translateY(10px); }
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Buttons */
.btn-gold {
    background: linear-gradient(135deg, var(--gold-primary) 0%, var(--gold-dark) 100%);
    border: none;
    color: var(--coffee-dark);
    padding: 0.9rem 2.5rem;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1rem;
    letter-spacing: 0.5px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 15px rgba(201, 169, 110, 0.3);
}

.btn-gold:hover {
    background: linear-gradient(135deg, var(--gold-light) 0%, var(--gold-primary) 100%);
    color: var(--coffee-dark);
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(201, 169, 110, 0.5);
}

.btn-outline-gold {
    background: transparent;
    border: 2px solid var(--gold-primary);
    color: var(--gold-primary);
    padding: 0.9rem 2.5rem;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.btn-outline-gold:hover {
    background: rgba(201, 169, 110, 0.1);
    border-color: var(--gold-light);
    color: var(--gold-light);
    transform: translateY(-3px);
}

/* Sections */
.section-menu,
.section-contact {
    padding: 6rem 0;
    position: relative;
}

.section-menu {
    background: linear-gradient(180deg, var(--coffee-dark) 0%, #1f1510 100%);
}

.section-contact {
    background: linear-gradient(180deg, #1f1510 0%, var(--coffee-dark) 100%);
}

.section-header {
    text-align: center;
    margin-bottom: 4rem;
}

.section-tag {
    display: inline-block;
    color: var(--gold-primary);
    font-size: 0.85rem;
    letter-spacing: 3px;
    text-transform: uppercase;
    margin-bottom: 1rem;
    position: relative;
    padding: 0 1rem;
}

.section-tag::before,
.section-tag::after {
    content: '';
    position: absolute;
    top: 50%;
    width: 30px;
    height: 1px;
    background: var(--gold-primary);
}

.section-tag::before { left: -35px; }
.section-tag::after { right: -35px; }

.section-title {
    font-family: 'Cormorant Garamond', serif;
    font-size: 3rem;
    font-weight: 700;
    color: var(--text-light);
    margin-bottom: 1rem;
}

.section-description {
    color: rgba(248, 249, 250, 0.6);
    font-size: 1.1rem;
}

/* Menu Cards */
.menu-card {
    background: rgba(44, 24, 16, 0.6);
    border-radius: 20px;
    overflow: hidden;
    border: 1px solid rgba(201, 169, 110, 0.2);
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    animation: fadeInUp 0.8s ease both;
}

.menu-card:hover {
    transform: translateY(-10px);
    border-color: rgba(201, 169, 110, 0.5);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
}

.menu-card-image {
    position: relative;
    overflow: hidden;
    height: 250px;
}

.menu-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.menu-card:hover .menu-card-image img {
    transform: scale(1.1);
}

.menu-card-overlay {
    position: absolute;
    top: 1rem;
    right: 1rem;
}

.menu-price {
    display: inline-block;
    padding: 0.5rem 1rem;
    background: var(--gold-primary);
    color: var(--coffee-dark);
    border-radius: 25px;
    font-weight: 700;
    font-size: 0.9rem;
}

.menu-card-body {
    padding: 1.5rem;
}

.menu-card-title {
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.5rem;
    color: var(--text-light);
    margin-bottom: 0.8rem;
}

.menu-card-desc {
    color: rgba(248, 249, 250, 0.7);
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.btn-menu-order {
    width: 100%;
    padding: 0.8rem;
    background: transparent;
    border: 1px solid rgba(201, 169, 110, 0.3);
    color: var(--gold-primary);
    border-radius: 10px;
    font-weight: 500;
    transition: all 0.3s ease;
    cursor: pointer;
}

.btn-menu-order:hover {
    background: rgba(201, 169, 110, 0.1);
    border-color: var(--gold-primary);
}

/* Features Section */
.section-features {
    padding: 4rem 0;
    background: linear-gradient(135deg, var(--coffee-brown) 0%, var(--coffee-medium) 100%);
}

.feature-card {
    text-align: center;
    padding: 2.5rem 1.5rem;
    border-radius: 20px;
    border: 1px solid rgba(201, 169, 110, 0.15);
    background: rgba(26, 15, 10, 0.3);
    transition: all 0.4s ease;
    height: 100%;
}

.feature-card:hover {
    background: rgba(26, 15, 10, 0.5);
    border-color: rgba(201, 169, 110, 0.3);
    transform: translateY(-5px);
}

.feature-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: linear-gradient(135deg, var(--gold-primary) 0%, var(--gold-dark) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    color: var(--coffee-dark);
    transition: all 0.4s ease;
}

.feature-card:hover .feature-icon {
    transform: scale(1.1) rotate(5deg);
}

.feature-card h3 {
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.5rem;
    color: var(--text-light);
    margin-bottom: 0.8rem;
}

.feature-card p {
    color: rgba(248, 249, 250, 0.7);
    font-size: 0.95rem;
    line-height: 1.6;
}

/* Contact Form */
.contact-form-wrapper {
    background: rgba(44, 24, 16, 0.4);
    padding: 2.5rem;
    border-radius: 20px;
    border: 1px solid rgba(201, 169, 110, 0.2);
    height: 100%;
}

.form-group-custom {
    position: relative;
    margin-bottom: 1.5rem;
}

.form-control-custom {
    width: 100%;
    padding: 1rem;
    background: rgba(26, 15, 10, 0.5);
    border: 1px solid rgba(201, 169, 110, 0.3);
    border-radius: 12px;
    color: var(--text-light);
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-control-custom:focus {
    outline: none;
    border-color: var(--gold-primary);
    box-shadow: 0 0 0 3px rgba(201, 169, 110, 0.1);
    background: rgba(26, 15, 10, 0.7);
}

.form-label-custom {
    position: absolute;
    top: 1rem;
    left: 1rem;
    color: rgba(248, 249, 250, 0.5);
    transition: all 0.3s ease;
    pointer-events: none;
}

.form-control-custom:focus ~ .form-label-custom,
.form-control-custom:not(:placeholder-shown) ~ .form-label-custom {
    top: -0.6rem;
    left: 0.8rem;
    font-size: 0.8rem;
    color: var(--gold-primary);
    background: var(--coffee-dark);
    padding: 0 0.5rem;
}

.alert-success-custom {
    background: rgba(76, 175, 80, 0.15);
    border: 1px solid rgba(76, 175, 80, 0.3);
    color: #81c784;
    padding: 1rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
}

.btn-submit {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
}

/* Contact Info */
.contact-info-wrapper {
    height: 100%;
}

.contact-info-card {
    background: rgba(44, 24, 16, 0.4);
    padding: 2.5rem;
    border-radius: 20px;
    border: 1px solid rgba(201, 169, 110, 0.2);
    height: 100%;
}

.contact-info-item {
    display: flex;
    gap: 1.2rem;
    margin-bottom: 2rem;
}

.contact-info-item:last-child {
    margin-bottom: 0;
}

.contact-icon {
    width: 50px;
    height: 50px;
    min-width: 50px;
    background: rgba(201, 169, 110, 0.15);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gold-primary);
    font-size: 1.2rem;
}

.contact-info-item h4 {
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.2rem;
    color: var(--gold-primary);
    margin-bottom: 0.5rem;
}

.contact-info-item p {
    color: rgba(248, 249, 250, 0.7);
    font-size: 0.95rem;
    line-height: 1.6;
    margin: 0;
}

/* Footer */
.footer {
    background: var(--coffee-dark);
    padding: 4rem 0 0;
    border-top: 1px solid rgba(201, 169, 110, 0.2);
}

.footer-top {
    padding-bottom: 3rem;
}

.footer-brand h3 {
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.8rem;
    color: var(--gold-primary);
    margin-bottom: 1rem;
}

.footer-brand p {
    color: rgba(248, 249, 250, 0.6);
    line-height: 1.8;
}

.footer h4 {
    font-family: 'Cormorant Garamond', serif;
    color: var(--gold-primary);
    font-size: 1.3rem;
    margin-bottom: 1.5rem;
}

.footer-links {
    list-style: none;
    padding: 0;
}

.footer-links li {
    margin-bottom: 0.8rem;
}

.footer-links a {
    color: rgba(248, 249, 250, 0.6);
    text-decoration: none;
    transition: all 0.3s ease;
}

.footer-links a:hover {
    color: var(--gold-primary);
    padding-left: 5px;
}

.footer-contact {
    list-style: none;
    padding: 0;
}

.footer-contact li {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    color: rgba(248, 249, 250, 0.6);
    margin-bottom: 0.8rem;
}

.footer-contact i {
    color: var(--gold-primary);
    width: 20px;
}

.social-links {
    display: flex;
    gap: 1rem;
}

.social-links a {
    width: 45px;
    height: 45px;
    background: rgba(201, 169, 110, 0.1);
    border: 1px solid rgba(201, 169, 110, 0.3);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gold-primary);
    font-size: 1.1rem;
    transition: all 0.4s ease;
    text-decoration: none;
}

.social-links a:hover {
    background: var(--gold-primary);
    color: var(--coffee-dark);
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(201, 169, 110, 0.3);
}

.footer-bottom {
    border-top: 1px solid rgba(201, 169, 110, 0.2);
    padding: 1.5rem 0;
    text-align: center;
    color: rgba(248, 249, 250, 0.5);
    font-size: 0.9rem;
}

/* Carousel Section */
.carousel-section {
    margin-top: 0;
    position: relative;
    z-index: 1;
    background: var(--coffee-dark);
    padding: 4rem 0;
    border-top: 2px solid rgba(201, 169, 110, 0.3);
    border-bottom: 2px solid rgba(201, 169, 110, 0.3);
}

.carousel-section .container {
    max-width: 100%;
    padding: 0 3rem;
}

#coffeeCarousel {
    position: relative;
    overflow: hidden;
    margin: 0;
}

.carousel-image-wrapper {
    height: 60vh;
    min-height: 400px;
    max-height: 600px;
    overflow: hidden;
    position: relative;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
}

.carousel-image-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    filter: brightness(0.7);
}

.carousel-caption-wrapper {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 10;
    background: linear-gradient(transparent, rgba(26, 15, 10, 0.9));
    padding: 4rem 0 2rem;
}

.carousel-caption {
    position: relative;
    left: 0;
    right: 0;
    bottom: 0;
    padding: 1.5rem;
    text-align: left;
}

.carousel-caption .caption-badge {
    display: inline-block;
    padding: 0.3rem 1rem;
    background: rgba(201, 169, 110, 0.2);
    border: 1px solid rgba(201, 169, 110, 0.4);
    border-radius: 25px;
    color: var(--gold-primary);
    font-size: 0.75rem;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 0.8rem;
}

.carousel-caption h5 {
    font-family: 'Cormorant Garamond', serif;
    font-size: 2.5rem;
    color: #ffffff;
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.7);
}

.carousel-caption p {
    color: #f5f0e8;
    font-size: 1.1rem;
    max-width: 600px;
    text-shadow: 0 1px 5px rgba(0, 0, 0, 0.7);
}

.carousel-indicators {
    bottom: 20px;
    z-index: 15;
}

.carousel-indicators button {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid rgba(201, 169, 110, 0.5);
    background: transparent;
    margin: 0 6px;
    transition: all 0.3s ease;
}

.carousel-indicators button.active {
    background: var(--gold-primary);
    border-color: var(--gold-primary);
    transform: scale(1.2);
}

.carousel-control-prev,
.carousel-control-next {
    width: 60px;
    height: 60px;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(26, 15, 10, 0.5);
    border: 1px solid rgba(201, 169, 110, 0.3);
    border-radius: 50%;
    opacity: 0;
    transition: all 0.4s ease;
    z-index: 15;
}

#coffeeCarousel:hover .carousel-control-prev,
#coffeeCarousel:hover .carousel-control-next {
    opacity: 1;
}

.carousel-control-prev {
    left: 30px;
}

.carousel-control-next {
    right: 30px;
}

.carousel-control-prev:hover,
.carousel-control-next:hover {
    background: rgba(201, 169, 110, 0.2);
    border-color: var(--gold-primary);
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
    width: 20px;
    height: 20px;
}

.carousel-fade .carousel-item {
    opacity: 0;
    transition: opacity 1s ease;
}

.carousel-fade .carousel-item.active {
    opacity: 1;
}

/* Responsive */
@media (max-width: 992px) {
    .hero-title {
        font-size: 3.5rem;
    }

    .section-title {
        font-size: 2.5rem;
    }

    .contact-form-wrapper,
    .contact-info-card {
        padding: 2rem;
    }
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 2.8rem;
    }

    .hero-subtitle {
        font-size: 1.1rem;
    }

    .section-menu,
    .section-contact {
        padding: 4rem 0;
    }

    .section-title {
        font-size: 2rem;
    }

    .menu-card-image {
        height: 200px;
    }

    .hero-actions {
        flex-direction: column;
        align-items: center;
    }

    .btn-gold,
    .btn-outline-gold {
        width: 100%;
        max-width: 300px;
    }

    .carousel-image-wrapper {
        height: 50vh;
        min-height: 300px;
    }

    .carousel-caption h5 {
        font-size: 1.8rem;
    }

    .carousel-caption p {
        font-size: 1rem;
    }

    .carousel-control-prev,
    .carousel-control-next {
        width: 45px;
        height: 45px;
    }

    .carousel-control-prev {
        left: 15px;
    }

    .carousel-control-next {
        right: 15px;
    }
}

@media (max-width: 576px) {
    .hero-title {
        font-size: 2.2rem;
    }

    .hero-badge {
        font-size: 0.75rem;
    }

    .section-tag::before,
    .section-tag::after {
        width: 20px;
    }

    .section-tag::before { left: -25px; }
    .section-tag::after { right: -25px; }

    .footer-brand h3 {
        font-size: 1.5rem;
    }

    .carousel-image-wrapper {
        height: 40vh;
        min-height: 250px;
    }

    .carousel-caption-wrapper {
        padding: 2rem 0 1rem;
    }

    .carousel-caption h5 {
        font-size: 1.5rem;
    }

    .carousel-caption p {
        font-size: 0.9rem;
    }

    .carousel-caption .caption-badge {
        font-size: 0.65rem;
        padding: 0.2rem 0.8rem;
    }

    .carousel-control-prev,
    .carousel-control-next {
        width: 35px;
        height: 35px;
        opacity: 1;
    }

    .carousel-control-prev {
        left: 10px;
    }

    .carousel-control-next {
        right: 10px;
    }

    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        width: 15px;
        height: 15px;
    }
}
</style>

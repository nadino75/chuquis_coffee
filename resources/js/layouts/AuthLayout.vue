<template>
    <div class="min-vh-100 d-flex align-items-center justify-content-center auth-bg">
        <div class="auth-card">
            <div class="auth-header">
                <router-link to="/" class="auth-brand">
                    <i class="fas fa-coffee"></i> Chuquis Coffee
                </router-link>
            </div>
            <div class="auth-body">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script setup>
</script>

<style scoped>
.auth-bg {
    background: linear-gradient(135deg, #2c2c2c 0%, #1a1a1a 100%);
}

.auth-card {
    background: #2c2c2c;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    width: 100%;
    max-width: 420px;
    overflow: hidden;
}

.auth-header {
    background: linear-gradient(90deg, #1a1a1a, #3e2723);
    padding: 20px;
    text-align: center;
}

.auth-brand {
    font-family: 'Playfair Display', serif;
    font-weight: 700;
    color: #daa520;
    font-size: 1.8rem;
    text-decoration: none;
    transition: all 0.3s ease;
}

.auth-brand:hover {
    color: #f8f9fa;
}

.auth-body {
    padding: 30px;
    color: #f8f9fa;
}
</style>

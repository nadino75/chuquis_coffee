<template>
    <div class="text-center py-5">
        <h1 class="display-4">404</h1>
        <p class="lead">Página no encontrada</p>
        <router-link to="/" class="btn btn-custom">Volver al inicio</router-link>
    </div>
</template>

<script setup>
</script>

<style scoped>
.btn-custom {
    background: linear-gradient(45deg, #daa520, #b8860b);
    border: none;
    color: #1a1a1a;
    padding: 12px 25px;
    border-radius: 30px;
    font-weight: 500;
}
</style>

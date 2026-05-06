<template>
    <div>
        <h2 class="text-center mb-4" style="font-family: 'Playfair Display', serif; color: #daa520;">
            <i class="fas fa-key"></i> Recuperar Contraseña
        </h2>
        <p class="text-center mb-4" style="color: #ccc;">
            Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
        </p>
        <div v-if="success" class="alert alert-success">{{ success }}</div>
        <form @submit.prevent="handleForgotPassword">
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input id="email" type="email" class="form-control" :class="{ 'is-invalid': errors.email }"
                       v-model="form.email" required autofocus>
                <div class="invalid-feedback" v-if="errors.email">{{ errors.email }}</div>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-login w-100" :disabled="loading">
                    <span v-if="loading" class="spinner-border spinner-border-sm mr-2"></span>
                    Enviar Enlace
                </button>
            </div>
            <div class="text-center">
                <router-link :to="{ name: 'login' }" class="back-link">
                    <i class="fas fa-arrow-left"></i> Volver al login
                </router-link>
            </div>
        </form>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import authService from '@/services/auth';

const loading = ref(false);
const success = ref('');
const errors = reactive({});
const form = reactive({ email: '' });

async function handleForgotPassword() {
    loading.value = true;
    errors.email = '';
    try {
        await authService.forgotPassword(form.email);
        success.value = 'Se ha enviado el enlace de recuperación a tu correo.';
    } catch (e) {
        if (e.response?.data?.errors) {
            Object.assign(errors, e.response.data.errors);
        } else {
            errors.email = 'Error al enviar el enlace. Intente nuevamente.';
        }
    } finally {
        loading.value = false;
    }
}
</script>

<style scoped>
.form-control {
    background: #2c2c2c;
    border: 1px solid #daa520;
    border-radius: 10px;
    color: #f8f9fa;
}

.form-control:focus {
    border-color: #daa520;
    box-shadow: 0 0 8px rgba(218, 165, 32, 0.5);
    background: #2c2c2c;
    color: #f8f9fa;
}

.form-label { color: #daa520; }

.btn-login {
    background: linear-gradient(45deg, #daa520, #b8860b);
    border: none;
    color: #1a1a1a;
    padding: 12px 25px;
    border-radius: 30px;
    font-weight: 500;
}

.btn-login:hover:not(:disabled) {
    background: linear-gradient(45deg, #b8860b, #daa520);
    transform: translateY(-2px);
}

.btn-login:disabled { opacity: 0.7; cursor: not-allowed; }

.back-link { color: #daa520; text-decoration: none; }
.back-link:hover { color: #f8f9fa; text-decoration: underline; }
.invalid-feedback { display: block; }
</style>

<template>
    <div>
        <h2 class="text-center mb-4" style="font-family: 'Playfair Display', serif; color: #daa520;">
            <i class="fas fa-lock"></i> Nueva Contraseña
        </h2>
        <form @submit.prevent="handleResetPassword">
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input id="email" type="email" class="form-control" :class="{ 'is-invalid': errors.email }"
                       v-model="form.email" required autofocus>
                <div class="invalid-feedback" v-if="errors.email">{{ errors.email }}</div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Nueva Contraseña</label>
                <input id="password" type="password" class="form-control" :class="{ 'is-invalid': errors.password }"
                       v-model="form.password" required autocomplete="new-password">
                <div class="invalid-feedback" v-if="errors.password">{{ errors.password }}</div>
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                <input id="password_confirmation" type="password" class="form-control"
                       v-model="form.password_confirmation" required autocomplete="new-password">
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-login w-100" :disabled="loading">
                    <span v-if="loading" class="spinner-border spinner-border-sm mr-2"></span>
                    Restablecer Contraseña
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import authService from '@/services/auth';

const route = useRoute();
const router = useRouter();
const loading = ref(false);
const errors = reactive({});
const form = reactive({
    email: '',
    password: '',
    password_confirmation: '',
    token: route.params.token,
});

async function handleResetPassword() {
    loading.value = true;
    Object.keys(errors).forEach(k => delete errors[k]);
    try {
        await authService.resetPassword(form);
        router.push({ name: 'login' });
    } catch (e) {
        if (e.response?.data?.errors) {
            Object.assign(errors, e.response.data.errors);
        } else {
            errors.email = 'Error al restablecer la contraseña.';
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
.invalid-feedback { display: block; }
</style>

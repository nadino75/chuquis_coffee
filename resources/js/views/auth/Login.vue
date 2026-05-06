<template>
    <div>
        <h2 class="text-center mb-4" style="font-family: 'Playfair Display', serif; color: #daa520;">
            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
        </h2>
        <form @submit.prevent="handleLogin">
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input id="email" type="email" class="form-control" :class="{ 'is-invalid': errors.email }"
                       v-model="form.email" required autofocus autocomplete="email">
                <div class="invalid-feedback" v-if="errors.email">{{ errors.email }}</div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input id="password" type="password" class="form-control" :class="{ 'is-invalid': errors.password }"
                       v-model="form.password" required autocomplete="current-password">
                <div class="invalid-feedback" v-if="errors.password">{{ errors.password }}</div>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" v-model="form.remember">
                <label class="form-check-label" for="remember">Recordarme</label>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-login w-100" :disabled="loading">
                    <span v-if="loading" class="spinner-border spinner-border-sm mr-2"></span>
                    Iniciar Sesión
                </button>
            </div>
            <div class="text-center mb-3">
                <router-link :to="{ name: 'password.request' }" class="forgot-password">
                    ¿Olvidaste tu contraseña?
                </router-link>
            </div>
            <div class="register-link text-center">
                ¿No tienes una cuenta? <router-link :to="{ name: 'register' }">Regístrate aquí</router-link>
            </div>
        </form>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import authState from '@/services/authState';

const router = useRouter();
const loading = ref(false);
const errors = reactive({});
const form = reactive({
    email: '',
    password: '',
    remember: false,
});

async function handleLogin() {
    loading.value = true;
    errors.email = '';
    errors.password = '';
    try {
        await authState.login(form);
        const redirect = router.currentRoute.value.query.redirect || '/dashboard';
        router.push(redirect);
    } catch (e) {
        if (e.response?.data?.errors) {
            Object.assign(errors, e.response.data.errors);
        } else if (e.response?.data?.message) {
            errors.email = e.response.data.message;
        } else {
            errors.email = 'Error al iniciar sesión. Intente nuevamente.';
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

.form-label {
    color: #daa520;
}

.btn-login {
    background: linear-gradient(45deg, #daa520, #b8860b);
    border: none;
    color: #1a1a1a;
    padding: 12px 25px;
    border-radius: 30px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-login:hover:not(:disabled) {
    background: linear-gradient(45deg, #b8860b, #daa520);
    transform: translateY(-2px);
}

.btn-login:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.forgot-password {
    color: #daa520;
    text-decoration: none;
}

.forgot-password:hover {
    color: #f8f9fa;
    text-decoration: underline;
}

.register-link {
    color: #f8f9fa;
}

.register-link a {
    color: #daa520;
    text-decoration: none;
    font-weight: 500;
}

.register-link a:hover {
    color: #f8f9fa;
    text-decoration: underline;
}

.invalid-feedback {
    display: block;
}
</style>

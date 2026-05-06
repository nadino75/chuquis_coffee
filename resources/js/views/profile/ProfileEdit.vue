<template>
    <div>
        <div v-if="success" class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-1"></i> {{ success }}
            <button type="button" class="close" @click="success = ''">&times;</button>
        </div>
        <div v-if="error" class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle mr-1"></i> {{ error }}
            <button type="button" class="close" @click="error = ''">&times;</button>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h5 class="card-title mb-0 text-white">
                            <i class="fas fa-user-edit mr-1"></i> Información del Perfil
                        </h5>
                    </div>
                    <form @submit.prevent="updateProfile">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Nombre <span class="text-danger">*</span></label>
                                <input type="text" v-model="profileForm.name" class="form-control" :class="{ 'is-invalid': profileErrors.name }" required>
                                <div class="invalid-feedback" v-if="profileErrors.name">{{ profileErrors.name[0] }}</div>
                            </div>
                            <div class="form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="email" v-model="profileForm.email" class="form-control" :class="{ 'is-invalid': profileErrors.email }" required>
                                <div class="invalid-feedback" v-if="profileErrors.email">{{ profileErrors.email[0] }}</div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary" :disabled="loadingProfile">
                                <span v-if="loadingProfile" class="spinner-border spinner-border-sm mr-1"></span>
                                <i class="fas fa-save mr-1"></i> Actualizar Información
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-info">
                        <h5 class="card-title mb-0 text-white">
                            <i class="fas fa-user mr-1"></i> Mi Perfil
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-user-circle fa-5x text-muted"></i>
                        </div>
                        <h5 class="mb-1">{{ user?.name || 'Usuario' }}</h5>
                        <p class="text-muted mb-2">{{ user?.email || '' }}</p>
                        <div v-if="user?.roles?.length" class="mb-2">
                            <span v-for="role in user.roles" :key="role.name" class="badge badge-primary mr-1">{{ role.name }}</span>
                        </div>
                        <small class="text-muted">
                            Miembro desde {{ formatDate(user?.created_at) }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-warning">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-lock mr-1"></i> Cambiar Contraseña
                        </h5>
                    </div>
                    <form @submit.prevent="updatePassword">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Contraseña Actual <span class="text-danger">*</span></label>
                                <input type="password" v-model="passwordForm.current_password" class="form-control" :class="{ 'is-invalid': passwordErrors.current_password }" required>
                                <div class="invalid-feedback" v-if="passwordErrors.current_password">{{ passwordErrors.current_password[0] }}</div>
                            </div>
                            <div class="form-group">
                                <label>Nueva Contraseña <span class="text-danger">*</span></label>
                                <input type="password" v-model="passwordForm.password" class="form-control" :class="{ 'is-invalid': passwordErrors.password }" required minlength="8">
                                <div class="invalid-feedback" v-if="passwordErrors.password">{{ passwordErrors.password[0] }}</div>
                            </div>
                            <div class="form-group">
                                <label>Confirmar Nueva Contraseña <span class="text-danger">*</span></label>
                                <input type="password" v-model="passwordForm.password_confirmation" class="form-control" :class="{ 'is-invalid': passwordErrors.password_confirmation }" required minlength="8">
                                <div class="invalid-feedback" v-if="passwordErrors.password_confirmation">{{ passwordErrors.password_confirmation[0] }}</div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-warning" :disabled="loadingPassword">
                                <span v-if="loadingPassword" class="spinner-border spinner-border-sm mr-1"></span>
                                <i class="fas fa-key mr-1"></i> Cambiar Contraseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-danger">
                    <div class="card-header bg-danger">
                        <h5 class="card-title mb-0 text-white">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Zona de Peligro
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Una vez que elimines tu cuenta, todos tus datos serán eliminados permanentemente. Esta acción no se puede deshacer.</p>
                        <button type="button" class="btn btn-danger" @click="confirmDeleteAccount" :disabled="loadingDelete">
                            <span v-if="loadingDelete" class="spinner-border spinner-border-sm mr-1"></span>
                            <i class="fas fa-trash mr-1"></i> Eliminar Cuenta
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Account Confirmation Modal -->
        <div class="modal fade" :class="{ show: showDeleteModal, dBlock: showDeleteModal }" tabindex="-1" style="background: rgba(0,0,0,0.5);" v-if="showDeleteModal">
            <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white"><i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación</h5>
                    <button type="button" class="close text-white" @click="showDeleteModal = false">&times;</button>
                </div>
                <div class="modal-body">
                    <p class="text-danger font-weight-bold">¿Está seguro de eliminar su cuenta?</p>
                    <p class="text-muted">Esta acción es irreversible. Para confirmar, escriba su contraseña:</p>
                    <div class="form-group">
                        <input type="password" v-model="deleteForm.password" class="form-control" :class="{ 'is-invalid': deleteError }" placeholder="Ingrese su contraseña" required>
                        <div class="invalid-feedback" v-if="deleteError">{{ deleteError }}</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="showDeleteModal = false">Cancelar</button>
                    <button type="button" class="btn btn-danger" @click="deleteAccount" :disabled="loadingDelete">
                        <span v-if="loadingDelete" class="spinner-border spinner-border-sm mr-1"></span>
                        Eliminar Cuenta
                    </button>
                </div>
            </div></div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import profileService from '@/services/profile';

const user = ref(null);
const loadingProfile = ref(false);
const loadingPassword = ref(false);
const loadingDelete = ref(false);
const success = ref('');
const error = ref('');
const showDeleteModal = ref(false);
const deleteError = ref('');

const profileForm = reactive({ name: '', email: '' });
const profileErrors = reactive({});

const passwordForm = reactive({ current_password: '', password: '', password_confirmation: '' });
const passwordErrors = reactive({});

const deleteForm = reactive({ password: '' });

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    return d.toLocaleDateString('es-ES', { day: '2-digit', month: 'long', year: 'numeric' });
}

async function loadUser() {
    const appData = window.__APP_DATA?.user;
    if (appData) {
        user.value = appData;
        profileForm.name = appData.name || '';
        profileForm.email = appData.email || '';
    } else {
        try {
            const res = await profileService.get();
            user.value = res.data;
            profileForm.name = res.data.name || '';
            profileForm.email = res.data.email || '';
        } catch (e) {
            error.value = 'Error al cargar los datos del perfil';
        }
    }
}

async function updateProfile() {
    loadingProfile.value = true;
    Object.keys(profileErrors).forEach(k => delete profileErrors[k]);
    try {
        await profileService.update({ name: profileForm.name, email: profileForm.email });
        success.value = 'Información del perfil actualizada exitosamente';
        await loadUser();
    } catch (e) {
        if (e.response?.data?.errors) Object.assign(profileErrors, e.response.data.errors);
        else error.value = 'Error al actualizar el perfil';
    } finally {
        loadingProfile.value = false;
    }
}

async function updatePassword() {
    loadingPassword.value = true;
    Object.keys(passwordErrors).forEach(k => delete passwordErrors[k]);
    try {
        await profileService.updatePassword(passwordForm);
        success.value = 'Contraseña actualizada exitosamente';
        passwordForm.current_password = '';
        passwordForm.password = '';
        passwordForm.password_confirmation = '';
    } catch (e) {
        if (e.response?.data?.errors) Object.assign(passwordErrors, e.response.data.errors);
        else error.value = 'Error al actualizar la contraseña';
    } finally {
        loadingPassword.value = false;
    }
}

function confirmDeleteAccount() {
    deleteForm.password = '';
    deleteError.value = '';
    showDeleteModal.value = true;
}

async function deleteAccount() {
    if (!deleteForm.password) {
        deleteError.value = 'La contraseña es requerida';
        return;
    }
    if (!confirm('¿Está completamente seguro? Esta acción no se puede deshacer.')) return;
    loadingDelete.value = true;
    deleteError.value = '';
    try {
        await profileService.destroy({ password: deleteForm.password });
        success.value = 'Cuenta eliminada exitosamente';
        showDeleteModal.value = false;
        setTimeout(() => {
            window.location.href = '/';
        }, 1500);
    } catch (e) {
        if (e.response?.data?.message) deleteError.value = e.response.data.message;
        else deleteError.value = 'Error al eliminar la cuenta';
    } finally {
        loadingDelete.value = false;
    }
}

onMounted(() => loadUser());
</script>

<style scoped>
.dBlock { display: block !important; }
.form-control { border-radius: 8px; }
.btn-group .btn { margin: 0 2px; }
.invalid-feedback { display: block; }
</style>

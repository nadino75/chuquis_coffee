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

        <div class="card">
            <div class="card-header bg-primary d-flex align-items-center">
                <h5 class="card-title mb-0 text-white flex-grow-1">
                    <i class="fas fa-users mr-1"></i> Lista de Clientes
                </h5>
                <button type="button" class="btn btn-light btn-sm" @click="openCreateModal">
                    <i class="fas fa-plus-circle mr-1"></i> Nuevo Cliente
                </button>
            </div>
            <div class="card-body">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" v-model="search" placeholder="Buscar cliente..." @keyup.enter="loadItems(1)">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" @click="loadItems(1)">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead class="thead-dark">
                            <tr class="text-center">
                                <th>#</th>
                                <th>CI</th>
                                <th>Nombre Completo</th>
                                <th>Celular</th>
                                <th>Email</th>
                                <th width="15%">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in items.data" :key="item.ci" class="text-center">
                                <td>{{ (items.current_page - 1) * items.per_page + index + 1 }}</td>
                                <td>{{ item.ci }}</td>
                                <td>{{ `${item.nombres} ${item.apellido_paterno} ${item.apellido_materno || ''}` }}</td>
                                <td>{{ item.celular }}</td>
                                <td>{{ item.correo || '-' }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-info btn-sm" title="Ver" @click="showItem(item)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-warning btn-sm" title="Editar" @click="openEditModal(item)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" title="Eliminar" @click="deleteItem(item)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!items.data?.length">
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-users fa-2x mb-2"></i><br>
                                    No hay registros
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3" v-if="items.last_page > 1">
                    <div class="text-muted">
                        Mostrando {{ items.from || 0 }} a {{ items.to || 0 }} de {{ items.total }} registros
                    </div>
                    <nav>
                        <ul class="pagination mb-0">
                            <li class="page-item" :class="{ disabled: !items.prev_page_url }">
                                <button class="page-link" @click="goToPage(items.current_page - 1)" :disabled="!items.prev_page_url">&laquo;</button>
                            </li>
                            <li class="page-item" v-for="page in visiblePages" :key="page" :class="{ active: page === items.current_page }">
                                <button class="page-link" @click="goToPage(page)">{{ page }}</button>
                            </li>
                            <li class="page-item" :class="{ disabled: !items.next_page_url }">
                                <button class="page-link" @click="goToPage(items.current_page + 1)" :disabled="!items.next_page_url">&raquo;</button>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <div class="modal fade" :class="{ show: showCreate, dBlock: showCreate }" tabindex="-1" style="background: rgba(0,0,0,0.5);" v-if="showCreate">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white"><i class="fas fa-plus"></i> Nuevo Cliente</h5>
                        <button type="button" class="close text-white" @click="closeCreateModal">&times;</button>
                    </div>
                    <form @submit.prevent="createItem">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>CI <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" :class="{ 'is-invalid': formErrors.ci }" v-model="createForm.ci" required>
                                <div class="invalid-feedback" v-if="formErrors.ci">{{ formErrors.ci[0] }}</div>
                            </div>
                            <div class="form-group">
                                <label>Nombres <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" :class="{ 'is-invalid': formErrors.nombres }" v-model="createForm.nombres" required>
                                <div class="invalid-feedback" v-if="formErrors.nombres">{{ formErrors.nombres[0] }}</div>
                            </div>
                            <div class="form-group">
                                <label>Apellido Paterno <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" :class="{ 'is-invalid': formErrors.apellido_paterno }" v-model="createForm.apellido_paterno" required>
                                <div class="invalid-feedback" v-if="formErrors.apellido_paterno">{{ formErrors.apellido_paterno[0] }}</div>
                            </div>
                            <div class="form-group">
                                <label>Apellido Materno</label>
                                <input type="text" class="form-control" :class="{ 'is-invalid': formErrors.apellido_materno }" v-model="createForm.apellido_materno">
                                <div class="invalid-feedback" v-if="formErrors.apellido_materno">{{ formErrors.apellido_materno[0] }}</div>
                            </div>
                            <div class="form-group">
                                <label>Celular</label>
                                <input type="text" class="form-control" :class="{ 'is-invalid': formErrors.celular }" v-model="createForm.celular">
                                <div class="invalid-feedback" v-if="formErrors.celular">{{ formErrors.celular[0] }}</div>
                            </div>
                            <div class="form-group">
                                <label>Correo</label>
                                <input type="email" class="form-control" :class="{ 'is-invalid': formErrors.correo }" v-model="createForm.correo">
                                <div class="invalid-feedback" v-if="formErrors.correo">{{ formErrors.correo[0] }}</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="closeCreateModal">Cancelar</button>
                            <button type="submit" class="btn btn-primary" :disabled="loading">
                                <span v-if="loading" class="spinner-border spinner-border-sm mr-1"></span>
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" :class="{ show: showEdit, dBlock: showEdit }" tabindex="-1" style="background: rgba(0,0,0,0.5);" v-if="showEdit">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Cliente</h5>
                        <button type="button" class="close" @click="closeEditModal">&times;</button>
                    </div>
                    <form @submit.prevent="updateItem">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>CI <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" :class="{ 'is-invalid': formErrors.ci }" v-model="editForm.ci" required>
                                <div class="invalid-feedback" v-if="formErrors.ci">{{ formErrors.ci[0] }}</div>
                            </div>
                            <div class="form-group">
                                <label>Nombres <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" :class="{ 'is-invalid': formErrors.nombres }" v-model="editForm.nombres" required>
                                <div class="invalid-feedback" v-if="formErrors.nombres">{{ formErrors.nombres[0] }}</div>
                            </div>
                            <div class="form-group">
                                <label>Apellido Paterno <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" :class="{ 'is-invalid': formErrors.apellido_paterno }" v-model="editForm.apellido_paterno" required>
                                <div class="invalid-feedback" v-if="formErrors.apellido_paterno">{{ formErrors.apellido_paterno[0] }}</div>
                            </div>
                            <div class="form-group">
                                <label>Apellido Materno</label>
                                <input type="text" class="form-control" :class="{ 'is-invalid': formErrors.apellido_materno }" v-model="editForm.apellido_materno">
                                <div class="invalid-feedback" v-if="formErrors.apellido_materno">{{ formErrors.apellido_materno[0] }}</div>
                            </div>
                            <div class="form-group">
                                <label>Celular</label>
                                <input type="text" class="form-control" :class="{ 'is-invalid': formErrors.celular }" v-model="editForm.celular">
                                <div class="invalid-feedback" v-if="formErrors.celular">{{ formErrors.celular[0] }}</div>
                            </div>
                            <div class="form-group">
                                <label>Correo</label>
                                <input type="email" class="form-control" :class="{ 'is-invalid': formErrors.correo }" v-model="editForm.correo">
                                <div class="invalid-feedback" v-if="formErrors.correo">{{ formErrors.correo[0] }}</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="closeEditModal">Cancelar</button>
                            <button type="submit" class="btn btn-warning" :disabled="loading">
                                <span v-if="loading" class="spinner-border spinner-border-sm mr-1"></span>
                                Actualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Show Modal -->
        <div class="modal fade" :class="{ show: showView, dBlock: showView }" tabindex="-1" style="background: rgba(0,0,0,0.5);" v-if="showView">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-info">
                        <h5 class="modal-title text-white"><i class="fas fa-eye"></i> Detalle Cliente</h5>
                        <button type="button" class="close text-white" @click="closeViewModal">&times;</button>
                    </div>
                    <div class="modal-body" v-if="currentItem">
                        <table class="table table-bordered">
                            <tr><th width="40%">CI</th><td>{{ currentItem.ci }}</td></tr>
                            <tr><th>Nombres</th><td>{{ currentItem.nombres }}</td></tr>
                            <tr><th>Apellido Paterno</th><td>{{ currentItem.apellido_paterno }}</td></tr>
                            <tr><th>Apellido Materno</th><td>{{ currentItem.apellido_materno || '-' }}</td></tr>
                            <tr><th>Celular</th><td>{{ currentItem.celular || '-' }}</td></tr>
                            <tr><th>Correo</th><td>{{ currentItem.correo || '-' }}</td></tr>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeViewModal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import clienteService from '@/services/clientes';

const items = ref({ data: [], current_page: 1, last_page: 1, from: 0, to: 0, total: 0, per_page: 10, prev_page_url: null, next_page_url: null });
const loading = ref(false);
const success = ref('');
const error = ref('');
const search = ref('');
const showCreate = ref(false);
const showEdit = ref(false);
const showView = ref(false);
const currentItem = ref(null);
const currentPage = ref(1);
const createForm = reactive({ ci: '', nombres: '', apellido_paterno: '', apellido_materno: '', celular: '', correo: '' });
const editForm = reactive({ ci: '', nombres: '', apellido_paterno: '', apellido_materno: '', celular: '', correo: '' });
const formErrors = reactive({});

const visiblePages = computed(() => {
    const pages = [];
    const start = Math.max(1, currentPage.value - 2);
    const end = Math.min(items.value.last_page, currentPage.value + 2);
    for (let i = start; i <= end; i++) pages.push(i);
    return pages;
});

async function loadItems(page = 1) {
    loading.value = true;
    try {
        const res = await clienteService.index({ page, search: search.value });
        items.value = res.data;
        currentPage.value = page;
    } catch (e) {
        error.value = 'Error al cargar los datos';
    } finally {
        loading.value = false;
    }
}

function goToPage(page) {
    if (page >= 1 && page <= items.value.last_page) loadItems(page);
}

function openCreateModal() {
    createForm.ci = '';
    createForm.nombres = '';
    createForm.apellido_paterno = '';
    createForm.apellido_materno = '';
    createForm.celular = '';
    createForm.correo = '';
    Object.keys(formErrors).forEach(k => delete formErrors[k]);
    showCreate.value = true;
}

function closeCreateModal() {
    showCreate.value = false;
}

async function createItem() {
    loading.value = true;
    Object.keys(formErrors).forEach(k => delete formErrors[k]);
    try {
        await clienteService.store(createForm);
        success.value = 'Registro creado exitosamente';
        closeCreateModal();
        loadItems(currentPage.value);
    } catch (e) {
        if (e.response?.data?.errors) Object.assign(formErrors, e.response.data.errors);
        else error.value = 'Error al crear el registro';
    } finally {
        loading.value = false;
    }
}

function openEditModal(item) {
    currentItem.value = { ...item };
    editForm.ci = item.ci || '';
    editForm.nombres = item.nombres || '';
    editForm.apellido_paterno = item.apellido_paterno || '';
    editForm.apellido_materno = item.apellido_materno || '';
    editForm.celular = item.celular || '';
    editForm.correo = item.correo || '';
    Object.keys(formErrors).forEach(k => delete formErrors[k]);
    showEdit.value = true;
}

function closeEditModal() {
    showEdit.value = false;
}

async function updateItem() {
    loading.value = true;
    Object.keys(formErrors).forEach(k => delete formErrors[k]);
    try {
        await clienteService.update(currentItem.value.ci, editForm);
        success.value = 'Registro actualizado exitosamente';
        closeEditModal();
        loadItems(currentPage.value);
    } catch (e) {
        if (e.response?.data?.errors) Object.assign(formErrors, e.response.data.errors);
        else error.value = 'Error al actualizar el registro';
    } finally {
        loading.value = false;
    }
}

function showItem(item) {
    currentItem.value = item;
    showView.value = true;
}

function closeViewModal() {
    showView.value = false;
}

async function deleteItem(item) {
    if (!confirm('¿Está seguro de eliminar este registro?')) return;
    try {
        await clienteService.destroy(item.ci);
        success.value = 'Registro eliminado exitosamente';
        loadItems(currentPage.value);
    } catch (e) {
        error.value = 'Error al eliminar el registro';
    }
}

onMounted(() => loadItems());
</script>

<style scoped>
.dBlock { display: block !important; }
.form-control { border-radius: 8px; }
.btn-group .btn { margin: 0 2px; }
.invalid-feedback { display: block; }
</style>

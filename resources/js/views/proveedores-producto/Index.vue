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
                    <i class="fas fa-link mr-1"></i> Proveedores-Producto
                </h5>
                <button type="button" class="btn btn-light btn-sm" @click="openCreateModal">
                    <i class="fas fa-plus-circle mr-1"></i> Nueva Relación
                </button>
            </div>
            <div class="card-body">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" v-model="search" placeholder="Buscar relación..." @keyup.enter="loadItems(1)">
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
                                <th>Proveedor</th>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th width="15%">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in items.data" :key="item.id" class="text-center">
                                <td>{{ (items.current_page - 1) * items.per_page + index + 1 }}</td>
                                <td>{{ item.proveedore?.nombre || '-' }}</td>
                                <td>{{ item.producto?.nombre || '-' }}</td>
                                <td>${{ item.precio }}</td>
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
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-link fa-2x mb-2"></i><br>
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
                        <h5 class="modal-title text-white"><i class="fas fa-plus"></i> Nueva Relación</h5>
                        <button type="button" class="close text-white" @click="closeCreateModal">&times;</button>
                    </div>
                    <form @submit.prevent="createItem">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Proveedor <span class="text-danger">*</span></label>
                                <select class="form-control" :class="{ 'is-invalid': formErrors.proveedore_id }" v-model="createForm.proveedore_id" required>
                                    <option value="">Seleccione un proveedor</option>
                                    <option v-for="p in proveedores" :key="p.id" :value="p.id">{{ p.nombre }}</option>
                                </select>
                                <div class="invalid-feedback" v-if="formErrors.proveedore_id">{{ formErrors.proveedore_id[0] }}</div>
                            </div>
                            <div class="form-group">
                                <label>Producto <span class="text-danger">*</span></label>
                                <select class="form-control" :class="{ 'is-invalid': formErrors.producto_id }" v-model="createForm.producto_id" required>
                                    <option value="">Seleccione un producto</option>
                                    <option v-for="pr in productos" :key="pr.id" :value="pr.id">{{ pr.nombre }}</option>
                                </select>
                                <div class="invalid-feedback" v-if="formErrors.producto_id">{{ formErrors.producto_id[0] }}</div>
                            </div>
                            <div class="form-group">
                                <label>Precio <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" :class="{ 'is-invalid': formErrors.precio }" v-model="createForm.precio" required step="0.01" min="0">
                                <div class="invalid-feedback" v-if="formErrors.precio">{{ formErrors.precio[0] }}</div>
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
                        <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Relación</h5>
                        <button type="button" class="close" @click="closeEditModal">&times;</button>
                    </div>
                    <form @submit.prevent="updateItem">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Proveedor <span class="text-danger">*</span></label>
                                <select class="form-control" :class="{ 'is-invalid': formErrors.proveedore_id }" v-model="editForm.proveedore_id" required>
                                    <option value="">Seleccione un proveedor</option>
                                    <option v-for="p in proveedores" :key="p.id" :value="p.id">{{ p.nombre }}</option>
                                </select>
                                <div class="invalid-feedback" v-if="formErrors.proveedore_id">{{ formErrors.proveedore_id[0] }}</div>
                            </div>
                            <div class="form-group">
                                <label>Producto <span class="text-danger">*</span></label>
                                <select class="form-control" :class="{ 'is-invalid': formErrors.producto_id }" v-model="editForm.producto_id" required>
                                    <option value="">Seleccione un producto</option>
                                    <option v-for="pr in productos" :key="pr.id" :value="pr.id">{{ pr.nombre }}</option>
                                </select>
                                <div class="invalid-feedback" v-if="formErrors.producto_id">{{ formErrors.producto_id[0] }}</div>
                            </div>
                            <div class="form-group">
                                <label>Precio <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" :class="{ 'is-invalid': formErrors.precio }" v-model="editForm.precio" required step="0.01" min="0">
                                <div class="invalid-feedback" v-if="formErrors.precio">{{ formErrors.precio[0] }}</div>
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
                        <h5 class="modal-title text-white"><i class="fas fa-eye"></i> Detalle Relación</h5>
                        <button type="button" class="close text-white" @click="closeViewModal">&times;</button>
                    </div>
                    <div class="modal-body" v-if="currentItem">
                        <table class="table table-bordered">
                            <tr><th width="40%">Proveedor</th><td>{{ currentItem.proveedore?.nombre || '-' }}</td></tr>
                            <tr><th>Producto</th><td>{{ currentItem.producto?.nombre || '-' }}</td></tr>
                            <tr><th>Precio</th><td>${{ currentItem.precio }}</td></tr>
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
import proveedoresProductoService from '@/services/proveedoresProducto';
import provedorService from '@/services/proveedores';
import productoService from '@/services/productos';

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
const proveedores = ref([]);
const productos = ref([]);
const createForm = reactive({ proveedore_id: '', producto_id: '', precio: '' });
const editForm = reactive({ proveedore_id: '', producto_id: '', precio: '' });
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
        const res = await proveedoresProductoService.index({ page, search: search.value });
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

async function loadProveedores() {
    try {
        const res = await provedorService.index({ per_page: 100 });
        proveedores.value = res.data.data || res.data || [];
    } catch (e) {
        console.error('Error loading proveedores:', e);
    }
}

async function loadProductos() {
    try {
        const res = await productoService.index({ per_page: 100 });
        productos.value = res.data.data || res.data || [];
    } catch (e) {
        console.error('Error loading productos:', e);
    }
}

function openCreateModal() {
    createForm.proveedore_id = '';
    createForm.producto_id = '';
    createForm.precio = '';
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
        await proveedoresProductoService.store(createForm);
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
    editForm.proveedore_id = item.proveedore_id || '';
    editForm.producto_id = item.producto_id || '';
    editForm.precio = item.precio || '';
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
        await proveedoresProductoService.update(currentItem.value.id, editForm);
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
        await proveedoresProductoService.destroy(item.id);
        success.value = 'Registro eliminado exitosamente';
        loadItems(currentPage.value);
    } catch (e) {
        error.value = 'Error al eliminar el registro';
    }
}

onMounted(() => {
    loadItems();
    loadProveedores();
    loadProductos();
});
</script>

<style scoped>
.dBlock { display: block !important; }
.form-control { border-radius: 8px; }
.btn-group .btn { margin: 0 2px; }
.invalid-feedback { display: block; }
</style>

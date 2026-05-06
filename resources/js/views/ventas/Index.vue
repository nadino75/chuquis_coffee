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
                    <i class="fas fa-shopping-cart mr-1"></i> Lista de Ventas
                </h5>
                <button type="button" class="btn btn-light btn-sm" @click="openCreateModal">
                    <i class="fas fa-plus-circle mr-1"></i> Nueva Venta
                </button>
            </div>
            <div class="card-body">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" v-model="search" placeholder="Buscar venta..." @keyup.enter="loadItems(1)">
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
                                <th>Recibo</th>
                                <th>Cliente</th>
                                <th>Tipo Pago</th>
                                <th>Total</th>
                                <th>Fecha</th>
                                <th width="15%">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in items.data" :key="item.id" class="text-center">
                                <td>{{ (items.current_page - 1) * items.per_page + index + 1 }}</td>
                                <td>{{ item.pago?.recibo || '-' }}</td>
                                <td>{{ item.cliente?.nombres || '-' }}</td>
                                <td><span class="badge" :class="pagoBadgeClass(item.pago?.tipo_pago)">{{ capitalize(item.pago?.tipo_pago) || '-' }}</span></td>
                                <td>${{ item.suma_total || item.total || 0 }}</td>
                                <td>{{ formatDate(item.created_at) }}</td>
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
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-shopping-cart fa-2x mb-2"></i><br>
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
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white"><i class="fas fa-plus"></i> Nueva Venta</h5>
                        <button type="button" class="close text-white" @click="closeCreateModal">&times;</button>
                    </div>
                    <form @submit.prevent="createItem">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Cliente <span class="text-danger">*</span></label>
                                        <select class="form-control" :class="{ 'is-invalid': formErrors.cliente_ci }" v-model="createForm.cliente_ci" required>
                                            <option value="">Seleccione un cliente</option>
                                            <option v-for="c in clientes" :key="c.ci" :value="c.ci">{{ c.nombres }} {{ c.apellido_paterno }}</option>
                                        </select>
                                        <div class="invalid-feedback" v-if="formErrors.cliente_ci">{{ formErrors.cliente_ci[0] }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Producto <span class="text-danger">*</span></label>
                                        <select class="form-control" :class="{ 'is-invalid': formErrors.producto_id }" v-model="createForm.producto_id" required @change="onProductChange">
                                            <option value="">Seleccione un producto</option>
                                            <option v-for="p in productos" :key="p.id" :value="p.id">{{ p.nombre }} (Stock: {{ p.stock }})</option>
                                        </select>
                                        <div class="invalid-feedback" v-if="formErrors.producto_id">{{ formErrors.producto_id[0] }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Cantidad <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" :class="{ 'is-invalid': formErrors.cantidad }" v-model.number="createForm.cantidad" required min="1" @input="updateTotal">
                                        <div class="invalid-feedback" v-if="formErrors.cantidad">{{ formErrors.cantidad[0] }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Precio <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" :class="{ 'is-invalid': formErrors.precio }" v-model.number="createForm.precio" required step="0.01" min="0" @input="updateTotal">
                                        <div class="invalid-feedback" v-if="formErrors.precio">{{ formErrors.precio[0] }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Total</label>
                                        <input type="text" class="form-control" :value="ventaTotal" readonly style="background-color: #2c2c2c; color: #daa520; font-weight: bold;">
                                    </div>
                                </div>
                            </div>

                            <!-- Payment section -->
                            <hr class="border-secondary">
                            <h6 class="text-gold"><i class="fas fa-credit-card mr-2"></i>Pago</h6>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tipo de Pago <span class="text-danger">*</span></label>
                                        <select class="form-control" :class="{ 'is-invalid': formErrors.tipo_pago }" v-model="createForm.tipo_pago" required>
                                            <option value="">Seleccione tipo</option>
                                            <option value="efectivo">Efectivo</option>
                                            <option value="tarjeta">Tarjeta</option>
                                            <option value="transferencia">Transferencia</option>
                                            <option value="qr">QR</option>
                                            <option value="mixto">Mixto (varios métodos)</option>
                                        </select>
                                        <div class="invalid-feedback" v-if="formErrors.tipo_pago">{{ formErrors.tipo_pago[0] }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Single payment -->
                            <div v-if="createForm.tipo_pago && createForm.tipo_pago !== 'mixto'">
                                <div class="form-group">
                                    <label>Monto Pagado <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" :class="{ 'is-invalid': formErrors.monto_pago }" v-model.number="createForm.monto_pago" required step="0.01" min="0">
                                    <small class="form-text text-muted" v-if="ventaTotal > 0">Total de la venta: ${{ ventaTotal }}</small>
                                    <div class="invalid-feedback" v-if="formErrors.monto_pago">{{ formErrors.monto_pago[0] }}</div>
                                </div>
                            </div>

                            <!-- Mixed payment section -->
                            <div v-if="createForm.tipo_pago === 'mixto'" class="border rounded p-3" style="border-color: #daa520 !important; background: rgba(218,165,32,0.05);">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0 text-gold">
                                        <i class="fas fa-layer-group mr-1"></i> Pago Dividido
                                    </h6>
                                    <span class="badge" :class="remainingBalance <= 0 ? 'bg-success' : 'bg-warning'">
                                        Restante: ${{ remainingBalance.toFixed(2) }}
                                    </span>
                                </div>

                                <div v-for="(pago, index) in createForm.pagos_mixtos" :key="index" class="row mb-2 align-items-end">
                                    <div class="col-md-4">
                                        <div class="form-group mb-0">
                                            <label class="small text-muted">Método {{ index + 1 }}</label>
                                            <select class="form-control form-control-sm" v-model="pago.tipo_pago" required>
                                                <option value="">Seleccione</option>
                                                <option value="efectivo">Efectivo</option>
                                                <option value="tarjeta">Tarjeta</option>
                                                <option value="transferencia">Transferencia</option>
                                                <option value="qr">QR</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-0">
                                            <label class="small text-muted">Monto</label>
                                            <input type="number" class="form-control form-control-sm" v-model.number="pago.monto" required step="0.01" min="0.01" @input="updateRemaining">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-sm btn-outline-danger" @click="removePago(index)" :disabled="createForm.pagos_mixtos.length <= 1">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-sm btn-outline-success" @click="addPago" :disabled="remainingBalance <= 0">
                                            <i class="fas fa-plus"></i> Agregar
                                        </button>
                                    </div>
                                </div>

                                <!-- Summary bar -->
                                <div class="mt-2 p-2 rounded" style="background: rgba(0,0,0,0.2);">
                                    <div class="d-flex justify-content-between text-white small">
                                        <span>Total venta: <strong>${{ ventaTotal }}</strong></span>
                                        <span>Total pagado: <strong>${{ pagadoSum }}</strong></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="closeCreateModal">Cancelar</button>
                            <button type="submit" class="btn btn-primary" :disabled="loading || (createForm.tipo_pago === 'mixto' && remainingBalance > 0.01)">
                                <span v-if="loading" class="spinner-border spinner-border-sm mr-1"></span>
                                <span v-if="createForm.tipo_pago === 'mixto' && remainingBalance > 0.01">Falta completar ${{ remainingBalance.toFixed(2) }}</span>
                                <span v-else>Registrar Venta</span>
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
                        <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Venta</h5>
                        <button type="button" class="close" @click="closeEditModal">&times;</button>
                    </div>
                    <form @submit.prevent="updateItem">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Total</label>
                                <input type="number" class="form-control" :class="{ 'is-invalid': formErrors.suma_total }" v-model="editForm.suma_total" step="0.01" min="0">
                                <div class="invalid-feedback" v-if="formErrors.suma_total">{{ formErrors.suma_total[0] }}</div>
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
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info">
                        <h5 class="modal-title text-white"><i class="fas fa-eye"></i> Detalle Venta</h5>
                        <button type="button" class="close text-white" @click="closeViewModal">&times;</button>
                    </div>
                    <div class="modal-body" v-if="currentItem">
                        <table class="table table-bordered">
                            <tr><th width="40%">Recibo</th><td>{{ currentItem.pago?.recibo || '-' }}</td></tr>
                            <tr><th>Tipo de Pago</th><td><span class="badge" :class="pagoBadgeClass(currentItem.pago?.tipo_pago)">{{ capitalize(currentItem.pago?.tipo_pago) || '-' }}</span></td></tr>
                            <tr><th>Cliente</th><td>{{ currentItem.cliente?.nombres || '-' }}</td></tr>
                            <tr><th>Total</th><td class="text-gold font-weight-bold">${{ currentItem.suma_total || currentItem.total || 0 }}</td></tr>
                            <tr><th>Fecha</th><td>{{ formatDate(currentItem.created_at) }}</td></tr>
                        </table>

                        <!-- Mixed payment details -->
                        <div v-if="currentItem.pago?.tipo_pago === 'mixto' && currentItem.pago?.pagos_hijos?.length">
                            <h6 class="text-gold mt-3"><i class="fas fa-layer-group mr-2"></i>Desglose de Pago Mixto</h6>
                            <table class="table table-sm table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Método</th>
                                        <th>Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(sub, i) in currentItem.pago.pagos_hijos" :key="i">
                                        <td><span class="badge bg-info">{{ capitalize(sub.tipo_pago) }}</span></td>
                                        <td class="font-weight-bold">${{ Number(sub.total_pagado).toFixed(2) }}</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="table-secondary">
                                        <td><strong>Total</strong></td>
                                        <td><strong>${{ currentItem.pago.total_pagado }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
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
import ventaService from '@/services/ventas';
import clienteService from '@/services/clientes';
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
const clientes = ref([]);
const productos = ref([]);
const createForm = reactive({
    cliente_ci: '',
    producto_id: '',
    cantidad: 1,
    precio: 0,
    tipo_pago: '',
    monto_pago: 0,
    pagos_mixtos: [{ tipo_pago: '', monto: 0 }],
});
const editForm = reactive({ suma_total: '' });
const formErrors = reactive({});

const ventaTotal = computed(() => {
    return (createForm.cantidad || 0) * (createForm.precio || 0);
});

const pagadoSum = computed(() => {
    return createForm.pagos_mixtos.reduce((sum, p) => sum + (Number(p.monto) || 0), 0);
});

const remainingBalance = computed(() => {
    return ventaTotal.value - pagadoSum.value;
});

const visiblePages = computed(() => {
    const pages = [];
    const start = Math.max(1, currentPage.value - 2);
    const end = Math.min(items.value.last_page, currentPage.value + 2);
    for (let i = start; i <= end; i++) pages.push(i);
    return pages;
});

function capitalize(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function pagoBadgeClass(tipo) {
    const map = { efectivo: 'badge-success', tarjeta: 'badge-info', transferencia: 'badge-warning', qr: 'badge-primary', mixto: 'badge-mixto' };
    return map[tipo] || 'badge-secondary';
}

function formatDate(d) {
    if (!d) return '-';
    const date = new Date(d);
    return date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' }) + ' ' +
           date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
}

function onProductChange() {
    const p = productos.value.find(p => p.id === createForm.producto_id);
    if (p) {
        createForm.precio = Number(p.precio);
        updateTotal();
    }
}

function updateTotal() {
    if (createForm.tipo_pago === 'mixto') {
        updateRemaining();
    }
}

function updateRemaining() {
    // Recalculate remaining when mixed payment amounts change
}

function addPago() {
    createForm.pagos_mixtos.push({ tipo_pago: '', monto: 0 });
}

function removePago(index) {
    if (createForm.pagos_mixtos.length > 1) {
        createForm.pagos_mixtos.splice(index, 1);
    }
}

async function loadItems(page = 1) {
    loading.value = true;
    try {
        const res = await ventaService.index({ page, search: search.value });
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

async function loadClientes() {
    try {
        const res = await clienteService.index({ per_page: 100 });
        clientes.value = res.data.data || res.data || [];
    } catch (e) {
        console.error('Error loading clientes:', e);
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
    createForm.cliente_ci = '';
    createForm.producto_id = '';
    createForm.cantidad = 1;
    createForm.precio = 0;
    createForm.tipo_pago = '';
    createForm.monto_pago = 0;
    createForm.pagos_mixtos = [{ tipo_pago: '', monto: 0 }];
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
        const payload = {
            cliente_ci: createForm.cliente_ci,
            producto_id: createForm.producto_id,
            cantidad: createForm.cantidad,
            precio: createForm.precio,
            tipo_pago: createForm.tipo_pago,
        };

        if (createForm.tipo_pago === 'mixto') {
            payload.pagos_mixtos = createForm.pagos_mixtos
                .filter(p => p.tipo_pago && p.monto > 0)
                .map(p => ({ tipo_pago: p.tipo_pago, monto: Number(p.monto) }));
        } else {
            payload.monto_pago = createForm.monto_pago;
        }

        await ventaService.store(payload);
        success.value = 'Venta registrada exitosamente';
        closeCreateModal();
        loadItems(currentPage.value);
    } catch (e) {
        if (e.response?.data?.errors) Object.assign(formErrors, e.response.data.errors);
        else if (e.response?.data?.message) error.value = e.response.data.message;
        else error.value = 'Error al crear la venta';
    } finally {
        loading.value = false;
    }
}

function openEditModal(item) {
    currentItem.value = { ...item };
    editForm.suma_total = item.suma_total || item.total || '';
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
        await ventaService.update(currentItem.value.id, editForm);
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
    if (!confirm('¿Está seguro de eliminar esta venta?')) return;
    try {
        await ventaService.destroy(item.id);
        success.value = 'Venta eliminada exitosamente';
        loadItems(currentPage.value);
    } catch (e) {
        error.value = 'Error al eliminar la venta';
    }
}

onMounted(() => {
    loadItems();
    loadClientes();
    loadProductos();
});
</script>

<style scoped>
.dBlock { display: block !important; }
.form-control { border-radius: 8px; }
.btn-group .btn { margin: 0 2px; }
.invalid-feedback { display: block; }
.text-gold { color: #daa520; }
.badge-mixto {
    background: linear-gradient(135deg, #daa520, #b8860b);
    color: #1a1a1a;
    font-weight: 600;
}
</style>

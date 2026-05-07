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
                                <td>Bs. {{ item.suma_total || item.total || 0 }}</td>
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
        <div class="modal fade" :class="{ show: showCreate, dBlock: showCreate }" tabindex="-1" style="background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);" v-if="showCreate">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content modal-content--enhanced">
                    <!-- Modal Header -->
                    <div class="modal-header modal-header--enhanced">
                        <div class="modal-header-content">
                            <div class="modal-header-icon">
                                <i class="fas fa-cash-register"></i>
                            </div>
                            <div>
                                <h5 class="modal-title modal-title--enhanced">Nueva Venta</h5>
                                <p class="modal-subtitle">Registra una venta rápida para el cliente</p>
                            </div>
                        </div>
                        <button type="button" class="btn-modal-close" @click="closeCreateModal">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form @submit.prevent="createItem">
                        <div class="modal-body modal-body--enhanced">
                            <!-- Section: Product Selection -->
                            <div class="form-section">
                                <div class="form-section-header">
                                    <i class="fas fa-coffee"></i>
                                    <span>Selección de Producto</span>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label--enhanced">
                                                Cliente <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control form-control--enhanced" :class="{ 'is-invalid': formErrors.cliente_ci }" v-model="createForm.cliente_ci" required>
                                                <option value="">Seleccione un cliente</option>
                                                <option v-for="c in clientes" :key="c.ci" :value="c.ci">
                                                    <i class="fas fa-user"></i> {{ c.nombres }} {{ c.apellido_paterno }}
                                                </option>
                                            </select>
                                            <div class="invalid-feedback" v-if="formErrors.cliente_ci">{{ formErrors.cliente_ci[0] }}</div>
                                        </div>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="form-group">
                                            <label class="form-label--enhanced">
                                                Producto <span class="text-danger">*</span>
                                                <span class="badge-product-count" v-if="productos.length">
                                                    <i class="fas fa-boxes"></i> {{ productos.length }} disponibles
                                                </span>
                                            </label>
                                            <input
                                                type="text"
                                                class="form-control form-control--enhanced product-search-input"
                                                v-model="productoSearch"
                                                placeholder="🔍 Buscar producto por nombre..."
                                                @focus="showProductGrid = true"
                                            />
                                            <!-- Selected product display -->
                                            <div v-if="selectedProduct" class="selected-product-card--enhanced">
                                                <div class="selected-product-info">
                                                    <div class="selected-product-icon">
                                                        <i :class="selectedProduct.icono || 'fas fa-coffee'"></i>
                                                    </div>
                                                    <div class="selected-product-details">
                                                        <span class="selected-name">{{ selectedProduct.nombre }}</span>
                                                        <span class="selected-stock" :class="selectedProduct.stock > 10 ? 'text-success' : selectedProduct.stock > 0 ? 'text-warning' : 'text-danger'">
                                                            <i class="fas" :class="selectedProduct.stock > 0 ? 'fa-check-circle' : 'fa-times-circle'"></i>
                                                            {{ selectedProduct.stock > 0 ? selectedProduct.stock + ' en stock' : 'Sin stock' }}
                                                        </span>
                                                    </div>
                                                    <div class="selected-price--enhanced">
                                                        Bs. {{ Number(selectedProduct.precio).toLocaleString('es-ES', {minimumFractionDigits: 2}) }}
                                                    </div>
                                                </div>
                                                <button type="button" class="btn-clear-selection--enhanced" @click="clearProductSelection" title="Cambiar producto">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <!-- Product Grid -->
                                            <div v-show="showProductGrid || !selectedProduct" class="product-grid-container--enhanced">
                                                <div v-if="!filteredProductos.length" class="product-empty--enhanced">
                                                    <i class="fas fa-search"></i>
                                                    <p>No se encontraron productos con "{{ productoSearch }}"</p>
                                                    <button type="button" class="btn btn-sm btn-outline-light" @click="productoSearch = ''">
                                                        <i class="fas fa-redo"></i> Limpiar búsqueda
                                                    </button>
                                                </div>
                                                <div v-else class="product-grid--enhanced">
                                                    <div
                                                        v-for="p in filteredProductos"
                                                        :key="p.id"
                                                        class="product-card--enhanced"
                                                        :class="{
                                                            'product-card--selected': createForm.producto_id === p.id,
                                                            'product-card--out-of-stock': p.stock <= 0
                                                        }"
                                                        @click="selectProduct(p)"
                                                    >
                                                        <div class="product-card__header">
                                                            <div class="product-card__icon--enhanced">
                                                                <i :class="p.icono || 'fas fa-coffee'"></i>
                                                            </div>
                                                            <div v-if="createForm.producto_id === p.id" class="product-card__check">
                                                                <i class="fas fa-check"></i>
                                                            </div>
                                                        </div>
                                                        <div class="product-card__body--enhanced">
                                                            <span class="product-card__name--enhanced">{{ p.nombre }}</span>
                                                            <span class="product-card__price--enhanced">
                                                                Bs. {{ Number(p.precio).toLocaleString('es-ES', {minimumFractionDigits: 2}) }}
                                                            </span>
                                                            <span class="product-card__stock--enhanced" :class="p.stock > 10 ? 'in-stock' : p.stock > 0 ? 'low-stock' : 'out-of-stock'">
                                                                <i class="fas" :class="p.stock > 10 ? 'fa-check-circle' : p.stock > 0 ? 'fa-exclamation-triangle' : 'fa-times-circle'"></i>
                                                                {{ p.stock > 0 ? p.stock + ' disp.' : 'Agotado' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="invalid-feedback" v-if="formErrors.producto_id">{{ formErrors.producto_id[0] }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section: Quantity and Price -->
                            <div class="form-section" v-if="selectedProduct">
                                <div class="form-section-header">
                                    <i class="fas fa-calculator"></i>
                                    <span>Detalles de la Venta</span>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label--enhanced">
                                                Cantidad <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group input-group--enhanced">
                                                <button class="btn btn-outline-gold" type="button" @click="createForm.cantidad = Math.max(1, createForm.cantidad - 1)">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" class="form-control form-control--enhanced text-center" :class="{ 'is-invalid': formErrors.cantidad }" v-model.number="createForm.cantidad" required min="1" @input="updateTotal">
                                                <button class="btn btn-outline-gold" type="button" @click="createForm.cantidad++">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                            <small class="form-text text-muted">Stock disp: {{ selectedProduct.stock }}</small>
                                            <div class="invalid-feedback" v-if="formErrors.cantidad">{{ formErrors.cantidad[0] }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label--enhanced">
                                                Precio Unit. <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group input-group--enhanced">
                                                <span class="input-group-text input-group-text--enhanced">Bs.</span>
                                                <input type="number" class="form-control form-control--enhanced" :class="{ 'is-invalid': formErrors.precio }" v-model.number="createForm.precio" required step="0.01" min="0" @input="updateTotal">
                                            </div>
                                            <div class="invalid-feedback" v-if="formErrors.precio">{{ formErrors.precio[0] }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label--enhanced">Subtotal</label>
                                            <div class="total-display">
                                                <span class="total-display__label">Total:</span>
                                                <span class="total-display__amount">Bs. {{ ventaTotal.toLocaleString('es-ES', {minimumFractionDigits: 2}) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label--enhanced">Stock Restante</label>
                                            <div class="stock-remaining" :class="(selectedProduct.stock - createForm.cantidad) >= 0 ? 'stock-ok' : 'stock-insufficient'">
                                                <i class="fas" :class="(selectedProduct.stock - createForm.cantidad) >= 0 ? 'fa-check-circle' : 'fa-exclamation-circle'"></i>
                                                {{ Math.max(0, selectedProduct.stock - createForm.cantidad) }} restantes
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section: Payment -->
                            <div class="form-section" v-if="selectedProduct">
                                <div class="form-section-header">
                                    <i class="fas fa-credit-card"></i>
                                    <span>Información de Pago</span>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label--enhanced">
                                                Tipo de Pago <span class="text-danger">*</span>
                                            </label>
                                            <div class="payment-methods-grid">
                                                <div
                                                    v-for="method in paymentMethods"
                                                    :key="method.value"
                                                    class="payment-method-card"
                                                    :class="{ 'payment-method--selected': createForm.tipo_pago === method.value }"
                                                    @click="createForm.tipo_pago = method.value"
                                                >
                                                    <i :class="method.icon"></i>
                                                    <span>{{ method.label }}</span>
                                                </div>
                                            </div>
                                            <div class="invalid-feedback" v-if="formErrors.tipo_pago">{{ formErrors.tipo_pago[0] }}</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cash Payment -->
                                <div v-if="createForm.tipo_pago === 'efectivo'" class="payment-detail-card">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label--enhanced">
                                                    Monto Recibido <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group input-group--enhanced">
                                                    <span class="input-group-text input-group-text--enhanced">Bs.</span>
                                                    <input type="number" class="form-control form-control--enhanced" :class="{ 'is-invalid': formErrors.monto_recibido }" v-model.number="createForm.monto_recibido" required step="0.01" :min="ventaTotal" min="0">
                                                </div>
                                                <small class="form-text text-muted">Monto que entrega el cliente</small>
                                                <div class="invalid-feedback" v-if="formErrors.monto_recibido">{{ formErrors.monto_recibido[0] }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="change-display">
                                                <div class="change-display__label">
                                                    <i class="fas fa-hand-holding-usd"></i> Cambio a Entregar
                                                </div>
                                                <div class="change-display__amount" :class="Number(createForm.monto_recibido) >= ventaTotal ? 'text-success' : 'text-danger'">
                                                    {{ cambio }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Other Payment Types -->
                                <div v-if="createForm.tipo_pago && createForm.tipo_pago !== 'mixto' && createForm.tipo_pago !== 'efectivo'" class="payment-detail-card">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label--enhanced">
                                                    Monto Pagado <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group input-group--enhanced">
                                                    <span class="input-group-text input-group-text--enhanced">Bs.</span>
                                                    <input type="number" class="form-control form-control--enhanced" :class="{ 'is-invalid': formErrors.monto_pago }" v-model.number="createForm.monto_pago" required step="0.01" min="0">
                                                </div>
                                                <small class="form-text text-muted">Total: Bs. {{ ventaTotal.toLocaleString('es-ES', {minimumFractionDigits: 2}) }}</small>
                                                <div class="invalid-feedback" v-if="formErrors.monto_pago">{{ formErrors.monto_pago[0] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Mixed Payment -->
                                <div v-if="createForm.tipo_pago === 'mixto'" class="payment-mixed-card">
                                    <div class="payment-mixed-header">
                                        <h6 class="text-gold mb-0">
                                            <i class="fas fa-layer-group mr-1"></i> Pago Dividido
                                        </h6>
                                        <span class="badge" :class="remainingBalance <= 0 ? 'bg-success' : 'bg-warning'">
                                            Restante: Bs. {{ remainingBalance.toFixed(2) }}
                                        </span>
                                    </div>

                                    <div v-for="(pago, index) in createForm.pagos_mixtos" :key="index" class="mixed-payment-row">
                                        <div class="mixed-payment-method">
                                            <label class="small text-muted">Método {{ index + 1 }}</label>
                                            <select class="form-control form-control-sm form-control--enhanced" v-model="pago.tipo_pago" required>
                                                <option value="">Seleccione</option>
                                                <option value="efectivo">Efectivo</option>
                                                <option value="tarjeta">Tarjeta</option>
                                                <option value="transferencia">Transferencia</option>
                                                <option value="qr">QR</option>
                                            </select>
                                        </div>
                                        <div class="mixed-payment-amount">
                                            <label class="small text-muted">Monto</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text input-group-text--enhanced">Bs.</span>
                                                <input type="number" class="form-control form-control-sm form-control--enhanced" v-model.number="pago.monto" required step="0.01" min="0.01" @input="updateRemaining">
                                            </div>
                                        </div>
                                        <div class="mixed-payment-actions">
                                            <button type="button" class="btn btn-sm btn-outline-danger" @click="removePago(index)" :disabled="createForm.pagos_mixtos.length <= 1">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn-sm btn-outline-success btn-add-payment" @click="addPago" :disabled="remainingBalance <= 0">
                                        <i class="fas fa-plus"></i> Agregar Método de Pago
                                    </button>

                                    <div class="mixed-payment-summary">
                                        <div class="d-flex justify-content-between text-white">
                                            <span>Total venta: <strong>Bs. {{ ventaTotal.toLocaleString('es-ES', {minimumFractionDigits: 2}) }}</strong></span>
                                            <span>Total pagado: <strong>Bs. {{ pagadoSum.toLocaleString('es-ES', {minimumFractionDigits: 2}) }}</strong></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer modal-footer--enhanced">
                            <button type="button" class="btn btn-secondary btn--enhanced" @click="closeCreateModal">
                                <i class="fas fa-times mr-1"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary btn--enhanced" :disabled="loading || (createForm.tipo_pago === 'mixto' && remainingBalance > 0.01)">
                                <span v-if="loading" class="spinner-border spinner-border-sm mr-1"></span>
                                <span v-if="createForm.tipo_pago === 'mixto' && remainingBalance > 0.01">
                                    <i class="fas fa-exclamation-circle mr-1"></i> Falta Bs. {{ remainingBalance.toFixed(2) }}
                                </span>
                                <span v-else>
                                    <i class="fas fa-check mr-1"></i> Registrar Venta
                                </span>
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
                        <h5 class="modal-title text-white"><i class="fas fa-edit"></i> Editar Venta</h5>
                        <button type="button" class="close text-white" @click="closeEditModal">&times;</button>
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
                            <tr><th>Total</th>                                <td class="text-gold font-weight-bold">Bs. {{ currentItem.suma_total || currentItem.total || 0 }}</td></tr>
                            <tr v-if="currentItem.pago?.tipo_pago === 'efectivo'"><th>Monto Recibido</th>                                        <td class="font-weight-bold">Bs. {{ Number(currentItem.pago.monto_recibido || 0).toFixed(2) }}</td></tr>
                            <tr v-if="currentItem.pago?.tipo_pago === 'efectivo'"><th>Cambio</th>                                        <td class="font-weight-bold" style="color: #4caf50;">Bs. {{ Number(currentItem.pago.cambio || 0).toFixed(2) }}</td></tr>
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
                                         <td class="font-weight-bold">Bs. {{ Number(sub.total_pagado).toFixed(2) }}</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="table-secondary">
                                        <td><strong>Total</strong></td>
                                         <td><strong>Bs. {{ currentItem.pago.total_pagado }}</strong></td>
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
const productoSearch = ref('');
const showProductGrid = ref(false);
const showCreate = ref(false);
const showEdit = ref(false);
const showView = ref(false);
const currentItem = ref(null);
const currentPage = ref(1);
const clientes = ref([]);
const productos = ref([]);
const selectedProduct = ref(null);
const createForm = reactive({
    cliente_ci: '',
    producto_id: '',
    cantidad: 1,
    precio: 0,
    tipo_pago: '',
    monto_pago: 0,
    monto_recibido: 0,
    pagos_mixtos: [{ tipo_pago: '', monto: 0 }],
});
const editForm = reactive({ suma_total: '' });
const formErrors = reactive({});

const filteredProductos = computed(() => {
    if (!productoSearch.value) return productos.value;
    const q = productoSearch.value.toLowerCase();
    return productos.value.filter(p => p.nombre.toLowerCase().includes(q));
});

const paymentMethods = [
    { value: 'efectivo', label: 'Efectivo', icon: 'fas fa-money-bill-wave' },
    { value: 'tarjeta', label: 'Tarjeta', icon: 'fas fa-credit-card' },
    { value: 'transferencia', label: 'Transferencia', icon: 'fas fa-exchange-alt' },
    { value: 'qr', label: 'QR', icon: 'fas fa-qrcode' },
    { value: 'mixto', label: 'Mixto', icon: 'fas fa-layer-group' },
];

const ventaTotal = computed(() => {
    return (createForm.cantidad || 0) * (createForm.precio || 0);
});

const pagadoSum = computed(() => {
    return createForm.pagos_mixtos.reduce((sum, p) => sum + (Number(p.monto) || 0), 0);
});

const remainingBalance = computed(() => {
    return ventaTotal.value - pagadoSum.value;
});

const cambio = computed(() => {
    const recib = Number(createForm.monto_recibido) || 0;
    const change = recib - ventaTotal.value;
    return change >= 0 ? `Bs. ${change.toFixed(2)}` : 'Bs. 0.00';
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
        selectedProduct.value = p;
        updateTotal();
    }
}

function selectProduct(p) {
    if (p.stock <= 0) return;
    createForm.producto_id = p.id;
    createForm.precio = Number(p.precio);
    selectedProduct.value = p;
    showProductGrid.value = false;
    productoSearch.value = '';
    updateTotal();
}

function clearProductSelection() {
    createForm.producto_id = '';
    createForm.precio = 0;
    selectedProduct.value = null;
    showProductGrid.value = true;
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
    createForm.monto_recibido = 0;
    createForm.pagos_mixtos = [{ tipo_pago: '', monto: 0 }];
    selectedProduct.value = null;
    showProductGrid.value = false;
    productoSearch.value = '';
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
        if (createForm.tipo_pago === 'efectivo' && Number(createForm.monto_recibido) < 0) {
            formErrors.monto_recibido = ['El monto recibido no puede ser negativo'];
            loading.value = false;
            return;
        }
        if (createForm.tipo_pago && createForm.tipo_pago !== 'mixto' && createForm.tipo_pago !== 'efectivo' && Number(createForm.monto_pago) < 0) {
            formErrors.monto_pago = ['El monto pagado no puede ser negativo'];
            loading.value = false;
            return;
        }
        if (createForm.tipo_pago === 'mixto') {
            const hasNegative = createForm.pagos_mixtos.some(p => p.monto < 0);
            if (hasNegative) {
                error.value = 'Los montos de pago no pueden ser negativos';
                loading.value = false;
                return;
            }
        }

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
        } else if (createForm.tipo_pago === 'efectivo') {
            payload.monto_recibido = createForm.monto_recibido;
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
/* ══════════════════════════════════════════════════════════════════════════
   ENHANCED SALES MODAL STYLES — CASHIER-FIRST DESIGN
   ══════════════════════════════════════════════════════════════════════════ */

.dBlock { display: block !important; }

/* ── Modal Enhanced ─────────────────────────────────────────── */
.modal-content--enhanced {
    background: linear-gradient(160deg, #fefcf5 0%, #fdf6ec 50%, #fef9f0 100%);
    border: 1px solid rgba(218, 165, 32, 0.35);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
}

.modal-header--enhanced {
    background: linear-gradient(135deg, rgba(218, 165, 32, 0.12), rgba(255, 255, 255, 0.8));
    border-bottom: 1px solid rgba(218, 165, 32, 0.25);
    padding: 1.25rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.modal-header-content {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex: 1;
}

.modal-header-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: linear-gradient(135deg, rgba(218, 165, 32, 0.2), rgba(184, 134, 11, 0.15));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    color: #8B4513;
}

.modal-title--enhanced {
    color: #5d4037;
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
}

.modal-subtitle {
    color: rgba(93, 64, 55, 0.6);
    font-size: 0.85rem;
    margin: 0.15rem 0 0 0;
}

.btn-modal-close {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: rgba(220, 53, 69, 0.15);
    border: 1px solid rgba(220, 53, 69, 0.25);
    color: #ef9a9a;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 1rem;
}

.btn-modal-close:hover {
    background: rgba(220, 53, 69, 0.3);
    color: #fff;
}

.modal-body--enhanced {
    padding: 1.5rem;
    max-height: 75vh;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: rgba(139, 69, 19, 0.3) rgba(254, 252, 245, 0.8);
}

.modal-body--enhanced::-webkit-scrollbar {
    width: 6px;
}

.modal-body--enhanced::-webkit-scrollbar-track {
    background: transparent;
}

.modal-body--enhanced::-webkit-scrollbar-thumb {
    background: rgba(218, 165, 32, 0.3);
    border-radius: 3px;
}

/* ── Form Sections ───────────────────────────────────────────── */
.form-section {
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid rgba(218, 165, 32, 0.2);
}

.form-section:last-of-type {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.form-section-header {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    color: #daa520;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 1rem;
}

.form-section-header i {
    font-size: 1rem;
}

/* ── Form Controls Enhanced ─────────────────────────────────── */
.form-label--enhanced {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: rgba(93, 64, 55, 0.9);
    font-size: 0.88rem;
}

.form-control--enhanced {
    background: rgba(255, 255, 255, 0.9);
    border: 2px solid rgba(218, 165, 32, 0.25);
    border-radius: 10px;
    color: #5d4037;
    padding: 0.6rem 1rem;
    transition: all 0.2s ease;
    font-family: 'Manrope', sans-serif;
}

.form-control--enhanced:focus {
    border-color: #8B4513;
    box-shadow: 0 0 0 0.2rem rgba(139, 69, 19, 0.15);
    background: #fff;
    outline: none;
}

.form-control--enhanced.is-invalid {
    border-color: #dc3545;
}

.input-group--enhanced {
    display: flex;
    align-items: stretch;
}

.input-group-text--enhanced {
    background: rgba(218, 165, 32, 0.12);
    border: 2px solid rgba(218, 165, 32, 0.25);
    border-right: none;
    color: #8B4513;
    font-weight: 600;
    border-radius: 10px 0 0 10px;
    padding: 0.6rem 0.75rem;
    font-family: 'Cormorant Garamond', serif;
    font-size: 1rem;
}

.input-group--enhanced .form-control--enhanced {
    border-left: none;
    border-radius: 0 10px 10px 0;
}

.input-group--enhanced .btn-outline-gold {
    border: 2px solid rgba(218, 165, 32, 0.25);
    border-left: none;
    border-radius: 0 10px 10px 0;
    color: #8B4513;
    background: rgba(218, 165, 32, 0.1);
    transition: all 0.2s ease;
}

.input-group--enhanced .btn-outline-gold:hover {
    background: rgba(218, 165, 32, 0.25);
}

.badge-product-count {
    font-size: 0.72rem;
    background: rgba(139, 69, 19, 0.12);
    color: rgba(93, 64, 55, 0.7);
    padding: 0.2rem 0.6rem;
    border-radius: 20px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

/* ── Product Search ───────────────────────────────────────────── */
.product-search-input {
    background: rgba(255, 255, 255, 0.9) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%238B4513' class='bi bi-search' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.002 6.002 0 1 1 .953-.953l3.602 3.602a.75.75 0 0 1-1.06 1.06l-3.602-3.602zM11 6a5 5 0 1 1 0-10 5 5 0 0 1 0 10z'/%3E%3C/svg%3E") no-repeat 12px center;
    background-size: 16px;
    border: 2px solid rgba(139, 69, 19, 0.25);
    border-radius: 10px;
    color: #5d4037;
    padding: 0.6rem 1rem 0.6rem 2.5rem;
    transition: all 0.2s ease;
    font-family: 'Manrope', sans-serif;
}

.product-search-input:focus {
    border-color: #daa520;
    box-shadow: 0 0 0 0.2rem rgba(218, 165, 32, 0.2);
    background-color: rgba(26, 15, 10, 0.8);
    outline: none;
}

/* ── Selected Product Card Enhanced ─────────────────────────── */
.selected-product-card--enhanced {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: linear-gradient(135deg, rgba(218, 165, 32, 0.12), rgba(255, 255, 255, 0.9));
    border: 2px solid rgba(218, 165, 32, 0.35);
    border-radius: 14px;
    padding: 1rem;
    margin-bottom: 0.75rem;
    animation: slideIn 0.3s ease;
    position: relative;
    overflow: hidden;
}

.selected-product-card--enhanced::before {
    content: '';
    position: absolute;
    top: -30px;
    right: -30px;
    width: 80px;
    height: 80px;
    background: radial-gradient(circle, rgba(76, 175, 80, 0.1) 0%, transparent 70%);
    border-radius: 50%;
}

@keyframes slideIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.selected-product-icon {
    width: 52px;
    height: 52px;
    min-width: 52px;
    border-radius: 14px;
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.15), rgba(56, 142, 60, 0.1));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    color: #2E7D32;
}

.selected-product-details {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}

.selected-product-details .selected-name {
    color: rgba(93, 64, 55, 0.95);
    font-weight: 600;
    font-size: 1rem;
}

.selected-product-details .selected-stock {
    font-size: 0.78rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.selected-price--enhanced {
    color: #8B4513;
    font-weight: 700;
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.25rem;
}

.btn-clear-selection--enhanced {
    width: 36px;
    height: 36px;
    min-width: 36px;
    border-radius: 10px;
    background: rgba(220, 53, 69, 0.1);
    border: 1px solid rgba(220, 53, 69, 0.2);
    color: #C62828;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-clear-selection--enhanced:hover {
    background: rgba(220, 53, 69, 0.35);
    color: #fff;
    transform: scale(1.05);
}

/* ── Product Grid Container Enhanced ──────────────────────── */
.product-grid-container--enhanced {
    max-height: 300px;
    overflow-y: auto;
    border: 2px solid rgba(218, 165, 32, 0.2);
    border-radius: 14px;
    padding: 0.75rem;
    background: rgba(255, 255, 255, 0.6);
    scrollbar-width: thin;
    scrollbar-color: rgba(139, 69, 19, 0.3) rgba(255, 255, 255, 0.8);
}

.product-grid-container--enhanced::-webkit-scrollbar {
    width: 6px;
}

.product-grid-container--enhanced::-webkit-scrollbar-track {
    background: transparent;
}

.product-grid-container--enhanced::-webkit-scrollbar-thumb {
    background: rgba(218, 165, 32, 0.3);
    border-radius: 3px;
}

.product-empty--enhanced {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2.5rem 1rem;
    color: rgba(93, 64, 55, 0.5);
}

.product-empty--enhanced i {
    font-size: 2.5rem;
    margin-bottom: 0.75rem;
    opacity: 0.5;
    color: rgba(139, 69, 19, 0.4);
}

.product-empty--enhanced p {
    margin: 0 0 1rem 0;
    font-size: 0.9rem;
}

/* ── Product Grid Enhanced ─────────────────────────────────── */
.product-grid--enhanced {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 0.75rem;
}

/* ── Product Card Enhanced ─────────────────────────────────── */
.product-card--enhanced {
    background: linear-gradient(150deg, #fefcf5, #fff);
    border: 2px solid rgba(218, 165, 32, 0.2);
    border-radius: 14px;
    padding: 1rem 0.75rem;
    cursor: pointer;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.product-card--enhanced::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #daa520, #b8860b);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.product-card--enhanced:hover {
    border-color: rgba(218, 165, 32, 0.4);
    transform: translateY(-4px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.45);
}

.product-card--enhanced:hover::before {
    opacity: 1;
}

.product-card--selected {
    border-color: #8B4513 !important;
    background: linear-gradient(150deg, #fdf6ec, #fefcf5);
    box-shadow: 0 4px 20px rgba(139, 69, 19, 0.15);
}

.product-card--selected::before {
    opacity: 1;
}

.product-card--selected::after {
    content: '\f00c';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    position: absolute;
    top: 8px;
    right: 8px;
    color: #4caf50;
    font-size: 0.75rem;
    background: rgba(76, 175, 80, 0.2);
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: checkPop 0.3s ease;
}

@keyframes checkPop {
    0% { transform: scale(0); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

.product-card--out-of-stock {
    opacity: 0.45;
    cursor: not-allowed;
    filter: grayscale(0.3);
}

.product-card--out-of-stock:hover {
    transform: none;
    box-shadow: none;
    border-color: rgba(220, 53, 69, 0.2);
}

.product-card__header {
    position: relative;
    width: 100%;
    display: flex;
    justify-content: center;
    margin-bottom: 0.5rem;
}

.product-card__icon--enhanced {
    width: 50px;
    height: 50px;
    border-radius: 14px;
    background: linear-gradient(135deg, rgba(218, 165, 32, 0.15), rgba(184, 134, 11, 0.1));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    color: #8B4513;
    transition: all 0.3s ease;
}

.product-card--enhanced:hover .product-card__icon--enhanced {
    transform: scale(1.1);
    background: linear-gradient(135deg, rgba(218, 165, 32, 0.28), rgba(184, 134, 11, 0.22));
}

.product-card__check {
    position: absolute;
    top: -4px;
    right: -4px;
    color: #4caf50;
    font-size: 0.7rem;
}

.product-card__body--enhanced {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    width: 100%;
}

.product-card__name--enhanced {
    color: rgba(93, 64, 55, 0.92);
    font-size: 0.84rem;
    font-weight: 600;
    line-height: 1.3;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 100%;
}

.product-card__price--enhanced {
    color: #8B4513;
    font-weight: 700;
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.05rem;
}

.product-card__stock--enhanced {
    font-size: 0.7rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.3rem;
    padding: 0.2rem 0.6rem;
    border-radius: 25px;
    font-weight: 500;
    width: fit-content;
    align-self: center;
}

.product-card__stock--enhanced.in-stock {
    color: #2E7D32;
    background: rgba(46, 125, 50, 0.1);
    border: 1px solid rgba(46, 125, 50, 0.2);
}

.product-card__stock--enhanced.low-stock {
    color: #E65100;
    background: rgba(230, 81, 0, 0.1);
    border: 1px solid rgba(230, 81, 0, 0.2);
}

.product-card__stock--enhanced.out-of-stock {
    color: #C62828;
    background: rgba(198, 40, 40, 0.1);
    border: 1px solid rgba(198, 40, 40, 0.2);
}

/* ── Total Display ────────────────────────────────────────────── */
.total-display {
    background: linear-gradient(135deg, rgba(218, 165, 32, 0.1), rgba(255, 255, 255, 0.8));
    border: 2px solid rgba(218, 165, 32, 0.25);
    border-radius: 12px;
    padding: 0.75rem 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}

.total-display__label {
    font-size: 0.78rem;
    color: rgba(93, 64, 55, 0.6);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.total-display__amount {
    color: #8B4513;
    font-weight: 700;
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.5rem;
}

/* ── Stock Remaining ─────────────────────────────────────────── */
.stock-remaining {
    font-size: 0.85rem;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.stock-remaining.stock-ok {
    color: #2E7D32;
    background: rgba(46, 125, 50, 0.1);
}

.stock-remaining.stock-insufficient {
    color: #C62828;
    background: rgba(198, 40, 40, 0.1);
}

/* ── Payment Methods Grid ──────────────────────────────────── */
.payment-methods-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
    gap: 0.75rem;
}

.payment-method-card {
    background: linear-gradient(145deg, #fefcf5, #fff);
    border: 2px solid rgba(218, 165, 32, 0.2);
    border-radius: 12px;
    padding: 1rem 0.75rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    text-align: center;
    color: rgba(93, 64, 55, 0.7);
    font-size: 0.82rem;
    font-weight: 500;
}

.payment-method-card i {
    font-size: 1.4rem;
    color: rgba(139, 69, 19, 0.4);
    transition: all 0.3s ease;
}

.payment-method-card:hover {
    border-color: rgba(139, 69, 19, 0.4);
    transform: translateY(-2px);
    color: rgba(93, 64, 55, 0.9);
    background: linear-gradient(145deg, #fff, #fdf6ec);
}

.payment-method-card:hover i {
    color: #8B4513;
    transform: scale(1.1);
}

.payment-method--selected {
    border-color: #8B4513 !important;
    background: linear-gradient(145deg, #fdf6ec, #fefcf5);
    color: #5d4037;
}

.payment-method--selected i {
    color: #8B4513;
}

/* ── Payment Detail Card ───────────────────────────────────── */
.payment-detail-card {
    background: linear-gradient(135deg, rgba(218, 165, 32, 0.08), rgba(255, 255, 255, 0.9));
    border: 1px solid rgba(218, 165, 32, 0.2);
    border-radius: 14px;
    padding: 1rem;
}

.change-display {
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.1), rgba(255, 255, 255, 0.8));
    border: 2px solid rgba(76, 175, 80, 0.2);
    border-radius: 12px;
    padding: 0.75rem 1rem;
}

.change-display__label {
    font-size: 0.78rem;
    color: rgba(93, 64, 55, 0.6);
    display: flex;
    align-items: center;
    gap: 0.4rem;
    margin-bottom: 0.2rem;
}

.change-display__amount {
    color: #2E7D32;
    font-weight: 700;
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.4rem;
}

/* ── Mixed Payment Card ─────────────────────────────────────── */
.payment-mixed-card {
    background: linear-gradient(135deg, rgba(218, 165, 32, 0.08), rgba(255, 255, 255, 0.9));
    border: 2px solid rgba(218, 165, 32, 0.25);
    border-radius: 16px;
    padding: 1.25rem;
}

.payment-mixed-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid rgba(218, 165, 32, 0.15);
}

.mixed-payment-row {
    background: rgba(255, 255, 255, 0.7);
    border: 1px solid rgba(218, 165, 32, 0.15);
    border-radius: 10px;
    padding: 0.75rem;
    margin-bottom: 0.75rem;
    transition: all 0.2s ease;
}

.mixed-payment-row:hover {
    border-color: rgba(139, 69, 19, 0.3);
}

.mixed-payment-method,
.mixed-payment-amount {
    margin-bottom: 0.5rem;
}

.mixed-payment-actions {
    display: flex;
    justify-content: flex-end;
}

.btn-add-payment {
    background: linear-gradient(135deg, rgba(218, 165, 32, 0.12), rgba(255, 255, 255, 0.8));
    border: 1px dashed rgba(139, 69, 19, 0.3);
    color: #8B4513;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.2s ease;
    cursor: pointer;
    width: 100%;
    text-align: center;
}

.btn-add-payment:hover:not(:disabled) {
    background: linear-gradient(135deg, rgba(218, 165, 32, 0.2), rgba(255, 255, 255, 0.9));
    border-style: solid;
    transform: translateY(-1px);
}

.btn-add-payment:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.mixed-payment-summary {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 10px;
    padding: 0.75rem 1rem;
    margin-top: 0.75rem;
}

/* ── Modal Footer Enhanced ─────────────────────────────────── */
.modal-footer--enhanced {
    background: rgba(255, 255, 255, 0.8);
    border-top: 1px solid rgba(218, 165, 32, 0.2);
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
}

.btn--enhanced {
    padding: 0.6rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.25s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn--enhanced.btn-secondary {
    background: rgba(93, 64, 55, 0.08);
    border: 1px solid rgba(93, 64, 55, 0.15);
    color: rgba(93, 64, 55, 0.8);
}

.btn--enhanced.btn-secondary:hover {
    background: rgba(93, 64, 55, 0.12);
    color: #5d4037;
}

.btn--enhanced.btn-primary {
    background: linear-gradient(135deg, #8B4513, #A0522D);
    border: none;
    color: #fff;
    font-weight: 700;
    box-shadow: 0 4px 15px rgba(139, 69, 19, 0.3);
}

.btn--enhanced.btn-primary:hover:not(:disabled) {
    background: linear-gradient(135deg, #A0522D, #8B4513);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(139, 69, 19, 0.4);
}

.btn--enhanced.btn-primary:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

/* ── Badge Mixto ───────────────────────────────────────────── */
.badge-mixto {
    background: linear-gradient(135deg, #daa520, #b8860b);
    color: #1a1a1a;
    font-weight: 600;
    padding: 0.3rem 0.8rem;
    border-radius: 6px;
}

/* ── Responsive ─────────────────────────────────────────────── */
@media (max-width: 768px) {
    .modal-body--enhanced {
        padding: 1rem;
    }

    .product-grid--enhanced {
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 0.5rem;
    }

    .product-card--enhanced {
        padding: 0.75rem 0.5rem;
    }

    .product-card__icon--enhanced {
        width: 42px;
        height: 42px;
        font-size: 1.15rem;
    }

    .product-card__name--enhanced {
        font-size: 0.78rem;
    }

    .selected-product-card--enhanced {
        flex-wrap: wrap;
    }

    .payment-methods-grid {
        grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
    }

    .payment-method-card {
        padding: 0.75rem 0.5rem;
        font-size: 0.75rem;
    }

    .payment-method-card i {
        font-size: 1.2rem;
    }
}

@media (max-width: 576px) {
    .product-grid--enhanced {
        grid-template-columns: repeat(2, 1fr);
    }

    .modal-dialog-xl {
        max-width: 95%;
        margin: 0.5rem auto;
    }

    .btn--enhanced {
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
    }
}
</style>

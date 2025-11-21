@extends('adminlte::page')

@section('title', 'Nueva Venta')

@section('content_header')
    <h1 class="d-flex align-items-center">
        <i class="fas fa-cash-register fa-lg mr-2"></i>
        Nueva Venta - Punto de Venta
    </h1>
@stop

@section('content')
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Columna de Productos -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-boxes mr-1"></i> Catálogo de Productos Disponibles
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <input type="text" id="searchProduct" class="form-control" placeholder="🔍 Buscar producto por nombre...">
                        </div>
                    </div>

                    <!-- Mosaico de Productos -->
                    <div class="row" id="productosGrid">
                        @foreach($productos as $producto)
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-3 product-card" 
                                 data-name="{{ strtolower($producto->nombre) }}"
                                 data-stock="{{ $producto->stock }}">
                                <div class="card product-card h-100 shadow-sm border-0" 
                                     style="cursor: pointer; transition: transform 0.2s;">
                                    <div class="card-body text-center p-3">
                                        <div class="product-image mb-2">
                                            @if($producto->imagen)
                                                <img src="{{ asset('storage/' . $producto->imagen) }}" 
                                                     alt="{{ $producto->nombre }}" 
                                                     class="img-fluid rounded" 
                                                     style="height: 80px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                     style="height: 80px;">
                                                    <i class="fas fa-box text-muted fa-2x"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <h6 class="product-name font-weight-bold text-dark mb-1" 
                                            style="font-size: 0.9rem; line-height: 1.2;">
                                            {{ $producto->nombre }}
                                        </h6>
                                        <p class="product-price text-success font-weight-bold mb-1">
                                            ${{ number_format($producto->precio, 2) }}
                                        </p>
                                        <small class="text-muted">
                                            Stock: 
                                            <span class="badge {{ $producto->stock > 10 ? 'badge-success' : ($producto->stock > 0 ? 'badge-warning' : 'badge-danger') }}">
                                                {{ $producto->stock }}
                                            </span>
                                        </small>
                                    </div>
                                    <div class="card-footer bg-transparent border-0 pt-0">
                                        @if($producto->stock > 0)
                                            <button class="btn btn-sm btn-outline-primary btn-block" 
                                                    onclick="agregarAlCarrito({{ $producto->id }}, '{{ $producto->nombre }}', {{ $producto->precio }}, {{ $producto->stock }})">
                                                <i class="fas fa-cart-plus mr-1"></i> Agregar
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary btn-block" disabled>
                                                <i class="fas fa-times mr-1"></i> Sin Stock
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($productos->isEmpty())
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-box-open fa-3x mb-3"></i>
                            <p>No hay productos disponibles en stock</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Columna del Carrito y Pago -->
        <div class="col-md-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-shopping-cart mr-1"></i> Carrito de Venta
                        <span class="badge badge-light ml-2" id="contadorCarrito">0</span>
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('ventas.store') }}" method="POST" id="ventaForm">
                        @csrf
                        
                        <!-- Información del Cliente -->
                        <div class="form-group">
                            <label for="cliente_ci" class="font-weight-bold">Cliente <span class="text-danger">*</span></label>
                            <select name="cliente_ci" id="cliente_ci" class="form-control" required>
                                <option value="">Seleccione un cliente</option>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->ci }}">
                                        {{ $cliente->nombres }} {{ $cliente->apellido_paterno }} - CI: {{ $cliente->ci }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Items del Carrito -->
                        <div id="carritoContainer">
                            <div class="text-center text-muted py-3" id="carritoVacio">
                                <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                <p>No hay productos en el carrito</p>
                            </div>
                        </div>

                        <!-- Campos ocultos para los productos -->
                        <div id="productosHidden"></div>

                        <!-- Totales -->
                        <div class="border-top pt-3 mt-3">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="font-weight-bold text-success">Total a Pagar:</span>
                                <span class="font-weight-bold text-success" id="totalVenta">$0.00</span>
                            </div>
                        </div>

                        <!-- Sección de Pagos -->
                        <div class="card mt-3">
                            <div class="card-header bg-warning text-dark py-2">
                                <h6 class="mb-0">
                                    <i class="fas fa-money-bill-wave mr-1"></i> Método de Pago
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <!-- Selección de Tipo de Pago -->
                                <div class="form-group">
                                    <label class="font-weight-bold">Tipo de Pago <span class="text-danger">*</span></label>
                                    <select name="tipo_pago_principal" id="tipo_pago_principal" class="form-control" required>
                                        <option value="">Seleccione tipo de pago</option>
                                        <option value="efectivo">Efectivo</option>
                                        <option value="tarjeta">Tarjeta</option>
                                        <option value="transferencia">Transferencia</option>
                                        <option value="qr">QR</option>
                                        <option value="mixto">Pago Mixto</option>
                                    </select>
                                </div>

                                <!-- Pago Simple (No Mixto) -->
                                <div id="pagoSimpleContainer">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Monto Recibido <span class="text-danger">*</span></label>
                                        <input type="number" name="monto_recibido" id="monto_recibido" 
                                               class="form-control" step="0.01" min="0" 
                                               placeholder="Ingrese monto recibido" value="0">
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Cambio:</span>
                                        <span class="font-weight-bold text-success" id="cambioSimple">$0.00</span>
                                    </div>
                                </div>

                                <!-- Pago Mixto -->
                                <div id="pagoMixtoContainer" style="display: none;">
                                    <div id="pagosMixtosContainer">
                                        <!-- Los métodos de pago mixtos se agregarán dinámicamente -->
                                    </div>
                                    
                                    <button type="button" class="btn btn-success btn-sm btn-block mt-2" id="addPagoMixto">
                                        <i class="fas fa-plus mr-1"></i> Agregar Método
                                    </button>
                                    
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Total Pagado:</span>
                                            <span class="font-weight-bold text-primary" id="totalPagadoMixto">$0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Cambio:</span>
                                            <span class="font-weight-bold" id="cambioMixto">$0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="mt-4">
                            <button type="submit" class="btn btn-success btn-lg btn-block" id="btnSubmit" disabled>
                                <i class="fas fa-credit-card mr-1"></i> Procesar Venta
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm btn-block mt-2" onclick="limpiarCarrito()">
                                <i class="fas fa-trash mr-1"></i> Limpiar Carrito
                            </button>
                            <a href="{{ route('ventas.index') }}" class="btn btn-secondary btn-sm btn-block mt-2">
                                <i class="fas fa-arrow-left mr-1"></i> Volver al Listado
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
        }
        .cart-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        .cart-item:last-child {
            border-bottom: none;
        }
        .sticky-top {
            position: -webkit-sticky;
            position: sticky;
            z-index: 1000;
        }
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .stock-warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 4px;
            padding: 5px;
            font-size: 0.8rem;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            let carrito = [];
            let totalVenta = 0;

            // Búsqueda de productos
            $('#searchProduct').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                $('.product-card').each(function() {
                    const productName = $(this).data('name');
                    if (productName.includes(searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            // Agregar producto al carrito
            window.agregarAlCarrito = function(id, nombre, precio, stock) {
                // Verificar stock
                if (stock <= 0) {
                    alert('Producto sin stock disponible');
                    return;
                }

                // Verificar si el producto ya está en el carrito
                const existingItem = carrito.find(item => item.id === id);
                
                if (existingItem) {
                    if (existingItem.cantidad >= stock) {
                        alert('No hay suficiente stock disponible');
                        return;
                    }
                    existingItem.cantidad++;
                } else {
                    carrito.push({
                        id: id,
                        nombre: nombre,
                        precio: parseFloat(precio),
                        cantidad: 1,
                        stock: stock
                    });
                }
                
                actualizarCarrito();
                $('html, body').animate({
                    scrollTop: $("#carritoContainer").offset().top - 100
                }, 500);
            };

            // Actualizar carrito
            function actualizarCarrito() {
                const container = $('#carritoContainer');
                const contador = $('#contadorCarrito');
                const productosHidden = $('#productosHidden');
                
                // Actualizar contador
                contador.text(carrito.length);
                
                if (carrito.length === 0) {
                    container.html('<div class="text-center text-muted py-3" id="carritoVacio"><i class="fas fa-shopping-cart fa-2x mb-2"></i><p>No hay productos en el carrito</p></div>');
                    productosHidden.empty();
                    return;
                }
                
                let html = '';
                productosHidden.empty();
                
                carrito.forEach((item, index) => {
                    const subtotal = item.precio * item.cantidad;
                    
                    // Agregar campos hidden para el formulario
                    productosHidden.append(`
                        <input type="hidden" name="productos[${index}][id]" value="${item.id}">
                        <input type="hidden" name="productos[${index}][cantidad]" value="${item.cantidad}">
                        <input type="hidden" name="productos[${index}][precio]" value="${item.precio}">
                    `);
                    
                    html += `
                        <div class="cart-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1" style="font-size: 0.9rem;">${item.nombre}</h6>
                                    <small class="text-muted">$${item.precio.toFixed(2)} c/u</small>
                                    ${item.cantidad >= item.stock ? '<div class="stock-warning mt-1"><small>Stock máximo alcanzado</small></div>' : ''}
                                </div>
                                <div class="text-right">
                                    <div class="quantity-controls mb-2">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="modificarCantidad(${index}, -1)" ${item.cantidad <= 1 ? 'disabled' : ''}>
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <span class="mx-2 font-weight-bold">${item.cantidad}</span>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="modificarCantidad(${index}, 1)" ${item.cantidad >= item.stock ? 'disabled' : ''}>
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div>
                                        <strong>$${subtotal.toFixed(2)}</strong>
                                        <button type="button" class="btn btn-outline-danger btn-sm ml-2" onclick="eliminarDelCarrito(${index})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                container.html(html);
                calcularTotales();
            }

            // Modificar cantidad
            window.modificarCantidad = function(index, cambio) {
                const item = carrito[index];
                const nuevaCantidad = item.cantidad + cambio;
                
                if (nuevaCantidad <= 0) {
                    eliminarDelCarrito(index);
                } else if (nuevaCantidad <= item.stock) {
                    item.cantidad = nuevaCantidad;
                    actualizarCarrito();
                } else {
                    alert('No hay suficiente stock disponible');
                }
            };

            // Eliminar del carrito
            window.eliminarDelCarrito = function(index) {
                carrito.splice(index, 1);
                actualizarCarrito();
            };

            // Limpiar carrito
            window.limpiarCarrito = function() {
                if (confirm('¿Está seguro de limpiar el carrito?')) {
                    carrito = [];
                    actualizarCarrito();
                    $('#pagosMixtosContainer').empty();
                    calcularTotales();
                }
            };

            // Calcular totales
            function calcularTotales() {
                totalVenta = carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
                
                $('#totalVenta').text('$' + totalVenta.toFixed(2));
                
                // Actualizar cálculo de cambio automáticamente
                if ($('#tipo_pago_principal').val() !== 'mixto') {
                    calcularCambioSimple();
                } else {
                    calcularTotalPagadoMixto();
                }
                
                validarPagos();
            }

            // Cambiar entre pago simple y mixto
            $('#tipo_pago_principal').change(function() {
                const tipoPago = $(this).val();
                
                if (tipoPago === 'mixto') {
                    $('#pagoSimpleContainer').hide();
                    $('#pagoMixtoContainer').show();
                    // Agregar primer método de pago mixto
                    if ($('#pagosMixtosContainer').children().length === 0) {
                        agregarMetodoPagoMixto();
                    }
                } else {
                    $('#pagoSimpleContainer').show();
                    $('#pagoMixtoContainer').hide();
                    $('#pagosMixtosContainer').empty();
                    // Auto-completar monto recibido con el total
                    $('#monto_recibido').val(totalVenta.toFixed(2));
                    calcularCambioSimple();
                }
                
                validarPagos();
            });

            // Calcular cambio para pago simple - VERSIÓN CORREGIDA
            function calcularCambioSimple() {
                const montoRecibido = parseFloat($('#monto_recibido').val()) || 0;
                const cambio = montoRecibido - totalVenta;
                
                // CORRECCIÓN: Asegurar que el cambio nunca sea negativo
                const cambioMostrar = Math.max(0, cambio);
                
                $('#cambioSimple').text('$' + cambioMostrar.toFixed(2));
                
                if (cambio >= 0) {
                    $('#cambioSimple').removeClass('text-danger').addClass('text-success');
                } else {
                    $('#cambioSimple').removeClass('text-success').addClass('text-danger');
                    // CORRECCIÓN ADICIONAL: Mostrar advertencia visual cuando el monto es insuficiente
                    $('#cambioSimple').text('$0.00 - Insuficiente');
                }
                
                validarPagos();
            }

            // Agregar método de pago mixto
            $('#addPagoMixto').click(function() {
                agregarMetodoPagoMixto();
            });

            function agregarMetodoPagoMixto() {
                const index = $('#pagosMixtosContainer').children().length;
                const newPago = `
                    <div class="row pagos-mixtos-row mb-2">
                        <div class="col-5">
                            <select name="tipo_pago[]" class="form-control form-control-sm tipo-pago-mixto" required>
                                <option value="">Tipo</option>
                                <option value="efectivo">Efectivo</option>
                                <option value="tarjeta">Tarjeta</option>
                                <option value="transferencia">Transferencia</option>
                                <option value="qr">QR</option>
                            </select>
                        </div>
                        <div class="col-5">
                            <input type="number" name="monto_pago[]" class="form-control form-control-sm monto-pago-mixto" 
                                   step="0.01" min="0" placeholder="Monto" required value="0">
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-danger btn-sm remove-pago-mixto" ${index === 0 ? 'disabled' : ''}>
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                `;
                $('#pagosMixtosContainer').append(newPago);
                calcularTotalPagadoMixto();
            }

            // Remover método de pago mixto
            $(document).on('click', '.remove-pago-mixto', function() {
                if ($('.pagos-mixtos-row').length > 1) {
                    $(this).closest('.pagos-mixtos-row').remove();
                    calcularTotalPagadoMixto();
                }
            });

            // Calcular total pagado mixto - VERSIÓN CORREGIDA
            function calcularTotalPagadoMixto() {
                let totalPagado = 0;
                $('.monto-pago-mixto').each(function() {
                    totalPagado += parseFloat($(this).val()) || 0;
                });
                
                $('#totalPagadoMixto').text('$' + totalPagado.toFixed(2));
                
                const cambio = totalPagado - totalVenta;
                const cambioMostrar = Math.max(0, cambio); // CORRECCIÓN: Evitar valores negativos
                
                $('#cambioMixto').text('$' + cambioMostrar.toFixed(2));
                
                if (cambio >= 0) {
                    $('#cambioMixto').removeClass('text-danger').addClass('text-success');
                } else {
                    $('#cambioMixto').removeClass('text-success').addClass('text-danger');
                    // CORRECCIÓN ADICIONAL: Mostrar advertencia visual
                    $('#cambioMixto').text('$0.00 - Insuficiente');
                }
                
                validarPagos();
            }

            // Validar pagos
            function validarPagos() {
                const btnSubmit = $('#btnSubmit');
                const tipoPago = $('#tipo_pago_principal').val();
                let condicionesCumplidas = false;
                
                if (carrito.length > 0 && $('#cliente_ci').val() && tipoPago) {
                    if (tipoPago === 'mixto') {
                        const totalPagado = parseFloat($('#totalPagadoMixto').text().replace('$', '')) || 0;
                        condicionesCumplidas = totalPagado >= totalVenta && $('.tipo-pago-mixto').length > 0;
                    } else if (tipoPago !== 'mixto') {
                        const montoRecibido = parseFloat($('#monto_recibido').val()) || 0;
                        condicionesCumplidas = montoRecibido >= totalVenta;
                    }
                }
                
                btnSubmit.prop('disabled', !condicionesCumplidas);
            }

            // Debug del formulario antes de enviar
            $('#ventaForm').on('submit', function(e) {
                console.log('=== DEBUG FORMULARIO VENTA ===');
                
                // Para pago simple: crear campos tipo_pago y monto_pago
                const tipoPago = $('#tipo_pago_principal').val();
                
                if (tipoPago !== 'mixto') {
                    // Limpiar campos mixtos si existen
                    $('[name="tipo_pago[]"]').remove();
                    $('[name="monto_pago[]"]').remove();
                    
                    // Crear campos para pago simple
                    const montoRecibido = parseFloat($('#monto_recibido').val()) || 0;
                    $(this).append(`<input type="hidden" name="tipo_pago[]" value="${tipoPago}">`);
                    $(this).append(`<input type="hidden" name="monto_pago[]" value="${montoRecibido}">`);
                }
                
                // Mostrar datos que se enviarán
                const formData = new FormData(this);
                console.log('Datos del formulario:');
                for (let [key, value] of formData.entries()) {
                    console.log(key + ': ' + value);
                }
                
                console.log('Total Venta:', totalVenta);
                console.log('=== FORMULARIO VÁLIDO - ENVIANDO ===');
            });

            // Event listeners
            $(document).on('input', '#monto_recibido', calcularCambioSimple);
            $(document).on('input', '.monto-pago-mixto', calcularTotalPagadoMixto);
            $('#cliente_ci').change(validarPagos);
            
            // Inicializar - Auto-completar monto recibido cuando hay productos
            $(document).on('input', function() {
                if ($('#tipo_pago_principal').val() !== 'mixto' && totalVenta > 0) {
                    $('#monto_recibido').val(totalVenta.toFixed(2));
                    calcularCambioSimple();
                }
            });
            
            actualizarCarrito();
        });
    </script>
@stop


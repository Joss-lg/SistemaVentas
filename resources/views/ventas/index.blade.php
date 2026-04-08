@extends('layouts.cajero')

@section('title', 'Punto de Venta')

@section('content')
<div class="grid grid-cols-12 gap-6 h-[calc(100vh-160px)]">
    <div class="col-span-8 bg-[#0d0d0d] border border-white/5 rounded-2xl shadow-2xl flex flex-col overflow-hidden text-white">
        <div class="p-6 border-b border-white/5 bg-white/[0.02] flex justify-between items-center">
            <h3 class="text-gray-500 font-black uppercase text-xs tracking-[0.3em]">Lista de Compra</h3>
            <span class="bg-red-600/10 text-red-500 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border border-red-600/20">
                F1 Activo
            </span>
        </div>

        <div class="flex-1 overflow-y-auto custom-scrollbar">
            <table class="w-full text-left">
                <thead class="sticky top-0 bg-[#0d0d0d] z-10">
                    <tr class="text-gray-600 uppercase font-black border-b border-white/5 text-[10px] tracking-[0.2em]">
                        <th class="p-6">Cant.</th>
                        <th class="p-6">Descripción</th>
                        <th class="p-6 text-center">Precio</th>
                        <th class="p-6 text-right">Subtotal</th>
                        <th class="p-6 text-right w-20"></th> 
                    </tr>
                </thead>
                <tbody id="lista-productos" class="divide-y divide-white/5 font-bold italic"></tbody>
            </table>
        </div>
    </div>

    <div class="col-span-4 flex flex-col gap-6">
        <div class="bg-[#0d0d0d] border border-red-600/30 p-6 rounded-2xl shadow-2xl">
            <label class="block text-red-600 text-[10px] font-black mb-3 uppercase tracking-[0.4em]">Escáner de Código</label>
            <input type="text" id="scanner" autofocus
                class="w-full bg-black border-b-2 border-red-600 text-red-500 text-5xl p-4 focus:outline-none font-black placeholder-zinc-900 transition-all focus:bg-red-600/5"
                placeholder="|||||||||||||">
        </div>

        <div class="bg-red-600 p-8 rounded-2xl text-white shadow-[0_20px_50px_rgba(220,38,38,0.25)] relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 text-white/10 text-9xl font-black italic rotate-12 group-hover:rotate-0 transition-transform">$</div>
            <p class="text-xs font-black uppercase opacity-60 italic tracking-widest relative z-10">Total a Cobrar</p>
            <div class="flex items-baseline gap-2 mt-2 relative z-10">
                <span class="text-4xl font-bold opacity-80">$</span>
                <span id="total-venta" class="text-8xl font-black italic tracking-tighter tabular-nums text-white">0.00</span>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-3">
            <button onclick="cobrar()" class="w-full bg-green-600 hover:bg-green-500 text-white p-6 rounded-xl font-black text-2xl transition active:scale-95 uppercase italic shadow-lg flex justify-between items-center px-10">
                <span>COBRAR</span>
                <span class="opacity-50">[F9]</span>
            </button>
            
            <button onclick="abrirModalRecuperar()" class="w-full bg-orange-600 hover:bg-orange-500 text-white p-4 rounded-xl font-black text-lg transition active:scale-95 uppercase italic shadow-lg flex justify-between items-center px-10 border border-orange-400/20">
                <span>RECUPERAR VENTA</span>
                <span class="opacity-50">[F2]</span>
            </button>

            <div class="grid grid-cols-2 gap-3">
                <button onclick="abrirModalBusqueda()" class="bg-white/5 hover:bg-white/10 text-gray-400 p-4 rounded-xl font-black uppercase transition border border-white/5 tracking-widest">[F10] BUSCAR</button>
                <button onclick="pausarVenta()" class="bg-orange-600/10 hover:bg-orange-600/20 text-orange-500 p-4 rounded-xl font-black uppercase transition border border-orange-600/20 tracking-widest">
                    [F4] ESPERA
                </button>
            </div>
            
            <button onclick="abrirModalProveedor()" class="w-full bg-blue-900/20 hover:bg-blue-900/40 text-blue-400 p-2 rounded-xl font-black text-[20px] transition uppercase tracking-[0.2em] border border-blue-500/20">
                [F8] ENTRADA PROVEEDOR
            </button>
        </div>
    </div>
</div>

<div id="modal-proveedor" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-[#0d0d0d] border border-white/10 p-8 rounded-3xl w-full max-w-lg shadow-2xl relative">
        <h2 class="text-2xl font-black italic uppercase text-white mb-6">Entrada de <span class="text-red-600">Stock</span></h2>
        
        <div class="space-y-4">
            <div class="relative">
                <label class="text-xs font-bold text-gray-400 uppercase italic">1. Buscar Producto</label>
                <input type="text" id="busqueda-prod-prov" oninput="buscarProductoNombreProveedor(this.value)" placeholder="Escribe nombre del producto..." 
                    class="w-full bg-white/5 border border-white/10 p-3 rounded-xl text-white text-sm focus:border-blue-500 outline-none" autocomplete="off">
                <div id="sugerencias-prov" class="absolute left-0 right-0 top-full z-[100] hidden bg-[#1a1a1a] border border-white/10 rounded-xl mt-1 shadow-2xl max-h-60 overflow-y-auto"></div>
            </div>

            <div id="info-producto-prov" class="hidden bg-blue-500/10 border border-blue-500/30 p-4 rounded-2xl">
                <p class="text-blue-400 text-xs font-bold uppercase italic">Seleccionado:</p>
                <p id="prov-nombre-display" class="text-white font-black uppercase text-sm"></p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase italic">Proveedor</label>
                    <input type="text" id="prov-nombre" placeholder="Ej. COCA COLA" class="w-full bg-white/5 border border-white/10 p-3 rounded-xl text-white uppercase text-sm">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase italic">Costo Total ($)</label>
                    <input type="number" id="prov-costo-total" step="0.01" placeholder="0.00" class="w-full bg-white/5 border border-white/10 p-3 rounded-xl text-white text-sm">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase italic">Cantidad</label>
                    <input type="number" id="prov-cantidad" step="0.001" placeholder="1" class="w-full bg-white/5 border border-white/10 p-3 rounded-xl text-white text-sm">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase italic">Método</label>
                    <select id="prov-metodo" class="w-full bg-white/5 border border-white/10 p-3 rounded-xl text-white text-sm">
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button onclick="cerrarModalProveedor()" class="flex-1 p-4 rounded-xl font-bold uppercase text-xs text-gray-400 hover:bg-white/5 transition">Cancelar</button>
                <button onclick="guardarEntradaStock()" class="flex-1 p-4 bg-green-600 hover:bg-green-500 text-white rounded-xl font-bold uppercase text-xs shadow-lg transition">Registrar e Imprimir</button>
            </div>
        </div>
    </div>
</div>

<div id="modal-cobro" class="hidden fixed inset-0 bg-black/95 backdrop-blur-sm flex items-center justify-center z-[110] p-4">
    <div class="bg-[#0d0d0d] border border-white/10 w-full max-w-lg rounded-3xl overflow-hidden shadow-2xl p-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <p class="text-gray-500 text-[10px] font-black uppercase tracking-widest">Artículos: <span id="resumen-articulos" class="text-white">0</span></p>
                <h2 class="text-4xl font-black text-white italic">Total:</h2>
            </div>
            <div class="text-right">
                <span class="text-5xl font-black text-green-500 italic">$<span id="resumen-total">0.00</span></span>
            </div>
        </div>
        <div class="bg-white/5 border border-white/5 rounded-2xl p-4 mb-6 flex justify-between items-center">
            <span class="text-gray-400 font-black uppercase text-xs tracking-widest">Su Cambio:</span>
            <span id="display-cambio" class="text-3xl font-black text-yellow-500 italic">$0.00</span>
        </div>
        <p class="text-[10px] font-black text-gray-600 uppercase mb-3 tracking-widest">Método de Pago</p>
        <div class="grid grid-cols-3 gap-3 mb-6">
            <button onclick="setMetodo('efectivo')" id="btn-efectivo" class="border-2 border-green-500 bg-green-500/10 p-4 rounded-2xl flex flex-col items-center transition">
                <span class="text-green-500 font-black uppercase italic text-xs">Efectivo</span>
            </button>
            <button onclick="setMetodo('tarjeta')" id="btn-tarjeta" class="border-2 border-white/5 bg-white/5 p-4 rounded-2xl flex flex-col items-center transition text-white">
                <span class="text-gray-400 font-black uppercase italic text-xs">Tarjeta</span>
            </button>
            <button onclick="setMetodo('transferencia')" id="btn-transferencia" class="border-2 border-white/5 bg-white/5 p-4 rounded-2xl flex flex-col items-center transition text-white">
                <span class="text-gray-400 font-black uppercase italic text-xs">Transf.</span>
            </button>
        </div>
        <div class="mb-6">
            <div id="container-monto">
                <p class="text-[10px] font-black text-gray-600 uppercase mb-3 tracking-widest">Monto Recibido</p>
                <input type="number" id="monto-recibido" oninput="calcularCambio()" class="w-full bg-black border-2 border-white/5 rounded-2xl p-5 text-4xl font-black text-white focus:border-green-500 outline-none transition text-center italic" placeholder="0.00">
            </div>
            <div id="container-folio" class="hidden">
                <p class="text-[10px] font-black text-blue-500 uppercase mb-3 tracking-widest">Folio de Operación</p>
                <input type="text" id="folio-pago" class="w-full bg-black border-2 border-blue-500/30 rounded-2xl p-5 text-3xl font-black text-white focus:border-blue-500 outline-none transition text-center italic uppercase" placeholder="EJ. 123456">
            </div>
        </div>
        <div id="atajos-dinero" class="grid grid-cols-4 gap-2 mb-8 text-xs">
            <button onclick="sumarMonto(50)" class="bg-white/5 p-3 rounded-xl font-black text-gray-400 hover:text-white">$50</button>
            <button onclick="sumarMonto(100)" class="bg-white/5 p-3 rounded-xl font-black text-gray-400 hover:text-white">$100</button>
            <button onclick="sumarMonto(200)" class="bg-white/5 p-3 rounded-xl font-black text-gray-400 hover:text-white">$200</button>
            <button onclick="sumarMonto(500)" class="bg-white/5 p-3 rounded-xl font-black text-gray-400 hover:text-white">$500</button>
        </div>
        <div class="flex gap-3">
            <button onclick="cerrarModalCobro()" class="flex-1 bg-white/5 text-gray-500 p-5 rounded-2xl font-black uppercase hover:bg-white/10 transition">Cancelar</button>
            <button onclick="finalizarProcesoCobro()" class="flex-[2] bg-green-600 text-white p-5 rounded-2xl font-black uppercase shadow-lg hover:bg-green-700 transition italic">Completar Venta</button>
        </div>
    </div>
</div>

<div id="modalVentasEspera" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/80 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-[#0d0d0d] border border-white/10 w-full max-w-2xl rounded-2xl shadow-2xl p-6">
            <h3 class="text-xl font-black italic text-white uppercase mb-4">Ventas en <span class="text-red-600">Espera</span></h3>
            <div class="overflow-hidden rounded-xl border border-white/5">
                <table class="w-full text-left">
                    <tbody id="listaVentasEspera" class="divide-y divide-white/5 text-white"></tbody>
                </table>
            </div>
            <button onclick="cerrarModalRecuperar()" class="mt-4 w-full bg-white/5 p-3 text-gray-500 font-black uppercase">Cerrar</button>
        </div>
    </div>
</div>

<div id="modal-busqueda" class="hidden fixed inset-0 bg-black/95 backdrop-blur-md flex items-center justify-center z-[100] p-4">
    <div class="bg-[#0d0d0d] border border-red-600/50 w-full max-w-4xl rounded-2xl p-8 shadow-2xl">
        <div class="flex justify-between items-center mb-8">
            <h3 class="text-red-600 font-black text-3xl uppercase italic">Buscador <span class="text-white">Express</span></h3>
            <button onclick="cerrarModalBusqueda()" class="text-gray-600 font-black uppercase text-xs">CERRAR [ESC]</button>
        </div>
        <input type="text" id="input-busqueda-nombre" class="w-full bg-black border border-white/10 text-white text-4xl p-6 rounded-xl focus:border-red-600 outline-none uppercase font-black italic mb-6" placeholder="ESCRIBE NOMBRE...">
        <div class="max-h-[400px] overflow-y-auto custom-scrollbar">
            <table class="w-full text-left">
                <tbody id="resultados-busqueda" class="divide-y divide-white/5 text-white"></tbody>
            </table>
        </div>
    </div>
</div>

<div id="modal-peso" class="hidden fixed inset-0 bg-black/95 backdrop-blur-md flex items-center justify-center z-[120] p-4">
    <div class="bg-[#111] border border-white/10 w-full max-w-md rounded-3xl p-8 text-center">
        <h2 id="peso-producto-nombre" class="text-xl font-black text-white uppercase italic mb-6">Producto</h2>
        <input type="number" id="input-peso-valor" step="0.001" class="w-full bg-black border-2 border-white/5 rounded-2xl p-6 text-6xl font-black text-green-500 text-center italic mb-6" placeholder="0.000">
        <div class="flex gap-3">
            <button onclick="confirmarPeso()" class="flex-[2] bg-red-600 text-white p-5 rounded-2xl font-black uppercase italic">Confirmar</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let carrito = [];
let totalVenta = 0;
let metodoSeleccionado = 'efectivo';
let productoPendientePeso = null;
let productoProvSeleccionado = null; // Guardará el producto de entrada de stock

// ==========================================
// 1. ESCÁNER Y BÚSQUEDA
// ==========================================
document.getElementById('scanner').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        let codigo = this.value; if (!codigo) return;
        fetch(`{{ url('/ventas/buscar-producto') }}?codigo=${codigo}`)
            .then(res => res.json())
            .then(p => { agregarAlCarrito(p); this.value = ""; })
            .catch(() => { alert("No encontrado"); this.value = ""; });
    }
});

document.getElementById('input-busqueda-nombre').addEventListener('input', function() {
    let q = this.value; if (q.length < 2) return;
    fetch(`{{ url('/ventas/buscar-nombre') }}?q=${q}`)
        .then(res => res.json())
        .then(productos => {
            let html = "";
            productos.forEach(p => {
                const pData = btoa(JSON.stringify(p));
                html += `<tr class="border-b border-white/5"><td class="p-4 uppercase italic font-black">${p.descripcion}</td>
                <td class="p-4 text-center text-green-500 font-black">$${parseFloat(p.precio_venta).toFixed(2)}</td>
                <td class="p-4 text-right"><button onclick="seleccionarProductoVenta('${pData}')" class="bg-red-600 text-white px-4 py-2 rounded-lg text-xs font-black italic">Seleccionar</button></td></tr>`;
            });
            document.getElementById('resultados-busqueda').innerHTML = html;
        });
});

function seleccionarProductoVenta(data) {
    agregarAlCarrito(JSON.parse(atob(data)));
    cerrarModalBusqueda();
}

// ==========================================
// 2. LÓGICA DE PROVEEDORES (CORREGIDA PARA GRANEL Y DESPLAZABLE)
// ==========================================
function buscarProductoNombreProveedor(query) {
    const contenedor = document.getElementById('sugerencias-prov');
    if (query.length < 2) { contenedor.classList.add('hidden'); return; }
    
    fetch(`{{ url('/ventas/buscar-nombre') }}?q=${query}`)
        .then(res => res.json())
        .then(productos => {
            let html = "";
            productos.forEach(p => {
                const pData = btoa(JSON.stringify(p));
                html += `<div onclick="seleccionarProductoProv('${pData}')" class="p-4 border-b border-white/5 hover:bg-blue-600/20 cursor-pointer transition-all">
                    <p class="text-white font-black text-xs uppercase italic">${p.descripcion}</p>
                </div>`;
            });
            contenedor.innerHTML = html;
            contenedor.classList.remove('hidden');
        });
}

function seleccionarProductoProv(dataBase64) {
    const p = JSON.parse(atob(dataBase64));
    productoProvSeleccionado = p;
    
    document.getElementById('prov-nombre-display').innerText = p.descripcion;
    document.getElementById('info-producto-prov').classList.remove('hidden');
    document.getElementById('sugerencias-prov').classList.add('hidden');
    document.getElementById('busqueda-prod-prov').value = "";
    document.getElementById('prov-cantidad').focus();
}

function guardarEntradaStock() {
    const cant = parseFloat(document.getElementById('prov-cantidad').value);
    const proveedor = document.getElementById('prov-nombre').value.trim();
    const costo = parseFloat(document.getElementById('prov-costo-total').value) || 0;
    const metodo = document.getElementById('prov-metodo').value;

    if (!productoProvSeleccionado || isNaN(cant) || cant <= 0 || !proveedor) {
        alert("RELLENA LOS DATOS CORRECTAMENTE");
        return;
    }

    // Primero guardamos en la BD
    fetch("{{ route('inventario.agregar-stock') }}", {
        method: "POST",
        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
        body: JSON.stringify({
            producto_id: productoProvSeleccionado.id,
            cantidad: cant,
            costo_total: costo,
            proveedor: proveedor,
            metodo_pago: metodo
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
        // 1. Abrimos una ventana pequeña para mandar el pulso al cajón
        // Esto cargará la vista 'abrir_cajon' que ya tienes en tu carpeta 'impresion'
        const win = window.open("{{ route('impresion.abrir-cajon') }}", '_blank', 'width=100,height=100');
        
        // Cerramos la ventanita del cajón automáticamente tras medio segundo
        if(win) setTimeout(() => win.close(), 500);

        alert("¡MERCANCÍA REGISTRADA Y CAJÓN ABIERTO!");
        location.reload(); 
    } else {
        alert("Error: " + data.mensaje);
    }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Error de conexión al servidor");
    });
}

// ==========================================
// 3. FUNCIONES DE CARRITO Y TABLA
// ==========================================
function agregarAlCarrito(producto) {
    if (producto.unidad_medida === 'kg' || producto.unidad_medida === 'granel') {
        productoPendientePeso = producto;
        document.getElementById('peso-producto-nombre').innerText = producto.descripcion;
        document.getElementById('modal-peso').classList.remove('hidden');
        setTimeout(() => document.getElementById('input-peso-valor').focus(), 200);
    } else { ejecutarAgregado(producto, 1); }
}

function confirmarPeso() {
    let peso = parseFloat(document.getElementById('input-peso-valor').value);
    if (peso > 0) { ejecutarAgregado(productoPendientePeso, peso); cerrarModalPeso(); }
}

function ejecutarAgregado(p, cant) {
    let item = carrito.find(i => i.id === p.id);
    let precio = parseFloat(p.precio_venta);
    if (item && p.unidad_medida !== 'kg') {
        item.cantidad += cant; item.subtotal = item.cantidad * item.precio;
    } else {
        carrito.push({ id: p.id, descripcion: p.descripcion, precio: precio, cantidad: cant, subtotal: precio * cant });
    }
    renderizarTabla();
}

function renderizarTabla() {
    let html = ""; totalVenta = 0;
    carrito.forEach((item, index) => {
        totalVenta += item.subtotal;
        html += `<tr class="border-b border-white/5"><td class="p-6 font-black">${item.cantidad}</td>
        <td class="p-6 uppercase text-gray-400 italic">${item.descripcion}</td>
        <td class="p-6 text-center text-green-500 font-black">$${item.precio.toFixed(2)}</td>
        <td class="p-6 text-right text-red-600 font-black">$${item.subtotal.toFixed(2)}</td>
        <td class="p-6 text-right"><button onclick="eliminarItem(${index})" class="text-gray-700 hover:text-white">&times;</button></td></tr>`;
    });
    document.getElementById('lista-productos').innerHTML = html;
    document.getElementById('total-venta').innerText = totalVenta.toFixed(2);
}

function eliminarItem(i) { carrito.splice(i, 1); renderizarTabla(); }

// ==========================================
// 4. LÓGICA DE COBRO
// ==========================================
// ==========================================
// Incluicion de cobro offline
//===========================================
function cobrar() {
    // 1. Validaciones iniciales
    if (carrito.length === 0) return alert("Carrito vacío");

    // 2. Preparar los datos que se van a guardar (IMPORTANTE)
    // Usamos los nombres de columnas que vimos en tus tablas de MySQL
    const datosVenta = {
        productos: carrito, // Array con los productos (id, cantidad, precio)
        total: totalVenta,
        fecha: new Date().toISOString().split('T')[0], // YYYY-MM-DD
        hora: new Date().toLocaleTimeString('it-IT'), // Formato 24h
        metodo_pago: metodoSeleccionado || 'efectivo', // Captura el método elegido
        vendedor: 'Caja 1' // Puedes hacerlo dinámico después
    };

    // 3. Mostrar resumen en el modal (Interfaz)
    document.getElementById('resumen-articulos').innerText = carrito.length;
    document.getElementById('resumen-total').innerText = totalVenta.toFixed(2);
    document.getElementById('modal-cobro').classList.remove('hidden');
    setMetodo('efectivo');

    // 4. Lógica de Conexión (Offline vs Online)
    if (!navigator.onLine) {
        // --- MODO OFFLINE ---
        // Recuperamos lo que ya esté en la "cola", si no hay nada, empezamos array vacío
        let ventasPendientes = JSON.parse(localStorage.getItem('cola_ventas_abarrotes')) || [];
        
        // Metemos la nueva venta al paquete
        ventasPendientes.push(datosVenta);
        
        // Guardamos de vuelta en la memoria de la PC
        localStorage.setItem('cola_ventas_abarrotes', JSON.stringify(ventasPendientes));

        alert("⚠️ SIN INTERNET: Venta guardada en la memoria local de Abarrotes. Se sincronizará sola al volver la red.");
        
        finalizarProcesoVenta(); // Función para limpiar pantalla y cerrar modal
        return;
    }

    // --- MODO ONLINE ---
    // Si hay internet, se va directo a tu base de datos de Laravel
    enviarVentaAlServidor(datosVenta);
}


function setMetodo(t) {
    metodoSeleccionado = t;
    const btnE = document.getElementById('btn-efectivo');
    const btnT = document.getElementById('btn-tarjeta');
    const btnTr = document.getElementById('btn-transferencia');
    const montoInput = document.getElementById('monto-recibido');

    [btnE, btnT, btnTr].forEach(b => {
        if (b) b.className = "border-2 border-white/5 bg-white/5 p-4 rounded-2xl flex flex-col items-center transition text-white opacity-50";
    });

    const btnSel = document.getElementById(`btn-${t}`);
    if (btnSel) {
        btnSel.classList.remove('opacity-50', 'border-white/5', 'bg-white/5');
        btnSel.classList.add(t === 'efectivo' ? 'border-green-500' : 'border-blue-500', 'bg-white/10');
    }

    if (t === 'efectivo') {
        document.getElementById('container-monto').classList.remove('hidden');
        document.getElementById('container-folio').classList.add('hidden');
        document.getElementById('atajos-dinero').classList.remove('hidden');
        setTimeout(() => montoInput.focus(), 200);
    } else {
        document.getElementById('container-monto').classList.add('hidden');
        document.getElementById('container-folio').classList.remove('hidden');
        document.getElementById('atajos-dinero').classList.add('hidden');
        document.getElementById('folio-pago').focus();
    }
}

function calcularCambio() {
    let rec = parseFloat(document.getElementById('monto-recibido').value) || 0;
    let cambio = rec - totalVenta;
    document.getElementById('display-cambio').innerText = "$" + (cambio > 0 ? cambio.toFixed(2) : "0.00");
}

function sumarMonto(v) {
    let inp = document.getElementById('monto-recibido');
    inp.value = ((parseFloat(inp.value) || 0) + v).toFixed(2);
    calcularCambio();
}

function finalizarProcesoCobro() {
    let montoRecibido = 0;
    let folio = document.getElementById('folio-pago').value;

    // 1. Validaciones de dinero (Se quedan igual)
    if (metodoSeleccionado === 'efectivo') {
        montoRecibido = parseFloat(document.getElementById('monto-recibido').value);
        if (isNaN(montoRecibido) || montoRecibido < totalVenta) return alert("Monto insuficiente");
    } else {
        montoRecibido = totalVenta;
        if (!folio) return alert("INGRESE EL FOLIO");
    }

    // 2. Preparamos el paquete de datos (El objeto que va a la DB o al LocalStorage)
    const payloadVenta = { 
        total: totalVenta, 
        metodo_pago: metodoSeleccionado, 
        referencia_pago: folio,
        monto_recibido: montoRecibido, 
        cambio: montoRecibido - totalVenta, 
        productos: carrito,
        fecha_local: new Date().toLocaleString() // Para saber a qué hora fue la venta offline
    };

    // 3. LA DECISIÓN CRÍTICA: ¿Hay internet?
    if (!navigator.onLine) {
        // --- MODO OFFLINE ---
        let cola = JSON.parse(localStorage.getItem('cola_ventas_abarrotes')) || [];
        cola.push(payloadVenta);
        localStorage.setItem('cola_ventas_abarrotes', JSON.stringify(cola));

        // LA MAGIA: Imprimimos el ticket de emergencia
        imprimirTicketOffline(payloadVenta);

        alert("⚠️ VENTA GUARDADA Y TICKET GENERADO LOCALMENTE.");
        ejecutarLimpiezaPostVenta();
        return;
    }

    // --- MODO ONLINE (Tu código original con fetch) ---
    fetch("{{ route('ventas.finalizar') }}", {
        method: "POST",
        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
        body: JSON.stringify(payloadVenta)
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') { 
            alert("VENTA COMPLETADA"); 
            window.open(`/ventas/ticket/${data.venta_id}`, "Ticket", "width=400,height=600");
            ejecutarLimpiezaPostVenta();
        }
    })
    .catch(err => {
        // Por si el internet falla justo en el momento del clic
        console.error("Error de red, guardando offline...", err);
        let cola = JSON.parse(localStorage.getItem('cola_ventas_abarrotes')) || [];
        cola.push(payloadVenta);
        localStorage.setItem('cola_ventas_abarrotes', JSON.stringify(cola));
        ejecutarLimpiezaPostVenta();
    });
}

// Creamos esta funcioncita para no repetir código de limpieza
function ejecutarLimpiezaPostVenta() {
    carrito = []; 
    renderizarTabla(); 
    cerrarModalCobro();
    const scanner = document.getElementById('scanner');
    if(scanner) scanner.focus();
}

// ==========================================
// 5. PAUSA Y RECUPERACIÓN
// ==========================================
async function pausarVenta() {
    if (carrito.length === 0) return alert("No hay productos");
    const res = await fetch("{{ route('ventas.pausar') }}", {
        method: "POST",
        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
        body: JSON.stringify({ productos: carrito })
    });
    const data = await res.json();
    if (data.status === 'success') { alert("EN ESPERA"); carrito = []; renderizarTabla(); }
}

function abrirModalRecuperar() {
    document.getElementById('modalVentasEspera').classList.remove('hidden');
    fetch('/admin/ventas-espera/listar').then(res => res.json()).then(data => {
        const tbody = document.getElementById('listaVentasEspera');
        tbody.innerHTML = '';
        data.forEach(v => {
            tbody.innerHTML += `<tr><td class="p-4">${new Date(v.fecha_pausa).toLocaleString()}</td>
            <td class="p-4 text-right"><button onclick="recuperar(${v.id})" class="bg-blue-600 px-4 py-2 rounded">Seleccionar</button></td></tr>`;
        });
    });
}

async function recuperar(id) {
    const res = await fetch(`/admin/ventas-espera/recuperar/${id}`);
    const data = await res.json();
    if (data.status === 'success') { carrito = data.carrito; renderizarTabla(); cerrarModalRecuperar(); }
}

// ==========================================
// 6. CONTROL MODALES Y TECLAS
// ==========================================
function abrirModalProveedor() { 
    document.getElementById('modal-proveedor').classList.remove('hidden'); 
    setTimeout(() => document.getElementById('busqueda-prod-prov').focus(), 200); 
}
function cerrarModalProveedor() { 
    document.getElementById('modal-proveedor').classList.add('hidden'); 
    productoProvSeleccionado = null;
    document.getElementById('info-producto-prov').classList.add('hidden');
}
function abrirModalBusqueda() { document.getElementById('modal-busqueda').classList.remove('hidden'); document.getElementById('input-busqueda-nombre').focus(); }
function cerrarModalBusqueda() { document.getElementById('modal-busqueda').classList.add('hidden'); }
function cerrarModalPeso() { document.getElementById('modal-peso').classList.add('hidden'); }
function cerrarModalCobro() { document.getElementById('modal-cobro').classList.add('hidden'); }
function cerrarModalRecuperar() { document.getElementById('modalVentasEspera').classList.add('hidden'); }

function imprimirTicketOffline(datos) {
    // Abrimos una ventana fantasma
    const ventana = window.open('', 'PRINT', 'height=600,width=400');
    
    // Obtenemos la fecha y hora actual para el ticket
    const ahora = new Date();
    const fechaFormateada = ahora.toLocaleDateString() + ' ' + ahora.toLocaleTimeString();

    ventana.document.write(`
        <html>
            <head>
                <style>
                    @page { margin: 0; }
                    body {
                        font-family: 'Courier New', Courier, monospace;
                        font-size: 12px;
                        width: 58mm;
                        margin: 0; padding: 0;
                    }
                    .ticket { width: 48mm; padding: 2mm; margin: 0 auto; }
                    .centered { text-align: center; }
                    .bold { font-weight: bold; }
                    .uppercase { text-transform: uppercase; }
                    .separator { border-top: 1px dashed black; margin: 5px 0; }
                    table { width: 100%; border-collapse: collapse; }
                    th { text-align: left; border-bottom: 1px solid black; font-size: 10px; }
                    .total-row { font-size: 14px; font-weight: bold; text-align: right; }
                </style>
            </head>
            <body onload="window.print(); window.close();">
                <div class="ticket">
                    <div class="centered">
                        <h1 style="font-size: 16px; margin:0;">ABARROTES</h1>
                        <p style="font-size: 9px;">VENTA LOCAL (SIN RED)</p>
                    </div>
                    <div class="separator"></div>
                    <p><strong>FOLIO:</strong> PENDIENTE</p>
                    <p><strong>FECHA:</strong> ${fechaFormateada}</p>
                    <div class="separator"></div>
                    <table>
                        <thead>
                            <tr>
                                <th>CANT</th>
                                <th>DESC</th>
                                <th style="text-align:right;">SUB</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${datos.productos.map(p => `
                                <tr>
                                    <td>${Math.floor(p.cantidad)}</td>
                                    <td class="uppercase">${(p.descripcion || 'PRODUCTO').substring(0, 15)}</td>
                                    <td style="text-align:right;">$${(p.cantidad * p.precio).toFixed(2)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                    <div class="separator"></div>
                    <div class="total-row">TOTAL: $${datos.total.toFixed(2)}</div>
                    <div class="centered" style="margin-top:10px;">
                        <p class="bold">¡GRACIAS POR SU COMPRA!</p>
                        <p>Venta guardada en memoria local</p>
                    </div>
                </div>
            </body>
        </html>
    `);

    ventana.document.close();
}

window.addEventListener('keydown', e => {
    if (e.key === 'F2') { e.preventDefault(); abrirModalRecuperar(); }
    if (e.key === 'F4') { e.preventDefault(); pausarVenta(); }
    if (e.key === 'F8') { e.preventDefault(); abrirModalProveedor(); }
    if (e.key === 'F9') { e.preventDefault(); cobrar(); }
    if (e.key === 'F10') { e.preventDefault(); abrirModalBusqueda(); }
    if (e.key === 'Escape') { 
        cerrarModalBusqueda(); cerrarModalCobro(); cerrarModalPeso(); 
        cerrarModalProveedor(); cerrarModalRecuperar(); 
        document.getElementById('scanner').focus();
    }
});
</script>
@endpush
@extends('layouts.cajero')

@section('title', 'Punto de Venta')

@section('content')
{{-- Estilo para el Modo Híbrido y x-cloak --}}
<style>
    [x-cloak] { display: none !important; }
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #dc2626; border-radius: 10px; }
</style>

<div class="grid grid-cols-12 gap-6 h-[calc(100vh-160px)]">
    {{-- LISTA DE COMPRA --}}
    <div class="col-span-8 bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/5 rounded-2xl shadow-2xl flex flex-col overflow-hidden text-zinc-900 dark:text-white transition-all">
        <div class="p-6 border-b border-zinc-100 dark:border-white/5 bg-zinc-50 dark:bg-white/[0.02] flex justify-between items-center">
            <h3 class="text-zinc-500 dark:text-gray-500 font-black uppercase text-xs tracking-[0.3em]">Lista de Compra</h3>
            <span class="bg-red-600/10 text-red-500 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border border-red-600/20">
                F1 Activo
            </span>
        </div>

        <div class="flex-1 overflow-y-auto custom-scrollbar">
            <table class="w-full text-left">
                <thead class="sticky top-0 bg-white dark:bg-[#0d0d0d] z-10">
                    <tr class="text-zinc-400 dark:text-gray-600 uppercase font-black border-b border-zinc-100 dark:border-white/5 text-[10px] tracking-[0.2em]">
                        <th class="p-6">Cant.</th>
                        <th class="p-6">Descripción</th>
                        <th class="p-6 text-center">Precio</th>
                        <th class="p-6 text-right">Subtotal</th>
                        <th class="p-6 text-right w-20"></th> 
                    </tr>
                </thead>
                <tbody id="lista-productos" class="divide-y divide-zinc-100 dark:divide-white/5 font-bold italic">
                    {{-- Dinámico con JS --}}
                </tbody>
            </table>
        </div>
    </div>

    {{-- LATERAL DERECHO --}}
    <div class="col-span-4 flex flex-col gap-6">
        {{-- SCANNER --}}
        <div class="bg-white dark:bg-[#0d0d0d] border border-red-600/30 p-6 rounded-2xl shadow-2xl">
            <label class="block text-red-600 text-[10px] font-black mb-3 uppercase tracking-[0.4em]">Escáner de Código</label>
            <input type="text" id="scanner" autofocus
                class="w-full bg-zinc-100 dark:bg-black border-b-2 border-red-600 text-red-600 dark:text-red-500 text-5xl p-4 focus:outline-none font-black placeholder-zinc-300 dark:placeholder-zinc-900 transition-all focus:bg-red-600/5"
                placeholder="|||||||||||||">
        </div>

        {{-- TOTAL --}}
        <div class="bg-red-600 p-8 rounded-2xl text-white shadow-[0_20px_50px_rgba(220,38,38,0.25)] relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 text-white/10 text-9xl font-black italic rotate-12 group-hover:rotate-0 transition-transform">$</div>
            <p class="text-xs font-black uppercase opacity-60 italic tracking-widest relative z-10">Total a Cobrar</p>
            <div class="flex items-baseline gap-2 mt-2 relative z-10">
                <span class="text-4xl font-bold opacity-80">$</span>
                <span id="total-venta" class="text-8xl font-black italic tracking-tighter tabular-nums text-white">0.00</span>
            </div>
        </div>

        {{-- ACCIONES --}}
        <div class="grid grid-cols-1 gap-3">
            <button onclick="cobrar()" class="w-full bg-green-600 hover:bg-green-500 text-white p-6 rounded-xl font-black text-2xl transition active:scale-95 uppercase italic shadow-lg flex justify-between items-center px-10">
                <span>COBRAR</span>
                <span class="opacity-50 text-sm">[F9]</span>
            </button>
            
            <button onclick="abrirModalRecuperar()" class="w-full bg-orange-600 hover:bg-orange-500 text-white p-4 rounded-xl font-black text-lg transition active:scale-95 uppercase italic shadow-lg flex justify-between items-center px-10 border border-orange-400/20">
                <span>RECUPERAR VENTA</span>
                <span class="opacity-50 text-sm">[F2]</span>
            </button>

            <div class="grid grid-cols-2 gap-3">
                <button onclick="abrirModalBusqueda()" class="bg-zinc-100 dark:bg-white/5 hover:bg-zinc-200 dark:hover:bg-white/10 text-zinc-500 dark:text-gray-400 p-4 rounded-xl font-black uppercase transition border border-zinc-200 dark:border-white/5 tracking-widest text-xs">[F10] BUSCAR</button>
                <button onclick="pausarVenta()" class="bg-orange-600/10 hover:bg-orange-600/20 text-orange-600 dark:text-orange-500 p-4 rounded-xl font-black uppercase transition border border-orange-600/20 tracking-widest text-xs">
                    [F4] ESPERA
                </button>
            </div>
            
            <button onclick="abrirModalProveedor()" class="w-full bg-blue-600/10 dark:bg-blue-900/20 hover:bg-blue-600/20 dark:hover:bg-blue-900/40 text-blue-600 dark:text-blue-400 p-3 rounded-xl font-black text-lg transition uppercase tracking-[0.2em] border border-blue-500/20">
                [F8] ENTRADA PROVEEDOR
            </button>
        </div>
    </div>
</div>

{{-- MODAL PROVEEDOR --}}
<div id="modal-proveedor" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-[#1a1a1a] w-full max-w-6xl rounded-[2.5rem] overflow-hidden shadow-2xl border border-gray-100 dark:border-white/5 transition-colors duration-300">
        
        <div class="p-8 flex justify-between items-center border-b border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/5">
            <h2 class="text-4xl font-black italic uppercase tracking-tighter text-zinc-800 dark:text-white">
                Entrada de <span class="text-red-600">Mercancía</span>
            </h2>
            <button onclick="cerrarModalProveedor()" class="text-gray-400 hover:text-red-600 transition-all p-2">
                <i class="fas fa-times text-4xl"></i>
            </button>
        </div>

        <div class="flex flex-col md:flex-row gap-10 p-10">
            <div class="w-full md:w-1/3 space-y-8">
                <div>
                    <label class="block text-xs font-black uppercase text-zinc-400 mb-2 italic tracking-widest">Proveedor / Factura</label>
                    <input type="text" id="prov-nombre" 
                        class="w-full bg-blue-50 dark:bg-blue-900/20 border-0 rounded-2xl p-6 font-black uppercase italic text-zinc-700 dark:text-blue-200 text-2xl focus:ring-4 focus:ring-blue-500/20 shadow-sm" 
                        placeholder="EJ. BIMBO">
                </div>

                <div class="relative">
                    <label class="block text-xs font-black uppercase text-zinc-400 mb-2 italic tracking-widest">Buscar Producto</label>
                    <input type="text" id="busqueda-prod-prov" oninput="buscarProductoNombreProveedor(this.value)" 
                        class="w-full bg-zinc-100 dark:bg-white/5 border-0 rounded-2xl p-6 font-black uppercase italic text-zinc-700 dark:text-white text-2xl focus:ring-4 focus:ring-red-500/20 shadow-sm" 
                        placeholder="ESCRIBE NOMBRE...">
                    
                    <div id="sugerencias-prov" class="absolute z-20 w-full bg-white dark:bg-[#2a2a2a] shadow-2xl rounded-2xl mt-3 hidden max-h-72 overflow-y-auto p-2 border border-gray-100 dark:border-white/10"></div>
                </div>

                <div id="info-producto-prov" class="hidden bg-white dark:bg-white/5 border-2 border-red-500 rounded-[2.5rem] p-8 shadow-2xl space-y-6">
                    <p id="prov-nombre-display" class="font-black uppercase italic text-red-600 dark:text-red-500 text-xl border-b border-gray-100 dark:border-white/10 pb-4"></p>
                    
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-zinc-400 uppercase mb-2">Cantidad</label>
                            <input type="number" id="prov-cantidad" value="1" step="0.001" 
                                class="w-full bg-zinc-50 dark:bg-white/5 border-0 rounded-2xl p-6 font-black text-center text-3xl text-zinc-800 dark:text-white focus:ring-0">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-zinc-400 uppercase mb-2">Costo Unit.</label>
                            <input type="number" id="prov-costo-total" step="0.01" 
                                class="w-full bg-zinc-50 dark:bg-white/5 border-0 rounded-2xl p-6 font-black text-center text-3xl text-green-600 dark:text-green-400 focus:ring-0" 
                                placeholder="0.00">
                        </div>
                    </div>

                    <button onclick="agregarAListaTemporalProv()" 
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-black italic py-6 rounded-2xl transition-all shadow-lg shadow-red-200 dark:shadow-none active:scale-95 uppercase text-lg">
                        + AGREGAR A LA LISTA
                    </button>
                </div>
            </div>

            <div class="w-full md:w-2/3 bg-white dark:bg-white/5 rounded-[2.5rem] border border-gray-100 dark:border-white/5 overflow-hidden flex flex-col shadow-inner">
                <div class="bg-zinc-900 p-6 flex justify-between items-center">
                    <span class="text-xs font-black uppercase text-zinc-400 italic tracking-widest">Lista de Carga</span>
                    <span id="contador-items-prov" class="bg-red-600 text-white text-[10px] px-5 py-2 rounded-full font-black uppercase italic">0 PRODUCTOS</span>
                </div>
                
                <div class="flex-1 overflow-y-auto min-h-[400px] max-h-[500px]">
                    <table class="w-full text-left">
                        <thead class="sticky top-0 bg-zinc-50 dark:bg-[#252525] text-[10px] font-black uppercase text-zinc-400 border-b border-gray-100 dark:border-white/5">
                            <tr>
                                <th class="p-6 italic">CANT</th>
                                <th class="p-6 italic">DESCRIPCIÓN</th>
                                <th class="p-6 text-right italic">COSTO U.</th>
                                <th class="p-6 text-right italic">SUBTOTAL</th>
                                <th class="p-6 w-20"></th>
                            </tr>
                        </thead>
                        <tbody id="lista-items-prov" class="text-2xl font-black uppercase italic text-zinc-800 dark:text-zinc-200">
                            </tbody>
                    </table>
                </div>

                <div class="p-10 bg-zinc-50 dark:bg-black/20 border-t border-gray-100 dark:border-white/5">
                    <button onclick="guardarEntradaStock()" 
                        class="w-full bg-[#10b981] hover:bg-[#059669] text-white font-black italic py-8 rounded-[1.8rem] transition-all shadow-xl shadow-emerald-200 dark:shadow-none uppercase tracking-widest active:scale-95 text-2xl">
                        GUARDAR EN INVENTARIO
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- MODAL COBRO --}}
<div id="modal-cobro" class="hidden fixed inset-0 bg-zinc-900/90 dark:bg-black/95 backdrop-blur-sm flex items-center justify-center z-[110] p-4">
    <div class="bg-white dark:bg-[#0d0d0d] border border-zinc-200 dark:border-white/10 w-full max-w-lg rounded-3xl overflow-hidden shadow-2xl p-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <p class="text-zinc-400 dark:text-gray-500 text-[10px] font-black uppercase tracking-widest">Artículos: <span id="resumen-articulos" class="text-zinc-900 dark:text-white">0</span></p>
                <h2 class="text-4xl font-black text-zinc-900 dark:text-white italic">TOTAL:</h2>
            </div>
            <div class="text-right">
                <span class="text-5xl font-black text-green-600 dark:text-green-500 italic">$<span id="resumen-total">0.00</span></span>
            </div>
        </div>
        <div class="bg-zinc-50 dark:bg-white/5 border border-zinc-100 dark:border-white/5 rounded-2xl p-4 mb-6 flex justify-between items-center transition-all">
            <span class="text-zinc-400 dark:text-gray-400 font-black uppercase text-xs tracking-widest">Su Cambio:</span>
            <span id="display-cambio" class="text-3xl font-black text-yellow-600 dark:text-yellow-500 italic">$0.00</span>
        </div>
        
        <p class="text-[10px] font-black text-zinc-400 dark:text-gray-600 uppercase mb-3 tracking-widest">Método de Pago</p>
        <div class="grid grid-cols-3 gap-3 mb-6">
            <button onclick="setMetodo('efectivo')" id="btn-efectivo" class="border-2 border-green-500 bg-green-500/10 p-4 rounded-2xl flex flex-col items-center transition">
                <span class="text-green-600 dark:text-green-500 font-black uppercase italic text-[10px]">Efectivo</span>
            </button>
            <button onclick="setMetodo('tarjeta')" id="btn-tarjeta" class="border-2 border-zinc-100 dark:border-white/5 bg-zinc-50 dark:bg-white/5 p-4 rounded-2xl flex flex-col items-center transition text-zinc-500 dark:text-white">
                <span class="text-zinc-400 dark:text-gray-400 font-black uppercase italic text-[10px]">Tarjeta</span>
            </button>
            <button onclick="setMetodo('transferencia')" id="btn-transferencia" class="border-2 border-zinc-100 dark:border-white/5 bg-zinc-50 dark:bg-white/5 p-4 rounded-2xl flex flex-col items-center transition text-zinc-500 dark:text-white">
                <span class="text-zinc-400 dark:text-gray-400 font-black uppercase italic text-[10px]">Transf.</span>
            </button>
        </div>

        <div class="mb-6">
            <div id="container-monto">
                <p class="text-[10px] font-black text-zinc-400 dark:text-gray-600 uppercase mb-3 tracking-widest">Monto Recibido</p>
                <input type="number" id="monto-recibido" oninput="calcularCambio()" class="w-full bg-zinc-100 dark:bg-black border-2 border-zinc-100 dark:border-white/5 rounded-2xl p-5 text-4xl font-black text-zinc-900 dark:text-white focus:border-green-500 outline-none transition text-center italic" placeholder="0.00">
            </div>
            <div id="container-folio" class="hidden">
                <p class="text-[10px] font-black text-blue-600 dark:text-blue-500 uppercase mb-3 tracking-widest">Folio de Operación</p>
                <input type="text" id="folio-pago" class="w-full bg-zinc-100 dark:bg-black border-2 border-blue-500/30 rounded-2xl p-5 text-3xl font-black text-zinc-900 dark:text-white focus:border-blue-500 outline-none transition text-center italic uppercase" placeholder="EJ. 123456">
            </div>
        </div>

        <div id="atajos-dinero" class="grid grid-cols-4 gap-2 mb-8 text-xs font-black">
            <button onclick="sumarMonto(50)" class="bg-zinc-100 dark:bg-white/5 p-3 rounded-xl text-zinc-500 dark:text-gray-400 hover:text-red-600 transition">$50</button>
            <button onclick="sumarMonto(100)" class="bg-zinc-100 dark:bg-white/5 p-3 rounded-xl text-zinc-500 dark:text-gray-400 hover:text-red-600 transition">$100</button>
            <button onclick="sumarMonto(200)" class="bg-zinc-100 dark:bg-white/5 p-3 rounded-xl text-zinc-500 dark:text-gray-400 hover:text-red-600 transition">$200</button>
            <button onclick="sumarMonto(500)" class="bg-zinc-100 dark:bg-white/5 p-3 rounded-xl text-zinc-500 dark:text-gray-400 hover:text-red-600 transition">$500</button>
        </div>

        <div class="flex gap-3">
            <button onclick="cerrarModalCobro()" class="flex-1 bg-zinc-100 dark:bg-white/5 text-zinc-500 dark:text-gray-500 p-5 rounded-2xl font-black uppercase hover:bg-zinc-200 dark:hover:bg-white/10 transition">Cancelar</button>
            <button onclick="finalizarProcesoCobro()" class="flex-[2] bg-green-600 text-white p-5 rounded-2xl font-black uppercase shadow-lg hover:bg-green-700 transition italic">FINALIZAR VENTA</button>
        </div>
    </div>
</div>

{{-- MODAL ESPERA --}}
<div id="modalVentasEspera" class="fixed inset-0 z-50 hidden bg-zinc-900/80 backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-[#0d0d0d] w-full max-w-2xl rounded-[2.5rem] shadow-2xl p-8 border border-zinc-200 dark:border-white/10">
            
            <h3 class="text-3xl font-black italic text-zinc-900 dark:text-white uppercase mb-6 tracking-tighter">
                Ventas en <span class="text-red-600">Espera</span>
            </h3>

            <div class="bg-zinc-50 dark:bg-white/5 rounded-3xl p-2 border border-zinc-100 dark:border-white/5 max-h-[400px] overflow-y-auto custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <tbody id="listaVentasEspera" class="divide-y divide-zinc-200 dark:divide-white/5">
                        </tbody>
                </table>
            </div>

            <button onclick="cerrarModalRecuperar()" class="mt-8 w-full bg-zinc-100 dark:bg-white/10 p-6 text-zinc-500 dark:text-zinc-400 font-black uppercase rounded-2xl hover:bg-red-600 hover:text-white transition-all text-xl italic tracking-widest">
                Cerrar Ventana
            </button>
        </div>
    </div>
</div>
{{-- MODAL BÚSQUEDA --}}
<div id="modal-busqueda" class="hidden fixed inset-0 bg-zinc-100/95 dark:bg-black/95 backdrop-blur-md flex items-center justify-center z-[100] p-4">
    <div class="bg-white dark:bg-[#0d0d0d] border-2 border-red-600/50 w-full max-w-4xl rounded-2xl p-8 shadow-2xl">
        <div class="flex justify-between items-center mb-8">
            <h3 class="text-red-600 font-black text-3xl uppercase italic">Buscador <span class="text-zinc-900 dark:text-white">Express</span></h3>
            <button onclick="cerrarModalBusqueda()" class="text-zinc-400 dark:text-gray-600 font-black uppercase text-xs hover:text-red-600">CERRAR [ESC]</button>
        </div>
        <input type="text" id="input-busqueda-nombre" class="w-full bg-zinc-100 dark:bg-black border border-zinc-200 dark:border-white/10 text-zinc-900 dark:text-white text-4xl p-6 rounded-xl focus:border-red-600 outline-none uppercase font-black italic mb-6" placeholder="ESCRIBE NOMBRE...">
        <div class="max-h-[400px] overflow-y-auto custom-scrollbar">
            <table class="w-full text-left">
                <tbody id="resultados-busqueda" class="divide-y divide-zinc-100 dark:divide-white/5 text-zinc-900 dark:text-white"></tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL PESO --}}
<div id="modal-peso" class="hidden fixed inset-0 bg-zinc-900/95 dark:bg-black/95 backdrop-blur-md flex items-center justify-center z-[120] p-4">
    <div class="bg-white dark:bg-[#111] border border-zinc-200 dark:border-white/10 w-full max-w-md rounded-3xl p-8 text-center shadow-2xl">
        <h2 id="peso-producto-nombre" class="text-xl font-black text-zinc-900 dark:text-white uppercase italic mb-6">Producto</h2>
        <input type="number" id="input-peso-valor" step="0.001" class="w-full bg-zinc-100 dark:bg-black border-2 border-zinc-100 dark:border-white/5 rounded-2xl p-6 text-6xl font-black text-green-600 dark:text-green-500 text-center italic mb-6 outline-none focus:border-red-600" placeholder="0.000">
        <div class="flex gap-3">
            <button onclick="confirmarPeso()" class="flex-[2] bg-red-600 text-white p-5 rounded-2xl font-black uppercase italic shadow-lg hover:bg-red-700 transition">Confirmar</button>
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
let productoProvSeleccionado = null; 
let listaTemporalProveedor = []; // <--- Lista para entradas de mercancía

// Notificaciones
const notify = (icon, title) => {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true,
        background: '#1a1a1a',
        color: '#fff'
    });
    Toast.fire({ icon, title });
};

// ==========================================
// RESPALDO LOCAL Y SINCRONIZACIÓN (OFFLINE)
// ==========================================
async function actualizarRespaldoProductos() {
    if (!navigator.onLine) return;
    try {
        const res = await fetch("{{ url('/ventas/buscar-nombre') }}?q="); 
        const productos = await res.json();
        localStorage.setItem('respaldo_productos', JSON.stringify(productos));
    } catch (e) { console.log("Esperando conexión..."); }
}

function buscarLocal(query) {
    const productos = JSON.parse(localStorage.getItem('respaldo_productos')) || [];
    return productos.filter(p => 
        p.descripcion.toLowerCase().includes(query.toLowerCase()) || 
        p.codigo_barras == query
    ).slice(0, 10);
}

async function sincronizarVentasPendientes() {
    let ventas = JSON.parse(localStorage.getItem('cola_ventas_abarrotes')) || [];
    if (ventas.length === 0) return;
    for (let i = 0; i < ventas.length; i++) {
        try {
            const res = await fetch("{{ route('ventas.finalizar') }}", {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                body: JSON.stringify(ventas[i])
            });
            if (res.ok) { ventas.splice(i, 1); i--; }
        } catch (e) { break; }
    }
    localStorage.setItem('cola_ventas_abarrotes', JSON.stringify(ventas));
    if (ventas.length === 0) { notify('success', 'VENTAS SINCRONIZADAS'); }
}

window.addEventListener('online', () => { sincronizarVentasPendientes(); actualizarRespaldoProductos(); });
document.addEventListener('DOMContentLoaded', () => { sincronizarVentasPendientes(); actualizarRespaldoProductos(); });

// ==========================================
// 1. ESCÁNER Y BÚSQUEDA DE VENTAS
// ==========================================
document.getElementById('scanner').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        let codigo = this.value; if (!codigo) return;
        if (!navigator.onLine) {
            const p = buscarLocal(codigo)[0];
            if (p) { agregarAlCarrito(p); this.value = ""; } 
            else { notify('error', 'No encontrado (Offline)'); this.value = ""; }
        } else {
            fetch(`{{ url('/ventas/buscar-producto') }}?codigo=${codigo}`)
                .then(res => res.json())
                .then(p => { agregarAlCarrito(p); this.value = ""; })
                .catch(() => { 
                    const p = buscarLocal(codigo)[0];
                    if (p) agregarAlCarrito(p);
                    else notify('error', 'Producto no encontrado'); 
                    this.value = ""; 
                });
        }
    }
});

document.getElementById('input-busqueda-nombre').addEventListener('input', function() {
    let q = this.value; if (q.length < 2) return;
    const render = (productos) => {
        let html = "";
        productos.forEach(p => {
            const pData = btoa(JSON.stringify(p));
            html += `<tr class="border-b border-white/5"><td class="p-4 uppercase italic font-black">${p.descripcion}</td>
            <td class="p-4 text-center text-green-500 font-black">$${parseFloat(p.precio_venta).toFixed(2)}</td>
            <td class="p-4 text-right"><button onclick="seleccionarProductoVenta('${pData}')" class="bg-red-600 text-white px-4 py-2 rounded-lg text-xs font-black italic">Seleccionar</button></td></tr>`;
        });
        document.getElementById('resultados-busqueda').innerHTML = html;
    };
    if (!navigator.onLine) render(buscarLocal(q));
    else fetch(`{{ url('/ventas/buscar-nombre') }}?q=${q}`).then(res => res.json()).then(productos => render(productos)).catch(() => render(buscarLocal(q)));
});

function seleccionarProductoVenta(data) {
    agregarAlCarrito(JSON.parse(atob(data)));
    cerrarModalBusqueda();
}

// ==========================================
// 2. LÓGICA DE PROVEEDORES (NUEVA MULTIPRODUCTO)
// ==========================================
function buscarProductoNombreProveedor(query) {
    const contenedor = document.getElementById('sugerencias-prov');
    if (query.length < 2) { 
        contenedor.classList.add('hidden'); 
        return; 
    }

    const renderProv = (productos) => {
        let html = "";
        productos.forEach(p => {
            const pData = btoa(JSON.stringify(p));
            // Ajuste de colores: text-zinc-900 en claro, dark:text-white en oscuro
            html += `
                <div onclick="seleccionarProductoProv('${pData}')" 
                    class="p-4 border-b border-zinc-100 dark:border-white/5 hover:bg-red-600/10 dark:hover:bg-red-600/20 cursor-pointer transition-all">
                    <p class="text-zinc-900 dark:text-white font-black text-xs uppercase italic">${p.descripcion}</p>
                </div>`;
        });
        contenedor.innerHTML = html; 
        contenedor.classList.remove('hidden');
    };

    if (!navigator.onLine) {
        renderProv(buscarLocal(query));
    } else {
        fetch(`{{ url('/ventas/buscar-nombre') }}?q=${query}`)
            .then(res => res.json())
            .then(productos => renderProv(productos))
            .catch(() => renderProv(buscarLocal(query)));
    }
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

function agregarAListaTemporalProv() {
    const cant = parseFloat(document.getElementById('prov-cantidad').value);
    const costo = parseFloat(document.getElementById('prov-costo-total').value) || 0;
    
    if (!productoProvSeleccionado || isNaN(cant) || cant <= 0) {
        if (typeof notify === 'function') notify('warning', 'DATOS INVÁLIDOS'); 
        return;
    }

    listaTemporalProveedor.push({
        id: productoProvSeleccionado.id,
        descripcion: productoProvSeleccionado.descripcion,
        cantidad: cant,
        costo_unitario: costo,
        subtotal: cant * costo,
        unidad: productoProvSeleccionado.unidad_medida
    });

    renderListaProv();
    productoProvSeleccionado = null;
    document.getElementById('info-producto-prov').classList.add('hidden');
    document.getElementById('busqueda-prod-prov').focus();
}

function renderListaProv() {
    const tbody = document.getElementById('lista-items-prov');
    if (!tbody) return;

    tbody.innerHTML = "";
    listaTemporalProveedor.forEach((item, index) => {
        // Ajuste de colores en las filas de la tabla para visibilidad total
        tbody.innerHTML += `
            <tr class="border-b border-zinc-100 dark:border-white/5">
                <td class="p-4 font-black text-zinc-900 dark:text-white">${item.cantidad}</td>
                <td class="p-4 uppercase italic font-bold text-zinc-700 dark:text-zinc-300">${item.descripcion}</td>
                <td class="p-4 text-right text-zinc-900 dark:text-white">$${item.costo_unitario.toFixed(2)}</td>
                <td class="p-4 text-right text-green-600 dark:text-green-400 font-black">$${item.subtotal.toFixed(2)}</td>
                <td class="p-4 text-center">
                    <button onclick="eliminarItemProv(${index})" 
                        class="text-zinc-400 hover:text-red-600 transition-colors text-xl">&times;</button>
                </td>
            </tr>`;
    });
    
    const contador = document.getElementById('contador-items-prov');
    if (contador) contador.innerText = `${listaTemporalProveedor.length} ARTÍCULOS`;
}

function eliminarItemProv(i) {
    listaTemporalProveedor.splice(i, 1);
    renderListaProv();
}

function guardarEntradaStock() {
    const proveedor = document.getElementById('prov-nombre').value.trim();
    if (listaTemporalProveedor.length === 0 || !proveedor) {
        if (typeof notify === 'function') notify('warning', 'PROVEEDOR O LISTA VACÍA'); 
        return;
    }

    const payload = { proveedor: proveedor, productos: listaTemporalProveedor };

    if (!navigator.onLine) {
        let cola = JSON.parse(localStorage.getItem('cola_stock_offline')) || [];
        cola.push(payload);
        localStorage.setItem('cola_stock_offline', JSON.stringify(cola));
        if (typeof notify === 'function') notify('warning', 'STOCK GUARDADO OFFLINE');
        limpiarYSalirProv();
        return;
    }

    fetch("{{ route('inventario.agregar-stock') }}", {
        method: "POST",
        headers: { 
            "Content-Type": "application/json", 
            "X-CSRF-TOKEN": "{{ csrf_token() }}" 
        },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            if (typeof notify === 'function') notify('success', 'MERCANCÍA REGISTRADA');
            limpiarYSalirProv();
            setTimeout(() => location.reload(), 1500);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function limpiarYSalirProv() {
    listaTemporalProveedor = [];
    const inputProv = document.getElementById('prov-nombre');
    if (inputProv) inputProv.value = "";
    renderListaProv();
    if (typeof cerrarModalProveedor === 'function') cerrarModalProveedor();
}

// ==========================================
// 3. CARRITO Y TABLA DE VENTAS
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
function cobrar() {
    if (carrito.length === 0) { notify('info', 'Carrito vacío'); return; }
    document.getElementById('resumen-articulos').innerText = carrito.length;
    document.getElementById('resumen-total').innerText = totalVenta.toFixed(2);
    document.getElementById('modal-cobro').classList.remove('hidden');
    setMetodo('efectivo');
}

function setMetodo(t) {
    metodoSeleccionado = t;
    const btnE = document.getElementById('btn-efectivo'), btnT = document.getElementById('btn-tarjeta'), btnTr = document.getElementById('btn-transferencia');
    [btnE, btnT, btnTr].forEach(b => { if (b) b.className = "border-2 border-white/5 bg-white/5 p-4 rounded-2xl flex flex-col items-center transition text-white opacity-50"; });
    const btnSel = document.getElementById(`btn-${t}`);
    if (btnSel) {
        btnSel.classList.remove('opacity-50', 'border-white/5', 'bg-white/5');
        btnSel.classList.add(t === 'efectivo' ? 'border-green-500' : 'border-blue-500', 'bg-white/10');
    }
    if (t === 'efectivo') {
        document.getElementById('container-monto').classList.remove('hidden');
        document.getElementById('container-folio').classList.add('hidden');
        document.getElementById('atajos-dinero').classList.remove('hidden');
        setTimeout(() => document.getElementById('monto-recibido').focus(), 200);
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
    let montoRecibido = 0, folio = document.getElementById('folio-pago').value;
    if (metodoSeleccionado === 'efectivo') {
        montoRecibido = parseFloat(document.getElementById('monto-recibido').value);
        if (isNaN(montoRecibido) || montoRecibido < totalVenta) { notify('error', 'Monto insuficiente'); return; }
    } else {
        montoRecibido = totalVenta;
        if (!folio) { notify('warning', 'INGRESE EL FOLIO'); return; }
    }

    const payloadVenta = { total: totalVenta, metodo_pago: metodoSeleccionado, referencia_pago: folio, monto_recibido: montoRecibido, cambio: montoRecibido - totalVenta, productos: carrito, fecha_local: new Date().toLocaleString() };

    if (!navigator.onLine) {
        let cola = JSON.parse(localStorage.getItem('cola_ventas_abarrotes')) || [];
        cola.push(payloadVenta);
        localStorage.setItem('cola_ventas_abarrotes', JSON.stringify(cola));
        abrirCajonManual(); notify('warning', 'VENTA GUARDADA OFFLINE');
        ejecutarLimpiezaPostVenta(); return;
    }

    fetch("{{ route('ventas.finalizar') }}", {
        method: "POST",
        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
        body: JSON.stringify(payloadVenta)
    }).then(res => res.json()).then(data => {
        if(data.status === 'success') {
            window.open(`/ventas/ticket/${data.venta_id}`, "Ticket", "width=400,height=600");
            ejecutarLimpiezaPostVenta();
        }
    }).catch(() => {
        let cola = JSON.parse(localStorage.getItem('cola_ventas_abarrotes')) || [];
        cola.push(payloadVenta); localStorage.setItem('cola_ventas_abarrotes', JSON.stringify(cola));
        abrirCajonManual(); ejecutarLimpiezaPostVenta();
    });
}

function ejecutarLimpiezaPostVenta() {
    carrito = []; renderizarTabla(); cerrarModalCobro();
    document.getElementById('scanner').focus();
}

// ==========================================
// 5. PAUSA Y RECUPERACIÓN (F2 y F4)
// ==========================================
async function pausarVenta() {
    if (carrito.length === 0) return;
    let pausadas = JSON.parse(localStorage.getItem('ventas_pausadas_local')) || [];
    pausadas.push({ id: Date.now(), fecha: new Date(), productos: carrito });
    localStorage.setItem('ventas_pausadas_local', JSON.stringify(pausadas));
    notify('success', 'EN ESPERA (LOCAL)');
    carrito = []; renderizarTabla();
}

function abrirModalRecuperar() {
    const modal = document.getElementById('modalVentasEspera');
    const tbody = document.getElementById('listaVentasEspera');
    
    if (!modal || !tbody) return;

    modal.classList.remove('hidden');
    tbody.innerHTML = '';
    
    const pausadas = JSON.parse(localStorage.getItem('ventas_pausadas_local')) || [];
    
    if (pausadas.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="2" class="p-10 text-center text-zinc-400 font-bold uppercase tracking-widest text-xs">
                    No hay ventas en espera en este equipo.
                </td>
            </tr>`;
        return;
    }

    pausadas.forEach(v => {
        tbody.innerHTML += `
            <tr class="border-b border-zinc-100 dark:border-white/5 hover:bg-zinc-50 dark:hover:bg-white/5 transition-colors">
                <td class="p-5">
                    <div class="flex flex-col">
                        <span class="text-zinc-900 dark:text-white font-black italic uppercase text-lg leading-tight">
                            ${new Date(v.fecha).toLocaleString()}
                        </span>
                        <span class="text-zinc-400 font-bold text-[10px] uppercase tracking-widest mt-1">
                            ID LOCAL: #${v.id}
                        </span>
                    </div>
                </td>
                <td class="p-5 text-right">
                    <button onclick="recuperarLocal(${v.id})" 
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-black uppercase text-xs italic transition-all active:scale-95 shadow-lg shadow-green-900/20">
                        Seleccionar
                    </button>
                </td>
            </tr>`;
    });
}

/**
 * Recupera una venta específica y la carga en el carrito.
 * @param {number} id - ID de la venta a recuperar.
 */
function recuperarLocal(id) {
    let pausadas = JSON.parse(localStorage.getItem('ventas_pausadas_local')) || [];
    const venta = pausadas.find(v => v.id === id);
    
    if (venta) {
        // Cargar productos al carrito global
        carrito = venta.productos; 
        
        // Actualizar la interfaz de la tabla de ventas
        if (typeof renderizarTabla === 'function') {
            renderizarTabla();
        }
        
        // Actualizar localStorage eliminando la venta recuperada
        const nuevasPausadas = pausadas.filter(v => v.id !== id);
        localStorage.setItem('ventas_pausadas_local', JSON.stringify(nuevasPausadas));
        
        // Notificación con SweetAlert2
        Swal.fire({
            icon: 'success',
            title: 'Venta Recuperada',
            text: 'Los productos se han cargado al carrito correctamente.',
            timer: 2000,
            showConfirmButton: false,
            background: document.documentElement.classList.contains('dark') ? '#0d0d0d' : '#fff',
            color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
        });
        
        cerrarModalRecuperar();
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo localizar la venta seleccionada.',
            background: document.documentElement.classList.contains('dark') ? '#0d0d0d' : '#fff',
            color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
        });
    }
}

// ==========================================
// 6. CONTROL MODALES Y CAJÓN
// ==========================================
function abrirCajonManual() {
    const win = window.open("{{ route('impresion.abrir-cajon') }}", '_blank', 'width=1,height=1');
    if(win) setTimeout(() => win.close(), 500);
}

function abrirModalProveedor() { document.getElementById('modal-proveedor').classList.remove('hidden'); setTimeout(() => document.getElementById('busqueda-prod-prov').focus(), 200); }
function cerrarModalProveedor() { document.getElementById('modal-proveedor').classList.add('hidden'); }
function abrirModalBusqueda() { document.getElementById('modal-busqueda').classList.remove('hidden'); document.getElementById('input-busqueda-nombre').focus(); }
function cerrarModalBusqueda() { document.getElementById('modal-busqueda').classList.add('hidden'); }
function cerrarModalPeso() { document.getElementById('modal-peso').classList.add('hidden'); }
function cerrarModalCobro() { document.getElementById('modal-cobro').classList.add('hidden'); }
function cerrarModalRecuperar() { document.getElementById('modalVentasEspera').classList.add('hidden'); }

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
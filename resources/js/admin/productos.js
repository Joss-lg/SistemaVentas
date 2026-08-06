export function initProductosAdmin() {
    const modalProducto = document.getElementById('modalProducto');
    if (!modalProducto) return; // esta vista no está cargada

    window.abrirModalEditar = function (producto) {
        const form = document.getElementById('formEditar');
        form.action = `/admin/productos/${producto.id}`;

        document.getElementById('edit_descripcion').value = producto.descripcion;
        document.getElementById('edit_codigo').value = producto.codigo_barras;
        document.getElementById('edit_depto').value = producto.departamento_id;
        document.getElementById('edit_costo').value = producto.precio_costo;
        document.getElementById('edit_venta').value = producto.precio_venta;
        document.getElementById('edit_stock_actual').value = producto.stock_actual;
        document.getElementById('edit_stock_minimo').value = producto.stock_minimo;
        document.getElementById('edit_unidad').value = producto.unidad_medida;
        document.getElementById('edit_es_granel').checked = (producto.es_granel == 1);

        document.getElementById('modalEditar').classList.remove('hidden');
    };

    window.confirmarBaja = function (id, nombre) {
        Swal.fire({
            title: '¿DAR DE BAJA?',
            text: `Confirmar baja definitiva de: ${nombre}`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#27272a',
            confirmButtonText: 'SÍ, ELIMINAR',
            cancelButtonText: 'CANCELAR',
            background: '#18181b',
            color: '#ffffff',
            customClass: { popup: 'rounded-3xl border border-white/10 italic font-black uppercase' }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('formBaja');
                form.action = `/admin/productos/${id}`;
                form.submit();
            }
        });
    };
}
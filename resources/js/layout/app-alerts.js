export function initAlertas() {
    if (typeof Swal === 'undefined') return;

    const esOscuro = document.documentElement.classList.contains('dark');
    const bgModal = esOscuro ? '#0d0d0d' : '#ffffff';
    const colorTexto = esOscuro ? '#ffffff' : '#18181b';
    const borderColor = esOscuro ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)';

    // Notificación Toast flotante global
    window.Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        background: bgModal,
        color: colorTexto,
        customClass: {
            popup: `border border-[${borderColor}] rounded-xl shadow-2xl`
        },
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    // Función global para confirmaciones
    window.confirmarAccion = function (formId, titulo = '¿ESTÁS SEGURO?', texto = 'Esta acción no se podrá deshacer.') {
        Swal.fire({
            title: titulo.toUpperCase(),
            text: texto,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#27272a',
            confirmButtonText: 'SÍ, CONFIRMAR',
            cancelButtonText: 'CANCELAR',
            background: bgModal,
            color: colorTexto,
            customClass: {
                popup: `border border-[${borderColor}] rounded-2xl shadow-2xl`,
                confirmButton: 'px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold italic text-xs uppercase tracking-wider rounded-xl cursor-pointer mr-2',
                cancelButton: 'px-4 py-2 bg-zinc-800 hover:bg-zinc-700 text-white font-bold italic text-xs uppercase tracking-wider rounded-xl cursor-pointer'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    };

    // Disparar notificaciones automáticas desde la sesión de Laravel
    const flashSuccess = document.body.dataset.flashSuccess;
    const flashError = document.body.dataset.flashError;

    if (flashSuccess) {
        window.Toast.fire({ icon: 'success', title: flashSuccess });
    }
    if (flashError) {
        window.Toast.fire({ icon: 'error', title: flashError });
    }
}
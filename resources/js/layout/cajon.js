export function initCajon() {
    const btn = document.getElementById('btn-abrir-cajon');
    const { rutaCajonAbrir } = document.body.dataset;

    if (btn) {
        btn.addEventListener('click', () => abrirCajonConConfirmacion(rutaCajonAbrir));
    }

    // Se expone para que el POS lo dispare tras una venta, SIN pedir confirmación
    window.abrirCajonAutomatico = () => abrirCajonSilencioso(rutaCajonAbrir);
}

function abrirCajonSilencioso(ruta) {
    const { esAdmin } = document.body.dataset;
    if (esAdmin !== 'true') return; // mismo criterio que tenías antes: solo dispara si es admin

    try {
        const win = window.open(ruta, 'Cajon', 'width=1,height=1,left=0,top=0');
        if (win) setTimeout(() => win.close(), 500);
    } catch (e) {
        console.warn('No se pudo abrir el cajón automáticamente:', e);
    }
}

function abrirCajonConConfirmacion(ruta) {
    const isDark = document.documentElement.classList.contains('dark');

    Swal.fire({
        title: 'SISTEMA F1 - CAJÓN',
        text: '¿Confirmas el envío de señal para abrir el cajón de dinero?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#27272a',
        confirmButtonText: 'SÍ, ABRIR AHORA',
        cancelButtonText: 'CANCELAR',
        reverseButtons: true,
        background: isDark ? '#0d0d0d' : '#ffffff',
        color: isDark ? '#ffffff' : '#09090b',
        customClass: {
            popup: 'rounded-[2rem] border-2 border-zinc-200 dark:border-white/10 shadow-2xl',
            title: 'font-black italic uppercase tracking-tighter text-2xl',
            confirmButton: 'rounded-xl font-black italic uppercase text-xs px-8 py-4 transition-transform hover:scale-105',
            cancelButton: 'rounded-xl font-black italic uppercase text-xs px-8 py-4'
        }
    }).then((result) => {
        if (!result.isConfirmed) return;

        fetch(ruta)
            .then(() => {
                Swal.fire({
                    title: 'SEÑAL ENVIADA',
                    text: 'El cajón ha sido desbloqueado.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false,
                    background: isDark ? '#0d0d0d' : '#ffffff',
                    color: isDark ? '#ffffff' : '#09090b'
                });
            })
            .catch(err => {
                console.error('Error al abrir cajón:', err);
                const win = window.open(ruta, 'Cajon', 'width=100,height=100,left=0,top=0');
                if (win) setTimeout(() => win.close(), 500);
            });
    });
}
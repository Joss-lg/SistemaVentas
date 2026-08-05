export function initCajon() {
    const btn = document.getElementById('btn-abrir-cajon');

    if (btn) {
        btn.addEventListener('click', () => abrirCajonConConfirmacion());
    }

    // Se expone para que el POS lo dispare tras una venta, SIN pedir confirmación
    window.abrirCajonAutomatico = () => abrirCajonSilencioso();
}

function getConfigHardware() {
    const d = document.body.dataset;
    return {
        modoSimulado: d.modoSimulado === 'true',
        impresoraNombre: d.impresoraNombre || null,
        impresoraTipo: d.impresoraTipo || 'usb', // 'usb' | 'red'
        impresoraIp: d.impresoraIp || null,
        cajonComando: d.cajonComando || '27,112,0,25,250', // CSV de bytes decimales
    };
}

async function conectarQZ() {
    if (typeof qz === 'undefined') {
        throw new Error('QZ Tray no está instalado o la librería qz-tray.js no está cargada');
    }
    if (!qz.websocket.isActive()) {
        await qz.websocket.connect();
    }
}

function csvABytes(csv) {
    return csv.split(',').map(n => parseInt(n.trim(), 10));
}

async function enviarComandoCajon() {
    const { modoSimulado, impresoraNombre, impresoraTipo, impresoraIp, cajonComando } = getConfigHardware();

    if (modoSimulado) {
        console.log('[SIMULADO] Cajón abierto — comando ESC/POS habría sido enviado aquí');
        return;
    }

    await conectarQZ();

    // Si es impresora de red, QZ Tray necesita el host (IP); si es USB, necesita el nombre exacto en Windows
    const identificador = impresoraTipo === 'red' ? impresoraIp : impresoraNombre;

    if (!identificador) {
        throw new Error(`No hay ${impresoraTipo === 'red' ? 'IP' : 'nombre'} de impresora configurado en Admin > Configuración de Hardware`);
    }

    const config = impresoraTipo === 'red'
        ? qz.configs.create({ host: impresoraIp })
        : qz.configs.create(impresoraNombre);

    const bytes = csvABytes(cajonComando);
    const comandoBinario = String.fromCharCode(...bytes);

    await qz.print(config, [{ type: 'raw', format: 'command', data: comandoBinario }]);
}

function abrirCajonSilencioso() {
    const { esAdmin } = document.body.dataset;
    if (esAdmin !== 'true') return;

    enviarComandoCajon().catch(e => console.warn('No se pudo abrir el cajón:', e));
}

function abrirCajonConConfirmacion() {
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

        enviarComandoCajon()
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
                const { modoSimulado } = getConfigHardware();
                Swal.fire({
                    title: 'ERROR',
                    text: modoSimulado
                        ? 'Error simulado (revisa consola)'
                        : 'No se pudo conectar con QZ Tray / la impresora. Verifica que QZ Tray esté abierto.',
                    icon: 'error',
                    background: isDark ? '#0d0d0d' : '#ffffff',
                    color: isDark ? '#ffffff' : '#09090b'
                });
            });
    });
}
let puerto = null;
let conectada = false;
let simInterval = null;

function getConfigHardware() {
    const d = document.body.dataset;
    return {
        modoSimulado: d.modoSimulado === 'true',
        basculaActivada: d.basculaActivada === 'true',
        baudRate: parseInt(d.basculaBaud) || 9600,
    };
}

export function initBascula() {
    const btnConectar = document.getElementById('bascula-conectar-btn');
    const estado = document.getElementById('bascula-estado');
    const input = document.getElementById('input-peso-valor');

    if (!btnConectar) return; // el modal de peso no está en esta vista

    const { basculaActivada, modoSimulado } = getConfigHardware();

    // Si la báscula no está activada en config y tampoco estamos en modo simulado, ocultamos el botón
    if (!basculaActivada && !modoSimulado) {
        btnConectar.style.display = 'none';
        estado.textContent = 'Báscula no configurada';
        return;
    }

    btnConectar.addEventListener('click', async () => {
        if (conectada) {
            desconectar(input, estado, btnConectar);
            return;
        }

        const { modoSimulado, baudRate } = getConfigHardware();

        if (modoSimulado) {
            iniciarSimulacion(input, estado, btnConectar);
            return;
        }

        if (!('serial' in navigator)) {
            Swal.fire('Error', 'Este navegador no soporta báscula USB (usa Chrome o Edge, y HTTPS)', 'error');
            return;
        }

        try {
            puerto = await navigator.serial.requestPort();
            await puerto.open({ baudRate });
            conectada = true;
            estado.textContent = 'Báscula conectada';
            estado.classList.remove('text-zinc-400', 'dark:text-zinc-500');
            estado.classList.add('text-emerald-600', 'dark:text-emerald-500');
            btnConectar.textContent = 'Desconectar';
            leerPeso(input);
        } catch (e) {
            console.error('Error al conectar báscula:', e);
            Swal.fire('Error', 'No se pudo conectar con la báscula', 'error');
        }
    });
}

async function leerPeso(input) {
    const reader = puerto.readable.getReader();
    const decoder = new TextDecoder();
    let buffer = '';
    try {
        while (conectada) {
            const { value, done } = await reader.read();
            if (done) break;
            buffer += decoder.decode(value);

            // AJUSTAR este regex al formato real de trama de la báscula
            const match = buffer.match(/([+-]?\d+\.\d+)\s*kg/i);
            if (match) {
                input.value = parseFloat(match[1]).toFixed(3);
                buffer = '';
            }
        }
    } catch (e) {
        console.error('Error leyendo báscula:', e);
    } finally {
        reader.releaseLock();
    }
}

// ---- Solo para desarrollo/tiendas sin báscula física todavía ----
function iniciarSimulacion(input, estado, btn) {
    conectada = true;
    estado.textContent = 'Báscula conectada (SIMULADA)';
    estado.classList.remove('text-zinc-400', 'dark:text-zinc-500');
    estado.classList.add('text-amber-500');
    btn.textContent = 'Desconectar';
    let peso = 0;
    clearInterval(simInterval);
    simInterval = setInterval(() => {
        peso = Math.max(0, peso + (Math.random() - 0.4) * 0.15);
        input.value = peso.toFixed(3);
    }, 400);
}

// ---- Desconectar (real o simulada) ----
async function desconectar(input, estado, btn) {
    conectada = false;
    clearInterval(simInterval);

    if (puerto) {
        try {
            await puerto.close();
        } catch (e) {
            console.error('Error al cerrar puerto:', e);
        }
        puerto = null;
    }

    estado.textContent = 'Báscula desconectada';
    estado.classList.remove('text-emerald-600', 'dark:text-emerald-500', 'text-amber-500');
    estado.classList.add('text-zinc-400', 'dark:text-zinc-500');
    btn.textContent = 'Conectar báscula';
    input.value = '';
}
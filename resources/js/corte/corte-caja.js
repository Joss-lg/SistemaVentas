export function initCorteCaja() {
    const formCorte = document.getElementById('formCorte');
    
    if (!formCorte) return;

    formCorte.addEventListener('submit', function (e) {
        e.preventDefault(); // Detener el envío directo

        const inputEfectivo = document.getElementById('efectivo_real');
        const inputVentasEsperadas = document.getElementById('ventas_esperadas');

        if (!inputEfectivo || !inputVentasEsperadas) return;

        const efectivoReal = parseFloat(inputEfectivo.value);
        const totalSistema = parseFloat(inputVentasEsperadas.value);

        // 1. Validar si está vacío o es inválido
        if (isNaN(efectivoReal) || efectivoReal <= 0) {
            Swal.fire({
                title: '¡Ingresa el dinero!',
                text: 'Indica cuánto dinero en efectivo hay en la caja.',
                icon: 'error',
                confirmButtonColor: '#ea580c',
                background: '#0d0d0d',
                color: '#ffffff'
            });
            return;
        }

        // 2. Validar si falta dinero
        if (efectivoReal < totalSistema) {
            const diferencia = (totalSistema - efectivoReal).toFixed(2);
            Swal.fire({
                title: '¡Falta Dinero en Caja!',
                html: `No puedes cerrar la caja. Te faltan <b class="text-red-500">$${diferencia}</b> respecto al esperado ($${totalSistema.toFixed(2)}). Revisa bien tus cuentas.`,
                icon: 'error',
                confirmButtonColor: '#dc2626',
                background: '#0d0d0d',
                color: '#ffffff'
            });
            return;
        }

        // 3. Confirmación si todo cuadra o hay sobrante
        Swal.fire({
            title: '¿Confirmar cierre?',
            text: "Se guardará el corte y se cerrará tu sesión. Revisa bien el dinero antes de continuar.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ea580c',
            cancelButtonColor: '#3f3f46',
            confirmButtonText: 'Sí, cerrar sesión',
            cancelButtonText: 'No',
            background: '#0d0d0d',
            color: '#ffffff'
        }).then((result) => {
            if (result.isConfirmed) {
                formCorte.submit(); // Enviar formulario
            }
        });
    });
}
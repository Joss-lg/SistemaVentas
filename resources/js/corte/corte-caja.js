const corteCajaComponent = function(totalSistema = 0) {
    return {
        tab: 'ventas',
        efectivoReal: '',
        estadoAutorizacion: 'ninguna',
        autorizadoPor: '',
        solicitando: false,
        modalGasto: false,
        modalAutorizacion: false,
        ventaAbierta: null,
        totalSistema: parseFloat(totalSistema) || 0,
        timerPoll: null,

        init() {
            this.consultarEstadoInicial();
        },

        getThemeColors() {
            const isDark = document.documentElement.classList.contains('dark') || 
                           document.body.classList.contains('dark-mode') || 
                           window.userTheme === 'dark';

            return {
                background: isDark ? '#0d0d0d' : '#ffffff',
                color: isDark ? '#ffffff' : '#18181b',
                confirmButton: '#ea580c',
                cancelButton: isDark ? '#27272a' : '#71717a',
                customClass: {
                    cancelButton: 'text-white font-bold',
                    denyButton: 'text-white font-bold'
                }
            };
        },

        async consultarEstadoInicial() {
            try {
                const res = await fetch('/caja/autorizacion/estado');
                if (res.ok) {
                    const data = await res.json();
                    this.estadoAutorizacion = data.estado || 'ninguna';
                    this.autorizadoPor = data.autorizado_por || '';
                }
            } catch (e) {
                console.error("Error al consultar estado inicial:", e);
            }
        },

        async validarCierre(e) {
            if (e) e.preventDefault();

            const form = e ? e.target : document.getElementById('formCorte');
            const efectivo = parseFloat(this.efectivoReal);
            const total = this.totalSistema;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const theme = this.getThemeColors();

            if (isNaN(efectivo) || efectivo <= 0) {
                Swal.fire({
                    title: '¡Ingresa el dinero!',
                    text: 'Indica cuánto dinero en efectivo hay en la caja.',
                    icon: 'error',
                    confirmButtonColor: theme.confirmButton,
                    background: theme.background,
                    color: theme.color
                });
                return;
            }

            if (efectivo < total) {
                const diferencia = (total - efectivo).toFixed(2);

                try {
                    const resEstado = await fetch('/caja/autorizacion/estado');
                    const dataEstado = await resEstado.json();
                    
                    this.estadoAutorizacion = dataEstado.estado;
                    this.autorizadoPor = dataEstado.autorizado_por || '';

                    if (this.estadoAutorizacion === 'aprobada') {
                        form.submit();
                        return;
                    }

                    if (this.estadoAutorizacion === 'pendiente') {
                        this.mostrarAlertaEspera();
                        this.iniciarMonitoreo(form);
                        return;
                    }

                    Swal.fire({
                        title: '¡Falta Dinero en Caja!',
                        html: `Te faltan <b class="text-red-500">$${diferencia}</b> respecto al esperado ($${total.toFixed(2)}).<br><br><b>¿Se encuentra el dueño / administrador presente?</b>`,
                        icon: 'warning',
                        showCancelButton: true,
                        showDenyButton: true,
                        confirmButtonText: 'SÍ, ESTÁ AQUÍ',
                        denyButtonText: 'NO, ENVIAR SOLICITUD',
                        cancelButtonText: 'REVISAR CUENTAS',
                        confirmButtonColor: '#10b981',
                        denyButtonColor: theme.confirmButton,
                        cancelButtonColor: theme.cancelButton,
                        background: theme.background,
                        color: theme.color,
                        customClass: theme.customClass
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            const authResult = await Swal.fire({
                                title: 'AUTORIZACIÓN EN SITIO',
                                text: 'Ingrese la contraseña del Administrador:',
                                input: 'password',
                                showCancelButton: true,
                                confirmButtonText: 'AUTORIZAR Y CERRAR',
                                cancelButtonText: 'CANCELAR',
                                confirmButtonColor: '#10b981',
                                cancelButtonColor: theme.cancelButton,
                                background: theme.background,
                                color: theme.color,
                                customClass: theme.customClass,
                                preConfirm: (pass) => pass || Swal.showValidationMessage('Debe ingresar la contraseña')
                            });

                            if (authResult.isConfirmed) {
                                await this.enviarSolicitud(efectivo, authResult.value, true, form, csrfToken);
                            }
                        } else if (result.isDenied) {
                            await this.enviarSolicitud(efectivo, null, false, form, csrfToken);
                        }
                    });

                } catch (error) {
                    console.error('Error al validar caja:', error);
                }
                return;
            }

            Swal.fire({
                title: '¿Confirmar cierre?',
                text: "Se guardará el corte y se cerrará tu sesión.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: theme.confirmButton,
                cancelButtonColor: theme.cancelButton,
                confirmButtonText: 'Sí, cerrar sesión',
                cancelButtonText: 'No',
                background: theme.background,
                color: theme.color,
                customClass: theme.customClass
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        },

        async solicitarPermiso() {
            const efectivo = parseFloat(this.efectivoReal) || 0;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const form = document.getElementById('formCorte');
            await this.enviarSolicitud(efectivo, null, false, form, csrfToken);
        },

        async enviarSolicitud(efectivo, password, esInmediato, form, csrfToken) {
            const theme = this.getThemeColors();
            this.solicitando = true;

            const bodyPayload = { efectivo_real: efectivo };
            if (password) bodyPayload.admin_password = password;

            try {
                const res = await fetch('/caja/autorizacion/solicitar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(bodyPayload)
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    if (esInmediato) {
                        form.submit();
                    } else {
                        this.estadoAutorizacion = 'pendiente';
                        this.modalAutorizacion = false;
                        this.mostrarAlertaEspera();
                        this.iniciarMonitoreo(form);
                    }
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'No se pudo procesar la solicitud.',
                        icon: 'error',
                        confirmButtonColor: '#dc2626',
                        background: theme.background,
                        color: theme.color
                    });
                }
            } catch (e) {
                console.error("Error enviando solicitud:", e);
            } finally {
                this.solicitando = false;
            }
        },

        mostrarAlertaEspera() {
            const theme = this.getThemeColors();
            Swal.fire({
                title: 'Esperando Autorización...',
                html: 'Se envió la solicitud al administrador.<br><b>El corte se procesará automáticamente en cuanto sea aprobada.</b>',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                background: theme.background,
                color: theme.color,
                didOpen: () => Swal.showLoading()
            });
        },

        iniciarMonitoreo(form) {
            if (this.timerPoll) clearInterval(this.timerPoll);

            this.timerPoll = setInterval(async () => {
                try {
                    const res = await fetch('/caja/autorizacion/estado');
                    const data = await res.json();
                    
                    this.estadoAutorizacion = data.estado;
                    this.autorizadoPor = data.autorizado_por || '';

                    if (data.estado === 'aprobada') {
                        clearInterval(this.timerPoll);
                        Swal.close();
                        form.submit();
                    } else if (data.estado === 'rechazada') {
                        clearInterval(this.timerPoll);
                        const theme = this.getThemeColors();
                        Swal.fire({
                            title: 'Solicitud Rechazada',
                            text: 'El administrador ha rechazado la solicitud de faltante.',
                            icon: 'error',
                            confirmButtonColor: '#dc2626',
                            background: theme.background,
                            color: theme.color
                        });
                    }
                } catch (e) {
                    console.error('Error en el monitoreo de caja:', e);
                }
            }, 3000);
        }
    };
};

// Auto-exposición global inmediata apenas carga el módulo
window.corteCaja = corteCajaComponent;

if (window.Alpine) {
    window.Alpine.data('corteCaja', corteCajaComponent);
} else {
    document.addEventListener('alpine:init', () => {
        window.Alpine.data('corteCaja', corteCajaComponent);
    });
}

// Mantener exportación por compatibilidad si se importa en otro lado
export function initCorteCaja() {
    // Ya se auto-registró arriba
}
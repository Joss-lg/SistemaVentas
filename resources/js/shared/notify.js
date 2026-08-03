export function notify(icon, title) {
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
}
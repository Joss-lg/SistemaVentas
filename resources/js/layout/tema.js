export function initTema() {
    const btn = document.getElementById('theme-toggle');
    const dot = document.getElementById('switch-dot');
    if (!btn || !dot) return;

    const html = document.documentElement;
    const { rutaUserTheme, csrf } = document.body.dataset;

    btn.addEventListener('click', () => {
        const esOscuro = html.classList.toggle('dark');
        const nuevoTema = esOscuro ? 'oscuro' : 'claro';

        btn.classList.toggle('bg-red-600', esOscuro);
        btn.classList.toggle('bg-zinc-300', !esOscuro);
        dot.classList.toggle('translate-x-[1.375rem]', esOscuro);
        dot.classList.toggle('translate-x-1', !esOscuro);

        fetch(rutaUserTheme, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ theme: nuevoTema })
        }).catch(err => console.error('Error al guardar el tema:', err));
    });
}
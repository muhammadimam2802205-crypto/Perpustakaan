document.addEventListener('DOMContentLoaded', function () {
    const toggles = document.querySelectorAll('[data-theme-toggle]');
    const theme = localStorage.getItem('perpus-theme') || 'light';
    setTheme(theme);

    toggles.forEach(btn => {
        btn.addEventListener('click', function () {
            const next = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
            setTheme(next);
        });
    });

    document.querySelectorAll('[data-counter]').forEach(el => {
        const value = parseInt(el.textContent, 10) || 0;
        animateNumber(el, value, 1500);
    });
});

function setTheme(mode) {
    if (mode === 'dark') {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
    localStorage.setItem('perpus-theme', mode);
}

function animateNumber(el, target, duration = 1200) {
    const start = 0;
    const stepTime = Math.max(Math.floor(duration / target), 20);
    let current = start;

    const timer = setInterval(() => {
        current += Math.ceil(target / (duration / stepTime));
        if (current >= target) {
            el.textContent = target;
            clearInterval(timer);
        } else {
            el.textContent = current;
        }
    }, stepTime);
}

function showToast(message, type = 'success') {
    const toastHtml = `
        <div class="toast toast-custom show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-inner bg-white p-3">
                <div class="d-flex align-items-center">
                    <div class="me-3 text-${type === 'success' ? 'success' : type === 'danger' ? 'danger' : 'info'}">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'info-circle'} fa-2x"></i>
                    </div>
                    <div>
                        <strong>${type === 'success' ? 'Berhasil' : type === 'danger' ? 'Error' : 'Info'}</strong>
                        <div>${message}</div>
                    </div>
                    <button type="button" class="btn-close ms-auto" aria-label="Close" onclick="this.closest('.toast').remove()"></button>
                </div>
            </div>
        </div>
    `;

    const wrapper = document.createElement('div');
    wrapper.innerHTML = toastHtml;
    document.body.appendChild(wrapper.firstElementChild);
    setTimeout(() => {
        document.querySelectorAll('.toast-custom').forEach(t => t.remove());
    }, 4500);
}

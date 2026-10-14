import gsap from 'gsap';

const $ = (selector, ctx = document) => ctx.querySelector(selector);
const $$ = (selector, ctx = document) => [...ctx.querySelectorAll(selector)];

const root = document.documentElement;
const motion = root.classList.contains('js');

function passwordToggles() {
    $$('[data-pw]').forEach((button) => {
        const input = document.getElementById(button.dataset.pw);
        button.addEventListener('click', () => {
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            button.firstElementChild.className = show ? 'ph-bold ph-eye-slash' : 'ph-bold ph-eye';
            button.setAttribute('aria-pressed', String(show));
            button.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Lihat kata sandi');
        });
    });
}

function strength() {
    const input = $('#password');
    const meter = $('[data-strength]');
    if (!input || !meter) return;

    const bars = $$('.strength__bars i', meter);
    const label = $('[data-strength-label]', meter);
    const levels = [
        { max: 1, text: 'Lemah', color: '#ff9fb0' },
        { max: 3, text: 'Sedang', color: '#ffd37a' },
        { max: 4, text: 'Sangat kuat', color: '#7ce3b4' },
    ];

    input.addEventListener('input', () => {
        const v = input.value;
        let score = 0;
        if (v.length >= 8) score += 1;
        if (/[A-Z]/.test(v) && /[a-z]/.test(v)) score += 1;
        if (/[0-9]/.test(v)) score += 1;
        if (/[^A-Za-z0-9]/.test(v)) score += 1;

        meter.classList.toggle('is-on', v.length > 0);
        const level = levels.find((l) => score <= l.max);
        meter.style.setProperty('--sc', level.color);
        label.textContent = level.text;
        bars.forEach((bar, i) => bar.classList.toggle('on', i < score));
    });
}

function intro() {
    if (!motion) return;

    gsap.set('.hero__line > span', { yPercent: 118, opacity: 1 });
    gsap.timeline({ defaults: { ease: 'expo.out' } })
        .fromTo('.hero__bg img', { scale: 1.15, opacity: 0 }, { scale: 1, opacity: 0.22, duration: 2.2 }, 0)
        .fromTo('.nav', { yPercent: -170, opacity: 0 }, { yPercent: 0, opacity: 1, duration: 1.1 }, 0.1)
        .to('.hero__line > span', { yPercent: 0, duration: 1.3, stagger: 0.13 }, 0.2)
        .fromTo('[data-hero]', { y: 30, opacity: 0 }, { y: 0, opacity: 1, duration: 1, stagger: 0.1 }, 0.55);

    const logo = $('.hero__bg img');
    if (logo && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        const x = gsap.quickTo(logo, 'x', { duration: 1.2, ease: 'power3.out' });
        const y = gsap.quickTo(logo, 'y', { duration: 1.2, ease: 'power3.out' });
        window.addEventListener('pointermove', (event) => {
            x((event.clientX / window.innerWidth - 0.5) * -36);
            y((event.clientY / window.innerHeight - 0.5) * -36);
        });
    }
}

try {
    passwordToggles();
    strength();
    intro();
} catch (error) {
    // Animasi gagal: tampilkan form apa adanya
    root.classList.remove('js');
    console.error('[auth]', error);
}

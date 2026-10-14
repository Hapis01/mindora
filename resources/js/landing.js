import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { SplitText } from 'gsap/SplitText';
import Lenis from 'lenis';
import { Chart, RadarController, RadialLinearScale, PointElement, LineElement, Filler } from 'chart.js';

window.__landing = true;

gsap.registerPlugin(ScrollTrigger, SplitText);
Chart.register(RadarController, RadialLinearScale, PointElement, LineElement, Filler);

const $ = (selector, ctx = document) => ctx.querySelector(selector);
const $$ = (selector, ctx = document) => [...ctx.querySelectorAll(selector)];

const root = document.documentElement;
// Kelas "js" dipasang di <head> hanya jika pengguna TIDAK meminta reduced motion.
const motion = root.classList.contains('js');
const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

let lenis = null;

/* ---------------------------------------------------------------- Scroll */

function initScroll() {
    if (!motion) return;
    lenis = new Lenis({ lerp: 0.1 });
    lenis.on('scroll', ScrollTrigger.update);
    gsap.ticker.add((time) => lenis.raf(time * 1000));
    gsap.ticker.lagSmoothing(0);
}

function goTo(target) {
    if (lenis) lenis.scrollTo(target, { duration: 1.5, easing: (t) => 1 - Math.pow(1 - t, 4) });
    else target.scrollIntoView();
}

/* ------------------------------------------------------------------- Nav */

function initNav() {
    const menu = $('#menu');
    const burger = $('#burger');
    let open = false;

    const setMenu = (next) => {
        open = next;
        burger.setAttribute('aria-expanded', String(open));
        burger.setAttribute('aria-label', open ? 'Tutup menu' : 'Buka menu');
        burger.firstElementChild.className = open ? 'ph ph-x' : 'ph ph-list';
        document.body.style.overflow = open ? 'hidden' : '';

        if (open) {
            lenis?.stop();
            menu.classList.add('is-open');
            gsap.fromTo(menu, { opacity: 0 }, { opacity: 1, duration: 0.35 });
            gsap.fromTo($$('.menu__links a, .menu__cta'), { y: 36, opacity: 0 },
                { y: 0, opacity: 1, duration: 0.7, stagger: 0.07, ease: 'expo.out', delay: 0.05 });
        } else {
            lenis?.start();
            gsap.to(menu, { opacity: 0, duration: 0.25, onComplete: () => menu.classList.remove('is-open') });
        }
    };

    burger.addEventListener('click', () => setMenu(!open));
    document.addEventListener('keydown', (event) => { if (event.key === 'Escape' && open) setMenu(false); });

    $$('a[href^="#"]').forEach((link) => {
        const id = link.getAttribute('href');
        if (id.length < 2) return;
        link.addEventListener('click', (event) => {
            const target = $(id);
            if (!target) return;
            if (open) setMenu(false);
            if (lenis) {
                event.preventDefault();
                goTo(target);
            }
        });
    });

}

// Dibuat setelah pin agar posisi memperhitungkan pin spacer
function navSpy() {
    if (!motion) return;

    // Tautan aktif mengikuti bagian yang sedang dilihat
    const links = new Map($$('.nav__links a').map((a) => [a.getAttribute('href').slice(1), a]));
    links.forEach((link, id) => {
        const section = document.getElementById(id);
        if (!section) return;
        ScrollTrigger.create({
            trigger: section,
            start: 'top 55%',
            end: 'bottom 55%',
            onToggle: (self) => link.classList.toggle('is-active', self.isActive),
        });
    });
}

/* ------------------------------------------------------------------ Hero */

function heroIntro() {
    if (!motion) return Promise.resolve();

    return new Promise((resolve) => {
        gsap.set('.hero__line > span', { yPercent: 118, opacity: 1 });

        gsap.timeline({ defaults: { ease: 'expo.out' }, onComplete: resolve })
            .fromTo('.hero__bg img', { scale: 1.2, opacity: 0 }, { scale: 1, opacity: 0.22, duration: 2.4 }, 0)
            .fromTo('.nav', { yPercent: -170, opacity: 0 }, { yPercent: 0, opacity: 1, duration: 1.2 }, 0.2)
            .to('.hero__line > span', { yPercent: 0, duration: 1.4, stagger: 0.14 }, 0.2)
            .fromTo('.chip-img', { scale: 0.35, rotate: -14, opacity: 0 },
                { scale: 1, rotate: 0, opacity: 1, duration: 1.3, ease: 'back.out(1.5)' }, 0.8)
            .fromTo('[data-hero]', { y: 30, opacity: 0 }, { y: 0, opacity: 1, duration: 1.1, stagger: 0.12 }, 0.95);

        gsap.to('.hero__bg img', {
            yPercent: 14,
            ease: 'none',
            scrollTrigger: { trigger: '.hero', start: 'top top', end: 'bottom top', scrub: true },
        });
        gsap.to('.hero__inner', {
            y: -90,
            opacity: 0.1,
            ease: 'none',
            scrollTrigger: { trigger: '.hero', start: 'top top', end: 'bottom 30%', scrub: true },
        });
    });
}

/* ---------------------------------------------------- Manifesto (scrub) */

function manifesto() {
    const el = $('.manifesto__text');
    if (!el || !motion) return;

    SplitText.create(el, {
        type: 'words',
        autoSplit: true,
        onSplit(self) {
            gsap.set(el, { opacity: 1 });
            return gsap.fromTo(self.words, { opacity: 0.13 }, {
                opacity: 1,
                ease: 'none',
                stagger: 0.12,
                scrollTrigger: { trigger: el, start: 'top 78%', end: 'bottom 52%', scrub: true },
            });
        },
    });
}

/* ----------------------------------- Sehari bersama Mindora (pin+scrub) */

function day() {
    const stage = $('.day__stage');
    if (!stage || !motion) return;

    const scenes = $$('.scene', stage);
    const clock = $('[data-clock]', stage);
    const times = [400, 735, 1170, 1390];
    const state = { t: times[0] };
    const format = (m) => {
        const v = Math.round(m);
        return `${String(Math.floor(v / 60)).padStart(2, '0')}:${String(v % 60).padStart(2, '0')}`;
    };

    gsap.set(scenes, { autoAlpha: 0, y: 40 });
    gsap.set(scenes[0], { autoAlpha: 1, y: 0 });
    clock.textContent = format(state.t);

    const hold = 1.2;
    const run = 1.2;
    const total = (hold + run) * (scenes.length - 1) + hold;

    const tl = gsap.timeline({
        defaults: { ease: 'none' },
        scrollTrigger: {
            trigger: stage,
            start: 'top top',
            end: () => `+=${window.innerHeight * 4}`,
            pin: true,
            scrub: 0.6,
            anticipatePin: 1,
            invalidateOnRefresh: true,
        },
    });

    tl.fromTo(stage, { backgroundColor: '#2b2475' }, { backgroundColor: '#0b0a29', duration: total }, 0)
        .fromTo('.day__glow', { scale: 0.8, opacity: 1 }, { scale: 1.25, opacity: 0.55, duration: total }, 0)
        .fromTo('.rail__fill', { scaleX: 0 }, { scaleX: 1, duration: total }, 0);

    for (let i = 1; i < scenes.length; i += 1) {
        const at = (i - 1) * (hold + run) + hold;
        tl.to(state, {
            t: times[i],
            duration: run,
            ease: 'power1.inOut',
            onUpdate: () => { clock.textContent = format(state.t); },
        }, at)
            .to(scenes[i - 1], { autoAlpha: 0, y: -40, duration: 0.6, ease: 'power2.in' }, at)
            .fromTo(scenes[i], { autoAlpha: 0, y: 40 }, { autoAlpha: 1, y: 0, duration: 0.6, ease: 'power2.out' }, at + 0.6);
    }
}

/* ----------------------------------------------- Judul dan reveal umum */

function headings() {
    if (!motion) return;

    $$('[data-split]').forEach((el) => {
        SplitText.create(el, {
            type: 'lines',
            mask: 'lines',
            autoSplit: true,
            onSplit(self) {
                // Beri ruang agar descender (y, g, p) tidak terpotong mask
                self.masks.forEach((mask) => {
                    mask.style.paddingBottom = '0.14em';
                    mask.style.marginBottom = '-0.14em';
                });
                gsap.set(el, { opacity: 1 });
                if (el.dataset.done) return undefined;

                return gsap.from(self.lines, {
                    yPercent: 112,
                    duration: 1.2,
                    ease: 'expo.out',
                    stagger: 0.1,
                    scrollTrigger: { trigger: el, start: 'top 86%', once: true },
                    onComplete: () => { el.dataset.done = '1'; },
                });
            },
        });
    });
}

function reveals() {
    if (!motion) return;

    ScrollTrigger.batch('[data-r]', {
        start: 'top 90%',
        once: true,
        onEnter: (items) => gsap.fromTo(items, { y: 38, opacity: 0 },
            { y: 0, opacity: 1, duration: 1.1, ease: 'expo.out', stagger: 0.1, overwrite: true }),
    });
}

/* ------------------------------------------------------- Coba satu soal */

function countTo(el, value, decimals = 0) {
    const fmt = (n) => n.toFixed(decimals).replace('.', ',');
    if (!motion) {
        el.textContent = fmt(value);
        return;
    }
    const o = { v: 0 };
    gsap.to(o, { v: value, duration: 1.1, ease: 'power3.out', onUpdate: () => { el.textContent = fmt(o.v); } });
}

function quiz() {
    const tabs = $$('[role="tab"]');
    const panels = tabs.map((tab) => document.getElementById(tab.getAttribute('aria-controls')));
    const started = new WeakMap();

    const showFeedback = (panel) => {
        const fb = $('[data-fb]', panel);
        fb.classList.add('is-on');
        if (motion) gsap.fromTo(fb, { y: 18, opacity: 0 }, { y: 0, opacity: 1, duration: 0.8, ease: 'expo.out' });
    };

    const reset = (panel) => {
        $$('.opt', panel).forEach((b) => { b.disabled = false; b.classList.remove('is-correct', 'is-wrong'); });
        $$('.likert button', panel).forEach((b) => { b.disabled = false; b.classList.remove('is-picked'); });
        $('[data-fb]', panel).classList.remove('is-on');
        started.set(panel, performance.now());
    };

    const select = (index) => {
        tabs.forEach((tab, i) => {
            const on = i === index;
            tab.setAttribute('aria-selected', String(on));
            tab.tabIndex = on ? 0 : -1;
            panels[i].hidden = !on;
        });
        started.set(panels[index], performance.now());
        if (motion) gsap.fromTo(panels[index], { y: 18, opacity: 0 }, { y: 0, opacity: 1, duration: 0.6, ease: 'expo.out' });
    };

    tabs.forEach((tab, i) => {
        tab.addEventListener('click', () => select(i));
        tab.addEventListener('keydown', (event) => {
            if (event.key !== 'ArrowRight' && event.key !== 'ArrowLeft') return;
            const next = (i + (event.key === 'ArrowRight' ? 1 : -1) + tabs.length) % tabs.length;
            select(next);
            tabs[next].focus();
        });
    });

    started.set(panels[0], performance.now());

    // Soal kognitif
    const cog = panels[0];
    $$('.opt', cog).forEach((button) => {
        button.addEventListener('click', () => {
            const seconds = (performance.now() - started.get(cog)) / 1000;
            const correct = button.hasAttribute('data-correct');
            $$('.opt', cog).forEach((b) => { b.disabled = true; });
            $$('.opt', cog).forEach((b) => {
                if (b.hasAttribute('data-correct')) b.classList.add('is-correct');
                else if (b === button) b.classList.add('is-wrong');
                else b.classList.add('is-wrong');
            });
            $('[data-fb-verdict]', cog).textContent = correct ? 'Tepat' : 'Belum tepat';
            countTo($('[data-fb-time]', cog), Math.min(seconds, 99), 1);
            showFeedback(cog);
        });
    });
    $('.fb__again', cog).addEventListener('click', () => reset(cog));

    // Pernyataan kepribadian
    const per = panels[1];
    $$('.likert button', per).forEach((button) => {
        button.addEventListener('click', () => {
            const pick = Number(button.dataset.v);
            const position = ((pick - 1) / 4) * 100;
            $$('.likert button', per).forEach((b) => { b.disabled = true; });
            button.classList.add('is-picked');
            countTo($('[data-fb-pos]', per), position);
            const bar = $('.measure i', per);
            if (motion) gsap.fromTo(bar, { width: 0 }, { width: `${position}%`, duration: 1.1, ease: 'expo.out' });
            else bar.style.width = `${position}%`;
            showFeedback(per);
        });
    });
    $('.fb__again', per).addEventListener('click', () => {
        reset(per);
        $('.measure i', per).style.width = '0%';
    });
}

/* ------------------------------------------------------ Bento: radar ML */

const ARCHETYPES = [
    { data: [60, 80, 48, 65, 40], desc: 'Profil berorientasi pada pemikiran metodis, ketepatan data kuantitatif, dan eksekusi tugas yang sistematis.' },
    { data: [85, 55, 68, 70, 50], desc: 'Profil dengan keterbukaan ide tinggi, antusiasme inovasi konseptual, dan fleksibilitas beradaptasi.' },
    { data: [70, 66, 84, 86, 38], desc: 'Profil fasilitator interpersonal, berorientasi menjaga keharmonisan tim, dan komunikatif.' },
    { data: [52, 76, 40, 58, 35], desc: 'Profil fokus mandiri yang tekun, konsisten, dan memiliki daya tahan kerja individual yang solid.' },
];

function radar() {
    const canvas = $('#radar');
    if (!canvas) return;

    const buttons = $$('.arch button');
    const desc = $('#arch-desc');
    let chart = null;

    const build = () => {
        chart = new Chart(canvas, {
            type: 'radar',
            data: {
                labels: ['Openness', 'Conscientiousness', 'Extraversion', 'Agreeableness', 'Neuroticism'],
                datasets: [{
                    data: ARCHETYPES[0].data,
                    borderColor: '#a99bff',
                    borderWidth: 2,
                    backgroundColor: 'rgba(169,155,255,0.22)',
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#07061b',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: 6 },
                animation: { duration: motion ? 1000 : 0, easing: 'easeOutQuart' },
                plugins: { legend: { display: false }, tooltip: { enabled: false } },
                scales: {
                    r: {
                        min: 0,
                        max: 100,
                        ticks: { display: false, stepSize: 25 },
                        grid: { color: 'rgba(190,184,255,0.16)' },
                        angleLines: { color: 'rgba(190,184,255,0.16)' },
                        pointLabels: { color: '#a3a1cf', font: { size: 11, weight: '600', family: "'Plus Jakarta Sans Variable', sans-serif" } },
                    },
                },
            },
        });
    };

    buttons.forEach((button, i) => {
        button.addEventListener('click', () => {
            buttons.forEach((b) => b.setAttribute('aria-pressed', String(b === button)));
            desc.textContent = ARCHETYPES[i].desc;
            if (!chart) return;
            chart.data.datasets[0].data = ARCHETYPES[i].data;
            chart.update();
        });
    });

    if (motion) ScrollTrigger.create({ trigger: canvas, start: 'top 80%', once: true, onEnter: build });
    else build();
}

function bento() {
    if (!motion) return;

    gsap.from('.shap__bar', {
        scaleX: 0,
        duration: 1.3,
        ease: 'expo.out',
        stagger: 0.09,
        scrollTrigger: { trigger: '.cell--shap', start: 'top 75%', once: true },
    });
    gsap.from('.sees li', {
        opacity: 0,
        x: -14,
        duration: 0.7,
        ease: 'power3.out',
        stagger: 0.08,
        scrollTrigger: { trigger: '.cell--ai', start: 'top 75%', once: true },
    });
}

/* ------------------------------------------------------ Konsultasi (AI) */

function chat() {
    const box = $('.chat__body');
    if (!box || !motion) return;

    const bubbles = $$('[data-bub]', box);
    const tl = gsap.timeline({
        paused: true,
        defaults: { ease: 'expo.out' },
    });

    bubbles.forEach((bubble, i) => {
        if (bubble.dataset.bub === 'u') {
            tl.fromTo(bubble, { y: 20, opacity: 0 }, { y: 0, opacity: 1, duration: 0.7 }, i === 0 ? 0 : '+=0.5');
            return;
        }

        const dots = $('.dots', bubble);
        const text = $('.txt', bubble);
        const split = SplitText.create(text, { type: 'words' });
        gsap.set(split.words, { opacity: 0 });
        gsap.set(dots, { opacity: 0 });

        tl.fromTo(bubble, { y: 20, opacity: 0 }, { y: 0, opacity: 1, duration: 0.6 }, '+=0.35')
            .to(dots, { opacity: 1, duration: 0.3 }, '<0.15')
            .to(dots, { opacity: 0, duration: 0.25 }, '+=1.1')
            .to(split.words, { opacity: 1, duration: 0.3, ease: 'none', stagger: 0.045 }, '<0.1');
    });

    ScrollTrigger.create({ trigger: box, start: 'top 72%', once: true, onEnter: () => tl.play() });
}

/* ------------------------------------------------------------ Interaksi */

function spotlight() {
    if (!finePointer) return;
    $$('.card').forEach((card) => {
        card.addEventListener('pointermove', (event) => {
            const rect = card.getBoundingClientRect();
            card.style.setProperty('--mx', `${event.clientX - rect.left}px`);
            card.style.setProperty('--my', `${event.clientY - rect.top}px`);
        });
    });
}

function magnetic() {
    if (!motion || !finePointer) return;

    $$('.hero .btn, .cta .btn').forEach((button) => {
        const x = gsap.quickTo(button, 'x', { duration: 0.5, ease: 'power3.out' });
        const y = gsap.quickTo(button, 'y', { duration: 0.5, ease: 'power3.out' });
        button.addEventListener('pointermove', (event) => {
            const rect = button.getBoundingClientRect();
            x((event.clientX - (rect.left + rect.width / 2)) * 0.22);
            y((event.clientY - (rect.top + rect.height / 2)) * 0.32);
        });
        button.addEventListener('pointerleave', () => { x(0); y(0); });
    });
}

function closing() {
    if (!motion) return;

    gsap.from('.foot__word', {
        yPercent: 38,
        opacity: 0,
        ease: 'none',
        scrollTrigger: { trigger: '.foot', start: 'top 92%', end: 'bottom bottom', scrub: true },
    });
}

/* --------------------------------------------- Disclaimer dan notifikasi */

function disclaimer(afterIntro) {
    const dialog = $('#disclaimer');
    if (!dialog || typeof dialog.showModal !== 'function') return;

    const open = () => {
        if (dialog.open) return;
        dialog.showModal();
        lenis?.stop();
    };

    dialog.addEventListener('close', () => lenis?.start());
    dialog.addEventListener('click', (event) => { if (event.target === dialog) dialog.close(); });
    $('#disclaimer-close').addEventListener('click', () => dialog.close());
    $$('[data-open-disclaimer]').forEach((button) => button.addEventListener('click', open));

    // Popup iklan/disclaimer tampil setiap halaman dimuat (perilaku asli)
    afterIntro.then(() => setTimeout(open, 700));
}

function toast() {
    const el = $('#toast');
    if (!el) return;

    if (!motion) {
        setTimeout(() => el.remove(), 5000);
        return;
    }
    gsap.timeline()
        .fromTo(el, { yPercent: 0, y: 30, opacity: 0 }, { y: 0, opacity: 1, duration: 0.7, ease: 'expo.out', delay: 0.9 })
        .to(el, { y: 20, opacity: 0, duration: 0.5, delay: 4, onComplete: () => el.remove() });
}

/* ------------------------------------------------------------------ Init */

async function init() {
    await Promise.race([document.fonts.ready, new Promise((resolve) => setTimeout(resolve, 2500))]);

    initScroll();
    initNav();

    // Urutan pembuatan ScrollTrigger mengikuti urutan halaman agar pin akurat
    const intro = heroIntro();
    manifesto();
    day();
    navSpy();
    headings();
    reveals();
    quiz();
    radar();
    bento();
    chat();
    spotlight();
    magnetic();
    closing();
    disclaimer(intro);
    toast();

    window.addEventListener('load', () => ScrollTrigger.refresh());
}

init().catch((error) => {
    // Jika animasi gagal, tampilkan halaman statis penuh, jangan biarkan konten tersembunyi
    root.classList.remove('js');
    console.error('[landing]', error);
});

function openSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    if (!sidebar) return;

    sidebar.classList.add('open');
    if (overlay) overlay.classList.add('active');
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    if (!sidebar) return;

    sidebar.classList.remove('open');
    if (overlay) overlay.classList.remove('active');
}

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    if (!sidebar) return;

    const isOpen = sidebar.classList.toggle('open');
    if (overlay) {
        if (isOpen) {
            overlay.classList.add('active');
        } else {
            overlay.classList.remove('active');
        }
    }
}

const scrollBtn = document.getElementById('scrollTopBtn');

if (scrollBtn) {
    // Show / hide with fade
    window.addEventListener('scroll', () => {
        if (window.scrollY > 200) {
            scrollBtn.classList.add('visible');
        } else {
            scrollBtn.classList.remove('visible');
        }
    });

    // Click to scroll to top smoothly
    scrollBtn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}


// ==========================
// Got the bag after 3 tries
// ==========================

let logoClickCount = 0;
let logoClickTimer = null;
const CLICK_WINDOW_MS = 600;

function handleLogoEasterEggClick(e) {
    if (e.preventDefault) e.preventDefault();

    const logoTarget = e.currentTarget;
    const duckSrc = logoTarget.dataset.duckSrc;
    if (!duckSrc) return;

    if (logoClickTimer) {
        clearTimeout(logoClickTimer);
    }
    logoClickTimer = setTimeout(() => {
        logoClickCount = 0;
        logoClickTimer = null;
    }, CLICK_WINDOW_MS);

    logoClickCount++;

    if (logoClickCount >= 3) {
        spawnDuck(duckSrc, e);
    }
}

function spawnDuck(duckSrc, event) {
    const duck = document.createElement('img');
    duck.src = duckSrc;
    duck.alt = 'Dancing duck';
    duck.className = 'duck-easter-egg';

    document.body.appendChild(duck);

    const startX = event.clientX || (window.innerWidth / 2);
    const startY = event.clientY || (window.innerHeight / 2);

    const amplitude = 20 + Math.random() * 40;
    const verticalDistance = 120 + Math.random() * 80;
    const duration = 1200 + Math.random() * 600;
    const frequency = 1.5 + Math.random() * 2.5;

    const startTime = performance.now();

    function animate(time) {
        const elapsed = time - startTime;
        let t = elapsed / duration;
        if (t > 1) t = 1;

        const y = startY - t * verticalDistance; // move up
        const x = startX + Math.sin(t * Math.PI * frequency) * amplitude;

        const scale = 0.8 + t * 0.4;
        const opacity = 1 - t;

        duck.style.left = (x - 30) + 'px';
        duck.style.top  = (y - 30) + 'px';
        duck.style.opacity = opacity;
        duck.style.transform = `scale(${scale})`;

        if (t < 1) {
            requestAnimationFrame(animate);
        } else {
            duck.remove();
        }
    }

    requestAnimationFrame(animate);
}

document.addEventListener('DOMContentLoaded', () => {
    const sidebarLogo = document.getElementById('sidebar-logo');
    if (sidebarLogo) {
        sidebarLogo.addEventListener('click', handleLogoEasterEggClick);
    }

    // Initialize dashboard carousels (if present on this page)
    setupDashboardCarousels();
});
// ==========================
// Dashboard carousels (left/right, looping)
// ==========================

document.addEventListener('DOMContentLoaded', () => {
    const carousels = document.querySelectorAll('.dashboard-carousel');

    carousels.forEach(carousel => {
        const track = carousel.querySelector('.dashboard-carousel-track');
        const prevBtn = carousel.querySelector('.carousel-btn.prev');
        const nextBtn = carousel.querySelector('.carousel-btn.next');

        if (!track || !prevBtn || !nextBtn) return;

        // One card width to scroll each click
        const getStep = () => {
            const firstCard = track.querySelector('.dash-card');
            if (!firstCard) return track.clientWidth * 0.8;
            const style = window.getComputedStyle(firstCard);
            const gap = parseFloat(style.marginRight || 0);
            return firstCard.offsetWidth + gap;
        };

        function scrollNext() {
            const step = getStep();
            const maxScroll = track.scrollWidth - track.clientWidth - 5;

            if (track.scrollLeft >= maxScroll) {
                // Loop back to start
                track.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                track.scrollBy({ left: step, behavior: 'smooth' });
            }
        }

        function scrollPrev() {
            const step = getStep();

            if (track.scrollLeft <= 5) {
                // Jump to end
                const maxScroll = track.scrollWidth - track.clientWidth;
                track.scrollTo({ left: maxScroll, behavior: 'smooth' });
            } else {
                track.scrollBy({ left: -step, behavior: 'smooth' });
            }
        }

        nextBtn.addEventListener('click', scrollNext);
        prevBtn.addEventListener('click', scrollPrev);
    });
});

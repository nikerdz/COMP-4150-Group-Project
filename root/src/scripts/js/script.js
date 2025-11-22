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
const CLICK_WINDOW_MS = 200;

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

// ==========================
// Dashboard carousels (left/right, looping)
// ==========================

function setupDashboardCarousels() {
    const carousels = document.querySelectorAll('.dashboard-carousel');

    carousels.forEach(carousel => {
        const track  = carousel.querySelector('.dashboard-carousel-track');
        const prevBtn = carousel.querySelector('.carousel-btn.prev');
        const nextBtn = carousel.querySelector('.carousel-btn.next');

        if (!track || !prevBtn || !nextBtn) return;

        // One card width to scroll each click
        const getStep = () => {
            const firstCard = track.querySelector('.dash-card');
            if (!firstCard) {
                // Fallback: scroll most of the visible width
                return track.clientWidth * 0.8;
            }
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
// Club view – Event tabs (Pending / Upcoming / Past)
// ==========================
document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.event-tab');
    const contents = document.querySelectorAll('.event-tab-content');

    if (!tabs.length || !contents.length) return;

    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            const target = this.dataset.tab; // pending, upcoming, past

            // Remove active class from all tabs
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            // Hide all content sections
            contents.forEach(c => c.style.display = 'none');

            // Show only the selected content
            const section = document.getElementById('tab-' + target);
            if (section) {
                section.style.display = 'block';
            }
        });
    });
});

// ==========================
// My Clubs – "Load more" behaviour
// ==========================
document.addEventListener('DOMContentLoaded', function () {
    const grid        = document.getElementById('userClubsGrid');
    const loadMoreBtn = document.getElementById('userClubsLoadMore');

    // If we're not on the My Clubs page, do nothing
    if (!grid || !loadMoreBtn) return;

    // Show 2 rows per click on desktop (3 cards per row -> 6 cards)
    const CARDS_PER_CLICK = 6;

    loadMoreBtn.addEventListener('click', function () {
        const hiddenCards = grid.querySelectorAll('.explore-card.is-hidden');
        let revealed = 0;

        hiddenCards.forEach(card => {
            if (revealed < CARDS_PER_CLICK) {
                card.classList.remove('is-hidden');
                revealed++;
            }
        });

        // If no hidden cards remain, hide the button
        if (!grid.querySelector('.explore-card.is-hidden')) {
            loadMoreBtn.style.display = 'none';
        }
    });
});

// ==========================
// Explore – Filters toggle + "Load more"
// ==========================
document.addEventListener('DOMContentLoaded', function () {
    const exploreGrid     = document.getElementById('exploreGrid');
    const exploreLoadMore = document.getElementById('exploreLoadMore');
    const filterBtn       = document.getElementById('exploreFilterToggle');
    const filterPanel     = document.getElementById('exploreFilterPanel');

    // Toggle filters open/closed
    if (filterBtn && filterPanel) {
        filterBtn.addEventListener('click', function () {
            filterPanel.classList.toggle('is-open');
        });
    }

    // Load more explore cards (2 rows = 6 cards at a time)
    if (exploreGrid && exploreLoadMore) {
        const CARDS_PER_CLICK = 6;

        exploreLoadMore.addEventListener('click', function () {
            const hiddenCards = exploreGrid.querySelectorAll('.explore-card.is-hidden');
            let revealed = 0;

            hiddenCards.forEach(card => {
                if (revealed < CARDS_PER_CLICK) {
                    card.classList.remove('is-hidden');
                    revealed++;
                }
            });

            if (!exploreGrid.querySelector('.explore-card.is-hidden')) {
                exploreLoadMore.style.display = 'none';
            }
        });
    }

});

// ==========================
// Comments – "Load more" (3 at a time)
// ==========================
document.addEventListener("DOMContentLoaded", function () {
    const list = document.getElementById("commentsList");
    const btn = document.getElementById("loadMoreComments");

    if (!list || !btn) return;

    btn.addEventListener("click", function () {
        const hidden = list.querySelectorAll(".is-hidden");
        let shown = 0;

        hidden.forEach(item => {
            if (shown < 3) {
                item.classList.remove("is-hidden");
                shown++;
            }
         });

        // Hide button if done
        if (list.querySelectorAll(".is-hidden").length === 0) {
            btn.style.display = "none";
        }
    });

});

// ==========================
// Admin – Manage Users & Clubs
// (each click shows ~2 rows = 6 cards)
// ==========================
document.addEventListener("DOMContentLoaded", () => {
    // --- Manage Users ---
    const usersGrid  = document.getElementById("adminUsersGrid");
    const usersBtn   = document.getElementById("adminUsersLoadMore");
    const usersToggle = document.getElementById("adminUsersFilterToggle");
    const usersPanel  = document.getElementById("adminUsersFilterPanel");

    if (usersToggle && usersPanel) {
        usersToggle.addEventListener("click", () => {
            usersPanel.classList.toggle("is-open");
        });
    }

    if (usersGrid && usersBtn) {
        const CARDS_PER_CLICK = 6;

        usersBtn.addEventListener("click", () => {
            const hidden = usersGrid.querySelectorAll(".admin-users-card-hidden");
            let shown = 0;

            hidden.forEach(card => {
                if (shown < CARDS_PER_CLICK) {
                    card.classList.remove("admin-users-card-hidden");
                    shown++;
                }
            });

            if (!usersGrid.querySelector(".admin-users-card-hidden")) {
                usersBtn.style.display = "none";
            }
        });
    }

    // --- Manage Clubs ---
    const clubsGrid  = document.getElementById("adminClubsGrid");
    const clubsBtn   = document.getElementById("adminClubsLoadMore");
    const clubsToggle = document.getElementById("adminClubsFilterToggle");
    const clubsPanel  = document.getElementById("adminClubsFilterPanel");

    if (clubsToggle && clubsPanel) {
        clubsToggle.addEventListener("click", () => {
            clubsPanel.classList.toggle("is-open");
        });
    }

    if (clubsGrid && clubsBtn) {
        const CARDS_PER_CLICK_CLUBS = 6;

        clubsBtn.addEventListener("click", () => {
            const hidden = clubsGrid.querySelectorAll(".admin-clubs-card-hidden");
            let shown = 0;

            hidden.forEach(card => {
                if (shown < CARDS_PER_CLICK_CLUBS) {
                    card.classList.remove("admin-clubs-card-hidden");
                    shown++;
                }
            });

            if (!clubsGrid.querySelector(".admin-clubs-card-hidden")) {
                clubsBtn.style.display = "none";
            }
        });
    }
}

);

// ========== Mobile Navigation Toggle ==========
const navToggle = document.getElementById('navToggle');
const navLinks = document.getElementById('navLinks');

if (navToggle && navLinks) {
    navToggle.addEventListener('click', () => {
        navLinks.classList.toggle('active');
        const icon = navToggle.querySelector('i');
        if (navLinks.classList.contains('active')) {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
        } else {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
    });

    // Close menu when clicking a link
    navLinks.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            navLinks.classList.remove('active');
            const icon = navToggle.querySelector('i');
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        });
    });
}

// ========== Navbar Scroll Effect ==========
const navbar = document.querySelector('.navbar');

window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

// ========== Products Filter ==========
const filterButtons = document.querySelectorAll('.filter-btn');
const productCards = document.querySelectorAll('.product-card-full');

filterButtons.forEach(button => {
    button.addEventListener('click', () => {
        // Update active button
        filterButtons.forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');

        const filter = button.getAttribute('data-filter');

        productCards.forEach(card => {
            if (filter === 'all' || card.getAttribute('data-category') === filter) {
                card.classList.remove('hidden');
                card.style.animation = 'fadeIn 0.5s ease';
            } else {
                card.classList.add('hidden');
            }
        });
    });
});

// ========== Scroll Animations ==========
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

// Apply animation to elements
document.addEventListener('DOMContentLoaded', () => {
    const animatedElements = document.querySelectorAll(
        '.feature-card, .price-card, .product-card, .product-card-full, .vm-card, .value-card, .stat-item, .info-card'
    );

    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
});

// ========== Fade In Animation Keyframe ==========
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
`;
document.head.appendChild(style);

// ========== Live Gold Prices Auto-Refresh ==========
(function () {
    const section = document.getElementById('goldPrices');
    if (!section) return;

    const endpoint = section.getAttribute('data-endpoint') || 'api/prices.php';
    const refreshMs = parseInt(section.getAttribute('data-refresh-ms'), 10) || 5 * 60 * 1000;

    const formatter = new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 });

    function pad(n) { return n < 10 ? '0' + n : '' + n; }

    function formatTimestamp(unixSeconds) {
        if (!unixSeconds) return '';
        const d = new Date(unixSeconds * 1000);
        return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate())
            + ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes());
    }

    function applyPrices(data) {
        const mapping = { 24: data.karat24, 21: data.karat21, 18: data.karat18 };
        Object.keys(mapping).forEach(function (karat) {
            const value = mapping[karat];
            if (typeof value !== 'number' || isNaN(value)) return;
            const el = section.querySelector('.price-value[data-karat="' + karat + '"]');
            if (!el) return;
            const newText = formatter.format(value) + ' ج.م';
            if (el.textContent.trim() !== newText) {
                el.textContent = newText;
                el.classList.remove('price-flash');
                // Force reflow so the animation restarts
                void el.offsetWidth;
                el.classList.add('price-flash');
            }
        });

        const updatedEl = section.querySelector('.prices-updated');
        if (updatedEl && data.updated_at) {
            updatedEl.setAttribute('data-updated-at', String(data.updated_at));
            updatedEl.textContent = formatTimestamp(data.updated_at);
        }
    }

    function refresh() {
        fetch(endpoint, { cache: 'no-store', headers: { 'Accept': 'application/json' } })
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(applyPrices)
            .catch(function () { /* silently ignore; retry next tick */ });
    }

    // Refresh on a timer, and also when the tab becomes visible again
    setInterval(refresh, refreshMs);
    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible') refresh();
    });
})();

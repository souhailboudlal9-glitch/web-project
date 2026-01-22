/**
 * Main JavaScript - Core functionality
 */

let currentCurrency = 'DH';

const $ = (sel) => document.querySelector(sel);
const $$ = (sel) => document.querySelectorAll(sel);

const navbar = $('#navbar');
const mobileToggle = $('#mobileToggle');
const navMenu = $('#navMenu');
const bookingModal = $('#bookingModal');
const carDetailModal = $('#carDetailModal');

// Navigation scroll effect
window.addEventListener('scroll', () => {
    navbar?.classList.toggle('scrolled', window.scrollY > 100);
});

// Mobile menu toggle
mobileToggle?.addEventListener('click', () => {
    navMenu.classList.toggle('active');
    const icon = mobileToggle.querySelector('i');
    icon.classList.toggle('fa-bars');
    icon.classList.toggle('fa-times');
});

// Close mobile menu on link click
$$('.nav-link').forEach(link => {
    link.addEventListener('click', () => {
        navMenu?.classList.remove('active');
        mobileToggle?.querySelector('i')?.classList.replace('fa-times', 'fa-bars');
    });
});

// Active nav link on scroll
const sections = $$('section[id]');
window.addEventListener('scroll', () => {
    let current = '';
    sections.forEach(section => {
        if (window.scrollY >= section.offsetTop - 200) {
            current = section.id;
        }
    });
    $$('.nav-link').forEach(link => {
        link.classList.toggle('active', link.getAttribute('href') === `#${current}`);
    });
});

// Currency Switcher
$$('.currency-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        $$('.currency-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentCurrency = btn.dataset.currency;

        const currencyInput = $('#selectedCurrency');
        if (currencyInput) currencyInput.value = currentCurrency;

        updatePrices(currentCurrency);
    });
});

function updatePrices(currency) {
    const symbols = { DH: 'DH', EUR: '€', USD: '$' };
    $$('.price-amount').forEach(el => {
        const price = el.dataset[`price${currency.charAt(0) + currency.slice(1).toLowerCase()}`];
        if (price) el.textContent = `${price} ${symbols[currency]}`;
    });
}

// Modal Management
function openModal(modal) {
    modal?.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal(modal) {
    modal?.classList.remove('active');
    document.body.style.overflow = '';
}

$('#openBookingModal')?.addEventListener('click', () => openModal(bookingModal));
$('#closeModal')?.addEventListener('click', () => closeModal(bookingModal));
$('#modalOverlay')?.addEventListener('click', () => closeModal(bookingModal));
$('#closeCarDetail')?.addEventListener('click', () => closeModal(carDetailModal));
$('#carDetailOverlay')?.addEventListener('click', () => closeModal(carDetailModal));

// Booking Form
const bookingForm = $('#bookingForm');
if (bookingForm) {
    const today = new Date().toISOString().split('T')[0];
    const datePriseInput = bookingForm.querySelector('[name="date_prise"]');
    const dateRetourInput = bookingForm.querySelector('[name="date_retour"]');

    datePriseInput?.setAttribute('min', today);
    datePriseInput?.addEventListener('change', () => {
        dateRetourInput?.setAttribute('min', datePriseInput.value);
    });

    bookingForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(bookingForm));
        if (!data.car_id) data.car_id = null;

        if (new Date(data.date_retour) <= new Date(data.date_prise)) {
            showNotification('La date de retour doit être après la date de prise', 'error');
            return;
        }

        const submitBtn = bookingForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi...';
        submitBtn.disabled = true;

        try {
            const res = await fetch('api/submit_booking.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await res.json();

            if (result.success) {
                showNotification('Réservation enregistrée!', 'success');
                bookingForm.reset();
                closeModal(bookingModal);
            } else {
                showNotification(result.message || 'Erreur', 'error');
            }
        } catch {
            showNotification('Erreur de connexion', 'error');
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });
}

// Contact Form
$('#contactForm')?.addEventListener('submit', (e) => {
    e.preventDefault();
    showNotification('Message envoyé!', 'success');
    e.target.reset();
});

// Notifications
function showNotification(message, type = 'info') {
    $('.notification')?.remove();

    const colors = { success: '#10b981', error: '#ef4444', info: '#3b82f6' };
    const icons = { success: 'check-circle', error: 'exclamation-circle', info: 'info-circle' };

    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.innerHTML = `<div class="notification-content"><i class="fas fa-${icons[type]}"></i><span>${message}</span></div>`;
    notification.style.cssText = `
        position:fixed; top:100px; right:20px; background:${colors[type]}; color:white;
        padding:1rem 1.5rem; border-radius:10px; box-shadow:0 10px 30px rgba(0,0,0,0.3);
        z-index:3000; animation:slideInRight 0.3s ease-out; max-width:400px;
    `;

    document.body.appendChild(notification);
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Notification animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight { from { transform:translateX(400px); opacity:0; } to { transform:translateX(0); opacity:1; } }
    @keyframes slideOutRight { from { transform:translateX(0); opacity:1; } to { transform:translateX(400px); opacity:0; } }
    .notification-content { display:flex; align-items:center; gap:1rem; }
    .notification-content i { font-size:1.5rem; }
`;
document.head.appendChild(style);

// Smooth Scroll
$$('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href !== '#' && href.length > 1) {
            e.preventDefault();
            const target = $(href);
            target && window.scrollTo({ top: target.offsetTop - 80, behavior: 'smooth' });
        }
    });
});

console.log('Luxe Drive - Website loaded');

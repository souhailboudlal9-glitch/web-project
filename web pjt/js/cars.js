/**
 * Cars JavaScript - Car listing and details
 */

const SYMBOLS = { DH: 'DH', EUR: '€', USD: '$' };

// Get current price based on currency
function getCurrentPrice(car) {
    const prices = { DH: car.prix_jour_dh, EUR: car.prix_jour_eur, USD: car.prix_jour_usd };
    // currentCurrency global variable from main.js
    return { amount: prices[currentCurrency] || prices.DH, symbol: SYMBOLS[currentCurrency] || 'DH' };
}

// Create car card HTML
function createCarCard(car) {
    const { amount, symbol } = getCurrentPrice(car);
    return `
        <div class="car-card" data-car-id="${car.id}">
            <div class="car-image">
                <img src="${car.image_url}" alt="${car.nom}" onerror="this.src='images/placeholder-car.jpg'">
                ${car.featured ? '<div class="car-badge">Premium</div>' : ''}
            </div>
            <div class="car-info">
                <div class="car-category">${car.categorie}</div>
                <h3 class="car-name">${car.nom}</h3>
                <div class="car-specs">
                    <div class="car-spec"><i class="fas fa-cog"></i><span>${car.transmission}</span></div>
                    <div class="car-spec"><i class="fas fa-gas-pump"></i><span>${car.carburant}</span></div>
                    <div class="car-spec"><i class="fas fa-users"></i><span>${car.places} places</span></div>
                </div>
                <div class="car-footer">
                    <div class="car-price">
                        <div class="price-amount" data-price-dh="${car.prix_jour_dh}" 
                             data-price-eur="${car.prix_jour_eur}" data-price-usd="${car.prix_jour_usd}">
                            ${amount} ${symbol}
                        </div>
                        <div class="price-period">/ jour</div>
                    </div>
                    <button class="car-btn" onclick="event.stopPropagation(); window.openBookingForCar(${car.id})">Réserver</button>
                </div>
            </div>
        </div>`;
}

// Add click handlers to car cards
function addCardClickHandlers() {
    document.querySelectorAll('.car-card').forEach(card => {
        card.addEventListener('click', () => loadCarDetails(card.dataset.carId));
    });
}

// Load featured cars
async function loadFeaturedCars() {
    const grid = document.querySelector('#featuredCarsGrid');
    if (!grid) return;

    try {
        const res = await fetch('api/get_cars.php?featured=true&limit=6');
        const { success, data } = await res.json();
        grid.innerHTML = success && data.length
            ? data.map(createCarCard).join('')
            : '<p class="text-center">Aucune voiture disponible.</p>';
        addCardClickHandlers();
    } catch (e) {
        console.error("Error loading featured cars:", e);
        grid.innerHTML = '<p class="text-center">Erreur de chargement.</p>';
    }
}

// Load all cars with filters
async function loadAllCars(filters = {}) {
    const grid = document.querySelector('#carsGrid');
    if (!grid) {
        // Fallback for collection page specific grid ID if needed, 
        // assuming collection.php uses #carsCollectionGrid based on previous reads
        const collectionGrid = document.querySelector('#carsCollectionGrid');
        if (collectionGrid) {
            loadCollectionCars(collectionGrid, filters);
        }
        return;
    }

    const params = new URLSearchParams(filters).toString();
    try {
        const res = await fetch(`api/get_cars.php?${params}`);
        const { success, data } = await res.json();
        grid.innerHTML = success && data.length
            ? data.map(createCarCard).join('')
            : '<p class="text-center">Aucune voiture trouvée.</p>';
        addCardClickHandlers();
    } catch {
        grid.innerHTML = '<p class="text-center">Erreur de chargement.</p>';
    }
}

// Special handler for collection page grid
async function loadCollectionCars(grid, filters = {}) {
    // This function seems to be missing in original, adapting logic
    // But collection.php renders server-side mostly. 
    // If this is client-side search, we need it.
    // For now, let's just stick to fixing the existing functions.
}


// Load car details
async function loadCarDetails(carId) {
    const modal = document.querySelector('#carDetailModal');
    const content = document.querySelector('#carDetailContent');
    if (!modal || !content) return;

    content.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i><p>Chargement...</p></div>';
    if (typeof openModal === 'function') openModal(modal);

    try {
        const res = await fetch(`api/get_car_details.php?id=${carId}`);
        const { success, data: car, similar } = await res.json();

        if (!success) {
            content.innerHTML = '<p class="text-center">Erreur de chargement.</p>';
            return;
        }

        const { amount, symbol } = getCurrentPrice(car);
        content.innerHTML = `
            <div class="car-detail">
                <div class="car-detail-image">
                    <img src="${car.image_url}" alt="${car.nom}" onerror="this.src='images/placeholder-car.jpg'">
                </div>
                <div class="car-detail-info">
                    <div class="car-category">${car.categorie}</div>
                    <h2 class="car-name">${car.nom}</h2>
                    <p class="car-description">${car.description || 'Véhicule premium pour une expérience exceptionnelle.'}</p>
                    <div class="car-specs-detailed">
                        ${[
                ['cog', 'Transmission', car.transmission],
                ['gas-pump', 'Carburant', car.carburant],
                ['users', 'Places', car.places + ' passagers'],
                ['door-closed', 'Portes', car.portes],
                ['shield-alt', 'Assurance', car.assurance],
                ['calendar', 'Année', car.annee]
            ].map(([icon, label, value]) => `
                            <div class="spec-item">
                                <i class="fas fa-${icon}"></i>
                                <div><strong>${label}</strong><span>${value}</span></div>
                            </div>
                        `).join('')}
                    </div>
                    <div class="car-detail-price">
                        <div class="price-label">Prix par jour</div>
                        <div class="price-amount-large" data-price-dh="${car.prix_jour_dh}" 
                             data-price-eur="${car.prix_jour_eur}" data-price-usd="${car.prix_jour_usd}">
                            ${amount} ${symbol}
                        </div>
                    </div>
                    <button class="btn btn-primary btn-block" onclick="window.openBookingForCar(${car.id})">
                        <i class="fas fa-calendar-check"></i> Réserver Maintenant
                    </button>
                </div>
            </div>
            ${similar?.length ? `
                <div class="similar-cars">
                    <h3>Véhicules Similaires</h3>
                    <div class="similar-cars-grid">
                        ${similar.map(c => `
                            <div class="similar-car-card" onclick="loadCarDetails(${c.id})">
                                <img src="${c.image_url}" alt="${c.nom}" onerror="this.src='images/placeholder-car.jpg'">
                                <h4>${c.nom}</h4>
                                <p>${getCurrentPrice(c).amount} ${getCurrentPrice(c).symbol} / jour</p>
                            </div>
                        `).join('')}
                    </div>
                </div>
            ` : ''}`;
        addCarDetailStyles();
    } catch (e) {
        console.error(e);
        content.innerHTML = '<p class="text-center">Erreur de connexion.</p>';
    }
}

// Open booking for specific car
function openBookingForCar(carId) {
    if (typeof closeModal === 'function') closeModal(document.querySelector('#carDetailModal'));
    const input = document.querySelector('#selectedCarId');
    if (input) input.value = carId;

    // Also update modal title or info if needed, but basic ID is enough
    if (typeof openModal === 'function') openModal(document.querySelector('#bookingModal'));
}
// Expose to window to ensure HTML onclick attributes can find it
window.openBookingForCar = openBookingForCar;
window.loadCarDetails = loadCarDetails;

// Add car detail styles (once)
function addCarDetailStyles() {
    if (document.querySelector('#carDetailStyles')) return;
    const style = document.createElement('style');
    style.id = 'carDetailStyles';
    style.textContent = `
        .car-detail { display:grid; grid-template-columns:1fr 1fr; gap:3rem; margin-bottom:3rem; }
        .car-detail-image img { width:100%; border-radius:15px; }
        .car-specs-detailed { display:grid; grid-template-columns:repeat(2,1fr); gap:1.5rem; margin:2rem 0; }
        .spec-item { display:flex; gap:1rem; align-items:start; }
        .spec-item i { color:var(--gold); font-size:1.5rem; margin-top:0.2rem; }
        .spec-item strong { display:block; color:var(--charcoal); margin-bottom:0.2rem; }
        .spec-item span { color:var(--text-light); }
        .car-detail-price { background:var(--off-white); padding:1.5rem; border-radius:15px; text-align:center; margin:2rem 0; }
        .price-label { color:var(--text-light); margin-bottom:0.5rem; }
        .price-amount-large { font-family:var(--font-heading); font-size:3rem; color:var(--gold); font-weight:700; }
        .similar-cars { margin-top:3rem; padding-top:3rem; border-top:2px solid var(--border-color); }
        .similar-cars h3 { font-family:var(--font-heading); font-size:2rem; margin-bottom:2rem; color:var(--charcoal); }
        .similar-cars-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1.5rem; }
        .similar-car-card { cursor:pointer; border-radius:15px; overflow:hidden; transition:var(--transition); background:var(--white); box-shadow:0 4px 15px rgba(0,0,0,0.08); }
        .similar-car-card:hover { transform:translateY(-5px); box-shadow:0 8px 25px rgba(0,0,0,0.15); }
        .similar-car-card img { width:100%; height:150px; object-fit:cover; }
        .similar-car-card h4 { padding:1rem; font-size:1.1rem; color:var(--charcoal); }
        .similar-car-card p { padding:0 1rem 1rem; color:var(--gold); font-weight:600; }
        @media(max-width:768px) {
            .car-detail { grid-template-columns:1fr; gap:2rem; }
            .car-specs-detailed, .similar-cars-grid { grid-template-columns:1fr; }
        }`;
    document.head.appendChild(style);
}

// Load filters
async function loadFilters() {
    try {
        const res = await fetch('api/get_filters.php');
        const { success, data } = await res.json();
        if (success) populateFilterDropdowns(data);
    } catch { }
}

function populateFilterDropdowns({ categories, brands }) {
    const catSel = document.querySelector('#categoryFilter');
    const brandSel = document.querySelector('#brandFilter');
    if (catSel && categories) {
        catSel.innerHTML = '<option value="">Toutes les catégories</option>' +
            categories.map(c => `<option value="${c.id}">${c.nom}</option>`).join('');
    }
    if (brandSel && brands) {
        brandSel.innerHTML = '<option value="">Toutes les marques</option>' +
            brands.map(b => `<option value="${b.id}">${b.nom}</option>`).join('');
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('#featuredCarsGrid')) loadFeaturedCars();
    if (document.querySelector('#carsCollectionGrid')) {
        // Collection grid handled by PHP mostly but if JS needed:
        addCardClickHandlers();
    }
});




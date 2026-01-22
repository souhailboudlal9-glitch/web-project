<?php
/**
 * Page d'accueil - Version PHP avec gestion backend
 * Charge les données depuis la base de données côté serveur
 */

require_once 'config/db.php';

// Récupérer les voitures en vedette
try {
    $featuredCars = fetchAll($pdo, "SELECT 
                                        c.id,
                                        c.nom,
                                        b.nom AS marque,
                                        cat.nom AS categorie,
                                        c.description,
                                        c.image_url,
                                        c.prix_jour_dh,
                                        c.prix_jour_eur,
                                        c.prix_jour_usd,
                                        c.transmission,
                                        c.carburant,
                                        c.places,
                                        c.portes,
                                        c.featured
                                    FROM cars c
                                    JOIN brands b ON c.brand_id = b.id
                                    JOIN categories cat ON c.category_id = cat.id
                                    WHERE c.disponible = TRUE AND c.featured = TRUE
                                    ORDER BY c.created_at DESC
                                    LIMIT 6");
} catch (Exception $e) {
    error_log("Erreur chargement voitures: " . $e->getMessage());
    $featuredCars = [];
}

// Récupérer les statistiques
try {
    $stats = [
        'total_cars' => fetchOne($pdo, "SELECT COUNT(*) as count FROM cars WHERE disponible = TRUE")['count'],
        'total_bookings' => fetchOne($pdo, "SELECT COUNT(*) as count FROM bookings")['count'],
        'categories' => fetchAll($pdo, "SELECT id, nom FROM categories ORDER BY nom"),
        'brands' => fetchAll($pdo, "SELECT id, nom FROM brands ORDER BY nom")
    ];
} catch (Exception $e) {
    $stats = ['total_cars' => 0, 'total_bookings' => 0, 'categories' => [], 'brands' => []];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Location de voitures de luxe - Flotte premium et exclusive avec <?php echo $stats['total_cars']; ?> véhicules disponibles">
    <meta name="keywords" content="location voiture luxe, Mercedes, BMW, Range Rover, Marrakech, voiture premium">
    <title>Location Voiture Luxe | Flotte Premium - <?php echo $stats['total_cars']; ?> Véhicules</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Stylesheet -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="container">
            <div class="nav-wrapper">
                <div class="logo">
                    <h1><i class="fas fa-crown"></i> Luxe Drive</h1>
                </div>
                
                <div class="nav-menu" id="navMenu">
                    <a href="#accueil" class="nav-link active">Accueil</a>
                    <a href="#services" class="nav-link">Nos Services</a>
                    <a href="#collection" class="nav-link">La Collection</a>
                    <a href="#apropos" class="nav-link">À Propos</a>
                    <a href="#contact" class="nav-link">Contact</a>
                </div>
                
                <div class="nav-actions">
                    <div class="currency-switcher">
                        <button class="currency-btn active" data-currency="DH">DH</button>
                        <button class="currency-btn" data-currency="EUR">€</button>
                        <button class="currency-btn" data-currency="USD">$</button>
                    </div>
                    <button class="mobile-toggle" id="mobileToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="accueil">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="container">
                <h2 class="hero-subtitle">Expérience Premium</h2>
                <h1 class="hero-title">Flotte Neuve et Exclusive</h1>
                <p class="hero-description">Découvrez notre collection de <?php echo $stats['total_cars']; ?> véhicules de luxe pour une expérience de conduite inoubliable</p>
                <button class="btn btn-primary btn-large" id="openBookingModal">
                    <i class="fas fa-map-marker-alt"></i> Lieu de Départ
                </button>
            </div>
        </div>
        <div class="scroll-indicator">
            <i class="fas fa-chevron-down"></i>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services" id="services">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Nos Services</h2>
                <p class="section-subtitle">Une expérience sur mesure pour chaque client</p>
            </div>
            
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-car-side"></i>
                    </div>
                    <h3>Flotte Premium</h3>
                    <p>Véhicules de luxe récents et parfaitement entretenus</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Assurance Tout Risque</h3>
                    <p>Protection complète incluse dans tous nos tarifs</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3>Livraison Gratuite</h3>
                    <p>Nous livrons votre véhicule où vous le souhaitez</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h3>Chauffeur Privé</h3>
                    <p>Service de chauffeur professionnel disponible</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3>Support 24/7</h3>
                    <p>Assistance disponible à tout moment</p>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-percent"></i>
                    </div>
                    <h3>Tarifs Flexibles</h3>
                    <p>Options de location adaptées à vos besoins</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Cars Section -->
    <section class="featured-cars" id="collection">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Véhicules en Vedette</h2>
                <p class="section-subtitle">Découvrez notre sélection premium</p>
            </div>
            
            <div class="cars-grid" id="featuredCarsGrid">
                <?php if (count($featuredCars) > 0): ?>
                    <?php foreach ($featuredCars as $car): ?>
                        <div class="car-card" data-car-id="<?php echo $car['id']; ?>" onclick="loadCarDetails(<?php echo $car['id']; ?>)">
                            <div class="car-image">
                                <img src="<?php echo escape($car['image_url']); ?>" 
                                     alt="<?php echo escape($car['nom']); ?>" 
                                     onerror="this.src='images/placeholder-car.jpg'">
                                <?php if ($car['featured']): ?>
                                    <div class="car-badge">Premium</div>
                                <?php endif; ?>
                            </div>
                            <div class="car-info">
                                <div class="car-category"><?php echo escape($car['categorie']); ?></div>
                                <h3 class="car-name"><?php echo escape($car['nom']); ?></h3>
                                <div class="car-specs">
                                    <div class="car-spec">
                                        <i class="fas fa-cog"></i>
                                        <span><?php echo escape($car['transmission']); ?></span>
                                    </div>
                                    <div class="car-spec">
                                        <i class="fas fa-gas-pump"></i>
                                        <span><?php echo escape($car['carburant']); ?></span>
                                    </div>
                                    <div class="car-spec">
                                        <i class="fas fa-users"></i>
                                        <span><?php echo $car['places']; ?> places</span>
                                    </div>
                                </div>
                                <div class="car-footer">
                                    <div class="car-price">
                                        <div class="price-amount" 
                                             data-price-dh="<?php echo $car['prix_jour_dh']; ?>" 
                                             data-price-eur="<?php echo $car['prix_jour_eur']; ?>" 
                                             data-price-usd="<?php echo $car['prix_jour_usd']; ?>">
                                            <?php echo number_format($car['prix_jour_dh'], 0); ?> DH
                                        </div>
                                        <div class="price-period">/ jour</div>
                                    </div>
                                    <button class="car-btn" onclick="event.stopPropagation(); openBookingForCar(<?php echo $car['id']; ?>)">
                                        Réserver
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="loading">
                        <p>Aucune voiture disponible pour le moment.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="text-center" style="margin-top: 3rem;">
                <a href="collection.php" class="btn btn-outline">
                    Voir Toute la Collection <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="why-choose" id="apropos">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Pourquoi Nous Choisir?</h2>
                <p class="section-subtitle">L'excellence à chaque étape de votre expérience</p>
            </div>
            
            <div class="why-grid">
                <div class="why-card">
                    <div class="why-number">01</div>
                    <h3>Qualité Garantie</h3>
                    <p>Tous nos véhicules sont régulièrement entretenus et vérifiés pour garantir votre sécurité et votre confort.</p>
                </div>
                
                <div class="why-card">
                    <div class="why-number">02</div>
                    <h3>Flotte Récente</h3>
                    <p>Véhicules de moins de 2 ans avec les dernières technologies et équipements de sécurité.</p>
                </div>
                
                <div class="why-card">
                    <div class="why-number">03</div>
                    <h3>Service Client</h3>
                    <p>Une équipe dédiée à votre écoute pour répondre à tous vos besoins et questions.</p>
                </div>
                
                <div class="why-card">
                    <div class="why-number">04</div>
                    <h3>Transparence</h3>
                    <p>Tarifs clairs sans frais cachés. Ce que vous voyez est ce que vous payez.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact" id="contact">
        <div class="container">
            <div class="contact-wrapper">
                <div class="contact-info">
                    <h2>Contactez-Nous</h2>
                    <p>Notre équipe est à votre disposition pour toute question</p>
                    
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <h4>Téléphone</h4>
                            <a href="tel:+212650359460">+212 6 50 35 94 60</a>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <h4>Email</h4>
                            <a href="mailto:souhailboudlal9@gmail.com">souhailboudlal9@gmail.com</a>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <h4>Adresse</h4>
                            <p>Marrakech, Maroc</p>
                        </div>
                    </div>
                    
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                
                <div class="contact-form-wrapper">
                    <form class="contact-form" id="contactForm" method="POST" action="api/contact.php">
                        <div class="form-group">
                            <input type="text" name="nom" placeholder="Votre nom" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" placeholder="Votre email" required>
                        </div>
                        <div class="form-group">
                            <input type="tel" name="telephone" placeholder="Votre téléphone" required>
                        </div>
                        <div class="form-group">
                            <textarea name="message" placeholder="Votre message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            Envoyer le Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-col">
                    <h3><i class="fas fa-crown"></i> Luxe Drive</h3>
                    <p>Votre partenaire de confiance pour la location de véhicules de luxe.</p>
                    <p style="margin-top: 1rem; color: var(--gold); font-size: 0.9rem;">
                        <i class="fas fa-car"></i> <?php echo $stats['total_cars']; ?> véhicules disponibles
                    </p>
                </div>
                
                <div class="footer-col">
                    <h4>Liens Rapides</h4>
                    <ul>
                        <li><a href="#accueil">Accueil</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#collection">Collection</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="#">Location Courte Durée</a></li>
                        <li><a href="#">Location Longue Durée</a></li>
                        <li><a href="#">Chauffeur Privé</a></li>
                        <li><a href="#">Transfert Aéroport</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>Newsletter</h4>
                    <p>Recevez nos offres exclusives</p>
                    <form class="newsletter-form" method="POST" action="api/newsletter.php">
                        <input type="email" name="email" placeholder="Votre email" required>
                        <button type="submit"><i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Luxe Drive. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- Booking Modal -->
    <div class="modal" id="bookingModal">
        <div class="modal-overlay" id="modalOverlay"></div>
        <div class="modal-content">
            <button class="modal-close" id="closeModal">
                <i class="fas fa-times"></i>
            </button>
            
            <h2 class="modal-title">Réserver Votre Véhicule</h2>
            
            <form class="booking-form" id="bookingForm">
                <div class="form-row">
                    <div class="form-group">
                        <label>Lieu de Prise en Charge</label>
                        <select name="lieu_prise" required>
                            <option value="">Sélectionnez un lieu</option>
                            <option value="Aéroport Marrakech Menara">Aéroport Marrakech Menara</option>
                            <option value="Centre-ville Marrakech">Centre-ville Marrakech</option>
                            <option value="Gare Marrakech">Gare Marrakech</option>
                            <option value="Hôtel (précisez dans message)">Hôtel (précisez dans message)</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Date de Prise en Charge</label>
                        <input type="date" name="date_prise" required>
                    </div>
                    <div class="form-group">
                        <label>Heure</label>
                        <input type="time" name="heure_prise" value="10:00" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Date de Retour</label>
                        <input type="date" name="date_retour" required>
                    </div>
                    <div class="form-group">
                        <label>Heure</label>
                        <input type="time" name="heure_retour" value="10:00" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Nom Complet</label>
                        <input type="text" name="nom_client" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email_client" required>
                    </div>
                    <div class="form-group">
                        <label>Téléphone</label>
                        <input type="tel" name="telephone_client" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Message (optionnel)</label>
                        <textarea name="message" rows="3"></textarea>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Chauffeur Privé</label>
                    <div class="driver-options" style="display: flex; gap: 1.5rem; margin-top: 0.5rem;">
                        <label class="driver-option" style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 1rem; border: 2px solid var(--border-color); border-radius: 10px; flex: 1; transition: all 0.3s;">
                            <input type="radio" name="chauffeur_prive" value="0" checked style="accent-color: var(--gold);">
                            <i class="fas fa-car" style="color: var(--gold); font-size: 1.2rem;"></i>
                            <span>Sans chauffeur</span>
                        </label>
                        <label class="driver-option" style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 1rem; border: 2px solid var(--border-color); border-radius: 10px; flex: 1; transition: all 0.3s;">
                            <input type="radio" name="chauffeur_prive" value="1" style="accent-color: var(--gold);">
                            <i class="fas fa-user-tie" style="color: var(--gold); font-size: 1.2rem;"></i>
                            <span>Avec chauffeur</span>
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Mode de Paiement</label>
                    <div class="payment-options" style="display: flex; gap: 1.5rem; margin-top: 0.5rem;">
                        <label class="payment-option" style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 1rem; border: 2px solid var(--border-color); border-radius: 10px; flex: 1; transition: all 0.3s;">
                            <input type="radio" name="mode_paiement" value="sur_place" checked style="accent-color: var(--gold);">
                            <i class="fas fa-hand-holding-usd" style="color: var(--gold); font-size: 1.2rem;"></i>
                            <span>Paiement sur place</span>
                        </label>
                        <label class="payment-option" style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 1rem; border: 2px solid var(--border-color); border-radius: 10px; flex: 1; transition: all 0.3s;">
                            <input type="radio" name="mode_paiement" value="carte" style="accent-color: var(--gold);">
                            <i class="fas fa-credit-card" style="color: var(--gold); font-size: 1.2rem;"></i>
                            <span>Paiement par carte</span>
                        </label>
                    </div>
                </div>
                
                <input type="hidden" name="car_id" id="selectedCarId">
                <input type="hidden" name="devise" id="selectedCurrency" value="DH">
                
                <button type="submit" class="btn btn-primary btn-block">
                    Confirmer la Réservation
                </button>
            </form>
        </div>
    </div>

    <!-- Car Detail Modal -->
    <div class="modal" id="carDetailModal">
        <div class="modal-overlay" id="carDetailOverlay"></div>
        <div class="modal-content modal-large">
            <button class="modal-close" id="closeCarDetail">
                <i class="fas fa-times"></i>
            </button>
            
            <div id="carDetailContent">
                <!-- Le contenu sera chargé dynamiquement -->
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="js/main.js?v=<?php echo time(); ?>"></script>
    <script src="js/cars.js?v=<?php echo time(); ?>"></script>
</body>
</html>

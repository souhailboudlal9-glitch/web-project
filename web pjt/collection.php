<?php
/**
 * Page Collection - Version PHP avec gestion backend
 */

require_once 'config/db.php';

// Récupérer les filtres depuis l'URL
$categoryFilter = isset($_GET['category']) ? intval($_GET['category']) : null;
$brandFilter = isset($_GET['brand']) ? intval($_GET['brand']) : null;
$searchFilter = isset($_GET['search']) ? trim($_GET['search']) : null;

// Construire la requête SQL
$sql = "SELECT 
            c.id,
            c.nom,
            b.nom AS marque,
            b.id AS brand_id,
            cat.nom AS categorie,
            cat.id AS category_id,
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
        WHERE c.disponible = TRUE";

$params = [];

if ($categoryFilter) {
    $sql .= " AND c.category_id = ?";
    $params[] = $categoryFilter;
}

if ($brandFilter) {
    $sql .= " AND c.brand_id = ?";
    $params[] = $brandFilter;
}

if ($searchFilter) {
    $sql .= " AND (c.nom LIKE ? OR b.nom LIKE ? OR cat.nom LIKE ?)";
    $searchTerm = "%$searchFilter%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

$sql .= " ORDER BY c.featured DESC, c.created_at DESC";

try {
    $cars = fetchAll($pdo, $sql, $params);
    $categories = fetchAll($pdo, "SELECT * FROM categories ORDER BY nom");
    $brands = fetchAll($pdo, "SELECT * FROM brands ORDER BY nom");
} catch (Exception $e) {
    error_log("Erreur collection: " . $e->getMessage());
    $cars = [];
    $categories = [];
    $brands = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Notre collection complète de véhicules de luxe - <?php echo count($cars); ?> voitures disponibles">
    <title>La Collection | Luxe Drive - <?php echo count($cars); ?> Véhicules</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Stylesheet -->
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        .collection-hero {
            padding: 150px 0 80px;
            background: linear-gradient(135deg, var(--charcoal) 0%, var(--charcoal-light) 100%);
            color: var(--white);
            text-align: center;
        }
        
        .collection-hero h1 {
            font-family: var(--font-heading);
            font-size: 3.5rem;
            margin-bottom: 1rem;
        }
        
        .collection-hero p {
            font-size: 1.2rem;
            color: var(--off-white);
        }
        
        .filters-section {
            background: var(--off-white);
            padding: 2rem 0;
            position: sticky;
            top: 70px;
            z-index: 100;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .filters-wrapper {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 25px;
            font-family: inherit;
            font-size: 1rem;
            transition: var(--transition);
        }
        
        .filter-group select:focus,
        .filter-group input:focus {
            outline: none;
            border-color: var(--gold);
        }
        
        .cars-section {
            padding: 4rem 0;
        }
        
        .results-info {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--text-light);
            font-size: 1.1rem;
        }
        
        .results-info strong {
            color: var(--gold);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="container">
            <div class="nav-wrapper">
                <div class="logo">
                    <a href="index.php">
                        <h1><i class="fas fa-crown"></i> Luxe Drive</h1>
                    </a>
                </div>
                
                <div class="nav-menu" id="navMenu">
                    <a href="index.php#accueil" class="nav-link">Accueil</a>
                    <a href="index.php#services" class="nav-link">Nos Services</a>
                    <a href="collection.php" class="nav-link active">La Collection</a>
                    <a href="index.php#apropos" class="nav-link">À Propos</a>
                    <a href="index.php#contact" class="nav-link">Contact</a>
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

    <!-- Collection Hero -->
    <section class="collection-hero">
        <div class="container">
            <h1>Notre Collection</h1>
            <p>Découvrez notre flotte exclusive de véhicules premium</p>
        </div>
    </section>

    <!-- Filters -->
    <section class="filters-section">
        <div class="container">
            <form method="GET" action="collection.php" class="filters-wrapper">
                <div class="filter-group">
                    <select name="category" id="categoryFilter" onchange="this.form.submit()">
                        <option value="">Toutes les catégories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo $categoryFilter == $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo escape($cat['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <select name="brand" id="brandFilter" onchange="this.form.submit()">
                        <option value="">Toutes les marques</option>
                        <?php foreach ($brands as $brand): ?>
                            <option value="<?php echo $brand['id']; ?>" <?php echo $brandFilter == $brand['id'] ? 'selected' : ''; ?>>
                                <?php echo escape($brand['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <input type="text" 
                           name="search" 
                           id="searchInput" 
                           placeholder="Rechercher un véhicule..." 
                           value="<?php echo escape($searchFilter ?? ''); ?>">
                </div>
                
                <button type="submit" class="btn btn-primary" style="padding: 0.8rem 2rem;">
                    <i class="fas fa-search"></i> Rechercher
                </button>
            </form>
        </div>
    </section>

    <!-- Cars Grid -->
    <section class="cars-section">
        <div class="container">
            <div class="results-info">
                <strong><?php echo count($cars); ?></strong> véhicule<?php echo count($cars) > 1 ? 's' : ''; ?> trouvé<?php echo count($cars) > 1 ? 's' : ''; ?>
            </div>
            
            <div class="cars-grid" id="carsCollectionGrid">
                <?php if (count($cars) > 0): ?>
                    <?php foreach ($cars as $car): ?>
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
                        <p>Aucune voiture trouvée avec ces critères. Essayez de modifier vos filtres.</p>
                    </div>
                <?php endif; ?>
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
                </div>
                
                <div class="footer-col">
                    <h4>Liens Rapides</h4>
                    <ul>
                        <li><a href="index.php">Accueil</a></li>
                        <li><a href="index.php#services">Services</a></li>
                        <li><a href="collection.php">Collection</a></li>
                        <li><a href="index.php#contact">Contact</a></li>
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
                    <h4>Contact</h4>
                    <p>Téléphone: +212 6 00 00 00 00</p>
                    <p>Email: contact@luxedrive.ma</p>
                    <div class="social-links" style="margin-top: 1rem;">
                        <a href="#" class="social-link"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-whatsapp"></i></a>
                    </div>
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

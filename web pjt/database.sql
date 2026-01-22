-- ============================================
-- Base de données pour site de location de voiture
-- Inspiré de Red City Drive
-- ============================================
-- Créer la base de données
CREATE DATABASE IF NOT EXISTS location_voiture CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE location_voiture;
-- ============================================
-- Table: categories
-- ============================================
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP --date et heure automatique --
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
--moteur qui respecte les normes de la base de données--
-- ============================================
-- Table: brands (marques)
-- ============================================
CREATE TABLE IF NOT EXISTS brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    logo_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ============================================
-- Table: cars (voitures)
-- ============================================
CREATE TABLE IF NOT EXISTS cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    brand_id INT NOT NULL,
    category_id INT NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    prix_jour_dh DECIMAL(10, 2) NOT NULL,
    prix_jour_eur DECIMAL(10, 2) NOT NULL,
    prix_jour_usd DECIMAL(10, 2) NOT NULL,
    transmission ENUM('Automatique', 'Manuelle') DEFAULT 'Automatique',
    --valeur controler(il limete les choix possibles)
    carburant ENUM('Diesel', 'Essence', 'Hybride', 'Électrique') DEFAULT 'Diesel',
    places INT DEFAULT 5,
    portes INT DEFAULT 4,
    climatisation BOOLEAN DEFAULT TRUE,
    assurance VARCHAR(100) DEFAULT 'Tout Risque',
    annee INT,
    disponible BOOLEAN DEFAULT TRUE,
    featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    INDEX idx_category (category_id),
    INDEX idx_brand (brand_id),
    INDEX idx_disponible (disponible),
    INDEX idx_featured (featured)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ============================================
-- Table: bookings (réservations)
-- ============================================
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    car_id INT NOT NULL,
    nom_client VARCHAR(150) NOT NULL,
    email_client VARCHAR(150) NOT NULL,
    telephone_client VARCHAR(20) NOT NULL,
    lieu_prise VARCHAR(200) NOT NULL,
    date_prise DATE NOT NULL,
    heure_prise TIME NOT NULL,
    date_retour DATE NOT NULL,
    heure_retour TIME NOT NULL,
    nombre_jours INT NOT NULL,
    prix_total_dh DECIMAL(10, 2) NOT NULL,
    devise VARCHAR(3) DEFAULT 'DH',
    mode_paiement ENUM('sur_place', 'carte') DEFAULT 'sur_place',
    chauffeur_prive BOOLEAN DEFAULT FALSE,
    paiement_confirme BOOLEAN DEFAULT FALSE,
    statut ENUM('En attente', 'Confirmée', 'Annulée', 'Terminée') DEFAULT 'En attente',
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE,
    INDEX idx_car (car_id),
    INDEX idx_dates (date_prise, date_retour),
    INDEX idx_statut (statut),
    INDEX idx_paiement (mode_paiement, paiement_confirme)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- ============================================
-- Insertion des données de base
-- ============================================
-- Catégories
INSERT INTO categories (nom, description)
VALUES (
        'SUV Premium',
        'Véhicules SUV de luxe pour un confort maximal'
    ),
    (
        'Berline Luxe',
        'Berlines haut de gamme pour vos déplacements professionnels'
    ),
    (
        'Citadine Élégante',
        'Voitures compactes et élégantes pour la ville'
    ),
    (
        'Sport & Performance',
        'Véhicules sportifs pour les amateurs de sensations'
    ),
    (
        'Familiale Confort',
        'Véhicules spacieux pour toute la famille'
    );
-- Marques
INSERT INTO brands (nom, logo_url)
VALUES ('Mercedes-Benz', 'images/brands/mercedes.png'),
    ('BMW', 'images/brands/bmw.png'),
    ('Audi', 'images/brands/audi.png'),
    ('Range Rover', 'images/brands/rangerover.png'),
    ('Porsche', 'images/brands/porsche.png'),
    ('Volkswagen', 'images/brands/vw.png'),
    ('Toyota', 'images/brands/toyota.png'),
    ('Peugeot', 'images/brands/peugeot.png');
-- Voitures (flotte premium)
INSERT INTO cars (
        nom,
        brand_id,
        category_id,
        description,
        image_url,
        prix_jour_dh,
        prix_jour_eur,
        prix_jour_usd,
        transmission,
        carburant,
        places,
        portes,
        annee,
        featured
    )
VALUES (
        'Range Rover Evoque',
        4,
        1,
        'SUV compact premium avec design élégant et technologies avancées. Parfait pour explorer Marrakech avec style.',
        'images/cars/range-rover-evoque.jpg',
        1200,
        115,
        125,
        'Automatique',
        'Diesel',
        5,
        5,
        2024,
        TRUE
    ),
    (
        'Mercedes-Benz Classe E',
        1,
        2,
        'Berline de luxe offrant un confort exceptionnel et des performances remarquables. Idéale pour vos déplacements professionnels.',
        'images/cars/mercedes-classe-e.jpg',
        1500,
        145,
        155,
        'Automatique',
        'Diesel',
        5,
        4,
        2024,
        TRUE
    ),
    (
        'BMW X5',
        2,
        1,
        'SUV spacieux et puissant combinant luxe et sportivité. Parfait pour les longs trajets en famille.',
        'images/cars/bmw-x5.jpg',
        1800,
        175,
        185,
        'Automatique',
        'Diesel',
        7,
        5,
        2023,
        TRUE
    ),
    (
        'Audi A6',
        3,
        2,
        'Berline premium alliant technologie de pointe et design raffiné. Confort et élégance garantis.',
        'images/cars/audi-a6.jpg',
        1400,
        135,
        145,
        'Automatique',
        'Diesel',
        5,
        4,
        2024,
        FALSE
    ),
    (
        'Mercedes-Benz GLE',
        1,
        1,
        'SUV de luxe spacieux avec intérieur raffiné et équipements haut de gamme.',
        'images/cars/mercedes-gle.jpg',
        2000,
        195,
        205,
        'Automatique',
        'Diesel',
        7,
        5,
        2024,
        TRUE
    ),
    (
        'Range Rover Sport',
        4,
        1,
        'SUV sportif et luxueux offrant des performances exceptionnelles sur route et hors-route.',
        'images/cars/range-rover-sport.jpg',
        2200,
        215,
        225,
        'Automatique',
        'Diesel',
        5,
        5,
        2023,
        FALSE
    ),
    (
        'BMW Série 5',
        2,
        2,
        'Berline executive combinant dynamisme et confort. Technologies innovantes et finitions premium.',
        'images/cars/bmw-serie-5.jpg',
        1600,
        155,
        165,
        'Automatique',
        'Diesel',
        5,
        4,
        2024,
        FALSE
    ),
    (
        'Audi Q7',
        3,
        1,
        'Grand SUV familial offrant espace, confort et technologies avancées pour 7 passagers.',
        'images/cars/audi-q7.jpg',
        1900,
        185,
        195,
        'Automatique',
        'Diesel',
        7,
        5,
        2023,
        FALSE
    ),
    (
        'Porsche Cayenne',
        5,
        4,
        'SUV sportif de luxe alliant performances exceptionnelles et confort premium.',
        'images/cars/porsche-cayenne.jpg',
        2500,
        245,
        255,
        'Automatique',
        'Essence',
        5,
        5,
        2024,
        TRUE
    ),
    (
        'Mercedes-Benz Classe C',
        1,
        3,
        'Berline compacte premium au design élégant. Parfaite pour la ville avec un style incomparable.',
        'images/cars/mercedes-classe-c.jpg',
        1100,
        105,
        115,
        'Automatique',
        'Diesel',
        5,
        4,
        2024,
        FALSE
    ),
    (
        'BMW X3',
        2,
        1,
        'SUV compact sportif offrant agilité et confort. Idéal pour les escapades urbaines et rurales.',
        'images/cars/bmw-x3.jpg',
        1300,
        125,
        135,
        'Automatique',
        'Diesel',
        5,
        5,
        2023,
        FALSE
    ),
    (
        'Volkswagen Tiguan',
        6,
        5,
        'SUV familial spacieux et confortable avec un excellent rapport qualité-prix.',
        'images/cars/vw-tiguan.jpg',
        800,
        75,
        85,
        'Automatique',
        'Diesel',
        5,
        5,
        2023,
        FALSE
    ),
    (
        'Toyota Land Cruiser',
        7,
        1,
        'SUV robuste et luxueux, parfait pour les aventures tout-terrain avec confort premium.',
        'images/cars/toyota-landcruiser.jpg',
        2100,
        205,
        215,
        'Automatique',
        'Diesel',
        7,
        5,
        2023,
        FALSE
    ),
    (
        'Peugeot 3008',
        8,
        5,
        'SUV compact moderne avec design innovant et technologies avancées. Confort et économie.',
        'images/cars/peugeot-3008.jpg',
        700,
        65,
        75,
        'Automatique',
        'Diesel',
        5,
        5,
        2024,
        FALSE
    );
-- ============================================
-- Exemples de réservations (pour tests)
-- ============================================
INSERT INTO bookings (
        car_id,
        nom_client,
        email_client,
        telephone_client,
        lieu_prise,
        date_prise,
        heure_prise,
        date_retour,
        heure_retour,
        nombre_jours,
        prix_total_dh,
        devise,
        statut,
        message
    )
VALUES (
        1,
        'Ahmed Benali',
        'ahmed.benali@email.com',
        '+212 6 12 34 56 78',
        'Aéroport Marrakech Menara',
        '2026-02-15',
        '10:00:00',
        '2026-02-20',
        '10:00:00',
        5,
        6000,
        'DH',
        'Confirmée',
        'Arrivée vol AT 456'
    ),
    (
        2,
        'Sophie Martin',
        'sophie.martin@email.com',
        '+33 6 12 34 56 78',
        'Hôtel La Mamounia',
        '2026-02-10',
        '14:00:00',
        '2026-02-17',
        '14:00:00',
        7,
        10500,
        'EUR',
        'En attente',
        'Besoin siège bébé'
    ),
    (
        5,
        'Mohamed Alami',
        'mohamed.alami@email.com',
        '+212 6 98 76 54 32',
        'Aéroport Marrakech Menara',
        '2026-03-01',
        '09:00:00',
        '2026-03-10',
        '09:00:00',
        9,
        18000,
        'DH',
        'Confirmée',
        NULL
    );
-- ============================================
-- Vues utiles
-- ============================================
-- Vue pour les voitures disponibles avec détails complets
CREATE OR REPLACE VIEW vue_cars_disponibles AS
SELECT c.id,
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
    c.climatisation,
    c.assurance,
    c.annee,
    c.featured
FROM cars c
    JOIN brands b ON c.brand_id = b.id
    JOIN categories cat ON c.category_id = cat.id
WHERE c.disponible = TRUE
ORDER BY c.featured DESC,
    c.created_at DESC;
-- Vue pour les statistiques de réservations
CREATE OR REPLACE VIEW vue_stats_reservations AS
SELECT c.nom AS voiture,
    COUNT(bk.id) AS nombre_reservations,
    SUM(bk.prix_total_dh) AS revenu_total_dh,
    AVG(bk.nombre_jours) AS duree_moyenne_jours
FROM cars c
    LEFT JOIN bookings bk ON c.id = bk.car_id
GROUP BY c.id,
    c.nom
ORDER BY nombre_reservations DESC;
-- ============================================
-- Fin du script
-- ============================================
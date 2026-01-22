# Site de Location de Voiture Premium - Luxe Drive

Un site web de location de voitures de luxe inspiré de Red City Drive, développé avec HTML, CSS, JavaScript, PHP et MySQL.

## 🚗 Caractéristiques

- **Design Premium**: Interface luxueuse avec palette or/noir
- **Flotte Complète**: Gestion de véhicules de luxe (Mercedes, BMW, Range Rover, etc.)
- **Système de Réservation**: Formulaire complet avec validation
- **Multi-devises**: Support DH, EUR, USD
- **Responsive**: Optimisé pour mobile, tablette et desktop
- **Admin Panel**: Gestion des véhicules et réservations
- **API REST**: Backend PHP avec architecture MVC

## 📋 Prérequis

- **Serveur Web**: Apache (XAMPP, WAMP, MAMP) ou Nginx
- **PHP**: Version 7.4 ou supérieure
- **MySQL**: Version 5.7 ou supérieure
- **Navigateur**: Chrome, Firefox, Safari ou Edge (dernières versions)

## 🛠️ Installation

### 1. Cloner/Copier le projet

Copiez tous les fichiers dans votre répertoire web (par exemple `htdocs` pour XAMPP).

### 2. Configurer la base de données

1. Démarrez votre serveur MySQL
2. Ouvrez phpMyAdmin (http://localhost/phpmyadmin)
3. Importez le fichier `database.sql`:
   - Cliquez sur "Importer"
   - Sélectionnez le fichier `database.sql`
   - Cliquez sur "Exécuter"

### 3. Configurer la connexion

Ouvrez `config/db.php` et modifiez si nécessaire:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'location_voiture');
define('DB_USER', 'root');
define('DB_PASS', ''); // Votre mot de passe MySQL
```

### 4. Lancer le site

1. Démarrez Apache et MySQL
2. Ouvrez votre navigateur
3. Accédez à: `http://localhost/web 2/index.html`

## 📁 Structure du Projet

```
web 2/
├── api/
│   ├── get_cars.php          # API pour récupérer les voitures
│   ├── get_car_details.php   # API pour les détails d'une voiture
│   ├── get_filters.php       # API pour les filtres
│   └── submit_booking.php    # API pour les réservations
├── config/
│   └── db.php                # Configuration base de données
├── css/
│   └── style.css             # Styles premium
├── js/
│   ├── main.js               # JavaScript principal
│   └── cars.js               # Gestion des voitures
├── images/
│   ├── cars/                 # Images des voitures
│   └── hero-bg.jpg           # Image hero
├── admin/
│   └── index.php             # Panel admin (à développer)
├── index.html                # Page d'accueil
├── collection.html           # Page collection
├── database.sql              # Script SQL
└── README.md                 # Ce fichier
```

## 🎨 Personnalisation

### Couleurs

Modifiez les variables CSS dans `css/style.css`:

```css
:root {
    --gold: #D4AF37;
    --charcoal: #1a1a1a;
    --off-white: #F5F5F0;
    /* ... */
}
```

### Ajouter des Voitures

1. Via phpMyAdmin:
   - Ouvrez la table `cars`
   - Cliquez sur "Insérer"
   - Remplissez les champs

2. Via le panel admin (à développer):
   - Accédez à `admin/index.php`
   - Utilisez le formulaire d'ajout

### Images des Voitures

Placez vos images dans `images/cars/` et mettez à jour le champ `image_url` dans la base de données.

## 🔧 API Endpoints

### GET /api/get_cars.php
Récupère la liste des voitures

**Paramètres**:
- `category` (optionnel): ID de catégorie
- `brand` (optionnel): ID de marque
- `search` (optionnel): Terme de recherche
- `featured` (optionnel): true/false
- `limit` (optionnel): Nombre de résultats
- `offset` (optionnel): Pagination

**Exemple**:
```
GET /api/get_cars.php?featured=true&limit=6
```

### GET /api/get_car_details.php
Récupère les détails d'une voiture

**Paramètres**:
- `id` (requis): ID de la voiture

**Exemple**:
```
GET /api/get_car_details.php?id=1
```

### POST /api/submit_booking.php
Soumet une réservation

**Body (JSON)**:
```json
{
  "car_id": 1,
  "nom_client": "Ahmed Benali",
  "email_client": "ahmed@email.com",
  "telephone_client": "+212 6 12 34 56 78",
  "lieu_prise": "Aéroport Marrakech Menara",
  "date_prise": "2026-02-15",
  "heure_prise": "10:00",
  "date_retour": "2026-02-20",
  "heure_retour": "10:00",
  "devise": "DH",
  "message": "Message optionnel"
}
```

## 🔐 Sécurité

- ✅ Requêtes préparées PDO (protection SQL injection)
- ✅ Validation des données côté serveur
- ✅ Échappement HTML (protection XSS)
- ✅ Headers CORS configurés
- ⚠️ **À faire**: Authentification admin
- ⚠️ **À faire**: HTTPS en production
- ⚠️ **À faire**: Rate limiting

## 📱 Responsive Design

Le site est optimisé pour:
- 📱 Mobile (320px - 767px)
- 📱 Tablette (768px - 1023px)
- 💻 Desktop (1024px+)

## 🌐 Navigateurs Supportés

- ✅ Chrome (dernière version)
- ✅ Firefox (dernière version)
- ✅ Safari (dernière version)
- ✅ Edge (dernière version)

## 🚀 Déploiement en Production

1. **Hébergement**: Choisissez un hébergeur PHP/MySQL (OVH, Hostinger, etc.)
2. **Upload**: Transférez tous les fichiers via FTP
3. **Base de données**: Importez `database.sql`
4. **Configuration**: Mettez à jour `config/db.php` avec vos identifiants
5. **HTTPS**: Activez SSL/TLS
6. **Email**: Configurez PHPMailer pour les confirmations

## 📧 Configuration Email

Pour activer l'envoi d'emails de confirmation, décommentez et configurez dans `api/submit_booking.php`:

```php
// Installer PHPMailer via Composer
// composer require phpmailer/phpmailer

use PHPMailer\PHPMailer\PHPMailer;

$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'votre-email@gmail.com';
$mail->Password = 'votre-mot-de-passe';
// ...
```

## 🐛 Dépannage

### Erreur de connexion à la base de données
- Vérifiez que MySQL est démarré
- Vérifiez les identifiants dans `config/db.php`
- Vérifiez que la base de données `location_voiture` existe

### Les images ne s'affichent pas
- Vérifiez les chemins dans la base de données
- Vérifiez que les fichiers existent dans `images/cars/`
- Vérifiez les permissions des dossiers

### Les API ne fonctionnent pas
- Vérifiez que `mod_rewrite` est activé dans Apache
- Vérifiez les chemins relatifs dans les requêtes AJAX
- Consultez la console du navigateur pour les erreurs

## 📝 TODO

- [ ] Panel admin complet (CRUD voitures)
- [ ] Système d'authentification
- [ ] Gestion des disponibilités
- [ ] Calendrier de réservations
- [ ] Paiement en ligne
- [ ] Galerie photos pour chaque voiture
- [ ] Avis clients
- [ ] Blog/Actualités

## 👨‍💻 Développement

### Technologies Utilisées

- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Backend**: PHP 7.4+
- **Base de données**: MySQL 5.7+
- **Fonts**: Google Fonts (Playfair Display, Inter)
- **Icons**: Font Awesome 6.5
- **Architecture**: MVC pattern

### Bonnes Pratiques

- Code commenté en français
- Nommage cohérent (snake_case pour PHP/SQL, camelCase pour JS)
- Responsive-first design
- Progressive enhancement
- Accessibilité (ARIA labels à ajouter)

## 📄 Licence

Ce projet est développé à des fins éducatives et de démonstration.

## 🤝 Support

Pour toute question ou problème:
- Consultez la documentation
- Vérifiez les logs d'erreur PHP
- Inspectez la console du navigateur

---

**Développé avec ❤️ pour une expérience de location premium**

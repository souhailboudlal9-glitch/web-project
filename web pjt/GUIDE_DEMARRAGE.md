# 🚀 Guide de Démarrage Rapide - Luxe Drive

## ⚡ Installation en 5 Minutes

### Étape 1: Prérequis
- ✅ XAMPP, WAMP ou MAMP installé
- ✅ Navigateur web moderne

### Étape 2: Démarrer les Services

**Avec XAMPP:**
1. Ouvrez le panneau de contrôle XAMPP
2. Cliquez sur **Start** pour Apache
3. Cliquez sur **Start** pour MySQL

![XAMPP Control Panel](https://i.imgur.com/xampp-example.png)

### Étape 3: Créer la Base de Données

1. Ouvrez votre navigateur
2. Allez sur: `http://localhost/phpmyadmin`
3. Cliquez sur **"Importer"** dans le menu du haut
4. Cliquez sur **"Choisir un fichier"**
5. Sélectionnez le fichier `database.sql` depuis le dossier `web 2`
6. Cliquez sur **"Exécuter"** en bas de la page

✅ Vous devriez voir: "Importation réussie"

### Étape 4: Vérifier la Configuration

Ouvrez le fichier `config/db.php` et vérifiez:

```php
define('DB_HOST', 'localhost');     // ✅ Correct
define('DB_NAME', 'location_voiture'); // ✅ Correct
define('DB_USER', 'root');          // ✅ Par défaut XAMPP
define('DB_PASS', '');              // ✅ Vide par défaut XAMPP
```

> **Note**: Si vous avez un mot de passe MySQL, modifiez `DB_PASS`

### Étape 5: Accéder au Site

Ouvrez votre navigateur et allez sur:

```
http://localhost/web%202/index.php
```

🎉 **C'est tout! Votre site est en ligne!**

---

## 📱 Pages Disponibles

### Frontend (Visiteurs)

| Page | URL | Description |
|------|-----|-------------|
| **Accueil** | `http://localhost/web%202/index.php` | Page principale avec voitures en vedette |
| **Collection** | `http://localhost/web%202/collection.php` | Toutes les voitures avec filtres |

### Backend (Administration)

| Page | URL | Description |
|------|-----|-------------|
| **Admin Panel** | `http://localhost/web%202/admin/index.php` | Gestion voitures et réservations |

---

## 🔧 Fonctionnalités Principales

### 1. Voir les Voitures
- Accédez à `index.php` ou `collection.php`
- Les voitures sont chargées automatiquement depuis la base de données
- Cliquez sur une voiture pour voir les détails

### 2. Filtrer les Voitures
Sur `collection.php`:
- **Par catégorie**: SUV Premium, Berline Luxe, etc.
- **Par marque**: Mercedes, BMW, Range Rover, etc.
- **Par recherche**: Tapez le nom d'une voiture

### 3. Faire une Réservation
1. Cliquez sur **"Lieu de Départ"** (bouton or sur la page d'accueil)
2. OU cliquez sur **"Réserver"** sur une voiture
3. Remplissez le formulaire:
   - Lieu de prise en charge
   - Dates et heures
   - Vos coordonnées
4. Cliquez sur **"Confirmer la Réservation"**

### 4. Changer la Devise
En haut à droite, cliquez sur:
- **DH** - Dirham marocain
- **€** - Euro
- **$** - Dollar américain

Les prix se mettent à jour automatiquement!

### 5. Gérer les Voitures (Admin)
1. Allez sur `http://localhost/web%202/admin/index.php`
2. **Onglet Voitures**: Voir et supprimer des voitures
3. **Onglet Réservations**: Voir toutes les réservations
4. **Onglet Ajouter**: Ajouter une nouvelle voiture

---

## 🎨 Personnalisation Rapide

### Changer les Couleurs

Éditez `css/style.css`, lignes 8-12:

```css
:root {
    --gold: #D4AF37;      /* Couleur or principale */
    --charcoal: #1a1a1a;  /* Couleur noire */
    --off-white: #F5F5F0; /* Couleur de fond */
}
```

### Changer le Nom du Site

Éditez `index.php`, ligne 71:

```php
<h1><i class="fas fa-crown"></i> Votre Nom Ici</h1>
```

### Ajouter Vos Coordonnées

Éditez `index.php`, lignes 265-280:

```php
<a href="tel:+212600000000">+212 6 00 00 00 00</a>
<a href="mailto:contact@luxedrive.ma">contact@luxedrive.ma</a>
```

---

## 📊 Structure de la Base de Données

### Tables Principales

```
location_voiture/
├── cars                    # Voitures (14 véhicules)
├── categories              # 5 catégories
├── brands                  # 8 marques
├── bookings                # Réservations
├── contact_messages        # Messages de contact
└── newsletter_subscribers  # Abonnés newsletter
```

### Ajouter une Voiture Manuellement

**Via phpMyAdmin:**
1. Allez sur `http://localhost/phpmyadmin`
2. Sélectionnez la base `location_voiture`
3. Cliquez sur la table `cars`
4. Cliquez sur **"Insérer"**
5. Remplissez les champs:
   - `nom`: Nom de la voiture (ex: "Audi A6")
   - `brand_id`: ID de la marque (1-8)
   - `category_id`: ID de la catégorie (1-5)
   - `prix_jour_dh`: Prix en DH
   - `prix_jour_eur`: Prix en EUR
   - `prix_jour_usd`: Prix en USD
   - `transmission`: "Automatique" ou "Manuelle"
   - `carburant`: "Diesel", "Essence", "Hybride", "Électrique"
   - `places`: Nombre de places (ex: 5)
   - `portes`: Nombre de portes (ex: 4)
   - `annee`: Année (ex: 2024)
   - `image_url`: Chemin de l'image (ex: "images/cars/audi-a6.jpg")
6. Cliquez sur **"Exécuter"**

**Via Admin Panel:**
1. Allez sur `admin/index.php`
2. Cliquez sur l'onglet **"Ajouter Voiture"**
3. Remplissez le formulaire
4. Cliquez sur **"Enregistrer"**

---

## 🐛 Résolution de Problèmes

### ❌ Erreur: "Connexion refusée"
**Solution:**
- Vérifiez que MySQL est démarré dans XAMPP
- Vérifiez les identifiants dans `config/db.php`

### ❌ Les voitures ne s'affichent pas
**Solution:**
1. Vérifiez que la base de données est importée
2. Ouvrez `http://localhost/web%202/api/get_cars.php?featured=true`
3. Vous devriez voir du JSON avec les voitures

### ❌ Les images ne s'affichent pas
**Solution:**
- Vérifiez que les fichiers existent dans `images/cars/`
- Les chemins dans la base de données doivent être: `images/cars/nom-voiture.jpg`

### ❌ Erreur 404 sur les pages PHP
**Solution:**
- Vérifiez que Apache est démarré
- Utilisez `http://localhost/web%202/` et non `file:///`
- Vérifiez que le dossier est bien dans `htdocs` (XAMPP)

### ❌ Les réservations ne fonctionnent pas
**Solution:**
1. Ouvrez la console du navigateur (F12)
2. Vérifiez les erreurs JavaScript
3. Testez l'API: `http://localhost/web%202/api/submit_booking.php`

---

## 📞 Support

### Logs d'Erreurs

**PHP Errors:**
- XAMPP: `C:\xampp\apache\logs\error.log`
- WAMP: `C:\wamp\logs\php_error.log`

**Console Navigateur:**
- Appuyez sur `F12`
- Onglet **"Console"** pour les erreurs JavaScript
- Onglet **"Network"** pour les requêtes API

### Tester les APIs

Utilisez votre navigateur ou Postman:

```
# Toutes les voitures
http://localhost/web%202/api/get_cars.php

# Voitures en vedette
http://localhost/web%202/api/get_cars.php?featured=true

# Détails d'une voiture
http://localhost/web%202/api/get_car_details.php?id=1

# Filtres
http://localhost/web%202/api/get_filters.php
```

---

## 🚀 Prochaines Étapes

### Recommandé
1. ✅ Changez les coordonnées (téléphone, email)
2. ✅ Ajoutez vos propres images de voitures
3. ✅ Personnalisez les couleurs
4. ✅ Testez toutes les fonctionnalités

### Optionnel
- 📧 Configurez l'envoi d'emails (PHPMailer)
- 🔐 Ajoutez un système de login pour l'admin
- 💳 Intégrez un système de paiement
- 🌍 Déployez sur un hébergeur en ligne

---

## ✅ Checklist de Vérification

Avant de mettre en production, vérifiez:

- [ ] Apache et MySQL démarrés
- [ ] Base de données importée
- [ ] Au moins 3-4 voitures visibles sur le site
- [ ] Réservation fonctionne (testez-la!)
- [ ] Filtres fonctionnent sur collection.php
- [ ] Currency switcher change les prix
- [ ] Admin panel accessible
- [ ] Vos coordonnées sont à jour
- [ ] Images des voitures s'affichent
- [ ] Responsive sur mobile (testez avec F12 > mode mobile)

---

## 🎓 Ressources

- **Documentation PHP**: https://www.php.net/
- **Documentation MySQL**: https://dev.mysql.com/doc/
- **XAMPP Tutoriel**: https://www.apachefriends.org/
- **Font Awesome Icons**: https://fontawesome.com/icons

---

**Besoin d'aide?** Consultez le `README.md` complet ou les commentaires dans le code!

🚗 **Bon lancement avec Luxe Drive!** ✨

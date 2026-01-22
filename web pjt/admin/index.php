<?php
/**
 * Simple Admin Panel for Car Management
 * Note: This is a basic version. Add authentication in production!
 */

require_once '../config/db.php';

// Handle car deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM cars WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: index.php?success=deleted');
    exit;
}

// Handle payment confirmation
if (isset($_GET['confirm_payment']) && is_numeric($_GET['confirm_payment'])) {
    $id = intval($_GET['confirm_payment']);
    executeQuery($pdo, "UPDATE bookings SET paiement_confirme = TRUE WHERE id = ?", [$id]);
    header('Location: index.php?tab=bookings&success=payment_confirmed');
    exit;
}

// Handle booking status update
if (isset($_GET['update_status']) && isset($_GET['booking_id'])) {
    $status = $_GET['update_status'];
    $id = intval($_GET['booking_id']);
    if (in_array($status, ['Confirmée', 'Annulée', 'Terminée'])) {
        executeQuery($pdo, "UPDATE bookings SET statut = ? WHERE id = ?", [$status, $id]);
    }
    header('Location: index.php?tab=bookings&success=status_updated');
    exit;
}

// Handle car addition/update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $brand_id = $_POST['brand_id'];
    $category_id = $_POST['category_id'];
    $description = $_POST['description'];
    $image_url = $_POST['image_url'];
    $prix_dh = $_POST['prix_dh'];
    $prix_eur = $_POST['prix_eur'];
    $prix_usd = $_POST['prix_usd'];
    $transmission = $_POST['transmission'];
    $carburant = $_POST['carburant'];
    $places = $_POST['places'];
    $portes = $_POST['portes'];
    $annee = $_POST['annee'];
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update
        $sql = "UPDATE cars SET nom=?, brand_id=?, category_id=?, description=?, image_url=?, 
                prix_jour_dh=?, prix_jour_eur=?, prix_jour_usd=?, transmission=?, carburant=?, 
                places=?, portes=?, annee=?, featured=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nom, $brand_id, $category_id, $description, $image_url, 
                       $prix_dh, $prix_eur, $prix_usd, $transmission, $carburant, 
                       $places, $portes, $annee, $featured, $_POST['id']]);
    } else {
        // Insert
        $sql = "INSERT INTO cars (nom, brand_id, category_id, description, image_url, 
                prix_jour_dh, prix_jour_eur, prix_jour_usd, transmission, carburant, 
                places, portes, annee, featured) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nom, $brand_id, $category_id, $description, $image_url, 
                       $prix_dh, $prix_eur, $prix_usd, $transmission, $carburant, 
                       $places, $portes, $annee, $featured]);
    }
    
    header('Location: index.php?success=saved');
    exit;
}

// Get all cars
$cars = fetchAll($pdo, "SELECT c.*, b.nom as marque, cat.nom as categorie 
                        FROM cars c 
                        JOIN brands b ON c.brand_id = b.id 
                        JOIN categories cat ON c.category_id = cat.id 
                        ORDER BY c.created_at DESC");

// Get categories and brands for form
$categories = fetchAll($pdo, "SELECT * FROM categories ORDER BY nom");
$brands = fetchAll($pdo, "SELECT * FROM brands ORDER BY nom");

// Get bookings
$bookings = fetchAll($pdo, "SELECT b.*, c.nom as car_name 
                            FROM bookings b 
                            LEFT JOIN cars c ON b.car_id = c.id 
                            ORDER BY b.created_at DESC 
                            LIMIT 50");

// Get revenue statistics
$revenueStats = [
    'total' => fetchOne($pdo, "SELECT COALESCE(SUM(prix_total_dh), 0) as total FROM bookings WHERE statut IN ('Confirmée', 'Terminée')")['total'],
    'total_confirmed' => fetchOne($pdo, "SELECT COALESCE(SUM(prix_total_dh), 0) as total FROM bookings WHERE paiement_confirme = TRUE")['total'],
    'pending' => fetchOne($pdo, "SELECT COALESCE(SUM(prix_total_dh), 0) as total FROM bookings WHERE statut = 'En attente'")['total'],
    'by_payment' => fetchAll($pdo, "SELECT mode_paiement, COUNT(*) as count, COALESCE(SUM(prix_total_dh), 0) as total FROM bookings WHERE statut IN ('Confirmée', 'Terminée') GROUP BY mode_paiement"),
    'monthly' => fetchAll($pdo, "SELECT DATE_FORMAT(created_at, '%Y-%m') as mois, COALESCE(SUM(prix_total_dh), 0) as total, COUNT(*) as count FROM bookings WHERE statut IN ('Confirmée', 'Terminée') GROUP BY mois ORDER BY mois DESC LIMIT 12"),
    'count_bookings' => fetchOne($pdo, "SELECT COUNT(*) as total FROM bookings")['total'],
    'count_confirmed' => fetchOne($pdo, "SELECT COUNT(*) as total FROM bookings WHERE statut = 'Confirmée'")['total']
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Luxe Drive</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .header { background: #1a1a1a; color: #D4AF37; padding: 1.5rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 1.8rem; }
        .header a { color: #D4AF37; text-decoration: none; }
        .container { max-width: 1400px; margin: 2rem auto; padding: 0 2rem; }
        .tabs { display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap; }
        .tab { padding: 1rem 2rem; background: white; border: none; cursor: pointer; border-radius: 8px; font-size: 1rem; }
        .tab.active { background: #D4AF37; color: #1a1a1a; font-weight: 600; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .card { background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        .card h2 { margin-bottom: 1.5rem; color: #1a1a1a; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #e0e0e0; }
        th { background: #f9f9f9; font-weight: 600; }
        .btn { padding: 0.6rem 1.2rem; border: none; border-radius: 6px; cursor: pointer; font-size: 0.9rem; transition: 0.3s; text-decoration: none; display: inline-block; }
        .btn-primary { background: #D4AF37; color: #1a1a1a; }
        .btn-danger { background: #ef4444; color: white; }
        .btn-success { background: #10b981; color: white; }
        .btn-sm { padding: 0.4rem 0.8rem; font-size: 0.8rem; }
        .btn:hover { opacity: 0.8; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; }
        .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; }
        .badge { padding: 0.4rem 0.8rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        .alert { padding: 1rem; border-radius: 8px; margin-bottom: 1rem; }
        .alert-success { background: #d1fae5; color: #065f46; }
        
        /* Stats Cards */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .stat-card.gold { background: linear-gradient(135deg, #D4AF37, #B8941E); color: white; }
        .stat-card.green { background: linear-gradient(135deg, #10b981, #059669); color: white; }
        .stat-card.blue { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; }
        .stat-card.purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; }
        .stat-icon { font-size: 2.5rem; opacity: 0.8; margin-bottom: 1rem; }
        .stat-value { font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem; }
        .stat-label { font-size: 0.9rem; opacity: 0.9; }
        
        /* Revenue Chart */
        .chart-container { margin-top: 1.5rem; }
        .bar-chart { display: flex; align-items: flex-end; gap: 1rem; height: 200px; padding: 1rem 0; }
        .bar { flex: 1; background: linear-gradient(to top, #D4AF37, #F4E4B0); border-radius: 6px 6px 0 0; min-width: 40px; position: relative; transition: 0.3s; }
        .bar:hover { opacity: 0.8; }
        .bar-label { position: absolute; bottom: -25px; left: 50%; transform: translateX(-50%); font-size: 0.75rem; white-space: nowrap; }
        .bar-value { position: absolute; top: -25px; left: 50%; transform: translateX(-50%); font-size: 0.75rem; font-weight: 600; }
        
        /* Payment breakdown */
        .payment-breakdown { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem; }
        .payment-card { padding: 1rem; border-radius: 8px; background: #f9f9f9; text-align: center; }
        .payment-card i { font-size: 2rem; color: #D4AF37; margin-bottom: 0.5rem; }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-crown"></i> Luxe Drive - Admin Panel</h1>
        <a href="../index.php"><i class="fas fa-arrow-left"></i> Retour au site</a>
    </div>
    
    <div class="container">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php 
                $messages = [
                    'saved' => 'Voiture enregistrée avec succès!',
                    'deleted' => 'Voiture supprimée avec succès!',
                    'payment_confirmed' => 'Paiement confirmé!',
                    'status_updated' => 'Statut mis à jour!'
                ];
                echo $messages[$_GET['success']] ?? 'Opération réussie!';
                ?>
            </div>
        <?php endif; ?>
        
        <div class="tabs">
            <button class="tab active" onclick="showTab('cars')">Voitures</button>
            <button class="tab" onclick="showTab('bookings')">Réservations</button>
            <button class="tab" onclick="showTab('revenue')"><i class="fas fa-chart-line"></i> Revenus</button>
            <button class="tab" onclick="showTab('add')">Ajouter Voiture</button>
        </div>
        
        <!-- Cars Tab -->
        <div id="cars" class="tab-content active">
            <div class="card">
                <h2>Gestion des Voitures</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Marque</th>
                            <th>Catégorie</th>
                            <th>Prix/Jour (DH)</th>
                            <th>Featured</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cars as $car): ?>
                        <tr>
                            <td><?php echo $car['id']; ?></td>
                            <td><?php echo escape($car['nom']); ?></td>
                            <td><?php echo escape($car['marque']); ?></td>
                            <td><?php echo escape($car['categorie']); ?></td>
                            <td><?php echo number_format($car['prix_jour_dh'], 2); ?> DH</td>
                            <td>
                                <?php if ($car['featured']): ?>
                                    <span class="badge badge-success">Oui</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Non</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="?delete=<?php echo $car['id']; ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Êtes-vous sûr?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Bookings Tab -->
        <div id="bookings" class="tab-content">
            <div class="card">
                <h2>Réservations Récentes</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Client</th>
                            <th>Voiture</th>
                            <th>Dates</th>
                            <th>Prix Total</th>
                            <th>Paiement</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?php echo $booking['id']; ?></td>
                            <td>
                                <?php echo escape($booking['nom_client']); ?><br>
                                <small><?php echo escape($booking['email_client']); ?></small>
                            </td>
                            <td><?php echo escape($booking['car_name'] ?? 'À déterminer'); ?></td>
                            <td>
                                <?php echo date('d/m/Y', strtotime($booking['date_prise'])); ?> - 
                                <?php echo date('d/m/Y', strtotime($booking['date_retour'])); ?>
                                <br><small>(<?php echo $booking['nombre_jours']; ?> jours)</small>
                            </td>
                            <td><?php echo number_format($booking['prix_total_dh'], 2); ?> <?php echo $booking['devise']; ?></td>
                            <td>
                                <?php 
                                $paymentMode = $booking['mode_paiement'] ?? 'sur_place';
                                $paymentIcon = $paymentMode === 'carte' ? 'fa-credit-card' : 'fa-hand-holding-usd';
                                $paymentLabel = $paymentMode === 'carte' ? 'Carte' : 'Sur place';
                                $isPaid = $booking['paiement_confirme'] ?? false;
                                ?>
                                <span class="badge <?php echo $isPaid ? 'badge-success' : 'badge-warning'; ?>">
                                    <i class="fas <?php echo $paymentIcon; ?>"></i> <?php echo $paymentLabel; ?>
                                    <?php echo $isPaid ? '✓' : ''; ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                $statusClass = [
                                    'En attente' => 'badge-warning',
                                    'Confirmée' => 'badge-success',
                                    'Annulée' => 'badge-danger',
                                    'Terminée' => 'badge-info'
                                ];
                                ?>
                                <span class="badge <?php echo $statusClass[$booking['statut']] ?? 'badge-warning'; ?>">
                                    <?php echo $booking['statut']; ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!($booking['paiement_confirme'] ?? false)): ?>
                                    <a href="?confirm_payment=<?php echo $booking['id']; ?>" class="btn btn-success btn-sm" title="Confirmer paiement">
                                        <i class="fas fa-check"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if ($booking['statut'] === 'En attente'): ?>
                                    <a href="?update_status=Confirmée&booking_id=<?php echo $booking['id']; ?>" class="btn btn-primary btn-sm" title="Confirmer">
                                        <i class="fas fa-check-double"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Revenue Tab -->
        <div id="revenue" class="tab-content">
            <div class="stats-grid">
                <div class="stat-card gold">
                    <div class="stat-icon"><i class="fas fa-coins"></i></div>
                    <div class="stat-value"><?php echo number_format($revenueStats['total'], 0); ?> DH</div>
                    <div class="stat-label">Revenus Totaux (Confirmés)</div>
                </div>
                <div class="stat-card green">
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-value"><?php echo number_format($revenueStats['total_confirmed'], 0); ?> DH</div>
                    <div class="stat-label">Paiements Reçus</div>
                </div>
                <div class="stat-card blue">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div class="stat-value"><?php echo number_format($revenueStats['pending'], 0); ?> DH</div>
                    <div class="stat-label">En Attente</div>
                </div>
                <div class="stat-card purple">
                    <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="stat-value"><?php echo $revenueStats['count_confirmed']; ?> / <?php echo $revenueStats['count_bookings']; ?></div>
                    <div class="stat-label">Réservations Confirmées</div>
                </div>
            </div>
            
            <div class="card">
                <h2><i class="fas fa-chart-bar"></i> Revenus par Mois</h2>
                <div class="chart-container">
                    <?php if (count($revenueStats['monthly']) > 0): ?>
                        <?php 
                        $maxRevenue = max(array_column($revenueStats['monthly'], 'total'));
                        $maxRevenue = $maxRevenue > 0 ? $maxRevenue : 1;
                        ?>
                        <div class="bar-chart">
                            <?php foreach (array_reverse($revenueStats['monthly']) as $month): ?>
                                <?php $height = ($month['total'] / $maxRevenue) * 100; ?>
                                <div class="bar" style="height: <?php echo max($height, 5); ?>%;">
                                    <span class="bar-value"><?php echo number_format($month['total']/1000, 1); ?>K</span>
                                    <span class="bar-label"><?php echo date('M Y', strtotime($month['mois'] . '-01')); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>Aucune donnée disponible</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card">
                <h2><i class="fas fa-credit-card"></i> Répartition par Mode de Paiement</h2>
                <div class="payment-breakdown">
                    <?php foreach ($revenueStats['by_payment'] as $payment): ?>
                        <div class="payment-card">
                            <i class="fas <?php echo $payment['mode_paiement'] === 'carte' ? 'fa-credit-card' : 'fa-hand-holding-usd'; ?>"></i>
                            <h3><?php echo $payment['mode_paiement'] === 'carte' ? 'Carte Bancaire' : 'Sur Place'; ?></h3>
                            <p><strong><?php echo number_format($payment['total'], 0); ?> DH</strong></p>
                            <p><?php echo $payment['count']; ?> réservations</p>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($revenueStats['by_payment'])): ?>
                        <div class="payment-card">
                            <i class="fas fa-info-circle"></i>
                            <p>Aucune donnée disponible</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Add Car Tab -->
        <div id="add" class="tab-content">
            <div class="card">
                <h2>Ajouter une Nouvelle Voiture</h2>
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nom du Véhicule *</label>
                            <input type="text" name="nom" required>
                        </div>
                        <div class="form-group">
                            <label>Marque *</label>
                            <select name="brand_id" required>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?php echo $brand['id']; ?>"><?php echo escape($brand['nom']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Catégorie *</label>
                            <select name="category_id" required>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>"><?php echo escape($category['nom']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>URL de l'Image</label>
                        <input type="text" name="image_url" placeholder="images/cars/voiture.jpg">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Prix/Jour (DH) *</label>
                            <input type="number" name="prix_dh" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label>Prix/Jour (EUR) *</label>
                            <input type="number" name="prix_eur" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label>Prix/Jour (USD) *</label>
                            <input type="number" name="prix_usd" step="0.01" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Transmission *</label>
                            <select name="transmission" required>
                                <option value="Automatique">Automatique</option>
                                <option value="Manuelle">Manuelle</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Carburant *</label>
                            <select name="carburant" required>
                                <option value="Diesel">Diesel</option>
                                <option value="Essence">Essence</option>
                                <option value="Hybride">Hybride</option>
                                <option value="Électrique">Électrique</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Places *</label>
                            <input type="number" name="places" value="5" required>
                        </div>
                        <div class="form-group">
                            <label>Portes *</label>
                            <input type="number" name="portes" value="4" required>
                        </div>
                        <div class="form-group">
                            <label>Année *</label>
                            <input type="number" name="annee" value="2024" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="featured"> Véhicule en vedette
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab').forEach(btn => btn.classList.remove('active'));
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }
        
        // Check URL params for default tab
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('tab')) {
            showTab(urlParams.get('tab'));
        }
    </script>
</body>
</html>

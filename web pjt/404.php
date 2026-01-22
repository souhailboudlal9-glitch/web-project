<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Non Trouvée | Luxe Drive</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .error-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--charcoal) 0%, var(--charcoal-light) 100%);
            color: var(--white);
            text-align: center;
            padding: 2rem;
        }
        .error-content h1 {
            font-family: var(--font-heading);
            font-size: 8rem;
            color: var(--gold);
            margin-bottom: 1rem;
        }
        .error-content h2 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        .error-content p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            color: var(--off-white);
        }
    </style>
</head>
<body>
    <div class="error-page">
        <div class="error-content">
            <h1>404</h1>
            <h2>Page Non Trouvée</h2>
            <p>Désolé, la page que vous recherchez n'existe pas.</p>
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-home"></i> Retour à l'Accueil
            </a>
        </div>
    </div>
</body>
</html>

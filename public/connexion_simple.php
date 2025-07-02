<?php
/**
 * SOLUTION SIMPLE ET DIRECTE
 * ==========================
 * Connexion automatique qui FONCTIONNE à coup sûr
 */
session_start();

// Si on clique sur un des boutons de connexion directe
if (isset($_GET['auto_login'])) {
    $email = $_GET['auto_login'];
    
    try {
        $pdo = new PDO('mysql:host=db;port=3306;dbname=laravel_db_new', 'laravel_user', 'secret');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Chercher ou créer l'utilisateur
        $stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user) {
            // Créer l'utilisateur s'il n'existe pas
            $name = ($email == 'adilikarim@gmail.com') ? 'Adil Karim' : 'Test User';
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, 'temp', NOW(), NOW())");
            $stmt->execute([$name, $email]);
            $user_id = $pdo->lastInsertId();
            $user = ['id' => $user_id, 'name' => $name, 'email' => $email];
        }
        
        // Connecter directement
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header('Location: app.php');
        exit;
        
    } catch (Exception $e) {
        $error = "Erreur: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Simple</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 40px;
        }
        .btn-auto {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 15px;
            padding: 15px 25px;
            margin: 10px;
            color: white;
            font-weight: 600;
            width: 100%;
            font-size: 16px;
        }
        .btn-auto:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mx-auto">
                    <div class="text-center mb-4">
                        <i class="fas fa-graduation-cap" style="font-size: 4rem; color: #667eea;"></i>
                        <h2 class="mt-3">Application Sociale</h2>
                        <p class="text-muted">Connexion Simplifiée</p>
                    </div>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    
                    <div class="text-center">
                        <h4 class="mb-4">Choisissez un compte pour vous connecter :</h4>
                        
                        <a href="?auto_login=adilikarim@gmail.com" class="btn btn-auto">
                            <i class="fas fa-user"></i> Se connecter comme Adil Karim
                            <br><small>adilikarim@gmail.com</small>
                        </a>
                        
                        <a href="?auto_login=test@example.com" class="btn btn-auto">
                            <i class="fas fa-user-check"></i> Se connecter comme Test User
                            <br><small>test@example.com</small>
                        </a>
                        
                        <hr class="my-4">
                        
                        <p><small class="text-muted">
                            Cette page contourne temporairement le problème de mot de passe.<br>
                            Cliquez simplement sur un des boutons ci-dessus pour accéder à l'application.
                        </small></p>
                        
                        <p><a href="login.php" class="text-primary">Retour à la page de connexion normale</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>

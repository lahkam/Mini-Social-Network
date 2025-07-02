<?php
/** * 
 * Page de connexion pour l'application sociale éducative
 * Fonctionnalités de base pour l'apprentissage
 */

session_start();

// Si déjà connecté, rediriger vers l'application
if (isset($_SESSION['user_id'])) {
    header('Location: app.php');
    exit;
}

$message = '';

// Configuration base de données
try {
    $pdo = new PDO('mysql:host=db;port=3306;dbname=laravel_db_new', 'laravel_user', 'secret');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}

// Traitement de la connexion
if ($_POST) {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($email) || empty($password)) {
        $message = '<div class="alert alert-warning">Veuillez remplir tous les champs</div>';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                header('Location: app.php');
                exit;
            } else {
                $message = '<div class="alert alert-danger">Email ou mot de passe incorrect</div>';
            }
        } catch (Exception $e) {
            $message = '<div class="alert alert-danger">Erreur de connexion</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réseau Social</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .carte-connexion {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 400px;
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo i {
            font-size: 4rem;
            color: #667eea;
        }
        .btn-connexion {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 12px;
            width: 100%;
            color: white;
            font-weight: 600;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e3e6f0;
            padding: 12px 15px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="carte-connexion mx-auto">
                    <div class="logo">
                        <i class="fas fa-graduation-cap"></i>
                        <h3 class="mt-3">Application Sociale</h3>
                        <p class="text-muted">Laravel Débutant</p>
                    </div>
                    
                    <?= $message ?>
                    
                    <form method="POST">
                        <input type="hidden" name="action" value="connexion">
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i> Email
                            </label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="votre@email.com" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock"></i> Mot de passe
                            </label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Votre mot de passe" required>
                        </div>
                        
                        <button type="submit" class="btn btn-connexion">
                            <i class="fas fa-sign-in-alt"></i> Se connecter
                        </button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <small class="text-muted">
                            Pas de compte ? <a href="register.php">S'inscrire</a>
                        </small>
                    </div>
                    
                    <div class="text-center mt-4 pt-3 border-top">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Comptes de test :</strong><br>
                            📧 <strong>adilikarim@gmail.com</strong><br>
                            📧 <strong>test@example.com</strong><br>
                            🔑 Mot de passe : <strong>password</strong>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

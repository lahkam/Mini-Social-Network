<!DOCTYPE html>
<html>
<head>
    <title>Inscription - Social Media</title>
    <style>
        body { font-family: Arial; max-width: 400px; margin: 100px auto; padding: 20px; background: #f8f9fa; }
        .register-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .form-group { margin: 15px 0; }
        input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 15px; background: #28a745; color: white; border: none; cursor: pointer; border-radius: 4px; font-size: 16px; }
        button:hover { background: #218838; }
        .alert { padding: 12px; margin: 15px 0; border-radius: 4px; }
        .alert-danger { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .alert-success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .logo { text-align: center; margin-bottom: 30px; }
        .login-link { text-align: center; margin-top: 20px; }
        .login-link a { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo">
            <h2>🌐 Social Media Platform</h2>
            <p style="color: #666;">Créer votre compte</p>
        </div>
        
        <form method="POST" action="">
            <div class="form-group">
                <input type="text" name="name" placeholder="Nom complet" required>
            </div>
            
            <div class="form-group">
                <input type="email" name="email" placeholder="Adresse email" required>
            </div>
            
            <div class="form-group">
                <input type="password" name="password" placeholder="Mot de passe" required>
            </div>
            
            <div class="form-group">
                <input type="password" name="password_confirm" placeholder="Confirmer le mot de passe" required>
            </div>
            
            <button type="submit">CRÉER LE COMPTE</button>
        </form>
        
        <div class="login-link">
            <a href="login.php">Déjà un compte ? Se connecter</a>
        </div>

        <?php
        if ($_POST) {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';
            
            // Validation
            if (empty($name) || empty($email) || empty($password)) {
                echo '<div class="alert alert-danger">❌ Tous les champs sont requis</div>';
            } elseif ($password !== $password_confirm) {
                echo '<div class="alert alert-danger">❌ Les mots de passe ne correspondent pas</div>';
            } elseif (strlen($password) < 6) {
                echo '<div class="alert alert-danger">❌ Le mot de passe doit contenir au moins 6 caractères</div>';
            } else {
                try {
                    $pdo = new PDO('mysql:host=db;port=3306;dbname=laravel_db_new', 'laravel_user', 'secret');
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    // Vérifier si l'email existe déjà
                    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                    $stmt->execute([$email]);
                    
                    if ($stmt->fetch()) {
                        echo '<div class="alert alert-danger">❌ Cette adresse email est déjà utilisée</div>';
                    } else {
                        // Créer l'utilisateur
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
                        
                        if ($stmt->execute([$name, $email, $hashed_password])) {
                            echo '<div class="alert alert-success">✅ Compte créé avec succès ! <a href="login.php">Se connecter</a></div>';
                        } else {
                            echo '<div class="alert alert-danger">❌ Erreur lors de la création du compte</div>';
                        }
                    }
                } catch (PDOException $e) {
                    echo '<div class="alert alert-danger">❌ Erreur de connexion à la base de données</div>';
                }
            }
        }
        ?>
    </div>
</body>
</html>

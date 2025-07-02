<?php
/**
 * TEST RAPIDE DE CONNEXION
 * ========================
 * Script pour tester rapidement si la connexion fonctionne
 */

try {
    $pdo = new PDO('mysql:host=db;port=3306;dbname=laravel_db_new', 'laravel_user', 'secret');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>🧪 Test Rapide de Connexion</h1>";
    echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;} .success{color:#28a745;} .error{color:#dc3545;} .info{color:#17a2b8;}</style>";
    
    $comptes_test = ['adilikarim@gmail.com', 'test@example.com'];
    
    foreach ($comptes_test as $email) {
        echo "<h3>Test : $email</h3>";
        
        // Récupérer l'utilisateur
        $stmt = $pdo->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user) {
            echo "<p class='error'>❌ Utilisateur non trouvé</p>";
            continue;
        }
        
        // Test avec "password"
        if (password_verify("password", $user['password'])) {
            echo "<p class='success'>✅ Connexion réussie avec 'password'</p>";
            
            // Simuler l'établissement de session
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            
            echo "<p class='info'>👤 Session établie pour : " . htmlspecialchars($user['name']) . "</p>";
            
        } else {
            echo "<p class='error'>❌ Connexion échouée</p>";
            echo "<p>Hash en base : <code>" . htmlspecialchars($user['password']) . "</code></p>";
        }
        
        echo "<hr>";
    }
    
    echo "<h3>🔗 Liens de test</h3>";
    echo "<p><a href='login.php' style='padding:10px 20px;background:#007bff;color:white;text-decoration:none;border-radius:5px;margin:5px;'>Login Page</a></p>";
    echo "<p><a href='app.php' style='padding:10px 20px;background:#28a745;color:white;text-decoration:none;border-radius:5px;margin:5px;'>Application</a></p>";
    echo "<p><a href='correction_definitive.php' style='padding:10px 20px;background:#ffc107;color:black;text-decoration:none;border-radius:5px;margin:5px;'>Correction</a></p>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

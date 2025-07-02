<?php
/**
 * SIMULATION EXACTE DU LOGIN
 * ==========================
 * Simule exactement ce qui se passe lors de la soumission du formulaire
 */

session_start();

try {
    $pdo = new PDO('mysql:host=db;port=3306;dbname=laravel_db_new', 'laravel_user', 'secret');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>🎯 Simulation Exacte du Login</h1>";
    echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;} .success{color:#28a745;} .error{color:#dc3545;} .info{color:#17a2b8;} .debug{background:#fff3cd;padding:10px;border-radius:5px;margin:10px 0;}</style>";
    
    // Simuler les données POST du formulaire
    $email = "adilikarim@gmail.com";
    $password = "password";
    
    echo "<div class='debug'>";
    echo "<h3>📥 Données simulées du formulaire:</h3>";
    echo "<p><strong>Email:</strong> $email</p>";
    echo "<p><strong>Password:</strong> $password</p>";
    echo "</div>";
    
    // Reproduire EXACTEMENT le code de login.php
    $email = trim($email);
    $password = trim($password);
    
    if (empty($email) || empty($password)) {
        echo "<p class='error'>❌ Champs vides</p>";
    } else {
        echo "<div class='debug'>";
        echo "<h3>🔍 Recherche de l'utilisateur...</h3>";
        
        $stmt = $pdo->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            echo "<p class='success'>✅ Utilisateur trouvé:</p>";
            echo "<ul>";
            echo "<li><strong>ID:</strong> " . $user['id'] . "</li>";
            echo "<li><strong>Nom:</strong> " . htmlspecialchars($user['name']) . "</li>";
            echo "<li><strong>Email:</strong> " . htmlspecialchars($user['email']) . "</li>";
            echo "<li><strong>Hash password:</strong> <code>" . htmlspecialchars($user['password']) . "</code></li>";
            echo "</ul>";
            
            echo "<h3>🔐 Test password_verify...</h3>";
            $verify_result = password_verify($password, $user['password']);
            echo "<p><strong>password_verify('$password', hash):</strong> " . ($verify_result ? "TRUE" : "FALSE") . "</p>";
            
            if ($verify_result) {
                echo "<div style='background:#d4edda;padding:15px;border-radius:5px;margin:20px 0;'>";
                echo "<h2 class='success'>🎉 CONNEXION RÉUSSIE !</h2>";
                echo "<p>L'authentification fonctionne parfaitement.</p>";
                echo "<p>Session qui serait créée :</p>";
                echo "<ul>";
                echo "<li>user_id = " . $user['id'] . "</li>";
                echo "<li>user_name = " . htmlspecialchars($user['name']) . "</li>";
                echo "</ul>";
                echo "</div>";
                
                // Créer réellement la session pour tester
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                
                echo "<p><a href='app.php' style='padding:15px 30px;background:#28a745;color:white;text-decoration:none;border-radius:5px;font-size:16px;'>🚀 ALLER À L'APPLICATION</a></p>";
                
            } else {
                echo "<div style='background:#f8d7da;padding:15px;border-radius:5px;margin:20px 0;'>";
                echo "<h2 class='error'>❌ CONNEXION ÉCHOUÉE</h2>";
                echo "<p>Le password_verify a retourné FALSE.</p>";
                echo "<p>Il y a un problème avec le hash du mot de passe.</p>";
                echo "</div>";
            }
        } else {
            echo "<p class='error'>❌ Aucun utilisateur trouvé avec cet email</p>";
        }
        echo "</div>";
    }
    
    echo "<hr>";
    echo "<h3>🔗 Actions</h3>";
    echo "<p><a href='login.php' style='padding:10px 20px;background:#007bff;color:white;text-decoration:none;border-radius:5px;margin:5px;'>🔗 Page de login</a></p>";
    echo "<p><a href='fix_auth_final.php' style='padding:10px 20px;background:#dc3545;color:white;text-decoration:none;border-radius:5px;margin:5px;'>🔧 Refixer les mots de passe</a></p>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

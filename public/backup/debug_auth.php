<?php
/**
 * DIAGNOSTIC PRÉCIS - AUTHENTIFICATION
 * ====================================
 * Test exact de ce qui se passe lors de la connexion
 */

try {
    $pdo = new PDO('mysql:host=db;port=3306;dbname=laravel_db_new', 'laravel_user', 'secret');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>🔍 Diagnostic Authentification</h1>";
    echo "<style>
        body{font-family:Arial;padding:20px;background:#f5f5f5;} 
        .success{color:#28a745;} 
        .error{color:#dc3545;} 
        .info{color:#17a2b8;}
        .debug{background:#f8f9fa;padding:10px;border:1px solid #dee2e6;margin:10px 0;border-radius:5px;}
    </style>";
    
    // 1. Vérifier les utilisateurs dans la base
    echo "<h2>1. Utilisateurs en base de données</h2>";
    $stmt = $pdo->query("SELECT id, name, email, password FROM users");
    $users = $stmt->fetchAll();
    
    if (empty($users)) {
        echo "<p class='error'>❌ AUCUN UTILISATEUR TROUVÉ !</p>";
        
        // Créer immédiatement les utilisateurs de test
        echo "<h3>🔧 Création des utilisateurs de test...</h3>";
        
        $password_hash = password_hash("password", PASSWORD_DEFAULT);
        echo "<div class='debug'>Hash généré pour 'password': <code>$password_hash</code></div>";
        
        $test_users = [
            ['name' => 'Adil Karim', 'email' => 'adilikarim@gmail.com'],
            ['name' => 'Test User', 'email' => 'test@example.com']
        ];
        
        foreach ($test_users as $user) {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
            $stmt->execute([$user['name'], $user['email'], $password_hash]);
            echo "<p class='success'>✅ Utilisateur créé: {$user['email']}</p>";
        }
        
        // Re-récupérer les utilisateurs
        $stmt = $pdo->query("SELECT id, name, email, password FROM users");
        $users = $stmt->fetchAll();
    }
    
    foreach ($users as $user) {
        echo "<div class='debug'>";
        echo "<h4>👤 " . htmlspecialchars($user['name']) . " (" . htmlspecialchars($user['email']) . ")</h4>";
        echo "<p><strong>Hash en base:</strong> <code>" . htmlspecialchars($user['password']) . "</code></p>";
        echo "<p><strong>Longueur hash:</strong> " . strlen($user['password']) . " caractères</p>";
        echo "</div>";
    }
    
    // 2. Test exact de l'authentification
    echo "<h2>2. Test d'authentification exact</h2>";
    
    foreach ($users as $user) {
        echo "<h3>Test pour: " . htmlspecialchars($user['email']) . "</h3>";
        
        $test_password = "password";
        
        echo "<div class='debug'>";
        echo "<p><strong>Mot de passe testé:</strong> '$test_password'</p>";
        echo "<p><strong>Hash en base:</strong> " . htmlspecialchars($user['password']) . "</p>";
        
        // Test password_verify
        if (password_verify($test_password, $user['password'])) {
            echo "<p class='success'>✅ password_verify FONCTIONNE</p>";
        } else {
            echo "<p class='error'>❌ password_verify ÉCHOUE</p>";
            
            // Tests de debug
            echo "<p><strong>Tests alternatifs:</strong></p>";
            if (md5($test_password) === $user['password']) {
                echo "<p class='info'>ℹ️ Hash MD5 détecté</p>";
            } elseif ($test_password === $user['password']) {
                echo "<p class='info'>ℹ️ Mot de passe en clair détecté</p>";
            } else {
                echo "<p class='error'>❌ Aucune correspondance trouvée</p>";
            }
        }
        echo "</div>";
    }
    
    // 3. Simulation exacte du code de login.php
    echo "<h2>3. Simulation du code login.php</h2>";
    
    $test_email = "adilikarim@gmail.com";
    $test_password = "password";
    
    echo "<div class='debug'>";
    echo "<p><strong>Email testé:</strong> $test_email</p>";
    echo "<p><strong>Password testé:</strong> $test_password</p>";
    
    // Reproduire exactement la logique de login.php
    $stmt = $pdo->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
    $stmt->execute([$test_email]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "<p class='success'>✅ Utilisateur trouvé</p>";
        echo "<p>Hash récupéré: <code>" . htmlspecialchars($user['password']) . "</code></p>";
        
        if (password_verify($test_password, $user['password'])) {
            echo "<p class='success'>🎉 AUTHENTIFICATION RÉUSSIE !</p>";
            echo "<p>La connexion devrait fonctionner avec ces identifiants.</p>";
        } else {
            echo "<p class='error'>❌ AUTHENTIFICATION ÉCHOUÉE</p>";
            echo "<p class='error'>Le hash ne correspond pas au mot de passe 'password'</p>";
        }
    } else {
        echo "<p class='error'>❌ Utilisateur non trouvé</p>";
    }
    echo "</div>";
    
    // 4. Correction immédiate si nécessaire
    if (isset($_POST['corriger_auth'])) {
        echo "<h2>🔧 Correction en cours...</h2>";
        
        $correct_hash = password_hash("password", PASSWORD_DEFAULT);
        echo "<p>Nouveau hash généré: <code>$correct_hash</code></p>";
        
        // Mettre à jour tous les utilisateurs
        $stmt = $pdo->prepare("UPDATE users SET password = ?");
        $stmt->execute([$correct_hash]);
        
        echo "<p class='success'>✅ Tous les mots de passe ont été corrigés !</p>";
        echo "<p><a href='login.php' style='padding:10px 20px;background:#007bff;color:white;text-decoration:none;border-radius:5px;'>🔗 Tester la connexion</a></p>";
    } else {
        echo "<h2>🔧 Action de correction</h2>";
        echo "<form method='POST'>";
        echo "<button type='submit' name='corriger_auth' style='padding:15px 30px;background:#dc3545;color:white;border:none;border-radius:5px;font-size:16px;cursor:pointer;'>🔧 CORRIGER L'AUTHENTIFICATION</button>";
        echo "</form>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

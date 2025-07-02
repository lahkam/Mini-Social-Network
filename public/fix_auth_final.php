<?php
/**
 * CORRECTION FORCÉE MOTS DE PASSE
 * ==============================
 * Force la correction des mots de passe pour "password"
 */

try {
    $pdo = new PDO('mysql:host=db;port=3306;dbname=laravel_db_new', 'laravel_user', 'secret');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>🔧 Correction Forcée - Mots de Passe</h1>";
    echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;} .success{color:#28a745;} .error{color:#dc3545;}</style>";
    
    // 1. Supprimer TOUS les utilisateurs existants
    $pdo->exec("DELETE FROM users");
    echo "<p class='success'>✅ Tous les anciens utilisateurs supprimés</p>";
    
    // 2. Créer un hash parfait pour "password"
    $perfect_hash = password_hash("password", PASSWORD_DEFAULT);
    echo "<p>🔐 Hash généré pour 'password': <code>$perfect_hash</code></p>";
    
    // 3. Créer les utilisateurs de test avec ce hash
    $users = [
        ['name' => 'Adil Karim', 'email' => 'adilikarim@gmail.com'],
        ['name' => 'Test User', 'email' => 'test@example.com']
    ];
    
    foreach ($users as $user_data) {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmt->execute([$user_data['name'], $user_data['email'], $perfect_hash]);
        echo "<p class='success'>✅ Utilisateur créé: " . $user_data['email'] . "</p>";
    }
    
    // 4. Test immédiat
    echo "<h2>🧪 Test Immédiat</h2>";
    foreach ($users as $user_data) {
        $stmt = $pdo->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
        $stmt->execute([$user_data['email']]);
        $user = $stmt->fetch();
        
        if ($user && password_verify("password", $user['password'])) {
            echo "<p class='success'>✅ " . $user_data['email'] . " : CONNEXION OK</p>";
        } else {
            echo "<p class='error'>❌ " . $user_data['email'] . " : PROBLÈME</p>";
        }
    }
    
    echo "<h2>🎉 CORRECTION TERMINÉE</h2>";
    echo "<p>Les comptes sont maintenant parfaitement configurés.</p>";
    echo "<p><strong>Identifiants de test:</strong></p>";
    echo "<ul>";
    echo "<li>Email: <code>adilikarim@gmail.com</code> | Mot de passe: <code>password</code></li>";
    echo "<li>Email: <code>test@example.com</code> | Mot de passe: <code>password</code></li>";
    echo "</ul>";
    
    echo "<p><a href='login.php' style='padding:15px 30px;background:#007bff;color:white;text-decoration:none;border-radius:5px;font-size:16px;'>🔗 TESTER LA CONNEXION</a></p>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

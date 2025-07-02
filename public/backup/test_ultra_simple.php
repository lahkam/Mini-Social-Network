<?php
/**
 * TEST ULTRA SIMPLE
 * =================
 * Test minimal pour identifier le problème d'auth
 */

// Connexion DB
$pdo = new PDO('mysql:host=db;port=3306;dbname=laravel_db_new', 'laravel_user', 'secret');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "<h1>🔍 Test Ultra Simple</h1>";
echo "<style>body{font-family:Arial;padding:20px;}</style>";

// Vérifier si des utilisateurs existent
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
$count = $stmt->fetch()['count'];

echo "<p><strong>Nombre d'utilisateurs en base:</strong> $count</p>";

if ($count == 0) {
    echo "<p style='color:red;'>❌ PROBLÈME: Aucun utilisateur en base !</p>";
    echo "<p>Je vais créer les utilisateurs maintenant...</p>";
    
    $hash = password_hash("password", PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
    $stmt->execute(['Adil Karim', 'adilikarim@gmail.com', $hash]);
    $stmt->execute(['Test User', 'test@example.com', $hash]);
    
    echo "<p style='color:green;'>✅ Utilisateurs créés !</p>";
}

// Test de connexion direct
echo "<h2>Test de connexion direct</h2>";

$test_email = "adilikarim@gmail.com";
$test_password = "password";

$stmt = $pdo->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
$stmt->execute([$test_email]);
$user = $stmt->fetch();

if ($user) {
    echo "<p>✅ Utilisateur trouvé: " . $user['name'] . "</p>";
    
    if (password_verify($test_password, $user['password'])) {
        echo "<p style='color:green;font-size:20px;'>🎉 CONNEXION OK !</p>";
        echo "<p>Le mot de passe 'password' fonctionne pour " . $test_email . "</p>";
    } else {
        echo "<p style='color:red;font-size:20px;'>❌ CONNEXION KO !</p>";
        echo "<p>Hash en base: " . $user['password'] . "</p>";
        echo "<p>Je vais corriger cela...</p>";
        
        $new_hash = password_hash("password", PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$new_hash, $test_email]);
        
        echo "<p style='color:green;'>✅ Hash corrigé !</p>";
    }
} else {
    echo "<p style='color:red;'>❌ Utilisateur non trouvé !</p>";
}

echo "<hr>";
echo "<h3>🔗 Test maintenant</h3>";
echo "<p><a href='login.php' style='padding:10px 20px;background:#007bff;color:white;text-decoration:none;border-radius:5px;'>Aller à la page de connexion</a></p>";
echo "<p><strong>Utilisez:</strong> adilikarim@gmail.com / password</p>";
?>

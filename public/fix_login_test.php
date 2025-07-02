<?php
/**
 * FIX LOGIN - UTILISATEUR TEST
 * ===========================
 * Corrige le problème de connexion pour test@example.com
 */

// Script accessible via le navigateur pour corriger le mot de passe
try {
    $pdo = new PDO('mysql:host=db;port=3306;dbname=laravel_db_new', 'laravel_user', 'secret');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h1>🔧 Fix Login - test@example.com</h1>";
    echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;} .success{color:#28a745;} .error{color:#dc3545;} .info{color:#17a2b8;}</style>";

    $stmt = $pdo->prepare("SELECT id, email, password FROM users WHERE email = ?");
    $stmt->execute(['test@example.com']);
    $user = $stmt->fetch();

    if ($user) {
        $new_hash = password_hash("password", PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$new_hash, 'test@example.com']);
        echo "<p class='success'>✅ Mot de passe corrigé avec succès</p>";
    } else {
        echo "<p class='error'>❌ Utilisateur test@example.com non trouvé</p>";
    }

    // Corriger le mot de passe pour adilikarim@gmail.com
    $stmt = $pdo->prepare("SELECT id, email, password FROM users WHERE email = ?");
    $stmt->execute(['adilikarim@gmail.com']);
    $user = $stmt->fetch();

    if ($user) {
        $new_hash = password_hash("password", PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$new_hash, 'adilikarim@gmail.com']);
        echo "<p class='success'>✅ Mot de passe corrigé avec succès pour adilikarim@gmail.com</p>";
    } else {
        echo "<p class='error'>❌ Utilisateur adilikarim@gmail.com non trouvé</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

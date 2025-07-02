<?php
/**
 * CORRECTION DÉFINITIVE - CONNEXION
 * =================================
 * Script pour corriger une fois pour toutes le problème de connexion
 */

session_start();

try {
    $pdo = new PDO('mysql:host=db;port=3306;dbname=laravel_db_new', 'laravel_user', 'secret');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<!DOCTYPE html>
    <html lang='fr'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Correction Définitive</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
        <style>
            body { background: #f8f9fa; padding: 20px; }
            .card { margin: 20px 0; }
        </style>
    </head>
    <body>
    <div class='container'>
        <h1 class='text-center mb-4'>🔧 Correction Définitive - Connexion</h1>";
    
    // ÉTAPE 1: Diagnostic
    echo "<div class='card'>
        <div class='card-header bg-info text-white'>
            <h3>📊 DIAGNOSTIC</h3>
        </div>
        <div class='card-body'>";
    
    // Vérifier les utilisateurs existants
    $stmt = $pdo->query("SELECT id, name, email, password FROM users");
    $users = $stmt->fetchAll();
    
    echo "<h4>Utilisateurs trouvés :</h4>";
    if ($users) {
        echo "<table class='table table-sm'>";
        echo "<tr><th>ID</th><th>Nom</th><th>Email</th><th>Hash Password</th><th>Test</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td>" . htmlspecialchars($user['name']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td><small>" . substr($user['password'], 0, 30) . "...</small></td>";
            echo "<td>";
            if (password_verify("password", $user['password'])) {
                echo "<span class='badge bg-success'>✅ OK</span>";
            } else {
                echo "<span class='badge bg-danger'>❌ KO</span>";
            }
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='alert alert-warning'>Aucun utilisateur trouvé</div>";
    }
    
    echo "</div></div>";
    
    // ÉTAPE 2: Correction
    if (isset($_POST['corriger'])) {
        echo "<div class='card'>
            <div class='card-header bg-success text-white'>
                <h3>🔧 CORRECTION EN COURS...</h3>
            </div>
            <div class='card-body'>";
        
        // 1. Nettoyer tous les utilisateurs
        $pdo->exec("DELETE FROM users");
        echo "<div class='alert alert-info'>🗑️ Tous les anciens utilisateurs ont été supprimés</div>";
        
        // 2. Créer les comptes de test avec le bon hash
        $hash_correct = password_hash("password", PASSWORD_DEFAULT);
        echo "<div class='alert alert-info'>🔐 Hash bcrypt généré : <code>" . $hash_correct . "</code></div>";
        
        $comptes_test = [
            ['name' => 'Adil Karim', 'email' => 'adilikarim@gmail.com'],
            ['name' => 'Test User', 'email' => 'test@example.com']
        ];
        
        foreach ($comptes_test as $compte) {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
            $stmt->execute([$compte['name'], $compte['email'], $hash_correct]);
            echo "<div class='alert alert-success'>✅ Compte créé : " . $compte['email'] . "</div>";
        }
        
        // 3. Test final
        echo "<h4>🧪 Test final :</h4>";
        foreach ($comptes_test as $compte) {
            $stmt = $pdo->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
            $stmt->execute([$compte['email']]);
            $user = $stmt->fetch();
            
            if ($user && password_verify("password", $user['password'])) {
                echo "<div class='alert alert-success'>✅ " . $compte['email'] . " : Connexion OK avec 'password'</div>";
            } else {
                echo "<div class='alert alert-danger'>❌ " . $compte['email'] . " : Problème de connexion</div>";
            }
        }
        
        echo "<div class='alert alert-success'><strong>🎉 CORRECTION TERMINÉE !</strong></div>";
        echo "<div class='text-center'>
            <a href='login.php' class='btn btn-primary btn-lg'>🔗 Tester la connexion</a>
            <a href='app.php' class='btn btn-secondary'>📱 Aller à l'app</a>
        </div>";
        
        echo "</div></div>";
        
    } else {
        // Afficher le bouton de correction
        echo "<div class='card'>
            <div class='card-header bg-warning'>
                <h3>⚠️ CORRECTION NÉCESSAIRE</h3>
            </div>
            <div class='card-body'>
                <p>Pour corriger définitivement le problème de connexion, cliquez sur le bouton ci-dessous.</p>
                <p><strong>Cette action va :</strong></p>
                <ul>
                    <li>Supprimer tous les utilisateurs existants</li>
                    <li>Recréer les comptes de test : <code>adilikarim@gmail.com</code> et <code>test@example.com</code></li>
                    <li>Utiliser le mot de passe <code>password</code> avec un hash bcrypt sécurisé</li>
                    <li>Vérifier que la connexion fonctionne</li>
                </ul>
                <form method='POST' class='text-center'>
                    <button type='submit' name='corriger' class='btn btn-danger btn-lg'>
                        🔧 CORRIGER MAINTENANT
                    </button>
                </form>
            </div>
        </div>";
    }
    
    echo "</div></body></html>";
    
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>❌ Erreur de connexion à la base : " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>

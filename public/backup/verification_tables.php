<?php
/**
 * VÉRIFICATION FINALE DES TABLES
 * ==============================
 * S'assure que toutes les tables sont bien créées et structurées
 */

try {
    $pdo = new PDO('mysql:host=db;port=3306;dbname=laravel_db_new', 'laravel_user', 'secret');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>🔍 Vérification Finale des Tables</h1>";
    echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;} .success{color:#28a745;} .error{color:#dc3545;} .info{color:#17a2b8;} table{width:100%;border-collapse:collapse;margin:10px 0;} th,td{border:1px solid #ddd;padding:8px;} th{background:#f2f2f2;}</style>";
    
    // Tables requises pour l'application
    $tables_requises = [
        'users' => "CREATE TABLE users (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )",
        'posts' => "CREATE TABLE posts (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            content TEXT NOT NULL,
            image_url VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )",
        'invitations' => "CREATE TABLE invitations (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            sender_id BIGINT UNSIGNED NOT NULL,
            invitee_id BIGINT UNSIGNED NOT NULL,
            status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (invitee_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_invitation (sender_id, invitee_id)
        )",
        'friends' => "CREATE TABLE friends (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            friend_id BIGINT UNSIGNED NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (friend_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE KEY unique_friendship (user_id, friend_id)
        )"
    ];
    
    echo "<h2>📊 État des Tables</h2>";
    
    foreach ($tables_requises as $table_name => $create_sql) {
        echo "<h3>Table : $table_name</h3>";
        
        try {
            // Vérifier si la table existe
            $stmt = $pdo->query("SHOW TABLES LIKE '$table_name'");
            $table_exists = $stmt->fetch();
            
            if ($table_exists) {
                echo "<p class='success'>✅ Table existe</p>";
                
                // Afficher la structure
                $stmt = $pdo->query("DESCRIBE $table_name");
                echo "<table>";
                echo "<tr><th>Champ</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th><th>Extra</th></tr>";
                while ($row = $stmt->fetch()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Default']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
                
                // Compter les entrées
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table_name");
                $count = $stmt->fetch()['count'];
                echo "<p class='info'>📊 Nombre d'entrées : $count</p>";
                
            } else {
                echo "<p class='error'>❌ Table manquante</p>";
                
                if (isset($_POST['creer_tables'])) {
                    try {
                        $pdo->exec($create_sql);
                        echo "<p class='success'>✅ Table créée avec succès</p>";
                    } catch (Exception $e) {
                        echo "<p class='error'>❌ Erreur création : " . htmlspecialchars($e->getMessage()) . "</p>";
                    }
                }
            }
        } catch (Exception $e) {
            echo "<p class='error'>❌ Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        
        echo "<hr>";
    }
    
    // Bouton pour créer les tables manquantes
    echo "<h2>🔧 Actions</h2>";
    if (!isset($_POST['creer_tables'])) {
        echo "<form method='POST'>";
        echo "<button type='submit' name='creer_tables' style='padding:15px 30px;background:#28a745;color:white;border:none;border-radius:5px;font-size:16px;cursor:pointer;'>🛠️ Créer les tables manquantes</button>";
        echo "</form>";
    }
    
    echo "<div style='margin-top:30px;'>";
    echo "<h3>🔗 Navigation</h3>";
    echo "<a href='login.php' style='padding:10px 20px;background:#007bff;color:white;text-decoration:none;border-radius:5px;margin:5px;display:inline-block;'>🔗 Page de connexion</a>";
    echo "<a href='app.php' style='padding:10px 20px;background:#28a745;color:white;text-decoration:none;border-radius:5px;margin:5px;display:inline-block;'>📱 Application</a>";
    echo "<a href='nettoyage_final.php' style='padding:10px 20px;background:#ffc107;color:black;text-decoration:none;border-radius:5px;margin:5px;display:inline-block;'>🧹 Nettoyage</a>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<p class='error'>❌ Erreur de connexion : " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

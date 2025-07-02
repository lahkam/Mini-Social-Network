<?php
/**
 * NETTOYAGE FINAL
 * ===============
 * Supprime tous les fichiers de test et de debug pour ne garder que l'essentiel
 */

$files_to_remove = [
    'super_fix.php',
    'solution_finale.php',
    'solution_md5.php', 
    'correction_finale.php',
    'final_password_fix.php',
    'test_password_ultra.php',
    'debug_invitations.php',
    'auto_login.php',
    'test_ai.php',
    'test_db_fix.php',
    'test_debug.php',
    'test_invitation.php',
    'ultra-debug.php',
    'quick_fix.php',
    'fix_database.php',
    'fix_passwords.php',
    'fix_tables.php',
    'test_send_invite.html',
    'test_ia_amelioree.html',
    'test_platform.php',
    'social_platform_clean.php',
    'diagnostic_invitations.php',
    'check_db.php',
    'debug_tables.php',
    'demo_interface.html',
    'diagnostic_final.php',
    'correction_definitive.php',
    'test_rapide.php'
];

echo "<h1>🧹 Nettoyage Final</h1>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;} .success{color:#28a745;} .error{color:#dc3545;} .info{color:#17a2b8;}</style>";

if (isset($_POST['nettoyer'])) {
    echo "<h2>🔄 Suppression en cours...</h2>";
    
    $removed = 0;
    $not_found = 0;
    
    foreach ($files_to_remove as $file) {
        $filepath = __DIR__ . '/' . $file;
        if (file_exists($filepath)) {
            if (unlink($filepath)) {
                echo "<p class='success'>✅ Supprimé : $file</p>";
                $removed++;
            } else {
                echo "<p class='error'>❌ Erreur suppression : $file</p>";
            }
        } else {
            echo "<p class='info'>ℹ️ Inexistant : $file</p>";
            $not_found++;
        }
    }
    
    echo "<h3>📊 Résumé</h3>";
    echo "<p>Fichiers supprimés : <strong>$removed</strong></p>";
    echo "<p>Fichiers non trouvés : <strong>$not_found</strong></p>";
    
    echo "<div style='background:#d4edda;border:1px solid #c3e6cb;padding:15px;border-radius:5px;margin:20px 0;'>";
    echo "<h3>🎉 Nettoyage terminé !</h3>";
    echo "<p>L'application est maintenant propre. Fichiers conservés :</p>";
    echo "<ul>";
    echo "<li><strong>login.php</strong> - Page de connexion</li>";
    echo "<li><strong>app.php</strong> - Application principale (dashboard)</li>";
    echo "<li><strong>register.php</strong> - Page d'inscription</li>";
    echo "<li><strong>logout.php</strong> - Déconnexion</li>";
    echo "<li><strong>index.php</strong> - Page d'accueil</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<p><a href='login.php' style='padding:10px 20px;background:#007bff;color:white;text-decoration:none;border-radius:5px;margin:5px;'>🔗 Aller à la page de connexion</a></p>";
    echo "<p><a href='app.php' style='padding:10px 20px;background:#28a745;color:white;text-decoration:none;border-radius:5px;margin:5px;'>📱 Aller à l'application</a></p>";
    
} else {
    echo "<h2>⚠️ Attention</h2>";
    echo "<p>Cette action va supprimer <strong>" . count($files_to_remove) . " fichiers</strong> de test et de debug.</p>";
    echo "<p>Seuls les fichiers essentiels seront conservés :</p>";
    echo "<ul>";
    echo "<li>login.php</li>";
    echo "<li>app.php</li>";
    echo "<li>register.php</li>";
    echo "<li>logout.php</li>";
    echo "<li>index.php</li>";
    echo "</ul>";
    
    echo "<form method='POST'>";
    echo "<button type='submit' name='nettoyer' style='padding:15px 30px;background:#dc3545;color:white;border:none;border-radius:5px;font-size:16px;cursor:pointer;'>🧹 NETTOYER MAINTENANT</button>";
    echo "</form>";
}
?>

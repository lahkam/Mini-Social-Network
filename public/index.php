<?php
/**
 * PAGE D'ACCUEIL - APPLICATION SOCIALE LARAVEL DÉBUTANT
 * =====================================================
 * 
 * Point d'entrée de l'application sociale éducative
 * Redirection automatique vers la page appropriée
 */

session_start();

// Si l'utilisateur est connecté, aller à l'app
if (isset($_SESSION['user_id'])) {
    header('Location: app.php');
    exit;
}

// Sinon, aller à la page de connexion
header('Location: login.php');
exit;
?>

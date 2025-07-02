<?php
/**
 * DÉCONNEXION - APPLICATION SOCIALE LARAVEL DÉBUTANT
 * ==================================================
 * 
 * Script simple pour déconnecter l'utilisateur
 * et le rediriger vers la page de connexion
 */

session_start();

// Détruire toutes les données de session
session_destroy();

// Rediriger vers la page de connexion
header('Location: login.php');
exit;
?>

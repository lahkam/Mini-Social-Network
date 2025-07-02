<?php


// Démarrage de la session pour gérer la connexion utilisateur
session_start();

// Configuration pour le développement (affichage des erreurs)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Vérification de la connexion utilisateur
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit('Redirection vers la page de connexion');
}

// CONFIGURATION BASE DE DONNÉES
// =============================
try {
    // Connexion PDO à MySQL via Docker
    $pdo = new PDO(
        'mysql:host=db;port=3306;dbname=laravel_db_new', 
        'laravel_user', 
        'secret'
    );
    // Mode d'erreur pour le debugging
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('❌ Erreur de connexion à la base de données : ' . $e->getMessage());
}

// Variable pour les messages à l'utilisateur
$message = '';

// FONCTION IA AVEC API GEMINI
// ==========================
/**
 * Génère du contenu via l'API Gemini
 *
 * @param string $prompt Le texte d'entrée de l'utilisateur
 * @return string Le contenu généré ou un message d'erreur
 */
function genererContenuIA($prompt) {
    $apiKey = 'AIzaSyAX-FtB6rbzEy6H5hbJ3II1Mll7of_ojP8'; // Votre clé API Gemini
    
    // Fallback suggestions en cas d'échec de l'API
    $suggestions = [
        'motivation' => 'Restez motivé et persévérez dans vos études ! Chaque effort que vous investissez aujourd\'hui sera récompensé demain.',
        'étude' => 'Les études demandent de la discipline et de l\'organisation. Planifiez vos révisions et prenez des pauses régulières.',
        'programmation' => 'La programmation est un art qui se perfectionne avec la pratique. Commencez par maîtriser les fondamentaux.',
    ];
    
    try {
        // Préparer les données pour l'API Gemini
        $data = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => "Génère un post inspirant et motivant sur le thème : $prompt. Le post doit faire environ 200-250 caractères et être adapté à un réseau social étudiant."
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'maxOutputTokens' => 100,
                'temperature' => 0.7
            ]
        ];
        
        // URL de l'API Gemini
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=" . $apiKey;
        
        // Initialiser cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        // Exécuter la requête
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200 && $response) {
            $responseData = json_decode($response, true);
            
            if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                $generatedText = trim($responseData['candidates'][0]['content']['parts'][0]['text']);
                
                // Limiter à 280 caractères pour respecter les contraintes
                if (strlen($generatedText) > 280) {
                    $generatedText = substr($generatedText, 0, 277) . '...';
                }
                
                error_log('Contenu généré par l\'API Gemini : ' . $generatedText);
                return $generatedText;
            }
        }
        
        // En cas d'échec de l'API, utiliser les suggestions prédéfinies
        error_log('Échec de l\'API Gemini, utilisation du fallback pour : ' . $prompt);
        
    } catch (Exception $e) {
        error_log('Erreur lors de l\'appel à l\'API Gemini : ' . $e->getMessage());
    }
    
    // Fallback intelligent basé sur le prompt
    $promptLower = strtolower(trim($prompt));
    
    if (isset($suggestions[$promptLower])) {
        return $suggestions[$promptLower];
    }
    
    // Génération contextuelle de fallback
    if (strpos($promptLower, 'motivation') !== false) {
        return "La motivation est le moteur de toute réussite. Gardez vos objectifs en tête et avancez pas à pas vers vos rêves !";
    } elseif (strpos($promptLower, 'étude') !== false || strpos($promptLower, 'étudier') !== false) {
        return "Les études sont un investissement dans votre futur. Organisez votre temps et créez un environnement propice à l'apprentissage.";
    } elseif (strpos($promptLower, 'code') !== false || strpos($promptLower, 'program') !== false) {
        return "Le code est un langage créatif. Pratiquez régulièrement et n'ayez pas peur de faire des erreurs, elles font partie de l'apprentissage.";
    } else {
        return "Voici une réflexion inspirée par « $prompt » : Votre idée mérite d'être explorée. Prenez le temps de réfléchir et d'agir pour atteindre vos objectifs !";
    }
}

// TRAITEMENT DES ACTIONS UTILISATEUR
// ==================================
if ($_POST) {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        
        // ACTION : Créer un nouveau post
        case 'creer_post':
            $titre = trim($_POST['titre'] ?? '');
            $contenu = trim($_POST['contenu'] ?? '');
            $type_post = $_POST['type_post'] ?? 'normal';
            $prompt_ia = trim($_POST['prompt_ia'] ?? '');
            
            // Si c'est un post généré par IA
            if ($type_post === 'ia' && !empty($prompt_ia)) {
                $contenu = genererContenuIA($prompt_ia);
                $titre = $titre ?: "Post généré par IA";
                $type_post = 'ai_generated'; // Convertir pour la base de données
            }
            
            // Validation des données
            if (empty($titre) || empty($contenu)) {
                $message = '<div class="alert alert-warning">⚠️ Veuillez remplir le titre et le contenu</div>';
                break;
            }
            
            // Limitation de caractères (comme Twitter)
            if (strlen($contenu) > 280) {
                $message = '<div class="alert alert-danger">❌ Le contenu dépasse 280 caractères</div>';
                break;
            }
            
            try {
                // Insertion en base de données
                $sql = "INSERT INTO posts (title, content, user_id, type, ai_prompt, created_at) 
                        VALUES (?, ?, ?, ?, ?, NOW())";
                $stmt = $pdo->prepare($sql);
                $resultat = $stmt->execute([
                    $titre, 
                    $contenu, 
                    $_SESSION['user_id'], 
                    $type_post, 
                    $prompt_ia
                ]);
                
                if ($resultat) {
                    $message = '<div class="alert alert-success">✅ Post créé avec succès !</div>';
                } else {
                    $message = '<div class="alert alert-danger">❌ Erreur lors de la création</div>';
                }
            } catch (Exception $e) {
                $message = '<div class="alert alert-danger">❌ Erreur technique : ' . $e->getMessage() . '</div>';
            }
            break;
            
        // ACTION : Envoyer une invitation d'amitié
        case 'envoyer_invitation':
            $email_ami = trim($_POST['email_ami'] ?? '');
            $message_invitation = trim($_POST['message_invitation'] ?? '');
            
            if (empty($email_ami)) {
                $message = '<div class="alert alert-warning">⚠️ Veuillez saisir un email</div>';
                break;
            }
            
            try {
                // Vérifier si l'utilisateur existe
                $stmt = $pdo->prepare("SELECT id, name FROM users WHERE email = ?");
                $stmt->execute([$email_ami]);
                $ami = $stmt->fetch();
                
                if (!$ami) {
                    $message = '<div class="alert alert-danger">❌ Utilisateur non trouvé</div>';
                    break;
                }
                
                // Vérifier si une invitation existe déjà
                $stmt = $pdo->prepare("SELECT id FROM invitations WHERE sender_id = ? AND invitee_id = ? AND status = 'pending'");
                $stmt->execute([$_SESSION['user_id'], $ami['id']]);
                if ($stmt->fetch()) {
                    $message = '<div class="alert alert-info">ℹ️ Invitation déjà envoyée à cette personne</div>';
                    break;
                }
                
                // Créer l'invitation
                $stmt = $pdo->prepare("INSERT INTO invitations (sender_id, invitee_id, message, status, created_at) VALUES (?, ?, ?, 'pending', NOW())");
                if ($stmt->execute([$_SESSION['user_id'], $ami['id'], $message_invitation])) {
                    $message = '<div class="alert alert-success">✅ Invitation envoyée à ' . htmlspecialchars($ami['name']) . ' !</div>';
                }
            } catch (Exception $e) {
                $message = '<div class="alert alert-danger">❌ Erreur : ' . $e->getMessage() . '</div>';
            }
            break;
            
        // ACTION : Répondre à une invitation (accepter/refuser)
        case 'repondre_invitation':
            $invitation_id = intval($_POST['invitation_id'] ?? 0);
            $reponse = $_POST['reponse'] ?? '';
            
            if (!$invitation_id || !in_array($reponse, ['accepter', 'refuser'])) {
                $message = '<div class="alert alert-warning">⚠️ Paramètres invalides</div>';
                break;
            }
            
            try {
                $statut = ($reponse === 'accepter') ? 'accepted' : 'declined';
                
                // Mettre à jour le statut de l'invitation
                $stmt = $pdo->prepare("UPDATE invitations SET status = ? WHERE id = ? AND invitee_id = ?");
                $stmt->execute([$statut, $invitation_id, $_SESSION['user_id']]);
                
                if ($reponse === 'accepter') {
                    // Récupérer l'expéditeur de l'invitation
                    $stmt = $pdo->prepare("SELECT sender_id FROM invitations WHERE id = ?");
                    $stmt->execute([$invitation_id]);
                    $sender_id = $stmt->fetchColumn();
                    
                    if ($sender_id) {
                        // Créer la relation d'amitié dans les deux sens
                        $stmt = $pdo->prepare("INSERT IGNORE INTO friends (user_id, friend_id, created_at) VALUES (?, ?, NOW()), (?, ?, NOW())");
                        $stmt->execute([$_SESSION['user_id'], $sender_id, $sender_id, $_SESSION['user_id']]);
                        $message = '<div class="alert alert-success">✅ Vous êtes maintenant amis !</div>';
                    }
                } else {
                    $message = '<div class="alert alert-info">ℹ️ Invitation refusée</div>';
                }
            } catch (Exception $e) {
                $message = '<div class="alert alert-danger">❌ Erreur : ' . $e->getMessage() . '</div>';
            }
            break;
        
        // ACTION : Générer un contenu via l'IA
        case 'generer_ia':
            // Vérification de la session utilisateur
            if (!isset($_SESSION['user_id'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Session invalide. Veuillez vous reconnecter.']);
                exit;
            }

            $prompt = trim($_POST['prompt'] ?? '');

            if (empty($prompt)) {
                error_log('Prompt vide reçu pour la génération IA.');
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Le prompt est vide.']);
                exit;
            }

            try {
                $contenu = genererContenuIA($prompt);
                header('Content-Type: application/json');
                if (strpos($contenu, '❌ Erreur IA') === 0) {
                    error_log('Erreur IA détectée : ' . $contenu);
                    echo json_encode(['success' => false, 'message' => $contenu]);
                } else {
                    error_log('Contenu généré avec succès : ' . $contenu);
                    echo json_encode(['success' => true, 'content' => $contenu]);
                }
            } catch (Exception $e) {
                error_log('Exception lors de la génération IA : ' . $e->getMessage());
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la génération : ' . $e->getMessage()]);
            }
            exit;
        
        // ACTION : Supprimer un post
        case 'supprimer_post':
            $post_id = intval($_POST['post_id'] ?? 0);

            if (!$post_id) {
                $message = '<div class="alert alert-warning">⚠️ ID de post invalide</div>';
                break;
            }

            try {
                $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
                $stmt->execute([$post_id, $_SESSION['user_id']]);

                if ($stmt->rowCount() > 0) {
                    $message = '<div class="alert alert-success">✅ Post supprimé avec succès</div>';
                } else {
                    $message = '<div class="alert alert-danger">❌ Erreur : Post introuvable ou non autorisé</div>';
                }
            } catch (Exception $e) {
                $message = '<div class="alert alert-danger">❌ Erreur technique : ' . $e->getMessage() . '</div>';
            }
            break;

        // ACTION : Modifier un post
        case 'modifier_post':
            $post_id = intval($_POST['post_id'] ?? 0);
            $nouveau_titre = trim($_POST['nouveau_titre'] ?? '');
            $nouveau_contenu = trim($_POST['nouveau_contenu'] ?? '');

            if (!$post_id || empty($nouveau_titre) || empty($nouveau_contenu)) {
                $message = '<div class="alert alert-warning">⚠️ Paramètres invalides</div>';
                break;
            }

            try {
                $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ? AND user_id = ?");
                $stmt->execute([$nouveau_titre, $nouveau_contenu, $post_id, $_SESSION['user_id']]);

                if ($stmt->rowCount() > 0) {
                    $message = '<div class="alert alert-success">✅ Post modifié avec succès</div>';
                } else {
                    $message = '<div class="alert alert-danger">❌ Erreur : Post introuvable ou non autorisé</div>';
                }
            } catch (Exception $e) {
                $message = '<div class="alert alert-danger">❌ Erreur technique : ' . $e->getMessage() . '</div>';
            }
            break;
    }
}

// RÉCUPÉRATION DES DONNÉES POUR L'AFFICHAGE
// =========================================

// Informations de l'utilisateur connecté
$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$utilisateur_actuel = $stmt->fetch();

// Statistiques pour le tableau de bord
$stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$nombre_posts = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM friends WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$nombre_amis = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM invitations WHERE invitee_id = ? AND status = 'pending'");
$stmt->execute([$_SESSION['user_id']]);
$invitations_en_attente = $stmt->fetchColumn();

// Invitations reçues en attente
$stmt = $pdo->prepare("
    SELECT i.*, u.name as nom_expediteur, u.email as email_expediteur 
    FROM invitations i 
    JOIN users u ON i.sender_id = u.id 
    WHERE i.invitee_id = ? AND i.status = 'pending'
    ORDER BY i.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$invitations_recues = $stmt->fetchAll();

// Liste des amis
$stmt = $pdo->prepare("
    SELECT u.name, u.email 
    FROM friends f 
    JOIN users u ON f.friend_id = u.id 
    WHERE f.user_id = ?
    ORDER BY u.name
");
$stmt->execute([$_SESSION['user_id']]);
$liste_amis = $stmt->fetchAll();

// Posts récents (filtrés par utilisateur et amis)
$stmt = $pdo->prepare("
    SELECT p.*, u.name as nom_auteur 
    FROM posts p 
    JOIN users u ON p.user_id = u.id 
    LEFT JOIN friends f ON f.friend_id = u.id AND f.user_id = ? 
    LEFT JOIN invitations i ON i.sender_id = u.id AND i.invitee_id = ? AND i.status = 'accepted' 
    WHERE p.user_id = ? OR f.user_id IS NOT NULL OR i.invitee_id IS NOT NULL 
    ORDER BY p.created_at DESC 
    LIMIT 10
");
$stmt->execute([$_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id']]);
$posts_recents = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réseau Social</title>
    <!-- Bootstrap pour le style (framework CSS populaire) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /* STYLES CSS PERSONNALISÉS */
        body {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .container-principal {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin: 20px auto;
            padding: 30px;
        }
        
        .en-tete {
            background: linear-gradient(135deg, #1976d2, #42a5f5);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .carte-statistique {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
            border-left: 4px solid #1976d2;
        }
        
        .nombre-stat {
            font-size: 2rem;
            font-weight: bold;
            color: #1976d2;
        }
        
        .carte-post {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #4caf50;
        }
        
        .carte-invitation {
            background: #fff3e0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #ff9800;
        }
        
        .bouton-principal {
            background: linear-gradient(135deg, #1976d2, #42a5f5);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            color: white;
            font-weight: 600;
        }
        
        .bouton-principal:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(25,118,210,0.3);
        }
        
        .badge-ia {
            background: linear-gradient(135deg, #e91e63, #f06292);
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
        }
        
        .compteur-caracteres {
            font-size: 0.9rem;
            color: #666;
        }
        .compteur-caracteres.attention {
            color: #ff5722;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="container-principal">
            
            <!-- EN-TÊTE DE L'APPLICATION -->
            <div class="en-tete">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2><i class="fas fa-graduation-cap"></i> Réseau Social</h2>
                        <p class="mb-0">Bienvenue, <strong><?= htmlspecialchars($utilisateur_actuel['name']) ?></strong> !</p>
                        <small><?= htmlspecialchars($utilisateur_actuel['email']) ?></small>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="logout.php" class="btn btn-light">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </a>
                    </div>
                </div>
            </div>

            <!-- AFFICHAGE DES MESSAGES -->
            <?= $message ?>

            <!-- STATISTIQUES DU TABLEAU DE BORD -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="carte-statistique">
                        <div class="nombre-stat"><?= $nombre_posts ?></div>
                        <div>Posts créés</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="carte-statistique">
                        <div class="nombre-stat"><?= $nombre_amis ?></div>
                        <div>Amis</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="carte-statistique">
                        <div class="nombre-stat"><?= $invitations_en_attente ?></div>
                        <div>Invitations reçues</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- COLONNE GAUCHE : Actions (Créer post, Inviter ami) -->
                <div class="col-md-6">
                    
                    <!-- FORMULAIRE : Créer un nouveau post -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-pen"></i> Créer un Post</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" id="form-post">
                                <input type="hidden" name="action" value="creer_post">
                                
                                <!-- Choix du type de post -->
                                <div class="mb-3">
                                    <label class="form-label">Type de post :</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="type_post" id="normal" value="normal" checked>
                                            <label class="form-check-label" for="normal">
                                                <i class="fas fa-edit"></i> Normal
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="type_post" id="ia" value="ia">
                                            <label class="form-check-label" for="ia">
                                                <i class="fas fa-robot"></i> Généré par IA
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Section IA (cachée par défaut) -->
                                <div id="section-ia" style="display: none;" class="mb-3 p-3 bg-light rounded">
                                    <label for="prompt_ia" class="form-label">Prompt pour l'IA :</label>
                                    <div class="mb-2">
                                        <small class="text-muted">Suggestions :</small>
                                        <div class="d-flex flex-wrap gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-secondary prompt-rapide">motivation</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary prompt-rapide">étude</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary prompt-rapide">programmation</button>
                                        </div>
                                    </div>
                                    <input type="text" class="form-control" id="prompt_ia" name="prompt_ia" 
                                           placeholder="Ex: motivation pour étudier">
                                    <button type="button" id="btn-generer" class="btn btn-outline-primary mt-2">Générer</button>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="titre" class="form-label">Titre :</label>
                                    <input type="text" class="form-control" id="titre" name="titre" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="contenu" class="form-label">Contenu :</label>
                                    <textarea class="form-control" id="contenu" name="contenu" rows="4" 
                                              placeholder="Écrivez votre message..." required></textarea>
                                    <div class="text-end mt-1">
                                        <span id="compteur-char" class="compteur-caracteres">0/280</span>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn bouton-principal">
                                    <i class="fas fa-paper-plane"></i> Publier
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- FORMULAIRE : Inviter un ami -->
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-user-plus"></i> Inviter un Ami</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="action" value="envoyer_invitation">
                                <div class="mb-3">
                                    <label for="email_ami" class="form-label">Email de votre ami :</label>
                                    <input type="email" class="form-control" id="email_ami" name="email_ami" 
                                           placeholder="ami@example.com" required>
                                </div>
                                <div class="mb-3">
                                    <label for="message_invitation" class="form-label">Message (optionnel) :</label>
                                    <textarea class="form-control" id="message_invitation" name="message_invitation" rows="2" 
                                              placeholder="Salut ! Veux-tu rejoindre mon réseau ?"></textarea>
                                </div>
                                <button type="submit" class="btn bouton-principal">
                                    <i class="fas fa-paper-plane"></i> Envoyer l'invitation
                                </button>
                            </form>
                        </div>
                    </div>

                </div>

                <!-- COLONNE DROITE : Affichage (Invitations, Amis, Posts) -->
                <div class="col-md-6">
                    
                    <!-- INVITATIONS REÇUES -->
                    <?php if ($invitations_recues): ?>
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="fas fa-envelope"></i> Invitations Reçues (<?= count($invitations_recues) ?>)</h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($invitations_recues as $invitation): ?>
                            <div class="carte-invitation">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1"><?= htmlspecialchars($invitation['nom_expediteur']) ?></h6>
                                        <small class="text-muted"><?= htmlspecialchars($invitation['email_expediteur']) ?></small>
                                        <?php if ($invitation['message']): ?>
                                            <p class="mt-2 mb-2"><?= htmlspecialchars($invitation['message']) ?></p>
                                        <?php endif; ?>
                                        <small class="text-muted">
                                            <i class="fas fa-clock"></i> <?= date('d/m/Y', strtotime($invitation['created_at'])) ?>
                                        </small>
                                    </div>
                                    <div>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="repondre_invitation">
                                            <input type="hidden" name="invitation_id" value="<?= $invitation['id'] ?>">
                                            <input type="hidden" name="reponse" value="accepter">
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fas fa-check"></i> Accepter
                                            </button>
                                        </form>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="repondre_invitation">
                                            <input type="hidden" name="invitation_id" value="<?= $invitation['id'] ?>">
                                            <input type="hidden" name="reponse" value="refuser">
                                            <button type="submit" class="btn btn-outline-danger btn-sm ms-1">
                                                <i class="fas fa-times"></i> Refuser
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- LISTE DES AMIS -->
                    <?php if ($liste_amis): ?>
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-users"></i> Mes Amis (<?= count($liste_amis) ?>)</h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($liste_amis as $ami): ?>
                            <div class="d-flex align-items-center mb-2 p-2 bg-light rounded">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px; margin-right: 15px;">
                                    <?= strtoupper(substr($ami['name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <h6 class="mb-0"><?= htmlspecialchars($ami['name']) ?></h6>
                                    <small class="text-muted"><?= htmlspecialchars($ami['email']) ?></small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- POSTS RÉCENTS -->
                    <?php if ($posts_recents): ?>
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="fas fa-stream"></i> Posts Récents</h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($posts_recents as $post): ?>
                            <div class="carte-post">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-1"><?= htmlspecialchars($post['title']) ?></h6>
                                    <?php if ($post['type'] === 'ai_generated'): ?>
                                        <span class="badge-ia">
                                            <i class="fas fa-robot"></i> IA
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <p class="mb-2"><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                                <small class="text-muted">
                                    Par <?= htmlspecialchars($post['nom_auteur']) ?> • 
                                    <?= date('d/m/Y à H:i', strtotime($post['created_at'])) ?>
                                </small>

                                <?php if ($post['user_id'] === $_SESSION['user_id']): ?>
                                <div class="mt-2">
                                    <!-- Bouton Modifier -->
                                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-modifier-<?= $post['id'] ?>">
                                        <i class="fas fa-edit"></i> Modifier
                                    </button>

                                    <!-- Modal Modifier -->
                                    <div class="modal fade" id="modal-modifier-<?= $post['id'] ?>" tabindex="-1" aria-labelledby="modalModifierLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalModifierLabel">Modifier le Post</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST">
                                                        <input type="hidden" name="action" value="modifier_post">
                                                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                                        <div class="mb-3">
                                                            <label for="nouveau_titre" class="form-label">Titre :</label>
                                                            <input type="text" class="form-control" id="nouveau_titre" name="nouveau_titre" value="<?= htmlspecialchars($post['title']) ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="nouveau_contenu" class="form-label">Contenu :</label>
                                                            <textarea class="form-control" id="nouveau_contenu" name="nouveau_contenu" rows="4" required><?= htmlspecialchars($post['content']) ?></textarea>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bouton Supprimer -->
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="supprimer_post">
                                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-trash"></i> Supprimer
                                        </button>
                                    </form>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            </div>

            <!-- PIED DE PAGE ÉDUCATIF -->
            <div class="text-center mt-5 pt-4 border-top">
                <small class="text-muted">
                    <i class="fas fa-graduation-cap"></i> 
                    Application développée dans le cadre de la formation Laravel (18h) - 
                    2ème année Cycle d'ingénieur
                </small>
            </div>

        </div>
    </div>

    <!-- JAVASCRIPT POUR L'INTERACTIVITÉ -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // GESTION DU TYPE DE POST (Normal vs IA)
            const radioNormal = document.getElementById('normal');
            const radioIA = document.getElementById('ia');
            const sectionIA = document.getElementById('section-ia');
            const contenuTextarea = document.getElementById('contenu');
            const titreInput = document.getElementById('titre');
            const promptInput = document.getElementById('prompt_ia');
            const boutonGenerer = document.getElementById('btn-generer');
            
            function afficherSectionIA() {
                if (radioIA.checked) {
                    sectionIA.style.display = 'block';
                    contenuTextarea.value = '';
                    titreInput.value = '';
                } else {
                    sectionIA.style.display = 'none';
                }
            }
            
            radioNormal.addEventListener('change', afficherSectionIA);
            radioIA.addEventListener('change', afficherSectionIA);
            
            // PROMPTS RAPIDES POUR L'IA
            document.querySelectorAll('.prompt-rapide').forEach(function(bouton) {
                bouton.addEventListener('click', function() {
                    document.getElementById('prompt_ia').value = this.textContent;
                });
            });
            
            // COMPTEUR DE CARACTÈRES
            function mettreAJourCompteur() {
                const texte = contenuTextarea.value;
                const longueur = texte.length;
                const compteur = document.getElementById('compteur-char');
                
                compteur.textContent = longueur + '/280';
                
                // Changer la couleur selon la longueur
                if (longueur > 280) {
                    compteur.className = 'compteur-caracteres attention';
                } else if (longueur > 250) {
                    compteur.className = 'compteur-caracteres text-warning';
                } else {
                    compteur.className = 'compteur-caracteres';
                }
            }
            
            contenuTextarea.addEventListener('input', mettreAJourCompteur);
            
            // VALIDATION DU FORMULAIRE
            document.getElementById('form-post').addEventListener('submit', function(e) {
                const contenu = contenuTextarea.value.trim();
                if (contenu.length > 280) {
                    e.preventDefault();
                    alert('Le contenu dépasse 280 caractères. Veuillez le raccourcir.');
                    return false;
                }
            });
            
            // Fonction pour générer le contenu via l'IA
            boutonGenerer.addEventListener('click', function() {
                const prompt = promptInput.value.trim();
                if (!prompt) {
                    alert('Veuillez entrer un prompt pour générer le contenu.');
                    return;
                }

                // Génération simple côté client
                const suggestions = {
                    'motivation': 'Restez motivé et persévérez dans vos études ! Chaque effort compte pour votre réussite.',
                    'étude': 'Les études sont un investissement pour votre futur. Organisez-vous et prenez des pauses régulières.',
                    'programmation': 'La programmation demande de la pratique et de la patience. Commencez petit et progressez étape par étape.',
                    'default': 'Voici un contenu généré automatiquement basé sur votre demande : ' + prompt
                };
                
                const contenuGenere = suggestions[prompt.toLowerCase()] || suggestions['default'];
                contenuTextarea.value = contenuGenere;
                titreInput.value = "Post inspiré par : " + prompt;
                
                alert('Contenu généré avec succès !');
            });
            
            // Initialiser le compteur
            mettreAJourCompteur();
        });
    </script>
</body>
</html>



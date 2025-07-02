# 📚 Application Sociale Laravel - Version Débutant

## 🎯 Objectif Pédagogique

Cette application a été développée pour un **étudiant de 2ème année Cycle d'ingénieur** dans le cadre d'une formation Laravel de **18 heures**. Elle présente les concepts de base de manière simple et compréhensible.

## ✨ Fonctionnalités Implémentées

### 🔐 Authentification
- **Connexion** sécurisée avec session PHP
- **Validation** des données utilisateur
- **Redirection** automatique selon le statut de connexion

### 📝 Gestion des Posts
- **Création** de posts avec titre et contenu
- **Limitation** à 280 caractères (comme Twitter)
- **Compteur** de caractères en temps réel
- **Types de posts** : Normal ou généré par IA

### 🤖 Simulation IA Simple
- **Templates** prédéfinis pour simuler une IA
- **Mots-clés** : motivation, étude, programmation
- **Prompts rapides** pour faciliter l'utilisation
- **Génération** de contenu éducatif

### 👥 Système d'Invitations
- **Envoi** d'invitations par email
- **Réception** et affichage des invitations
- **Acceptation/Refus** avec mise à jour en base
- **Gestion** automatique des relations d'amitié

### 📊 Tableau de Bord
- **Statistiques** personnelles (posts, amis, invitations)
- **Interface** responsive et moderne
- **Affichage** des posts récents
- **Liste** des amis connectés

## 🛠️ Technologies Utilisées

- **Backend** : PHP 8+ (concepts Laravel appliqués)
- **Frontend** : Bootstrap 5, HTML5, CSS3, JavaScript
- **Base de Données** : MySQL 8
- **Environnement** : Docker (apprentissage DevOps)
- **Architecture** : MVC simplifiée

## 📁 Structure des Fichiers

```
src/public/
├── app.php              # Application principale (toutes fonctionnalités)
├── login_simple.php     # Page de connexion éducative
├── index.php           # Point d'entrée (redirection)
├── register.php        # Inscription (existant)
└── README_SIMPLE.md    # Cette documentation
```

## 🗄️ Structure de la Base de Données

### Table `users`
- `id` : Identifiant unique
- `name` : Nom de l'utilisateur
- `email` : Email (unique)
- `password` : Mot de passe haché

### Table `posts`
- `id` : Identifiant unique
- `title` : Titre du post
- `content` : Contenu (max 280 caractères)
- `user_id` : Référence à l'utilisateur
- `type` : Type (normal/ia)
- `ai_prompt` : Prompt utilisé pour l'IA
- `created_at` : Date de création

### Table `invitations`
- `id` : Identifiant unique
- `sender_id` : Expéditeur de l'invitation
- `invitee_id` : Destinataire de l'invitation
- `message` : Message personnalisé
- `status` : Statut (pending/accepted/declined)
- `created_at` : Date d'envoi

### Table `friends`
- `id` : Identifiant unique
- `user_id` : Utilisateur
- `friend_id` : Ami
- `created_at` : Date de création de l'amitié

## 🚀 Comment Utiliser

### 1. Démarrage
```bash
# L'application fonctionne avec Docker
# Accéder à : http://localhost:8080
```

### 2. Connexion
- Utiliser un compte existant : `adilikarim@gmail.com` ou `test@example.com`
- Ou créer un nouveau compte via "S'inscrire"

### 3. Navigation
- **Page unique** : Toutes les fonctionnalités sur une seule page
- **Interface intuitive** : Colonne gauche (actions), colonne droite (affichage)

### 4. Fonctionnalités
- **Créer un post** normal ou généré par IA
- **Inviter des amis** par email
- **Répondre aux invitations** reçues
- **Consulter** les posts récents et la liste d'amis

## 📖 Concepts Laravel Appliqués

### 1. **Routing** (Simplifié)
- Redirection automatique selon l'authentification
- Gestion des actions via `$_POST['action']`

### 2. **Eloquent** (Concept appliqué avec PDO)
- Requêtes préparées pour la sécurité
- Relations entre tables (users, posts, friends, invitations)

### 3. **Validation**
- Validation côté serveur et client
- Messages d'erreur contextuels

### 4. **Sessions**
- Gestion de l'authentification
- Persistance des données utilisateur

### 5. **Sécurité**
- Protection contre l'injection SQL
- Hachage des mots de passe
- Échappement des données affichées

## 🎓 Objectifs Pédagogiques Atteints

### **Niveau Débutant (18h de formation)**
- ✅ Comprendre la structure MVC
- ✅ Gérer une base de données
- ✅ Implémenter l'authentification
- ✅ Créer des formulaires sécurisés
- ✅ Utiliser Bootstrap pour l'interface
- ✅ Appliquer les bonnes pratiques de sécurité

### **Compétences Développées**
- **PHP orienté objet** (PDO, sessions)
- **SQL** (requêtes complexes avec jointures)
- **Frontend** (Bootstrap, JavaScript interactif)
- **UX/UI** (interface responsive et intuitive)
- **Sécurité** (validation, échappement, hachage)

## 🔧 Améliorations Possibles

Pour approfondir l'apprentissage :

1. **Middleware** pour la gestion des autorisations
2. **API REST** pour découpler frontend/backend  
3. **Tests unitaires** avec PHPUnit
4. **Cache** pour optimiser les performances
5. **Upload de fichiers** (images de profil)
6. **Notifications** en temps réel
7. **Pagination** pour les posts
8. **Recherche** d'utilisateurs et de contenu

## 📞 Support

Cette application est conçue pour l'apprentissage. Le code est largement commenté pour faciliter la compréhension des concepts Laravel de base.

---

**Développé avec ❤️ pour l'apprentissage Laravel**  
*Version éducative - 2ème année Cycle d'ingénieur*

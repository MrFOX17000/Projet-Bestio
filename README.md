# 🐾 Projet Bestio

Bienvenue dans **Bestio**, une application Symfony permettant de découvrir, et discuter des différentes espèces d’animaux.  
Le but du projet est d’allier **apprentissage** de Symfony et **fun** en construisant une appli complète, propre et extensible.

---

## ✨ Fonctionnalités

-   🔐 Authentification (inscription, connexion, mot de passe sécurisé)
-   👤 Gestion du profil utilisateur (pseudo, photo, mot de passe)
-   🐇 Création de fiches d’animaux (espèce, description, habitat, alimentation, etc.)
-   🖼️ Upload d’images
-   📊 Affichage dynamique des données
-   ⚙️ Back-office administrateur (modération des contenus)

---

## 🛠️ Stack technique

-   **Backend** : [Symfony 7+](https://symfony.com/)
-   **Base de données** : MySQL (Doctrine ORM & Migrations)
-   **Frontend** : Twig + Bootstrap / CSS
-   **Tests** : PHPUnit
-   **Outils Dev** : Symfony CLI, Composer, Laragon (ou équivalent)

---

## 🚀 Démarrage rapide

### ⚡ Prérequis

-   PHP `^8.2`
-   [Composer](https://getcomposer.org/)
-   [Symfony CLI](https://symfony.com/download)
-   MySQL ou MariaDB

### 📥 Installation

#### Clone le projet sur ta machine :

```bash
git clone https://github.com/ton-compte/ton-projet.git
cd ton-projet
```

#### Installe les dépendances :

```bash
composer install
```

#### Crée ton fichier d’environnement :

```bash
cp .env .env.local
```

⚠️ Pense à modifier .env.local avec tes identifiants de base de données (ex. DATABASE_URL).

#### Exécute les migrations pour créer la base :

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

#### Lance le serveur Symfony :

```bash
symfony serve
```

#### Et ouvre le projet dans ton navigateur 🚀

👉 http://127.0.0.1:8000

## 👥 Contributeurs

🧑‍💻 - Mathias

👩‍💻 - Hajar

Heureux de vous présenter ce projet plein d'énergie, de bonne ambiance et de chats 🐈

## 📜 Licence

Projet réalisé dans le cadre de notre formation Dawan.

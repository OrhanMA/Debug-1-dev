# Debug-1-dev

## Spécificités fonctionnelles

#### \*(Threads = fils de discussions.)

L'ensemble du projet est réalisé en anglais.

Debug-1-dev est un projet développé avec [Symfony](https://symfony.com/), [MySQL](https://www.mysql.com/fr/) et le moteur de template [Twig](https://twig.symfony.com/).

Le but de ce projet est de créer un espace permettant à des développeurs juniors de publier des \*threads pour demander de l'aide lorsqu'ils sont coincés, un peu dans l'esprit du fameux [Stack Overflow](https://stackoverflow.com/).

## Fonctionnalités principales

- système d'authentification permettant à un utilisateur de s'inscrire, de se connecter et de se déconnecter (gestion des rôles nécessaire).
- CRUD complet d'un thread, de commentaires.
- Vote pour un thread et/ou une réponse à un thread.
- Ajout d'un commentaire comme solution à un thread.
- Rôle administrateur pour gérer les modifications/suppression de threads, commentaires, solutions appartenants à d'autres utilisateurs.

### Pages

Le projet dispose des pages suivantes:

- accueil: afficher l'ensemble des threads, par ordre chronologique décroissant.
- connexion
- inscription
- création de thread: formulaire de création d'un thread.
- édition de thread: formulaire d'édition d'un thread.
- détails d'un thread: afficher le détail d'un thread (message principal + réponses + formulaire d'ajout d'une réponse).
- profil privé: détails de l'utilisateur connecté + la liste des threads dont il est l'auteur + la liste des threads auxquels il n'est pas auteur mais auxquels il a contribué.
- profil public: détails de l'utilisateur + la liste des threads dont il est l'auteur + la liste des threads auxquels il n'est pas auteur mais auxquels il a contribué.
- édition de profil: formulaire d'édition du profil utilisateur.
  Fonctionnalités

## Installation

Clone the project

```bash
  git clone git@github.com:OrhanMA/Debug-1-dev.git
```

Allez dans le projet:

```bash
  cd Debug-1-dev
```

Install dependencies

```bash
  composer install
```

Paramétrez la variable DATABASE_URL de votre .env pour la connecter votre base de données

```bash
  DATABASE_URL="mysql://<username>:<password>@<ip>:<port>/<database_name>?serverVersion=<server-version>&charset=utf8mb4"
```

Mettez en place votre base de données
(si vous avez le CLI Symfony, vous pouvez utiliser symfony console au lieu de php bin/console).

Création de la base de données:

```bash
php bin/console doctrine:database:create
```

Création de la migration **si aucune migration n'est déjà présente dans le projet**

```bash
php bin/console doctrine:make:migration
```

Exécution de la migration

```bash
php bin/console d:m:m
```

### Fausses données

Des fausses données ont été créées pour les catégories. Pour les charger, exécutez la commande suivante:

```bash
php bin/console doctrine:fixtures:load
```

### Actions administrateur

Un profil adminstrateur a été créé par défaut. Ses identifiants de connexions sont:

- email: admin@admin.com
- password: admin1234

### Démarrer votre serveur local

```bash
php bin/console server:start
```

Rendez-vous sur http://localhost:8000 (ou l'URL donné par votre terminal)

> ⚠️ Pour donner les actions administrateur à un utilisateur, ajoutez-lui le rôle "ROLE_ADMIN" en base de données ⚠️

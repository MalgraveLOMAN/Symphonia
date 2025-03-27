# Guide d'Installation et de Lancement

## Prérequis

Avant de commencer, assurez-vous d'avoir les éléments suivants installés sur votre machine :

- **PHP** (version recommandée : 8.x)
- **Composer** (gestionnaire de dépendances PHP)
- **Symfony CLI** (pour gérer le serveur de développement)
- **SQLite** (base de données légère utilisée dans ce projet)

---

## Installation

### 1. Installer les dépendances

Exécutez la commande suivante pour installer les dépendances du projet :

```bash
composer install
```


### 2. Configuration de la base de données

1. **Copier et configurer le fichier `.env`**  
   Copiez le fichier `.env` existant et renommez-le `.env.local` :

   ```bash
   cp .env .env.local
   ```

2. **Configurer SQLite**  
   Dans le fichier `.env.local`, décommentez la ligne suivante :

   ```ini
   DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
   ```

   Et commentez la ligne suivante :

   ```ini
   # DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=16&charset=utf8"
   ```

---

### 3. Création et migration de la base de données

Exécutez les commandes suivantes pour créer la base de données et appliquer les migrations :

```bash
php bin/console doctrine:database:create
symfony console make:migration
php bin/console doctrine:migrations:migrate
```

---

### 4. Démarrer le serveur Symfony

Lancez le serveur Symfony avec la commande :

```bash
symfony server:start
```

Ensuite, ouvrez votre navigateur et accédez à l'adresse indiquée dans le terminal.  
Si cette adresse a déjà été utilisée précédemment, pensez à **supprimer les cookies**.

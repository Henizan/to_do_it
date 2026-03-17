# To Do It

To Do It est une application web conçue pour aider les utilisateurs à organiser leurs journées et accomplir leurs tâches facilement. Elle permet de gérer une liste de tâches, de suivre ses activités dans un calendrier et d'accéder à un tableau de bord.

## Technologies
- **Frontend** : HTML5, CSS3, JavaScript (Vanilla)
- **Backend** : PHP
- **Base de données** : PostgreSQL (via Docker)
- **Infrastructure** : Docker & Docker Compose

## Fonctionnalités principales
- Gestion des tâches quotidiennes (To Do List)
- Suivi des tâches via un calendrier unifié
- Tableau de bord utilisateur récapitulant les activités
- Réglage des paramètres utilisateur
- Authentification sécurisée (Inscription, Connexion)

## Structure
- `public/index.html` : Page d'accueil et présentation
- `public/tab_bord.html` : Tableau de bord de l'utilisateur
- `public/calendrier.html` : Interface calendrier des tâches
- `public/setting.html` : Page des paramètres
- `public/connexion.html` / `public/inscription.html` : Pages de connexion et de création de compte
- `public/connexion.php` / `public/inscription.php` : Scripts backend de gestion de l'authentification
- `includes/` : Fichiers de configuration (ex: connexion à la base de données)
- `assets/` : Ressources graphiques (`images/`), feuilles de style (`css/`) et scripts (`js/`)
- `Dockerfile` / `docker-compose.yml` : Configuration de l'environnement conteneurisé
- `init.sql` : Script de création de la structure de la base de données PostgreSQL

## Lancer le projet (local via Docker)

L'application est entièrement dockérisée pour simplifier son lancement sur n'importe quel système d'exploitation.

### Prérequis
- [Docker](https://www.docker.com/) installé et en cours d'exécution sur votre machine (ex: Docker Desktop).

### Lancement
1. Ouvrez un terminal dans le dossier racine du projet (`To_Do_It`).
2. Exécutez la commande suivante pour construire et démarrer les conteneurs (PHP/Apache et PostgreSQL) en arrière-plan :
   ```bash
   docker-compose up -d --build
   ```
3. Ouvrez votre navigateur web et accédez à l'application via : `http://localhost:8080/public/`

*(La base de données PostgreSQL est automatiquement initialisée avec les tables nécessaires au premier lancement grâce au fichier `init.sql`).*

### Arrêter le projet
Pour stopper les conteneurs, exécutez dans le même dossier :
```bash
docker-compose down
```

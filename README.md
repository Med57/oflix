# Projet O'flix, Symfony Getting Started

Le but de ce repo a été d'apprendre à utiliser Symfony.

Nous avons vu : 
   - Doctrine, 
   - Fixtures, 
   - Formulaires,
   - Authentification + Roles + Permissions,
   - Voters,
   - Service,
   - Commandes,
   - Evenements,
   - API
   - Tests

## Installer le projet.

Apres telechargement : 
 
- Installation des dépendances : composer install
- Modifier le fichier .env.local avec la commande qui permet de lié la BDD au projet symfony.
- Créer le code SQL de migration :  php bin/console make:migration
- Executer le code de migration : php bin/console doctrine:migrations:migrate
- Executer le code pour les fixtures : php bin/console doctrine:fixtures:load

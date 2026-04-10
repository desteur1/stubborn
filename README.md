####### DATA BASE #########

# Projet Stubborn

# How symfony reads files

.env

.env.local

.env.dev

.env.dev.local

- hence .env.local will be our environment variable

# # # Commands

# composer require symfony/orm-pack

- Doctrine ORM

- connection to MySQL

- Entities management

- migrations

. without this symfony can't communicate with the database

# maker-bundle

- Use to generate code automatically (e.g
  php bin/console make:entity
  php bin/console make:user
  php bin/console make:controller)

# composer require symfonycasts/verify-email-bundle

- use to verify emails

## Installation

composer install
configurer .env (DB + Stripe)
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
symfony server:start

## Tests

Des tests unitaires ont été réalisés pour :

- Ajout au panier
- Modification des quantités
- Suppression d’un produit
- Vérification produit existant
- Accès à la route checkout
- Authentification

Lancer les tests avec :

php bin/phpunit

## Email (Mailer)

Un compte admin est déjà disponible :
compte admin en dessus

Il n’est donc pas nécessaire de configurer le mailer pour tester l’application

## Comptes de test admin

nom : admin
email: smartbrief.me@gmail.com
password: admin1234

## Stripe

Configurer .env.local :

## Configuration Stripe

1. Créez un fichier `.env.local`
2. Ajoutez votre clé Stripe :

STRIPE_SECRET_KEY=your_stripe_secret_key

👉 Vous pouvez récupérer une clé sur Stripe Dashboard (mode test)

Carte test :
4242 4242 4242 4242
Date : 12/34
CVC : 123

Mode test Stripe utilisé.

## Commandes utiles

php bin/console server:start
php bin/phpunit

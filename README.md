# Liste des Tâches du Projet

Ce document détaille la répartition des tâches entre les membres de l'équipe pour le développement front-end et back-end.

## Pseudo Github

Frederic : FredericESGI

Achraf : Kherraz-Med-Achraf

Youri : Yghlis

Fayssal : fayssalmechmeche

## Répartition des Tâches

| Fonctionnalité                                       | Front-End            | Back-End             |
| ----------------------------------------------------- | -------------------- | -------------------- |
| Inscription / Connexion (Mot de passe oublié inclus) | Frederic             | Fayssal              |
| Gestion des rôles                                    | Fayssal              | Fayssal              |
| Superadmin                                            | Frederic / Fayssal   | Frederic / Fayssal   |
| Facture                                               | Achraf / Youri       | Youri / Achraf       |
| Devis                                                 | Achraf / Youri       | Achraf / Youri       |
| Clients                                               | Achraf               | Youri                |
| Produits                                              | Achraf               | Youri                |
| Formule                                               | Achraf               | Youri                |
| Catégorie                                            | Frederic             | Frederic             |
| Envoi de mail                                         | Fayssal              | Fayssal              |
| Stripe (en général)                                 | Fayssal              | Fayssal              |
| Statut des paiements des devis et factures            | Rien                 | Youri                |
| Dashboard                                             | Achraf               | Achraf               |
| Dashboard superadmin                                  | Frédéric / Fayssal | Frédéric / Fayssal |
| Gestion des sociétés                                | Fayssal / Frédéric | Fayssal / Frédéric |
| Rapports financiers                                   | Frédéric / Fayssal | Frédéric / Fayssal |
| Profil                                                | Achraf               | Achraf / Fayssal     |
| PDF devis facture                                     | Achraf               | Achraf               |

## Note

Cette liste est susceptible d'être mise à jour au fur et à mesure de l'avancement du projet.

lien github : https://github.com/fayssalmechmeche/Challenge-4IW3

lien figma : https://www.figma.com/file/3ftoKivF4TxUTXdGKx4MIP/Untitled?type=design&node-id=0%3A1&mode=design&t=tuhlzoCXMtbmT2Xo-1

lien du site : http://astroshe.live/

STRIPE CB : 4242 4242 4242 4242     CODE SECRET : 123    DATE: SUPERIEUR A AUJOURD'HUI

# Symfony Docker (PHP8 / Caddy / Postgresql)

A [Docker](https://www.docker.com/)-based installer and runtime for the [Symfony](https://symfony.com) web framework, with full [HTTP/2](https://symfony.com/doc/current/weblink.html), HTTP/3 and HTTPS support.

## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/)
2. Run `docker compose build --pull --no-cache` to build fresh images
3. Run `docker compose up` (the logs will be displayed in the current shell) or Run `docker compose up -d` to run in background
4. Run `docker compose exec php bin/console d:d:c` to create a database challenge
5. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
6. Run `docker compose down --remove-orphans` to stop the Docker containers.
7. Run `docker compose logs -f` to display current logs, `docker compose logs -f [CONTAINER_NAME]` to display specific container's current logs

## Commandes utiles

Lister l'ensemble des commandes existances `docker compose exec php bin/console`

#### Création de fichier vierge

Controller `docker compose exec php bin/console make:controller`

FormType `docker compose exec php bin/console make:form`

CRUD `docker compose exec php bin/console make:crud`

#### Debug

Supprimer le cache du navigateur

`docker compose exec php bin/console cache:clear`

`docker compose exec php bin/console c:c`

Voir les routes actives

`docker compose exec php bin/console debug:router`

## Gestion des routes

[https://symfony.com/doc/current/routing.html](https://symfony.com/doc/current/routing.html)

## Autowiring & ParamConverter

Autowiring [https://symfony.com/doc/current/service_container/autowiring.html](https://symfony.com/doc/current/service_container/autowiring.html)

ParamConverter [https://symfony.com/bundles/SensioFrameworkExtraBundle/current/annotations/converters.html](https://symfony.com/bundles/SensioFrameworkExtraBundle/current/annotations/converters.html)

## Gestion de base de données

#### Commandes de création des fichiers entity/repository et d'ajout de champs

`docker compose exec php bin/console make:entity`

Documentation sur les relations entre les entités [https://symfony.com/doc/current/doctrine/associations.html](https://symfony.com/doc/current/doctrine/associations.html)

#### Mise à jour de la base de données via migration

Generation d'une migration

`docker compose exec php bin/console make:migration`

Jouer les migrations

`docker compose exec php bin/console doctrine:migration:migrate`

`docker compose exec php bin/console d:m:m`

#### Mise à jour de la base de données via update de schema sans migration

Voir les requètes interprétées (sans mise à jour de la DB)

`docker compose exec php bin/console doctrine:schema:update --dump-sql`

`docker compose exec php bin/console d:s:u --dump-sql`

Executer les requètes en DB

`docker compose exec php bin/console doctrine:schema:update --force`

`docker compose exec php bin/console d:s:u --force`

## 28/09/2023

Ajout des entités pour la base de données

```php

docker compose exec php bin/console m:m
docker compose exec php bin/console d:m:m

```

## 5/11/2023

Ajout de webpack encore sur le projet

```
docker compose exec php composer install
docker compose up
docker compose exec php npm install
docker compose exec php npm run dev

install tailwindcss

docker compose exec php npm install -D tailwindcss postcss postcss-loader autoprefixer

```

# API DontWasteIt

Fonctionne sous le micro-framework PHP Lumen

## Pourquoi ne pas avoir choisi expressJS
* Single threadé et on a besoin de beaucoup de requete
* Les paquets de NodeJS ont toujours des MàJs
* PHP c'est bien quand il y a beaucoup de données

## Pourquoi avoir choisi un framework
* En cinq lettres: PRESS: Performance, rapidité, efficacité, sécurité, simplicité
* Une API ca prend du temps a sécurisé et un micro framework fait pour permet de gagner beaucoup de temps

## Pourquoi Lumen
* Je sais comment ca fonctionne
* L'architecture MVC (en l'occurrence il n'y a pas de vu car pas besoin)
* Le confort de pouvoir étendre l'API au besoin

## Comment installer l'API

Requis: mysql, php 8.0 minimum, serveur web apache de préférence (ou nginx mais développé sous apache)

* Installer composer (https://getcomposer.org/)

* Suivre ces étapes:

Cloner le projet `git clone https://github.com/IHM-SI3/API.git` puis `cd ./API`

Installation des vendors: `composer install`

Copié le .env.exemple en .env: `cp .env.exemple .env`

Générer la clé de l'application (commande a ne jamais refaire en cas de données dans la bdd): `php artisan key:generate`

Ouvrir le fichier .env et modifier les informations suivantes:

* API_KEY=
* DB_HOST=
* DB_PORT=
* DB_DATABASE=
* DB_USERNAME=
* DB_PASSWORD=

en mettant une clé d'API ainsi que les identifiants de base de données

Dernière étape, créer les tables SQL en faisant: `php artisan migrate` (logiquement 5 tables devraient apparaitre dans la bdd)

## Côté ANDROID

Modifier les deux meta data dans le manifest avec l'API KEY et le API HOST

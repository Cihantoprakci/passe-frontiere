# Passe-Frontière

Application web pour les frontaliers de Haute-Savoie qui travaillent à Genève. Elle dit chaque matin s'il vaut mieux prendre la voiture ou le Léman Express, avec le retard estimé à chaque douane.

Projet réalisé pour le module Booster Portfolio, B2 développement web, MyDigitalSchool Annecy.

## Fonctionnalités

- Tableau de bord : train ou voiture, avec le temps de trajet de chaque solution
- Les 6 douanes du Genevois avec leur retard actuel
- Fiche de chaque douane avec le retard moyen heure par heure sur les 5 dernières semaines
- Prochains trains vers Genève-Cornavin depuis 5 gares
- Compte utilisateur : trajet habituel et alertes par e-mail quand une douane bouchonne

## Comment le retard est calculé

Il n'existe pas de donnée officielle sur l'attente aux douanes. Toutes les 15 minutes, l'application demande à TomTom le temps de passage d'un tronçon d'environ 1 km autour de chaque douane, avec et sans trafic. La différence donne le retard. Les relevés sont gardés pour calculer les heures creuses.

## Technologies

- PHP 8.4, Symfony 7.4, Twig
- Doctrine ORM, MySQL 8.4
- AssetMapper, Stimulus, Symfony UX Chart.js
- PHPUnit

## API utilisées

| Donnée | Source |
| --- | --- |
| Horaires des trains | [transport.opendata.ch](https://transport.opendata.ch) |
| Trafic aux douanes | [TomTom Routing API](https://developer.tomtom.com) |
| Météo | [Open-Meteo](https://open-meteo.com) |
| Taux euro / franc | [Frankfurter](https://frankfurter.dev) |

Seule TomTom demande une clé (gratuite).

## Installation en local

Il faut PHP 8.4, Composer, Docker et la [CLI Symfony](https://symfony.com/download) (facultative).

```bash
git clone https://github.com/Cihantoprakci/passe-frontiere.git
cd passe-frontiere
composer install
```

Lancer MySQL et Mailpit :

```bash
docker compose up -d
```

Créer le fichier `.env.local` :

```
APP_SECRET=une_chaine_au_hasard
CRON_TOKEN=une_autre_chaine_au_hasard
TOMTOM_API_KEY=ta_cle_tomtom
```

Créer la base et charger les données de départ (6 douanes et 5 semaines de relevés) :

```bash
php bin/console doctrine:migrations:migrate -n
php -d memory_limit=1G bin/console doctrine:fixtures:load -n --no-debug
```

Les fixtures créent environ 20 000 relevés, d'où la limite de mémoire plus haute.

Lancer le serveur :

```bash
symfony serve
```

ou sans la CLI Symfony :

```bash
php -S 127.0.0.1:8000 -t public
```

Le site est sur http://localhost:8000 et les e-mails envoyés arrivent dans Mailpit sur http://localhost:8025.

## Variables d'environnement

| Variable | Rôle |
| --- | --- |
| `DATABASE_URL` | Connexion MySQL |
| `MAILER_DSN` | Serveur SMTP pour les alertes |
| `MAILER_FROM` | Adresse d'envoi des alertes |
| `TOMTOM_API_KEY` | Clé de l'API TomTom |
| `CRON_TOKEN` | Protège l'adresse appelée par la tâche planifiée |

Les vraies valeurs vont dans `.env.local`, qui n'est pas envoyé sur GitHub.

## Relevé toutes les 15 minutes

En local, avec la commande :

```bash
php bin/console app:record-borders
```

En ligne, une tâche planifiée appelle toutes les 15 minutes :

```
https://passe-frontiere.ctdigitalweb.com/cron/releve?token=VOTRE_CRON_TOKEN
```

Chaque appel enregistre un relevé par douane puis envoie les alertes qui doivent partir.

## Tests

```bash
php bin/phpunit
```

## Crédits

- Photo de la douane de Bardonnex : Henk Monster, CC BY 3.0, Wikimedia Commons
- Photo de la douane de Moillesulaz : Enrique Íñiguez Rodríguez, CC BY-SA 3.0, Wikimedia Commons
- Icônes : [Lucide](https://lucide.dev)
- Police : Atkinson Hyperlegible Next, Braille Institute

## Auteur

Cihan Toprakci

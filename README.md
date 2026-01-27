# EcoTrack

Plateforme Symfony pour mesurer, suivre et reduire l'empreinte carbone.

## Fonctionnalites
- Authentification securisee (form login, roles)
- Tableau de bord carbone
- Calcul des emissions (transport, energie, alimentation, numerique)
- Actions ecologiques et suivi
- Reporting et export PDF
- API REST via API Platform

## Prerequis
- PHP 8.4+
- Composer 2.8+
- PostgreSQL 16+ (ou via Docker)

## Installation locale
```bash
composer install
cp .env .env.local
```

Configurer `DATABASE_URL` dans `.env.local`, puis :
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console server:run
```

## Docker
```bash
docker compose up -d --build
```
Application : http://localhost:8080

## Tests et qualite
```bash
composer test
composer phpstan
```

## API
Documentation interactive : `/api`

## Export PDF
Route web : `/report/pdf`
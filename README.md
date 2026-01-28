# EcoTrack

Plateforme Symfony pour mesurer, suivre et reduire l'empreinte carbone.

## Fonctionnalites
- Authentification securisee (form login, roles)
- Tableau de bord carbone + tendances
- Calcul des emissions (transport, energie, alimentation, numerique)
- Actions ecologiques (CRUD) et suivi
- Enregistrements carbone (CRUD)
- Recommandations d'actions
- Export PDF
- API REST via API Platform
- Interface responsive

## Stack
- PHP 8.4+, Symfony 8
- Twig, Stimulus
- Doctrine ORM
- PostgreSQL
- Docker, GitHub Actions
- PHPUnit, PHPStan

## Installation locale (sans Docker)
```bash
composer install
cp .env .env.local
```

Configurer `DATABASE_URL` dans `.env.local`, puis :
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php -S 127.0.0.1:8000 -t public
```

## Docker
### Dev (avec volumes)
```bash
docker compose -f compose.dev.yaml up -d --build
```
Application : http://localhost:8080
Base de donnees : localhost:5433

### Prod-like (sans volumes)
```bash
docker compose up -d --build
```
Si vous modifiez des assets ou des templates, rebuild l'image (ou recopier `public/assets` vers les conteneurs).

## Assets (prod)
```bash
php bin/console asset-map:compile --env=prod
```

## Tests et qualite
```bash
composer test
composer phpstan
```

## API
Documentation interactive : `/api`

## Export PDF
Route web : `/report/pdf`

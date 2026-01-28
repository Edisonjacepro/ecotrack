# EcoTrack

Symfony platform to measure, track, and reduce carbon footprint.

## Features
- Secure authentication (form login, roles)
- Carbon dashboard + trends
- Emissions calculation (transport, energy, food, digital)
- User actions (CRUD) and tracking
- Carbon records (CRUD)
- Action recommendations
- PDF export
- REST API via API Platform
- Responsive UI

## Stack
- PHP 8.4+, Symfony 8
- Twig, Stimulus
- Doctrine ORM
- PostgreSQL
- Docker, GitHub Actions
- PHPUnit, PHPStan

## Local setup (no Docker)
```bash
composer install
cp .env .env.local
```

Set `DATABASE_URL` in `.env.local`, then:
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php -S 127.0.0.1:8000 -t public
```

## Docker
### Dev (with volumes)
```bash
docker compose -f compose.dev.yaml up -d --build
```
App: http://localhost:8080
Database: localhost:5433

### Prod-like (no volumes)
```bash
docker compose up -d --build
```
If you change assets or templates, rebuild the image (or copy `public/assets` into containers).

## Assets (prod)
```bash
php bin/console asset-map:compile --env=prod
```

## Tests
```bash
composer test
composer phpstan
```

## API
Interactive docs: `/api`

## PDF export
Web route: `/report/pdf`

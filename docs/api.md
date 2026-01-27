# API

API Platform expose les ressources :
- CarbonRecord
- EcoAction
- UserEcoAction

## Endpoints utiles
- `/api` : documentation et console
- `/api/calculate` : calcul des emissions (POST)
- `/api/report/summary` : resume du reporting (GET)

### Exemple POST /api/calculate
```json
{
  "category": "transport",
  "data": {
    "distance_km": 12,
    "mode": "car"
  }
}
```

## Cas d'utilisation concrets

### 1) Import automatique depuis un outil externe
Un ERP ou un outil de facture envoie les donnees d'energie.

**POST /api/carbon_records**
```json
{
  "category": "energy",
  "amountKg": 18.6,
  "recordedAt": "2026-01-27T10:30:00+00:00",
  "sourceData": {
    "kwh": 320,
    "energy_type": "electricity"
  },
  "notes": "Facture de janvier"
}
```

### 2) App mobile ou plugin navigateur
Enregistrement rapide des deplacements.

**POST /api/carbon_records**
```json
{
  "category": "transport",
  "amountKg": 4.2,
  "recordedAt": "2026-01-27T08:10:00+00:00",
  "sourceData": {
    "distance_km": 22,
    "mode": "bus"
  }
}
```

### 3) Reporting externe (BI, dashboard)
Recuperer un resume mensuel pour un tableau de bord.

**GET /api/report/summary**
```json
{
  "monthly_total": 123.45,
  "category_totals": [
    { "category": "transport", "total": 45.2 },
    { "category": "energy", "total": 38.1 }
  ],
  "monthly_trend": [
    { "month": "2025-10", "total": 110.0 },
    { "month": "2025-11", "total": 98.5 }
  ]
}
```

### 4) Automatisation de rapports
Un cron appelle l'API puis telecharge le PDF.

- `GET /api/report/summary`
- `GET /report/pdf`

### 5) Tests QA rapides
La page `/api` permet de tester les endpoints sans client externe.

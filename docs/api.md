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
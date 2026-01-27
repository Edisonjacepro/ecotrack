# Base de donnees

## Entites
- User
- CarbonRecord
- EcoAction
- UserEcoAction

## Relations
- User 1..* CarbonRecord
- User 1..* UserEcoAction
- EcoAction 1..* UserEcoAction

## Schema (Mermaid)
```mermaid
erDiagram
    USER ||--o{ CARBON_RECORD : has
    USER ||--o{ USER_ECO_ACTION : tracks
    ECO_ACTION ||--o{ USER_ECO_ACTION : referenced_by
```

## Migration
Voir `migrations/Version20260127141000.php`.
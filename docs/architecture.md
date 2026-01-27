# Architecture

EcoTrack suit une architecture MVC Symfony.

## Couches
- Controllers : orchestration web/API
- Services : logique metier (calcul, recommandations, PDF)
- Repositories : acces BDD et aggregation
- Entites : modele de donnees Doctrine

## Services metier
- CarbonCalculatorService : calcule les emissions par categorie
- RecommendationService : suggere des actions par categorie
- PdfReportService : generation du rapport PDF

## API
Exposee via API Platform : CRUD des entites principales et endpoint de calcul.
# My Cinema

Dashboard simple pour afficher films, salles et seances.

## Structure

- backend/
  - app/
    - config/
    - controllers/
    - models/
    - Repositories/
  - public/
- frontend/
  - css/
  - js/
- script.sql

## Prerequis

- PHP 8+
- MySQL
- Python 3 (optionnel, pour servir le frontend)

## Installation

1. Creer la base et les tables

```bash
mysql -u root my_cinema < script.sql
```

2. Demarrer l'API backend

```bash
php -S localhost:8000 -t backend/public
```

3. Servir le frontend (optionnel mais recommande)

```bash
python3 -m http.server 8080 --directory frontend
```

## URLs

- Frontend: http://localhost:8080/index.html
- API films: http://localhost:8000/index.php?resource=films
- API salles: http://localhost:8000/index.php?resource=salles
- API seances: http://localhost:8000/index.php?resource=seances

## Notes

- Les donnees de test peuvent etre ajoutees dans MySQL si besoin.
- Le frontend consomme l'API backend via fetch.

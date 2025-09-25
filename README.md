## ParoCompta

Application Laravel pour la gestion des paroisses, catégories de fiches et fiches hebdomadaires avec génération de documents Word.

### Prérequis
- PHP 8.2+
- Composer
- Node 18+
- SQLite (par défaut) ou MySQL

### Installation
1. Cloner le projet puis installer les dépendances:
   - `composer install`
   - `npm install`
2. Copier l'environnement:
   - `cp .env.example .env` (ou dupliquer manuellement sous Windows)
3. Générer la clé et migrer la base:
   - `php artisan key:generate`
   - `php artisan migrate --seed`
4. Lier le stockage public (pour les téléchargements):
   - `php artisan storage:link`
5. Lancer les serveurs:
   - `php artisan serve`
   - `npm run dev`

### Accès et rôles
- Un utilisateur admin et des paroisses de test sont créés par les seeders (voir `database/seeders`).
- Rôles: `admin` (toutes les paroisses), `user` (restreint à sa paroisse).

### Fonctionnalités
- Paroisses: CRUD, configuration du suivi des paiements et du chemin de stockage.
- Catégories par paroisse: CRUD, upload d'un modèle `.docx`, définition de `fields_json` (structure des champs dynamiques du formulaire).
- Fiches (entries): création avec `week_label`, `start_date`/`end_date`, génération d'un `.docx` depuis le modèle de la catégorie ou upload direct d'un fichier.
- Téléchargement des documents générés, filtrage par mois/trimestre/année.
- API protégée par Sanctum, alignée avec le modèle Web.

### Notes de stockage
- Les fichiers sont rangés sous `storage/app/{storage_path}/paroisse_{id}/trimestre_{n}/categorie_{id}/`.
- Sur Windows/XAMPP, veille à exécuter `php artisan storage:link` en tant qu'administrateur si nécessaire.

### API (extraits)
- `GET /api/parishes`
- `GET /api/parishes/{parish}/categories`
- `POST /api/parishes/{parish}/categories` (name, template_file[.docx], fields_json[array])
- `GET /api/entries?parish_id=...&category_id=...&year=...&month=...`
- `POST /api/entries` (parish_id, category_id, week_label, start_date, end_date, data[array], document[.docx|.doc])
- `GET /api/entries/{id}/download`

### Tests
- Lancer `php artisan test`.

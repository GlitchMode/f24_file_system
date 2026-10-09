# F24 File Browser

Starter structure for the F24 Junior PHP Developer take-home exercise.

This repository intentionally contains **only the project scaffold**. Folder/file creation, browsing, searching, suggestions, and deletion have not been implemented.

## Stack

- Laravel 12 on PHP 8.4
- React 19 and Vite
- SQLite with Eloquent
- REST/JSON routes in `routes/api.php`
- Laravel Form Requests for validation
- PHPUnit feature tests
- Native browser `fetch()` (Axios is not installed)
- Plain CSS
- DDEV, Docker Compose, or a local PHP/Node setup

## Structure

```text
app/
  Http/
    Controllers/Api/   REST/JSON controllers
    Requests/          Form Request validation
  Models/              Eloquent models
database/
  migrations/          Database schema
resources/
  css/                 Plain CSS
  js/
    components/        Reusable React components
    pages/             Page-level React components
    services/          Native fetch() API clients
routes/
  api.php              REST/JSON routes
  web.php              React application entry route
tests/
  Feature/Api/         API feature tests
```

## Run with DDEV (recommended)

Requirements: Docker and DDEV 1.25 or newer.

```bash
ddev start
ddev composer install
ddev npm install
ddev exec cp -n .env.example .env
ddev artisan key:generate
ddev exec touch database/database.sqlite
ddev artisan migrate
ddev npm run dev
```

Open the URL shown by `ddev describe`. This scaffold uses a loopback `nip.io` domain so DDEV can start without modifying the system hosts file.

Run checks in another terminal:

```bash
ddev artisan test
ddev npm run build
```

## Run with Docker Compose

Requirements: Docker with the Compose plugin.

```bash
cp .env.example .env
docker compose build
docker compose run --rm app php artisan key:generate --show
```

Copy the printed key into `APP_KEY` in `.env`, then run:

```bash
docker compose run --rm app php artisan migrate
docker compose up
```

Open <http://localhost:8000>.

## Run without Docker

Requirements: PHP 8.3 or 8.4 with SQLite/PDO SQLite, Composer 2, Node.js 20+ and npm.

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
npm run build
php artisan serve
```

Open <http://localhost:8000>. For frontend hot reload, use `npm run dev` in a second terminal.

Run checks with:

```bash
php artisan test
npm run build
```

## Current status and assumptions

- The architecture and tooling are prepared; the take-home functionality is incomplete by request.
- SQLite is sufficient for the exercise and keeps setup small.
- DDEV starts its standard database container, but the application itself uses the SQLite file in `database/database.sqlite`.
- Authentication and authorization are intentionally excluded by the brief.
- API endpoints should return JSON and use appropriate HTTP methods/status codes when implemented.
- Happy-path feature tests should be added alongside each main API behavior.

## Suggested next steps

1. Design the folder/file data model and migration.
2. Add Eloquent relationships and constraints.
3. Add Form Requests and REST controllers/routes.
4. Write API feature tests for the happy paths.
5. Build the React browser and native `fetch()` service functions.
6. Document completed behavior, assumptions, trade-offs, and remaining work here.

## GitHub

The repository is initialized locally with Git. Create an empty GitHub repository, then connect and push it:

```bash
git remote add origin git@github.com:YOUR_USERNAME/f24.git
git branch -M main
git add .
git commit -m "Initial project scaffold"
git push -u origin main
```

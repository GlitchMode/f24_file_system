# F24 File Browser

A browser-based file manager built for the F24 Junior PHP Developer take-home exercise.

The application supports nested folders, files, folder browsing, exact file search, prefix suggestions, and cascading deletion.

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
  Feature/Feature/Api/ API feature tests
```

## Using the file manager

- Double-click a folder to open it and use the breadcrumb to navigate back.
- Right-click empty workspace space to create a folder or file.
- Right-click a folder or file to delete it. Deleting a folder also deletes its contents.
- Type in the search field to see up to 10 prefix suggestions.
- Submit the search to find exact file-name matches across all files or only the current folder.

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

Open the URL shown by `ddev describe`. This project uses a loopback `nip.io` domain so DDEV can start without modifying the system hosts file.

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

- Folder and file creation, browsing, deletion, exact search, and prefix suggestions are implemented.
- SQLite is sufficient for the exercise and keeps setup small.
- DDEV starts its standard database container, but the application itself uses the SQLite file in `database/database.sqlite`.
- Authentication and authorization are intentionally excluded by the brief.
- The React frontend uses native `fetch()` and plain CSS.
- Feature tests cover the main API happy paths and validation of parent references.

## Suggested next steps

1. Add frontend component tests and end-to-end browser tests.
2. Add keyboard-accessible alternatives for all context-menu actions.
3. Return folder paths with global search results instead of displaying only the folder ID.
4. Add duplicate-name rules if the product requirements require them.

## GitHub

The repository is initialized locally with Git. Create an empty GitHub repository, then connect and push it:

```bash
git remote add origin git@github.com:YOUR_USERNAME/f24.git
git branch -M main
git add .
git commit -m "Initial project scaffold"
git push -u origin main
```

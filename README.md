# F24 File Browser

A browser-based file system built as part of the F24 Junior PHP Developer take-home assignment.

The application allows users to create and browse folders and subfolders, create files, delete files and folders, and search for files by exact filename or filename prefix.

Files do not contain any actual content and are represented only by their filename, as specified in the assignment.

## Features

- Create root folders
- Create nested folders and subfolders
- Create files in the root directory
- Create files inside folders
- Browse the root directory and individual folders
- Delete files
- Delete folders
- Search for files by exact filename
- Search globally or within a specific parent folder
- Return up to 10 filename suggestions using "starts with" matching
- Basic request validation and error handling
- Automated feature tests for the main API functionality
- React frontend for interacting with the filesystem

## Tech Stack

### Backend

- PHP 8.4
- Laravel 12
- Eloquent ORM
- MariaDB

### Frontend

- React
- Vite
- JavaScript

### Development Environment

- Docker
- DDEV

## Requirements

The recommended way to run the application is with DDEV.

You will need:

- Docker
- DDEV
- Git

No local PHP, MariaDB, or Node.js installation is required when using DDEV.

## Installation

Clone the repository:

```bash
git clone <repository-url>
cd <repository-directory>
```

Start the DDEV environment:

```bash
ddev start
```

Install PHP dependencies:

```bash
ddev composer install
```

Create the Laravel environment file if it does not already exist:

```bash
cp .env.example .env
```

Generate the Laravel application key:

```bash
ddev artisan key:generate
```

Run the database migrations:

```bash
ddev artisan migrate
```

Install frontend dependencies:

```bash
ddev npm install
```

## Running the Application

Start the DDEV environment:

```bash
ddev start
```

Start the Vite development server:

```bash
ddev npm run dev
```

The application URL can be displayed with:

```bash
ddev describe
```

For example:

```text
https://f24.127.0.0.1.nip.io
```

Open the displayed HTTPS URL in your browser.

The application is intended to run in development/debug mode for this exercise.

## Running Tests

Run the complete test suite with:

```bash
ddev artisan test
```

Individual tests can also be run using Laravel's test filtering:

```bash
ddev artisan test --filter=FolderTest
```

or:

```bash
ddev artisan test --filter=FileTest
```

The tests use Laravel's database testing utilities so that individual tests do not depend on data created by previous tests.

## API

The frontend communicates with a Laravel JSON API.

### Folders

Create a folder:

```http
POST /api/folders
```

Example request:

```json
{
    "name": "Documents",
    "parent_id": null
}
```

To create a subfolder:

```json
{
    "name": "Work",
    "parent_id": 1
}
```

Browse the root directory:

```http
GET /api/folders
```

Browse a specific folder:

```http
GET /api/folders/{folder}
```

Delete a folder:

```http
DELETE /api/folders/{folder}
```

### Files

Create a file:

```http
POST /api/files
```

Example request:

```json
{
    "name": "report.pdf",
    "folder_id": 1
}
```

A `null` `folder_id` creates the file in the root directory.

Delete a file:

```http
DELETE /api/files/{file}
```

### Exact Filename Search

Search across all files:

```http
GET /api/files/search?name=report.pdf
```

Search only within a specific parent folder:

```http
GET /api/files/search?name=report.pdf&folder_id=1
```

### Filename Suggestions

Return up to 10 files whose filenames begin with the supplied search string:

```http
GET /api/files/suggestions?q=rep
```

For example, a search for `rep` may return:

```text
report.pdf
report-2025.pdf
report-final.pdf
```

The suggestion endpoint only implements prefix matching as required by the assignment.

## Data Model

The filesystem uses two primary database tables.

### folders

Each folder contains:

- `id`
- `name`
- `parent_id`
- timestamps

`parent_id` references another folder.

A `null` `parent_id` represents a folder located in the root directory.

This allows folders to be nested without requiring a separate database record representing the root directory.

### files

Each file contains:

- `id`
- `name`
- `folder_id`
- timestamps

`folder_id` references the folder containing the file.

A `null` `folder_id` represents a file located in the root directory.

No actual file contents are stored.

## Validation and Error Handling

The API performs basic validation including:

- Folder names are required
- File names are required
- Folder and file names are limited to an appropriate maximum length
- Parent folders must exist before creating a child folder
- A file's target folder must exist
- Invalid resource IDs return the appropriate not-found response
- Invalid request data returns Laravel validation responses

Authentication and authorization have intentionally not been implemented, as requested in the assignment specification.

## Assumptions

The following assumptions were made while implementing the assignment:

### Root directory

The root directory is not represented by a database row.

Instead:

```text
parent_id = null
```

represents a folder in the root directory, and:

```text
folder_id = null
```

represents a file in the root directory.

### Folder-scoped search

Searching "within a parent folder" is interpreted as searching files directly contained by that folder.

Files contained in nested descendant folders are not included in that folder-scoped search.

### Files

Files contain only a filename and no file content, metadata, or binary data, following the assignment requirements.

### Duplicate names

If duplicate filenames or folder names are currently allowed:

Duplicate names are currently allowed because the assignment does not explicitly require filesystem-style uniqueness constraints.

In a production implementation, I would clarify the expected duplicate-name behavior and likely prevent two resources of the same type from having the same name within the same parent directory.

## Trade-offs

This project intentionally uses a relatively simple architecture.

The assignment focuses on code readability, maintainability, API design, validation, and software engineering fundamentals rather than production-scale architecture.

For that reason, the application does not introduce additional abstractions such as repository layers, CQRS, event sourcing, or a separate search infrastructure.

Laravel controllers, request validation, Eloquent models, and database relationships provide sufficient separation for the current scope.

The frontend has also intentionally been kept simple because visual design and mobile responsiveness were not requirements of the assignment.

## What I Would Improve With More Time

For a production-scale file browser, I would consider several additional improvements:

- Add database constraints preventing duplicate names within the same parent directory if required
- Add pagination for folders and files
- Add dedicated API Resources to provide a stricter and more stable response format
- Improve search performance for very large datasets
- Evaluate database indexing and collation behavior for prefix searches
- Consider a dedicated search service for sufficiently large datasets
- Add recursive or configurable folder search
- Add move and rename functionality
- Add additional validation around filenames
- Add confirmation dialogs and improved frontend error states
- Add loading indicators and optimistic UI updates
- Add broader edge-case and integration test coverage
- Add production Docker configuration
- Add authentication and authorization if the application were used by multiple users

## Development Notes

The application was created specifically for this technical exercise.

The implementation prioritizes:

- clear naming
- straightforward code structure
- separation between the API, persistence, and frontend layers
- correct HTTP methods and status codes
- maintainability
- ease of local setup

The project intentionally avoids unnecessary production-scale complexity.
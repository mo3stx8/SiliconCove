# Silicon Cove 

**Technologies:**
- Composer
- PHP (Laravel X12)
- npm

## Installation

1. Clone the repository:

```bash
git clone https://github.com/mo3stx7/SiliconCove.git
```

2. Go to the repository 

```bash
cd repo-name
```

3. Install Packages

```bash
composer install
```

4. Copy `.env` file

```bash
cp .env.example .env
```

5. Generate the app key

```bash
php artisan key:generate
```

6. Setting up your database credentials in your `.env` file.

7. Create the Database after 

8. Seed Database: 

```bash
php artisan migrate:fresh --seed
```

9. Create Storage Link

```bash
php artisan storage:link
```

10. Install NPM dependencies 

```bash
npm install && npm run dev
```

11. Run 

```bash
php artisan serve
```
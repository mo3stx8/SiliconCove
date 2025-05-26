# Silicon Cove 

**Technologies:**
- Composer
- PHP
- npm

## Installation

1. Clone the repository:

```bash
git clone https://github.com/your-username/your-repo.git

 markdown
bash
cd your-repo
composer install
cp .env.example .env
php artisan key:generate
php artisan storage:link
php artisan migrate:fresh --seed
php artisan serve
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan optimize:clear
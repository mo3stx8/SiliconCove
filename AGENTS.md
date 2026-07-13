# SiliconCove — Project Context

## Stack
- Laravel 12 + MySQL
- Bootstrap 5 + Tailwind CSS + Vite
- PHP 8.2+

## Key Commands
- `composer run dev` — start dev server + queue + logs + Vite
- `php artisan migrate:fresh --seed` — reset DB with seed data
- `php artisan storage:link` — create storage symlink
- `npm run dev` — Vite dev server

## Deployment
- GitHub Actions CI runs tests on push/PR to `main`
- Deploy workflow requires repo secrets: `DEPLOY_HOST`, `DEPLOY_USER`, `DEPLOY_SSH_KEY`, `DEPLOY_PATH`
- CI uses MySQL 8.0 service container

## Secrets (CRITICAL — already leaked in git history)
- Google OAuth: revoke at https://console.cloud.google.com
- GitHub OAuth: revoke at https://github.com/settings/developers
- Gmail app password: revoke at https://myaccount.google.com/apppasswords
- APP_KEY: regenerate with `php artisan key:generate`

## Security
- `.env` is in `.gitignore` — never commit it
- `.env.example` contains only placeholder values

# Lootora

> Self-hosted **rewards / GPT platform** built with Laravel 8. Users earn points from offerwalls, surveys, bonus codes, daily check-in, and referrals — then cash out via PayPal, USDT, e-wallets, and bank transfer.

**Stack:** Laravel 8.x · PHP 8+ · MySQL/MariaDB (or SQLite for dev) · Bootstrap 5 + Tailwind CDN · Livewire 2 · Laravel Mix

---

## Features

- Offerwall provider integrations with **S2S postback** support (CPX Research, BitLabs, AdGate, AdGem, OfferToro, Pollfish, Generic).
- Hardened **withdrawal workflow** — strict status transitions, refund-once safety, admin notes, IP logging.
- Modern **admin dashboard** with KPIs, system health, deployment status, integration coverage, payout summary, setup checklist, recent admin actions.
- **12-section admin sidebar** — Users, Providers, Payout Methods, Withdrawals, Postback Logs, Fraud Logs, Bonus Codes, Bonus History, Site Settings, Integration Guide, Leads, Dashboard.
- **Bonus codes**, **referrals**, **daily check-in**, **leaderboard**.
- **/health** endpoint for Docker / uptime monitors.
- **Production-ready Docker stack** — multi-stage build, nginx + php-fpm + MySQL + queue worker + scheduler.

---

## Quick deploy with Docker

The fastest way to get Lootora running on any Linux/macOS/Windows machine that has Docker.

```bash
# 1. Clone
git clone https://github.com/your-user/Lootora.git
cd Lootora

# 2. Bootstrap env
cp .env.docker.example .env

# 3. Build and start
make up
# (or: docker compose up -d --build)

# 4. Migrate + seed (first run only)
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force
```

Open **http://localhost:8000** and log in:

| Field    | Value                |
|----------|----------------------|
| Email    | `admin@lootora.net`  |
| Password | `password`           |

**Change this password immediately** in Settings → Profile.

### Useful Make targets

```
make up          # build + start
make down        # stop
make logs        # tail app logs
make shell       # bash into app container
make migrate     # run migrations
make seed        # idempotent seeders
make fresh       # DROP + re-migrate + re-seed (DEV ONLY)
make optimize    # cache config/route/view for prod
make clear       # optimize:clear
make test        # phpunit
```

### Health check

```bash
curl http://localhost:8000/health
# => {"status":"ok","app":"Lootora","database":"ok","storage":"ok"}
```

### Auto-migrate / auto-seed

Toggle in `.env`:

```
AUTO_MIGRATE=true   # run migrate --force on container boot
AUTO_SEED=false     # safer default for prod
```

---

## Production with Docker Hub

Build once, deploy anywhere:

```bash
# Build and tag
docker build -t your-dockerhub-user/lootora:1.0.0 .

# Push
docker login
docker push your-dockerhub-user/lootora:1.0.0

# On the server, only need .env + docker-compose.prod.yml
DOCKER_IMAGE=your-dockerhub-user/lootora:1.0.0 \
  docker compose -f docker-compose.prod.yml up -d
```

> **Never** push `.env`, `database/database.sqlite`, or `storage/app/private/*` to Docker Hub or GitHub.

---

## Local development without Docker

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite      # if using SQLite
php artisan migrate --seed
php artisan storage:link
npm install && npm run dev          # Laravel Mix (NOT Vite)
php artisan serve
```

---

## Adding a provider

1. Go to **Admin → Providers → Add provider**.
2. Fill name, slug, app_id, postback secret, revenue share %.
3. Paste the **iframe URL template** with `{user_id}` placeholder, e.g.
   ```
   https://offers.cpx-research.com/index.php?app_id=YOUR_APP_ID&ext_user_id={user_id}
   ```
4. Copy your **postback URL** shown on the provider page into the provider's S2S settings.
5. Toggle **Active** and test from the user `/earn` page.

See **Admin → Integration Guide** for per-provider templates, curl test, and troubleshooting.

---

## Troubleshooting

| Symptom                                | Fix                                                                                          |
|----------------------------------------|----------------------------------------------------------------------------------------------|
| 500 on first load                      | `docker compose exec app php artisan key:generate && optimize:clear`                         |
| Provider logos blank                   | Upload via Admin → Providers → Edit. Without a file, initials placeholder is shown.          |
| MySQL connection refused               | `docker compose logs mysql` — wait for "ready for connections" then retry.                   |
| Assets / Tailwind not loading          | `docker compose exec app sh -c 'npm install && npm run prod'` or rebuild the image.          |
| `/health` returns 503                  | DB or storage symlink failed. Run `php artisan storage:link`.                                |
| Withdrawals stuck                      | Check **Admin → Withdrawals**. Status transitions are strict: paid/rejected are terminal.    |

---

## Project structure (key paths)

```
app/Http/Controllers/Admin/         # Admin CRUD + dashboard
app/Http/Controllers/User/          # User-facing dashboard, wallet, earn, etc.
app/Models/                         # Eloquent models with hasLogo() helpers
app/Support/Lootora.php             # Brand + currency helpers
database/seeders/                   # AdminUser, PayoutMethod, SiteSettings, Bonus
docker/                             # nginx, php, entrypoint, supervisord
resources/views/admin/              # New Tailwind admin layout
resources/views/user/               # User dashboard
routes/                             # web.php / admin.php / api.php
```

---

## License

MIT — see [LICENSE](LICENSE).

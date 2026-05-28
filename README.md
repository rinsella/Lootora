# Lootora

> **Complete missions. Earn rewards. Unlock your loot.**

Lootora is a modern, mobile-first web rewards platform built on Laravel. Users complete offers, surveys, games and daily missions from integrated offerwall providers to earn **LOOT Points** (`$LOOT`) that can be redeemed via supported payout methods.

- Production domain: **lootora.net**
- Internal currency: **LOOT Points** (token symbol `$LOOT`)
- Stack: Laravel 9.x · PHP 8+ · MySQL/MariaDB · Bootstrap 5 (legacy admin) + Tailwind CSS via CDN (new UI) · Livewire

> Lootora does **not** require deposits, does **not** publish fake payment proof, and does **not** guarantee earnings. Rewards depend on provider availability, eligibility, and successful completion verification.

---

## Features

### User
- Email/password registration with optional reCAPTCHA
- Modern mobile-first dashboard with balance card, streak card, offer preview, quick actions
- Earn page with offerwall provider cards, search, category filters and sort
- Wallet/Withdraw with saved payout accounts (PayPal, USDT, DANA, OVO, GoPay, Bank Transfer, Gift Card)
- Daily check-in streak with bonus LOOT
- Referral system with unique referral code & link
- Notifications, earning history, transaction history
- Banned / Suspicious account flows

### Admin
- Users management (search, filter, ban, mark suspicious, manual point adjust with reason)
- Offerwall provider CRUD (logo, iframe template, postback secret, IP whitelist, revenue share, sort, status)
- Provider transactions (filterable, CSV export, linked postback log)
- Withdrawals (approve / reject / mark paid / note)
- Site settings (branding, point rate, min withdrawal, referral %, check-in reward, maintenance mode, etc.)
- Logs: postback, fraud, admin actions

### Postback Engine
- Generic endpoint `/api/postback/{provider}` plus legacy per-provider endpoints
- Every postback is logged to `postback_logs` *before* processing
- Signature validation, optional IP whitelist
- Duplicate transaction protection (responds `DUPLICATE`, never credits twice)
- DB-transactional credit with row lock on user balance
- Supports reversals / chargebacks (negative payouts)
- Responses: `OK` · `DUPLICATE` · `REJECTED` · `ERROR`

---

## Requirements

- PHP **>= 8.0** with extensions: `bcmath`, `ctype`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `tokenizer`, `xml`
- Composer 2+
- MySQL 5.7+ / MariaDB 10.3+
- Node 16+ and npm (for asset build)
- Apache (with `mod_rewrite`) or Nginx

---

## Installation

```bash
git clone <your-repo-url> lootora
cd lootora

composer install --optimize-autoloader

cp .env.example .env
php artisan key:generate
```

Edit `.env` and set:

```dotenv
APP_NAME=Lootora
APP_URL=https://lootora.net

DB_DATABASE=lootora
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

MAIL_FROM_ADDRESS=no-reply@lootora.net
```

Then migrate and build assets:

```bash
php artisan migrate --seed
php artisan storage:link

npm install
npm run prod
```

Run locally:

```bash
php artisan serve
```

---

## Cron & Queue

Add this single cron entry on the server (covers scheduled jobs such as streak resets and daily aggregates):

```cron
* * * * * cd /home/user/lootora && php artisan schedule:run >> /dev/null 2>&1
```

If you switch `QUEUE_CONNECTION` to `database` or `redis`, run a worker:

```bash
php artisan queue:work --tries=3 --timeout=60
```

A `supervisor` config is recommended for VPS deployments.

---

## Offerwall Providers

Each provider supports:

- `iframe_url_template` — e.g. `https://provider.example/wall?appid=123&uid={user_id}&sub1={username}`
- Available placeholders: `{user_id}`, `{username}`, `{email}`, `{country}`, `{ip}`
- `postback_url` — the URL Lootora exposes to the provider
- `postback_secret` and optional `ip_whitelist`
- `revenue_share_percentage` — user's share of the provider payout (USD)

### Postback URLs

- Generic: `https://lootora.net/api/postback/{provider_slug}`
- Legacy per-provider routes (Adgem, CPALead, OfferToro, Revenue, AdscendMedia, AdwallGate, Admantum, Mediumpath, Monlix, Notik, CPXResearch, etc.) remain wired in `routes/api.php` for backwards compatibility.

### Reward calculation

```
user_payout_usd     = provider_payout_usd * (revenue_share_percentage / 100)
loot_points         = user_payout_usd * LOOT_USD_TO_POINTS         # default 1 USD = 1000 LOOT
platform_profit_usd = provider_payout_usd - user_payout_usd
```

All money & point columns are `DECIMAL(16,4)` — never `FLOAT`.

---

## Deployment — cPanel (shared hosting)

1. Upload the project to `/home/<user>/lootora` (outside `public_html`).
2. Move/symlink the contents of `public/` into `public_html/` and edit `public_html/index.php`:
   ```php
   require __DIR__.'/../lootora/vendor/autoload.php';
   $app = require_once __DIR__.'/../lootora/bootstrap/app.php';
   ```
3. In cPanel → **Setup PHP**: choose PHP 8.x and enable required extensions.
4. SSH and run `composer install --no-dev --optimize-autoloader`.
5. Create DB in MySQL Databases, then run `php artisan migrate --seed`.
6. Set permissions:
   ```bash
   chmod -R 775 storage bootstrap/cache
   chown -R <user>:<user> storage bootstrap/cache
   ```
7. Add the cron entry above in **Cron Jobs**.

## Deployment — aaPanel / VPS (Nginx + PHP-FPM)

1. Create site → set document root to `/www/wwwroot/lootora/public`.
2. SSH:
   ```bash
   cd /www/wwwroot/lootora
   composer install --no-dev --optimize-autoloader
   cp .env.example .env && nano .env
   php artisan key:generate
   php artisan migrate --seed
   php artisan storage:link
   npm install && npm run prod
   chown -R www:www storage bootstrap/cache
   chmod -R 775 storage bootstrap/cache
   ```
3. Add Nginx rewrite for Laravel (aaPanel offers a one-click preset for Laravel).
4. Enable SSL via Let's Encrypt for `lootora.net` and `www.lootora.net`.
5. Add the cron entry above under **Cron**.

---

## Security

- CSRF on all forms · auth middleware · admin middleware · `not_banned` middleware
- Rate limiting on auth and postback endpoints
- Signature + optional IP whitelist on postbacks
- Duplicate transaction protection
- All admin balance adjustments require a reason and are logged
- Fraud logs for multi-account / IP change / postback flood / suspicious UA
- Validation on every form, XSS-safe Blade output, secure logo uploads

---

## Testing

```bash
php artisan test
```

Suggested feature tests:

- Valid postback credits the user
- Duplicate postback does **not** credit twice
- Postback with unknown `user_id` is rejected
- Postback with invalid signature is rejected
- Withdrawal request deducts points
- Rejected withdrawal refunds points
- Referral reward is created when referred user completes a mission
- Daily check-in only succeeds once per 24h

---

## License

MIT

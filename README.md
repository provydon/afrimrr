# AfriMRR

**Verified MRR for African startups.** TrustMRR, but it plugs into the payment rails
African founders actually use — Paystack, Flutterwave and Stripe.

Founders connect a read-only API key, we read their active subscriptions directly from the
provider, normalize every plan interval and currency to a monthly USD figure, and publish a
tamper-proof, always-live MRR. No screenshots, no vanity metrics.

## Features

- **Verified MRR** via Paystack, Flutterwave & Stripe (subscription APIs). Monnify is
  intentionally unsupported — it has no native subscription product to verify against.
- **Interval + FX normalization** — daily→annual plans and NGN/KES/GHS/ZAR/… all normalized to monthly USD.
- **Leaderboard** — Top 100 by verified MRR with MoM growth and medals.
- **Directory** — browse/search/filter by category & country.
- **Public startup & founder profiles** with MRR history sparkline.
- **Embeddable SVG badge** that auto-updates (`/badge/{slug}.svg`).
- **Marketplace** — list startups for sale with a revenue multiple.
- **Badge store** — free digital badge, physical plexiglass plaque, Pro tier.
- **Public JSON API** — `GET /api/startups`, `GET /api/startups/{slug}`.
- **Hourly re-sync** scheduler + encrypted credential storage.

## Stack

Laravel 13 · Inertia · Vue 3 + TypeScript · Tailwind v4 · PostgreSQL · Octane (FrankenPHP).
Containerized with [`provydon/laravel-scale`](https://github.com/provydon/laravel-scale).

## Local development

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed   # seeds demo (fictional) startups
npm run dev
php artisan serve
```

The MRR engine:

- `App\Services\Mrr\IntervalNormalizer` — any interval → monthly minor units.
- `App\Services\Mrr\CurrencyConverter` — local minor units → USD cents (FX in `config/mrr.php`).
- `App\Services\Mrr\{Paystack,Flutterwave,Stripe}Connector` — pull active subscriptions.
- `App\Actions\SyncStartup` — aggregate, snapshot, compute MoM growth.
- `php artisan mrr:sync` — scheduled hourly.

## Deploy (Render)

This repo ships a `render.yaml` blueprint (web + worker + Postgres + env group). Set the
`APP_KEY` and `APP_URL` secrets in the env group after the first deploy.

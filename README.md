# Mini Influencer Tracker

A Laravel + React + Inertia application that tracks influencer profile statistics from Instagram via Apify.

The system fetches profile data asynchronously using queued jobs, stores historical snapshots, prevents duplicate concurrent fetches using PostgreSQL advisory locks, and includes resiliency features such as retry classification, token-bucket rate limiting, circuit breakers, webhook verification, and health monitoring.

---

## Features

- Add influencer profiles
- Fetch Instagram statistics via Apify
- Snapshot history
- Queue processing
- Concurrency safety
- Token bucket rate limiting
- Circuit breaker
- HMAC webhook verification
- Health check endpoint

---

## Tech Stack

- Laravel 13
- PHP 8.3
- PostgreSQL
- React
- TypeScript
- Inertia.js
- Tailwind CSS

---

## Installation
- git clone <repository-url>
- cd miniinfluencer
- composer install
- npm install
- cp .env.example .env
- php artisan key:generate
- php artisan migrate
- npm run dev
- php artisan queue:work

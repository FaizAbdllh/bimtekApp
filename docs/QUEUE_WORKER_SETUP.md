Queue worker setup
===================

This document describes two production-ready options for running Laravel queue workers for this project.

Prerequisites
-------------
- Ensure the `jobs` and `failed_jobs` tables are migrated (this repo already includes the migration).
- Install PHP, Composer and the app dependencies on the server.
- Make sure the application `APP_ENV` and `.env` are set correctly.

Option A — Supervisor + `queue:work` (database or redis driver)
------------------------------------------------------------
When you use the `database` queue driver the app stores jobs in DB (migration exists). For higher throughput use `redis`.

1. Set the `.env` values on the server:

```bash
QUEUE_CONNECTION=database   # or redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

2. Install Supervisor (example for Ubuntu):

```bash
sudo apt update
sudo apt install supervisor
```

3. Example Supervisor program config (see file `deploy/supervisor/laravel-worker.conf` in this repo). Place it under `/etc/supervisor/conf.d/` and reload:

```bash
sudo cp deploy/supervisor/laravel-worker.conf /etc/supervisor/conf.d/laravel-worker.conf
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

Notes for the Supervisor config:
- Replace `/var/www/html/your-app` with your application path.
- The command runs `php artisan queue:work` which is recommended for long-running workers.
- Tune `--sleep`, `--tries`, and `--timeout` values to fit your environment.

Option B — Redis + Laravel Horizon
----------------------------------
Horizon provides a nice UI and process management for Redis-backed queues.

1. Install and configure Redis on the server.
2. Install Horizon:

```bash
composer require laravel/horizon
php artisan horizon:install
php artisan migrate
```

3. Publish and review `config/horizon.php` to tune supervisors and queue names.
4. Run Horizon under Supervisor or systemd. Example systemd unit is in `deploy/horizon/horizon.service`.

Supervisor vs systemd
---------------------
- Either process manager is fine. Supervisor is widely used and flexible. For systemd, use the provided `horizon.service` file and enable it with `systemctl enable --now horizon`.

Testing queues locally
----------------------
To test a single job immediately:

```bash
php artisan queue:work --once
```

To dispatch a job and see it processed by the worker run the worker in another terminal:

```bash
php artisan queue:work
php artisan tinker
// dispatch job from tinker
```

Monitoring
----------
- For Horizon visit `/horizon` (requires auth — see Horizon docs).
- For Supervisor check `supervisorctl status` and logs under `/var/log/supervisor`.

Security & environment
----------------------
- Never commit secrets. Keep `.env` out of repo.
- Ensure queue workers run under the correct system user (the same user that owns the storage/cache folders).

Files included in this repo
---------------------------
- `deploy/supervisor/laravel-worker.conf` — example Supervisor config for `queue:work`.
- `deploy/horizon/horizon.service` — example systemd service for Laravel Horizon.

If you want, I can install and enable a Supervisor config for you, or create a PR with these files. Tell me which option you prefer (Supervisor+database, Supervisor+redis, or Horizon+redis).

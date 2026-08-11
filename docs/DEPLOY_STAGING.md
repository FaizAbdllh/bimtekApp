Deploy to Staging — Guide
========================

This guide explains how to deploy the `feature/revisi-bimtek` branch to a staging server.

Manual deploy (SSH)
-------------------

1. Ensure the server has `git`, `php`, `composer`, `node`, `npm`, and `supervisor`/`systemd` installed.
2. From your development machine run:

```bash
./deploy/deploy_to_staging.sh user@staging.example.com /var/www/html/bimtekApp
```

3. After deploy, verify:

- `php artisan migrate:status`
- `php artisan queue:work` (or Supervisor/Horizon running)
- Application reachable and logs have no errors (`storage/logs/laravel.log`).

Automated deploy (GitHub Actions)
--------------------------------

1. Configure repository secrets:
- `STAGING_HOST`
- `STAGING_USER`
- `STAGING_PATH`
- `STAGING_SSH_KEY` (private key)
- `STAGING_SSH_PORT` (optional)
- `COMPOSER_AUTH` (if using private composer repos)

2. Push branch — workflow will run on `push` to `feature/revisi-bimtek`.

3. Monitor Actions tab for logs. If deploy fails, inspect logs and fix.

Post-deploy QA checklist
------------------------

- Login as `pic@test.com`, generate token for `peserta@test.com` and verify email queued and delivered.
- Use raw token to visit `/activate/{token}` and complete activation.
- Verify `used_at` set and subsequent uses blocked.
- Generate batch tokens (admin) and download CSV — confirm raw tokens present.
- Revoke a token and verify activation blocked.
- Export tokens per `bimtek` and confirm CSV contents.
- Verify UI in browser (invite link, generate/print modal, copy buttons).

If you want, provide SSH access (username/host and private key) and I can trigger a test deployment using the script or enable the GitHub Actions workflow for you.

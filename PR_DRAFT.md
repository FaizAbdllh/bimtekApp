PR: Implement invite-code registration, activation tokens, CSV export, and queued mails

Summary

This PR adds support for:
- Per-bimtek shared invite codes and public self-registration gated by the invite code.
- Per-participant activation tokens (single-use, expiry) with admin/panitia generation.
- CSV export of activation tokens per bimtek.
- Small admin UI on `bimtek.show` to create/copy/regenerate invite link and export tokens.
- Per-participant "Generate Token" button in the peserta list; returns raw token via AJAX and opens a print-friendly window for easy printing.
- Queueing of `ActivationTokenMail` for async delivery.

Migrations

- database/migrations/2026_05_25_000001_create_activation_tokens_table.php (added)
- database/migrations/2026_05_25_000002_add_invite_code_to_bimteks_table.php (added)
- database/migrations/2026_05_25_000003_add_bimtek_to_activation_tokens.php (added)

Key Files Changed / Added

- routes/web.php: added routes for public registration, activation, invite-code generation and token export.
- app/Models/ActivationToken.php: model for tokens (generate, findByRawToken, markUsed, isExpired); added `bimtek_id` fillable.
- app/Http/Controllers/ActivationController.php: token generation now associates `bimtek_id`, supports AJAX JSON responses, and queues email sending.
- app/Http/Controllers/BimtekController.php: `generateInviteCode()` and `exportActivationTokens()` implemented.
- app/Mail/ActivationTokenMail.php: now implements `ShouldQueue`.
- resources/views/bimtek/show.blade.php: UI for invite link (create/regenerate/copy) and CSV export button.
- resources/views/peserta/index.blade.php: per-participant "Generate Token" button + JS for AJAX, clipboard copy, and print-friendly output.
- tests/Feature/InviteCodeRegistrationTest.php: feature tests for invite-code gated registration.
- tests/Feature/ActivationTokenGenerationTest.php: test for panitia token generation.

Behavior Notes

- Tokens are hashed with SHA-256 and stored in `activation_tokens.token_hash`. Raw tokens are shown once (returned to requester/email).
- Tokens expire after 7 days by default and are single-use (marked via `used_at`).
- Public registration (`/bimtek/{bimtek}/daftar`) requires the invite code if `bimtek.invite_code` is set.
- `ActivationTokenMail` is queued; ensure `php artisan queue:work` or a queued worker is running in production.

Testing

Run migrations and the test suite locally:

```bash
php artisan migrate
php artisan test
```

To observe queued mail delivery locally (synchronous mail drivers will still work), run a queue worker:

```bash
php artisan queue:work
```

Recommended Next Steps

- Add a small admin modal for printing multiple tokens at once (batch generate + CSV-with-raw option) if needed by the institution.
- Add audit log / revoke UI for un-used tokens.
- Configure and run queue workers in production (Supervisor/Horizon) and ensure `QUEUE_CONNECTION` is set.

If you want, I can open a GitHub Pull Request draft with this description and the branch `feature/revisi-bimtek`.

QA & Staging Checklist
----------------------

Before merging to `main`, verify the following in a staging environment:

- [ ] Set up staging `.env` (DB, MAIL, QUEUE_CONNECTION, APP_KEY).
- [ ] Run `php artisan migrate --force` on staging.
- [ ] Ensure queue worker is running (Supervisor/Horizon) and processes `ActivationTokenMail` jobs.
- [ ] Generate per-participant token (panitia) and verify raw token delivered via AJAX and email.
- [ ] Generate batch tokens (admin) and verify CSV contains raw tokens for secure distribution.
- [ ] Test activation flow: visit `/activate/{token}`, complete registration, ensure `used_at` is set and subsequent use fails.
- [ ] Test expired token behavior by setting `expires_at` in the past and verifying activation blocked.
- [ ] Test revoke: revoke a token and confirm it cannot be used.
- [ ] Export tokens CSV per `bimtek` and validate format and encoding.
- [ ] Verify UI elements: invite link create/regenerate/copy, print modal, buttons disabled/enabled states.
- [ ] Run full test suite in staging or CI and ensure green checks.

Add any reviewer notes or known caveats here.


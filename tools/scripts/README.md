# Tools / Scripts

Small developer QA scripts used during local testing. These are convenience helpers and should not be used in production environments.

Location: `tools/scripts/`

Available scripts:

- `create_activation.php` — Generate an activation token raw value for an existing user.
  Usage: `php tools/scripts/create_activation.php user@example.com [days] [bimtek_id]`

- `check_token.php` — Inspect an activation token record by raw token value.
  Usage: `php tools/scripts/check_token.php <raw_token>`

- `activate_token.php` — Activate a user using a raw token (server-side helper).
  Usage: `php tools/scripts/activate_token.php <raw_token> <new_password>`

- `describe_table.php` — Print column list for a database table.
  Usage: `php tools/scripts/describe_table.php [table_name]`

Notes:
- These scripts bootstrap the Laravel application and will execute against your local environment and database. Make sure you point your `.env` to a local dev DB or use a disposable environment.
- Keep these scripts under `tools/` to avoid accidental execution in automated environments. If you'd prefer, we can move them to a separate repository for tooling.

Security:
- Do not commit real secrets when using these scripts.
- Tokens printed by `create_activation.php` are raw activation tokens; treat them as credentials and remove them from logs if necessary.

If you want, I can also add small unit tests around token generation/inspection scripts or a Github Actions workflow to run `pint` + `phpstan` + `phpunit` on PRs.
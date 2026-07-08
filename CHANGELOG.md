# Release Notes

## [v1.1.1]() - 2026-07-08

### Fixed

* Default webhook route in config (`signhost.webhook.route`) pointed to `laravel-signhost.postback`, which does not exist — the registered route is `laravel-signhost.postback.transaction`. Creating a transaction without an explicit `postbackUrl` threw a `RouteNotFoundException`.
* `Transaction` model cast `status_code` to `TransactionStatus`, but the actual column is `status`. The enum cast never applied, so `$transaction->status` returned a raw int instead of a `TransactionStatus` instance.
* `sh_transactions` table was missing `finalized` and `finalized_at` columns. `Transactions::markFinalized()` updated `finalized_at` only, which Eloquent silently discarded (mass-assignment guard drops unknown columns) — transactions were never actually marked finalized.

### Breaking

* New migration adds `finalized` (bool) and `finalized_at` (datetime) columns to `sh_transactions`. Republish and run migrations.
* `$transaction->status` now returns a `TransactionStatus` enum instance instead of a raw int. Update any code comparing it directly against an integer.

## [v1.0.0]() - 2026-01-09

* Initial release

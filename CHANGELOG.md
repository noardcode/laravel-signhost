# Release Notes

## [v1.1.1]() - 2026-07-09

### Fixed

* Default webhook route in config (`signhost.webhook.route`) pointed to `laravel-signhost.postback`, which does not exist — the registered route is `laravel-signhost.postback.transaction`. Creating a transaction without an explicit `postbackUrl` threw a `RouteNotFoundException`.
* `Transaction` model cast `status_code` to `TransactionStatus`, but the actual column is `status`. The enum cast never applied, so `$transaction->status` returned a raw int instead of a `TransactionStatus` instance.
* `sh_transactions` table was missing `finalized` and `finalized_at` columns. `Transactions::markFinalized()` updated `finalized_at` only, which Eloquent silently discarded (mass-assignment guard drops unknown columns) — transactions were never actually marked finalized.
* `startTransaction()` never checked the HTTP response — a rejected start request (e.g. transaction already started) failed silently instead of throwing.
* `FormSet\Location`'s `search`/`occurence` were required, non-nullable constructor arguments. Coordinate-only placement (no text search) had to pass `search: ''`, which SignHost's API treats as an invalid search rather than "no search" — the field silently fell back to SignHost's default placement instead of the requested coordinates.
* The wire-format key for `occurence` was spelled `Occurence`; SignHost's API expects `Occurrence` (two c's).
* A `FormSet` had no way to be linked to a signer — `FileMetaData` only supported `setFormSet()`, never `setSigners()`/`addSigner()`. SignHost accepts a FormSet with no signer reference (`meta_data_exported` succeeds) but silently ignores it and falls back to its own default field placement, producing the exact same symptom as the `Search: ''` bug above.

### Added

* `FileMetaData::addSigner()` and `FileUpload::addSigner()`, to link a signer (by id) to one or more FormSets by name — required for a FormSet to actually be applied by SignHost, see Fixed above.

### Breaking

* New migration adds `finalized` (bool) and `finalized_at` (datetime) columns to `sh_transactions`. Republish and run migrations.
* `$transaction->status` now returns a `TransactionStatus` enum instance instead of a raw int. Update any code comparing it directly against an integer.
* `FormSet\Location`'s `$search`/`$occurence` constructor arguments are now nullable and default to `null`. `toArray()` omits `Search`/`Occurrence` entirely when `$search` is `null`, matching SignHost's coordinate-only placement format. If you were relying on `Search`/`Occurrence` always being present in the array output, update accordingly.
* `startTransaction()` now throws `SignhostException` on a non-2xx response instead of returning silently.

## [v1.1.0]() - 2026-07-03

### Added

* PHP 8.5 support.
* Laravel 13 support.
* Deprecation notice for the IdProof feature — SignHost is phasing out the current IdProof flow, new projects should not build on it.

## [v1.0.0]() - 2026-01-09

* Initial release

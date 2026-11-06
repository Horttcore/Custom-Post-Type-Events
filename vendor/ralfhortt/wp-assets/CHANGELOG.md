# 3.1.0 - 2026-08-07

- Improve multisite and multidomain URL detection for local assets
- Resolve filesystem paths across scheme mismatches and domain-mapped hosts
- Warn and return `null` instead of throwing when an asset path is unreadable
- Add Pest test suite with Brain Monkey WordPress stubs
- Add PHPStan linting with WordPress stubs (`composer lint`)
- Add GitHub Actions CI for lint and tests

# 3.0.0 - 2024-06-18

- Add loading strategy for script assets

# 2.2.2 - 2024-03-24

-   Fix bug

# 2.2.1 - 2024-03-20

-   Fix bug

# 2.2.0 - 2024-03-20

-   Deprecate $inFooter in script assets

# 2.1.0 - 2023-02-23

-   Add `PrintStyle` class

# 2.0.0 - 2020-03-25

-   Fix cache busting
-   Remove relative path support
-   Improve child theme handling
-   Rename composer package name

# 1.1.0 - 2019-10-04

-   Register assets globally.
-   Assets can be enqueued only by the handle, if they are already registered.

# 1.0.0 - 2019-04-10

-   Initial release.

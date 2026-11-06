# WP Assets

[![CI](https://github.com/Horttcore/wp-assets/actions/workflows/ci.yml/badge.svg)](https://github.com/Horttcore/wp-assets/actions/workflows/ci.yml)
[![Latest Stable Version](https://poser.pugx.org/ralfhortt/wp-assets/v/stable)](https://packagist.org/packages/ralfhortt/wp-assets)
[![License](https://poser.pugx.org/ralfhortt/wp-assets/license)](https://packagist.org/packages/ralfhortt/wp-assets)
[![PHP Version Require](https://poser.pugx.org/ralfhortt/wp-assets/require/php)](https://packagist.org/packages/ralfhortt/wp-assets)

A small helper package for registering and enqueuing WordPress scripts and styles.

## Requirements

- PHP `>= 8.2`
- WordPress (functions used at runtime)

## Installation

```bash
composer require ralfhortt/wp-assets
```

## Documentation

### Scripts

```php
use RalfHortt\Assets\Script;
use RalfHortt\Assets\AdminScript;
use RalfHortt\Assets\EditorScript;
use RalfHortt\Assets\LoginScript;

new Script(string $handle, string $source = '', array $dependencies = [], string|bool|null $version = null, array|bool $args = []);
new AdminScript(string $handle, string $source = '', array $dependencies = [], string|bool|null $version = null, array|bool $args = []);
new EditorScript(string $handle, string $source = '', array $dependencies = [], string|bool|null $version = null, array|bool $args = []);
new LoginScript(string $handle, string $source = '', array $dependencies = [], string|bool|null $version = null, array|bool $args = []);
```

### Inline scripts

```php
use RalfHortt\Assets\InlineScript;

new InlineScript(string $handle, string $data, string $position = 'after');
```

### Styles

```php
use RalfHortt\Assets\Style;
use RalfHortt\Assets\AdminStyle;
use RalfHortt\Assets\EditorStyle;
use RalfHortt\Assets\LoginStyle;
use RalfHortt\Assets\PrintStyle;

new Style(string $handle, string $source = '', array $dependencies = [], string|bool|null $version = true, string $media = 'all');
new AdminStyle(string $handle, string $source = '', array $dependencies = [], string|bool|null $version = true, string $media = 'all');
new EditorStyle(string $handle, string $source = '', array $dependencies = [], string|bool|null $version = true, string $media = 'all');
new LoginStyle(string $handle, string $source = '', array $dependencies = [], string|bool|null $version = true, string $media = 'all');
new PrintStyle(string $handle, string $source = '', array $dependencies = [], string|bool|null $version = true, string $media = 'all');
```

Pass absolute asset URLs as `$source` (for example from `get_stylesheet_directory_uri()` or `plugins_url()`). Local URL detection supports multisite, `home`/`siteurl` mismatches, and http/https differences so cache-busting via `filemtime` keeps working.

## Usage

```php
use RalfHortt\Assets\Style;
use RalfHortt\Assets\InlineScript;
use RalfHortt\Assets\LoginStyle;

(new Style('my-theme', get_stylesheet_directory_uri() . '/theme.css'))->register();

(new InlineScript('my-theme', 'const ThemeName = "Awesome";'))->register();

// Reuse a registered handle on another hook
(new Style('my-theme'))->register();
(new LoginStyle('my-theme'))->register();
```

```php
use RalfHortt\Assets\Script;

(new Script(
    'my-theme',
    get_stylesheet_directory_uri() . '/app.js',
    dependencies: ['wp-element'],
    version: true,
    args: ['strategy' => 'defer'],
))->register();
```

## Development

```bash
composer install
composer lint
composer test
```

- `composer lint` — PHPStan (level max) with WordPress stubs
- `composer test` — Pest suite for URL/path/version resolution

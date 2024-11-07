# WP Block

A composer wrapper for ServerSideRender blocks

## Installation

`$ composer require ralfhortt/wp-block`

## Usage

- Create a custom class that extends `RalfHortt\WPBlock\Block`
- Set `$name` property
- Set `$attributes` property
- Add a `render` method

## Example

```php
<?php
use RalfHortt\WPBlock\Block;

class MyBlock extends Block {
	protected $name = 'ralfhortt/myblock';
	protected $attributes = [
		'postType' => [
			'type' => 'string',
			'default' => '',
		],
		// …
	];

	protected function render($atts, $content): void
	{
		$query = new WP_Query([
			'post_type' => $atts['postType'],
			'showposts' => 5,
		]);

		if ( $query->have_posts()) {
			while ( $query->have_posts() ) {
				$query->the_post();
				the_title();
			}
		}

		wp_reset_query();
	}
}

class MyOtherBlock extends Block {
	protected string $name = 'ralfhortt/myotherblock';
	protected string $title = 'My other Block';
	protected string $blockJson = 'block.json';
	// …
}
```

## Hooks

### Actions

- `{$name}/before` - Before block output
- `{$name}/after` - After block output
-
### Filters

- `{$name}/name` - Block name
- `{$name}/attributes` - Block attributes
- `{$name}/render` - Overwrite render callback

## Changelog

### v1.2 - 2022-05-10

- Add support for `block.json`
- 
### v1.1 - 2020-05-25

- Apply fixes from StyleCI

### v1.0 - 2020-01-17

- Initial release

# WordPress Meta Box Helper

## Installation

`$ composer require ralfhortt/wp-meta-box`

## Usage

* Extend `RalfHortt\MetaBoxes\MetaBox()`
* You _MUST_ set `$this->identifier`, `$this->name`, `$this->screen` in the class constructor
* You _CAN_ set the additional variables `$this->context`, `$this->priority`, `$this->callbackArgs`
* You _MUST_ Add a `render()` method
* You _CAN_ add a `save()` method
* A nonce is added automatically and checked

## Extend

### Action

* `do_action("before-meta-box-{$this->identifier}", $post, $callbackArgs)`
* `do_action("after-meta-box-{$this->identifier}", $post, $callbackArgs)`
* `do_action("saved-meta-box-{$this->identifier}", $postId, $post, $update)`

## Example

```php
<?php
use RalfHortt\MetaBoxes\MetaBox;

class MyMetaBox extends MetaBox
{
	
	public function __construct()
	{
		$this->identifier = 'my-meta-box';
		$this->name = __('My Meta Box', 'textdomain');
		$this->screen = ['post'];
		$this->context = 'side';
		$this->priority = 'high';
	}

	protected function render(\WP_Post $post): void 
	{
		?>
		<label for="my-meta">Meta Label</label>
		<input id="my-meta" name="my-meta" class="regular-text" type="text" value="<?= esc_attr(get_post_meta($post->ID, 'my-meta', true )) ?>">
		<?php
	}

	protected function save(int $postId, \WP_Post $post, bool $update): void
	{
		update_post_meta($postId, 'my-meta', sanitize_text_field($_POST['my-meta']));
	}
}
```

## Changelog

### 2.0

* Change namespace
* Changed visibility of `render` and `save` form public to protected
* Save method is now not mandatory
* Added return type definition
* Added action hooks
* Validate `context` and `priority`
* Fixed meta box callback arguments

### 1.1

* Add save_post args

### 1.0

* Initial release

# WordPress Meta Box Helper

## Usage

* Extend `Horttcore\MetaBoxes\MetaBox()`
* You _MUST_ set `$this->identifier`, `$this->name`, `$this->screen` in the class constructor
* You _CAN_ set the additional variables `$this->context`, `$this->priority`, `$this->callbackArgs`
* Add a `render()` method
* Add a `save()` method
* A nonce is added automatically

## Changelog

### v1.0.0

* Initial release
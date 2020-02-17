# Custom Taxonomy Helper Class

## Installation

`composer require Horttcore\wp-custom-taxonomy`

## Usage

Extend the abstract class `Taxonomy` and overwrite following methods:

* `getConfig()`
* `getLabels()`

The extending class _MUST_ define protected class variable `slug`
# Custom Post Type Events

A custom post type to manage events

## Supports

* Title
* Post-Thumbnail
* Page Attributes

## Custom Fields

* eventStart
* eventEnd

## Template tags

* `theEventDate($format, $before, $after)`
* `theEventTime($format, $before, $after)`
* `theEventDateTime($format, $before, $after)`

## Functions

* `getEventInfo($postId)`
* `getEventDate($postId, $format)`
* `getEventTime($postId, $format)`
* `getEventDateTime($postId, $format)`
* `eventIsAllDay($postId)`
* `eventIsMultiDay($postId)`

* `getPreviousEvent($postId)`
* `getNextEvent($postId)`

## Language Support

* english
* german

Translation ready

## Hooks

### Actions

### Filters

#### Template file for the loop

`add_filter('custom-post-type-events-loop-template', fn () => __DIR__.'/views/block-events.php');`

#### Template file for a single entry in the loop

`add_filter('custom-post-type-events-single-template', fn () => __DIR__.'/views/block-event.php');`

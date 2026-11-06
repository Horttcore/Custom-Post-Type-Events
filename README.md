# Custom Post Type Events

A custom post type to manage events

## Supports

* Title
* Post-Thumbnail
* Page Attributes

## Custom Fields

* eventStart
* eventEnd
* eventOccurrences (array of `{ id, range }`)
* eventPrimaryRange
* eventLastEnd
* eventIsAllDay (boolean)

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

## Blocks

* **Events Date** (`custom-post-type-events/events-date`) — display formatted event date/time in the editor.

For event lists, use the **Query Loop** block with post type **event** (sorting by event date is applied when `orderby` is left at the default for that query).

## Hooks

### Actions

### Filters

#### Event date/time output

* `event-date-separator` — between start and end dates in `getEventDate()` (multi-day)
* `event-time-separator` — between start and end times in `getEventTime()`
* `event-date-time-separator` — between date and time in `getEventDatetime()`

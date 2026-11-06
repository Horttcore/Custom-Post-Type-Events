import {
  BlockControls,
  useBlockProps,
  InspectorControls,
} from "@wordpress/block-editor";
import { __, _x } from "@wordpress/i18n";
import { dateI18n, getSettings } from "@wordpress/date";
import {
  Button,
  DatePicker,
  DateTimePicker,
  Dropdown,
  PanelBody,
  ToolbarButton,
  ToolbarGroup,
  TimePicker,
  TextControl,
  ToggleControl,
} from "@wordpress/components";
import DateTimeDropdownControl from "../../shared/date-time-dropdown-control";
import {
  formatEventDateTime,
  parseEventDateTime,
  toPickerDateTime,
} from "../../shared/event-date-time";
import useEventDateMeta from "../../shared/use-event-date-meta";

const Edit = ({ attributes, setAttributes, context }) => {
  const {
    format,
    timeSeparator = " | ",
    showTime = true,
    showEndDate = true,
    oneLineRange = false,
    dateRangeSeparator = "\u2013",
    timeFormat = "H:i",
  } = attributes;

  const {
    isEventPost,
    meta,
    parsedStart,
    startDate,
    endDate,
    isAllDay,
    isMultiDay,
    showEndField,
    canShowTime,
    toggleAllDay,
    toggleMultiDay,
    updateStartFromValue,
    updateEndFromValue,
    updateEndTimeFromValue,
    clearEventDate,
  } = useEventDateMeta({
    context,
    preferContext: true,
  });

  // Only show for event post type
  if (!isEventPost) {
    return (
      <div {...useBlockProps()}>
        <p>
          {__(
            "This block only works with event posts.",
            "custom-post-type-events",
          )}
        </p>
      </div>
    );
  }

  // Check if we have event meta loaded
  if (!meta) {
    return (
      <div {...useBlockProps()}>
        <p>{__("No event date available.", "custom-post-type-events")}</p>
      </div>
    );
  }

  if (!parsedStart) {
    return (
      <div {...useBlockProps()}>
        <p>{__("No event date set.", "custom-post-type-events")}</p>
      </div>
    );
  }

  // Format the dates
  const formatDate = (dateString, customFormat = null) => {
    const parsedDate = parseEventDateTime(dateString);
    if (!parsedDate) return "";
    const normalizedDate = formatEventDateTime(parsedDate);
    if (!normalizedDate) return "";

    const dateFormat = customFormat || format || getSettings().formats.date;
    return dateI18n(dateFormat, normalizedDate);
  };

  const formatTime = (dateString) => {
    const parsedDate = parseEventDateTime(dateString);
    if (!parsedDate) return "";
    const normalizedDate = formatEventDateTime(parsedDate);
    if (!normalizedDate) return "";

    return dateI18n(timeFormat || "H:i", normalizedDate);
  };

  // Parse separator for HTML
  const parseSeparator = (separator) => {
    if (!separator) return null;

    // Check if separator contains HTML tags
    const htmlTagRegex = /<[^>]*>/;
    if (htmlTagRegex.test(separator)) {
      return <span dangerouslySetInnerHTML={{ __html: separator }} />;
    }
    return separator;
  };

  const renderSeparator = (separator, key) => {
    const parsedSeparator = parseSeparator(separator);

    if (!parsedSeparator) {
      return null;
    }

    if (typeof parsedSeparator === "string") {
      return <span key={key}>{parsedSeparator}</span>;
    }

    return <span key={key}>{parsedSeparator}</span>;
  };

  const showTimeOutput = showTime && canShowTime;
  const startDateOutput = formatDate(meta.eventStart);
  const endDateOutput =
    endDate && showEndDate && meta.eventEnd ? formatDate(meta.eventEnd) : "";
  const startTimeOutput = showTimeOutput ? formatTime(meta.eventStart) : "";
  const endTimeOutput =
    showTimeOutput && meta.eventEnd ? formatTime(meta.eventEnd) : "";

  const hasOutput = Boolean(startDateOutput);

  const startToolbarText = [
    __("Start", "custom-post-type-events"),
    startDateOutput,
    startTimeOutput,
  ]
    .filter(Boolean)
    .join(" ");

  const endToolbarDate = formatDate(meta.eventEnd);
  const endToolbarTime = formatTime(meta.eventEnd);
  const endToolbarText = [
    __("End", "custom-post-type-events"),
    endToolbarDate || __("set", "custom-post-type-events"),
    endToolbarTime,
  ]
    .filter(Boolean)
    .join(" ");

  const showEndInControls = showEndField;

  // Get WordPress date format
  const wpDateFormat = getSettings().formats.date;

  return (
    <>
      <BlockControls>
        <ToolbarGroup>
          <Dropdown
            popoverProps={{ placement: "bottom-start" }}
            renderToggle={({ isOpen, onToggle }) => (
              <ToolbarButton
                label={__("Edit start date", "custom-post-type-events")}
                isPressed={isOpen}
                onClick={onToggle}
              >
                {startToolbarText}
              </ToolbarButton>
            )}
            renderContent={() =>
              isAllDay ? (
                <DatePicker
                  currentDate={toPickerDateTime(startDate)}
                  onChange={updateStartFromValue}
                />
              ) : (
                <DateTimePicker
                  currentDate={toPickerDateTime(startDate)}
                  onChange={updateStartFromValue}
                  is12Hour={false}
                />
              )
            }
          />

          {showEndInControls && (
            <Dropdown
              popoverProps={{ placement: "bottom-start" }}
              renderToggle={({ isOpen, onToggle }) => (
                <ToolbarButton
                  label={
                    isMultiDay
                      ? __("Edit end date", "custom-post-type-events")
                      : __("Edit end time", "custom-post-type-events")
                  }
                  isPressed={isOpen}
                  onClick={onToggle}
                >
                  {endToolbarText}
                </ToolbarButton>
              )}
              renderContent={() =>
                isAllDay ? (
                  <DatePicker
                    currentDate={toPickerDateTime(endDate)}
                    onChange={updateEndFromValue}
                  />
                ) : !isMultiDay ? (
                  <TimePicker
                    currentTime={toPickerDateTime(endDate)}
                    onChange={updateEndTimeFromValue}
                    is12Hour={false}
                  />
                ) : (
                  <DateTimePicker
                    currentDate={toPickerDateTime(endDate)}
                    onChange={updateEndFromValue}
                    is12Hour={false}
                  />
                )
              }
            />
          )}
        </ToolbarGroup>
      </BlockControls>

      <InspectorControls>
        <PanelBody title={__("Event Date", "custom-post-type-events")}>
          <ToggleControl
            label={__("Multi-day", "custom-post-type-events")}
            checked={isMultiDay}
            onChange={toggleMultiDay}
          />
          <ToggleControl
            label={__("All-day", "custom-post-type-events")}
            checked={isAllDay}
            onChange={toggleAllDay}
          />

          <DateTimeDropdownControl
            label={
              isMultiDay
                ? __("Start", "custom-post-type-events")
                : __("Date", "custom-post-type-events")
            }
            currentDate={toPickerDateTime(startDate)}
            mode={isAllDay ? "date" : "datetime"}
            onChange={updateStartFromValue}
            is12Hour={false}
          />

          {isMultiDay && (
            <DateTimeDropdownControl
              label={__("End", "custom-post-type-events")}
              currentDate={toPickerDateTime(endDate)}
              mode={isAllDay ? "date" : "datetime"}
              onChange={updateEndFromValue}
              is12Hour={false}
            />
          )}

          {!isMultiDay && !isAllDay && (
            <DateTimeDropdownControl
              label={__("End time", "custom-post-type-events")}
              currentDate={toPickerDateTime(endDate)}
              mode="time"
              onChange={updateEndTimeFromValue}
              is12Hour={false}
            />
          )}
          <Button variant="tertiary" isDestructive onClick={clearEventDate}>
            {__("Clear event date", "custom-post-type-events")}
          </Button>
        </PanelBody>

        <PanelBody title={__("Date Settings", "custom-post-type-events")}>
          <TextControl
            label={__("Date Format", "custom-post-type-events")}
            value={format}
            onChange={(newFormat) => setAttributes({ format: newFormat })}
            placeholder={wpDateFormat}
            help={__(
              `Leave empty to use WordPress default format: ${wpDateFormat}. Use PHP date format characters (e.g., F j, Y for "October 24, 2025").`,
              "custom-post-type-events",
            )}
          />
          {canShowTime && (
            <ToggleControl
              label={__("Show Time", "custom-post-type-events")}
              checked={showTime}
              onChange={(newShowTime) =>
                setAttributes({ showTime: newShowTime })
              }
              help={__(
                "Display the event time along with the date.",
                "custom-post-type-events",
              )}
            />
          )}

          {isMultiDay && (
            <ToggleControl
              label={__("Show End Date", "custom-post-type-events")}
              checked={showEndDate}
              onChange={(newShowEndDate) =>
                setAttributes({ showEndDate: newShowEndDate })
              }
              help={__(
                "Display the end date for multi-day events.",
                "custom-post-type-events",
              )}
            />
          )}

          {isMultiDay && showEndDate && (
            <ToggleControl
              label={__("Show Range In One Line", "custom-post-type-events")}
              checked={oneLineRange}
              onChange={(newOneLineRange) =>
                setAttributes({ oneLineRange: newOneLineRange })
              }
              help={__(
                "Display start and end for multi-day events in one line.",
                "custom-post-type-events",
              )}
            />
          )}

          {isMultiDay && showEndDate && (
            <TextControl
              label={__("Date range separator", "custom-post-type-events")}
              value={dateRangeSeparator}
              onChange={(newSeparator) =>
                setAttributes({
                  dateRangeSeparator: newSeparator,
                })
              }
              placeholder="\u2013"
              help={__(
                "Separator between start and end date (one-line range) or between start date and end date (two-line range). Use an en dash (–) or your preferred character.",
                "custom-post-type-events",
              )}
            />
          )}

          {showTime && canShowTime && (
            <>
              <TextControl
                label={__("Time Format", "custom-post-type-events")}
                value={timeFormat}
                onChange={(newTimeFormat) =>
                  setAttributes({ timeFormat: newTimeFormat })
                }
                placeholder="H:i"
                help={__(
                  'PHP time format, e.g. "H:i" (14:30) or "g:i a" (2:30 pm).',
                  "custom-post-type-events",
                )}
              />
              <TextControl
                label={__("Time Separator", "custom-post-type-events")}
                value={timeSeparator}
                onChange={(newSeparator) =>
                  setAttributes({ timeSeparator: newSeparator })
                }
                placeholder=" | "
                help={__(
                  'Separator between date and time. Supports HTML (e.g., <br /> for line break, <span class="separator"> | </span>).',
                  "custom-post-type-events",
                )}
              />
            </>
          )}
        </PanelBody>
      </InspectorControls>
      <div {...useBlockProps()}>
        {hasOutput ? (
          <time dateTime={meta.eventStart}>
            {isMultiDay && endDate && showEndDate ? (
              <>
                <span>{startDateOutput}</span>
                {startTimeOutput &&
                  renderSeparator(timeSeparator, "start-separator")}
                {startTimeOutput && <span>{startTimeOutput}</span>}
                {oneLineRange ? (
                  <span>{dateRangeSeparator || "\u2013"}</span>
                ) : (
                  <>
                    <span>
                      {_x(
                        "to",
                        "Line break between start and end in multi-day range (lowercase)",
                        "custom-post-type-events",
                      )}
                    </span>
                    <br />
                  </>
                )}
                <span>{endDateOutput}</span>
                {endTimeOutput &&
                  renderSeparator(timeSeparator, "end-separator")}
                {endTimeOutput && <span>{endTimeOutput}</span>}
              </>
            ) : isMultiDay ? (
              <>
                <span>{startDateOutput}</span>
                {startTimeOutput &&
                  renderSeparator(timeSeparator, "start-only-separator")}
                {startTimeOutput && <span>{startTimeOutput}</span>}
              </>
            ) : (
              <>
                <span>{startDateOutput}</span>
                {startTimeOutput &&
                  renderSeparator(timeSeparator, "single-separator")}
                {startTimeOutput && <span>{startTimeOutput}</span>}
                {endTimeOutput && <span>{` - ${endTimeOutput}`}</span>}
              </>
            )}
          </time>
        ) : (
          <p>{__("No event date available.", "custom-post-type-events")}</p>
        )}
      </div>
    </>
  );
};

export default Edit;

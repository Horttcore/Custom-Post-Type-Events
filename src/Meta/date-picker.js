import React from "react";
import { __ } from "@wordpress/i18n";
import { PluginDocumentSettingPanel } from "@wordpress/editor";
import { Button, ToggleControl } from "@wordpress/components";
import DateTimeDropdownControl from "../shared/date-time-dropdown-control";
import { toPickerDateTime } from "../shared/event-date-time";
import useEventDateMeta from "../shared/use-event-date-meta";

export default function EventDateTimePanel() {
  const {
    isEventPost,
    postId,
    meta,
    parsedStart,
    startDate,
    endDate,
    isAllDay,
    isMultiDay,
    toggleAllDay,
    toggleMultiDay,
    updateStartFromValue,
    updateEndFromValue,
    updateEndTimeFromValue,
    clearEventDate,
  } = useEventDateMeta();

  if (!isEventPost || !postId || !meta) {
    return null;
  }

  return (
    <PluginDocumentSettingPanel
      name="datetime-picker"
      title={__("Event Date", "custom-post-type-events")}
    >
      {parsedStart && (
        <div style={{ display: "flex", gap: "1em" }}>
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
        </div>
      )}

      <DateTimeDropdownControl
        label={
          parsedStart && isMultiDay
            ? __("Start", "custom-post-type-events")
            : __("Date", "custom-post-type-events")
        }
        currentDate={parsedStart ? toPickerDateTime(startDate) : ""}
        mode={parsedStart && isAllDay ? "date" : "datetime"}
        onChange={updateStartFromValue}
        is12Hour={false}
      />

      {parsedStart && isMultiDay && (
        <>
          <DateTimeDropdownControl
            label={__("End", "custom-post-type-events")}
            currentDate={toPickerDateTime(endDate)}
            mode={isAllDay ? "date" : "datetime"}
            onChange={updateEndFromValue}
            is12Hour={false}
          />
        </>
      )}

      {parsedStart && !isMultiDay && !isAllDay && (
        <DateTimeDropdownControl
          label={__("End time", "custom-post-type-events")}
          currentDate={toPickerDateTime(endDate)}
          mode="time"
          onChange={updateEndTimeFromValue}
          is12Hour={false}
        />
      )}
      {parsedStart && (
        <Button variant="tertiary" isDestructive onClick={clearEventDate}>
          {__("Clear event date", "custom-post-type-events")}
        </Button>
      )}
    </PluginDocumentSettingPanel>
  );
}

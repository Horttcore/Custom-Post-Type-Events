import React from "react";
import {
  Button,
  DatePicker,
  DateTimePicker,
  Dropdown,
  TimePicker,
} from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import { dateI18n, getSettings } from "@wordpress/date";
import { formatEventDateTime, parseEventDateTime } from "./event-date-time";

const getDateTimeLabel = (currentDate, mode = "datetime") => {
  if (!currentDate) {
    return __("Select date and time", "custom-post-type-events");
  }

  const parsedDate = parseEventDateTime(currentDate);
  if (!parsedDate) {
    return __("Select date and time", "custom-post-type-events");
  }

  const settings = getSettings();
  const dateFormat = settings?.formats?.date || "Y-m-d";
  const timeFormat = settings?.formats?.time || "H:i";
  const normalizedDate = formatEventDateTime(parsedDate);
  if (!normalizedDate) {
    return __("Select date and time", "custom-post-type-events");
  }

  if (mode === "date") {
    return dateI18n(dateFormat, normalizedDate);
  }

  if (mode === "time") {
    return dateI18n(timeFormat, normalizedDate);
  }

  return dateI18n(`${dateFormat} ${timeFormat}`, normalizedDate);
};

export default function DateTimeDropdownControl({
  label,
  currentDate,
  onChange,
  mode = "datetime",
  is12Hour = false,
}) {
  const emptyLabel =
    mode === "date"
      ? __("Select date", "custom-post-type-events")
      : mode === "time"
      ? __("Select time", "custom-post-type-events")
      : __("Select date and time", "custom-post-type-events");

  return (
    <div style={{ marginBottom: "1em" }}>
      <p>
        <strong>{label}</strong>
      </p>
      <Dropdown
        popoverProps={{ placement: "bottom-start" }}
        renderToggle={({ isOpen, onToggle }) => (
          <Button
            variant="secondary"
            onClick={onToggle}
            aria-expanded={isOpen}
            style={{ width: "100%", justifyContent: "space-between" }}
          >
            {currentDate ? getDateTimeLabel(currentDate, mode) : emptyLabel}
          </Button>
        )}
        renderContent={() =>
          mode === "date" ? (
            <DatePicker currentDate={currentDate} onChange={onChange} />
          ) : mode === "time" ? (
            <TimePicker
              currentTime={currentDate}
              onChange={onChange}
              is12Hour={is12Hour}
            />
          ) : (
            <DateTimePicker
              currentDate={currentDate}
              onChange={onChange}
              is12Hour={is12Hour}
            />
          )
        }
      />
    </div>
  );
}

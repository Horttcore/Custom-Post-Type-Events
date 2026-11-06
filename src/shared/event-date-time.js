const pad = (value) => String(value).padStart(2, "0");

export const parseEventDateTime = (value) => {
  if (value instanceof Date) {
    return cloneDate(value);
  }

  if (typeof value === "number") {
    const timestamp = value < 10000000000 ? value * 1000 : value;
    const parsed = new Date(timestamp);
    return Number.isNaN(parsed.getTime()) ? null : parsed;
  }

  if (!value || typeof value !== "string") {
    return null;
  }

  const raw = value.trim();
  if (!raw) {
    return null;
  }

  // Let the runtime parse full ISO strings with timezone offsets first.
  if (/[zZ]|[+-]\d{2}:\d{2}$/.test(raw)) {
    const isoDate = new Date(raw);
    if (!Number.isNaN(isoDate.getTime())) {
      return isoDate;
    }
  }

  const normalized = raw.replace("T", " ").replace("Z", "");

  const dateTimeMatch = normalized.match(
    /^(\d{4})-(\d{2})-(\d{2})(?:\s+(\d{2}):(\d{2})(?::(\d{2}))?)?$/,
  );

  if (dateTimeMatch) {
    const [, year, month, day, hours = "00", minutes = "00", seconds = "00"] =
      dateTimeMatch;
    const parsed = new Date(
      Number(year),
      Number(month) - 1,
      Number(day),
      Number(hours),
      Number(minutes),
      Number(seconds),
    );

    return Number.isNaN(parsed.getTime()) ? null : parsed;
  }

  const [datePart, timePart = "00:00:00"] = normalized.split(" ");

  if (!datePart) {
    return null;
  }

  const [year, month, day] = datePart.split("-").map(Number);
  const [hours = 0, minutes = 0, seconds = 0] = timePart.split(":").map(Number);

  if (!year || !month || !day) {
    return null;
  }

  return new Date(year, month - 1, day, hours, minutes, seconds);
};

export const formatEventDateTime = (date) => {
  if (!(date instanceof Date) || Number.isNaN(date.getTime())) {
    return "";
  }

  const year = date.getFullYear();
  const month = pad(date.getMonth() + 1);
  const day = pad(date.getDate());
  const hours = pad(date.getHours());
  const minutes = pad(date.getMinutes());
  const seconds = pad(date.getSeconds());

  return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
};

export const toPickerDateTime = (date) => {
  const formatted = formatEventDateTime(date);
  return formatted ? formatted.replace(" ", "T") : "";
};

export const toPickerTime = (date) => {
  const formatted = formatEventDateTime(date);
  if (!formatted) {
    return "";
  }

  const [, time = ""] = formatted.split(" ");
  return time;
};

export const isEventAllDay = (startDate, endDate) => {
  if (!startDate || !endDate) {
    return false;
  }

  const startsAtMidnight =
    startDate.getHours() === 0 &&
    startDate.getMinutes() === 0 &&
    startDate.getSeconds() === 0;

  const endsAtAllDayBoundary =
    (endDate.getHours() === 23 &&
      endDate.getMinutes() === 59 &&
      endDate.getSeconds() >= 0) ||
    (endDate.getHours() === 0 &&
      endDate.getMinutes() === 0 &&
      endDate.getSeconds() === 0);

  return startsAtMidnight && endsAtAllDayBoundary;
};

export const isEventMultiDay = (startDate, endDate) => {
  if (!startDate || !endDate) {
    return false;
  }

  return (
    startDate.getFullYear() !== endDate.getFullYear() ||
    startDate.getMonth() !== endDate.getMonth() ||
    startDate.getDate() !== endDate.getDate()
  );
};

export const cloneDate = (date) => {
  if (!(date instanceof Date) || Number.isNaN(date.getTime())) {
    return null;
  }

  return new Date(date.getTime());
};

export const normalizeEventRange = ({
  startDate,
  endDate,
  isAllDay,
  isMultiDay,
}) => {
  const nextStart = cloneDate(startDate);

  if (!nextStart) {
    return {
      eventStart: "",
      eventEnd: "",
    };
  }

  const nextEnd = cloneDate(endDate) ?? cloneDate(startDate);

  if (!nextEnd) {
    return {
      eventStart: formatEventDateTime(nextStart),
      eventEnd: "",
    };
  }

  if (!isMultiDay) {
    nextEnd.setFullYear(
      nextStart.getFullYear(),
      nextStart.getMonth(),
      nextStart.getDate(),
    );
  }

  if (isAllDay) {
    nextStart.setHours(0, 0, 0, 0);
    nextEnd.setHours(23, 59, 59, 0);
  }

  return {
    eventStart: formatEventDateTime(nextStart),
    eventEnd: formatEventDateTime(nextEnd),
  };
};

export const getTodayAllDayRangeDates = () => {
  const startDate = new Date();
  startDate.setHours(0, 0, 0, 0);

  const endDate = cloneDate(startDate) ?? new Date(startDate.getTime());
  endDate.setHours(23, 59, 59, 0);

  return {
    startDate,
    endDate,
  };
};

export const getTimedRangeDates = ({
  startDate,
  endDate,
  isMultiDay,
  startHour = null,
  startMinute = 0,
  endHour = null,
  endMinute = 0,
}) => {
  const nextStart = cloneDate(startDate) ?? new Date();
  const nextEnd = cloneDate(endDate) ?? cloneDate(nextStart) ?? new Date();

  const now = new Date();
  const nextFullHour = cloneDate(now) ?? new Date();
  nextFullHour.setMinutes(0, 0, 0);
  nextFullHour.setHours(nextFullHour.getHours() + 1);

  const resolvedStartHour = startHour ?? nextFullHour.getHours();
  const resolvedEndHour = endHour ?? (resolvedStartHour + 1) % 24;

  if (!isMultiDay) {
    nextEnd.setFullYear(
      nextStart.getFullYear(),
      nextStart.getMonth(),
      nextStart.getDate(),
    );
  }

  nextStart.setHours(resolvedStartHour, startMinute, 0, 0);
  nextEnd.setHours(resolvedEndHour, endMinute, 0, 0);

  if (!isMultiDay && nextEnd <= nextStart) {
    nextEnd.setHours(nextStart.getHours() + 1, endMinute, 0, 0);
  }

  return {
    startDate: nextStart,
    endDate: nextEnd,
  };
};

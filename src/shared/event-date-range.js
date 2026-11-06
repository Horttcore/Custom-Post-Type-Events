import { parseEventDateTime, formatEventDateTime } from "./event-date-time";

export const parseEventDateRange = (range) => {
  if (!range || typeof range !== "string" || !range.includes("/")) {
    return null;
  }

  const [start, end] = range.split("/").map((part) => part.trim());
  const startDate = parseEventDateTime(start);
  const endDate = parseEventDateTime(end);

  if (!startDate || !endDate || endDate < startDate) {
    return null;
  }

  return {
    start,
    end,
    startDate,
    endDate,
  };
};

export const formatEventDateRange = (startDate, endDate) => {
  const start = formatEventDateTime(startDate);
  const end = formatEventDateTime(endDate);

  if (!start || !end) {
    return "";
  }

  return `${start}/${end}`;
};

export const isValidEventDateRange = (range) => parseEventDateRange(range) !== null;

export const isEventAllDayRange = (range) => {
  const parsed = parseEventDateRange(range);

  if (!parsed) {
    return false;
  }

  const startTime = parsed.start.split(" ")[1] ?? "";
  const endTime = parsed.end.split(" ")[1] ?? "";

  return startTime === "00:00:00" && (endTime === "23:59:59" || endTime === "00:00:00");
};

export const isEventMultiDayRange = (range) => {
  const parsed = parseEventDateRange(range);

  if (!parsed) {
    return false;
  }

  return (
    parsed.startDate.getFullYear() !== parsed.endDate.getFullYear() ||
    parsed.startDate.getMonth() !== parsed.endDate.getMonth() ||
    parsed.startDate.getDate() !== parsed.endDate.getDate()
  );
};

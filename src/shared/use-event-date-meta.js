import { useEntityProp } from "@wordpress/core-data";
import { useSelect } from "@wordpress/data";
import { useEffect } from "@wordpress/element";
import {
  cloneDate,
  getTimedRangeDates,
  getTodayAllDayRangeDates,
  isEventAllDay,
  isEventMultiDay,
  normalizeEventRange,
  parseEventDateTime,
} from "./event-date-time";

const clearedEventDatePostIds =
  globalThis.customPostTypeEventsClearedDatePostIds ||
  (globalThis.customPostTypeEventsClearedDatePostIds = new Set());

export default function useEventDateMeta({
  context = null,
  preferContext = false,
} = {}) {
  const editorState = useSelect(
    (select) => ({
      postType: select("core/editor").getCurrentPostType(),
      postId: select("core/editor").getCurrentPostId(),
    }),
    [],
  );

  const resolvedPostType =
    (preferContext && context?.postType) || editorState.postType || null;
  const resolvedPostId =
    (preferContext && context?.postId) || editorState.postId || null;

  const safePostType = resolvedPostType || "event";
  const safePostId = resolvedPostId || 0;

  const [meta, setMeta] = useEntityProp(
    "postType",
    safePostType,
    "meta",
    safePostId,
  );

  const parsedStart = parseEventDateTime(meta?.eventStart);
  const parsedEnd = parseEventDateTime(meta?.eventEnd);

  const startDate = parsedStart ?? new Date();
  const endDate = parsedEnd ?? cloneDate(startDate) ?? new Date();

  const isAllDay = isEventAllDay(startDate, endDate);
  const isMultiDay = isEventMultiDay(startDate, endDate);
  const showEndField = isMultiDay || !isAllDay;
  const canShowTime = !isAllDay;

  useEffect(() => {
    if (
      resolvedPostType !== "event" ||
      !resolvedPostId ||
      !meta ||
      meta.eventStart ||
      clearedEventDatePostIds.has(resolvedPostId)
    ) {
      return;
    }

    const { startDate: defaultStart, endDate: defaultEnd } =
      getTodayAllDayRangeDates();
    const range = normalizeEventRange({
      startDate: defaultStart,
      endDate: defaultEnd,
      isAllDay: true,
      isMultiDay: false,
    });

    setMeta({
      ...meta,
      ...range,
    });
  }, [meta, resolvedPostId, resolvedPostType, setMeta]);

  const getSingleDayEndDate = () => {
    const nextEnd = cloneDate(endDate) ?? cloneDate(startDate) ?? new Date();
    nextEnd.setFullYear(
      startDate.getFullYear(),
      startDate.getMonth(),
      startDate.getDate(),
    );
    return nextEnd;
  };

  const getMultiDayEndDate = () => {
    const nextEnd = cloneDate(endDate) ?? cloneDate(startDate) ?? new Date();

    if (isEventMultiDay(startDate, nextEnd)) {
      return nextEnd;
    }

    const fallback = cloneDate(startDate) ?? new Date();
    fallback.setDate(fallback.getDate() + 1);

    if (isAllDay) {
      fallback.setHours(23, 59, 59, 0);
    }

    return fallback;
  };

  const updateMetaRange = ({
    start = startDate,
    end = endDate,
    nextIsAllDay = isAllDay,
    nextIsMultiDay = isMultiDay,
  }) => {
    if (!meta) {
      return;
    }

    const range = normalizeEventRange({
      startDate: start,
      endDate: end,
      isAllDay: nextIsAllDay,
      isMultiDay: nextIsMultiDay,
    });

    setMeta({
      ...meta,
      ...range,
    });
  };

  const toggleMultiDay = (nextIsMultiDay) => {
    updateMetaRange({
      end: nextIsMultiDay ? getMultiDayEndDate() : getSingleDayEndDate(),
      nextIsMultiDay,
    });
  };

  const toggleAllDay = (nextIsAllDay) => {
    const timedRange = getTimedRangeDates({
      startDate,
      endDate,
      isMultiDay,
    });

    updateMetaRange(
      nextIsAllDay
        ? {
            nextIsAllDay,
          }
        : {
            start: timedRange.startDate,
            end: timedRange.endDate,
            nextIsAllDay,
          },
    );
  };

  const updateStartFromValue = (value) => {
    const nextStart = parseEventDateTime(value);
    if (!nextStart) {
      return;
    }

    updateMetaRange({ start: nextStart });
  };

  const updateEndFromValue = (value) => {
    const nextEnd = parseEventDateTime(value);
    if (!nextEnd) {
      return;
    }

    updateMetaRange({ end: nextEnd });
  };

  const updateEndTimeFromValue = (value) => {
    let nextHours = null;
    let nextMinutes = null;
    let nextSeconds = 0;

    const parsedDateTime = parseEventDateTime(value);
    if (parsedDateTime) {
      nextHours = parsedDateTime.getHours();
      nextMinutes = parsedDateTime.getMinutes();
      nextSeconds = parsedDateTime.getSeconds();
    } else if (typeof value === "string") {
      const timeMatch = value.trim().match(/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/);
      if (!timeMatch) {
        return;
      }

      const [, hours, minutes, seconds = "00"] = timeMatch;
      nextHours = Number(hours);
      nextMinutes = Number(minutes);
      nextSeconds = Number(seconds);
    } else {
      return;
    }

    const baseEndDate =
      cloneDate(endDate) ?? cloneDate(startDate) ?? new Date();

    if (!isMultiDay) {
      baseEndDate.setFullYear(
        startDate.getFullYear(),
        startDate.getMonth(),
        startDate.getDate(),
      );
    }

    baseEndDate.setHours(nextHours, nextMinutes, nextSeconds, 0);

    updateMetaRange({ end: baseEndDate });
  };

  const clearEventDate = () => {
    if (!meta) {
      return;
    }

    if (resolvedPostId) {
      clearedEventDatePostIds.add(resolvedPostId);
    }

    setMeta({
      ...meta,
      eventStart: "",
      eventEnd: "",
    });
  };

  return {
    postType: resolvedPostType,
    postId: resolvedPostId,
    isEventPost: resolvedPostType === "event",
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
  };
}

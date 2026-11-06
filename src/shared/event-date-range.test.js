import {
  formatEventDateRange,
  isEventAllDayRange,
  isEventMultiDayRange,
  isValidEventDateRange,
  parseEventDateRange,
} from "./event-date-range";

describe("parseEventDateRange", () => {
  it("parses a valid slash-separated range", () => {
    const parsed = parseEventDateRange(
      "2026-01-05 10:00:00/2026-01-05 12:00:00",
    );

    expect(parsed).toEqual({
      start: "2026-01-05 10:00:00",
      end: "2026-01-05 12:00:00",
      startDate: expect.any(Date),
      endDate: expect.any(Date),
    });
  });

  it("returns null for invalid input", () => {
    expect(parseEventDateRange("invalid")).toBeNull();
    expect(parseEventDateRange("")).toBeNull();
  });
});

describe("formatEventDateRange", () => {
  it("formats start and end into a range string", () => {
    const start = new Date(2026, 0, 5, 10, 0, 0);
    const end = new Date(2026, 0, 5, 12, 0, 0);

    expect(formatEventDateRange(start, end)).toBe(
      "2026-01-05 10:00:00/2026-01-05 12:00:00",
    );
  });
});

describe("isValidEventDateRange", () => {
  it("accepts valid ranges", () => {
    expect(
      isValidEventDateRange("2026-01-05 10:00:00/2026-01-05 12:00:00"),
    ).toBe(true);
  });

  it("rejects ranges where end is before start", () => {
    expect(
      isValidEventDateRange("2026-01-10 10:00:00/2026-01-05 12:00:00"),
    ).toBe(false);
  });
});

describe("isEventAllDayRange", () => {
  it("detects all-day ranges", () => {
    expect(
      isEventAllDayRange("2026-01-05 00:00:00/2026-01-05 23:59:59"),
    ).toBe(true);
  });
});

describe("isEventMultiDayRange", () => {
  it("detects multi-day ranges", () => {
    expect(
      isEventMultiDayRange("2026-01-05 10:00:00/2026-01-07 18:00:00"),
    ).toBe(true);
  });
});

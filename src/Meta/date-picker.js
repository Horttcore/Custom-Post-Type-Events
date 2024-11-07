import React from "react";
import { select } from '@wordpress/data';
import { __ } from "@wordpress/i18n";
import { useEffect, useState } from "@wordpress/element";
import { useEntityProp } from "@wordpress/core-data";
import { PluginDocumentSettingPanel } from "@wordpress/editor";
import { TextControl, ToggleControl } from "@wordpress/components";

export default function DateTimePicker() {
    // Load only for events
    const postType = select("core/editor").getCurrentPostType();
    if (postType !== "event") {
        return;
    }

    const format = (x, y) => {
        let z = {
            m: x.getMonth() + 1,
            d: x.getDate(),
            h: x.getHours(),
            i: x.getMinutes(),
            s: x.getSeconds(),
        };
        y = y.replace(/(M+|d+|h+|m+|s+)/g, function (v) {
            return ((v.length > 1 ? "0" : "") + z[v.slice(-1)]).slice(-2);
        });

        return y.replace(/(y+)/g, function (v) {
            return x.getFullYear().toString().slice(-v.length);
        });
    };

    // Load meta
    const [meta, setMeta] = useEntityProp("postType", "event", "meta");
    if (!meta) {
        return;
    }

    const eventStart = meta[`eventStart`] ? new Date(meta[`eventStart`]) : "";
    const eventEnd = meta[`eventEnd`] ? new Date(meta[`eventEnd`]) : "";

    const [startDate, setStartDate] = useState(
        eventStart ? meta[`eventStart`].split(" ")[0] : ""
    );
    const [endDate, setEndDate] = useState(
        eventEnd !== "" ? meta[`eventEnd`].split(" ")[0] : startDate || ""
    );

    const startDatetime = meta[`eventStart`]
        ? meta[`eventStart`].split(" ")[1]
        : "00:00:00";
    const endDatetime = meta[`eventEnd`]
        ? meta[`eventEnd`].split(" ")[1]
        : "23:59:59";

    const [startTime, setStartTime] = useState(
        startDatetime ? startDatetime.replace(/:00$/, "") : ""
    );
    const [endTime, setEndTime] = useState(
        eventEnd && endDatetime ? endDatetime.replace(/:59|:00$/, "") : ""
    );

    const [isAllDay, setIsAllDay] = useState(
        (!startDatetime || startDatetime === "00:00:00") &&
        (!endDatetime || endDatetime === "23:59:59")
    );
    const [isMultiDay, setIsMultiDay] = useState(
        !(
            !eventStart ||
            !eventEnd ||
            (eventStart.getFullYear() === eventEnd.getFullYear() &&
                eventStart.getMonth() === eventEnd.getMonth() &&
                eventStart.getDate() === eventEnd.getDate())
        )
    );

    useEffect(() => {
        const regex = new RegExp(/^[0-2]?[0-9]:[0-5][0-9]$/);
        const sTime = regex.test(startTime) ? `${startTime}:00` : "";
        const eTime = regex.test(endTime) ? `${endTime}:00` : "";
        setMeta({
            ...meta,
            eventStart: `${startDate} ${sTime}`.trim(),
            eventEnd: `${endDate} ${eTime}`.trim(),
        });
    }, [startDate, startTime, endDate, endTime]);

    return (
        <PluginDocumentSettingPanel
            name="datetime-picker"
            title={__("Event Date", "custom-post-type-events")}
        >
            <div style={{ display: "flex", gap: "1em" }}>
                <ToggleControl
                    label={__("Multi-day", "custom-post-type-events")}
                    checked={isMultiDay}
                    onChange={(isMultiDay) => {
                        setIsMultiDay(isMultiDay);
                        if (!isMultiDay) {
                            setEndDate(startDate);
                        }
                    }}
                />

                <ToggleControl
                    label={__("All-day", "custom-post-type-events")}
                    checked={isAllDay}
                    onChange={(isAllDay) => {
                        setIsAllDay(isAllDay);
                        if (!isAllDay) {
                            setStartTime("");
                            setEndTime("");
                        }
                    }}
                />
            </div>

            <TextControl
                label={
                    isMultiDay
                        ? __("Start date", "custom-post-type-events")
                        : __("Date", "custom-post-type-events")
                }
                placeholder={__("dd.mm.yyyy", "custom-post-type-events")}
                value={startDate}
                onChange={(startDate) => {
                    setStartDate(startDate);
                    if (!isMultiDay) {
                        setEndDate(startDate);
                    }
                }}
                type="date"
            />

            {isMultiDay && (
                <TextControl
                    label={__("End date", "custom-post-type-events")}
                    placeholder={__("dd.mm.yyyy", "custom-post-type-events")}
                    value={endDate}
                    onChange={(endDate) => setEndDate(endDate)}
                    type="date"
                />
            )}

            {!isAllDay && (
                <div style={{ display: "flex", alignItems: "center", gap: ".25em" }}>
                    <TextControl
                        label={__("From", "custom-post-type-events")}
                        placeholder={__("hh:ii", "custom-post-type-events")}
                        value={startTime}
                        onChange={(newValue) => setStartTime(newValue)}
                    />

                    <span style={{ paddingTop: "1.125em" }}>&rarr;</span>

                    <TextControl
                        label={__("To", "custom-post-type-events")}
                        placeholder={__("hh:ii", "custom-post-type-events")}
                        value={endTime}
                        onChange={(newValue) => setEndTime(newValue)}
                    />
                </div>
            )}
        </PluginDocumentSettingPanel>
    );
}

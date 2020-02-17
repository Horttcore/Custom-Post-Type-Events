/**
 * Internal block libraries
 */
const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
import edit from './edit';

registerBlockType("custom-post-type-events/events", {
    title: __("Events", "custom-post-type-events"),
    description: __("Shows a custom event loop", "custom-post-type-events"),
    icon: 'groups',
    category: "widgets",
    keywords: [
        __("Events", "custom-post-type-events"),
        __("Query", "custom-post-type-events"),
        __("Loop", "custom-post-type-events")
    ],
    attributes: {
        postsToShow: {
            type: 'number',
            default: 10,
        },
    },
    supports: {
        anchor: true,
    },
    edit,
    save: props => {
        return null;
    }
});

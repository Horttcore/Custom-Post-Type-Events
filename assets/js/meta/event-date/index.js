import EventDateTimePicker from './component/event-datetime-picker.js';

const { __ } = wp.i18n;
const { registerPlugin } = wp.plugins;

registerPlugin(
    'event-datetime-picker',
    {
        render: EventDateTimePicker,
        icon: '',
    }
);
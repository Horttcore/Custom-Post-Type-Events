import EventDateTimePicker from './date-picker.js';
import { __ } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';

registerPlugin(
    'event-datetime-picker',
    {
        render: EventDateTimePicker,
        icon: '',
    }
);
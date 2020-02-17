import '../../../../sass/editorStyles.sass';

const { __ } = wp.i18n;

const { PluginDocumentSettingPanel } = wp.editPost;

const {
    Component,
} = wp.element;

const { compose } = wp.compose;

const {
    TextControl,
    ToggleControl,
    IconButton,
    DatePicker
} = wp.components;

const { withSelect, withDispatch } = wp.data;

class EventDateTimePicker extends Component {

    constructor(props) {
        super();
        this.props = props;

        this.state = {
            isOpenBegin: this.props.isOpenBegin,
            isOpenEnd: this.props.isOpenEnd,
            isMultiDay: this.isMultiDay(this.props.eventStartDateTime, this.props.eventEndDateTime),
            isAllDay: this.isAllDay(this.props.eventStartDateTime, this.props.eventEndDateTime),
            eventStart: this.props.eventStart,
            eventStartDateTime: this.props.eventStartDateTime,
            eventTimeStart: this.props.eventTimeStart,
            eventEndDateTime: this.props.eventEndDateTime,
            eventEnd: this.props.eventEnd,
            eventTimeEnd: this.props.eventTimeEnd,
        }
    }

    render() {

        if ( 'event' != this.props.postType ) {
            return '';
        }

        return (
            <PluginDocumentSettingPanel
                name="event-datetime-picker"
                title={__('Event Date', 'custom-post-type-events')}
                className="event-date"
            >

                <div className="event-date-container">
                    <ToggleControl
                        label={__('Multi-day', 'custom-post-type-events')}
                        checked={this.state.isMultiDay}
                        onChange={(isMultiDay) => this.setMultiDay(isMultiDay)}
                    />

                    <ToggleControl
                        label={__('All-day', 'custom-post-type-events')}
                        checked={this.state.isAllDay}
                        onChange={(isAllDay) => this.setAllDay(isAllDay)}
                    />
                </div>

                <div className="event-date-container">
                    <TextControl
                        label={this.state.isMultiDay ? __('Start date', 'custom-post-type-events') : __('Date', 'custom-post-type-events')}
                        placeholder={__('dd.mm.yyyy', 'custom-post-type-events')}
                        value={this.state.eventStart}
                        onChange={(eventStart) => { this.setDate('start', eventStart) }}
                    />

                    <IconButton
                        icon="calendar-alt"
                        label={__('Select a Date', 'custom-post-type-events')}
                        onClick={() => this.setState({ isOpenBegin: !this.state.isOpenBegin })}
                    />
                </div>

                {this.state.isOpenBegin && (
                    <DatePicker
                        currentDate={this.state.eventStartDateTime ? this.state.eventStartDateTime : new Date()}
                        onChange={(date) => this.pickDate('start', date)}
                    />
                )}

                {this.state.isMultiDay && (

                    <div className="event-date-container">

                        <TextControl
                            label={__('End date', 'custom-post-type-events')}
                            placeholder={__('dd.mm.yyyy', 'custom-post-type-events')}
                            value={this.state.eventEnd}
                            onChange={(eventEnd) => this.setDate('end', eventEnd)}
                        />

                        <IconButton
                            icon="calendar-alt"
                            label={__('Select a Date', 'custom-post-type-events')}
                            onClick={() => this.setState({ isOpenEnd: !this.state.isOpenEnd })}
                        />

                    </div>
                )}

                {this.state.isOpenEnd && (
                    <DatePicker
                        currentDate={this.state.eventEndDateTime ? this.state.eventEndDateTime : new Date()}
                        onChange={(date) => this.pickDate('end', date)}
                    />
                )}

                {!this.state.isAllDay && (
                    <div className="event-time-container">
                        <TextControl
                            label={__('Time', 'custom-post-type-events')}
                            placeholder={__('hh:ii', 'custom-post-type-events')}
                            value={this.state.eventTimeStart}
                            onChange={(time) => this.setTime('start', time)}
                        />

                        <span className="event-time-seperator">&rarr;</span>

                        <TextControl
                            className="event-time-end"
                            placeholder={__('hh:ii', 'custom-post-type-events')}
                            value={this.state.eventTimeEnd}
                            onChange={(time) => this.setTime('end', time)}
                        />
                    </div>
                )}
            </PluginDocumentSettingPanel>
        );
    }

    setDate(position, date) {

        const toDateTime = (string) => {
            string = string.split('.');

            if (string.length != 3) {
                return ''
            }

            const d = new Date();
            d.setFullYear(string[2]);
            d.setMonth(string[1] - 1);
            d.setDate(string[0]);

            return d;
        };

        if (position == 'start') {
            this.setState({
                eventStart: date,
                eventStartDateTime: toDateTime(date) ? toDateTime(date) : this.state.eventStartDateTime
            }, () => this.props.updateMeta(this.state));
        } else {
            this.setState({
                eventEnd: date,
                eventEndDateTime: toDateTime(date) ? toDateTime(date) : this.state.eventEndDateTime
            }, () => this.props.updateMeta(this.state));
        }
    }

    setTime(position, time) {
        if (position == 'start') {
            this.setState({ eventTimeStart: time }, () => this.props.updateMeta(this.state));
        } else {
            this.setState({ eventTimeEnd: time }, () => this.props.updateMeta(this.state));
        }
    }

    pickDate(position, date) {
        date = new Date(date);

        if (position == 'start') {
            this.setState({
                eventStartDateTime: date,
                eventStart: date.toLocaleDateString(window.wp.editor.getDefaultSettings().tinymce.wp_lang_att),
                isOpenBegin: false
            }, () => this.props.updateMeta(this.state));
        } else {
            this.setState({
                eventEndDateTime: date,
                eventEnd: date.toLocaleDateString(window.wp.editor.getDefaultSettings().tinymce.wp_lang_att),
                isOpenEnd: false,
            }, () => this.props.updateMeta(this.state));
        }

    }

    isMultiDay(date, date2) {
        if (!date || !date2)
            return false;

        return !(date.getFullYear() === date2.getFullYear() && date.getMonth() === date2.getMonth() && date.getDate() === date2.getDate());
    }

    setMultiDay(isMultiDay) {

        if (!isMultiDay) {
            let eventEndDateTime = new Date(this.props.eventStartDateTime);
            eventEndDateTime.setHours(23);
            eventEndDateTime.setMinutes(59);
            eventEndDateTime.setSeconds(59);
            this.setState({
                isMultiDay,
                eventEndDateTime: '',
                eventEnd: '',
            }, () => this.props.updateMeta(this.state));
        } else {
            this.setState({
                isMultiDay,
                eventEndDateTime: '',
                eventEnd: '',
            }, () => this.props.updateMeta(this.state));
        }
    }

    isAllDay(date, date2) {
        if (!date || !date2)
            return false;
        return (date.getHours() == 0 && date.getMinutes() == 0 && date.getSeconds() == 0 && date2.getHours() == 23 && date2.getMinutes() == 59 && date2.getSeconds() == 59);
    }

    setAllDay(isAllDay) {

        let state = {};

        const eventStartDateTime = new Date(this.props.eventStartDateTime);
        eventStartDateTime.setHours(0);
        eventStartDateTime.setMinutes(0);
        eventStartDateTime.setSeconds(0);

        const eventEndDateTime = new Date(this.props.eventEndDateTime);
        eventEndDateTime.setHours(23);
        eventEndDateTime.setMinutes(59);
        eventEndDateTime.setSeconds(59);

        state = {
            eventStartDateTime,
            eventTimeStart: '',
            eventEndDateTime,
            eventTimeEnd: '',
            isAllDay
        };

        this.setState(state, () => {
            this.props.updateMeta(this.state)
        })
    }

}

export default compose([
    withSelect((select) => {
        const { getEditedPostAttribute, getCurrentPostType } = select('core/editor');
        const meta = getEditedPostAttribute('meta');

        const eventStartDateTime = meta.eventStart ? new Date(meta.eventStart) : '';
        const eventEndDateTime = meta.eventEnd ? new Date(meta.eventEnd) : '';

        const eventStart = eventStartDateTime ? eventStartDateTime.toLocaleDateString(window.wp.editor.getDefaultSettings().tinymce.wp_lang_att) : '';
        const eventTimeStart = eventStartDateTime ? eventStartDateTime.getHours() + ':' + ('0' + eventStartDateTime.getMinutes()).slice(-2) : '';

        const eventEnd = eventEndDateTime ? eventEndDateTime.toLocaleDateString(window.wp.editor.getDefaultSettings().tinymce.wp_lang_att) : '';
        const eventTimeEnd = eventEndDateTime && eventEndDateTime.getHours() != '23' && eventEndDateTime.getMinutes() != '59' && eventEndDateTime.getSeconds() != '59' ? eventEndDateTime.getHours() + ':' + ('0' + eventEndDateTime.getMinutes()).slice(-2) : '';

        return {
            postType: getCurrentPostType(),
            isOpenBegin: false,
            isOpenEnd: false,
            isAllDay: false,
            isMultiDay: false,
            eventStart,
            eventStartDateTime,
            eventTimeStart,
            eventEndDateTime,
            eventEnd,
            eventTimeEnd,
        };
    }),
    withDispatch((dispatch) => {

        const { savePost, editPost } = dispatch('core/editor');

        return {
            onSave: savePost,
            updateMeta(state) {

                let newMeta = {
                    eventStart: '',
                    eventEnd: '',
                };
                let start = '';
                let end = '';

                const pad = (number) => ('0' + number).slice(-2);
                const toDateTimeString = (date) => (`${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())} ${pad(date.getHours())}:${pad(date.getMinutes())}:${pad(date.getSeconds())}`);

                if (state.eventStartDateTime) {
                    start = new Date(state.eventStartDateTime);
                    start.setHours(0);
                    start.setMinutes(0);
                    start.setSeconds(0);
                    if (state.eventTimeStart) {
                        const time = state.eventTimeStart.split(':');
                        start.setHours(time[0]);
                        start.setMinutes(time[1]);
                        start.setSeconds(0);
                    }
                    newMeta.eventStart = toDateTimeString(start);

                    end = start;
                    end.setHours(23)
                    end.setMinutes(59)
                    end.setSeconds(59)
                    newMeta.eventEnd = toDateTimeString(end);
                }

                if (state.eventEndDateTime) {
                    end = new Date(state.eventEndDateTime);
                    end.setHours(23);
                    end.setMinutes(59);
                    end.setSeconds(59);
                    if (state.eventTimeEnd) {
                        const time = state.eventTimeEnd.split(':');
                        end.setHours(time[0]);
                        end.setMinutes(time[1]);
                        end.setSeconds(0);
                    }
                    newMeta.eventEnd = toDateTimeString(end);
                }

                editPost({ meta: { ...newMeta } }); // Important: Old and new meta need to be merged in a non-mutating way!
            },
        };
    }),
])(EventDateTimePicker);
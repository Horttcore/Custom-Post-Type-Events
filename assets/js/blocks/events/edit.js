/**
 * External dependencies
 */
const { __ } = wp.i18n;

/**
 * WordPress dependencies
 */
const { Component, Fragment } = wp.element;
const { PanelBody, ServerSideRender, RangeControl } = wp.components;
const { InspectorControls } = wp.editor;
const { withSelect } = wp.data;

class Events extends Component {

    constructor() {
        super(...arguments);
    }

    render() {
        const { attributes, setAttributes, events } = this.props;
        const { postsToShow } = attributes;
        const hasPosts = Array.isArray(events) && events.length;

        return (
            <Fragment>
                <InspectorControls>
                    <PanelBody title={__('Events')}>
                        <RangeControl
                            label={__('Number of items')}
                            value={postsToShow}
                            onChange={(postsToShow) => setAttributes({ postsToShow })}
                            min={1}
                            max={100}
                        />
                    </PanelBody>
                </InspectorControls>
                <ServerSideRender block="custom-post-type-events/events" />
            </Fragment>
        );
    }
}


export default withSelect((select, props) => {
    const { postsToShow } = props;
    const { getEntityRecords } = select('core');
    const query = {
        orderby: 'event-date',
        order: 'asc',
        per_page: postsToShow,
        _embed: true,
    };

    return {
        events: getEntityRecords('postType', 'event', query),
    };
})(Events);

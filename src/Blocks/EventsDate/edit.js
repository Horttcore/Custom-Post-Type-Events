import ServerSideRender from '@wordpress/server-side-render';
import LoopControl from './loop-control';
import { useBlockProps } from '@wordpress/block-editor';
import React from 'react';

const Edit = ({ attributes, setAttributes }) => {
    return (
        <div {...useBlockProps()}>
            <ServerSideRender
                block="custom-post-type-events/events-date"
                attributes={attributes}
            />
        </div>
    );
};

export default Edit;

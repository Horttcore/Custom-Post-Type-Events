import { registerBlockType } from '@wordpress/blocks';
import block from './block.json';
import edit from './edit';
import "./styles.css"

registerBlockType(block, { edit });


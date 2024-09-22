/**
 * WordPress dependencies
 */
const {
    addFilter,
} = wp.hooks;

/**
 * Internal dependencies
 */
import TOCBlockEdit from './edit.jsx';

/**
 * Custom block Edit output for TOC block.
 *
 * @param {JSX} edit Original block edit.
 * @param {Object} blockProps Block data.
 *
 * @return {JSX} Block edit.
 */
function editRender( edit, blockProps ) {
	if ( 'canvas/toc' === blockProps.name ) {
		return (
			<TOCBlockEdit { ...blockProps } />
		);
	}

    return edit;
}

addFilter( 'canvas.customBlock.editRender', 'canvas/toc/editRender', editRender );

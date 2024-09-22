/**
 * WordPress dependencies
 */
const {
    addFilter,
} = wp.hooks;

/**
 * Internal dependencies
 */
import PinterestBoardBlockEdit from './edit.jsx';

/**
 * Custom block Edit output for PinterestBoard block.
 *
 * @param {JSX} edit Original block edit.
 * @param {Object} blockProps Block data.
 *
 * @return {JSX} Block edit.
 */
function editRender( edit, blockProps ) {
	if ( 'canvas/pinterest-board' === blockProps.name ) {
		return (
			<PinterestBoardBlockEdit { ...blockProps } />
		);
	}

    return edit;
}

addFilter( 'canvas.customBlock.editRender', 'canvas/pinterest-board/editRender', editRender );

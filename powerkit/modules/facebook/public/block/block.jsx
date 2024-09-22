/**
 * WordPress dependencies
 */
const {
	addFilter,
} = wp.hooks;

/**
 * Internal dependencies
 */
import FacebookFanpageBlockEdit from './edit.jsx';

/**
 * Custom block Edit output for FacebookFanpage block.
 *
 * @param {JSX} edit Original block edit.
 * @param {Object} blockProps Block data.
 *
 * @return {JSX} Block edit.
 */
function editRender(edit, blockProps) {
	if ('canvas/facebook-fanpage' === blockProps.name) {
		return (
			<FacebookFanpageBlockEdit {...blockProps} />
		);
	}

	return edit;
}

addFilter('canvas.customBlock.editRender', 'canvas/facebook-fanpage/editRender', editRender);

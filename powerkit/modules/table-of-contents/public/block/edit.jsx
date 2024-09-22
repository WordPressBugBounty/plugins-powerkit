/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import TOCPreviewRender from './preview-render';

/**
 * WordPress dependencies
 */
const {
	Component,
} = wp.element;

/**
 * Component
 */
export default class TOCBlockEdit extends Component {
	render() {
		let {
			className,
        } = this.props;

        const {
			canvasClassName,
		} = this.props.attributes;

		className = classnames(
			'pk-block-toc',
			canvasClassName,
			className
        );

        return (
            <div className={ className }>
			    <TOCPreviewRender { ...this.props } />
            </div>
        );
    }
}

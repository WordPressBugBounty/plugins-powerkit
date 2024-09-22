/**
 * External dependencies
 */
import classnames from 'classnames';
import { debounce } from 'throttle-debounce';

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;

const {
	Component,
	Fragment,
	createRef,
} = wp.element;

const {
	Placeholder,
	Disabled,
} = wp.components;

/**
 * Component
 */
export default class FacebookFanpageBlockEdit extends Component {
	constructor() {
		super( ...arguments );

		this.fbRef = createRef();

        this.maybeInit = debounce( 300, this.maybeInit.bind( this ) );
    }

	componentDidMount() {
		this.maybeInit();
	}	

	componentDidUpdate() {
		this.maybeInit();
	}

	maybeInit() {
        const {
			href,
		} = this.props.attributes;

		if ( ! this.fbRef || ! this.fbRef.current ) {
			return;
		}

		if ( ! href || ! window.FB || ! window.FB.XFBML ) {
			return;
		}

		const $ref = this.fbRef.current;

        let {
			showCover,
			showFacepile,
			showPosts,
			smallHeader,
		} = this.props.attributes;

		const hideCover = showCover ? 'false' : 'true';
		showFacepile = showFacepile ? 'true' : 'false';
		showPosts = showPosts ? 'true' : 'false';
		smallHeader = smallHeader ? 'true' : 'false';

		// check if already rendered
		const $query = $ref.getAttribute( 'fb-iframe-plugin-query' );
		if ( $query ) {
			let queryObj = false;

			try {
				// thanks https://stackoverflow.com/questions/8648892/convert-url-parameters-to-a-javascript-object
				queryObj = JSON.parse('{"' + decodeURIComponent( $query ).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g,'":"') + '"}');
			} catch( e ) {
				queryObj = false;
			}

			if (
				queryObj &&
				queryObj.href === href &&
				queryObj.hide_cover === hideCover &&
				queryObj.show_facepile === showFacepile &&
				queryObj.show_posts === showPosts &&
				queryObj.small_header === smallHeader
			) {
				return;
			}
		}

		// try to re-render
		if ( $ref.attributes['fb-xfbml-state'] ) {
			$ref.attributes['fb-xfbml-state'] = 're-rendering';
		}

		FB.XFBML.parse( $ref.parentNode );
	}

	render() {
		const {
			setAttributes,
		} = this.props;

		let {
			className,
		} = this.props;
		
        const {
			href,
			showCover,
			showFacepile,
			showPosts,
			smallHeader,
			canvasClassName,
		} = this.props.attributes;

		className = classnames(
			'fb-page-wrapper',
			canvasClassName,
			className
		);

        return (
			<Fragment>
				{ href ? (
					<Disabled>
						<div
							className={ className }
						>
							<div className="fb-page"
								ref={ this.fbRef }
								data-href={ href }
								data-hide-cover={ showCover ? 'false' : 'true' }
								data-show-facepile={ showFacepile ? 'true' : 'false' }
								data-show-posts={ showPosts ? 'true' : 'false' }
								data-small-header={ smallHeader ? 'true' : 'false' }
								data-adapt-container-width="true"
							/>
						</div>
					</Disabled>
				) : (
					<Placeholder>
						{ __( 'Please, enter Facebook Fanpage URL.' ) }
					</Placeholder>
				) }
			</Fragment>
        );
    }
}

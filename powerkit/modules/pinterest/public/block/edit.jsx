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
} = wp.element;

const {
	Placeholder,
	Disabled,
} = wp.components;

/**
 * Component
 */
export default class PinterestBoardBlockEdit extends Component {
	constructor() {
		super( ...arguments );

		this.state = {
			pinApi: false,
		};

        this.maybeFindGlobalPinObj = this.maybeFindGlobalPinObj.bind( this );
        this.maybeInit = debounce( 300, this.maybeInit.bind( this ) );
    }

	componentDidMount() {
		this.maybeInit();
	}	

	componentDidUpdate() {
		this.maybeInit();
	}

	/**
	 * Try to find global Pinterest object.
	 */
	maybeFindGlobalPinObj() {
		Object.keys( window ).forEach( ( k ) => {
			if ( /^PIN_\d+$/.test( k ) && window[ k ].f && window[ k ].f.build ) {
				this.setState( {
					pinApi: k,
				} );
			}
		} );
	}

	maybeInit() {
		if ( ! this.state.pinApi ) {
			this.maybeFindGlobalPinObj();
			return;
		}

		window[ this.state.pinApi ].f.build();
	}

	render() {
		let {
			className,
		} = this.props;
		
        const {
			href,
			canvasClassName,
		} = this.props.attributes;

		className = classnames(
			'pinterest-board-wrapper pk-block-pinterest-board',
			canvasClassName,
			className
		);

        return (
			<Fragment>
				{ href ? (
					<Disabled>
						<div
							className={ className }
							key={ `pin-board-${ href }` }
						>
							<a
								data-pin-do="embedBoard"
								data-pin-board-width="100%"
								href={ href }
							/>
						</div>
					</Disabled>
				) : (
					<Placeholder>
						{ __( 'Please, enter Pinterest Board URL.' ) }
					</Placeholder>
				) }
			</Fragment>
        );
    }
}

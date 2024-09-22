/**
 * External dependencies
 */
import { debounce } from 'throttle-debounce';

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;

const { apiFetch } = wp;

const {
	Component,
	Fragment,
	RawHTML,
} = wp.element;

const {
	Disabled,
	Placeholder,
	Spinner,
} = wp.components;

const {
    withSelect,
} = wp.data;

/**
 * Component
 */
class TOCPreviewRender extends Component {
	constructor() {
        super( ...arguments );

        this.state = {
            html: '',
            data: '',
            loading: false,
        };

        this.maybeFetchToc = debounce( 300, this.maybeFetchToc.bind( this ) );
    }

    componentDidMount() {
        this.maybeFetchToc( this.props );
    }
    componentDidUpdate() {
        this.maybeFetchToc( this.props );
    }

    maybeFetchToc( props ) {
        const {
            attributes,
            titlesData,
            getEditedPostContent,
        } = props;

        const {
            title,
            depth,
            minCount,
			minCharacters,
			btnHide,
			defaultState
        } = attributes;

        // still loading.
        if ( this.state.loading ) {
            return;
        }

        // compare current and previous block attributes and all titles in the post.
        let newData = `${ title } ${ depth } ${ minCount } ${ minCharacters } ${ btnHide } ${ defaultState } ${ titlesData }`;

        // data was not changed.
        if ( this.state.data === newData ) {
            return;
        }

        this.setState( {
            loading: true,
        } );

        const fetchParams = {
            path: '/powerkit-toc/v1/get',
            method: 'post',
            data: {
                content: getEditedPostContent(),
                params: {
                    title,
                    depth,
                    min_count: minCount,
					min_characters: minCharacters,
					btn_hide: btnHide,
					default_state: defaultState,
                },
            },
        };

        apiFetch( fetchParams ).then( ( fetchedData ) => {
            const updatedState = {
                loading: false,
                data: newData,
            };

            if ( typeof fetchedData === 'string' ) {
                updatedState.html = fetchedData;
            }

            this.setState( updatedState );
        } );
    }

	render() {
		const {
			html,
			loading,
		} = this.state;

        return (
			<Fragment>
				{ loading ? (
					<Placeholder>
						<Spinner />
					</Placeholder>
				) : '' }
				{ ! loading && html ? (
					<Disabled>
						<RawHTML>{ html }</RawHTML>
					</Disabled>
				) : '' }
				{ ! loading && ! html ? (
					<Placeholder>
						{ __( 'There is no table of contents for this post.' ) }
					</Placeholder>
				) : '' }
			</Fragment>
        );
    }
}

export default withSelect( ( select, props ) => {
    const {
        getEditedPostContent,
        getBlocks,
    } = select( 'core/editor' );

    let titlesData = '';

    const blocks = getBlocks();
    blocks.forEach( ( block ) => {
        if ( 'core/heading' === block.name ) {
            titlesData += block.attributes.level + block.attributes.content;
        }
    } );

	return {
        getEditedPostContent,
        getBlocks,
        titlesData,
	};
})( TOCPreviewRender );

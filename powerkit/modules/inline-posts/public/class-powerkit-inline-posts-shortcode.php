<?php
/**
 * Shortcode
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    Powerkit
 * @subpackage Powerkit/shortcodes
 */

/**
 * Template handler
 *
 * @param string $name     Specific template.
 * @param array  $posts    Array of posts.
 * @param array  $settings Array of settings.
 */
function powerkit_inline_posts_template_handler( $name, $posts, $settings ) {
	$templates = apply_filters( 'powerkit_inline_posts_templates', array() );

	if ( isset( $templates[ $name ] ) && function_exists( $templates[ $name ]['func'] ) ) {
		call_user_func( $templates[ $name ]['func'], $posts, $settings );
	} else {
		call_user_func( 'powerkit_inline_posts_default_template', $posts, $settings );
	}
}

/**
 * Shortcode
 *
 * @param array  $atts      User defined attributes in shortcode tag.
 * @param string $content   Shorcode tag content.
 * @return string           Shortcode result HTML.
 */
function powerkit_inline_posts_shortcode( $atts, $content = '' ) {

	if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
		return;
	}

	global $post;

	ob_start();

	global $powerkit_inline_posts;

	// Attributes.
	$atts = powerkit_shortcode_atts( shortcode_atts( array(
		'title'         => '',
		'count'         => 1,
		'offset'        => 0,
		'ids'           => null,
		'category'      => null,
		'tag'           => null,
		'time_frame'    => null,
		'orderby'       => 'date',
		'order'         => 'DESC',
		'template'      => 'list',
		'image_size'    => 'pk-thumbnail',
		'exclude_posts' => $powerkit_inline_posts,
	), $atts ) );

	$posts = powerkit_get_inline_posts( $atts );

	$columns  = 1;
	$template = $atts['template'];

	// Check grid template.
	switch ( $atts['template'] ) {
		case 'grid-2':
			$template = 'grid';
			$columns  = 2;
			break;
		case 'grid-3':
			$template = 'grid';
			$columns  = 3;
			break;
		case 'grid-4':
			$template = 'grid';
			$columns  = 4;
			break;
	}

	if ( $posts ) {
		?>
		<div class="pk-inline-posts">
			<?php
			$tag = apply_filters( 'powerkit_section_title_tag', 'h5' );

			if ( $atts['title'] ) {
			?>
				<<?php echo esc_html( $tag ); ?> class="pk-inline-posts-title pk-title pk-font-block">
					<?php echo esc_html( $atts['title'] ); ?>
				</<?php echo esc_html( $tag ); ?>>
			<?php } ?>

			<div class="pk-inline-posts-container pk-inline-posts-template-<?php echo esc_attr( $template ); ?>"
				data-columns="<?php echo esc_attr( $columns ); ?>">
				<?php
				foreach ( $posts as $post ) {

					setup_postdata( $post );
					// Exclude current post ID.
					$powerkit_inline_posts[] = $post->ID;

					// Output template.
					powerkit_inline_posts_template_handler( $atts['template'], $posts, $atts );
				}
				?>
			</div>
		</div>
		<?php
	}

	wp_reset_postdata();

	return ob_get_clean();
}
add_shortcode( 'powerkit_posts', 'powerkit_inline_posts_shortcode' );

/**
 * Map Social Links Shortcode into the Basic Shortcodes Plugin
 */
if ( function_exists( 'powerkit_basic_shortcodes_register' ) ) :

	add_action( 'init', function() {

		$shortcode_map = array(
			'name'         => 'inline_posts',
			'title'        => esc_html__( 'Inline Posts', 'powerkit' ),
			'priority'     => 200,
			'base'         => 'powerkit_posts',
			'autoregister' => false,
			'fields'       => array(
				array(
					'type'    => 'input',
					'name'    => 'title',
					'label'   => esc_html__( 'Title', 'powerkit' ),
					'default' => '',
				),
				array(
					'type'    => 'input',
					'name'    => 'count',
					'label'   => esc_html__( 'Count', 'powerkit' ),
					'default' => 1,
				),
				array(
					'type'    => 'input',
					'name'    => 'offset',
					'label'   => esc_html__( 'Offset', 'powerkit' ),
					'default' => 0,
				),
				array(
					'type'    => 'input',
					'name'    => 'image_size',
					'label'   => esc_html__( 'Image size', 'powerkit' ),
					'default' => 'pk-thumbnail',
				),
				array(
					'type'    => 'input',
					'name'    => 'category',
					'label'   => esc_html__( 'Filter by categories', 'powerkit' ),
					'desc'    => esc_html__( 'Add comma-separated list of category slugs. For example: &laquo;travel, lifestyle, food&raquo;. Leave empty for all categories.', 'powerkit' ),
					'default' => '',
				),
				array(
					'type'    => 'input',
					'name'    => 'tag',
					'label'   => esc_html__( 'Filter by tags', 'powerkit' ),
					'desc'    => esc_html__( 'Add comma-separated list of tag slugs. For example: &laquo;worth-reading, top-5, playlists&raquo;. Leave empty for all tags.', 'powerkit' ),
					'default' => '',
				),
				array(
					'type'    => 'input',
					'name'    => 'ids',
					'label'   => esc_html__( 'Filter by posts', 'powerkit' ),
					'desc'    => esc_html__( 'Add comma-separated list of post IDs. For example: 12, 34, 145. Leave empty for all posts.', 'powerkit' ),
					'default' => '',
				),
			),
		);

		// Add fields order and time frame.
		if ( powerkit_post_views_enabled() ) {
			$shortcode_map['fields'][] = array(
				'type'    => 'radio',
				'name'    => 'orderby',
				'label'   => esc_html__( 'Order posts by', 'powerkit' ),
				'style'   => 'vertical',
				'default' => 'date',
				'options' => array(
					'date'       => esc_html__( 'Date', 'powerkit' ),
					'post_views' => esc_html__( 'Views', 'powerkit' ),
				),
			);

			$shortcode_map['fields'][] = array(
				'type'    => 'radio',
				'name'    => 'order',
				'label'   => esc_html__( 'Order', 'powerkit' ),
				'style'   => 'vertical',
				'default' => 'DESC',
				'options' => array(
					'DESC' => esc_html__( 'DESC', 'powerkit' ),
					'ASC'  => esc_html__( 'ASC', 'powerkit' ),
				),
			);

			$shortcode_map['fields'][] = array(
				'type'    => 'input',
				'name'    => 'time_frame',
				'label'   => esc_html__( 'Filter by time frame', 'powerkit' ),
				'desc'    => esc_html__( 'Work only if Order by Views', 'powerkit' ) . '<br>' .
							 esc_html__( 'Add period of posts in English. For example: &laquo;2 months&raquo;, &laquo;14 days&raquo; or even &laquo;1 year&raquo;', 'powerkit' ),
				'default' => '',
			);
		}

		// Add field template.
		$templates = apply_filters( 'powerkit_inline_posts_templates', array() );

		if ( count( (array) $templates ) > 1 ) {
			$options = array();

			foreach ( $templates as $key => $item ) {
				if ( isset( $item['name'] ) ) {
					$options[ $key ] = $item['name'];
				}
			}

			$shortcode_map['fields'][] = array(
				'type'    => 'select',
				'name'    => 'template',
				'label'   => esc_html__( 'Template', 'powerkit' ),
				'default' => 'list',
				'options' => $options,
			);
		}

		powerkit_basic_shortcodes_register( $shortcode_map );

	});

endif;

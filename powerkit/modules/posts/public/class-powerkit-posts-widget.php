<?php
/**
 * Widget Featured Posts
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    Powerkit
 * @subpackage Powerkit/widgets
 */

/**
 * Widget Featured Posts
 */
class Powerkit_Featured_Posts_Widget extends WP_Widget {

	/**
	 * The default settings.
	 *
	 * @var array $default_settings The default settings.
	 */
	public $default_settings = array();

	/**
	 * Sets up a new widget instance.
	 */
	public function __construct() {

		$this->default_settings = apply_filters(
			'powerkit_widget_posts_settings', array(
				'title'             => '',
				'template'          => 'list',
				'post_type'         => 'post',
				'posts_per_page'    => 5,
				'orderby'           => 'date',
				'order'             => 'desc',
				'time_frame'        => '',
				'category'          => false,
				'post_meta'         => array( 'date' ),
				'post_meta_compact' => false,
				'avoid_duplicate'   => false,
			)
		);

		$widget_details = array(
			'classname'   => 'powerkit_widget_posts',
			'description' => esc_html__( 'Display a list of your featured posts.', 'powerkit' ),
		);
		parent::__construct( 'powerkit_widget_posts', esc_html__( 'Featured Posts', 'powerkit' ), $widget_details );
	}

	/**
	 * Outputs the content for the current widget instance.
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current widget instance.
	 */
	public function widget( $args, $instance ) {
		global $pk_posts;
		global $wp_query;

		if ( ! $pk_posts ) {
			$pk_posts = array();
		}

		$params = array_merge( $this->default_settings, $instance );

		if ( in_array( 'category', $params['post_meta'], true ) ) {
			$params['post_meta_category'] = true;
		} else {
			$params['post_meta_category'] = false;
		}

		$posts_args = array(
			'post_type'           => $params['post_type'],
			'posts_per_page'      => $params['posts_per_page'],
			'order'               => $params['order'],
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
		);

		if ( $params['category'] ) {
			$category          = $params['category'];
			$posts_args['cat'] = $category;
		}

		// Avoid Duplicate.
		if ( $params['avoid_duplicate'] ) {
			$main_posts = array();

			if ( isset( $wp_query->posts ) && $wp_query->posts ) {
				$main_posts = wp_list_pluck( $wp_query->posts, 'ID' );
			}

			if ( $main_posts ) {
				$posts_args['post__not_in'] = array_merge( $main_posts, $pk_posts );
			} else {
				$posts_args['post__not_in'] = $pk_posts;
			}
		}

		// Post order.
		$posts_args['orderby'] = $params['orderby'];

		$type_post_views = powerkit_post_views_enabled();

		if ( $type_post_views && ( 'views' === $params['orderby'] || 'post_views' === $params['orderby'] ) ) {
			$posts_args['orderby'] = $type_post_views;
			// Don't hide posts without views.
			$posts_args['views_query']['hide_empty'] = false;
		}

		if ( $params['time_frame'] ) {
			$posts_args['date_query'] = array(
				array(
					'column' => 'post_date_gmt',
					'after'  => $params['time_frame'] . ' ago',
				),
			);
		}

		$posts = new WP_Query( apply_filters_ref_array( 'powerkit_widget_featured_posts_args', array( $posts_args, & $params ) ) );

		if ( $posts->have_posts() ) {

			// Before Widget.
			echo $args['before_widget']; // XSS.

			// Title.
			if ( $params['title'] ) {
				echo $args['before_title'] . apply_filters( 'widget_title', wp_kses( $params['title'], 'pk-title' ), $instance, $this->id_base ) . $args['after_title']; // XSS.
			}

			// Template.
			if ( in_array( $params['template'], array( 'list', 'numbered', 'large' ), true ) ) {
				$class = sprintf( 'pk-widget-posts-template-default pk-widget-posts-template-%s', $params['template'] );
			} else {
				$class = sprintf( 'pk-widget-posts-template-%s', $params['template'] );
			}

			// Number of Posts.
			$class .= sprintf( ' posts-per-page-%s', (int) $params['posts_per_page'] );
			?>

			<div class="widget-body pk-widget-posts <?php echo esc_html( $class ); ?>">
				<ul>
					<?php
					while ( $posts->have_posts() ) :
						$posts->the_post();

						$pk_posts[] = get_the_ID();
						?>
						<li class="pk-post-item">
							<?php powerkit_widget_featured_posts_handler( $params['template'], $posts, $params, $instance ); ?>
						</li>
					<?php endwhile; ?>
				</ul>
			</div>

			<?php

			// After Widget.
			echo $args['after_widget']; // XSS.
		}

		wp_reset_postdata();
	}

	/**
	 * Handles updating settings for the current widget instance.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $new_instance;

		// Post Meta.
		if ( ! isset( $instance['post_meta'] ) ) {
			$instance['post_meta'] = array();
		}

		// Compact Post Meta.
		if ( ! isset( $instance['post_meta_compact'] ) ) {
			$instance['post_meta_compact'] = false;
		}

		// Avoid duplicate posts.
		if ( ! isset( $instance['avoid_duplicate'] ) ) {
			$instance['avoid_duplicate'] = false;
		}

		return apply_filters( 'powerkit_widget_posts_update', $instance );
	}

	/**
	 * Outputs the widget settings form.
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$params = array_merge( $this->default_settings, $instance );

		$templates = apply_filters( 'powerkit_featured_posts_templates', array() );
		?>
			<!-- Title -->
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'powerkit' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $params['title'] ); ?>" /></p>

			<?php do_action( 'powerkit_widget_posts_form_before', $this, $params, $instance ); ?>

			<!-- Template -->
			<?php if ( $templates ) { ?>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'template' ) ); ?>"><?php esc_html_e( 'Template:', 'powerkit' ); ?></label>
					<select name="<?php echo esc_attr( $this->get_field_name( 'template' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'template' ) ); ?>" class="widefat">
						<?php
						foreach ( $templates as $slug => $template ) {
							$name = isset( $template['name'] ) ? $template['name'] : $slug;
							?>
							<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $params['template'], $slug ); ?>><?php echo esc_html( $name ); ?></option>
						<?php } ?>
					</select>
				</p>
			<?php } ?>

			<!-- Post Type -->
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'post_type' ) ); ?>"><?php esc_html_e( 'Post Type', 'powerkit' ); ?>:</label>
				<select name="<?php echo esc_attr( $this->get_field_name( 'post_type' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'post_type' ) ); ?>" class="widefat">
					<?php
					$post_types = get_post_types( array(
						'publicly_queryable' => 1,
					), 'objects' );

					foreach ( $post_types as $name => $post_type ) {
						?>
						<option value="<?php echo esc_attr( $name ); ?>" <?php selected( $params['post_type'], $name ); ?>><?php echo esc_html( $post_type->labels->name ); ?></option>
					<?php } ?>
				</select>
			</p>

			<!-- Number of Posts -->
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'posts_per_page' ) ); ?>"><?php esc_html_e( 'Number of posts:', 'powerkit' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'posts_per_page' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'posts_per_page' ) ); ?>" type="number" value="<?php echo esc_attr( $params['posts_per_page'] ); ?>" /></p>

			<!-- Order by -->
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>"><?php esc_html_e( 'Order by:', 'powerkit' ); ?></label>
				<select name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>" class="widefat">
					<option value="date" <?php selected( $params['orderby'], 'date' ); ?>><?php esc_html_e( 'Date', 'powerkit' ); ?></option>
					<option value="comment_count" <?php selected( $params['orderby'], 'comment_count' ); ?>><?php esc_html_e( 'Comments', 'powerkit' ); ?></option>
					<option value="rand" <?php selected( $params['orderby'], 'rand' ); ?>><?php esc_html_e( 'Random', 'powerkit' ); ?></option>

					<?php if ( powerkit_post_views_enabled() ) { ?>
						<option value="post_views" <?php selected( $params['orderby'], 'post_views' ); ?>><?php esc_html_e( 'Views', 'powerkit' ); ?></option>
					<?php } ?>
				</select>
			</p>

			<!-- Order -->
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>"><?php esc_html_e( 'Order', 'powerkit' ); ?>:</label>
				<select name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>" class="widefat">
					<option value="desc" <?php selected( $params['order'], 'desc' ); ?>><?php esc_html_e( 'Descending', 'powerkit' ); ?></option>
					<option value="asc" <?php selected( $params['order'], 'asc' ); ?>><?php esc_html_e( 'Ascending', 'powerkit' ); ?></option>
				</select>
			</p>

			<!-- Time Frame -->
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'time_frame' ) ); ?>"><?php esc_html_e( 'Time frame:', 'powerkit' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'time_frame' ) ); ?>" placeholder="<?php esc_html_e( '3 months', 'powerkit' ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'time_frame' ) ); ?>" type="text" value="<?php echo esc_attr( $params['time_frame'] ); ?>" /></p>

			<!-- Category -->
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>"><?php esc_html_e( 'Category:', 'powerkit' ); ?></label>
				<select name="<?php echo esc_attr( $this->get_field_name( 'category' ) ); ?>[]" id="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>" class="widefat" style="height: auto !important;" multiple="multiple" size="8">
					<?php
						$cat_args = array(
							'hide_empty'   => 0,
							'hierarchical' => 1,
							'selected'     => (array) $params['category'],
							'walker'       => new Powerkit_Add_Posts_Categories_Tree_Walker(),
						);

						$allowed_html = array(
							'option' => array(
								'class'    => true,
								'value'    => true,
								'selected' => true,
							),
						);

						echo wp_kses( walk_category_dropdown_tree( get_categories( $cat_args ), 0, $cat_args ), $allowed_html );
						?>
				</select>
			</p>

			<!-- Post meta -->
			<h4><?php esc_html_e( 'Post meta:', 'powerkit' ); ?></h4>

			<?php
			$allowed_post_meta = powerkit_allowed_post_meta();

			foreach ( $allowed_post_meta  as $key => $caption ) {
				?>
					<p><input id="<?php echo esc_attr( $this->get_field_id( 'post_meta' ) ); ?>-<?php echo esc_attr( $key ); ?>" class="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'post_meta' ) ); ?>[]" type="checkbox" value="<?php echo esc_attr( $key ); ?>" <?php checked( in_array( $key, (array) $params['post_meta'], true ) ? true : false ); ?> />
					<label for="<?php echo esc_attr( $this->get_field_id( 'post_meta' ) ); ?>-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $caption ); ?></label></p>
				<?php
			}
			?>

			<!-- Compact Post Meta -->
			<h4><?php esc_html_e( 'Compact post meta:', 'powerkit' ); ?></h4>
			<p><input id="<?php echo esc_attr( $this->get_field_id( 'post_meta_compact' ) ); ?>" class="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'post_meta_compact' ) ); ?>" type="checkbox" <?php checked( (bool) $params['post_meta_compact'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'post_meta_compact' ) ); ?>"><?php esc_html_e( 'Display compact post meta', 'powerkit' ); ?></label></p>

			<!-- Avoid duplicate posts -->
			<h4><?php esc_html_e( 'Avoid duplicate posts:', 'powerkit' ); ?></h4>
			<p><input id="<?php echo esc_attr( $this->get_field_id( 'avoid_duplicate' ) ); ?>" class="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'avoid_duplicate' ) ); ?>" type="checkbox" <?php checked( (bool) $params['avoid_duplicate'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'avoid_duplicate' ) ); ?>"><?php esc_html_e( 'Exclude duplicate posts', 'powerkit' ); ?></label></p>

			<?php do_action( 'powerkit_widget_posts_form_after', $this, $params, $instance ); ?>

		<?php
	}
}

/**
 * Create HTML dropdown list of Categories.
 */
class Powerkit_Add_Posts_Categories_Tree_Walker extends Walker_CategoryDropdown {

	/**
	 * Starts the element output.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @see Walker::start_el()
	 *
	 * @param string $output   Passed by reference. Used to append additional content.
	 * @param object $category Category data object.
	 * @param int    $depth    Depth of category. Used for padding.
	 * @param array  $args     Uses 'selected', 'show_count', and 'value_field' keys, if they exist.
	 *                         See wp_dropdown_categories().
	 * @param int    $id       Optional. ID of the current category. Default 0 (unused).
	 */
	public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		$pad      = $depth > 0 ? '- ' . str_repeat( '&nbsp;', $depth * 3 ) : '';
		$selected = array_map( 'intval', $args['selected'] );
		$cat_name = apply_filters( 'list_cats', $category->name, $category );

		$output .= sprintf(
			'<option class="level-%1$s" value="%2$s" %4$s>%3$s</option>',
			esc_attr( $depth ),
			esc_attr( $category->term_id ),
			esc_html( $pad . $cat_name ),
			selected( in_array( $category->term_id, $selected, true ), true )
		);
		$output .= "\n";
	}
}

/**
 * Register Widget
 */
function powerkit_widget_init_featured_posts() {
	register_widget( 'Powerkit_Featured_Posts_Widget' );
}
add_action( 'widgets_init', 'powerkit_widget_init_featured_posts' );

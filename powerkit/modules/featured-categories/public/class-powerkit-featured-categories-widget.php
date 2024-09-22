<?php
/**
 * Widget Featured Categories
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    Powerkit
 * @subpackage Powerkit/widgets
 */

/**
 * Widget Featured Categories
 */
class Powerkit_Featured_Categories_Widget extends WP_Widget {

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

		$this->default_settings = apply_filters( 'powerkit_widget_featured_categories_settings', array(
			'title'      => esc_html__( 'Featured Categories', 'powerkit' ),
			'layout'     => 'tiles',
			'filter_ids' => '',
			'orderby'    => 'name',
			'order'      => 'ASC',
			'maximum'    => 0,
			'number'     => true,
		) );

		$widget_details = array(
			'classname'   => 'powerkit_widget_featured_categories',
			'description' => '',
		);

		parent::__construct( 'powerkit_widget_featured_categories', esc_html__( 'Featured Categories', 'powerkit' ), $widget_details );
	}

	/**
	 * Outputs the content for the current widget instance.
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current widget instance.
	 */
	public function widget( $args, $instance ) {
		$params = array_merge( $this->default_settings, $instance );

		// Title.
		if ( $params['title'] ) {
			$params['title'] = apply_filters( 'widget_title', $params['title'], $instance, $this->id_base );
		}

		// Before Widget.
		echo $args['before_widget']; // XSS.

		// Title.
		if ( $params['title'] ) {
			echo $args['before_title'] . wp_kses( $params['title'], 'pk-title') . $args['after_title']; // XSS.
		}

		?>
			<div class="widget-body">
				<?php
				powerkit_featured_categories_output( $params );
				?>
			</div>
		<?php

		// After Widget.
		echo $args['after_widget']; // XSS.
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

		// Display number posts.
		if ( ! isset( $instance['number'] ) ) {
			$instance['number'] = false;
		}

		return apply_filters( 'powerkit_widget_featured_categories_update', $instance );
	}

	/**
	 * Outputs the widget settings form.
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$params = array_merge( $this->default_settings, $instance );

		$layouts = powerkit_featured_categories_locations();
		?>
			<?php do_action( 'powerkit_widget_featured_categories_form_title_before', $this, $params, $instance ); ?>

			<!-- Title -->
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'powerkit' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $params['title'] ); ?>" /></p>

			<?php do_action( 'powerkit_widget_featured_categories_form_layouts_before', $this, $params, $instance ); ?>

			<!-- Layouts -->
			<?php if ( $layouts ) { ?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'layout' ) ); ?>"><?php esc_html_e( 'Layouts', 'powerkit' ); ?></label>
				<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'layout' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'layout' ) ); ?>">
					<?php foreach ( $layouts as $slug => $layout ) { ?>
						<option value="<?php echo esc_attr( $slug ); ?>" <?php echo selected( $params['layout'], $slug ); ?>><?php echo esc_html( $layout['name'] ); ?></option>
					<?php } ?>
				</select>
			</p>
			<?php } ?>

			<!-- Filter by Categories -->
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'filter_ids' ) ); ?>"><?php esc_html_e( 'Filter by categories:', 'powerkit' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'filter_ids' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'filter_ids' ) ); ?>" type="text" value="<?php echo esc_attr( $params['filter_ids'] ); ?>" />
			</p>

			<p class="help"><?php esc_html_e( 'Add comma-separated list of categories IDs. For example: 1, 2, 3. Leave empty for all categories.' ); ?></p>

			<!-- Order By -->
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>"><?php esc_html_e( 'Order By', 'powerkit' ); ?></label>
				<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>">
					<option value="name" <?php echo selected( $params['orderby'], 'name' ); ?>><?php esc_html_e( 'Name', 'powerkit' ); ?></option>
					<option value="count" <?php echo selected( $params['orderby'], 'count' ); ?>><?php esc_html_e( 'Posts count', 'powerkit' ); ?></option>
					<option value="include" <?php echo selected( $params['orderby'], 'include' ); ?>><?php esc_html_e( 'Filter include', 'powerkit' ); ?></option>
					<option value="id" <?php echo selected( $params['orderby'], 'id' ); ?>><?php esc_html_e( 'ID', 'powerkit' ); ?></option>
				</select>
			</p>

			<!-- Order -->
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>"><?php esc_html_e( 'Order', 'powerkit' ); ?></label>
				<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>">
					<option value="ASC" <?php echo selected( $params['order'], 'ASC' ); ?>><?php esc_html_e( 'ASC', 'powerkit' ); ?></option>
					<option value="DESC" <?php echo selected( $params['order'], 'DESC' ); ?>><?php esc_html_e( 'DESC', 'powerkit' ); ?></option>
				</select>
			</p>

			<!-- Maximum count -->
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'maximum' ) ); ?>"><?php esc_html_e( 'Maximum count', 'powerkit' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'maximum' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'maximum' ) ); ?>" type="number" value="<?php echo esc_attr( $params['maximum'] ); ?>" /></p>

			<!-- Display number posts -->
			<p><input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" class="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="checkbox" <?php checked( (bool) $params['number'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_html_e( 'Display number posts', 'powerkit' ); ?></label></p>
		<?php
	}
}

/**
 * Register Widget
 */
function powerkit_widget_init_featured_categories() {
	register_widget( 'Powerkit_Featured_Categories_Widget' );
}
add_action( 'widgets_init', 'powerkit_widget_init_featured_categories' );

<?php
/**
 * Widget Contributors
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    Powerkit
 * @subpackage Powerkit/widgets
 */

/**
 * Contributors
 */
class Powerkit_Contributors_Widget extends WP_Widget {

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

		$this->default_settings = apply_filters( 'powerkit_widget_contributors_settings', array(
			'title'           => esc_html__( 'Contributors', 'powerkit' ),
			'filter_ids'      => '',
			'avatar'          => true,
			'social_accounts' => true,
		) );

		$widget_details = array(
			'classname'   => 'powerkit_widget_contributors',
			'description' => '',
		);

		parent::__construct( 'powerkit_widget_contributors', esc_html__( 'Contributors', 'powerkit' ), $widget_details );
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

		powerkit_contributors_get_html( $params, $args );
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

		// Display avatar.
		if ( ! isset( $instance['avatar'] ) ) {
			$instance['avatar'] = false;
		}

		// Display social accounts.
		if ( ! isset( $instance['social_accounts'] ) ) {
			$instance['social_accounts'] = false;
		}

		return $instance;
	}

	/**
	 * Outputs the widget settings form.
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$params = array_merge( $this->default_settings, $instance );
		?>
			<!-- Title -->
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'powerkit' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $params['title'] ); ?>" /></p>

			<!-- Filter by Authors -->
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'filter_ids' ) ); ?>"><?php esc_html_e( 'Filter by authors:', 'powerkit' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'filter_ids' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'filter_ids' ) ); ?>" type="text" value="<?php echo esc_attr( $params['filter_ids'] ); ?>" />
			</p>

			<p class="help"><?php esc_html_e( 'Add comma-separated list of authors IDs. For example: 1, 2, 3. Leave empty for all authors.' ); ?></p>

			<!-- Display avatar -->
			<p><input id="<?php echo esc_attr( $this->get_field_id( 'avatar' ) ); ?>" class="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'avatar' ) ); ?>" type="checkbox" <?php checked( (bool) $params['avatar'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'avatar' ) ); ?>"><?php esc_html_e( 'Display avatar', 'powerkit' ); ?></label></p>

			<?php if ( powerkit_module_enabled( 'social_links' ) ) : ?>
				<!-- Display social accounts -->
				<p><input id="<?php echo esc_attr( $this->get_field_id( 'social_accounts' ) ); ?>" class="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'social_accounts' ) ); ?>" type="checkbox" <?php checked( (bool) $params['social_accounts'] ); ?> />
				<label for="<?php echo esc_attr( $this->get_field_id( 'social_accounts' ) ); ?>"><?php esc_html_e( 'Display social accounts', 'powerkit' ); ?></label></p>
			<?php endif; ?>
		<?php
	}
}

/**
 * Register Widget
 */
function powerkit_widget_init_contributors() {
	register_widget( 'Powerkit_Contributors_Widget' );
}
add_action( 'widgets_init', 'powerkit_widget_init_contributors' );

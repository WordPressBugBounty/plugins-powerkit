<?php
/**
 * Widget Pinterest Profile
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    PowerKit
 * @subpackage PowerKit/widgets
 */

/**
 * Widget Pinterest Profile Class
 */
class Powerkit_Pinterest_Profile_Widget extends WP_Widget {

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

		$this->default_settings = apply_filters( 'powerkit_pinterest_profile_widget_settings', array(
			'title' => esc_html__( 'Pinterest Profile', 'powerkit' ),
			'href'  => '',
		) );

		$widget_details = array(
			'classname'   => 'powerkit_pinterest_profile_widget',
			'description' => esc_html__( 'Add the Pinterest Profile widget to your sidebar.', 'powerkit' ),
		);
		parent::__construct( 'powerkit_pinterest_profile_widget', esc_html__( 'Pinterest Profile', 'powerkit' ), $widget_details );
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

		// Before Widget.
		echo $args['before_widget']; // XSS OK.
		?>

		<div class="widget-body">
			<?php

			// Title.
			if ( $params['title'] ) {
				echo $args['before_title'] . apply_filters( 'widget_title', wp_kses( $params['title'], 'pk-title' ), $instance, $this->id_base ) . $args['after_title']; // XSS.
			}

			if ( $params['href'] ) {
				?>
				<div class="pinterest-profile-wrapper">
					<a data-pin-do="embedUser" data-pin-board="100%" href="<?php echo esc_url( $params['href'] ); ?>"></a>
				</div>
				<?php
			} else {
				powerkit_alert_warning( esc_html__( 'The "Pinterest Profile URL" field is required!', 'powerkit' ) );
			}
			?>
		</div>

		<?php

		// After Widget.
		echo $args['after_widget']; // XSS OK.
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

			<!-- Pinterest Profile URL -->
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'href' ) ); ?>"><?php esc_html_e( 'Pinterest profile URL:', 'powerkit' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'href' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'href' ) ); ?>" type="text" value="<?php echo esc_url( $params['href'] ); ?>" /></p>
		<?php
	}
}

/**
 * Register Widget
 */
function powerkit_widget_init_pinterest_profile() {
	register_widget( 'Powerkit_Pinterest_Profile_Widget' );
}
add_action( 'widgets_init', 'powerkit_widget_init_pinterest_profile' );

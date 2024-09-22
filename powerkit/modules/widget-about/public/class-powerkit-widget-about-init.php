<?php
/**
 * Widget About
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    Powerkit
 * @subpackage Powerkit/widgets
 */

/**
 * Widget About
 */
class Powerkit_Widget_About_Init extends WP_Widget {

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

		$this->default_settings = apply_filters( 'powerkit_widget_about_settings', array(
			'title'        => esc_html__( 'About', 'powerkit' ),
			'subtitle'     => '',
			'image'        => false,
			'text'         => '',
			'button_url'   => '',
			'button_text'  => '',
			'social_links' => true,
		) );

		$widget_details = array(
			'classname'   => 'powerkit_widget_about',
			'description' => '',
		);

		parent::__construct( 'powerkit_widget_about', esc_html__( 'About', 'powerkit' ), $widget_details );
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
			$params['widget_title'] = $args['before_title'] . apply_filters( 'widget_title', wp_kses( $params['title'], 'pk-title' ), $instance, $this->id_base ) . $args['after_title'];
		}

		// Before Widget.
		echo $args['before_widget']; // XSS.

		?>
			<div class="widget-body pk-widget-about">
				<?php
					powerkit_about_get_html( $params );
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

		// Display social links.
		if ( ! isset( $instance['social_links'] ) ) {
			$instance['social_links'] = false;
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

		$bg_image_url = $params['image'] ? wp_get_attachment_image_url( intval( $params['image'] ), 'large' ) : '';
		?>
			<!-- Title -->
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'powerkit' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $params['title'] ); ?>" /></p>

			<!-- Subtitle -->
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'subtitle' ) ); ?>"><?php esc_html_e( 'Subtitle', 'powerkit' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'subtitle' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'subtitle' ) ); ?>" type="text" value="<?php echo esc_attr( $params['subtitle'] ); ?>" /></p>

			<!-- Image URL -->
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>"><?php esc_html_e( 'Image URL', 'powerkit' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'image' ) ); ?>" type="text" value="<?php echo esc_attr( $params['image'] ); ?>" /></p>

			<!-- Text -->
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>"><?php esc_html_e( 'Text', 'powerkit' ); ?></label>
			<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>" rows="10"><?php echo esc_textarea( $params['text'] ); ?></textarea></p>

			<!-- Button URL -->
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'button_url' ) ); ?>"><?php esc_html_e( 'Button URL', 'powerkit' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_url' ) ); ?>" type="text" value="<?php echo esc_attr( $params['button_url'] ); ?>" /></p>

			<!-- Button Text -->
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>"><?php esc_html_e( 'Button Text', 'powerkit' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_text' ) ); ?>" type="text" value="<?php echo esc_attr( $params['button_text'] ); ?>" /></p>

			<!-- Display social accounts -->
			<?php if ( powerkit_module_enabled( 'social_links' ) ) : ?>
				<p><input id="<?php echo esc_attr( $this->get_field_id( 'social_links' ) ); ?>" class="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'social_links' ) ); ?>" type="checkbox" <?php checked( (bool) $params['social_links'] ); ?> />
				<label for="<?php echo esc_attr( $this->get_field_id( 'social_links' ) ); ?>"><?php esc_html_e( 'Display social links', 'powerkit' ); ?></label></p>
			<?php endif; ?>
		<?php
	}

}

/**
 * Register Widget
 */
function powerkit_widget_about_init() {
	register_widget( 'Powerkit_Widget_About_Init' );
}
add_action( 'widgets_init', 'powerkit_widget_about_init' );

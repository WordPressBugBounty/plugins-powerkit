<?php
/**
 * Widget Facebook Fanpage
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    PowerKit
 * @subpackage PowerKit/widgets
 */

/**
 * Widget Facebook Fanpage Class
 */
class Powerkit_Facebook_Fanpage_Widget extends WP_Widget {

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

		$this->default_settings = apply_filters( 'powerkit_facebook_fanpage_widget_settings', array(
			'title'                 => esc_html__( 'Facebook Fanpage', 'powerkit' ),
			'href'                  => '',
			'hide_cover'            => false,
			'show_facepile'         => false,
			'show_posts'            => false,
			'small_header'          => false,
			'adapt_container_width' => true,
		) );

		$widget_details = array(
			'classname'   => 'powerkit_facebook_fanpage_widget',
			'description' => esc_html__( 'A Facebook Fanpage widget.', 'powerkit' ),
		);
		parent::__construct( 'powerkit_facebook_fanpage_widget', esc_html__( 'Facebook Fanpage', 'powerkit' ), $widget_details );
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
				<div class="fb-page-wrapper">
					<div class="fb-page"
						data-href="<?php echo esc_url( $params['href'] ); ?>"
						data-hide-cover="<?php echo esc_attr( $params['hide_cover'] ? 'true' : 'false' ); ?>"
						data-show-facepile="<?php echo esc_attr( $params['show_facepile'] ? 'true' : 'false' ); ?>"
						data-show-posts="<?php echo esc_attr( $params['show_posts'] ? 'true' : 'false' ); ?>"
						data-small-header="<?php echo esc_attr( $params['small_header'] ? 'true' : 'false' ); ?>"
						data-adapt-container-width="<?php echo esc_attr( $params['adapt_container_width'] ? 'true' : 'false' ); ?>"
						data-width="500px">
					</div>
				</div>
				<?php
			} else {
				powerkit_alert_warning( esc_html__( 'The "Facebook Fanpage URL" field is required!', 'powerkit' ) );
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

		// Hide cover.
		if ( ! isset( $instance['hide_cover'] ) ) {
			$instance['hide_cover'] = false;
		}

		// Show facepile.
		if ( ! isset( $instance['show_facepile'] ) ) {
			$instance['show_facepile'] = false;
		}

		// Show posts.
		if ( ! isset( $instance['show_posts'] ) ) {
			$instance['show_posts'] = false;
		}

		// Small header.
		if ( ! isset( $instance['small_header'] ) ) {
			$instance['small_header'] = false;
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

			<!-- Facebook Fanpage URL -->
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'href' ) ); ?>"><?php esc_html_e( 'Facebook fanpage URL:', 'powerkit' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'href' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'href' ) ); ?>" type="text" value="<?php echo esc_url( $params['href'] ); ?>" /></p>

			<!-- Hide Cover -->
			<p><input id="<?php echo esc_attr( $this->get_field_id( 'hide_cover' ) ); ?>" class="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'hide_cover' ) ); ?>" type="checkbox" <?php checked( (bool) $params['hide_cover'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'hide_cover' ) ); ?>"><?php esc_html_e( 'Hide cover', 'powerkit' ); ?></label></p>

			<!-- Show Facepile -->
			<p><input id="<?php echo esc_attr( $this->get_field_id( 'show_facepile' ) ); ?>" class="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'show_facepile' ) ); ?>" type="checkbox" <?php checked( (bool) $params['show_facepile'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_facepile' ) ); ?>"><?php esc_html_e( 'Show facepile', 'powerkit' ); ?></label></p>

			<!-- Show Posts -->
			<p><input id="<?php echo esc_attr( $this->get_field_id( 'show_posts' ) ); ?>" class="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'show_posts' ) ); ?>" type="checkbox" <?php checked( (bool) $params['show_posts'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_posts' ) ); ?>"><?php esc_html_e( 'Show posts', 'powerkit' ); ?></label></p>

			<!-- Small Header -->
			<p><input id="<?php echo esc_attr( $this->get_field_id( 'small_header' ) ); ?>" class="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'small_header' ) ); ?>" type="checkbox" <?php checked( (bool) $params['small_header'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'small_header' ) ); ?>"><?php esc_html_e( 'Small header', 'powerkit' ); ?></label></p>
		<?php
	}
}

/**
 * Register Widget
 */
function powerkit_widget_init_facebook() {
	register_widget( 'Powerkit_Facebook_Fanpage_Widget' );
}
add_action( 'widgets_init', 'powerkit_widget_init_facebook' );

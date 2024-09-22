<?php
/**
 * The admin-specific functionality of the module.
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    Powerkit
 * @subpackage Modules/Admin
 */

/**
 * The admin-specific functionality of the module.
 */
class Powerkit_Facebook_Admin extends Powerkit_Module_Admin {

	/**
	 * Initialize
	 */
	public function initialize() {
		add_action( 'admin_init', array( $this, 'register_settings_section' ) );
	}


	/**
	 * Register admin page
	 *
	 * @since 1.0.0
	 */
	public function register_settings_section() {

		add_settings_section( 'powerkit_facebook_settings', sprintf( '<span id="%s">%s</span>', powerkit_get_page_slug( $this->slug ), esc_html__( 'Facebook Comments', 'powerkit' ) ), array( $this, 'powerkit_facebook_settings_callback' ), 'discussion' );

		add_settings_field( 'powerkit_facebook_enable_comments', esc_html__( 'Enable Facebook Comments', 'powerkit' ), array( $this, 'powerkit_facebook_enable_comments_callback' ), 'discussion', 'powerkit_facebook_settings' );
		add_settings_field( 'powerkit_facebook_number_comments', esc_html__( 'Number of Comments', 'powerkit' ), array( $this, 'powerkit_facebook_number_comments_callback' ), 'discussion', 'powerkit_facebook_settings' );

		register_setting( 'discussion', 'powerkit_facebook_enable_comments' );
		register_setting( 'discussion', 'powerkit_facebook_number_comments_callback' );

		$locations = apply_filters( 'powerkit_facebook_comments_location', array() );

		// If locations > 1.
		if ( count( (array) $locations ) > 1 ) {
			add_settings_field( 'powerkit_facebook_location', esc_html__( 'Location', 'powerkit' ), array( $this, 'powerkit_facebook_location_callback' ), 'discussion', 'powerkit_facebook_settings' );
			register_setting( 'discussion', 'powerkit_facebook_location' );
		}
	}

	/**
	 * Section Description.
	 *
	 * @since 1.0.0
	 */
	public function powerkit_facebook_settings_callback() {
		return null;
	}

	/**
	 * Field | Enable Facebook Comments.
	 *
	 * @since 1.0.0
	 */
	public function powerkit_facebook_enable_comments_callback() {
		?>
			<input class="regular-text" id="powerkit_facebook_enable_comments" name="powerkit_facebook_enable_comments" type="checkbox" value="true" <?php checked( (bool) get_option( 'powerkit_facebook_enable_comments', false ) ); ?>>
		<?php
	}

	/**
	 * Field | Number of Comments.
	 *
	 * @since 1.0.0
	 */
	public function powerkit_facebook_number_comments_callback() {
		?>
			<input class="small-text" id="powerkit_facebook_number_comments" name="powerkit_facebook_number_comments" type="number" value="<?php echo esc_attr( get_option( 'powerkit_facebook_number_comments', 10 ) ); ?>" /> <?php esc_html_e( 'items', 'powerkit' ); ?>
		<?php
	}

	/**
	 * Field | Location.
	 *
	 * @since 1.0.0
	 */
	public function powerkit_facebook_location_callback() {
		$locations = apply_filters( 'powerkit_facebook_comments_location', array() );
		?>
			<select class="regular-text" name="powerkit_facebook_location" id="powerkit_facebook_location">
				<?php
				if ( $locations ) {
					foreach ( $locations as $key => $item ) {
						?>
							<option value="<?php echo esc_attr( $key ); ?>" <?php selected( get_option( 'powerkit_facebook_location' ), $key ); ?>><?php echo esc_attr( $item['name'] ); ?></option>
						<?php
					}
				}
				?>
			</select>
		<?php
	}
}

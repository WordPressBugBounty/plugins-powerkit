<?php
/**
 * Fonts
 *
 * @package    Powerkit
 * @subpackage Extensions
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_Fonts extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name     = 'fonts';
			$this->slug     = 'fonts';
			$this->type     = 'extension';
			$this->category = 'basic';
			$this->public   = false;
			$this->enabled  = false;
		}

		/**
		 * Initialize module
		 */
		public function initialize() {
			add_action( 'admin_menu', array( $this, 'register_options_page' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
			add_filter( 'powerkit_fonts_choices', array( $this, 'kirki_fonts_choices' ) );
		}

		/**
		 * Register admin page
		 *
		 * @since 1.0.0
		 */
		public function register_options_page() {
			add_theme_page( esc_html__( 'Fonts', 'powerkit' ), esc_html__( 'Fonts', 'powerkit' ), 'manage_options', powerkit_get_page_slug( $this->slug ), array( $this, 'settings_page' ) );
		}

		/**
		 * Build admin page
		 *
		 * @since 1.0.0
		 */
		public function settings_page() {

			powerkit_uuid_hash();

			// Check wpnonce.
			if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'] ) ) { // Input var ok; sanitization ok.
				return;
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'You do not have sufficient rights to view this page.', 'powerkit' ) );
			}
			?>
				<div class="wrap">
					<h1 class="wp-heading-inline"><?php esc_html_e( 'Fonts', 'powerkit' ); ?></h1>

					<hr class="wp-header-end">

					<div class="settings">
						<?php $fonts_settings = apply_filters( 'powerkit_fonts_register_settings', array() ); ?>

						<?php if ( $fonts_settings ) : ?>
								<div class="tabs">
									<?php
									$first_settings = current( $fonts_settings );

									$tab = sanitize_title( isset( $_REQUEST['tab'] ) ? $_REQUEST['tab'] : $first_settings['id'] ); // Input var ok; sanitization ok.
									?>
									<nav class="nav-tab-wrapper">
										<?php
										foreach ( $fonts_settings as $item ) {
											$class = ( $item['id'] === $tab ) ? 'nav-tab-active' : '';

											printf( '<a class="nav-tab %4$s" href="%1$s&tab=%2$s">%3$s</a>',
												esc_url( powerkit_get_page_url( $this->slug, 'themes' ) ), esc_attr( $item['id'] ), esc_html( $item['name'] ), esc_attr( $class )
											);
										}
										?>
									</nav>

									<?php
									foreach ( $fonts_settings as $item ) {
										if ( $item['id'] === $tab ) {
											?>
											<div id="tab-<?php echo esc_attr( $item['id'] ); ?>" class="tab-wrap">
												<?php
												if ( is_callable( $item['function'] ) ) {
													call_user_func( $item['function'] );
												}
												?>
											</div>
											<?php
										}
									}
									?>
								</div>
						<?php endif; ?>
					</div>
				</div>
			<?php
		}

		/**
		 * Add support wp-custom-fonts in Kirki.
		 *
		 * @param array $settings Pre settings.
		 */
		public function kirki_fonts_choices( $settings = array() ) {
			$fonts_list = apply_filters( 'powerkit_fonts_list', array() );

			if ( ! $fonts_list ) {
				return $settings;
			}

			$fonts_settings = array(
				'fonts' => array(
					'google'   => array(),
					'families' => isset( $fonts_list['families'] ) ? $fonts_list['families'] : null,
					'variants' => isset( $fonts_list['variants'] ) ? $fonts_list['variants'] : null,
				),
			);

			$fonts_settings = array_merge( (array) $fonts_settings, (array) $settings );

			return $fonts_settings;
		}

		/**
		 * Register the stylesheets and JavaScript for the admin area.
		 *
		 * @param string $page Current page.
		 */
		public function admin_enqueue_scripts( $page ) {
			if ( 'appearance_page_' . powerkit_get_page_slug( $this->slug ) === $page ) {

				wp_enqueue_media();

				wp_enqueue_script( 'admin-powerkit-fonts', plugin_dir_url( __FILE__ ) . 'js/admin-powerkit-fonts.js', array( 'jquery' ), powerkit_get_setting( 'version' ), false );

				wp_localize_script( 'admin-powerkit-fonts', 'powerkitFonts', array(
					'delete' => esc_html__( 'Are you sure you want to delete this font and all the files downloaded from it?', 'powerkit' ),
					'title'  => esc_html__( 'Select or Upload Font', 'powerkit' ),
					'button' => esc_html__( 'Use This File', 'powerkit' ),
				) );
			}
		}
	}

	new Powerkit_Fonts();
}

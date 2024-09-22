<?php
/**
 * Post Views
 *
 * @package    Powerkit
 * @subpackage Modules
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_Post_Views extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name     = esc_html__( 'Post Views', 'powerkit' );
			$this->desc     = esc_html__( 'This module links to your Google Analytics account to retrieve the pageviews for your posts.', 'powerkit' );
			$this->slug     = 'post_views';
			$this->type     = 'default';
			$this->category = 'tools';
			$this->priority = 140;
			$this->public   = true;
			$this->enabled  = true;

			$this->initialize_database();

			// Check Post Views Counter.
			if ( $this->post_views_counter() ) {

				// Making the module inactive.
				add_filter( 'powerkit_module_enabled', function( $status, $slug ) {

					if ( 'post_views' === $slug ) {
						$status = false;
					}

					return $status;
				}, 10, 2 );

				// Set message.
				ob_start();
				?>
				<div class="update-message notice inline notice-warning notice-alt">
					<?php esc_html_e( 'Please deactivate the Post Views Counter plugin.', 'powerkit' ); ?>
				</div>
				<?php
				$this->desc .= ob_get_clean();
			} else {

				$this->links = array(
					array(
						'name' => esc_html__( 'Go to settings', 'powerkit' ),
						'url'  => powerkit_get_page_url( $this->slug ),
					),
					array(
						'name'   => esc_html__( 'View documentation', 'powerkit' ),
						'url'    => powerkit_get_setting( 'documentation' ) . '/post-views/',
						'target' => '_blank',
					),
				);
			}
		}


		/**
		 * Check post views plugin.
		 */
		public function post_views_counter() {
			return class_exists( 'Post_Views_Counter' );
		}

		/**
		 * Initialize database
		 */
		public function initialize_database() {
			require_once dirname( __FILE__ ) . '/helpers/db-powerkit-post-views.php';
		}

		/**
		 * Initialize module
		 */
		public function initialize() {
			if ( $this->post_views_counter() ) {
				return;
			}

			/* Load the required dependencies for this module */

			// Helpers Functions for the module.
			require_once dirname( __FILE__ ) . '/helpers/helper-powerkit-post-views.php';
			require_once dirname( __FILE__ ) . '/helpers/query-powerkit-post-views.php';

			// Admin and public area.
			require_once dirname( __FILE__ ) . '/admin/class-powerkit-post-views-admin.php';
			require_once dirname( __FILE__ ) . '/public/class-powerkit-post-views-public.php';

			new Powerkit_Post_Views_Admin( $this->slug );
			new Powerkit_Post_Views_Public( $this->slug );
		}
	}

	new Powerkit_Post_Views();
}

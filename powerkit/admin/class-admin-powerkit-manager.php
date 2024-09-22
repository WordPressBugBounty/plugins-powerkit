<?php
/**
 * Admin Manager
 *
 * @package    Powerkit
 * @subpackage Admin
 */

/**
 * Admin manager class.
 */
class Admin_Powerkit_Manager {

	/**
	 * The message output.
	 *
	 * @var string $msg The message output.
	 */
	public $msg;

	/**
	 * The slug name to refer to this menu by.
	 *
	 * @var string $menu_slug The menu slug.
	 */
	public $menu_slug = 'manager';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$self = $this;
		add_action( 'init', function () use ( $self ) {
			$self->handler_actions();

			add_action( 'admin_head', array( $self, 'manager_styles' ) );
			add_action( 'admin_menu', array( $self, 'add_menu_page' ) );
			add_action( 'plugin_action_links_powerkit/powerkit.php', array( $self, 'action_links' ) );
		} );
	}

	/**
	 * Register a callback for our specific plugin's actions
	 *
	 * @param array $actions An array of plugin action links.
	 */
	public function action_links( $actions ) {
		$actions[] = sprintf( '<a href="%s">%s</a>', powerkit_get_page_url( $this->menu_slug ), esc_html__( 'Settings', 'powerkit' ) );

		return $actions;
	}

	/**
	 * Add menu page
	 */
	public function add_menu_page() {

		$svg = '<svg width="18px" height="18px" viewBox="0 0 18 18" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g fill="#81878B"><path d="M6.78872178,17.7263347 L7.27177123,13.7724855 L8.84170382,13.7724855 C11.0605419,13.7724855 12.7351366,13.2073097 13.6980286,12.160688 C14.6399882,11.1349987 14.7865152,9.87905267 14.7865152,8.87429582 C14.7865152,7.9532687 14.6609206,6.69732264 13.6142989,5.67163335 C12.5676771,4.64594406 11.1442716,4.43661972 9.55340659,4.43661972 L4.88547371,4.43661972 L3.45307884,16.0879292 C1.35084819,14.4404374 5.68434189e-14,11.8779725 5.68434189e-14,9 C5.68434189e-14,4.02943725 4.02943725,0 9,0 C13.9705627,0 18,4.02943725 18,9 C18,13.9705627 13.9705627,18 9,18 C8.23700819,18 7.49619203,17.9050548 6.78872178,17.7263347 Z M9.05102816,7.36716054 C9.55340659,7.36716054 10.3069742,7.3462281 10.7674878,7.8276741 C10.9768121,8.03699844 11.165204,8.41378226 11.165204,9.02082286 C11.165204,9.33480937 11.1233392,9.85812024 10.7256229,10.2558365 C10.1813796,10.8000798 9.2603525,10.8419447 8.75797408,10.8419447 L7.64855505,10.8419447 L8.06720374,7.36716054 L9.05102816,7.36716054 Z" id="Powerkit"></path></g></svg>';

		$svg = 'data:image/svg+xml;base64,' . base64_encode( $svg );

		add_menu_page( esc_html__( 'Powerkit', 'powerkit' ), esc_html__( 'Powerkit', 'powerkit' ), 'manage_options', powerkit_get_page_slug( $this->menu_slug ), array( $this, 'settings_page' ), $svg );
	}

	/**
	 * Manager styles
	 */
	public function manager_styles() {
		?>
		<style>
		.powerkit-manager .wp-list-table th,
		.powerkit-manager .wp-list-table td {
			padding-top: 16px;
			padding-bottom: 16px;
		}
		.powerkit-manager .dashicons.status {
			width: auto;
		}
		.powerkit-manager .dashicons.status:before{
			background-color: transparent;
			box-shadow: none;
			font-size: initial;
		}
		.powerkit-manager .spinner {
			background-size: 16px 16px;
			float: none;
			width: 16px;
			height: 16px;
			margin: 3px 4px;
		}
		</style>
		<?php
	}



	/**
	 * Settings
	 */
	public function settings_page() {

		powerkit_uuid_hash();

		$modules = powerkit_get_modules();

		$page_link = powerkit_get_page_url( $this->menu_slug, 'admin' );

		// Check wpnonce.
		if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'] ) ) { // Input var ok; sanitization ok.
			return;
		}

		// Filter modules.
		if ( isset( $_REQUEST['filter'] ) ) { // Input var ok.
			$filter = sanitize_key( $_REQUEST['filter'] ); // Input var ok.
		}

		// Output Message.
		if ( $this->msg ) {
			echo wp_kses( $this->msg, 'post' );
		}
		?>
		<div class="wrap powerkit-manager">
			<h1><?php esc_html_e( 'Powerkit Modules', 'powerkit' ); ?></h1>

			<div id="poststuff">
				<div id="post-body" class="metabox-holder">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<form method="post">
								<div class="tablenav">
									<div class="alignleft">
										<?php
										$active_link = add_query_arg( array(
											'_wpnonce' => wp_create_nonce(),
											'filter'   => 'active',
										), $page_link );

										$inactive_link = add_query_arg( array(
											'_wpnonce' => wp_create_nonce(),
											'filter'   => 'inactive',
										), $page_link );

										$num_modules          = 0;
										$num_active_modules   = 0;
										$num_inactive_modules = 0;

										if ( $modules ) {
											foreach ( $modules as $key => $module ) {
												if ( ! $module['public'] ) {
													continue;
												}
												$num_modules++;
												if ( powerkit_module_enabled( $module['slug'] ) ) {
													$num_active_modules++;
												} else {
													$num_inactive_modules++;
												}
											}
										}
										?>
										<ul class="subsubsub">
											<li class="all"><a href="<?php echo esc_url( $page_link ); ?>" class="<?php echo esc_attr( ( ! isset( $filter ) ) ? 'current' : '' ); ?>"><?php esc_html_e( 'All', 'powerkit' ); ?> <span class="count">(<?php echo esc_attr( $num_modules ); ?>)</span></a> |</li>
											<li class="active"><a href="<?php echo esc_url( $active_link ); ?>" class="<?php echo esc_attr( ( isset( $filter ) && 'active' === $filter ) ? 'current' : '' ); ?>"><?php esc_html_e( 'Active', 'powerkit' ); ?> <span class="count">(<?php echo esc_attr( $num_active_modules ); ?>)</span></a> |</li>
											<li class="inactive"><a href="<?php echo esc_url( $inactive_link ); ?>" class="<?php echo esc_attr( ( isset( $filter ) && 'inactive' === $filter ) ? 'current' : '' ); ?>"><?php esc_html_e( 'Inactive', 'powerkit' ); ?> <span class="count">(<?php echo esc_attr( $num_inactive_modules ); ?>)</span></a></li>
										</ul>
									</div>
									<div class="tablenav top">
										<div class="alignleft actions bulkactions">
										<label for="bulk-action-selector-top" class="screen-reader-text"><?php esc_html_e( 'Select bulk action', 'powerkit' ); ?></label>
											<select name="action" id="bulk-action-selector-top">
												<option value="-1"><?php esc_html_e( 'Bulk Actions', 'powerkit' ); ?></option>
												<option value="activate-selected"><?php esc_html_e( 'Activate', 'powerkit' ); ?></option>
												<option value="deactivate-selected"><?php esc_html_e( 'Deactivate', 'powerkit' ); ?></option>
											</select>
											<input type="submit" id="doaction" class="button action" value="Apply">
										</div>

										<div class="tablenav-pages one-page">
											<span class="displaying-num"><?php echo esc_attr( $num_modules ); ?> <?php esc_html_e( 'items', 'powerkit' ); ?></span>
										</div>
									</div>
								</div>
								<table class="wp-list-table widefat plugins">
									<thead>
										<tr>
											<th id="cb" class="manage-column column-cb check-column">
												<input id="cb-select-all-1" type="checkbox">
											</th>
											<th scope="col" class="manage-column column-name column-primary"><?php esc_html_e( 'Module', 'powerkit' ); ?></th>
											<th scope="col" class="manage-column column-description"><?php esc_html_e( 'Description', 'powerkit' ); ?></th>
											<th scope="col" class="manage-column"></th>
										</tr>
									</thead>
									<tbody id="the-list">
										<?php
										$counter = 0;
										// Loop modules.
										if ( $modules ) {
											foreach ( $modules as $key => $module ) {
												// Public module.
												if ( ! $module['public'] ) {
													continue;
												}

												// Check module enabled.
												$module_enabled = powerkit_module_enabled( $module['slug'] );

												// Filter list.
												if ( isset( $filter ) ) {
													if ( 'active' === $filter && ! $module_enabled ) {
														continue;
													}
													if ( 'inactive' === $filter && $module_enabled ) {
														continue;
													}
												}

												$activate_link = add_query_arg( array(
													'_wpnonce' => wp_create_nonce(),
													'slug'     => $module['slug'],
													'action'   => 'activate',
												), $page_link );

												$deactivate_link = add_query_arg( array(
													'_wpnonce' => wp_create_nonce(),
													'slug'     => $module['slug'],
													'action'   => 'deactivate',
												), $page_link );

												$counter++;
												?>
												<tr class="<?php echo esc_attr( $module_enabled ? 'active' : 'inactive' ); ?>">
													<th scope="row" class="check-column">
														<input type="checkbox" name="checked[]" value="<?php echo esc_attr( $module['slug'] ); ?>">
													</th>
													<td scope="col" class="plugin-title column-primary">
														<strong><?php echo esc_html( $module['name'] ); ?></strong>

														<div class="actions">
															<?php if ( 'default' === $module['type'] ) { ?>
																<?php if ( $module_enabled ) { ?>
																	<span class="edit">
																		<a href="<?php echo esc_url( $deactivate_link ); ?>" role="button"><?php esc_html_e( 'Deactivate', 'powerkit' ); ?></a>
																	</span>
																<?php } else { ?>
																	<span class="edit">
																		<a href="<?php echo esc_url( $activate_link ); ?>" role="button"><?php esc_html_e( 'Activate', 'powerkit' ); ?></a>
																	</span>
																<?php } ?>
															<?php } ?>
														</div>
													</td>
													<td scope="col" class="column-description desc">
														<div class="plugin-description">
															<p><?php echo esc_html( $module['desc'] ); ?></p>
														</div>

														<?php
														if ( $module_enabled && $module['links'] ) {
															$counter = 0;
															foreach ( $module['links'] as $link ) {
																$counter++;
																if ( ! isset( $link['name'] ) ) {
																	continue;
																}

																// Target of link.
																$target = isset( $link['target'] ) ? $link['target'] : '_self';

																// Output separator.
																echo 1 < $counter ? '|' : '';
																?>
																<span class="edit">
																	<a target="<?php echo esc_attr( $target ); ?>" href="<?php echo esc_url( isset( $link['url'] ) ? $link['url'] : '' ); ?>" role="button">
																		<?php echo esc_html( $link['name'] ); ?>
																	</a>
																</span>
																<?php
															}
														}
														?>
													</td>
													<td scope="col" class="manage-column">
														<?php if ( $module['badge'] ) { ?>
															<div class="pk-badge pk-badge-primary"><?php echo esc_attr( $module['badge'] ); ?></div>
														<?php } ?>
													</td>
												</tr>
												<?php
											}
										}

										if ( ! $counter ) {
											?>
												<tr>
													<td scope="col" colspan="4"><?php esc_html_e( 'No modules avaliable.', 'powerkit' ); ?></td>
												</tr>
											<?php
										}
										?>
									</tbody>
								</table>
								<?php wp_nonce_field(); ?>
							</form>

							<script>
							if ( window.history.replaceState ) {
								if ( window.location.href.indexOf( 'action=' ) >= 0 ) {
									window.history.pushState( null, '', '<?php echo esc_url( $page_link ); ?>' );
								}
							}
							</script>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Handler actions.
	 */
	public function handler_actions() {

		// Check wpnonce.
		if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'] ) ) { // Input var ok; sanitization ok.
			return;
		}

		if ( ! isset( $_REQUEST['action'] ) ) { // Input var ok; sanitization ok.
			return;
		}

		$action = sanitize_title( $_REQUEST['action'] ); // Input var ok; sanitization ok.

		// Bulk Actions.
		if ( isset( $_REQUEST['checked'] ) && is_array( $_REQUEST['checked'] ) ) { // Input var ok; sanitization ok.
			$checked = array_map( 'sanitize_key', $_REQUEST['checked'] ); // Input var ok; sanitization ok.

			foreach ( $checked as $slug ) {
				if ( 'activate-selected' === $action ) {
					$this->set_module_state( $slug, 1 );
				} elseif ( 'deactivate-selected' === $action ) {
					$this->set_module_state( $slug, 0 );
				}
			}
		}

		if ( ! isset( $_REQUEST['slug'] ) ) { // Input var ok.
			return;
		}

		$slug = sanitize_key( $_REQUEST['slug'] ); // Input var ok.

		// Activate module.
		if ( 'activate' === $action && $slug ) {
			$this->set_module_state( $slug, 1 );
		}

		// Deactivate module.
		if ( 'deactivate' === $action && $slug ) {
			$this->set_module_state( $slug, 0 );
		}
	}

	/**
	 * Set state module.
	 *
	 * @param string $slug  The slug module.
	 * @param bool   $state The state module.
	 */
	public function set_module_state( $slug, $state ) {
		update_option( sprintf( 'powerkit_enabled_%s', $slug ), $state );

		if ( $state ) {
			$this->msg = sprintf( '<div id="message" class="updated fade"><p><strong>%s</strong></p></div>', esc_html__( 'Module activated.', 'powerkit' ) );
		} else {
			$this->msg = sprintf( '<div id="message" class="updated fade"><p><strong>%s</strong></p></div>', esc_html__( 'Module deactivated.', 'powerkit' ) );
		}
	}
}

new Admin_Powerkit_Manager();

<?php
/**
 * The public-facing functionality of the module.
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    Powerkit
 * @subpackage Modules/public
 */

/**
 * The public-facing functionality of the module.
 */
class Powerkit_Opt_In_Forms_Public extends Powerkit_Module_Public {

	/**
	 * Initialize
	 */
	public function initialize() {
		add_filter( 'powerkit_pinit_exclude_selectors', array( $this, 'pinit_disable' ) );
		add_action( 'powerkit_subscribe_template', array( $this, 'register_subscription_form' ) );
		add_action( 'wp_ajax_powerkit_subscription', array( $this, 'subscription' ) );
		add_action( 'wp_ajax_nopriv_powerkit_subscription', array( $this, 'subscription' ) );
	}

	/**
	 * PinIt disable
	 *
	 * @param string $selectors List selectors.
	 */
	public function pinit_disable( $selectors ) {
		$selectors[] = '.pk-subscribe-image';

		return $selectors;
	}

	/**
	 * Register Form Template
	 *
	 * @since    1.0.0
	 * @param array $params The params of form.
	 */
	public function register_subscription_form( $params = array() ) {

		$service = isset( $params['service'] ) ? $params['service'] : powerkit_get_subscription_service();

		$class = sprintf( 'pk-subscribe-form-%s', $params['type'] );

		$class .= $params['bg_image_id'] ? ' pk-subscribe-with-bg' : '';
		$class .= $params['display_name'] ? ' pk-subscribe-with-name' : '';

		switch ( $service ) {
			case 'mailchimp':
				$this->render_mailchimp_form( $params, $class );
				break;

			case 'kit':
				$this->render_kit_form( $params, $class );
				break;

			case 'mailerlite':
				$this->render_mailerlite_form( $params, $class );
				break;

			case 'custom':
				$this->render_custom_form( $params, $class );
				break;

			default:
				$this->render_mailchimp_form( $params, $class );
		}
	}

	/**
	 * Subscription.
	 *
	 * @since    1.0.0
	 */
	public function subscription() {
		powerkit_uuid_hash();

		// Check wpnonce.
		if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'] ) ) { // Input var ok; sanitization ok.
			return;
		}

		// Default service.
		$service = 'mailchimp';

		if ( isset( $_REQUEST['service'] ) ) { // Input var ok.
			$service = sanitize_text_field( wp_unslash( $_REQUEST['service'] ) ); // Input var ok.
		}

		if ( isset( $_REQUEST['list_id'] ) ) { // Input var ok.
			$list_id = sanitize_text_field( wp_unslash( $_REQUEST['list_id'] ) ); // Input var ok.
		}

		if ( isset( $_REQUEST['USER'] ) ) { // Input var ok.
			$user = sanitize_text_field( wp_unslash( $_REQUEST['USER'] ) ); // Input var ok.
		}

		if ( isset( $_REQUEST['EMAIL'] ) ) { // Input var ok.
			$email = sanitize_email( wp_unslash( $_REQUEST['EMAIL'] ) ); // Input var ok.
		}

		if ( ! isset( $list_id ) || ! $list_id ) {
			wp_send_json_error( esc_html__( 'Something is wrong with your list ID.', 'powerkit' ) );
		}

		if ( ! isset( $email ) || ! $email || ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			wp_send_json_error( esc_html__( 'Email is invalid.', 'powerkit' ) );
		}

		switch ( $service ) {
			case 'mailchimp':
				$this->process_mailchimp_subscription( $list_id, $email, $user );
				break;

			case 'kit':
				$this->process_kit_subscription( $list_id, $email, $user );
				break;

			case 'mailerlite':
				$this->process_mailerlite_subscription( $list_id, $email, $user );
				break;

			default:
				$this->process_mailchimp_subscription( $list_id, $email, $user );
		}
	}

	/**
	 * Process MailChimp subscription
	 *
	 * @param string $list_id List ID.
	 * @param string $email   Email address.
	 * @param string $user    User name.
	 */
	private function process_mailchimp_subscription( $list_id, $email, $user = null ) {
		$token = get_option( 'powerkit_mailchimp_token' );

		if ( $token ) {
			if ( get_option( 'powerkit_mailchimp_double_optin', false ) ) {
				$status = 'pending';
			} else {
				$status = 'subscribed';
			}

			// Request parameters.
			$args = array(
				'email_address' => $email,
				'status'        => $status,
			);

			// If display first name field.
			if ( isset( $user ) ) {
				$args['merge_fields'] = array(
					'FNAME' => $user,
				);
			}

			$result = powerkit_mailchimp_request( 'POST', "lists/$list_id/members", $args );

			if ( isset( $result['status'] ) && 'subscribed' === $result['status'] ) {

				wp_send_json_success( esc_html__( 'You have successfully subscribed.', 'powerkit' ) );

			} elseif ( isset( $result['status'] ) && 'pending' === $result['status'] ) {

				wp_send_json_success( esc_html__( 'You have successfully subscribed. Confirm the subscription in your mailbox.', 'powerkit' ) );

			} elseif ( isset( $result['title'] ) && 'Member Exists' === $result['title'] ) {

				wp_send_json_error( esc_html__( 'You are already subscribed.', 'powerkit' ) );

			} else {

				if ( isset( $result['status'] ) && isset( $result['detail'] ) && 400 <= $result['status'] ) {
					$result = $result['detail'];
				}

				wp_send_json_error( $result );
			}
		}
	}

	/**
	 * Process Kit.com subscription
	 *
	 * @param string $list_id List ID.
	 * @param string $email   Email address.
	 * @param string $user    User name.
	 */
	private function process_kit_subscription( $list_id, $email, $user = null ) {
		$token = get_option( 'powerkit_kit_token' );

		if ( $token ) {
			$args = array(
				'email' => $email,
			);

			if ( isset( $user ) ) {
				$args['first_name'] = $user;
			}

			$endpoint = 'forms/' . $list_id . '/subscribe';

			$result = powerkit_kit_request( 'POST', $endpoint, $args );

			if ( isset( $result['subscription'] ) ) {
				if ( isset( $result['subscription']['state'] ) ) {
					if ( 'active' === $result['subscription']['state'] ) {
						wp_send_json_success( esc_html__( 'You are already subscribed.', 'powerkit' ) );
					} elseif ( 'inactive' === $result['subscription']['state'] ) {
						wp_send_json_success( esc_html__( 'You are already subscribed, confirm your subscription by email.', 'powerkit' ) );
					} elseif ( 'cancelled' === $result['subscription']['state'] ) {
						wp_send_json_success( esc_html__( 'You have successfully subscribed, confirm your subscription by email.', 'powerkit' ) );
					} else {
						wp_send_json_success( esc_html__( 'You have successfully subscribed.', 'powerkit' ) );
					}
				} else {
					wp_send_json_success( esc_html__( 'You have successfully subscribed.', 'powerkit' ) );
				}
			} elseif ( isset( $result['error'] ) ) {
					wp_send_json_error( (string) $result['error'] );
			} else {
				wp_send_json_error( esc_html__( 'An error occurred while subscribing.', 'powerkit' ) );
			}
		}
	}

	/**
	 * Process MailerLite subscription
	 *
	 * @param string $list_id List ID.
	 * @param string $email   Email address.
	 * @param string $user    User name.
	 */
	private function process_mailerlite_subscription( $list_id, $email, $user = null ) {
		$token = get_option( 'powerkit_mailerlite_token' );

		if ( $token ) {
			// Request parameters.
			$args = array(
				'email' => $email,
			);

			// Add first name if available.
			if ( isset( $user ) ) {
				$args['name'] = $user;
			}

			// Add to group.
			$result = powerkit_mailerlite_request( 'POST', "groups/{$list_id}/subscribers", $args );

			if ( isset( $result['id'] ) ) {
				wp_send_json_success( esc_html__( 'You have successfully subscribed.', 'powerkit' ) );
			} elseif ( isset( $result['error'] ) && isset( $result['error']['code'] ) && 409 === $result['error']['code'] ) {
				wp_send_json_error( esc_html__( 'You are already subscribed.', 'powerkit' ) );
			} else {
				$error_message = isset( $result['error']['message'] ) ? $result['error']['message'] : esc_html__( 'An error occurred while subscribing.', 'powerkit' );
				wp_send_json_error( $error_message );
			}
		}
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function wp_enqueue_scripts() {
		// Styles.
		wp_enqueue_style( 'powerkit-opt-in-forms', powerkit_style( plugin_dir_url( __FILE__ ) . 'css/public-powerkit-opt-in-forms.css' ), array(), powerkit_get_setting( 'version' ), 'all' );

		// Add RTL support.
		wp_style_add_data( 'powerkit-opt-in-forms', 'rtl', 'replace' );

		// Scripts.
		wp_enqueue_script( 'powerkit-opt-in-forms', plugin_dir_url( __FILE__ ) . 'js/public-powerkit-opt-in-forms.js', array( 'jquery' ), powerkit_get_setting( 'version' ), true );

		wp_localize_script( 'powerkit-opt-in-forms', 'opt_in', array(
			'ajax_url'        => admin_url( 'admin-ajax.php' ),
			'warning_privacy' => esc_html__( 'Please confirm that you agree with our policies.', 'powerkit' ),
			'is_admin'        => current_user_can( 'edit_posts' ) || current_user_can( 'manage_options' ),
			'server_error'    => esc_html__( 'Server error occurred. Please try again later.', 'powerkit' ),
		) );
	}

	/**
	 * Render MailChimp form
	 *
	 * @param array  $params     The params of form.
	 * @param string $wrap_class Wrap class.
	 */
	private function render_mailchimp_form( $params, $wrap_class ) {
		$token = get_option( 'powerkit_mailchimp_token' );

		if ( $token ) {
			if ( ! $params['list_id'] || 'default' === $params['list_id'] ) {
				$params['list_id'] = get_option( 'powerkit_mailchimp_list' );
			}

			if ( $params['list_id'] ) {
				?>
				<div class="pk-subscribe-form-wrap <?php echo esc_attr( $wrap_class ); ?>" data-service="mailchimp">
					<?php if ( $params['bg_image_id'] ) { ?>
						<div class="pk-subscribe-bg">
							<?php
							echo wp_get_attachment_image( $params['bg_image_id'], apply_filters( 'powerkit_subscribe_image_size', 'large' ), false,
								array( 'class' => 'pk-subscribe-image' )
							);
							?>
						</div>
					<?php } ?>

					<div class="pk-subscribe-container <?php echo esc_attr( $params['bg_image_id'] ? ' pk-bg-overlay' : '' ); ?>">
						<div class="pk-subscribe-data">
							<?php
							if ( $params['title'] ) {
								echo wp_kses( $params['title'], 'pk-title' ); // XSS.
							}
							?>

							<?php if ( $params['text'] ) { ?>
								<p class="pk-subscribe-message <?php echo 'block' !== $params['type'] ? 'pk-font-heading' : ''; ?>"><?php echo esc_html( $params['text'] ); ?></p>
							<?php } ?>

							<form method="post" class="subscription">
								<input type="hidden" name="service" value="mailchimp">
								<input type="hidden" name="list_id" value="<?php echo esc_attr( $params['list_id'] ); ?>">

								<div class="pk-input-group">
									<?php if ( $params['display_name'] ) { ?>
										<input type="text" name="USER" class="user form-control" placeholder="<?php echo esc_attr( apply_filters( 'powerkit_subscribe_placeholder_name', esc_html__( 'Enter your name', 'powerkit' ) ) ); ?>">
									<?php } ?>

									<input type="text" name="EMAIL" class="email form-control" placeholder="<?php echo esc_attr( apply_filters( 'powerkit_subscribe_placeholder_email', esc_html__( 'Enter your email', 'powerkit' ) ) ); ?>">

									<button class="pk-subscribe-submit" type="submit"><?php echo wp_kses( apply_filters( 'powerkit_subscribe_submit', esc_html__( 'Subscribe', 'powerkit' ) ), 'post' ); ?></button>
								</div>

								<?php wp_referer_field(); ?>
							</form>

							<?php if ( $params['privacy'] ) { ?>
								<div class="pk-privacy pk-color-secondary">
									<label><input name="pk-privacy" type="checkbox"><?php echo wp_kses( $params['privacy'], 'post' ); ?></label>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
				<?php
			} else {
				/* translators: MailChimp Settings. */
				powerkit_alert_warning( sprintf( __( 'Please select the "List" for your subscription form in <object><a href="%s" target="_blank">Opt-In Forms Settings</a></object>.', 'powerkit' ), esc_url( powerkit_get_page_url( 'opt_in_forms' ) ) ) );
			}
		} else {
			/* translators: MailChimp Settings. */
			powerkit_alert_warning( sprintf( __( 'Please add your MailChimp Token in <object><a href="%s" target="_blank">Opt-In Forms Settings</a></object>.', 'powerkit' ), esc_url( powerkit_get_page_url( 'opt_in_forms' ) ) ) );
		}
	}

	/**
	 * Render Kit.com form
	 *
	 * @param array  $params     The params of form.
	 * @param string $wrap_class Wrap class.
	 */
	private function render_kit_form( $params, $wrap_class ) {
		$token = get_option( 'powerkit_kit_token' );

		if ( $token ) {
			if ( ! $params['list_id'] || 'default' === $params['list_id'] ) {
				$params['list_id'] = get_option( 'powerkit_kit_form' );
			}

			if ( $params['list_id'] ) {
				?>
				<div class="pk-subscribe-form-wrap <?php echo esc_attr( $wrap_class ); ?>" data-service="kit">
					<?php if ( $params['bg_image_id'] ) { ?>
						<div class="pk-subscribe-bg">
							<?php
							echo wp_get_attachment_image( $params['bg_image_id'], apply_filters( 'powerkit_subscribe_image_size', 'large' ), false,
								array( 'class' => 'pk-subscribe-image' )
							);
							?>
						</div>
					<?php } ?>

					<div class="pk-subscribe-container <?php echo esc_attr( $params['bg_image_id'] ? ' pk-bg-overlay' : '' ); ?>">
						<div class="pk-subscribe-data">
							<?php
							if ( $params['title'] ) {
								echo wp_kses( $params['title'], 'pk-title' );
							}
							?>

							<?php if ( $params['text'] ) { ?>
								<p class="pk-subscribe-message <?php echo 'block' !== $params['type'] ? 'pk-font-heading' : ''; ?>"><?php echo esc_html( $params['text'] ); ?></p>
							<?php } ?>

							<form method="post" class="subscription">
								<input type="hidden" name="service" value="kit">
								<input type="hidden" name="list_id" value="<?php echo esc_attr( $params['list_id'] ); ?>">

								<div class="pk-input-group">
									<?php if ( $params['display_name'] ) { ?>
										<input type="text" name="USER" class="user form-control" placeholder="<?php echo esc_attr( apply_filters( 'powerkit_subscribe_placeholder_name', esc_html__( 'Enter your name', 'powerkit' ) ) ); ?>">
									<?php } ?>

									<input type="text" name="EMAIL" class="email form-control" placeholder="<?php echo esc_attr( apply_filters( 'powerkit_subscribe_placeholder_email', esc_html__( 'Enter your email', 'powerkit' ) ) ); ?>">

									<button class="pk-subscribe-submit" type="submit"><?php echo wp_kses( apply_filters( 'powerkit_subscribe_submit', esc_html__( 'Subscribe', 'powerkit' ) ), 'post' ); ?></button>
								</div>

								<?php wp_referer_field(); ?>
							</form>

							<?php if ( $params['privacy'] ) { ?>
								<div class="pk-privacy pk-color-secondary">
									<label><input name="pk-privacy" type="checkbox"><?php echo wp_kses( $params['privacy'], 'post' ); ?></label>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
				<?php
			} else {
				/* translators: Kit.com Settings. */
				powerkit_alert_warning( sprintf( __( 'Please select the Kit.com Form in <object><a href="%s" target="_blank">Opt-In Forms Settings</a></object>.', 'powerkit' ), esc_url( powerkit_get_page_url( 'opt_in_forms' ) ) ) );
			}
		} else {
			/* translators: Kit.com Settings. */
			powerkit_alert_warning( sprintf( __( 'Please add your Kit.com Token in <object><a href="%s" target="_blank">Opt-In Forms Settings</a></object>.', 'powerkit' ), esc_url( powerkit_get_page_url( 'opt_in_forms' ) ) ) );
		}
	}

	/**
	 * Render MailerLite form
	 *
	 * @param array  $params     The params of form.
	 * @param string $wrap_class Wrap class.
	 */
	private function render_mailerlite_form( $params, $wrap_class ) {
		$token = get_option( 'powerkit_mailerlite_token' );

		if ( $token ) {
			if ( ! $params['list_id'] || 'default' === $params['list_id'] ) {
				$params['list_id'] = get_option( 'powerkit_mailerlite_group' );
			}

			if ( $params['list_id'] ) {
				?>
				<div class="pk-subscribe-form-wrap <?php echo esc_attr( $wrap_class ); ?>" data-service="mailerlite">
					<?php if ( $params['bg_image_id'] ) { ?>
						<div class="pk-subscribe-bg">
							<?php
							echo wp_get_attachment_image( $params['bg_image_id'], apply_filters( 'powerkit_subscribe_image_size', 'large' ), false,
								array( 'class' => 'pk-subscribe-image' )
							);
							?>
						</div>
					<?php } ?>

					<div class="pk-subscribe-container <?php echo esc_attr( $params['bg_image_id'] ? ' pk-bg-overlay' : '' ); ?>">
						<div class="pk-subscribe-data">
							<?php
							if ( $params['title'] ) {
								echo wp_kses( $params['title'], 'pk-title' );
							}
							?>

							<?php if ( $params['text'] ) { ?>
								<p class="pk-subscribe-message <?php echo 'block' !== $params['type'] ? 'pk-font-heading' : ''; ?>"><?php echo esc_html( $params['text'] ); ?></p>
							<?php } ?>

							<form method="post" class="subscription">
								<input type="hidden" name="service" value="mailerlite">
								<input type="hidden" name="list_id" value="<?php echo esc_attr( $params['list_id'] ); ?>">

								<div class="pk-input-group">
									<?php if ( $params['display_name'] ) { ?>
										<input type="text" name="USER" class="user form-control" placeholder="<?php echo esc_attr( apply_filters( 'powerkit_subscribe_placeholder_name', esc_html__( 'Enter your name', 'powerkit' ) ) ); ?>">
									<?php } ?>

									<input type="text" name="EMAIL" class="email form-control" placeholder="<?php echo esc_attr( apply_filters( 'powerkit_subscribe_placeholder_email', esc_html__( 'Enter your email', 'powerkit' ) ) ); ?>">

									<button class="pk-subscribe-submit" type="submit"><?php echo wp_kses( apply_filters( 'powerkit_subscribe_submit', esc_html__( 'Subscribe', 'powerkit' ) ), 'post' ); ?></button>
								</div>

								<?php wp_referer_field(); ?>
							</form>

							<?php if ( $params['privacy'] ) { ?>
								<div class="pk-privacy pk-color-secondary">
									<label><input name="pk-privacy" type="checkbox"><?php echo wp_kses( $params['privacy'], 'post' ); ?></label>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
				<?php
			} else {
				/* translators: MailerLite Settings. */
				powerkit_alert_warning( sprintf( __( 'Please select the MailerLite Group ID in <object><a href="%s" target="_blank">Opt-In Forms Settings</a></object>.', 'powerkit' ), esc_url( powerkit_get_page_url( 'opt_in_forms' ) ) ) );
			}
		} else {
			/* translators: MailerLite Settings. */
			powerkit_alert_warning( sprintf( __( 'Please add your MailerLite Token in <object><a href="%s" target="_blank">Opt-In Forms Settings</a></object>.', 'powerkit' ), esc_url( powerkit_get_page_url( 'opt_in_forms' ) ) ) );
		}
	}

	/**
	 * Render Custom form
	 *
	 * @param array  $params     The params of form.
	 * @param string $wrap_class Wrap class.
	 */
	private function render_custom_form( $params, $wrap_class ) {
		$form_action   = get_option( 'powerkit_custom_form_action' );
		$email_name    = get_option( 'powerkit_custom_email_name', 'email' );
		$name_field    = get_option( 'powerkit_custom_name_field', 'name' );
		$hidden_fields = powerkit_parse_custom_fields();

		if ( $form_action ) {
			?>
			<div class="pk-subscribe-form-wrap <?php echo esc_attr( $wrap_class ); ?>" data-service="custom">
				<?php if ( $params['bg_image_id'] ) { ?>
					<div class="pk-subscribe-bg">
						<?php
						echo wp_get_attachment_image( $params['bg_image_id'], apply_filters( 'powerkit_subscribe_image_size', 'large' ), false,
							array( 'class' => 'pk-subscribe-image' )
						);
						?>
					</div>
				<?php } ?>

				<div class="pk-subscribe-container <?php echo esc_attr( $params['bg_image_id'] ? ' pk-bg-overlay' : '' ); ?>">
					<div class="pk-subscribe-data">
						<?php
						if ( $params['title'] ) {
							echo wp_kses( $params['title'], 'pk-title' );
						}
						?>

						<?php if ( $params['text'] ) { ?>
							<p class="pk-subscribe-message <?php echo 'block' !== $params['type'] ? 'pk-font-heading' : ''; ?>"><?php echo esc_html( $params['text'] ); ?></p>
						<?php } ?>

						<form method="post" class="subscription" action="<?php echo esc_url( $form_action ); ?>" target="_blank">
							<input type="hidden" name="service" value="custom">

							<?php if ( ! empty( $hidden_fields ) ) { ?>
								<?php foreach ( $hidden_fields as $field_name => $field_value ) { ?>
									<input type="hidden" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $field_value ); ?>">
								<?php } ?>
							<?php } ?>

							<div class="pk-input-group">
								<?php if ( $params['display_name'] ) { ?>
									<input type="text" name="<?php echo esc_attr( $name_field ); ?>" class="user form-control" placeholder="<?php echo esc_attr( apply_filters( 'powerkit_subscribe_placeholder_name', esc_html__( 'Enter your name', 'powerkit' ) ) ); ?>">
								<?php } ?>

								<input type="text" name="<?php echo esc_attr( $email_name ); ?>" class="email form-control" placeholder="<?php echo esc_attr( apply_filters( 'powerkit_subscribe_placeholder_email', esc_html__( 'Enter your email', 'powerkit' ) ) ); ?>">

								<button class="pk-subscribe-submit" type="submit"><?php echo wp_kses( apply_filters( 'powerkit_subscribe_submit', esc_html__( 'Subscribe', 'powerkit' ) ), 'post' ); ?></button>
							</div>
						</form>

						<?php if ( $params['privacy'] ) { ?>
							<div class="pk-privacy pk-color-secondary">
								<label><input name="pk-privacy" type="checkbox"><?php echo wp_kses( $params['privacy'], 'post' ); ?></label>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
			<?php
		} else {
			/* translators: Custom Settings. */
			powerkit_alert_warning( sprintf( __( 'Please set a form action URL in <object><a href="%s" target="_blank">Opt-In Forms Settings</a></object>.', 'powerkit' ), esc_url( powerkit_get_page_url( 'opt_in_forms' ) ) ) );
		}
	}
}

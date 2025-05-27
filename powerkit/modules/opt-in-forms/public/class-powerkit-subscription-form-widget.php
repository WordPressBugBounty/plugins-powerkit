<?php
/**
 * Widget Subscription Form
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    Powerkit
 * @subpackage Powerkit/widgets
 */

/**
 * Widget Subscription Form
 */
class Powerkit_Subscription_Form_Widget extends WP_Widget {

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

		$this->default_settings = apply_filters( 'powerkit_opt_in_subscription_widget_settings', array(
			'title'        => esc_html__( 'Subscription Form', 'powerkit' ),
			'privacy'      => powerkit_mailchimp_get_privacy_text(),
			'text'         => '',
			'bg_image_id'  => false,
			'list_id'      => null,
			'type'         => 'widget',
			'display_name' => false,
			'service'      => powerkit_get_subscription_service(),
		) );

		$widget_details = array(
			'classname'   => 'powerkit_opt_in_subscription_widget',
			'description' => esc_html__( 'Add a subscription form to your sidebar.', 'powerkit' ),
		);

		// Actions.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		parent::__construct( 'powerkit_opt_in_subscription_widget', esc_html__( 'Subscription Form', 'powerkit' ), $widget_details );
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
		echo $args['before_widget']; // XSS.

		if ( $params['title'] ) {
			$params['title'] = $args['before_title'] . apply_filters( 'widget_title', $params['title'], $instance, $this->id_base ) . $args['after_title'];
		}
		?>

		<div class="widget-body">
			<?php do_action( 'powerkit_subscribe_template', $params ); ?>
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

		// Display first name field.
		if ( ! isset( $instance['display_name'] ) ) {
			$instance['display_name'] = false;
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

		$bg_image_url = $params['bg_image_id'] ? wp_get_attachment_image_url( intval( $params['bg_image_id'] ), 'large' ) : '';
		?>
			<!-- Title -->
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'powerkit' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $params['title'] ); ?>" /></p>

			<!-- Background Image container -->
			<div class="subscription-upload-image upload-img-container" data-frame-title="<?php esc_html_e( 'Select or upload background image', 'powerkit' ); ?>" data-frame-btn-text="<?php esc_html_e( 'Set background image', 'powerkit' ); ?>">
				<p class="uploaded-img-box">
					<label for="<?php echo esc_attr( $this->get_field_id( 'bg_image_id' ) ); ?>"><?php esc_html_e( 'Background image:', 'powerkit' ); ?></label>

					<span class="uploaded-image">
						<?php if ( $bg_image_url ) : ?>
							<img src="<?php echo esc_url( $bg_image_url ); ?>" style="display: block; margin-top: 5px; max-width:100%;" />
						<?php endif; ?>
					</span>

					<input id="<?php echo esc_attr( $this->get_field_id( 'bg_image_id' ) ); ?>" class="uploaded-img-id" name="<?php echo esc_attr( $this->get_field_name( 'bg_image_id' ) ); ?>" type="hidden" value="<?php echo esc_attr( $params['bg_image_id'] ); ?>" />
				</p>

				<!-- Add & remove image links -->
				<p class="hide-if-no-js">
					<a class="upload-img-link button button-primary <?php echo esc_attr( $bg_image_url ? 'hidden' : '' ); ?>" href="#"><?php esc_html_e( 'Add Image', 'powerkit' ); ?></a>
					<a class="delete-img-link button button-secondary <?php echo esc_attr( ! $bg_image_url ? 'hidden' : '' ); ?>" href="#"><?php esc_html_e( 'Remove Image', 'powerkit' ); ?></a>
				</p>
			</div>

			<!-- Subscribe Message -->
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>"><?php esc_html_e( 'Subscribe message:', 'powerkit' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>" type="text" value="<?php echo esc_attr( $params['text'] ); ?>" /></p>

			<!-- Subscription Service -->
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'service' ) ); ?>"><?php esc_html_e( 'Subscription Service:', 'powerkit' ); ?></label>
				<select class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'service' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'service' ) ); ?>">
					<option value="mailchimp" <?php selected( 'mailchimp', $params['service'] ); ?>><?php esc_html_e( 'MailChimp', 'powerkit' ); ?></option>
					<option value="kit" <?php selected( 'kit', $params['service'] ); ?>><?php esc_html_e( 'Kit.com', 'powerkit' ); ?></option>
					<option value="mailerlite" <?php selected( 'mailerlite', $params['service'] ); ?>><?php esc_html_e( 'MailerLite', 'powerkit' ); ?></option>
					<option value="custom" <?php selected( 'custom', $params['service'] ); ?>><?php esc_html_e( 'Custom', 'powerkit' ); ?></option>
				</select>
			</p>

			<!-- List/Form/Group ID field for all services -->
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'list_id' ) ); ?>"><?php esc_html_e( 'List/Form/Group ID:', 'powerkit' ); ?></label>
				<input class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'list_id' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'list_id' ) ); ?>" type="text" value="<?php echo esc_attr( $params['list_id'] ); ?>" placeholder="<?php esc_attr_e( 'Default', 'powerkit' ); ?>">
				<span class="description"><?php esc_html_e( 'If empty, the default ID from Settings → Opt-In Forms will be used.', 'powerkit' ); ?></span>
			</p>

			<!-- Display first name field -->
			<p><input id="<?php echo esc_attr( $this->get_field_id( 'display_name' ) ); ?>" class="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'display_name' ) ); ?>" type="checkbox" <?php checked( (bool) $params['display_name'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'display_name' ) ); ?>"><?php esc_html_e( 'Display first name field', 'powerkit' ); ?></label>
			<span class="howto">(<?php esc_html_e( 'Make sure the name field is supported by your selected service', 'powerkit' ); ?>)</span></p>
		<?php
	}

	/**
	 * Admin Enqunue Scripts
	 *
	 * @param string $page Current page.
	 */
	public function admin_enqueue_scripts( $page ) {
		if ( 'widgets.php' === $page ) {
			wp_enqueue_script( 'jquery' );

			wp_enqueue_media();

			ob_start();
			?>
			<script>
			jQuery( document ).ready(function( $ ) {

				var powerkitMediaFrame;
				/* Set all variables to be used in scope */
				var metaBox = '.subscription-upload-image';

				/* Add Image Link */
				$( metaBox ).find( '.upload-img-link' ).on( 'click', function( event ){
					event.preventDefault();

					var parentContainer = $( this ).parents( metaBox );

					// Options.
					var options = {
						title: parentContainer.data( 'frame-title' ) ? parentContainer.data( 'frame-title' ) : 'Select or Upload Media',
						button: {
							text: parentContainer.data( 'frame-btn-text' ) ? parentContainer.data( 'frame-btn-text' ) : 'Use this media',
						},
						library : { type : 'image' },
						multiple: false // Set to true to allow multiple files to be selected.
					};

					// Create a new media frame
					powerkitMediaFrame = wp.media( options );

					// When an image is selected in the media frame...
					powerkitMediaFrame.on( 'select', function() {

						// Get media attachment details from the frame state.
						var attachment = powerkitMediaFrame.state().get('selection').first().toJSON();

						// Send the attachment URL to our custom image input field.
						parentContainer.find( '.uploaded-image' ).html( '<img src="' + attachment.url + '" style="display: block; margin-top: 5px; max-width:100%;"/>' );
						parentContainer.find( '.uploaded-img-id' ).val( attachment.id ).change();
						parentContainer.find( '.upload-img-link' ).addClass( 'hidden' );
						parentContainer.find( '.delete-img-link' ).removeClass( 'hidden' );

						powerkitMediaFrame.close();
					});

					// Finally, open the modal on click.
					powerkitMediaFrame.open();
				});


				/* Delete Image Link */
				$( metaBox ).find( '.delete-img-link' ).on( 'click', function( event ){
					event.preventDefault();

					$( this ).parents( metaBox ).find( '.uploaded-image' ).html( '' );
					$( this ).parents( metaBox ).find( '.upload-img-link' ).removeClass( 'hidden' );
					$( this ).parents( metaBox ).find( '.delete-img-link' ).addClass( 'hidden' );
					$( this ).parents( metaBox ).find( '.uploaded-img-id' ).val( '' ).change();
				});
			});
			</script>
			<?php
			wp_add_inline_script( 'jquery', str_replace( array( '<script>', '</script>' ), '', ob_get_clean() ) );
		}
	}
}

/**
 * Register Widget
 */
function powerkit_widget_init_subscription_form() {
	register_widget( 'Powerkit_Subscription_Form_Widget' );
}
add_action( 'widgets_init', 'powerkit_widget_init_subscription_form' );

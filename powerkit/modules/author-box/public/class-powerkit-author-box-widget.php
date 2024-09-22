<?php
/**
 * Widget Author
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    Powerkit
 * @subpackage Powerkit/widgets
 */

/**
 * Widget Author
 */
class Powerkit_Widget_Author_Init extends WP_Widget {

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

		$this->default_settings = apply_filters( 'powerkit_widget_author_settings', array(
			'title'                => esc_html__( 'Author', 'powerkit' ),
			'author'               => 'current',
			'template'             => 'default',
			'bg_image_id'          => false,
			'avatar'               => true,
			'description'          => true,
			'description_override' => '',
			'description_length'   => 100,
			'social_accounts'      => true,
			'posts_only'           => false,
			'archive_btn'          => false,
		) );

		$widget_details = array(
			'classname'   => 'powerkit_widget_author',
			'description' => '',
		);

		// Actions.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		parent::__construct( 'powerkit_widget_author', esc_html__( 'Author', 'powerkit' ), $widget_details );
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

		$authors = array();

		// Get authors.
		if ( 'current' === $params['author'] || 'сurrent' === $params['author'] ) {
			if ( is_single() ) {
				$params['posts_only'] = true;

				$coauthors = array();

				if ( function_exists( 'get_coauthors' ) ) {
					$coauthors = get_coauthors();
				}

				if ( $coauthors ) {
					// Get co authors.
					foreach ( $coauthors as $author ) {
						$authors[] = $author->ID;
					}
				} else {
					// Get the default WP author.
					$authors[] = get_the_author_meta( 'ID' );
				}
			}
		} else {

			if ( get_the_author_meta( 'display_name', $params['author'] ) ) {

				$authors[] = $params['author'];

			} elseif ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) {
				?>
				<p class="pk-alert pk-alert-warning" role="alert">
					<?php esc_html_e( 'Author not found.', 'powerkit' ); ?>
				</p>
				<?php
			}
		}

		if ( empty( $authors ) ) {
			return;
		}

		foreach ( $authors as $author ) {
			// Display on posts only.
			if ( $params['posts_only'] ) {
				if ( is_single() ) {
					$post_id = get_queried_object_id();

					if ( ! powerkit_check_post_author( $author, $post_id ) ) {
						continue;
					}
				} else {
					continue;
				}
			}

			// Display author.
			if ( ! @ is_author( $author ) ) {
				powerkit_widget_author_template_handler( $params['template'], $author, $args, $params, $instance );
			}
		}
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

		// Display description.
		if ( ! isset( $instance['description'] ) ) {
			$instance['description'] = false;
		}

		// Display social accounts.
		if ( ! isset( $instance['social_accounts'] ) ) {
			$instance['social_accounts'] = false;
		}

		// Display on posts only.
		if ( ! isset( $instance['posts_only'] ) ) {
			$instance['posts_only'] = false;
		}

		// Display post archive button.
		if ( ! isset( $instance['archive_btn'] ) ) {
			$instance['archive_btn'] = false;
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

		$templates = apply_filters( 'powerkit_widget_author_templates', array() );
		?>
			<!-- Title -->
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'powerkit' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $params['title'] ); ?>" /></p>

			<!-- Template -->
			<?php if ( count( $templates ) > 1 ) { ?>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'template' ) ); ?>"><?php esc_html_e( 'Template:', 'powerkit' ); ?></label>
					<select name="<?php echo esc_attr( $this->get_field_name( 'template' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'template' ) ); ?>" class="widefat">
						<?php
						foreach ( $templates as $slug => $template ) {
							$name = isset( $template['name'] ) ? $template['name'] : $slug;
						?>
							<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $params['template'], $slug ); ?>><?php echo esc_html( $name ); ?></option>
						<?php } ?>
					</select>
				</p>
			<?php } ?>

			<!-- Author -->
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'author' ) ); ?>"><?php esc_html_e( 'Author:', 'powerkit' ); ?></label>
				<select name="<?php echo esc_attr( $this->get_field_name( 'author' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'author' ) ); ?>" class="widefat">
					<option value="current" <?php selected( $params['author'], 'current' ); ?>><?php esc_html_e( 'Current Post’s Author' ); ?></option>
					<?php
					$authors = powerkit_get_users();

					if ( isset( $authors ) && ! empty( $authors ) ) {
						foreach ( $authors as $author ) {
							?>
								<option value="<?php echo esc_attr( $author->ID ); ?>" <?php selected( $params['author'], $author->ID ); ?>><?php echo esc_html( $author->display_name ); ?></option>
							<?php
						}
					}
					?>
				</select>
			</p>

			<!-- Background Image container -->
			<div class="author-upload-image upload-img-container" data-frame-title="<?php esc_html_e( 'Select or upload background image', 'powerkit' ); ?>" data-frame-btn-text="<?php esc_html_e( 'Set background image', 'powerkit' ); ?>">
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

			<!-- Display avatar -->
			<p><input id="<?php echo esc_attr( $this->get_field_id( 'avatar' ) ); ?>" class="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'avatar' ) ); ?>" type="checkbox" <?php checked( (bool) $params['avatar'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'avatar' ) ); ?>"><?php esc_html_e( 'Display avatar', 'powerkit' ); ?></label></p>

			<!-- Display description -->
			<p><input id="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>" class="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'description' ) ); ?>" type="checkbox" <?php checked( (bool) $params['description'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>"><?php esc_html_e( 'Display description', 'powerkit' ); ?></label></p>

			<!-- Override Description -->
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'description_override' ) ); ?>"><?php esc_html_e( 'Override Description', 'powerkit' ); ?></label>
			<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'description_override' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'description_override' ) ); ?>" rows="10"><?php echo esc_textarea( $params['description_override'] ); ?></textarea></p>

			<!-- Description Length -->
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'description_length' ) ); ?>"><?php esc_html_e( 'Description Length:', 'powerkit' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'description_length' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'description_length' ) ); ?>" type="text" value="<?php echo esc_attr( $params['description_length'] ); ?>" /></p>

			<!-- Display social accounts -->
			<?php if ( powerkit_module_enabled( 'social_links' ) ) : ?>
				<p><input id="<?php echo esc_attr( $this->get_field_id( 'social_accounts' ) ); ?>" class="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'social_accounts' ) ); ?>" type="checkbox" <?php checked( (bool) $params['social_accounts'] ); ?> />
				<label for="<?php echo esc_attr( $this->get_field_id( 'social_accounts' ) ); ?>"><?php esc_html_e( 'Display social accounts', 'powerkit' ); ?></label></p>
			<?php endif; ?>

			<!-- Display post archive button -->
			<p><input id="<?php echo esc_attr( $this->get_field_id( 'archive_btn' ) ); ?>" class="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'archive_btn' ) ); ?>" type="checkbox" <?php checked( (bool) $params['archive_btn'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'archive_btn' ) ); ?>"><?php esc_html_e( 'Display post archive button', 'powerkit' ); ?></label></p>

			<!-- Display on posts only -->
			<p><input id="<?php echo esc_attr( $this->get_field_id( 'posts_only' ) ); ?>" class="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'posts_only' ) ); ?>" type="checkbox" <?php checked( (bool) $params['posts_only'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'posts_only' ) ); ?>"><?php esc_html_e( 'Display on posts of the author only', 'powerkit' ); ?></label></p>

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
				var metaBox = '.author-upload-image';

				/* Add Image Link */
				$(document).on( 'click', metaBox + ' .upload-img-link', function( event ){
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
				$( document ).on( 'click', metaBox + ' .delete-img-link', function( event ){
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
function powerkit_widget_author_init() {
	register_widget( 'Powerkit_Widget_Author_Init' );
}
add_action( 'widgets_init', 'powerkit_widget_author_init' );

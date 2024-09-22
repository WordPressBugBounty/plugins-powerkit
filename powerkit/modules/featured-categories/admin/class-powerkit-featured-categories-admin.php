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
class Powerkit_Featured_Categories_Admin extends Powerkit_Module_Admin {

	/**
	 * Initialize
	 */
	public function initialize() {
		add_action( 'category_add_form_fields', array( $this, 'category_add_form_fields' ), 10 );
		add_action( 'category_edit_form_fields', array( $this, 'category_edit_form_fields' ), 10, 2 );
		add_action( 'created_category', array( $this, 'save_category' ), 10, 2 );
		add_action( 'edited_category', array( $this, 'save_category' ), 10, 2 );
	}

	/**
	 * Add fields to Category
	 *
	 * @param string $taxonomy The taxonomy slug.
	 */
	public function category_add_form_fields( $taxonomy ) {
		wp_nonce_field( 'category_options', 'powerkit_category' );
		?>
			<div class="form-field">
				<label><?php esc_html_e( 'Featured Image', 'powerkit' ); ?></label>

				<div class="pk-featured-image" data-frame-title="<?php esc_html_e( 'Select or upload image', 'powerkit' ); ?>" data-frame-btn-text="<?php esc_html_e( 'Set image', 'powerkit' ); ?>">
					<p class="uploaded-img-box">
						<span class="uploaded-image"></span>
						<input id="powerkit_featured_image" class="uploaded-img-id" name="powerkit_featured_image" type="hidden"/>
					</p>
					<p class="hide-if-no-js">
						<a class="upload-img-link button button-primary" href="#"><?php esc_html_e( 'Upload image', 'powerkit' ); ?></a>
						<a class="delete-img-link button button-secondary hidden" href="#"><?php esc_html_e( 'Remove image', 'powerkit' ); ?></a>
					</p>
				</div>

				<p><?php esc_html_e( 'This image is used in the category blocks.', 'powerkit' ); ?></p>
			</div>
			<br>
		<?php
	}

	/**
	 * Edit fields from Category
	 *
	 * @param object $tag      Current taxonomy term object.
	 * @param string $taxonomy Current taxonomy slug.
	 */
	public function category_edit_form_fields( $tag, $taxonomy ) {
		wp_nonce_field( 'category_options', 'powerkit_category' );

		$powerkit_featured_image = get_term_meta( $tag->term_id, 'powerkit_featured_image', true );
		?>
		<tr class="form-field">
			<th scope="row" valign="top">
				<label for="powerkit_featured_image"><?php esc_html_e( 'Featured Image', 'powerkit' ); ?></label>
			</th>
			<td>
				<div class="pk-featured-image" data-frame-title="<?php esc_html_e( 'Select or upload image', 'powerkit' ); ?>" data-frame-btn-text="<?php esc_html_e( 'Set image', 'powerkit' ); ?>">
					<p class="uploaded-img-box">
						<span class="uploaded-image">
							<?php if ( $powerkit_featured_image ) : ?>
								<?php
									echo wp_get_attachment_image( $powerkit_featured_image, 'large', false, array(
										'style' => 'max-width:100%; height: auto;',
									) );
								?>
							<?php endif; ?>
						</span>

						<input id="powerkit_featured_image" class="uploaded-img-id" name="powerkit_featured_image" type="hidden" value="<?php echo esc_attr( $powerkit_featured_image ); ?>" />
					</p>
					<p class="hide-if-no-js">
						<a class="upload-img-link button button-primary <?php echo esc_attr( $powerkit_featured_image ? 'hidden' : '' ); ?>" href="#"><?php esc_html_e( 'Upload image', 'powerkit' ); ?></a>
						<a class="delete-img-link button button-secondary <?php echo esc_attr( ! $powerkit_featured_image ? 'hidden' : '' ); ?>" href="#"><?php esc_html_e( 'Remove image', 'powerkit' ); ?></a>
					</p>
				</div>

				<p class="description"><?php esc_html_e( 'This image is used in the category blocks.', 'powerkit' ); ?></p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save category fields
	 *
	 * @param int    $term_id  ID of the term about to be edited.
	 * @param string $taxonomy Taxonomy slug of the related term.
	 */
	public function save_category( $term_id, $taxonomy ) {

		// Bail if we're doing an auto save.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// if our nonce isn't there, or we can't verify it, bail.
		if ( ! isset( $_POST['powerkit_category'] ) || ! wp_verify_nonce( $_POST['powerkit_category'], 'category_options' ) ) { // Input var ok; sanitization ok.
			return;
		}

		if ( isset( $_POST['powerkit_featured_image'] ) ) { // Input var ok; sanitization ok.
			$powerkit_featured_image = sanitize_text_field( $_POST['powerkit_featured_image'] ); // Input var ok; sanitization ok.

			update_term_meta( $term_id, 'powerkit_featured_image', $powerkit_featured_image );
		}
	}

	/**
	 * Register the stylesheets and JavaScript for the admin area.
	 *
	 * @param string $page Current page.
	 */
	public function admin_enqueue_scripts( $page ) {
		if ( 'edit-tags.php' === $page || 'term.php' === $page ) {
			wp_enqueue_script( 'jquery' );

			wp_enqueue_media();

			ob_start();
			?>
			<script>
			( function() {

				var powerkitFeaturedContainer = '.pk-featured-image';

				var powerkitFeaturedFrame;


				jQuery( document ).ready( function( $ ) {

					/* Add Image Link */
					jQuery( powerkitFeaturedContainer ).find( '.upload-img-link' ).on( 'click', function( event ){
						event.preventDefault();

						var parentContainer = $( this ).parents( powerkitFeaturedContainer );

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
						powerkitFeaturedFrame = wp.media( options );

						// When an image is selected in the media frame...
						powerkitFeaturedFrame.on( 'select', function() {

							// Get media attachment details from the frame state.
							var attachment = powerkitFeaturedFrame.state().get('selection').first().toJSON();

							// Send the attachment URL to our custom image input field.
							parentContainer.find( '.uploaded-image' ).html( '<img src="' + attachment.url + '" style="max-width:100%;"/>' );
							parentContainer.find( '.uploaded-img-id' ).val( attachment.id ).change();
							parentContainer.find( '.upload-img-link' ).addClass( 'hidden' );
							parentContainer.find( '.delete-img-link' ).removeClass( 'hidden' );

							powerkitFeaturedFrame.close();
						});

						// Finally, open the modal on click.
						powerkitFeaturedFrame.open();
					});


					/* Delete Image Link */
					$( powerkitFeaturedContainer ).find( '.delete-img-link' ).on( 'click', function( event ){
						event.preventDefault();

						$( this ).parents( powerkitFeaturedContainer ).find( '.uploaded-image' ).html( '' );
						$( this ).parents( powerkitFeaturedContainer ).find( '.upload-img-link' ).removeClass( 'hidden' );
						$( this ).parents( powerkitFeaturedContainer ).find( '.delete-img-link' ).addClass( 'hidden' );
						$( this ).parents( powerkitFeaturedContainer ).find( '.uploaded-img-id' ).val( '' ).change();
					});
				});

				jQuery( document ).ajaxSuccess(function(e, request, settings){
					let action   = settings.data.indexOf( 'action=add-tag' );
					let screen   = settings.data.indexOf( 'screen=edit-category' );
					let taxonomy = settings.data.indexOf( 'taxonomy=category' );

					if( action > -1 && screen > -1 && taxonomy > -1 ){
						$( powerkitFeaturedContainer ).find( '.delete-img-link' ).click();
					}
				});

			} )();
			</script>
			<?php
			wp_add_inline_script( 'jquery', str_replace( array( '<script>', '</script>' ), '', ob_get_clean() ) );
		}
	}
}

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
class Powerkit_Post_Format_UI_Admin extends Powerkit_Module_Admin {

	/**
	 * Initialize
	 */
	public function initialize() {
		add_action( 'add_meta_boxes', array( $this, 'add_custom_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );
		add_action( 'wp_ajax_pk_media_oembed', array( $this, 'media_oembed' ) );
		add_action( 'wp_ajax_pk_gallery_attachment', array( $this, 'gallery_attachment' ) );
		add_action( 'wp_ajax_pk_gallery_update', array( $this, 'gallery_update' ) );
	}

	/**
	 * Add custom meta boxes.
	 */
	public function add_custom_meta_boxes() {
		add_meta_box( 'powerkit-post-format-link', esc_html__( 'Link', 'powerkit' ), array( $this, 'link_markup' ), 'post', 'normal', 'high', null );
		add_meta_box( 'powerkit-post-format-audio', esc_html__( 'Audio', 'powerkit' ), array( $this, 'audio_markup' ), 'post', 'normal', 'high', null );
		add_meta_box( 'powerkit-post-format-video', esc_html__( 'Video', 'powerkit' ), array( $this, 'video_markup' ), 'post', 'normal', 'high', null );
		add_meta_box( 'powerkit-post-format-gallery', esc_html__( 'Gallery', 'powerkit' ), array( $this, 'gallery_markup' ), 'post', 'normal', 'high', null );
	}

	/**
	 * Link markup.
	 */
	public function link_markup() {
		$link = get_post_meta( get_the_ID(), 'powerkit_post_format_link', true );
		?>
		<div class="pk-post-format-media pk-post-format-link">
			<div class="title-search">
				<input class="search-input" name="pk-post-format-link" type="text" placeholder="<?php esc_html_e( 'Enter URL', 'powerkit' ); ?>" value="<?php echo esc_attr( $link ); ?>">
			</div>
		</div>
		<?php wp_nonce_field( 'pk-post-format', 'pk-post-format' ); ?>
		<?php
	}

	/**
	 * Audio markup.
	 */
	public function audio_markup() {
		$this->media_markup( 'audio' );
	}

	/**
	 * Video markup.
	 */
	public function video_markup() {
		$this->media_markup( 'video' );
	}

	/**
	 * Media markup.
	 *
	 * @param string $markup The type markup.
	 */
	public function media_markup( $markup ) {
		$url = get_post_meta( get_the_ID(), 'powerkit_post_format_' . $markup, true );
		?>
		<div class="pk-post-format-media pk-post-format-<?php echo esc_attr( $markup ); ?>">
			<div class="title-search">
				<input class="search-input" name="pk-post-format-<?php echo esc_attr( $markup ); ?>" type="text" placeholder="<?php esc_html_e( 'Enter URL', 'powerkit' ); ?>" value="<?php echo esc_attr( $url ); ?>">
			</div>
			<div class="canvas"><?php echo $this->wp_oembed_get( $url ); // XSS. ?></div>
		</div>
		<?php wp_nonce_field( 'pk-post-format', 'pk-post-format' ); ?>
		<?php
	}

	/**
	 * Ajax get oembed.
	 */
	public function media_oembed() {
		check_ajax_referer( 'nonce', 'nonce' );

		if ( isset( $_POST['url'] ) ) { // Input var ok; sanitization ok.
			$url = $_POST['url']; // Input var ok; sanitization ok.
		}

		if ( ! isset( $url ) ) {
			return;
		}

		echo $this->wp_oembed_get( $url ); // XSS.

		die();
	}

	/**
	 * Get oembed.
	 *
	 * @param string $url    The url media.
	 * @param int    $width  The width media.
	 * @param int    $height The height media.
	 */
	public function wp_oembed_get( $url = '', $width = 640, $height = 390 ) {

		$embed = @wp_oembed_get( $url, array(
			'width'  => $width,
			'height' => $height,
		) );

		return $embed;
	}

	/**
	 * Gallery markup.
	 */
	public function gallery_markup() {
		$attachments = get_post_meta( get_the_ID(), 'powerkit_post_format_gallery', true );
		?>
		<div class="pk-post-format-gallery">
			<div class="pk-post-format-gallery-main">
				<div class="pk-post-format-gallery-attachments">
					<?php
					if ( $attachments ) {
						foreach ( $attachments as $attachment ) {
							$attachment_url = wp_get_attachment_image_url( $attachment, 'thumbnail' );
							?>
								<div class="pk-post-format-gallery-attachment" data-id="<?php echo esc_attr( $attachment ); ?>">
									<input name="pk-post-format-gallery[]" type="hidden" value="<?php echo esc_attr( $attachment ); ?>">
									<div class="thumbnail"><img src="<?php echo esc_attr( $attachment_url ); ?>" alt="thumbnail"></div>
									<div class="actions">
										<a href="#" class="pk-post-format-gallery-remove" data-id="<?php echo esc_attr( $attachment ); ?>"></a>
									</div>
								</div>
							<?php
						}
					}
					?>
				</div>
				<div class="pk-post-format-gallery-toolbar">
					<a href="#" class="button button-primary pk-post-format-gallery-add"><?php esc_html_e( 'Add to gallery', 'powerkit' ); ?></a>
				</div>
			</div>
			<div class="pk-post-format-gallery-side">
				<div class="pk-post-format-gallery-side-wrap">
					<div class="pk-post-format-gallery-side-inner">
						<div class="pk-post-format-gallery-side-data">
							<span class="spinner"></span>
						</div>
						<div class="pk-post-format-gallery-side-toolbar">
							<a href="#" class="button pk-post-format-gallery-close"><?php esc_html_e( 'Close', 'powerkit' ); ?></a>

							<span class="spinner"></span>
							<a href="#" class="button button-primary pk-post-format-gallery-update"><?php esc_html_e( 'Update', 'powerkit' ); ?></a>
						</div>
					</div>
				</div>
			</div>
			<?php $ids = $attachments ? implode( ',', $attachments ) : null; ?>
			<input class="pk-post-format-gallery-settings" type="hidden" value="<?php echo esc_attr( $ids ); ?>" data-title="<?php esc_html_e( 'Add Image', 'powerkit' ); ?>" data-button="<?php esc_html_e( 'Select', 'powerkit' ); ?>" disabled>
		</div>
		<?php wp_nonce_field( 'pk-post-format', 'pk-post-format' ); ?>
		<?php
	}

	/**
	 * Gallery attachment info.
	 */
	public function gallery_attachment() {
		check_ajax_referer( 'nonce', 'nonce' );

		if ( isset( $_POST['id'] ) ) { // Input var ok; sanitization ok.
			$attachment_id = (int) $_POST['id']; // Input var ok; sanitization ok.
		}

		if ( ! isset( $attachment_id ) ) {
			return;
		}

		// Vars.
		$attachment = wp_prepare_attachment_for_js( $attachment_id );
		$compat     = get_compat_media_markup( $attachment_id );
		$thumb      = null;
		$dimentions = null;

		// Thumb.
		if ( 'image' === $attachment['type'] ) {
			$thumb = $attachment['url'];
		} else {
			$thumb = wp_mime_type_icon();
		}

		// Dimentions.
		if ( ! empty( $attachment['width'] ) ) {
			$dimentions = $attachment['width'] . ' x ' . $attachment['height'];
		}
		if ( ! empty( $attachment['filesizeHumanReadable'] ) ) {
			$dimentions .= sprintf( ' (%s)', $attachment['filesizeHumanReadable'] );
		}
		?>

				<div class="pk-post-format-gallery-side-info">
					<img src="<?php echo esc_attr( $thumb ); ?>" alt="<?php echo esc_attr( $attachment['alt'] ); ?>" />
					<p class="filename"><strong><?php echo esc_html( $attachment['filename'] ); ?></strong></p>
					<p class="uploaded"><?php echo esc_html( $attachment['dateFormatted'] ); ?></p>
					<p class="dimensions"><?php echo esc_html( $dimentions ); ?></p>
				</div>
				<div class="clear"></div>
				<table class="form-table">
					<tbody>
					<tr data-name="title" data-type="text">
						<td class="label"><label for="pk-attachments-title"><?php esc_html_e( 'Title', 'powerkit' ); ?></label></td>
						<td class="input"><input type="text" id="pk-attachments-title" name="title" value="<?php echo esc_html( $attachment['title'] ); ?>"></td>
					</tr>
					<tr data-name="caption" data-type="textarea">
						<td class="label"><label for="pk-attachments-caption"><?php esc_html_e( 'Caption', 'powerkit' ); ?></label></td>
						<td class="input"><textarea id="pk-attachments-caption" name="caption"><?php echo wp_kses( $attachment['caption'], 'post' ); ?></textarea></td>
					</tr>
					<tr data-name="alt" data-type="text">
						<td class="label"><label for="pk-attachments-alt"><?php esc_html_e( 'Alt Text', 'powerkit' ); ?></label></td>
						<td class="input"><input type="text" id="pk-attachments-alt" name="alt" value="<?php echo esc_html( $attachment['alt'] ); ?>"></td>
					</tr>
					<tr data-name="description" data-type="textarea">
						<td class="label"><label for="pk-attachments-description"><?php esc_html_e( 'Description', 'powerkit' ); ?></label></td>
						<td class="input"><textarea id="pk-attachments-description" name="description"><?php echo wp_kses( $attachment['description'], 'post' ); ?></textarea></td>
					</tr>
					</tbody>
				</table>
				<input type="hidden" name="id" value="<?php echo esc_html( $attachment_id ); ?>">
				<input type="hidden" name="action" value="pk_gallery_update">
				<?php wp_nonce_field( 'nonce', 'nonce' ); ?>
		<?php

		die();
	}

	/**
	 * Gallery attachment update.
	 */
	public function gallery_update() {
		check_ajax_referer( 'nonce', 'nonce' );

		$changes = null;

		if ( isset( $_POST['id'] ) ) { // Input var ok; sanitization ok.
			$attachment_id = (int) sanitize_key( $_POST['id'] ); // Input var ok; sanitization ok.
		}

		if ( isset( $_POST['title'] ) ) {
			$changes['title'] = sanitize_text_field( $_POST['title'] ); // Input var ok; sanitization ok.
		}
		if ( isset( $_POST['caption'] ) ) {
			$changes['caption'] = sanitize_textarea_field( $_POST['caption'] ); // Input var ok; sanitization ok.
		}
		if ( isset( $_POST['description'] ) ) {
			$changes['description'] = sanitize_textarea_field( $_POST['description'] ); // Input var ok; sanitization ok.
		}
		if ( isset( $_POST['alt'] ) ) {
			$changes['alt'] = sanitize_text_field( $_POST['alt'] ); // Input var ok; sanitization ok.
		}

		if ( ! isset( $attachment_id ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $attachment_id ) ) {
			wp_send_json_error();
		}

		$post = get_post( $attachment_id, ARRAY_A );

		if ( 'attachment' !== $post['post_type'] ) {
			wp_send_json_error();
		}

		if ( isset( $changes['title'] ) ) {
			$post['post_title'] = $changes['title'];
		}

		if ( isset( $changes['caption'] ) ) {
			$post['post_excerpt'] = $changes['caption'];
		}

		if ( isset( $changes['description'] ) ) {
			$post['post_content'] = $changes['description'];
		}

		if ( isset( $changes['alt'] ) ) {
			$alt = wp_unslash( $changes['alt'] );

			$post_meta = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );

			if ( $alt !== $post_meta ) {
				$alt = wp_strip_all_tags( $alt, true );
				update_post_meta( $attachment_id, '_wp_attachment_image_alt', wp_slash( $alt ) );
			}
		}

		// Save post.
		wp_update_post( $post );

		/** This filter is documented in wp-admin/includes/media.php */
		// - seems off to run this filter AFTER the update_post function, but there is a reason
		// - when placed BEFORE, an empty post_title will be populated by WP
		// - this filter will still allow 3rd party to save extra image data!
		$post = apply_filters( 'attachment_fields_to_save', $post, $changes );

		wp_send_json_success();
	}

	/**
	 * Save meta box
	 *
	 * @param int $post_id The post id.
	 */
	public function save_post( $post_id ) {

		// Bail if we're doing an auto save.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// if our nonce isn't there, or we can't verify it, bail.
		if ( ! isset( $_POST['pk-post-format'] ) || ! wp_verify_nonce( $_POST['pk-post-format'], 'pk-post-format' ) ) { // Input var ok; sanitization ok.
			return;
		}

		if ( isset( $_POST['pk-post-format-link'] ) ) { // Input var ok; sanitization ok.
			update_post_meta( $post_id, 'powerkit_post_format_link', esc_url_raw( $_POST['pk-post-format-link'] ) ); // Input var ok; sanitization ok.
		} else {
			delete_post_meta( $post_id, 'powerkit_post_format_link' );
		}
		if ( isset( $_POST['pk-post-format-audio'] ) ) { // Input var ok; sanitization ok.
			update_post_meta( $post_id, 'powerkit_post_format_audio', esc_url_raw( $_POST['pk-post-format-audio'] ) ); // Input var ok; sanitization ok.
		} else {
			delete_post_meta( $post_id, 'powerkit_post_format_audio' );
		}
		if ( isset( $_POST['pk-post-format-video'] ) ) { // Input var ok; sanitization ok.
			update_post_meta( $post_id, 'powerkit_post_format_video', esc_url_raw( $_POST['pk-post-format-video'] ) ); // Input var ok; sanitization ok.
		} else {
			delete_post_meta( $post_id, 'powerkit_post_format_video' );
		}
		if ( isset( $_POST['pk-post-format-gallery'] ) ) { // Input var ok; sanitization ok.
			update_post_meta( $post_id, 'powerkit_post_format_gallery', array_map( 'sanitize_key', $_POST['pk-post-format-gallery'] ) ); // Input var ok; sanitization ok.
		} else {
			delete_post_meta( $post_id, 'powerkit_post_format_gallery' );
		}
	}

	/**
	 * Register the stylesheets and JavaScript for the admin area.
	 *
	 * @param string $page Current page.
	 */
	public function admin_enqueue_scripts( $page ) {
		if ( 'post.php' === $page || 'post-new.php' === $page ) {

			wp_enqueue_media();
			wp_enqueue_script( 'jquery-ui-sortable' );

			// Styles.
			wp_enqueue_style( 'powerkit-post-format-ui', powerkit_style( plugin_dir_url( __FILE__ ) . 'css/admin-powerkit-post-format-ui.css' ), array(), powerkit_get_setting( 'version' ), 'all' );

			wp_add_inline_style( 'powerkit-post-format-ui', sprintf( '#powerkit-post-format-%s { display: block; }', get_post_format() ) );

			// Scripts.
			wp_enqueue_script( 'powerkit-post-format-ui', plugin_dir_url( __FILE__ ) . 'js/admin-powerkit-post-format-ui.js', array( 'jquery' ), powerkit_get_setting( 'version' ), false );

			wp_localize_script( 'powerkit-post-format-ui', 'powerkit_post_format_ui',
				array(
					'url'   => admin_url( 'admin-ajax.php' ),
					'nonce' => wp_create_nonce( 'nonce' ),
				)
			);
		}
	}
}

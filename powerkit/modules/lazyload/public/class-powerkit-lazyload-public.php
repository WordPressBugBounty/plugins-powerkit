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
class Powerkit_Lazyload_Public extends Powerkit_Module_Public {

	/**
	 * The placeholder default.
	 *
	 * @var string $name The placeholder default.
	 */
	public $placeholder;

	/**
	 * The lqip size.
	 *
	 * @var string $name The lqip size.
	 */
	public $lqip_size;

	/**
	 * Initialize
	 */
	public function initialize() {

		// Generation placeholder default.
		$placeholder_image = powerkit_lazy_get_image_placeholder( 1, 1, true );

		// Set placeholder default.
		$this->placeholder = apply_filters( 'powerkit_lazyload_placeholder', $placeholder_image );

		// Set lqip size.
		$this->lqip_size = apply_filters( 'powerkit_lazyload_lqip_size', 80 );

		// Define the filters of the module.
		add_filter( 'init', array( $this, 'add_lqip_sizes' ) );
		add_filter( 'init', array( $this, 'allow_lazy_attributes' ) );
		add_filter( 'kses_allowed_protocols', array( $this, 'allow_lazy_protocols' ) );
		add_filter( 'the_content', array( $this, 'content_process_images' ), 200, 1 );
		add_filter( 'get_avatar', array( $this, 'content_process_images' ), 200, 1 );
		add_filter( 'powerkit_lazy_process_images', array( $this, 'content_process_images' ), 200, 1 );
		add_filter( 'wp_update_attachment_metadata', array( $this, 'generate_attachment_placeholder' ) );
		add_filter( 'wp_generate_attachment_metadata', array( $this, 'generate_attachment_placeholder' ) );
		add_filter( 'wp_get_attachment_image_attributes', array( $this, 'add_image_placeholders' ), 10, 3 );
		add_filter( 'powerkit_lazyload_instagram_output', array( $this, 'instagram_placeholder' ) );
	}

	/**
	 * This conditional tag checks if Lazy Load allowed.
	 *
	 * @param array $attr Attributes for the image markup.
	 */
	public function is_enabled( $attr = array() ) {
		if ( is_admin() || is_preview() || is_embed() ) {
			return false;
		}

		// Check AMP endpoint.
		if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
			return false;
		}

		// Check feed.
		if ( is_feed() ) {
			return false;
		}

		// Check printpage.
		if ( get_query_var( 'print' ) || get_query_var( 'printpage' ) ) {
			return false;
		}

		// Is filter disabled ?
		if ( apply_filters( 'powerkit_lazyload_is_disabled', false, $attr ) ) {
			return false;
		}

		// Is image disabled ?
		if ( isset( $attr['class'] ) && preg_match( '/pk-lazyload-disabled/', $attr['class'] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * This conditional tag checks if lqip enabled.
	 *
	 * @param array $attr Attributes for the image markup.
	 */
	public function is_lqip_enabled( $attr = array() ) {
		// Is image unstyled ?
		if ( isset( $attr['class'] ) && preg_match( '/pk-lazyload-unstyled/', $attr['class'] ) ) {
			return false;
		}

		return get_option( 'powerkit_lazyload_csco_lqip', false );
	}

	/**
	 * Allow attributes of Lazy Load for wp_kses.
	 */
	public function allow_lazy_attributes() {
		global $allowedposttags;

		if ( $allowedposttags ) {
			foreach ( $allowedposttags as $key => & $tags ) {
				if ( 'img' === $key ) {
					$tags['data-pk-src']    = true;
					$tags['data-pk-sizes']  = true;
					$tags['data-pk-srcset'] = true;
				}
			}
		}
	}

	/**
	 * Allow protocols of Lazy Load.
	 *
	 * @param array $protocols Array of allowed protocols e.g.
	 */
	public function allow_lazy_protocols( $protocols ) {
		$protocols[] = 'data';

		return $protocols;
	}

	/**
	 * The generated attachment meta data.
	 *
	 * @param array $metadata An array of attachment meta data.
	 */
	public function generate_attachment_placeholder( $metadata ) {

		$placeholder_unique = apply_filters( 'powerkit_lazyload_placeholder_unique', true );

		if ( $placeholder_unique ) {
			// Generate image full size.
			if ( isset( $metadata['width'] ) && isset( $metadata['height'] ) ) {
				$metadata['placeholder'] = powerkit_lazy_get_image_placeholder( $metadata['width'], $metadata['height'] );
			}

			// Generate image sizes.
			if ( isset( $metadata['sizes'] ) ) {
				foreach ( $metadata['sizes'] as $slug => & $size ) {
					// Ignore lqip size.
					if ( preg_match( '/pk-lqip/', $slug ) ) {
						continue;
					}
					// Ignore retina size.
					if ( preg_match( '/-2x$/', $slug ) ) {
						continue;
					}
					if ( isset( $size['width'] ) && isset( $size['height'] ) ) {
						$size['placeholder'] = powerkit_lazy_get_image_placeholder( $size['width'], $size['height'] );
					}
				}
			}
		}

		return $metadata;
	}

	/**
	 * Processing images in the content.
	 *
	 * @param string $content Text with Images.
	 */
	public function content_process_images( $content ) {

		// Get all images.
		preg_match_all( '/<img\s+.*?>/', $content, $matches );

		$images = array_shift( $matches );

		// Check exists images.
		if ( ! $images ) {
			return $content;
		}

		foreach ( $images as $image ) {

			// Ignore init lazyload.
			if ( preg_match( '/pk-lazyload/', $image ) ) {
				continue;
			}

			// Get Attributes for the image markup.
			if ( preg_match_all( '/\s(.*?)="(.*?)"/', $image, $matches ) ) {

				$attr_data = array_shift( $matches );

				// Get attr list of image.
				$attr = array();

				foreach ( $attr_data as $key => $fulldata ) {
					$name  = $matches[0][ $key ];
					$value = $matches[1][ $key ];

					$attr[ $name ] = $value;
				}

				/**
				 * Process image.
				 * --------------------------------
				 */
				$attachment = powerkit_lazy_attachment_attr_to_object( $attr );
				$size       = powerkit_attachment_attr_to_size( $attr, $content );
				$attr       = $this->add_image_placeholders( $attr, $attachment, $size );

				// Variables for new image.
				$new_image = '<img [attr]>';
				$new_attr  = null;

				// Build new attributes.
				foreach ( $attr as $key => $value ) {
					$new_attr .= sprintf( ' %s="%s" ', $key, $value );
				}

				// Create new image based on new attributes.
				$new_image = str_replace( '[attr]', $new_attr, $new_image );

				// Update content.
				$content = str_replace( $image, $new_image, $content );
			}
		}

		return $content;
	}

	/**
	 * Add placeholder Lazy Load for images.
	 *
	 * @param array        $attr       Attributes for the image markup.
	 * @param WP_Post      $attachment Image attachment post.
	 * @param string|array $size       Requested size. Image size or array of width and height values
	 *                                 (in that order). Default 'thumbnail'.
	 */
	public function add_image_placeholders( $attr, $attachment, $size ) {

		// Is enabled.
		if ( ! $this->is_enabled( $attr ) ) {
			return $attr;
		}

		// Init class of image.
		if ( ! isset( $attr['class'] ) ) {
			$attr['class'] = null;
		}

		// Init src of image.
		if ( ! isset( $attr['src'] ) ) {
			$attr['src'] = null;
		}

		// Default Placeholder.
		$placeholder = $this->placeholder;

		// Is string.
		if ( is_string( $size ) ) {

			// Get attachment id.
			if ( isset( $attachment->ID ) ) {
				$attachment_id = $attachment->ID;

			} elseif ( isset( $attachment['ID'] ) ) {

				$attachment_id = $attachment['ID'];

			} else {
				$attachment_id = null;
			}

			// The right Image Placeholder.
			$metadata = get_post_meta( $attachment_id, '_wp_attachment_metadata', true );

			if ( isset( $metadata['sizes'][ $size ]['placeholder'] ) ) {

				$placeholder = $metadata['sizes'][ $size ]['placeholder'];

			} elseif ( isset( $metadata['placeholder'] ) ) {

				$placeholder = $metadata['placeholder'];
			}

			// Low Quality Image Placeholder.
			if ( $this->is_lqip_enabled( $attr ) ) {

				$lqip = $this->get_lqip_slug( $size );

				if ( powerkit_lazy_get_image_size( $lqip ) ) {
					$placeholder_image = wp_get_attachment_image_url( $attachment_id, $lqip );

					// Check lqip image exists.
					if ( preg_match( '/-\d*x\d*\.\w*$/', $placeholder_image ) ) {
						$placeholder = $placeholder_image;

						// Add lqip class.
						$attr['class'] .= ' pk-lqip';
					}
				}
			}
		}

		// Lazy Sizes.
		$attr['class'] .= ' pk-lazyload';

		// Set data-pk-sizes.
		if ( ! isset( $attr['data-pk-sizes'] ) ) {
			$attr['data-pk-sizes'] = 'auto';
		}

		if ( isset( $attr['sizes'] ) ) {
			$attr['data-ls-sizes'] = $attr['sizes'];

			unset( $attr['sizes'] );
		}

		// Set data-pk-src.
		if ( ! isset( $attr['data-pk-src'] ) ) {
			$attr['data-pk-src'] = $attr['src'];
		}

		// Set data-pk-srcset and unset sizes / srcset.
		if ( isset( $attr['srcset'] ) ) {
			$attr['data-pk-srcset'] = $attr['srcset'];

			unset( $attr['srcset'] );
		}

		// Set placeholder.
		$attr['src'] = $placeholder;

		return $attr;
	}

	/**
	 * Add lqip sizes.
	 */
	public function add_lqip_sizes() {
		if ( ! $this->is_lqip_enabled() ) {
			return;
		}

		$sizes = powerkit_lazy_get_available_image_sizes();

		// Add full lqip size.
		add_image_size( 'pk-lqip-full', $this->lqip_size, 9999 );

		if ( $sizes ) {
			foreach ( $sizes as $size => $data ) {
				$divider = $data['width'] / $data['height'];

				// Add new lqip size.
				add_image_size( $this->get_lqip_slug( $size ), $this->lqip_size, intval( $this->lqip_size / $divider ), $data['crop'] );
			}
		}
	}

	/**
	 * Get lqip slug.
	 *
	 * @param array $size Registered size or full size.
	 */
	public function get_lqip_slug( $size ) {
		$lqip_slug = 'pk-lqip-full';

		$data = powerkit_lazy_get_image_size( $size );

		if ( isset( $data['width'] ) && isset( $data['height'] ) ) {
			$crop = null;

			if ( isset( $data['crop'] ) ) {
				// Set crop if val array.
				if ( is_array( $data['crop'] ) ) {
					$crop = '-' . implode( '-', $data['crop'] );
				}
				// Set crop if val exist.
				if ( is_bool( $data['crop'] ) && $data['crop'] ) {
					$crop = '-crop';
				}
			}

			// Set divider.
			$divider = $data['width'] / $data['height'];

			$lqip_slug = sprintf( 'pk-lqip-%s%s', round( $divider, 2 ), $crop );
		}

		return $lqip_slug;
	}

	/**
	 * Set instagram placeholder.
	 */
	public function instagram_placeholder() {
		// Is enabled.
		if ( ! $this->is_enabled() ) {
			return;
		}

		return $this->placeholder;
	}

	/**
	 * Maybe enqueue lazyload scripts.
	 *
	 * @param boolean $force force enqueue without check `is_enabled`.
	 */
	public function maybe_enqueue_scripts( $force = false ) {
		if ( $force || $this->is_enabled() ) {
			wp_enqueue_script( 'lazysizes.config', plugin_dir_url( __FILE__ ) . 'js/lazysizes.config.js', array( 'jquery' ), false, true );
			wp_enqueue_script( 'lazysizes', plugin_dir_url( __FILE__ ) . 'js/lazysizes.min.js', array( 'jquery' ), false, true );

			wp_enqueue_style( 'powerkit-lazyload', powerkit_style( plugin_dir_url( __FILE__ ) . 'css/public-powerkit-lazyload.css' ), array(), powerkit_get_setting( 'version' ), 'all' );
		}
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function wp_enqueue_scripts() {
		$this->maybe_enqueue_scripts();
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function enqueue_block_editor_assets() {
		$this->maybe_enqueue_scripts( true );
	}
}

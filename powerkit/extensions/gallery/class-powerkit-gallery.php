<?php
/**
 * Gallery
 *
 * @package    Powerkit
 * @subpackage Extensions
 */

if ( class_exists( 'Powerkit_Module' ) ) {
	/**
	 * Init module
	 */
	class Powerkit_Gallery extends Powerkit_Module {

		/**
		 * Register module
		 */
		public function register() {
			$this->name     = 'gallery';
			$this->slug     = 'gallery';
			$this->type     = 'extension';
			$this->category = 'basic';
			$this->public   = false;
			$this->enabled  = false;
		}

		/**
		 * Initialize module
		 */
		public function initialize() {
			add_action( 'admin_init', array( $this, 'gallery_init' ) );
			add_action( 'post_gallery', array( $this, 'change_post_gallery' ), 10, 3 );
			add_filter( 'jetpack_gallery_types', array( $this, 'jetpack_gallery_types' ) );

			// Force Jetpack compatibility with our galleries.
			add_filter( 'jp_carousel_force_enable', '__return_true' );
		}

		/**
		 * Gallery Shortcode Function
		 *
		 * @param string $output   The current output.
		 * @param array  $attr     Attributes from the gallery shortcode.
		 * @param int    $instance Numeric ID of the gallery shortcode instance.
		 */
		public function change_post_gallery( $output, $attr, $instance ) {

			// Make sure we're overwriting only galleries with either layout or type attributes.
			if ( ! ( isset( $attr['layout'] ) || isset( $attr['type'] ) ) ) {
				return;
			}

			// Support for the deprecated layout attribute.
			if ( isset( $attr['layout'] ) ) {
				if ( 'grid' === $attr['layout'] ) {
					$attr['type'] = 'default';
				} else {
					$attr['type'] = $attr['layout'];
				}
			}

			if ( ! isset( $attr['type'] ) ) {
				return;
			}

			// Return if type is neither justified nor slider.
			$gallery_types = apply_filters( 'powerkit_gallery_types', array() );

			if ( ! array_key_exists( $attr['type'], (array) $gallery_types ) ) {
				return '';
			}

			global $post;

			$atts = shortcode_atts(
				array(
					'order'      => 'ASC',
					'orderby'    => 'menu_order ID',
					'id'         => $post ? $post->ID : 0,
					'itemtag'    => 'figure',
					'icontag'    => 'div',
					'captiontag' => 'figcaption',
					'columns'    => 3,
					'type'       => 'default',
					'size'       => 'thumbnail',
					'include'    => '',
					'exclude'    => '',
					'link'       => '',
				), $attr, 'gallery'
			);

			$settings = array(
				'custom_class' => '',
				'custom_attrs' => '',
			);

			$settings = apply_filters( 'powerkit_gallery_settings', $settings, $attr );

			$id = intval( $atts['id'] );

			if ( ! empty( $atts['include'] ) ) {
				$_attachments = get_posts(
					array(
						'include'        => $atts['include'],
						'post_status'    => 'inherit',
						'post_type'      => 'attachment',
						'post_mime_type' => 'image',
						'order'          => $atts['order'],
						'orderby'        => $atts['orderby'],
					)
				);

				$attachments = array();
				foreach ( $_attachments as $key => $val ) {
					$attachments[ $val->ID ] = $_attachments[ $key ];
				}
			} elseif ( ! empty( $atts['exclude'] ) ) {
				$attachments = get_children(
					array(
						'post_parent'    => $id,
						'exclude'        => $atts['exclude'],
						'post_status'    => 'inherit',
						'post_type'      => 'attachment',
						'post_mime_type' => 'image',
						'order'          => $atts['order'],
						'orderby'        => $atts['orderby'],
					)
				);
			} else {
				$attachments = get_children(
					array(
						'post_parent'    => $id,
						'post_status'    => 'inherit',
						'post_type'      => 'attachment',
						'post_mime_type' => 'image',
						'order'          => $atts['order'],
						'orderby'        => $atts['orderby'],
					)
				);
			}

			if ( empty( $attachments ) ) {
				return '';
			}

			if ( is_feed() ) {
				$output = "\n";
				foreach ( $attachments as $att_id => $attachment ) {
					$output .= wp_get_attachment_link( $att_id, $atts['size'], true ) . "\n";
				}
				return $output;
			}

			$itemtag      = tag_escape( $atts['itemtag'] );
			$captiontag   = tag_escape( $atts['captiontag'] );
			$custom_class = $settings['custom_class'];
			$custom_attrs = $settings['custom_attrs'];

			if ( 'justified' === $atts['type'] ) {
				$captiontag = 'div';
			}

			$type  = esc_attr( $atts['type'] );
			$float = is_rtl() ? 'right' : 'left';

			$selector = "gallery-{$instance}";

			$size_class  = sanitize_html_class( $atts['size'] );
			$gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-type-{$type} gallery-size-{$size_class} {$custom_class}' {$custom_attrs}>";

			$output = apply_filters( 'powerkit_gallery_style', $gallery_div );

			$i = 0;
			foreach ( $attachments as $id => $attachment ) {

				$attr = ( trim( $attachment->post_excerpt ) ) ? array(
					'aria-describedby' => "$selector-$id",
				) : array();

				// Add gallery type attribute.
				$attr['data-gallery'] = $atts['type'];

				if ( ! empty( $atts['link'] ) && 'file' === $atts['link'] ) {
					$image_output = wp_get_attachment_link( $id, $atts['size'], false, false, false, $attr );
				} elseif ( ! empty( $atts['link'] ) && 'none' === $atts['link'] ) {
					$image_output = wp_get_attachment_image( $id, $atts['size'], false, $attr );
				} else {
					$image_output = wp_get_attachment_link( $id, $atts['size'], true, false, false, $attr );
				}
				$image_meta = wp_get_attachment_metadata( $id );

				$output .= "<{$itemtag} class='gallery-item'>";
				$output .= $image_output;
				if ( $captiontag && trim( $attachment->post_excerpt ) ) {
					$output .= "
						<{$captiontag} class='caption wp-caption-text gallery-caption' id='$selector-$id'>
						" . wptexturize( $attachment->post_excerpt ) . "
						</{$captiontag}>";
				}
				$output .= "</{$itemtag}>";
			}

			$output .= '</div>';

			return $output;
		}

		/**
		 * Add new gallery types if Jetpack is not installed
		 */
		public function gallery_init() {
			add_action( 'print_media_templates', array( $this, 'print_media_templates' ) );
		}

		/**
		 * Add new gallery types to Jetpack if it's installed
		 *
		 * @param array $types Existing Jetpack gallery types.
		 */
		public function jetpack_gallery_types( $types ) {

			$gallery_types = apply_filters(
				'powerkit_gallery_types', array(
					'default' => esc_html__( 'Default', 'powerkit' ),
				)
			);

			$types = array_merge( $types, $gallery_types );

			return $types;
		}

		/**
		 * Add Gallery Type Dropdown
		 */
		public function print_media_templates() {

			// Return if Jetpack's Tiled Gallery.
			if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'tiled-gallery' ) ) {
				return '';
			}

			$gallery_types = apply_filters(
				'powerkit_gallery_types', array(
					'default' => esc_html__( 'Default', 'powerkit' ),
				)
			);

			$default_gallery_type = apply_filters( 'powerkit_default_gallery_type', 'default' );
			?>

			<script type="text/html" id="tmpl-pk-gallery-settings">
				<label class="setting">
					<span><?php esc_html_e( 'Type', 'powerkit' ); ?></span>
					<select class="type" name="type" data-setting="type">
						<?php foreach ( $gallery_types as $value => $caption ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $caption ); ?></option>
						<?php endforeach; ?>
					</select>
				</label>
			</script>

			<script>
				jQuery( document ).ready( function() {
					_.extend( wp.media.gallery.defaults, {
						type: '<?php echo esc_attr( $default_gallery_type ); ?>'
					} );

					// join default gallery settings template with yours -- store in list
					if ( !wp.media.gallery.templates ) wp.media.gallery.templates = [ 'gallery-settings' ];
					wp.media.gallery.templates.push( 'pk-gallery-settings' );

					// loop through list -- allowing for other templates/settings
					wp.media.view.Settings.Gallery = wp.media.view.Settings.Gallery.extend( {
						template: function( view ) {
							var output = '';
							for ( var i in wp.media.gallery.templates ) {
								output += wp.media.template( wp.media.gallery.templates[ i ] )( view );
							}
							return output;
						},
						render: function() {
							var $el = this.$el;

							wp.media.view.Settings.prototype.render.apply( this, arguments );

							// Hide the Columns setting for all types except Default
							$el.find( 'select[name=type]' ).on( 'change', function () {
								var columnSetting = $el.find( 'select[name=columns]' ).closest( 'label.setting' );

								if ( 'default' === jQuery( this ).val() || 'thumbnails' === jQuery( this ).val() ) {
									columnSetting.show();
								} else {
									columnSetting.hide();
								}
							} ).change();

							return this;
						}
					} );

				} );
			</script>
			<?php
		}
	}

	new Powerkit_Gallery();
}

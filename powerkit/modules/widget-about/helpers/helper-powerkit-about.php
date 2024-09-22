<?php
/**
 * Helpers About
 *
 * @package    Powerkit
 * @subpackage Modules/Helper
 */

/**
 * Get html of about block
 *
 * @param array $params Recent options.
 */
function powerkit_about_get_html( $params ) {
	// Image.
	if ( $params['image'] ) {
		$image = sprintf( '<div class="pk-about-media"><img src="%s" alt="about"></div>', esc_url( $params['image'] ) );

		echo wp_kses_post( apply_filters( 'powerkit_lazy_process_images', $image ) );
	}

	// Title.
	if ( isset( $params['widget_title'] ) && $params['widget_title'] ) {
		echo wp_kses_post( apply_filters( 'powerkit_widget_about_title', $params['widget_title'], $params['title'] ) );
	}

	// Subtitle.
	if ( $params['subtitle'] ) {
		echo sprintf( '<p class="pk-about-small">%1$s</p>', wp_kses_post( $params['subtitle'] ) );
	}

	// Text.
	if ( $params['text'] ) {
		echo sprintf( '<div class="pk-about-content">%1$s</div>', wp_kses_post( $params['text'] ) );
	}
	?>

	<?php
	if ( ! empty( $params['button_url'] ) && ! empty( $params['button_text'] ) ) {
		$text = apply_filters( 'powerkit_widget_about_button', $params['button_text'] );

		if ( isset( $params['is_block'] ) && isset( $params['block_attrs'] ) && $params['is_block'] ) {
			powerkit_print_gutenberg_blocks_button( $text, $params['button_url'], '', 'button', $params['block_attrs'] );
		} else {
			?>
			<a href="<?php echo esc_url( $params['button_url'], null, '' ); ?>" class="pk-about-button button">
				<?php echo wp_kses( $text, 'post' ); ?>
			</a>
			<?php
		}
	}
	?>

	<?php
	if ( $params['social_links'] && powerkit_module_enabled( 'social_links' ) ) {
		powerkit_social_links( false, false, false, 'inline', 'light', 'mixed', 5 );
	}
}

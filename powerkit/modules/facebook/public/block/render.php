<?php
/**
 * Facebook Fanpage block template
 *
 * @var        $attributes - block attributes
 * @var        $options - layout options
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    PowerKit
 * @subpackage PowerKit/templates
 */

?>

<div class="<?php echo esc_attr( $attributes['className'] ); ?>" <?php echo ( isset( $attributes['anchor'] ) ? ' id="' . esc_attr( $attributes['anchor'] ) . '"' : '' ); ?>>
	<div class="fb-page"
		data-href="<?php echo esc_url( $attributes['href'] ); ?>"
		data-hide-cover="<?php echo esc_attr( $attributes['showCover'] ? 'false' : 'true' ); ?>"
		data-show-facepile="<?php echo esc_attr( $attributes['showFacepile'] ? 'true' : 'false' ); ?>"
		data-show-posts="<?php echo esc_attr( $attributes['showPosts'] ? 'true' : 'false' ); ?>"
		data-small-header="<?php echo esc_attr( $attributes['smallHeader'] ? 'true' : 'false' ); ?>"
		data-adapt-container-width="true"
	></div>
</div>

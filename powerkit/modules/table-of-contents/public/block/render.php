<?php
/**
 * TOC block template
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

$params = array(
	'title'          => isset( $attributes['title'] ) ? $attributes['title'] : esc_html__( 'Table of Contents', 'powerkit' ),
	'depth'          => isset( $attributes['depth'] ) ? $attributes['depth'] : 2,
	'min_count'      => isset( $attributes['minCount'] ) ? $attributes['minCount'] : 4,
	'min_characters' => isset( $attributes['minCharacters'] ) ? $attributes['minCharacters'] : 1000,
	'btn_hide'       => isset( $attributes['btnHide'] ) ? $attributes['btnHide'] : false,
	'default_state'  => isset( $attributes['defaultState'] ) ? $attributes['defaultState'] : 'expanded',
);
?>

<div class="<?php echo esc_attr( $attributes['className'] ); ?>" <?php echo ( isset( $attributes['anchor'] ) ? ' id="' . esc_attr( $attributes['anchor'] ) . '"' : '' ); ?>>
	<?php
	global $powerkit_toc_parse;

	if ( ! $powerkit_toc_parse ) {
		// TOC output.
		powerkit_toc_list( $params );
	}
	?>
</div>

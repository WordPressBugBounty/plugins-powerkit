<?php
/**
 * Pinterest Board block template
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

$attributes['className'] .= ' pinterest-board-wrapper';

?>

<div class="<?php echo esc_attr( $attributes[ 'className' ] ); ?>" <?php echo ( isset( $attributes[ 'anchor' ] ) ? ' id="' . esc_attr( $attributes[ 'anchor' ] ) . '"' : '' ); ?>>
    <a href="<?php echo esc_url( $attributes['href'] ); ?>" data-pin-do="embedBoard" data-pin-board-width="100%"></a>
</div>

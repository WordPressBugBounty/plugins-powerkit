<?php
/**
 * Coming Soon default template
 *
 * @var     $object  - Page object.
 * @var     $title   - The title.
 * @var     $content - The content.
 *
 * @package Powerkit
 */

?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">

	<?php wp_head(); ?>

	<?php
	if ( function_exists( 'cnvs_gutenberg' ) ) {
		$blocks = parse_blocks( $content );

		$blocks_css = cnvs_gutenberg()->parse_blocks_css( $blocks );

		if ( $blocks_css ) {
			call_user_func( 'printf', '<style>%s</style>', $blocks_css );
		}
	}
	?>
</head>
<body>
	<div class="pk-coming-soon-page pk-coming-soon-default">
		<div class="pk-coming-soon-container">
			<?php if ( $object && has_post_thumbnail( $object ) ) { ?>
				<div class="pk-coming-soon-image">
					<?php echo get_the_post_thumbnail( $object, 'large', array( 'class' => 'pk-lazyload-disabled' ) ); ?>
				</div>
			<?php } ?>
			<div class="pk-coming-soon-content">
				<div class="entry-content">
					<?php
					$content = do_blocks( $content );

					call_user_func( 'printf', '%s', do_shortcode( $content ) );
					?>
				</div>
			</div>
		</div>
	</div>

	<?php wp_footer(); ?>
</body>
</html>

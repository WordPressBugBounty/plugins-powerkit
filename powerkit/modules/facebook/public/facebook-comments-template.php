<?php
/**
 * Facebook Comments Template
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    PowerKit
 * @subpackage PowerKit/templates
 */

?>

<?php
if ( get_option( 'powerkit_connect_facebook_app_id' ) ) {
	?>
		<div class="fb-comments" data-width="100%" data-href="<?php the_permalink(); ?>" data-numposts="<?php get_option( 'powerkit_facebook_number_comments', 10 ); ?>"></div>
	<?php
} else {
	powerkit_alert_warning( sprintf( __( 'The Facebook App ID is not provided, please add it on the', 'powerkit' ), admin_url( 'options-general.php?page=powerkit_connect&tab=facebook' ) ) );
}

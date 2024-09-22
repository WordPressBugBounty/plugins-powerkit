<?php
/**
 * Helpers Social Links
 *
 * @package    Powerkit
 * @subpackage Modules/Helper
 */

/**
 * Register Shortcodes
 *
 * @param array $map Shortcode parameters.
 */
function powerkit_basic_shortcodes_register( $map ) {
	add_filter( 'powerkit_basic_shortcodes_ui_args', function( $sections ) use ( $map ) {
		$sections[] = $map;
		return $sections;
	} );
}

/**
 * Autoload files in the directory.
 *
 * @param string $path Directory path.
 * @param string $pattern Regex pattern.
 * @since    1.0.0
 */
function powerkit_basic_shortcodes_autoload( $path, $pattern = false ) {
	if ( is_dir( $path ) ) {
		$files = scandir( $path );
	} else {
		return false;
	}

	// loop folders.
	foreach ( $files as $file ) {
		$path_file = $path . '/' . basename( $file );

		if ( $pattern && ! preg_match( "/$pattern/", basename( $file ) ) ) {
			continue;
		}

		if ( file_exists( $path_file ) && 'index.php' !== $file ) {

			if ( is_dir( $path_file ) && file_exists( $path_file . "/$file.php" ) ) {
				require_once $path_file . "/$file.php";
			} elseif ( is_file( $path_file ) && preg_match( '/\.php$/i', $path_file ) ) {
				require_once $path_file;
			}
		}
	}
}

/**
 * Clean shortcodes
 *
 * @param string $content Post content.
 * @return string         Filtered Post content.
 */
function powerkit_basic_shortcodes_clean( $content ) {
	$array   = array(
		'<p>['    => '[',
		']</p>'   => ']',
		']<br />' => ']',
	);
	$content = strtr( $content, $array );
	return $content;
}
add_filter( 'the_content', 'powerkit_basic_shortcodes_clean' );

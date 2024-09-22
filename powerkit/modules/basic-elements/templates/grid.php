<?php
/**
 * Shortcode Grid config
 *
 * @package    Powerkit
 * @subpackage Templates
 */

/**
 * Init module
 */
class Powerkit_Basic_Grid {

	/**
	 * Initialize.
	 */
	public function __construct() {

		if ( class_exists( 'Gridable' ) ) {
			add_filter( 'gridable_row_class', array( $this, 'gridable_row_class' ) );
			add_filter( 'gridable_column_class', array( $this, 'gridable_column_class' ), 10, 4 );
			add_filter( 'gridable_load_public_style', '__return_false' );

		} else {

			add_shortcode( 'powerkit_row', array( $this, 'add_row_shortcode' ) );
			add_shortcode( 'powerkit_col', array( $this, 'add_column_shortcode' ) );
			add_shortcode( 'row', array( $this, 'add_row_shortcode' ) );
			add_shortcode( 'col', array( $this, 'add_column_shortcode' ) );
			add_filter( 'the_content', array( $this, 'parse_content_for_nested_rows' ), 9 );
			add_filter( 'powerkit_the_column_content', array( $this, 'fix_lost_p_tags' ), 10, 2 );
		}
	}

	/**
	 * Render the [powerkit-row]
	 *
	 * @param array  $atts    The atts.
	 * @param string $content The content.
	 */
	public function add_row_shortcode( $atts, $content ) {
		ob_start();
		?>
			<div class="pk-row">
				<?php
				$row_content = apply_filters( 'powerkit_the_row_content', $content, $atts );

				if ( apply_filters( 'powerkit_render_shortcodes_in_row', true, $content, $atts ) ) {
					echo do_shortcode( $row_content );
				} else {
					echo $row_content; // XSS.
				}
				?>
			</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render the [powerkit-col]
	 *
	 * @param array  $atts    The atts.
	 * @param string $content The content.
	 */
	public function add_column_shortcode( $atts, $content ) {
		$size = 1;
		if ( ! empty( $atts['size'] ) ) {
			$size = (int) $atts['size'];
		}

		ob_start();
		?>
			<div class="pk-col-md-<?php echo esc_attr( $size ); ?>">
				<?php
				$column_content = apply_filters( 'powerkit_the_column_content', $content, $atts );

				if ( apply_filters( 'powerkit_render_shortcodes_in_column', true, $content, $atts ) ) {
					echo do_shortcode( $column_content );
				} else {
					echo $column_content; // XSS.
				}
				?>
			</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * This function  strips unclosed p tags at a beggining and at the end of a row
	 *
	 * @param string $content The content.
	 * @param array  $atts    The atts.
	 */
	public function fix_lost_p_tags( $content, $atts ) {
		if ( is_admin() ) {
			return $content;
		}

		$first_4_chars = substr( $content, 0, 4 );

		$last_3_chars = substr( $content, -3, 4 );

		if ( '</p>' === $first_4_chars ) {
			$content = substr( $content, 5 );
		}

		if ( '<p>' === $last_3_chars ) {
			$content = substr( $content, 0, -4 );
		}

		return $content;
	}

	/**
	 * Try to allow one level of nested rows
	 *
	 * @param string $content The content.
	 * @param bool   $rec     The rec.
	 */
	public function parse_content_for_nested_rows( $content, $rec = false ) {
		$rows_matches = array();

		preg_match_all( '#' . get_shortcode_regex( array( 'powerkit-row' ) ) . '#ims', $content, $rows_matches );

		/**
		 * Basically in the first group of matches are the plain row texts
		 * If a row contains another row, we should render it before.
		 */
		if ( ! empty( $rows_matches[0] ) ) {

			// Iterate through each row and check if anyone has a nested row.
			foreach ( $rows_matches[0] as $key => $match ) {

				$row_pos = strpos( $rows_matches[0][ $key ], '[powerkit-row cols_nr="', 5 );

				// If there is another row inside render it first.
				if ( false !== $row_pos ) {
					// Make a clone of the original row.
					$temp_row = $match;
					// If this row has an inner row, let's render it and replace it in the clone row.
					preg_match( '#' . get_shortcode_regex( array( 'powerkit-row' ) ) . '#', $match, $smatch );
					if ( substr_count( $smatch[0], '[powerkit-row ' ) > 1 ) {
						$inner_rows = array();

						// Right now the row form is [powerkit-row] content [powerkit-row]content[/powerkit-row]
						// if we render the available rows we will have a nested-free row.
						$remove_starting_row = '~\[' . $smatch[1] . $smatch[2] . $smatch[3] . '\]~';

						$temp_content = preg_replace( $remove_starting_row, '', $smatch[0], 1 );

						preg_match_all( '#' . get_shortcode_regex( array( 'powerkit-row' ) ) . '#ms', $temp_content, $inner_rows );

						// There may be more than one inner row, catch'em all.
						foreach ( $inner_rows[0] as $inner_row ) {
							$temp_row = str_replace( $inner_row, do_shortcode( $inner_row ), $temp_row );
						}
					}
					// Now we have a [powerkit-row] content <div class="pk-row"></div>
					// the closing [/powerkit-row] is definetly somewhere after.
					$content = str_replace( $match, $temp_row, $content );
				} else {
					if ( ! $rec ) {
						$content = $this->parse_content_for_nested_rows( $content, true );
					}
				}
			}
		}

		return $content;
	}

	/**
	 * -------------------------------------------------------------------------
	 * [ Support Gridable ]
	 * -------------------------------------------------------------------------
	 */

	/**
	 * Row Class
	 */
	public function gridable_row_class() {
		return array( 'pk-row' );
	}

	/**
	 * Column Class
	 *
	 * @param array  $classes Available classes.
	 * @param int    $size    Column size.
	 * @param array  $atts    Attributes.
	 * @param string $content Content.
	 */
	public function gridable_column_class( $classes, $size, $atts, $content ) {

		$classes = array( 'pk-col-md-' . $size );

		return $classes;
	}
}

new Powerkit_Basic_Grid();

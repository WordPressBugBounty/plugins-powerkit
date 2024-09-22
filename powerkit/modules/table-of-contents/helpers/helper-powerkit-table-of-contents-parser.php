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
class Powerkit_Table_Of_Contents_Parser {
	/**
	 * The options.
	 *
	 * @var string $options The options.
	 */
	private $options;

	/**
	 * The collision collector.
	 *
	 * @var string $collision_collector The collision collector.
	 */
	private $collision_collector;

	/**
	 * __construct
	 *
	 * This function will initialize the initialize
	 */
	public function __construct() {
		$this->collision_collector = array();

		$defaults = array(
			'depth'          => 6,
			'count'          => 0,
			'characters'     => 0,
			'ordered_list'   => false,
			'show_heirarchy' => true,
			'exclude'        => apply_filters( 'pk_toc_exclude', '.pk-title' ),
		);

		$this->options = $defaults;
	}

	/**
	 * Returns a clean url to be used as the destination anchor target
	 *
	 * @param string $title Text title.
	 */
	private function url_anchor_target( $title ) {
		$return = false;

		if ( $title ) {
			$return = trim( strip_tags( $title ) );

			// Converts Cyrillic characters.
			$return = powerkit_text_with_translit( $return );

			// Filters a sanitized title string.
			$return = sanitize_title( $return );
		}

		if ( array_key_exists( $return, $this->collision_collector ) ) {
			$this->collision_collector[ $return ]++;
			$return .= '-' . $this->collision_collector[ $return ];
		} else {
			$this->collision_collector[ $return ] = 1;
		}

		return apply_filters( 'pk_toc_url_anchor_target', $return );
	}

	/**
	 * Build Hierarchy
	 *
	 * @param array $matches The matches.
	 */
	private function build_hierarchy( &$matches ) {
		$current_depth      = 100;
		$html               = '';
		$numbered_items     = array();
		$numbered_items_min = null;

		// Reset the internal collision collection.
		$this->collision_collector = array();

		// Find the minimum heading to establish our baseline.
		for ( $i = 0; $i < count( $matches ); $i++ ) {
			if ( $current_depth > $matches[ $i ][2] ) {
				$current_depth = (int) $matches[ $i ][2];
			}
		}

		$numbered_items[ $current_depth ] = 0;
		$numbered_items_min               = $current_depth;

		for ( $i = 0; $i < count( $matches ); $i++ ) {

			if ( $current_depth === (int) $matches[ $i ][2] ) {
				$html .= '<li>';
			}

			// Start lists.
			if ( $current_depth !== (int) $matches[ $i ][2] ) {
				for ( $current_depth; $current_depth < (int) $matches[ $i ][2]; $current_depth++ ) {
					$numbered_items[ $current_depth + 1 ] = 0;

					$html .= '<ol><li>';
				}
			}

			// List item.
			$html .= '<a href="#' . $this->url_anchor_target( $matches[ $i ][0] ) . '">';
			if ( $this->options['ordered_list'] ) {
				// Attach leading numbers when lower in hierarchy.
				$html .= '<span class="toc_number toc_depth_' . ( $current_depth - $numbered_items_min + 1 ) . '">';
				for ( $j = $numbered_items_min; $j < $current_depth; $j++ ) {
					$number = ( $numbered_items[ $j ] ) ? $numbered_items[ $j ] : 0;
					$html  .= $number . '.';
				}

				$html .= ( $numbered_items[ $current_depth ] + 1 ) . '</span> ';
				$numbered_items[ $current_depth ]++;
			}
			$html .= strip_tags( $matches[ $i ][0] ) . '</a>';

			// End lists.
			if ( $i !== count( $matches ) - 1 ) {
				if ( $current_depth > (int) $matches[ $i + 1 ][2] ) {
					for ( $current_depth; $current_depth > (int) $matches[ $i + 1 ][2]; $current_depth-- ) {
						$html .= '</li></ol>';

						$numbered_items[ $current_depth ] = 0;
					}
				}

				if ( (int) @$matches[ $i + 1 ][2] === $current_depth ) {
					$html .= '</li>';
				}
			} else {
				// This is the last item, make sure we close off all tags.
				for ( $current_depth; $current_depth >= $numbered_items_min; $current_depth-- ) {
					$html .= '</li>';
					if ( $current_depth !== $numbered_items_min ) {
						$html .= '</ol>';
					}
				}
			}
		}

		return $html;
	}

	/**
	 * Returns a string with all items from the $find array replaced with their matching
	 * items in the $replace array.  This does a one to one replacement (rather than
	 * globally).
	 *
	 * This function is multibyte safe.
	 *
	 * @param array  $find    The find.
	 * @param array  $replace The replace.
	 * @param string $string  The string.
	 */
	public function mb_find_replace( &$find = false, &$replace = false, &$string = '' ) {
		if ( is_array( $find ) && is_array( $replace ) && $string ) {
			// Check if multibyte strings are supported.
			if ( function_exists( 'mb_strpos' ) ) {
				for ( $i = 0; $i < count( $find ); $i++ ) {
					$string =
						mb_substr( $string, 0, mb_strpos( $string, $find[ $i ] ) ) .
						$replace[ $i ] .
						mb_substr( $string, mb_strpos( $string, $find[ $i ] ) + mb_strlen( $find[ $i ] ) );
				}
			} else {
				for ( $i = 0; $i < count( $find ); $i++ ) {
					$string = substr_replace(
						$string,
						$replace[ $i ],
						strpos( $string, $find[ $i ] ),
						strlen( $find[ $i ] )
					);
				}
			}
		}

		return $string;
	}

	/**
	 * This function extracts headings from the html formatted $content.  It will pull out
	 * only the required headings as specified in the options.  For all qualifying headings,
	 * this function populates the $find and $replace arrays (both passed by reference)
	 * with what to search and replace with.
	 *
	 * Returns a html formatted string of list items for each qualifying heading.  This
	 * is everything between and NOT including <ol> and </ol>
	 *
	 * @param array  $find    The find.
	 * @param array  $replace The replace.
	 * @param string $content The content.
	 * @param array  $params The params.
	 */
	public function extract_headings( &$find, &$replace, $content = '', $params = array() ) {
		$matches = array();
		$anchor  = '';
		$items   = false;

		$this->collision_collector = array();

		$inner_content = strip_tags( $content );

		if ( isset( $params['depth'] ) && (int) $params['depth'] ) {
			$this->options['depth'] = (int) $params['depth'];
		}
		if ( isset( $params['min_count'] ) && (int) $params['min_count'] ) {
			$this->options['count'] = (int) $params['min_count'];
		}
		if ( isset( $params['min_characters'] ) && (int) $params['min_characters'] ) {
			$this->options['characters'] = (int) $params['min_characters'];
		}

		// Minimum number of characters.
		if ( function_exists( 'mb_strlen' ) ) {
			if ( mb_strlen( $inner_content ) < $this->options['characters'] ) {
				return $items;
			}
		} else {
			if ( strlen( $inner_content ) < $this->options['characters'] ) {
				return $items;
			}
		}

		if ( is_array( $find ) && is_array( $replace ) && $content ) {
			/*
			 * get all headings
			 * the html spec allows for a maximum of 6 heading depths
			 */
			if ( preg_match_all( '/(<h([1-6]{1})[^>]*>).*<\/h\2>/msuU', $content, $matches, PREG_SET_ORDER ) ) {

				// Remove specific headings if provided via the 'exclude' property.
				if ( $this->options['exclude'] ) {
					$excluded_headings = explode('|', $this->options['exclude']);

					if ( count( $excluded_headings ) > 0 ) {
						for ( $j = 0; $j < count( $excluded_headings ); $j++ ) {
							// Escape some regular expression characters.
							$excluded_headings[ $j ] = str_replace(
								array( '*' ),
								array( '.*' ),
								trim( $excluded_headings[ $j ] )
							);
						}

						$new_matches = array();
						for ( $i = 0; $i < count( $matches ); $i++ ) {
							$found = false;
							for ( $j = 0; $j < count( $excluded_headings ); $j++ ) {
								if ( @preg_match( '/' . $excluded_headings[ $j ] . '/imU', $matches[ $i ][0] ) ) {
									$found = true;
									break;
								}
							}
							if ( ! $found ) {
								$new_matches[] = $matches[$i];
							}
						}
						if ( count( $matches ) !== count( $new_matches ) ) {
							$matches = $new_matches;
						}
					}
				}

				// Remove empty headings.
				$new_matches = array();
				for ( $i = 0; $i < count( $matches ); $i++ ) {
					if ( trim( strip_tags( $matches[ $i ][0] ) ) !== false ) {
						$new_matches[] = $matches[ $i ];
					}
				}

				if ( count( $matches ) !== count( $new_matches ) ) {
					$matches = $new_matches;
				}

				// Check minimum number of headings.
				if ( count( $matches ) >= $this->options['count'] ) {

					for ( $i = 0; $i < count( $matches ); $i++ ) {
						$match_data  = $matches[ $i ][0];
						$match_index = $matches[ $i ][2];
						$match_open  = $matches[ $i ][1];
						$match_end   = sprintf( '</h%s>', $matches[ $i ][2] );

						// Get anchor and add to find and replace arrays.
						$anchor = $this->url_anchor_target( $match_data );

						// Find.
						$find[] = $match_data;

						// Check for the same identifier.
						if ( ! preg_match( '/id="' . $anchor . '"/', $match_data ) ) {
							// Add anchor link inside.
							if ( apply_filters( 'powerkit_toc_anchor_link', false ) ) {
								$replace[] = str_replace(
									array( $match_open ),
									array( $match_open . sprintf( '<a class="pk-anchor-link" href="%1$s#%2$s" id="%2$s"></a>', site_url( add_query_arg( array() ) ), $anchor ) ),
									$match_data
								);
								// Wrap the span inside.
							} elseif ( preg_match( '/<h' . $match_index . '[^>]*?id=".*?"[^>]*?>/', $match_open ) ) {
								$replace[] = str_replace(
									array( $match_open, $match_end ),
									array( $match_open . sprintf( '<span id="%s">', $anchor ), '</span>' . $match_end ),
									$match_data
								);
								// Add ID to element.
							} else {
								$replace[] = str_replace( '<h' . $match_index, sprintf( '<h%s id="%s"', $match_index, $anchor ), $match_data );
							}
						} else {
							$replace[] = $match_data;
						}

						// Assemble flat list.
						if ( ! $this->options['show_heirarchy'] ) {
							$items .= '<li><a href="#' . $anchor . '">';
							if ( $this->options['ordered_list'] ) {
								$items .= count( $replace ) . ' ';
							}
							$items .= strip_tags( $match_data ) . '</a></li>';
						}
					}

					/*
					* build a hierarchical toc?
					* we could have tested for $items but that var can be quite large in some cases
					*/
					if ( $this->options['show_heirarchy'] ) {

						$minimum_level = 100;

						// Find the minimum heading to establish our baseline.
						for ( $i = 0; $i < count( $matches ); $i++ ) {
							if ( $minimum_level > $matches[ $i ][2] ) {
								$minimum_level = (int) $matches[ $i ][2];
							}
						}

						// Filter of depth.
						$final_matches = array();
						for ( $i = 0; $i < count( $matches ); $i++ ) {
							if ( (int) $matches[ $i ][2] < $minimum_level + $this->options['depth'] ) {
								$final_matches[] = $matches[ $i ];
							}
						}

						if ( $final_matches ) {
							$items = $this->build_hierarchy( $final_matches );
						}
					}
				}
			}
		}

		return $items;
	}
}

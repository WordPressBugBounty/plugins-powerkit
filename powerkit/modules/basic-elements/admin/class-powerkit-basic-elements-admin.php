<?php
/**
 * The admin-specific functionality of the module.
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    Powerkit
 * @subpackage Modules/Admin
 */

/**
 * The admin-specific functionality of the module.
 */
class Powerkit_Basic_Elements_Admin extends Powerkit_Module_Admin {

	/**
	 * Initialize
	 */
	public function initialize() {
		add_action( 'wp_ajax_powerkit_basic_shortcodes_sections', array( $this, 'powerkit_basic_shortcodes_ajax_panel' ) );
		add_filter( 'mce_buttons', array( $this, 'powerkit_basic_shortcodes_register_buttons' ) );
		add_filter( 'mce_external_plugins', array( $this, 'powerkit_basic_shortcodes_register_plugin' ) );
	}


	/**
	 * Register shorcodes button for TinyMCE buttons (Visual tab).
	 *
	 * @since  1.0.0
	 * @param  array $buttons First-row list of buttons.
	 * @return array Buttons.
	 */
	public function powerkit_basic_shortcodes_register_buttons( $buttons ) {
		if ( current_user_can( 'edit_posts' ) ) {
			array_push( $buttons, 'powerkit_basic_shortcodes_button' );
		}

		return $buttons;
	}

	/**
	 * Register shortcodes plugin
	 *
	 * @since  1.0.0
	 * @param  array $plugins An array of external TinyMCE plugins.
	 * @return array Plugins.
	 */
	public function powerkit_basic_shortcodes_register_plugin( $plugins ) {
		if ( current_user_can( 'edit_posts' ) ) {
			$plugins['powerkit_basic_shortcodes'] = trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/admin-powerkit-basic-elements.js';
		}

		return $plugins;
	}

	/**
	 * Generate Section Fileds
	 *
	 * @since 1.0.0
	 * @param array $section Sections Fields.
	 * @param array $counter Unique field identifier.
	 */
	public function generate_section_fields( $section, $counter = 0 ) {
		if ( isset( $section['fields'] ) && is_array( $section['fields'] ) ) {

			// Default Field Settings.
			$default_field = array(
				'type'    => 'input',
				'name'    => '',
				'label'   => '',
				'desc'    => '',
				'default' => '',
				'attrs'   => array(),
				'style'   => 'vertical', // For radio.
				'suffix'  => '',         // For inputs.
				'options' => array(),    // For select, radio.
				'fields'  => array(),    // For repeater.
			);

			foreach ( $section['fields'] as $field ) {

				$counter++;

				// Merge Settings.
				$field = array_merge( $default_field, $field );

				// Attrs.
				$attrs = null;

				if ( ! empty( $field['attrs'] ) ) {
					foreach ( $field['attrs'] as $key => $value ) {
						$attrs[] = sprintf( '%s="%s"', $key, $value );
					}
					$attrs = implode( ' ', $attrs );
				}

				// Output by type.
				switch ( $field['type'] ) {
					case 'section':
						?>
							<tr>
								<th><h3><?php echo wp_kses_post( $field['label'] ); ?></h3></th><td>&nbsp;</td>
							</tr>
						<?php
						break;
					case 'input':
						?>
							<tr>
								<th><?php echo wp_kses_post( $field['label'] ); ?>:<small><?php echo wp_kses_post( $field['desc'] ); ?></small></th>
								<td>
									<input name="<?php echo esc_attr( $field['name'] ); ?>" type="text" value="<?php echo esc_attr( $field['default'] ); ?>" <?php echo wp_kses_data( $attrs ); ?>><?php echo esc_attr( $field['suffix'] ); ?>
								</td>
							</tr>
						<?php
						break;
					case 'textarea':
						?>
							<tr>
								<th><?php echo wp_kses_post( $field['label'] ); ?>:<small><?php echo wp_kses_post( $field['desc'] ); ?></small></th>
								<td>
									<textarea name="<?php echo esc_attr( $field['name'] ); ?>" <?php echo wp_kses_data( $attrs ); ?>><?php echo wp_kses_post( $field['default'] ); ?></textarea>
								</td>
							</tr>
						<?php
						break;
					case 'select':
						?>
							<tr>
								<th><?php echo wp_kses_post( $field['label'] ); ?>:<small><?php echo wp_kses_post( $field['desc'] ); ?></small></th>
								<td>
									<select name="<?php echo esc_attr( $field['name'] ); ?>" <?php echo wp_kses_data( $attrs ); ?>>
										<?php if ( isset( $field['options'] ) && $field['options'] ) : ?>
											<?php foreach ( $field['options'] as $key => $option ) : ?>
												<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $field['default'], esc_attr( $key ) ); ?>><?php echo esc_attr( $option ); ?></option>
											<?php endforeach; ?>
										<?php endif; ?>
									</select>
								</td>
							</tr>
						<?php
						break;

					case 'checkbox':
						?>
							<tr>
								<th><?php echo wp_kses_post( $field['label'] ); ?>:<small><?php echo wp_kses_post( $field['desc'] ); ?></small></th>
								<td>
									<input name="<?php echo esc_attr( $field['name'] ); ?>" type="checkbox" value="true" <?php checked( (bool) $field['default'], true ); ?> <?php echo wp_kses_data( $attrs ); ?>>
								</td>
							</tr>
						<?php
						break;

					case 'radio':
						?>
							<tr>
								<th><?php echo wp_kses_post( $field['label'] ); ?>:<small><?php echo wp_kses_post( $field['desc'] ); ?></small></th>
								<td>
									<?php if ( isset( $field['options'] ) && $field['options'] ) : ?>
										<?php foreach ( $field['options'] as $key => $option ) : $counter++; ?>
											<?php $id = sprintf( 'field-%s-%s-%s', $section['name'], $field['name'], $counter ); ?>
											<input id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $field['name'] ); ?>" type="radio" value="<?php echo esc_attr( $key ); ?>" <?php checked( $field['default'], $key ); ?> <?php echo wp_kses_data( $attrs ); ?>>
											<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_attr( $option ); ?></label>
											<?php if ( 'vertical' === $field['style'] ) : ?>
												<br>
											<?php else : ?>
												&nbsp;
											<?php endif; ?>
										<?php endforeach; ?>
									<?php endif; ?>
								</td>
							</tr>
						<?php
						break;

					case 'content':
						?>
							<tr>
								<th><?php echo wp_kses_post( $field['label'] ); ?>:<small><?php echo wp_kses_post( $field['desc'] ); ?></small></th>
								<td>
									<textarea name="<?php echo esc_attr( $field['name'] ); ?>" <?php echo wp_kses_data( $attrs ); ?>><?php echo wp_kses_post( $field['default'] ); ?></textarea>
								</td>
							</tr>
						<?php
						break;

					case 'repeater':
						?>
							<tr>
								<th><?php echo wp_kses_post( $field['label'] ); ?>:</label><small><?php echo wp_kses_post( $field['desc'] ); ?></small></th>
								<td class="basic-shortcodes-repeater-field">
									<div data-repeater-list="<?php echo esc_attr( $field['base'] ); ?>" >
										<table class="form-table" data-repeater-item>
											<tbody>
												<?php $this->generate_section_fields( $field, $counter ); ?>
											</tbody>
											<tfoot>
												<tr>
													<th></th>
													<td>
														<span class="button-secondary" data-repeater-delete><?php esc_html_e( 'Delete Item', 'powerkit' ); ?></span>
													</td>
												</tr>
											</tfoot>
										</table>
									</div>
									<span data-repeater-create class="button-primary"><?php esc_html_e( 'Add Item', 'powerkit' ); ?></span>
								</td>
							</tr>
						<?php
						break;
				}
			}
		}
	}

	/**
	 * Ajax Load Admin Shortcodes Panel
	 *
	 * @since 1.0.0
	 */
	public function powerkit_basic_shortcodes_ajax_panel() {

		// Allow themes and plugins to enable/disable specific shortcodes UI panel.
		$sections = apply_filters( 'powerkit_basic_shortcodes_ui_args', array() );

		// Sort elements.
		usort( $sections, function ( $a, $b ) {
			return $a['priority'] - $b['priority'];
		} );
		?>
			<div class="wrap powerkit_basic_shortcodes_wrap">

				<div class="powerkit_basic_shortcodes_tabs">

					<ul>
					<?php foreach ( $sections as $section ) : ?>
						<li><a data-nav="<?php echo esc_attr( $section['name'] ); ?>" href="#"><?php echo esc_html( $section['title'] ); ?></a></li>
					<?php endforeach; ?>
					</ul>

				</div>

				<div class="powerkit_basic_shortcodes_tabs_sections">

					<?php foreach ( $sections as $section ) : ?>
						<div class="hidable wrap tabs-<?php echo esc_attr( $section['name'] ); ?>" style="display: none;">

							<form class="powerkit_basic_shortcodes_tab" enctype="multipart/form-data">
								<table class="form-table">
									<tbody>
										<?php $this->generate_section_fields( $section ); ?>

										<tr>
											<th><input type="submit" class="button-primary" value="<?php esc_html_e( 'Insert', 'powerkit' ); ?>"></th>
											<td>&nbsp;</td>
										</tr>
									</tbody>
								</table>
							</form>
						</div>

						<script type="text/javascript">
						/* <![CDATA[ */
						(function($) {

							// Insert Shortcode.
							$('.tabs-<?php echo esc_attr( $section['name'] ); ?> form').submit( function( e ) {
								e.preventDefault();

								// Hide Popup.
								tb_remove();

								// Get input values from form.
								var fieldsObj = $( this ).serializeObject();

								// Vars.
								var shortcodeContent = '',
									shortcodeAttrs   = [];

								<?php foreach ( (array) $section['fields'] as $field ) : ?>

									var fieldName = '<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : '' ); ?>';
									<?php
									switch ( $field['type'] ) {
										case 'section':
											break;
										case 'repeater':
											?>
											var subContentVars  = [],
												subCheckboxVars = [],
												shortcodeBase   = '<?php echo esc_attr( $field['base'] ); ?>';

											<?php foreach ( (array) $field['fields'] as $sub_field ) : ?>

												<?php if ( 'content' === $sub_field['type'] ) : ?>
													subContentVars.push( '<?php echo esc_attr( $sub_field['name'] ); ?>' );
												<?php endif; ?>

												<?php if ( 'checkbox' === $sub_field['type'] ) : ?>
													subCheckboxVars.push( '<?php echo esc_attr( $sub_field['name'] ); ?>' );
												<?php endif; ?>

											<?php endforeach; ?>

											if( typeof fieldsObj[ shortcodeBase ] !== 'undefined' ) {

												$.each( fieldsObj[ shortcodeBase ], function( key, obj ) {

													var subShortcodeContent = '',
														subShortcodeAttrs   = [];

													$.each( obj, function( s_key, s_obj ) {
														if( typeof s_obj !== 'undefined' ) {

															// Field "Content" fix.
															if( ! $.inArray( s_key, subContentVars ) ) {
																subShortcodeContent += s_obj;
															} else {

																// Other fields.
																subShortcodeAttrs.push( s_key + '="' + s_obj + '"' );
															}
														}

													} );

													// Add Inner Shortcode.
													if( subShortcodeContent ) {
														shortcodeContent += '[' + shortcodeBase + ' ' + subShortcodeAttrs.join( ' ' ) + ']<br>' + subShortcodeContent + '<br>[/' + shortcodeBase + ']<br>';
													} else {
														shortcodeContent += '[' + shortcodeBase + ' ' + subShortcodeAttrs.join( ' ' ) + ']<br>';
													}

												} );
											}
											<?php
											break;
										case 'content':
											?>
											if( typeof fieldsObj[ fieldName ] !== 'undefined' ) {
												shortcodeContent += fieldsObj[ fieldName ];
											}
											<?php
											break;
										case 'checkbox':
											?>
											if( typeof fieldsObj[ fieldName ] !== 'undefined' ) {
												var fieldAttr = fieldName + '="' + fieldsObj[ fieldName ] + '"';
											} else {
												var fieldAttr = fieldName + '="false"';
											}

											shortcodeAttrs.push( fieldAttr );
											<?php
											break;
										default:
											?>
											if( typeof fieldsObj[ fieldName ] !== 'undefined' ) {
												var fieldAttr = fieldName + '="' + fieldsObj[ fieldName ] + '"';
											} else {
												var fieldAttr = fieldName + '=""';
											}

											shortcodeAttrs.push( fieldAttr );
											<?php
											break;
									}
									?>
								<?php endforeach; ?>

								// Add Shortcode.
								var shortcodeName = '<?php echo esc_attr( $section['base'] ); ?>';

								if( shortcodeContent ) {

									// Trim Line Breaks.
									shortcodeContent = shortcodeContent.replace( /^<br>|<br>$/gm, '' );

									var content = '[' + shortcodeName + ' ' + shortcodeAttrs.join( ' ' ) + ']<br>' + shortcodeContent + '<br>[/' + shortcodeName + ']';
								} else {
									var content = '[' + shortcodeName + ' ' + shortcodeAttrs.join( ' ' ) + ']';
								}

								// Remove odd space.
								content = content.replace(/\s\]/g, ']');

								// Convert br.
								content = content.replace(/([^>])\n/g, '$1<br/>');

								powerkit_basic_shortcodes.setContent( content );
							});

						})(jQuery);
						/* ]]> */
						</script>
					<?php endforeach; ?>

				</div>

				<script type="text/javascript">
				/* <![CDATA[ */
				( function( $ ) {

					// Tabs Navigation.
					$( '.powerkit_basic_shortcodes_tabs a' ).click( function( e ) {
						e.preventDefault();
						powerkit_basic_shortcodes_tabs_switch( $( this ) );
					} );

					powerkit_basic_shortcodes_tabs_switch( $( '.powerkit_basic_shortcodes_tabs a' ).first() );

					function powerkit_basic_shortcodes_tabs_switch( obj ) {
						$( '.powerkit_basic_shortcodes_tabs_sections .hidable' ).hide();
						$( '.powerkit_basic_shortcodes_tabs_sections .tabs-' + obj.attr( 'data-nav' ) ).show();
						$( '.powerkit_basic_shortcodes_tabs li' ).removeClass( 'current' );
						obj.parent().addClass( 'current' );
					}

					// Init Repeater.
					$( document ).ready( function() {
						$( '.basic-shortcodes-repeater-field' ).each( function() {
							$( this ).repeater( {
								show: function() {
									$( this ).slideDown();
								},
								hide: function( deleteElement ) {
									$( this ).fadeOut( 400 );
								},
								isFirstItemUndeletable: true,
							} );
						} );
					} );

				} )( jQuery );
				/* ]]> */
				</script>
			</div>
		<?php
		die();
	}

	/**
	 * Register the stylesheets and JavaScript for the admin area.
	 *
	 * @param string $page Current page.
	 */
	public function admin_enqueue_scripts( $page ) {

		add_thickbox();

		wp_enqueue_style( 'wp-color-picker' );

		// Styles.
		wp_enqueue_style( 'powerkit-basic-elements', powerkit_style( plugin_dir_url( __FILE__ ) . 'css/admin-powerkit-basic-elements.css' ), array(), powerkit_get_setting( 'version' ), 'all' );

		wp_enqueue_script( 'wp-color-picker' );
		// Scripts.
		wp_enqueue_script( 'powerkit-jquery-serialize', plugin_dir_url( __FILE__ ) . 'js/jquery.serialize-to-json.min.js', array( 'jquery' ), powerkit_get_setting( 'version' ), false );
		wp_enqueue_script( 'powerkit-jquery-repeater', plugin_dir_url( __FILE__ ) . 'js/jquery.repeater.min.js', array( 'jquery' ), powerkit_get_setting( 'version' ), false );
	}
}

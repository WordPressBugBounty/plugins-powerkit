<?php
/**
 * Widget Social Links
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    Powerkit
 * @subpackage Powerkit/widgets
 */

/**
 * Widget Social Links
 */
class Powerkit_Social_Links_Widget extends WP_Widget {

	/**
	 * The default settings.
	 *
	 * @var array $default_settings The default settings.
	 */
	public $default_settings = array();

	/**
	 * Sets up a new widget instance.
	 */
	public function __construct() {

		$this->default_settings = apply_filters( 'powerkit_social_links_widget_settings', array(
			'title'    => esc_html__( 'Social Links', 'powerkit' ),
			'template' => 'inline',
			'scheme'   => 'light',
			'cache'    => true,
			'labels'   => true,
			'titles'   => true,
			'counts'   => true,
			'maximum'  => -1,
			'mode'     => apply_filters( 'powerkit_social_links_counter_mode', 'mixed' ),
		) );

		$widget_details = array(
			'classname'   => 'powerkit_social_links_widget',
			'description' => esc_html__( 'Add a list of social links with fan counts to your sidebar.', 'powerkit' ),
		);
		parent::__construct( 'powerkit_social_links_widget', esc_html__( 'Social Links', 'powerkit' ), $widget_details );
	}

	/**
	 * Outputs the content for the current widget instance.
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current widget instance.
	 */
	public function widget( $args, $instance ) {
		$params = array_merge( $this->default_settings, $instance );

		// Before Widget.
		echo $args['before_widget']; // XSS OK.
		?>

		<div class="widget-body">
			<?php

			// Title.
			if ( $params['title'] ) {
				echo $args['before_title'] . apply_filters( 'widget_title', wp_kses( $params['title'], 'pk-title' ), $instance, $this->id_base ) . $args['after_title']; // XSS.
			}

			powerkit_social_links_appearance( $params );
			?>
		</div>

		<?php

		// After Widget.
		echo $args['after_widget']; // XSS OK.
	}

	/**
	 * Handles updating settings for the current widget instance.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $new_instance;

		// Labels.
		if ( ! isset( $instance['labels'] ) ) {
			$instance['labels'] = false;
		}

		// Titles.
		if ( ! isset( $instance['titles'] ) ) {
			$instance['titles'] = false;
		}

		// Counts.
		if ( ! isset( $instance['counts'] ) ) {
			$instance['counts'] = false;
		}

		return $instance;
	}

	/**
	 * Outputs the widget settings form.
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$params = array_merge( $this->default_settings, $instance );

		$templates     = apply_filters( 'powerkit_social_links_templates', array() );
		$color_schemes = apply_filters( 'powerkit_social_links_color_schemes', array() );
		?>
			<!-- Title -->
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'powerkit' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $params['title'] ); ?>" /></p>

			<!-- Labels -->
			<p><input id="<?php echo esc_attr( $this->get_field_id( 'labels' ) ); ?>" class="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'labels' ) ); ?>" type="checkbox" <?php checked( (bool) $params['labels'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'labels' ) ); ?>"><?php esc_html_e( 'Display labels', 'powerkit' ); ?></label></p>

			<!-- Titles -->
			<p><input id="<?php echo esc_attr( $this->get_field_id( 'titles' ) ); ?>" class="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'titles' ) ); ?>" type="checkbox" <?php checked( (bool) $params['titles'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'titles' ) ); ?>"><?php esc_html_e( 'Display titles', 'powerkit' ); ?></label></p>

			<!-- Сounts -->
			<p><input id="<?php echo esc_attr( $this->get_field_id( 'counts' ) ); ?>" class="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'counts' ) ); ?>" type="checkbox" <?php checked( (bool) $params['counts'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'counts' ) ); ?>"><?php esc_html_e( 'Display counts', 'powerkit' ); ?></label></p>

			<!-- Maximum Number of Social Links -->
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'maximum' ) ); ?>"><?php esc_html_e( 'Maximum number of social links:', 'powerkit' ); ?></label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'maximum' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'maximum' ) ); ?>" type="text" value="<?php echo (int) $params['maximum']; ?>" size="3" />
			</p>

			<p class="help">
				<?php echo esc_html__( 'Input -1 to remove the maximum limit of the social links.', 'powerkit' ); ?>
			</p>

			<!-- Template -->
			<?php if ( count( (array) $templates ) > 1 ) : ?>
				<p><label for="<?php echo esc_attr( $this->get_field_id( 'Template' ) ); ?>"><?php esc_html_e( 'Template:', 'powerkit' ); ?></label>
					<select name="<?php echo esc_attr( $this->get_field_name( 'template' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'template' ) ); ?>" class="widefat">
						<?php
						if ( $templates ) {
							foreach ( $templates as $key => $item ) {
								if ( isset( $item['public'] ) && false === $item['public'] ) {
									continue;
								}
								?>
									<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $params['template'], $key ); ?>><?php echo esc_attr( $item['name'] ); ?></option>
								<?php
							}
						}
						?>
					</select>
				</p>
			<?php endif; ?>

			<!-- Color scheme -->
			<?php if ( count( (array) $color_schemes ) > 1 ) : ?>
				<p><label for="<?php echo esc_attr( $this->get_field_id( 'Template' ) ); ?>"><?php esc_html_e( 'Color scheme:', 'powerkit' ); ?></label>
					<select name="<?php echo esc_attr( $this->get_field_name( 'scheme' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'scheme' ) ); ?>" class="widefat">
						<?php
						if ( $color_schemes ) {
							foreach ( $color_schemes as $key => $item ) {
								if ( isset( $item['public'] ) && false === $item['public'] ) {
									continue;
								}
								?>
									<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $params['scheme'], $key ); ?>><?php echo esc_attr( $item['name'] ); ?></option>
								<?php
							}
						}
						?>
					</select>
				</p>
			<?php endif; ?>

			<p class="pk-alert pk-alert-warning">
				<?php
					echo sprintf( '<a href="%2$s" target="_blank">%1$s</a>', esc_html__( 'Configure Social Links', 'powerkit' ), esc_url( powerkit_get_page_url( 'social_links' ) ) );
				?>
			</p>
		<?php
	}
}

/**
 * Register Widget
 */
function powerkit_widget_init_social_links() {
	register_widget( 'Powerkit_Social_Links_Widget' );
}
add_action( 'widgets_init', 'powerkit_widget_init_social_links' );

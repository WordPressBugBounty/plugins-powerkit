<?php
/**
 * Widget Instagram
 *
 * @link       https://codesupply.co
 * @since      1.0.0
 *
 * @package    PowerKit
 * @subpackage PowerKit/widgets
 */

/**
 * Widget Instagram Class
 */
class Powerkit_Instagram_Widget extends WP_Widget {

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

		$this->default_settings = apply_filters( 'powerkit_instagram_widget_settings', array(
			'title'    => esc_html__( 'Instagram', 'powerkit' ),
			'header'   => true,
			'button'   => true,
			'number'   => 6,
			'columns'  => 2,
			'size'     => 'auto',
			'target'   => '_blank',
			'template' => 'default',
		) );

		$widget_details = array(
			'classname'   => 'powerkit_instagram_widget',
			'description' => esc_html__( 'Instagram widget.', 'powerkit' ),
		);
		parent::__construct( 'powerkit_instagram_widget', esc_html__( 'Instagram', 'powerkit' ), $widget_details );
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

		$widget_id = isset( $args['widget_id'] ) ? $args['widget_id'] : 0;

		// Before Widget.
		echo $args['before_widget']; // XSS OK.
		?>

		<div class="widget-body">
			<?php
			// Title.
			if ( $params['title'] ) {
				echo $args['before_title'] . apply_filters( 'widget_title', wp_kses( $params['title'], 'pk-title' ), $instance, $this->id_base ) . $args['after_title']; // XSS.
			}

			powerkit_instagram_get_recent( $params );
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

		// Display header.
		if ( ! isset( $instance['header'] ) ) {
			$instance['header'] = false;
		}

		// Display follow button.
		if ( ! isset( $instance['button'] ) ) {
			$instance['button'] = false;
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

		$templates = apply_filters( 'powerkit_instagram_templates', array() );
		?>
			<!-- Title -->
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'powerkit' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $params['title'] ); ?>" />
			</p>

			<!-- Display header -->
			<p><input id="<?php echo esc_attr( $this->get_field_id( 'header' ) ); ?>" class="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'header' ) ); ?>" type="checkbox" <?php checked( (bool) $params['header'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'header' ) ); ?>"><?php esc_html_e( 'Display header', 'powerkit' ); ?></label></p>

			<!-- Display follow button -->
			<p><input id="<?php echo esc_attr( $this->get_field_id( 'button' ) ); ?>" class="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'button' ) ); ?>" type="checkbox" <?php checked( (bool) $params['button'] ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'button' ) ); ?>"><?php esc_html_e( 'Display follow button', 'powerkit' ); ?></label></p>

			<!-- Number of photos -->
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_html_e( 'Number of images:', 'powerkit' ); ?></label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo absint( $params['number'] ); ?>" size="3" />
			</p>

			<!-- Number of columns -->
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'columns' ) ); ?>"><?php esc_html_e( 'Number of columns:', 'powerkit' ); ?></label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'columns' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'columns' ) ); ?>" class="widefat">
					<option value="1" <?php selected( $params['columns'], '1' ); ?>><?php esc_html_e( '1', 'powerkit' ); ?></option>
					<option value="2" <?php selected( $params['columns'], '2' ); ?>><?php esc_html_e( '2', 'powerkit' ); ?></option>
					<option value="3" <?php selected( $params['columns'], '3' ); ?>><?php esc_html_e( '3', 'powerkit' ); ?></option>
				</select>
			</p>

			<!-- Open links in -->
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'target' ) ); ?>"><?php esc_html_e( 'Open links in:', 'powerkit' ); ?></label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'target' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'target' ) ); ?>" class="widefat">
					<option value="_blank" <?php selected( $params['target'], '_blank' ); ?>><?php esc_html_e( 'New window (_blank)', 'powerkit' ); ?></option>
					<option value="_self" <?php selected( $params['target'], '_self' ); ?>><?php esc_html_e( 'Current window (_self)', 'powerkit' ); ?></option>
				</select>
			</p>

			<!-- Template -->
			<?php if ( count( (array) $templates ) > 1 ) : ?>
				<p><label for="<?php echo esc_attr( $this->get_field_id( 'Template' ) ); ?>"><?php esc_html_e( 'Template', 'powerkit' ); ?></label>
					<select name="<?php echo esc_attr( $this->get_field_name( 'template' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'template' ) ); ?>" class="widefat">
						<?php
						if ( $templates ) {
							foreach ( $templates as $key => $item ) {
								?>
									<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $params['template'], $key ); ?>><?php echo esc_attr( $item['name'] ); ?></option>
								<?php
							}
						}
						?>
					</select>
				</p>
			<?php endif; ?>
		<?php
	}
}

/**
 * Register Widget
 */
function powerkit_widget_init_instagram() {
	register_widget( 'Powerkit_Instagram_Widget' );
}
add_action( 'widgets_init', 'powerkit_widget_init_instagram' );

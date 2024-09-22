<?php
/**
 * The api functions for the plugin
 *
 * @package    Powerkit
 * @subpackage Core
 * @version    1.0.0
 * @since      1.0.0
 */

/**
 * This function will return true for a non empty array
 *
 * @param array $array Array.
 */
function powerkit_is_array( $array ) {
	return ( is_array( $array ) && ! empty( $array ) );
}

/**
 * This function will return true for an empty var (allows 0 as true)
 *
 * @param mixed $value Value.
 */
function powerkit_is_empty( $value ) {
	return ( empty( $value ) && ! is_numeric( $value ) );
}

/**
 * Alias of powerkit()->has_setting()
 *
 * @param string $name The name.
 */
function powerkit_has_setting( $name = '' ) {
	return powerkit()->has_setting( $name );
}

/**
 * Alias of powerkit()->get_setting()
 *
 * @param string $name The name.
 */
function powerkit_raw_setting( $name = '' ) {
	return powerkit()->get_setting( $name );
}

/**
 * Alias of powerkit()->update_setting()
 *
 * @param string $name The name.
 * @param mixed  $value The value.
 */
function powerkit_update_setting( $name, $value ) {

	return powerkit()->update_setting( $name, $value );
}

/**
 * Alias of powerkit()->get_setting()
 *
 * @param string $name  The name.
 * @param mixed  $value The value.
 */
function powerkit_get_setting( $name, $value = null ) {

	// Check settings.
	if ( powerkit_has_setting( $name ) ) {
		$value = powerkit_raw_setting( $name );
	}

	// Filter.
	$value = apply_filters( "powerkit_settings_{$name}", $value );

	return $value;
}

/**
 * This function will add a value into the settings array found in the acf object
 *
 * @param string $name  The name.
 * @param mixed  $value The value.
 */
function powerkit_append_setting( $name, $value ) {

	// Vars.
	$setting = powerkit_raw_setting( $name );

	// Bail ealry if not array.
	if ( ! is_array( $setting ) ) {
		$setting = array();
	}

	// Append.
	$setting[] = $value;

	// Update.
	return powerkit_update_setting( $name, $setting );
}

/**
 * Returns data.
 *
 * @param string $name  The name.
 */
function powerkit_get_data( $name ) {
	return powerkit()->get_data( $name );
}

/**
 * Sets data.
 *
 * @param string $name  The name.
 * @param mixed  $value The value.
 */
function powerkit_set_data( $name, $value ) {
	return powerkit()->set_data( $name, $value );
}


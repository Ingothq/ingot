<?php
/**
 * Utilities for UI
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\ui;


class util {

	/**
	 * Make HTML select options
	 *
	 * @since 0.0.5
	 *
	 * @param array $options Options for selector
	 * @param string $selected Currently selected option
	 *
	 * @return string
	 */
	public static function select_options( $options, $selected ) {
		$out = '';
		foreach( $options as $value => $label ) {
			if ( $selected === $value ) {
				$is_selected = 'selected';
			}else{
				$is_selected = '';
			}

			$out .= sprintf( '<option value="%s" %s>%s</option>', esc_attr( $value ), $is_selected, esc_html( $label ) );

		}

		return $out;
	}

	/**
	 * Make HTML for eCommerce plugin select options
	 *
	 * @since 0.0.9
	 *
	 * @return string
	 */
	public static function price_test_plugin_selector() {

		return self::select_options( self::price_test_plugin_options(), 0 );

	}

	/**
	 * Make otpions array for eCommerce plugin select options
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @return string
	 */
	protected static function price_test_plugin_options() {
		$options = array(
			0 => ' -- ' . __( 'Choose', 'ingot' ) . ' -- ',
			'edd' => __( 'Easy Digital Downloads', 'ignot' ),
			'woo' => __( 'WooCommerce', 'ignot' )
		);


		foreach( $options as $value => $label ) {
			if( 0 === $value ){
				continue;
			}

			$active = ingot_check_ecommerce_active( $value );
			if( true != $active ){
				unset( $options[ $value ] );
			}

		}

		return $options;
	}

	/**
	 * Enqueue front-end scripts
	 *
	 * @since 0.0.5
	 *
	 * @param string $for
	 */
	public static function enqueue_front_end_scripts( $for = 'click') {
		if ( 'click' == $for ) {
			wp_enqueue_script( 'ingot-click-test' );
			wp_enqueue_style( 'ingot-click-test' );
		}
	}

	/**
	 * The nonce for verifying click tracking is valid.
	 *
	 * @since 0.0.6
	 *
	 * @param int $test_id Test ID
	 * @param int $sequence_id Sequence ID
	 * @param int $group_id Group ID
	 *
	 * @return string
	 */
	public static function click_nonce( $test_id, $sequence_id, $group_id  ){
		return wp_create_nonce();
		$action = self::click_nonce_action( $test_id, $sequence_id, $group_id );
		return wp_create_nonce( $action );
	}

	/**
	 * Verify a click nonce
	 *
	 * @since 0.0.6
	 *
	 * @param string $nonce The nonce
	 * @param int $test_id Test ID
	 * @param int $sequence_id Sequence ID
	 * @param int $group_id Group ID
	 *
	 * @return bool
	 */
	public static function verify_click_nonce( $nonce, $test_id, $sequence_id, $group_id ){

		return wp_verify_nonce( $nonce );
		$action = self::click_nonce_action( $test_id, $sequence_id, $group_id );

		return (bool) wp_verify_nonce( $nonce, $action );
	}

	/**
	 * The action for a click nonce
	 *
	 * @since 0.0.6
	 *
	 * @param int $test_id Test ID
	 * @param int $sequence_id Sequence ID
	 * @param int $group_id Group ID
	 *
	 * @return string
	 */
	protected static function click_nonce_action( $test_id, $sequence_id, $group_id ) {
		$action = md5( $test_id . $sequence_id . $group_id );
		return $action;

	}

}

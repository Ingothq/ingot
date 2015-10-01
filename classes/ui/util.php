<?php
/**
 * @TODO What this does.
 *
 * @package   @TODO
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\ui;


class util {

	public static function select_options( $options, $selected ) {
		$out = '';
		foreach( $options as $value => $label ) {
			if ( $selected == $value ) {
				$is_selected = 'selected';
			}else{
				$is_selected = '';
			}

			$out .= sprintf( '<option value="%s" %s>%s</option>', esc_attr( $value ), $is_selected, esc_html( $label ) );

		}

		return $out;
	}

	public static function enqueue_front_end_scripts( $for = 'click') {
		if ( 'click' == $for ) {
			wp_enqueue_script( 'ingot-click-test' );
			wp_enqueue_style( 'ingot-click-test' );
		}
	}

	public static function click_nonce( $test_id, $sequence_id, $group_id  ){
		$action = self::click_nonce_action( $test_id, $sequence_id, $group_id );
		return wp_create_nonce( $action );
	}

	public static function verify_click_nonce( $nonce, $test_id, $sequence_id, $group_id ){
		$action = $action = self::click_nonce_action( $test_id, $sequence_id, $group_id );
		$valid = wp_verify_nonce( $nonce, $action );
		return $valid;
	}

	protected static function click_nonce_action( $test_id, $sequence_id, $group_id ) {
		$action = $test_id . $sequence_id . $group_id;
		return $action;
	}

}

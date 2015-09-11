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

namespace ingot\testing;


class ingot {
	public function __construct() {

	}

	public function hooks(){
		$api_class =  "\\ingot\\testing\\api\\temp";
		add_action( 'wp_ajax_ingot_click_test', array( $api_class, 'record_click_text' ) );
		add_action( 'wp_ajax_nopriv_ingot_click_test', array( $api_class, 'record_click_text' ) );
		add_action( 'wp_enqueue_scripts', function() {
			wp_enqueue_script( 'ingot', plugin_dir_url( __FILE__ ) . '/assets/js/ingot.js', array( 'jquery'), INGOT_VER, true );
			wp_localize_script( 'ingot', 'INGOT', ingot::js_vars() );
		});
	}

	public static function increase_total( $test_id, $sequence_id ) {
		$sequence = sequence::get( $sequence_id );
		$is_a = self::is_a( $test_id, $sequence );
		switch( $is_a  ) {
			case true === $is_a  :
				$sequence[ 'a_total' ] = $sequence[ 'a_total' ] + 1;
				break;
			case false === $is_a :
				$sequence[ 'b_total' ] = $sequence[ 'b_total' ] + 1;
				break;
		}

		$updated =  sequence::save( $sequence, $sequence_id, true );
		return $updated;

	}

	public static function increase_victory( $test_id, $sequence_id ) {
		$sequence = sequence::get( $sequence_id );
		$is_a = self::is_a( $test_id, $sequence );
		switch( $is_a  ) {
			case true === $is_a  :
				$sequence[ 'a_win' ] = $sequence[ 'a_win' ] + 1;
				break;
			case false === $is_a :
				$sequence[ 'b_win' ] = $sequence[ 'b_win' ] + 1;
				break;
		}

		$updated = sequence::save( $sequence, $sequence_id, true );
		return $updated;

	}

	public static function is_a( $test_id, $sequence ) {
		if ( $test_id == $sequence[ 'a_id' ] ){
			return true;

		}elseif ( $test_id == $sequence[ 'b_id' ] ) {
			return false;

		}else{
			return new \WP_Error( 'ingot-test-squence-mismatch' );
		}

	}



	public static function choose_a( $a_chance ) {
		$val = rand( 1, 100 );
		if ( $val <= $a_chance ) {
			return true;

		}
	}

	public static function js_vars() {
		$vars = array(
			'api_url' => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
			'nonce' => wp_create_nonce( 'ingot_nonce' )
		);

		return $vars;
	}


}

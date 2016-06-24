<?php
/**
 * hanle loading premium only code
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

/**
 * Function to load up premium codes
 *
 * @since 1.1.0
 */
function ingot_premium() {
	
	/**
	 * Make sure we have destination tests
	 *
	 * @since 1.3.2
	 */
	add_filter( 'ingot_allowed_click_types', function( $types ){
		return array_merge( $types, \ingot\testing\types::destination_definition() );
	}, 0 );
	
	if( ! defined( 'INGOT_EDD_VER' ) && ingot_is_edd_active() ) {
		define( 'INGOT_EDD_VER', '1.0.0' );
		new ingot\addon\edd\add_destinations();
		new ingot\addon\edd\tracking();
	}

	if( ! defined( 'INGOT_WOO_VER' ) && ingot_is_woo_active() ) {
		define( 'INGOT_WOO_VER', '1.0.0' );
		new ingot\addon\woo\add_destinations();
		new ingot\addon\woo\tracking();

	}

	if( ! defined( 'INGOT_GIVE_VER' ) && ingot_is_give_active() ) {
		define( 'INGOT_GIVE_VER', '1.0.0' );
		new ingot\addon\woo\add_destinations();
		new ingot\addon\woo\tracking();

	}


	if( ! defined( 'INGOT_PMRPO_VER' ) ){
		define( 'INGOT_PMRPO_VER', '1.0.0' );
		if ( defined( 'PMPRO_VERSION' ) ) {
			new ingot\addon\pmpro\add_destinations();
			new ingot\addon\pmpro\tracking();
		}
	}


}

/**
 * Maybe load premium code
 *
 * @since 1.2.0
 *
 * @uses "ingot_plan_init" action
 *
 * @param $type
 * @param $object
 */
function ingot_maybe_load_premium( $type ){
	switch( $type ){
		case 'freemius' :
			if ( ingot_fs()->is_plan( 'premium' )  ){
				ingot_premium();
			}
			break;
		case 'edd' :
			//@todo
			break;
	}
}

/**
 * Set Ingot Plan
 *
 * @since 2.1.0
 */
function ingot_init_plan(){
	if( is_object( ingot_fs() ) ) {
		$type = 'freemius';
		$object = \ingot\licensing\freemius::get_instance();
	}else{
		$type = 'edd';
		$object = \ingot\licensing\license::get_instance();
	}

	/**
	 * Runs after licence plan is set
	 *
	 * @since 1.2.0
	 *
	 * @param string $type
	 * @param object $object
	 */
	do_action( 'ingot_plan_init', $type );
}

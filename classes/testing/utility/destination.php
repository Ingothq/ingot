<?php
/**
 * @TODO What this does.
 *
 * @package   @TODO
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace ingot\testing\utility;



use ingot\testing\tests\click\destination\cookie;
use ingot\testing\tests\click\destination\types;

class destination {



	public static function is_tagline( $group ){
		return helpers::v( 'is_tagline', $group[ 'meta' ], false );
	}

	public static function get_destination( $group ){
		return helpers::v( 'destination', $group[ 'meta' ], false );
	}

	public static function get_page_id( $group ){
		return helpers::v( 'page', $group[ 'meta' ], 0 );
	}

	public static function conversion( $group_id ){
		$variant_id = cookie::get_cookie( $group_id );
		if( is_numeric( $variant_id ) ){
			ingot_register_conversion( $variant_id );
		}

	}

	public static function prepare_meta( $group ){
		if( ! isset( $group[ 'meta' ] ) || ! is_array( $group[ 'meta' ] ) ){
			$group[ 'meta' ] = [];
		}

		$meta = $group[ 'meta' ];

		if( ! isset( $meta[ 'destination' ] ) || ! types::allowed_destination_type( $meta[ 'destination' ] ) ){
			return new \WP_Error( 'ingot-invalid-destination-type', __( 'Invalid destination', 'ingot' ),
				[
					'destination' => helpers::v( 'destination', $meta, false ),
					'meta' => $meta
				]);
		}

		if ( 'page' == $meta[ 'destination' ] ) {

			if ( ! isset( $meta[ 'page' ] ) ) {
				return new \WP_Error( 'ingot-invalid-destination-page', __( 'Page destination types need a page ID', 'ingot' ) );
			} else {
				$meta[ 'page' ] = absint( $meta[ 'page' ] );
			}
		}

		if( ! isset( $meta[ 'is_tagline' ] ) ){
			$meta[ 'is_tagline' ] = false;
		}

		$meta[ 'is_tagline' ] = (bool) $meta[ 'is_tagline' ];



		$group[ 'meta' ] = $meta;

		return $group;

	}


	public static function hooks(){
		$hooks = [
			'template_redirect' => null
		];

		if( ingot_is_edd_active() ) {
			$hooks[ 'edd_post_add_to_cart' ] = null;
			$hooks[ 'edd_complete_purchase' ] = null;
		}

		if( ingot_is_woo_active() ) {
			$hooks[ 'woocommerce_add_to_cart' ] = null;
			$hooks[ 'woocommerce_payment_complete_order_status' ] = null;
		}

		return apply_filters( 'ingot_destination_hooks', $hooks );
	}



}

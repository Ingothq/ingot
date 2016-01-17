<?php
/**
 * Utility function for groups
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace ingot\testing\utility;


class group {

	/**
	 * Get group type
	 *
	 * @since 1.1.0
	 *
	 * @param array $group Group config
	 *
	 * @return string|bool Type or false if $group is not valid group config
	 */
	public static function type( array $group ){
		if( \ingot\testing\crud\group::valid( $group ) ){
			return $group[ 'type' ];
		}

	}

	/**
	 * Get group sub type
	 *
	 * @since 1.1.0
	 *
	 * @param array $group Group config
	 *
	 * @return string|bool Type or false if $group is not valid group config
	 */
	public static function sub_type( array $group ){
		if( \ingot\testing\crud\group::valid( $group ) ){
			return $group[ 'sub_type' ];
		}

	}

	/**
	 * Get total itterations and possible conversions for a group
	 *
	 * @since 1.1.0
	 *
	 * @param array  $group Group config array
	 * @param bool $return_conversions Optional. To count conversions as well. Default is false
	 *
	 * @return array|int Total iterations as an integer or both iterations and conversions in an array
	 */
	public static function get_total( array $group, $return_conversions = false ){
		$total = $conversions = 0;
		if( \ingot\testing\crud\group::valid( $group )){
			$levers = helpers::v( 'levers', $group, [] );
			if ( ! empty( $levers ) ) {

				foreach ( $levers[ $group[ 'ID' ] ] as $lever ) {
					if ( is_object( $lever ) && method_exists( $lever, 'getDenominator' ) ) {
						$total += $lever->getDenominator();
						if( $return_conversions ){
							$conversions += $lever->getNumerator();
						}

					}
				}

			}

		}

		if( ! $return_conversions ){
			return $total;
		}else{
			return [
				'total' => $total,
				'conversion' => $conversions,
			];
		}

	}

}

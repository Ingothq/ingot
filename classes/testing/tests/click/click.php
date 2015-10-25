<?php
/**
 * Handles tracking for a click test
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\tests\click;

use ingot\testing\crud\group;
use ingot\testing\crud\price_group;
use ingot\testing\crud\sequence;
use ingot\testing\crud\tracking;
use ingot\testing\options;
use ingot\testing\tests\flow;

//@todo rename this class and move it to flow namespace
class click {


	/**
	 * Create initial sequence for group
	 *
	 * @since 0.0.6
	 *
	 * @param int $group_id The Group ID
	 * @param bool $price Optional. If is price group. Use false, the default, for click groups.
	 *
	 * @return array|bool|\WP_Error Array (group config) if succesful, false if not, and WP_Error if input invalid.
	 */
	public static function make_initial_sequence( $group_id, $price = false ){
		if ( false == $price ) {
			$group = group::read( $group_id );
			if ( empty( $group['order'] ) || ! isset( $group['order'][0] ) || ! isset( $group['order'][1] ) ) {
				return new \WP_Error( 'bad-order-for-sequence-creation', __( 'Can not make sequences for group', 'ingot' ) );
			}

			$order_key = 'order';
			$test_type = 'click';
		}else{
			$group = price_group::read( $group_id );
			if ( empty( $group[ 'test_order' ] ) || ! isset( $group[ 'test_order' ][0] ) || ! isset( $group[ 'test_order' ][1] ) ) {
				return new \WP_Error( 'bad-order-for-sequence-creation', __( 'Can not make sequences for group', 'ingot' ) );
			}
			$order_key = 'test_order';
			$test_type = 'price';
		}

		if ( ! empty( $group ) ){
			$order = $group[ $order_key ];
			$sequence_args = array(
				'test_type' => $test_type,
				'a_id' => $order[0],
				'b_id' => $order[1],
				'initial' => $group[ 'initial' ],
				'threshold' => $group[ 'threshold' ],
				'group_ID' => (int) $group[ 'ID' ]
			);
			$sequence = sequence::create( $sequence_args );
			if( $sequence ) {
				$group[ 'sequences' ] = array( $sequence );
				$group[ 'current_sequence' ] = $sequence;
			}

			if ( false == $price ) {
				$group_id = group::update( $group, $group_id );
			} else {
				$group_id = price_group::update( $group, $group_id );
			}

			return $group_id;

		}

	}

	public static function make_next_sequence( $group_id, $last_winner_id, $group ) {
		if ( ! empty( $group ) ) {
			$current_sequence_id = $group[ 'current_sequence' ];
			$current_sequence = sequence::read( $current_sequence_id);
			$a = $current_sequence[ 'a_id' ];
			$b = $current_sequence[ 'b_id' ];
			if( is_wp_error( flow::is_a( $a, $current_sequence ) ) || is_wp_error( flow::is_a( $b, $current_sequence ) ) ){
				return new \WP_Error( 'invalid-victor' );
			}

			$completed = sequence::complete( $current_sequence_id );

			if ( 'click' == $group[ 'type' ] ) {
				$type = 'click';
				$order_key = 'order';
			}else{
				$type = 'price';
				$order_key = 'test_order';
			}

			$a_key = array_search( $a, $group[ $order_key] );
			$b_key = array_search( $b, $group[ $order_key ] );
			if( $a_key > $b_key ) {
				$next_key = $a_key;
			}else{
				$next_key = $b_key + 1;
			}


			if( isset( $group[ $order_key ][ $next_key ] ) ) {
				$data = array(
					'a_id' => $last_winner_id,
					'b_id' => $group[ $order_key ][ $next_key ],
					'test_type' => $type,
					'initial' => $group[ 'initial' ],
					'threshold' => $group[ 'threshold' ],
					'completed' => 0,
					'group_ID' => $group_id
				);


				$new_sequence = sequence::create( $data );
				$group[ 'sequences' ][] = $new_sequence;
				$group[ 'current_sequence' ] = $new_sequence;
				if ( 'click' == $group[ 'type' ] ) {
					$updated = group::update( $group, $group_id );
				}else{
					$updated = price_group::update( $group, $group_id );
				}

				return $new_sequence;

			}

		}

	}



}

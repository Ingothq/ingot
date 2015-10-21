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
use ingot\testing\crud\sequence;
use ingot\testing\crud\tracking;
use ingot\testing\options;
use ingot\testing\tests\flow;

class click {


	/**
	 * Create initial sequence for group
	 *
	 * @since 0.0.6
	 *
	 * @param int $group_id The Group ID
	 *
	 * @return array|bool|\WP_Error Array (group config) if succesful, false if not, and WP_Error if input invalid.
	 */
	public static function make_initial_sequence( $group_id ){
		$group = group::read( $group_id );
		if( empty( $group[ 'order' ] ) || ! isset( $group[ 'order' ][0]) || ! isset( $group[ 'order' ][1] ) ) {
			return new \WP_Error( 'bad-order-for-sequence-creation', __( 'Can not make sequences for group', 'ingot' ) );
		}

		if ( ! empty( $group ) ){
			$order = $group[ 'order' ];
			$sequence_args = array(
				'test_type' => $group[ 'type' ],
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

			return group::update( $group, $group_id );

		}

	}

	public static function make_next_sequence( $group_id, $last_winner_id ) {
		$group = group::read( $group_id );
		if ( ! empty( $group ) ) {
			$current_sequence_id = $group[ 'current_sequence' ];
			$current_sequence = sequence::read( $current_sequence_id);
			$a = $current_sequence[ 'a_id' ];
			$b = $current_sequence[ 'b_id' ];
			if( is_wp_error( flow::is_a( $a, $current_sequence ) ) || is_wp_error( flow::is_a( $b, $current_sequence ) ) ){
				return new \WP_Error( 'invalid-victor' );
			}

			$completed = sequence::complete( $current_sequence_id );
			$a_key = array_search( $a, $group[ 'order'] );
			$b_key = array_search( $b, $group[ 'order' ] );
			if( $a_key > $b_key ) {
				$next_key = $a_key;
			}else{
				$next_key = $b_key + 1;
			}


			if( isset( $group[ 'order' ][ $next_key ] ) ) {
				$data = array(
					'a_id' => $last_winner_id,
					'b_id' => $group[ 'order' ][ $next_key ],
					'test_type' => $group[ 'type' ],
					'initial' => $group[ 'initial' ],
					'threshold' => $group[ 'threshold' ],
					'completed' => 0,
					'group_ID' => $group_id
				);


				$new_sequence = sequence::create( $data );
				$group[ 'sequences' ][] = $new_sequence;
				$group[ 'current_sequence' ] = $new_sequence;
				$updated = group::update( $group, $group_id );
				return $new_sequence;

			}

		}

	}



}

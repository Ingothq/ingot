<?php
/**
 * Functions for test flow
 *
 * @package  ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\tests;

use ingot\testing\crud\group;
use ingot\testing\crud\price_group;
use ingot\testing\crud\sequence;
use ingot\testing\crud\settings;
use ingot\testing\crud\tracking;
use ingot\testing\options;
use ingot\testing\tests\click\click;
use ingot\testing\utility\defaults;
use ingot\testing\utility\helpers;

class flow {

	/**
	 * Increase total times a test ran inside a sequence
	 *
	 * @since 0.0.3
	 * @since 0.0.8 In this location
	 *
	 * @param int $test_id Test ID
	 * @param int $sequence_id Sequence ID
	 *
	 * @return array|bool
	 */
	public static function increase_total( $test_id, $sequence_id ) {
		$sequence = sequence::read( $sequence_id );
		$is_a = self::is_a( $test_id, $sequence );

		if ( is_wp_error( $is_a ) ) {
			return $is_a;
		}

		switch ( $is_a ) {
			case true :
				$sequence['a_total'] = $sequence['a_total'] + 1;
				break;
			case false :
				$sequence['b_total'] = $sequence['b_total'] + 1;
				break;
		}


		$updated =  sequence::update( $sequence, $sequence_id, true );
		return $updated;

	}

	/**
	 * Increase times a victory
	 *
	 * @since 0.0.3
	 * @since 0.0.8 In this location
	 *
	 * @param $test_id
	 * @param $sequence_id
	 *
	 * @return bool|int
	 */
	public static function increase_victory( $test_id, $sequence_id ) {
		$sequence = sequence::read( $sequence_id );

		$is_a = self::is_a( $test_id, $sequence );
		if ( is_wp_error( $is_a ) ) {
			return $sequence_id;
		}

		switch( $is_a  ) {
			case true  :
				$sequence[ 'a_win' ] = $sequence['a_win'] + 1;
				$winner = $sequence[ 'a_id' ];
				$total_win = $sequence[ 'a_win' ];
				break;
			case false :
				$sequence[ 'b_win' ] = $sequence[ 'b_win' ] + 1;
				$total_win = $sequence[ 'b_win' ];
				$winner = $sequence[ 'b_id' ];
				break;
		}

		if ( 'price' == $sequence[ 'test_type' ] ) {
			$group = price_group::read( $sequence[ 'group_ID' ] );
		}else{
			$group = group::read( $sequence[ 'group_ID' ] );
		}

		$updated = sequence::update( $sequence, $sequence_id, true );
		$threshold = helpers::v( 'threshold', $group, defaults::threshold() );
		$intial = helpers::v( 'initial', $group, defaults::initial() );

		if( self::threshold_exceeded( $sequence, $threshold, $intial ) ) {
			$updated = sequence_progression::make_next_sequence( $sequence[ 'group_ID' ], $winner, $group );
		}

		self::maybe_track_click_details( $test_id, $sequence_id, $sequence );

		return $updated;

	}

	/**
	 * Check if threshold is exceeded
	 *
	 * @since 0.3.0
	 *
	 *
	 * @param array $sequence
	 * @param int $threshold Threshold percentage
	 * @param int $inital Initital number of tests to run before making caluclation.
	 *
	 * @return bool
	 */
	public static function threshold_exceeded( $sequence, $threshold , $inital ) {
		$sequence = new \ingot\testing\object\sequence( $sequence );

		if( $sequence->total < $inital ) {
			return false;
		}

		if( $sequence->a_win_percentage < $threshold || $sequence->b_win_percentage < $threshold ){
			return true;

		}

		return false;

	}

	/**
	 * Check if test is "A" or "B"
	 *
	 * @since 0.0.3
	 * @since 0.0.8 In this location
	 *
	 * @param int $test_id Test ID
	 * @param array $sequence Sequence config
	 *
	 * @return bool|\WP_Error True if is A, False is B, WP_Error if neither.
	 */
	public static function is_a( $test_id, $sequence ) {
		if ( $test_id == $sequence[ 'a_id' ] ){
			return true;

		}elseif ( $test_id == $sequence[ 'b_id' ] ) {
			return false;

		}else{
			return new \WP_Error( 'ingot-test-sequence-mismatch' );
		}

	}


	/**
	 * Choose A or B based on a given probability
	 *
	 * @since 0.0.3
	 *
	 * @param int $a_chance Chance (1-100) of selecting A
	 * @param bool $return_bool Optional. If true, the default, return is a boolean, true for A, false for B. If false, return is "a" or "b".
	 *
	 * @return bool|string Boolean or a|b representing a or b selection.
	 */
	public static function choose_a( $a_chance, $return_bool = true ) {
		$val = rand( 1, 100 );
		if ( $val <= $a_chance ) {
			if ( $return_bool ) {
				return true;
			}else{
				return 'a';
			}

		}else{
			if ( $return_bool ) {
				return false;
			}else{
				return 'b';
			}
		}

	}

	/**
	 * Track click details if required
	 *
	 * @since 0.0.8
	 *
	 * @param int $test_id
	 * @param int $sequence_id
	 * @param array $sequence
	 */
	protected static function maybe_track_click_details( $test_id, $sequence_id, $sequence ) {
		if ( settings::is_click_track_mode() ) {
			$sequence_object = new \ingot\testing\object\sequence( $sequence );
			$tracked         = tracking::create( array(
				'test_ID'     => $test_id,
				'sequence_ID' => $sequence_id,
				'group_ID'    => $sequence_object->group_ID
			) );

			tracking::read( $tracked );

		}

	}


}

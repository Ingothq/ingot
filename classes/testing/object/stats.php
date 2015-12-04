<?php
/**
 * Create a simple object for returning stats to REST API with
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\object;


class stats extends sequence {

	/**
	 * An array of stats for this sequence
	 *
	 * @since 0.3.0
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $stats;

	/**
	 * Array of properties used to make stats and key stats array
	 *
	 * @since 0.3.0
	 *
	 * @access private
	 *
	 * @var array
	 */
	protected $stats_keys = [
		'a_id',
		'b_id',
		'a_total',
		'b_total',
		'a_win',
		'b_win',
		'a_win_percentage',
		'b_win_percentage',
		'a_total_percentage',
		'b_total_percentage',
		'total',
		'win_total'
	];

	/**
	 * Get the stats for this sequence
	 *
	 * @since 0.3.0
	 *
	 * @access protected
	 *
	 * @return array
	 */
	public function get_stats() {
		if( is_null( $this->stats ) ) {
			$this->make_stats();
		}

		return $this->stats;
	}

	/**
	 * Make the stats for this sequence
	 *
	 * @since 0.3.0
	 *
	 * @access private
	 *
	 * @return array
	 */
	private function make_stats() {
		foreach( $this->stats_keys as $key ) {
			$this->stats[ $key ] = $this->$key;
		}
	}

}



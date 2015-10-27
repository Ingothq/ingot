<?php
/**
 * Calculate chance A should be selected for a sequence
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\tests;

class chance {

	/**
	 * Sequence calculation is based on
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected $sequence;

	/**
	 * Final chance 1-100
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @var int
	 */
	protected $chance;

	/**
	 * Total times this sequnce has run
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 *
	 * @var int
	 */
	protected $total;

	/**
	 * @param array $sequence
	 */
	public function __construct( $sequence ) {
		$this->set_sequence( $sequence );
		$this->calculate_total();
		$this->set_chance();
	}

	/**
	 * Get the chance 1-100 that A will be selected
	 *
	 * @return int
	 */
	public function get_chance() {
		return $this->chance;
	}

	/**
	 * Calculate chance and set in the chance property
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 */
	protected function set_chance() {
		$this->chance = 50;
		if( $this->less_than_initial() ) {
			return;
		}

		$this->chance = $this->calcualate_chance();

	}

	/**
	 * Calculate chance when above initial
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 */
	protected function calcualate_chance() {
		if ( $this->sequence['a_win'] > $this->sequence['b_win'] ) {
			if( 0 == $this->sequence['b_win'] ) {
				return 50;
			}

			$invert = true;
			$percentage = $this->sequence['b_win'] / $this->sequence['a_win'];
		} else {
			if( 0 == $this->sequence['b_win'] ) {
				return 50;
			}

			$invert = false;
			$percentage = $this->sequence['a_win'] / $this->sequence['b_win'];
		}

		$chance = (int) 100 * $percentage;

		if ( $invert ) {
			$chance = absint( 100 - $chance );
		}

		return $chance;

	}

	/**
	 * Check if initital number of tests has been exceeded yet
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @return bool
	 */
	protected function less_than_initial() {
		if( $this->sequence[ 'initial' ] > $this->total ) {
			return true;

		}

	}

	/**
	 * Set total property of this class
	 *
	 * @since 0.0.7
	 *
	 * @access private
	 */
	protected function calculate_total() {
		$this->total = $this->sequence[ 'a_total' ] + $this->sequence[ 'b_total' ];
	}

	/**
	 * Set sequence property of this class
	 *
	 * @since 0.0.7
	 *
	 * @access private
	 *
	 * @param array $sequence
	 */
	private function set_sequence( $sequence ) {
		$this->sequence = $sequence;
	}

}

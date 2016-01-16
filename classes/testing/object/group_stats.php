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


use Oefenweb\Statistics\Statistics;

class group_stats  {

	/**
	 * An array of stats for this sequence
	 *
	 * @since 0.3.0
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $stats = [
		'variants' => [],
		'group' => []
	];

	private $levers;

	public function __construct( $levers ) {
		$this->levers = $levers;
	}

	/**
	 * Calculate stats for one lever
	 *
	 * @since 0.4.0
	 *
	 * @access protected
	 *
	 * @param \MaBandit\Lever $lever
	 */
	protected function lever_stats( $lever ) {
		if ( is_object( $lever ) ) {
			$this->stats[ 'variants' ][ $lever->getValue() ] = new stats(
				$lever->getDenominator(),
				$lever->getNumerator(),
				$lever->getConversionRate()
			);
		}
	}

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
		$this->make_stats();
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
		if ( ! empty( $this->levers ) ) {
			foreach ( $this->levers as $lever ) {
				$this->lever_stats( $lever );
			}
		}

		if( ! empty( $this->stats[ 'variants' ] ) ) {
			$this->group_stats();
		}
	}

	/**
	 * Calculate stats for the whole group
	 *
	 * @since 0.4.0
	 *
	 * @access protected
	 */
	protected function group_stats() {
		$total = $conversions = 0;
		$rates = [];
		foreach(  $this->stats[ 'variants' ] as $variant ) {
			$total = $total + $variant->total;
			$conversions = $conversions + $variant->conversions;
			$rates[] = $variant->conversion_rate;
		}

		$avg_conversion_rate = Statistics::mean( $rates );
		if( 0 == $total ) {
			$conversion_rate = 0;
		}else{
			$conversion_rate = $conversions / $total;
		}
		//$std_deviation = Statistics::standardDeviation( $rates );

		$this->stats[ 'group' ] = new stats( $total, $conversions,  $conversion_rate, $avg_conversion_rate );
	}

}



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

namespace ingot\testing\object;


class stats {

	public $total;

	public $conversions;

	public $conversion_rate;

	public $average_conversion_rate;

	public function __construct( $total, $conversions, $conversion_rate = null, $average_conversion_rate = null ){
		$this->total = $total;
		$this->conversions = $conversions;
		if( is_null( $conversion_rate ) ) {
			if(0 == $total ) {
				$conversion_rate = 0;
			}else{
				$conversion_rate = $conversions / $total;
			}

		}

		$this->conversion_rate = $conversion_rate;

		$this->average_conversion_rate = $average_conversion_rate;

	}

}

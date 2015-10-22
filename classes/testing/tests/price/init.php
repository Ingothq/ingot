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

namespace ingot\testing\tests\price;


class init {

	private $tests;

	public function  __construct() {
		$this->set_tests();
		if( is_array( $this->tests ) && ! empty( $this->tests ) ) {
			foreach( $this->tests as $plugin => $tests ) {
				$function = "ingot_is_{$plugin}_active";
				if( function_exists( $function ) && true == call_user_func( $function ) ) {
					$class = "ingot\\testing\\tests\\" . $plugin;
					if ( class_exists( $class ) ) {
						foreach( $tests as $test ) {
							new $class( $test );
						}
					}

				}

			}

		}

	}

	public function get_tests() {
		return $this->tests;
	}

	protected function has_price_tests() {

	}

	protected function set_tests() {

	}
}

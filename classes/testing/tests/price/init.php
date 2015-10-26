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


use ingot\testing\crud\price_test;
use ingot\testing\utility\helpers;

class init {

	private $tests;

	public function  __construct( $tests ) {
		$this->tests;

	}

	protected function setup_tests() {
		if( ! empty( $this->tests ) ){
			foreach( $this->tests as $test_detail ){
				$test = price_test::read( helpers::v( 'test_id', $test_detail, 0 ) );
				if( is_array( $test ) ){
					$plugin = helpers::v( 'plugin', 'test', 0 );
					if( is_string( $plugin ) && ingot_acceptable_plugin_for_price_test( $plugin ) ){
						$class_name = $this->class_name( $plugin );
						if( is_callable( $class_name ) ){
							new $class_name( $test, helpers::v( 'a_or_b', $test_detail, 'a' ) );
						}
					}

				}

			}

		}

	}

	protected function class_name( $plugin ){
		return "ingot\\testing\\tests\\price\\plugins\\" . $plugin;
	}


}

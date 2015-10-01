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

namespace ingot\testing;


class test_types {

	private $tests;

	private static $instance;

	private function __construct() {
		$this->set_tests();
	}

	public function get_test_types() {
		return $this->tests;
	}


	protected function set_tests() {
		$this->tests = apply_filters( 'ingot_tests_types', $this->internal() );
	}


	protected function internal() {
		$internal_tests = array(
			'click' => array(
				'click',
				'text',
				'button'
			),
			'price' => array(
				'edd',
				'woo'
			)
		);

		return $internal_tests;
	}

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}

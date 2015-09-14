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

namespace ingot\testing\tests\click;


use ingot\testing\test_types;

class click {

	private $tests;

	private static $instance;

	private function __construct() {
		$this->set_tests();
	}

	public function get_test_types() {
		return $this->tests;
	}


	protected function set_tests() {
		$this->tests = apply_filters( 'ingot_click_tests_types', $this->internal() );
	}


	protected function internal() {
		$internal = test_types::instance()->get_test_types();
	}

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


}

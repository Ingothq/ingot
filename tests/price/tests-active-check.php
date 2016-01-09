<?php
/**
 * Test functions that test if other plugins are active
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */
class test_active_check extends \WP_UnitTestCase {

	/**
	 * Test EDD is active
	 *
	 * @since 1.1.0
	 *
	 * @group price
	 * @group edd
	 * @group functions
	 *
	 * @covers ingot_is_edd_active()
	 * @covers ingot_check_ecommerce_active()
	 */
	public function testEDDActive(){
		if ( class_exists( 'Easy_Digital_Downloads' ) ) {
			$this->assertTrue( ingot_is_edd_active() );
			$this->assertTrue( ingot_check_ecommerce_active( 'edd' ) );
		}else{
			$this->assertFalse( ingot_is_edd_active() );
			$this->assertFalse( ingot_check_ecommerce_active( 'edd' ) );
		}

	}

	/**
	 * Test WOO is active
	 *
	 * @since 1.1.0
	 *
	 * @group price
	 * @group woo
	 * @group functions
	 *
	 * @covers ingot_is_woo_active()
	 * @covers ingot_check_ecommerce_active()
	 */
	public function testWOOActive(){
		if ( class_exists( 'WooCommerce' ) ) {
			$this->assertTrue( ingot_is_woo_active() );
			$this->assertTrue( ingot_check_ecommerce_active( 'woo' ) );
		}else{
			$this->assertFalse(ingot_is_woo_active() );
			$this->assertFalse( ingot_check_ecommerce_active( 'woo' ) );
		}

	}

}

<?php
/**
 * Situational tests to test that everything works together properly
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

class tests_situational extends \WP_UnitTestCase {

	/**
	 *
	 * @group group
	 * @group variant
	 * @group situational
	 */
	public function testClickButton(){
		$groups = ingot_tests_data::click_button_group( true, 1, 2 );
		$group_id = $groups['ids'][0];
		$variants = $groups[ 'variants' ];
		foreach( $variants as $variant_id ){
			$variant = \ingot\testing\crud\variant::read( $variant_id );
			$this->assertSame( $variant[ 'group_id' ],$group_id );
		}
	}
}

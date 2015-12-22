<?php
/**
 * Test UI rendering
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
class tests_ui extends \WP_UnitTestCase {

	/**
	 * Test that click button tests render properly
	 *
	 * @since 0.4.0
	 *
	 * @group group
	 * @group front_ui
	 *
	 */
	public function testClickButton(){
		$groups = ingot_tests_data::click_button_group( true, 1, 3 );
		$this->check_render( $groups );
	}

	/**
	 * Test that click button_color tests render properly
	 *
	 * @since 0.4.0
	 *
	 * @group group
	 * @group front_ui
	 *
	 */
	public function testClickButtonColor(){
		$groups = ingot_tests_data::click_button_color_group( true, 1, 3 );
		$this->check_render( $groups );
	}

	/**
	 * Test that click link tests render properly
	 *
	 * @since 0.4.0
	 *
	 * @group group
	 * @group front_ui
	 *
	 */
	public function testClickLink(){
		$groups = ingot_tests_data::click_link_group( true, 1, 3 );
		$this->check_render( $groups );
	}

	/**
	 * Check the rendering of a test
	 *
	 * @since 0.4.0
	 *
	 * @param $groups
	 */
	protected function check_render( $groups ) {
		$render = new \ingot\ui\render\click_tests\button( $groups[ 'ids' ][ 0 ] );
		$chosen = $render->get_chosen_variant_id();
		$this->assertTrue( is_numeric( $chosen ) );
		$this->assertTrue( in_array( (int) $chosen, $groups[ 'variants' ][ $groups[ 'ids' ][ 0 ] ] ) );
		$html = $render->get_html();
		$this->assertInternalType( 'string', $html );
		$this->assertNotEquals( 0, strlen( $html ) );
	}
}

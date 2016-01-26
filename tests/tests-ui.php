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
		$this->check_render( $groups, __METHOD__ );
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
		$this->check_render( $groups, __METHOD__  );
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
		$this->check_render( $groups, __METHOD__  );
	}

	/**
	 * Loop through a few renders to make sure nothing gets wonky -- IE new levers being created or something
	 *
	 * @since 0.4.0
	 *
	 * @group bandit
	 * @group group
	 * @group front_ui
	 *
	 */
	public function testMultiple(){
		$groups = ingot_tests_data::click_link_group( true, 1, 3 );
		$group_id = $groups[ 'ids' ][ 0 ];
		$variants = $groups[ 'variants' ][ $groups[ 'ids' ][ 0 ] ];

		for ( $i = 0; $i <= 5; $i++  ) {
			$this->check_render( $groups, __METHOD__  );
			$group = \ingot\testing\crud\group::read( $group_id );
			$levers = $group[ 'levers' ];
			$this->assertInternalType( 'array', $levers );
			$this->assertFalse( empty( $levers ) );
			$this->assertArrayHasKey( $group_id, $levers );
			foreach( $variants as $variant_id ){
				$this->assertArrayHasKey( $variant_id, $levers[ $group_id ], 'iteration: ' . $i );
			}

		}

	}

	/**
	 * Test conversion
	 *
	 * @todo move this test to a better place
	 *
	 * @since 0.4.0
	 *
	 * @group bandit
	 * @group group
	 * @group front_ui
	 *
	 * @covers ingot_register_conversion()
	 */
	public function testConversion(){
		$groups = ingot_tests_data::click_link_group( true, 1, 3 );
		$chosen = $this->check_render( $groups, __METHOD__ );
		$group_id = $groups[ 'ids' ][ 0 ];

		ingot_register_conversion($chosen);
		$levers = \ingot\testing\crud\group::get_levers( $group_id );
		/** @var \MaBandit\Lever $chosen_lever */
		$chosen_lever = $levers[ $group_id ][ $chosen ];

		$this->assertSame( 1, $chosen_lever->getNumerator() );
		$this->assertSame( 1, $chosen_lever->getDenominator() );
		/** @var \MaBandit\Lever $lever */
		foreach( $levers[ $group_id ] as $id =>  $lever ){
			if ( $chosen != $id ) {
				$this->assertSame( 0, $lever->getNumerator() );
				$this->assertSame( 0, $lever->getDenominator() );
			}
		}


	}

	/**
	 * Test multiple conversions
	 *
	 * @todo move this test to a better place
	 *
	 * @since 0.4.0
	 *
	 * @group bandit
	 * @group group
	 * @group front_ui
	 *
	 * @covers ingot_register_conversion()
	 */
	public function testMultipleConversions(){
		$groups = ingot_tests_data::click_link_group( true, 1, 3 );
		$group_id = $groups[ 'ids' ][ 0 ];
		$variants = $groups[ 'variants' ][ $groups[ 'ids' ][ 0 ] ];
		$expected = [];
		foreach( $variants as $variant ){
			$expected[ $variant ] = [ 'n' => 0 , 'd' => 0 ];
		}

		for ( $i = 0; $i <= 25; $i++  ) {
			$chosen   = $this->check_render( $groups, __METHOD__  );
			if ( in_array( $i, [ 2,3,5,8,13,21 ]) ) {
				ingot_register_conversion( $chosen );
				$expected[ $chosen ][ 'n' ] = $expected[ $chosen ][ 'n' ] + 1;
			}
			$expected[ $chosen ][ 'd' ] = $expected[ $chosen ][ 'd' ] + 1;
			$levers = \ingot\testing\crud\group::get_levers( $group_id );
			/** @var \MaBandit\Lever $lever */
			foreach ( $levers[ $group_id ] as $id => $lever ) {
				if ( $chosen != $id ) {
					$this->assertSame( $expected[ $id ][ 'n' ], $lever->getNumerator(), $id . '-' . $lever->getNumerator() );
					$this->assertSame( $expected[ $id ][ 'd' ], $lever->getDenominator(), $id );
				}
			}
		}


	}

	/**
	 * Check the rendering of a test
	 *
	 * @since 0.4.0
	 *
	 * @param $groups
	 *
	 * @return int ID of chosen variant
	 */
	protected function check_render( $groups, $test_name ) {
		$render = new \ingot\ui\render\click_tests\button( $groups[ 'ids' ][ 0 ] );
		$chosen = $render->get_chosen_variant_id();
		$this->assertTrue( is_numeric( $chosen ), $test_name );
		if( ! in_array( $chosen, $groups[ 'variants' ][ $groups[ 'ids' ][ 0 ] ] ) ){
			var_dump( $chosen,$groups[ 'variants' ][ $groups[ 'ids' ][ 0 ] ] );die();
		}
		$this->assertTrue( in_array( $chosen, $groups[ 'variants' ][ $groups[ 'ids' ][ 0 ] ], $test_name) );
		$html = $render->get_html();
		$this->assertInternalType( 'string', $html, $test_name );
		$this->assertNotEquals( 0, strlen( $html ), $test_name );

		return $chosen;

	}

}

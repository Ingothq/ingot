<?php
/**
 * Test destination test conversion tracking
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */
class tests_destination_tracking extends \WP_UnitTestCase{

	/**
	 * Test setting up tagline tests
	 *
	 * @since 1.1.0
	 *
	 * @group group
	 * @group destination
	 *
	 * @covers \ingot\testing\tests\click\destination\init::set_tracking()
	 */
	public function testTagline(){
		ingot_test_desitnation::create('page', false );
		$args = [
			'name' => rand(),
			'type'     => 'click',
			'sub_type' => 'destination',
			'meta'     => [
				'destination' => 'page',
				'link' => 'https://bats.com',
				'page' => 11,
				'is_tagline' => true
			],
		];

		$group_id = \ingot\testing\crud\group::create( $args, true  );
		$group = \ingot\testing\crud\group::read( $group_id );
		$this->assertTrue( \ingot\testing\crud\group::valid( $group ) );

		$content_1 = 'hats';
		$args = [
			'type'     => 'click',
			'group_ID' => $group_id,
			'content'  => $content_1
		];
		$variant_1 = \ingot\testing\crud\variant::create( $args, true );

		$content_2 = 'bats';
		$args = [
			'type'     => 'click',
			'group_ID' => $group_id,
			'content'  => $content_2
		];

		$variant_2 = \ingot\testing\crud\variant::create( $args, true );

		$group[ 'variants' ] = [ $variant_1, $variant_2 ];
		\ingot\testing\crud\group::update( $group, $group_id, true );
		\ingot\testing\tests\click\destination\init::set_tracking();
		$this->assertTrue( in_array( \ingot\testing\tests\click\destination\init::get_test( $group_id ), [ $variant_1, $variant_2 ]  ) );
		$variant_id =  \ingot\testing\tests\click\destination\init::get_test( $group_id );
		$variant = \ingot\testing\crud\variant::read( $variant_id );
		$tagline = $variant[ 'content' ];
		$this->assertSame( $tagline, get_bloginfo( 'description', 'display' ) );

	}

	/**
	 *
	 *
	 * @since 1.1.0
	 *
	 * @group one
	 * @group group
	 * @group destination
	 *
	 * @covers  \ingot\testing\tests\click\destination\hooks::track_by_id()
	 */
	public function testPageTracking(){
		$post = get_post( wp_insert_post( [ 'post_title' => 'Hi Chris' ] ) );

		$args = [
			'name' => rand(),
			'type'     => 'click',
			'sub_type' => 'destination',
			'meta'     => [
				'destination' => 'page',
				'link' => 'https://bats.com',
				'page' => 11,
				'is_tagline' => true
			],
		];

		$group_id = \ingot\testing\crud\group::create( $args, true );
		$group = \ingot\testing\crud\group::read( $group_id );

		$content_1 = 'hats';
		$args = [
			'type'     => 'click',
			'group_ID' => $group_id,
			'content'  => $content_1
		];
		$variant_1 = \ingot\testing\crud\variant::create( $args, true );

		$content_2 = 'bats';
		$args = [
			'type'     => 'click',
			'group_ID' => $group_id,
			'content'  => $content_2
		];

		$variant_2 = \ingot\testing\crud\variant::create( $args, true );

		$group[ 'variants' ] = [ $variant_1, $variant_2 ];
		$update = \ingot\testing\crud\group::update( $group, $group_id );
		$this->assertEquals( $group_id, $update );
		$group = \ingot\testing\crud\group::read( $group_id );
		$this->assertTrue( is_array( $group ) );
		$this->assertFalse( empty ($group[ 'variants' ] ) ) ;

		\ingot\testing\tests\click\destination\init::set_tracking();
		$this->assertTrue( in_array( \ingot\testing\tests\click\destination\init::get_test( $group_id ), [ $variant_1, $variant_2 ]  ) );
		$variant_id =  \ingot\testing\tests\click\destination\init::get_test( $group_id );

		$tracking = new \ingot\testing\tests\click\destination\hooks(  [ $group_id => $variant_id ] );
		$tracking->track_by_id( 11 );

		$obj = new \ingot\testing\object\group( $group_id );
		$levers = $obj->get_levers();
		$this->assertArrayHasKey( $group_id, $levers );
		$this->assertArrayHasKey( $variant_id, $levers[ $group_id ] );
		$lever = $levers[ $group_id ][ $variant_id ];
		$this->assertInternalType( 'object', $lever );
		$this->assertEquals( 1, $lever->getNumerator() );
		$this->assertEquals( 1, $lever->getDenominator() );
	}

	/**
	 * Track EDD at to cart conversions
	 *
	 * @since 1.1.0
	 *
	 * @group group
	 * @group destination
	 * @group edd
	 *
	 * @covers  \ingot\testing\tests\click\destination\hooks::edd_post_add_to_cart()
	 * @covers  \ingot\testing\tests\click\destination\hooks::add_hooks()
	 */
	public function testEDDConversionsCart(){
		if( ingot_is_edd_active() ){
			$this->assertTrue( \ingot\testing\tests\click\destination\types::allowed_destination_type( 'cart_edd' ) );
			$product = ingot_test_data_price::edd_create_simple_download( 10 );
			$data  = ingot_test_desitnation::create( 'cart_edd' );
			$group_id = $data[ 'group_ID' ];
			$variants = $data[ 'variants' ];
			$this->assertInternalType( 'array', $variants  );
			$this->assertFalse( empty( $variants ) );
			$variant_id = \ingot\testing\tests\click\destination\init::get_test( $group_id );
			$hooks = new \ingot\testing\tests\click\destination\hooks( [ $group_id => $variant_id ]);

			$hooks->edd_post_add_to_cart();
			$obj = new \ingot\testing\object\group( $group_id );
			$levers = $obj->get_levers();
			$this->assertArrayHasKey( $group_id, $levers );
			$this->assertArrayHasKey( $variant_id, $levers[ $group_id ] );
			$lever = $levers[ $group_id ][ $variant_id ];
			$this->assertInternalType( 'object', $lever );
			$this->assertEquals( 1, $lever->getNumerator() );
			$this->assertEquals( 1, $lever->getDenominator() );

			edd_add_to_cart( $product->ID );
			$obj = new \ingot\testing\object\group( $group_id );
			$levers = $obj->get_levers();
			$this->assertArrayHasKey( $group_id, $levers );
			$this->assertArrayHasKey( $variant_id, $levers[ $group_id ] );
			$lever = $levers[ $group_id ][ $variant_id ];
			$this->assertInternalType( 'object', $lever );
			$this->assertEquals( 2, $lever->getNumerator() );
			$this->assertEquals( 3, $lever->getDenominator() );

		}

	}

	/**
	 * Track EDD sale conversions
	 *
	 * @since 1.1.0
	 *
	 * @group group
	 * @group destination
	 * @group edd
	 *
	 * @covers  \ingot\testing\tests\click\destination\hooks::edd_complete_purchase()
	 * @covers  \ingot\testing\tests\click\destination\hooks::add_hooks()
	 */
	public function testEDDConversionsSale(){
		if( ingot_is_edd_active() ){
			$this->assertTrue( \ingot\testing\tests\click\destination\types::allowed_destination_type( 'sale_edd' ) );
			$product = ingot_test_data_price::edd_create_simple_download( 10 );
			$data  = ingot_test_desitnation::create( 'sale_edd' );
			$group_id = $data[ 'group_ID' ];


			$this->assertTrue( is_numeric( $group_id ) );
			$variants = $data[ 'variants' ];

			$variant_id = \ingot\testing\tests\click\destination\init::get_test( $group_id );
			$this->assertTrue( is_numeric( $variant_id  ) );

			\ingot\testing\tests\click\destination\init::set_tracking();

			$this->assertTrue( in_array( \ingot\testing\tests\click\destination\init::get_test( $group_id ), $variants  ) );
			$variant_id =  \ingot\testing\tests\click\destination\init::get_test( $group_id );

			$hooks = new \ingot\testing\tests\click\destination\hooks( [ $group_id => $variant_id ]);
			$hooks->edd_complete_purchase();
			$obj = new \ingot\testing\object\group( $group_id );
			$levers = $obj->get_levers();
			$this->assertArrayHasKey( $group_id, $levers );
			$this->assertArrayHasKey( $variant_id, $levers[ $group_id ] );
			$lever = $levers[ $group_id ][ $variant_id ];
			$this->assertInternalType( 'object', $lever );
			$this->assertEquals( 1, $lever->getNumerator() );
			$this->assertEquals( 1, $lever->getDenominator() );

			$payment_id = ingot_test_data_price::edd_create_simple_payment( $product);
			edd_complete_purchase( $payment_id, 'publish', 'pending' );

			$obj = new \ingot\testing\object\group( $group_id );
			$levers = $obj->get_levers();
			$this->assertArrayHasKey( $group_id, $levers );
			$this->assertArrayHasKey( $variant_id, $levers[ $group_id ] );
			$lever = $levers[ $group_id ][ $variant_id ];
			$this->assertInternalType( 'object', $lever );
			$this->assertEquals( 2, $lever->getNumerator() );
			$this->assertEquals( 3, $lever->getDenominator() );

		}

	}


}


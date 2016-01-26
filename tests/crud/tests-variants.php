<?php
/**
 * Test for tracking table CRUD
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
class tests_variants extends \WP_UnitTestCase {


	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
		\ingot\testing\crud\variant::delete( 'all' );
	}

	/**
	 * Test that table name is right
	 *
	 * @since 0.4.0
	 *
	 * @group crud
	 * @group variant_crud
	 *
	 * @covers \ingot\testing\crud\variant::get_table_name()
	 */
	public function testTableName() {
		$tablename = \ingot\testing\crud\variant::get_table_name();
		global $wpdb;
		$this->assertEquals( $wpdb->prefix . 'ingot_variant', $tablename );
	}

	/**
	 * Test create
	 *
	 * @since 0.4.0
	 *
	 * @group crud
	 * @group variant_crud
	 *
	 * @covers \ingot\testing\crud\variant::create()
	 */
	public function testCreateMinimal() {
		$params = [
			'type'     => 'click',
			'group_ID' => rand(),
			'content' => rand()
		];

		$created = \ingot\testing\crud\variant::create( $params );
		$this->assertTrue( is_int( $created ) );
		$this->assertFalse( is_wp_error( $created ) );
		$this->assertTrue( is_numeric( $created ) );

	}

	/**
	 * Test invalid type -- must make WP_Error
	 *
	 * @since 0.4.0
	 *
	 * @group crud
	 * @group variant_crud
	 *
	 * @covers \ingot\testing\crud\variant::create()
	 * @covers \ingot\testing\crud\variant::validate_config()
	 */
	public function testInvalidType() {
		$params = [
			'type'     => 'josh',
			'group_ID' => rand(),
			'content' => rand()
		];

		$created = \ingot\testing\crud\variant::create( $params );
		$this->assertWPError( $created );

	}

	/**
	 * Test invalid group -- must make WP_Error
	 *
	 * @since 0.4.0
	 *
	 * @group crud
	 * @group variant_crud
	 *
	 * @covers \ingot\testing\crud\variant::create()
	 * @covers \ingot\testing\crud\variant::validate_config()
	 */
	public function testInvalidGroup() {
		$params = [
			'type'     => 'click',
			'group_ID' => 'l',
			'content' => rand()
		];

		$created = \ingot\testing\crud\variant::create( $params );
		$this->assertWPError( $created );

	}

	/**
	 * Test invalid content for a price test -- must make WP_Error
	 *
	 * @since 0.4.0
	 *
	 * @group crud
	 * @group variant_crud
	 *
	 * @covers \ingot\testing\crud\variant::create()
	 * @covers \ingot\testing\crud\variant::validate_config()
	 */
	public function testInvalidContent() {
		$params = [
			'type'     => 'price',
			'group_ID' => '42',
			'content' => 'lorem ipsum sandwich'
		];

		$created = \ingot\testing\crud\variant::create( $params );
		$this->assertWPError( $created );

	}

	/**
	 * Test reading
	 *
	 * @since 0.4.0
	 *
	 * @group crud
	 * @group variant_crud
	 *
	 * @covers \ingot\testing\crud\variant::read()
	 */
	public function testRead(){

		$time = current_time( 'mysql' );
		$params = [
			'type'     => 'click',
			'group_ID' => '42',
			'content'  => '12345',
			'levers'    => [rand(),rand()],
			'created'  => $time,
			'modified' => $time,
			'meta' => [rand(),rand()]
		];

		$created = \ingot\testing\crud\variant::create( $params );
		$this->assertTrue( is_int( $created ) );
		$variant = \ingot\testing\crud\variant::read( $created );
		$this->assertEquals( $created, $variant[ 'ID' ] );
		foreach( \ingot\testing\crud\variant::get_all_fields() as $field ){
			$this->assertArrayHasKey( $field, $variant );
			if ( 'levers' != $field ) {
				$this->assertEquals( $variant[ $field ], $params[ $field ], $field );
			}
		}

	}

	/**
	 * Test that data is filled in properly
	 *
	 *
	 * @since 0.4.0
	 *
	 * @group crud
	 * @group variant_crud
	 *
	 * @covers \ingot\testing\crud\variant::fill_in()
	 */
	public function testFillIn(){
		$params = [
			'type'     => 'click',
			'group_ID' => rand(),
			'content' => rand()
		];

		$created = \ingot\testing\crud\variant::create( $params );
		$this->assertTrue( is_int( $created ) );
		$variant = \ingot\testing\crud\variant::read( $created );
		$this->assertEquals( $created, $variant[ 'ID' ] );

		foreach( \ingot\testing\crud\variant::get_all_fields() as $field ){
			$this->assertArrayHasKey( $field, $variant );
		}
	}

	/**
	 * Test that data is filled in properly
	 *
	 *
	 * @since 0.4.0
	 *
	 * @group crud
	 * @group variant_crud
	 *
	 * @covers \ingot\testing\crud\update::fill_in()
	 */
	public function testUpdate() {
		$params = [
			'type'     => 'click',
			'group_ID' => rand(),
			'content'  => rand()
		];
		$created = \ingot\testing\crud\variant::create( $params );
		$variant = \ingot\testing\crud\variant::read( $created );
		$variant[ 'content' ] = 'I AM THE BATMAN';
		\ingot\testing\crud\variant::update( $variant, $created );
		$variant = \ingot\testing\crud\variant::read( $created );
		$this->assertFalse( is_wp_error( $variant ) );
		$this->assertSame('I AM THE BATMAN', $variant[ 'content' ] );

	}

	/**
	 * Test that we can delete a group
	 *
	 * @since 0.4.0
	 *
	 * @group crud
	 * @group variant_crud
	 *
	 * @covers \ingot\testing\crud\variant::delete()
	 */
	public function testDelete() {
		$params = [
			'type'     => 'click',
			'group_ID' => rand(),
			'content'  => rand()
		];
		$created = \ingot\testing\crud\variant::create( $params );

		\ingot\testing\crud\variant::delete( $created );
		$variant = \ingot\testing\crud\variant::read( $created );
		$this->assertFalse( is_array( $variant ) );

	}

	/**
	 * Test that we can delete all variants
	 *
	 * @since 0.4.0
	 *
	 * @group crud
	 * @group variant_crud
	 *
	 * @covers \ingot\testing\crud\variant::delete()
	 */
	public function testDeleteAll() {
		for ( $i=0; $i <= 7; $i++ ) {
			$params = [
				'type'     => 'click',
				'group_ID' => $i + rand(),
				'content'  => $i
			];
			$created[ $i ] = \ingot\testing\crud\variant::create( $params );
		}


		\ingot\testing\crud\variant::delete( 'all' );

		$items = \ingot\testing\crud\variant::get_items( array() );
		$this->assertEquals( count( $items ), 0 );

	}

	/**
	 * Test item exists method
	 *
	 * @since 1.1.0
	 *
	 * @group variant
	 * @group variant_crud
	 * @group crud
	 *
	 * @covers  \ingot\testing\crud\group::variant()
	 */
	public function testExists(){
		$params = [
			'type'     => 'click',
			'group_ID' => rand(),
			'content'  => rand()
		];
		$id = \ingot\testing\crud\variant::create( $params );

		$this->assertTrue( is_numeric( $id ) );
		$this->assertTrue( \ingot\testing\crud\variant::exists( $id ) );

		$this->assertFalse( \ingot\testing\crud\variant::exists( 99999 )  );

		\ingot\testing\crud\variant::delete( $id );
		$this->assertFalse( \ingot\testing\crud\variant::exists( $id ) );
	}

}

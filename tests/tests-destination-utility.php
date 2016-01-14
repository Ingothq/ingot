<?php
/**
 * Test destination utility functions
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */
class test_utilities extends \WP_UnitTestCase {

	/**
	 *
	 *
	 * @since 1.1.0
	 *
	 * @group group
	 * @group group_crud
	 * @group destination
	 *
	 * @covers \ingot\testing\tests\click\destination\types::allowed_destination_type()
	 * @covers \ingot\testing\utility\destination::prepare_meta()
	 */
	public function testPrepareMeta(){
		foreach( \ingot\testing\tests\click\destination\types::destination_types() as $type ){
			if ( \ingot\testing\tests\click\destination\types::allowed_destination_type( $type ) ) {
				$args = ingot_test_desitnation::group_args( $type );
				$id   = \ingot\testing\crud\group::create( $args );
				$this->assertTrue( is_numeric( $id ) );
				$group = \ingot\testing\crud\group::read( $id );
				$this->assertInternalType( 'array', $group );

				$this->assertTrue( $this->verify_meta( $group[ 'meta' ] ), var_export( $group, true ) );
			}

		}


	}

	/**
	 * Check meta data is good
	 *
	 * @since 1.1.0
	 */
	protected function verify_meta( $meta ){
		foreach( [ 'page', 'is_tagline', 'destination'] as $key  ){
			if( ! isset( $meta[ $key ] ) ){
				return false;
			}

		}

		if( ! is_bool( $meta[ 'is_tagline' ] ) ){
			return false;
		}

		if( ! is_numeric( $meta[ 'page' ] ) ){
			return false;
		}

		return \ingot\testing\tests\click\destination\types::allowed_destination_type( $meta[ 'destination' ] );
	}


	/**
	 * Check for tagline
	 *
	 * @since 1.1.0
	 *
	 * @group group
	 * @group destination
	 *
	 * @covers \ingot\testing\utility\destination::is_tagline();
	 */
	public function testIsTagline(){
		$data = ingot_test_desitnation::create( 'page', true );
		$group = \ingot\testing\crud\group::read( $data[ 'group_ID' ] );
		$this->assertTrue( \ingot\testing\utility\destination::is_tagline( $group ) );

		$data = ingot_test_desitnation::create( 'page', false );
		$group = \ingot\testing\crud\group::read( $data[ 'group_ID' ] );
		$this->assertFalse( \ingot\testing\utility\destination::is_tagline( $group ) );

	}

	/**
	 * Cehck getting destination from meta
	 *
	 * @since 1.1.0
	 *
	 * @group group
	 * @group destination
	 *
	 * @covers \ingot\testing\utility\destination::get_destination()
	 */
	public function testGetDestination(){
		foreach( \ingot\testing\tests\click\destination\types::destination_types() as $type ){
			$args = ingot_test_desitnation::group_args( $type  );
			$data = ingot_test_desitnation::create( $type );
			$group = \ingot\testing\crud\group::read( $data[ 'group_ID' ] );
			$this->assertEquals( $type, \ingot\testing\utility\destination::get_destination( $group ) );

		}
	}

	/**
	 * Check getting page ID
	 *
	 * @since 1.1.0
	 *
	 * @group group
	 * @group destination
	 *
	 * \ingot\testing\utility\destination::get_page_id()
	 */
	public function testGetPageID(){
		$data = ingot_test_desitnation::create( 'page', true );
		$group = \ingot\testing\crud\group::read( $data[ 'group_ID' ] );
		$this->assertSame( $data[ 'page_ID' ], \ingot\testing\utility\destination::get_page_id( $group ) );
	}


}

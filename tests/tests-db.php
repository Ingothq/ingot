<?php
/**
 * Test our DB setup/update
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */
class tests_database extends \WP_UnitTestCase {

	/**
	 * Test ingot version compare
	 *
	 * @since 1.1.0
	 *
	 * @group db
	 *
	 * @covers \ingot\testing\db\upgrade()
	 */
	public function testVersionCompare(){
		$updater = new \ingot\testing\db\upgrade( '1.1.0' );
		$this->assertTrue( $updater->before( '0.9' ) );
		$this->assertTrue( $updater->before( '1.0.0' ) );
		$this->assertTrue( $updater->before( '1.1.0-b-1' ) );
		$this->assertFalse( $updater->before( '1.1.1-b-1' ) );
		$this->assertFalse( $updater->before( '1.1.1' ) );
		$this->assertFalse( $updater->before( '2.0' ) );
	}

	/**
	 * Test that the wp_ID column gets added to groups via the 1.1.0 updater
	 *
	 * @since 1.1.0
	 *
	 * @group db
	 *
	 * @covers \ingot\testing\db\add_wp_id_column()
	 * @covers \ingot\testing\db\run()
	 */
	public function test1dot1Update(){
		global $wpdb;
		$table_name = \ingot\testing\crud\group::get_table_name();
		//test method in isolation

		$wpdb->query( sprintf( 'ALTER TABLE %s DROP COLUMN wp_ID', $table_name ) );
		$updater = new \ingot\testing\db\upgrade( '1.1.0' );
		$updater->add_wp_id_column();

		$data = ingot_test_data_price::edd_tests( 10 );
		$this->assertTrue( is_array( $data ) );
		$this->assertTrue( is_numeric( $data[ 'group_ID' ] ) );
		$product_ID = $data[ 'product_ID' ];
		$results = $wpdb->query( sprintf( 'SELECT * FROM `%s` WHERE `wp_ID` = %d', $table_name, $product_ID ) );
		$this->assertTrue( ! empty( $results ) );

		//test using run method
		$wpdb->query( sprintf( 'ALTER TABLE %s DROP COLUMN wp_ID', $table_name ) );
		$updater->run();

		$data = ingot_test_data_price::edd_tests( 10 );
		$this->assertTrue( is_array( $data ) );
		$this->assertTrue( is_numeric( $data[ 'group_ID' ] ) );
		$product_ID = $data[ 'product_ID' ];
		$results = $wpdb->query( sprintf( 'SELECT * FROM `%s` WHERE `wp_ID` = %d', $table_name, $product_ID ) );
		$this->assertTrue( ! empty( $results ) );

		//test that running it anyway doesn't break stuff
		$results = $wpdb->query( sprintf( 'SELECT * FROM `%s` WHERE `wp_ID` = %d', $table_name, $product_ID ) );
		$this->assertTrue( ! empty( $results ) );

	}


}

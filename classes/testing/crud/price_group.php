<?php
/**
 * Table based CRUD for price test groups
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\crud;


use ingot\testing\utility\helpers;

class price_group extends table_crud {

	/**
	 * Name of this object
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected static $what = 'price_group';

	/**
	 * Get price groups
	 *
	 * @since 0.0.9
	 *
	 * @acess protected
	 *
	 * @param array $params {
	 *  $get_current bool Optional. Get current only. Default is true
	 *  $ids bool Optional. If false, the default, all fields are returned. If true, only IDs
	 *  $limit int Optional. Total number to return. Default is -1 to get all.
	 *  $page int Optional. Page of results. Default is 1
	 * }
	 *
	 * @return array|null|object
	 */
	public static function get_items( $params ){
		$args = wp_parse_args(
			$params,
			array(
				'get_current' => true,
				'ids' => false,
				'limit' => -1,
				'page' => 1,
				'preview' => false
			)
		);

		global $wpdb;

		if( -1 == $args[ 'limit' ] ) {
			$args[ 'limit' ] = 999999999;
		}



		$table_name = static::get_table_name();
		if( 'true' == $args[ 'get_current' ] ){
			if( true == $args[ 'ids' ] ){
				$select = "SELECT `ID`";
			}else{
				$select = "SELECT *";
			}
			$sql = sprintf( "%s FROM %s WHERE `current_sequence` > 0", $select, $table_name );

		}else{
			if( true == $args[ 'ids' ] ){
				$select = "`ID`";
			}else{
				$select = "*";
			}
			$sql = sprintf( "SELECT %s FROM `%s` ", $select, $table_name );
		}



		$sql .= sprintf( ' ORDER BY `ID` ASC LIMIT %d OFFSET %d', $args[ 'limit' ], self::calculate_offset( $args[ 'limit' ], $args[ 'page' ] )  );
		$wpdb->last_error = '';
		$results = $wpdb->get_results( $sql, ARRAY_A );

		if ( empty( $wpdb->last_error ) && ! empty( $results ) ) {
			if ( false == $args['ids'] ) {
				foreach( $results as $result ){
					$_item = self::fill_in( $result );
					$_item[ 'sequences' ] = maybe_unserialize( helpers::v( 'sequences', $_item, array() ) );
					$_item[ 'test_order' ] = maybe_unserialize( helpers::v( 'test_order', $_item, array() ) );
					if( ! is_wp_error( $_item ) ){
						$items[] = $_item;
					}

				}

				return $items;

			}

			return $results;
		}


	}

	/**
	 * Deactivate a price group
	 *
	 * @since 0.0.9
	 *
	 * @param int|array $id Group ID or config array
	 */
	public static function deactivate( $id ) {
		if ( ! is_array( $id ) ) {
			$group = self::read( $id );
			$id = $group[ 'ID' ];
		}

		$current_sequence = $group[ 'current_sequence' ];
		sequence::complete( $current_sequence );
		$group[ 'current_sequence' ] = 0;
		self::update( $group, $id );

	}

	/**
	 * Validate item config
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @param array $data Item config
	 *
	 * @return bool|array Item config array if valid, false if not.
	 */
	protected static function validate_config( $data ) {
		$required = self::required();

		if( ! isset( $data[ 'type' ] ) || 'price' !== $data[ 'type' ] ){
			return new \WP_Error( 'ingot-invalid-price-group-type', __( 'For forward-compatibility reasons, price test groups must be created with the type "price"', 'ingot' )  );
		}

		if( ! isset( $data[ 'plugin' ] ) || ! in_array( $data[ 'plugin' ], ingot_accepted_plugins_for_price_tests() ) ){
			return new \WP_Error( 'ingot-invalid-price-group-plugin', __( 'Price test group plugin type is not supported', 'ingot' )  );
		}


		foreach( $required as $key ) {
			if ( ! isset( $data[ $key ] ) ) {
				return new \WP_Error( 'ingot-invalid-config', __( sprintf( '%s require the field %s', static::what(), $key )  ) );
			}

		}

		$product_id = $data[ 'product_ID' ];

		if ( ! empty( $data[ 'test_order' ] )  ) {
			$product_id = $data[ 'product_ID' ];
			foreach ( $data['test_order'] as $i => $test ) {
				$test = price_test::read( $test );
				if ( $product_id != $test['product_ID'] ) {
					return new \WP_Error( 'ingot-invalid-test-group-test', __( 'All price tests in a price test group must be for the same product', 'ingot' ) );
				}
			}
		}

		$data = self::fill_in( $data );

		if( isset( $data[ 'created' ] ) ) {
			$data[ 'created' ] = current_time( 'mysql' );
		}

		$fields = self::get_all_fields();
		foreach ( $fields as $key  ) {
			if ( 'test_order' == $key || 'sequences' == $key  ) {
				if ( ! is_array( $data[ $key ] ) ) {
					$data[ $key ] = array();
				}
			}elseif( is_int( $data[ $key ] ) || is_string( $data[ $key ] ) ){
				continue;
			} else  {
				return new \WP_Error( $key . '-invalid', __( 'Invalid data type', 'ingot' ), array( $key => $data[ $key ] ) );
			}
		}


		return $data;


	}

	/**
	 * Necesarry, but not required fields of this object
	 *
	 * @since 0.0.4
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected static function needed() {
		$needed = array(
			'group_name',
			'sequences',
			'test_order',
			'initial',
			'threshold',
			'created',
			'modified',
			'current_sequence',
		);

		return $needed;
	}

	/**
	 * Required fields of this object
	 *
	 * @since 0.0.4
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected static function required() {
		$required = array(
			'type',
			'plugin',
			'product_ID'
		);

		return $required;
	}



}

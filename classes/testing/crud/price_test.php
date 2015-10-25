<?php
/**
 * Price test CRUD
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
namespace ingot\testing\crud;


class price_test extends options_crud {

	/**
	 * Name of this object
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected static $what = 'price_test_group';

	protected static function what() {
		return self::$what;
	}


	/**
	 *  Prepared data to be saved
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @param array $data Data
	 *
	 * @return array|\WP_Error Array of prepared data or WP_Error if invalid.
	 */
	protected static function validate_config( $data ) {
		if( empty( $data[ 'default' ] ) ) {
			return new \WP_Error( 'ingot-invalid-price-test', __( 'Price tests must have a default set of price variations' ), 'ingot' );
		}

		$data[ 'default' ] = self::validate_test_part( $data[ 'default' ] );
		if( is_wp_error( $data[ 'default' ] ) ){
			return $data[ 'default' ];
		}

		$data = self::fill_in( $data );

		if( is_array( $data[ 'variable_prices' ] ) && ! empty( $data[ 'variable_prices' ] ) ){
			foreach( $data[ 'variable_prices' ] as $i => $test_part ) {
				$test_part = self::validate_test_part( $test_part );
				if( is_wp_error( $test_part ) ) {
					return $test_part;
				}

				$data[ 'variable_prices' ][ $i ] = $test_part;
			}
		}

		if( ! isset( $data[ 'created' ] ) || 0 == $data[ 'created' ] ) {
			$data[ 'created' ] = time();

		}

		if( ! isset( $data[ 'modified' ] ) || 0 == $data[ 'modified' ] ) {
			$data[ 'modified' ] = time();
		}



		return $data;


	}


	/**
	 * Fill in needed, but not required keys
	 * @since 0.0.4
	 *
	 * @access protected
	 *
	 * @param $data
	 *
	 * @return array
	 */
	protected static function fill_in( $data ){
		foreach( self::needed() as $needed ) {
			if( ! isset( $data[ $needed ] ) ) {
				if( 'variable_prices' == $needed ) {
					$data[ 'variable_prices' ] = array();
				}else{
					$data[ $needed ] = 0;
				}
			}

		}

		return $data;

	}

	/**
	 * Validate a part of a test
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @param $test_part
	 *
	 * @return array|\WP_Error Test part if OK, WP_Error if not
	 */
	protected static function validate_test_part( $test_part ) {
		if( ! is_array( $test_part ) || ( ! isset( $test_part[ 'a' ] ) || ! isset( $test_part[ 'b' ] ) ) ) {
			return new \WP_Error( 'ingot-invalid-price-test-part', __( 'Price test parts must be an array and have an A and B', 'ingot' ) );
		}

		foreach( array( 'a', 'b' ) as $part ){
			if( 0 === $test_part[ $part ] ){
				continue;
			}

			$value = (float) $test_part[ $part ];

			if( 0 == $value || false === self::float_in_allowed_range( $value ) ) {
				return new \WP_Error( 'ingot-invalid-price-test-part-value', __( 'Price test parts values must be a float less than 1 and greater than -1.', 'ingot' ) );
			}

			$test_part[ $part ] = $value;
		}

		return $test_part;


	}

	/**
	 * Check that we have a float less than 1 and greater than -1
	 *
	 * @param float $i
	 *
	 * @return bool
	 */
	protected static function float_in_allowed_range( $i ) {
		if( $i > -1 && $i < 1 ) {
			return true;

		}else{
			return false;

		}

	}


	/**
	 * Required fields of this object
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected static function required() {
		$required = array(
			'product_ID',
			'default'
		);

		return $required;

	}

	/**
	 * Neccasary, but not required fields of this object
	 *
	 * @since 0.0.9
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected static function needed() {

		$needed = array(
			'name',
			'variable_prices',
			'created',
			'modified'
		);

		return $needed;

	}
}

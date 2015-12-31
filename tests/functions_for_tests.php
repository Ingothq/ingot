<?php
/**
 * Helper functions for tests
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */


function ingot_tests_make_groups( $add_variants = true, $total_groups = 5, $variants_per_group = 3, $args = array() ){
	return ingot_tests_data::make_groups( $add_variants, $total_groups, $variants_per_group, $args );

}


class ingot_tests_data {

	/**
	 * Create groups with variants, optionally added to them.
	 *
	 * @param bool $add_variants Optional. If true, the default, variants are added to groups via groups object. If false, they are created, IDs are returned but they will nto be associated with a group properly.
	 * @param int $total_groups Optional. Default is 5.
	 * @param int $variants_per_group Optional. Default is 3.
	 * @param array $args Optional
	 *
	 * @return array
	 */
	public static function make_groups( $add_variants = true, $total_groups = 5, $variants_per_group = 3, $args = array() ){
		$defaults = [
			'group_args' => [
				'name' => rand(),
				'type'     => 'click',
				'sub_type' => 'button',
				'meta'     => [ 'link' => 'https://bats.com' ],
			],
			'variant_args' => [
				'type'     => 'click',
				'group_ID' => 1,
				'content'  => rand()
			]
		];

		$keys = array_keys( $defaults );
		foreach ( $keys as $i => $key  ) {
			if ( ! empty( $args[ $key ] ) ) {
				$args[ $key ] = wp_parse_args(
					$args[ $key ],
					$defaults[ $key ]
				);

			}else{
				$args[ $key ] = $defaults[ $key ];
			}

		}

		$groups = [ 'ids' => [], 'variants' => [] ];
		$group_args = $args[ 'group_args' ];
		$variant_args = $args[ 'variant_args' ];
		for ( $g = 0; $g <= $total_groups; $g++  ) {
			$variants = [];
			$group_args[ 'name' ] = (string) $g . rand();

			$group_id = \ingot\testing\crud\group::create( $group_args, true );

			$groups[ 'ids' ][] =  $group_id;

			$variant_args[ 'group_ID' ] = $group_id;

			for( $v = 0; $v <= $variants_per_group; $v++ ) {
				$variant_id = \ingot\testing\crud\variant::create( $variant_args, true );
				$variants[] = $variant_id;

			}

			$groups[ 'variants' ][ $group_id ] = $variants;

			if ( $add_variants ) {
				if ( is_user_logged_in() ) {
					$obj = new \ingot\testing\object\group( $group_id );
					$obj->update_group( [ 'variants' => $variants ] );
				}else{
					$_group = \ingot\testing\crud\group::read( $group_id );
					$_group[ 'variants' ] = $variants;
					$saved = \ingot\testing\crud\group::update( $_group, $group_id, true );
				}
			}

		}

		return $groups;
	}

	public static function click_link_group($add_variants = true, $total_groups = 5, $variants_per_group = 3 ){
		return self::make_groups( $add_variants, $total_groups, $variants_per_group );
	}

	public static function click_button_group( $add_variants = true, $total_groups = 5, $variants_per_group = 3 ) {
		$args = [
			'group_args'   => [
				'sub_type' => 'button',
				'meta'     => [
					'link' => 'https://bats.com',
					'color' => '#fff',
					'background_color' => '#000'
				],
			]
		];
		return self::make_groups( $add_variants, $total_groups, $variants_per_group, $args );

	}

	public static function click_button_color_group( $add_variants = true, $total_groups = 5, $variants_per_group = 3 ) {
		$args = [
			'group_args'   => [
				'sub_type' => 'button_color',
				'meta'     => [
					'link' => 'https://bats.com',
					'color_test_text' => 'Default Text for group',
				],
				'variant_args' => [
					'color' => '#fff',
					'background_color' => '#000'
				]
			]
		];
		return self::make_groups( $add_variants, $total_groups, $variants_per_group, $args );

	}


}

/**
 * Create sample data for price tests
 *
 * Class ingot_price_data_edd
 */
class ingot_test_data_price {

	public static function edd_tests( $base_price = 10, $variable_price = false ){
		if ( $variable_price ){
			$product = self::edd_create_variable_download($base_price);
		}else{
			$product = self::edd_create_simple_download($base_price);
		}
		$args = self::edd_args( $product->ID );

		return self::make_groups( $args );
	}

	public static function make_groups( $args ){

		$group_args = $args[ 'group_args' ];
		$variant_args = $args[ 'variant_args' ];

		$variants = [];
		$group_args[ 'name' ] =  rand();

		$group_id = \ingot\testing\crud\group::create( $group_args, true );
		if( is_wp_error( $group_id ) ){
			return $group_id;
		}
		$data[ 'group_ID' ] = $group_id;

 		$variant_args[ 'group_ID' ] = $group_id;
		$price_variations = [];

		for( $v = 0; $v <= 5; $v++ ) {
			$variation = rand( -0.9, 0.9 );
			$variant_args[ 'meta' ][ 'price' ] = $variation;
			$variant_id = \ingot\testing\crud\variant::create( $variant_args, true );

			if ( is_numeric( $variant_id) ) {
				$variants[] = $variant_id;
				$price_variations[ $variant_id ] = $variation;

			}else{
				return $variant_id;
			}

		}

		$data[ 'variants' ] = $variants;
		$data[ 'price_variations' ] = $price_variations;



		if ( is_user_logged_in() ) {
			$obj = new \ingot\testing\object\group( $group_id );
			$obj->update_group( [ 'variants' => $variants ] );
		}else{
			$_group = \ingot\testing\crud\group::read( $group_id );
			$_group[ 'variants' ] = $variants;
			$saved = \ingot\testing\crud\group::update( $_group, $group_id, true );
		}


		return $data;
	}

	/**
	 * @param $product_id
	 *
	 * @return array
	 */
	public static function edd_args( $product_id ) {
		return [
			'group_args'   => [
				'type'     => 'price',
				'sub_type' => 'edd',
			],
			'variant_args' => [
				'type' => 'price',
				'meta' => [
					'price' => [ ]
				],
				'content' => $product_id
			]
		];

	}


	/**
	 *
	 *
	 * Copied from https://github.com/easydigitaldownloads/Easy-Digital-Downloads/blob/master/tests/helpers/class-helper-download.php
	 * @return \WP_Post
	 */
	public static function edd_create_simple_download($base_price) {
		$post_id = wp_insert_post( array(
			'post_title'    => 'Test Download Product',
			'post_name'     => 'test-download-product',
			'post_type'     => 'download',
			'post_status'   => 'publish'
		) );
		$_download_files = array(
			array(
				'name'      => 'Simple File 1',
				'file'      => 'http://localhost/simple-file1.jpg',
				'condition' => 0
			),
		);
		$meta = array(
			'edd_price'                         => $base_price,
			'_variable_pricing'                 => 0,
			'edd_variable_prices'               => false,
			'edd_download_files'                => array_values( $_download_files ),
			'_edd_download_limit'               => 20,
			'_edd_hide_purchase_link'           => 1,
			'edd_product_notes'                 => 'Purchase Notes',
			'_edd_product_type'                 => 'default',
			'_edd_download_earnings'            => 40,
			'_edd_download_sales'               => 2,
			'_edd_download_limit_override_1'    => 1,
			'edd_sku'                           => 'sku_0012'
		);
		foreach( $meta as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		}
		return get_post( $post_id );
	}

	/**
	 *
	 * Copied from https://github.com/easydigitaldownloads/Easy-Digital-Downloads/blob/master/tests/helpers/class-helper-download.php
	 *
	 * @return \WP_Post
	 */
	public static function edd_create_variable_download($base_price) {
		$post_id = wp_insert_post( array(
			'post_title'    => 'Variable Test Download Product',
			'post_name'     => 'variable-test-download-product',
			'post_type'     => 'download',
			'post_status'   => 'publish'
		) );
		$_variable_pricing = array(
			array(
				'name'   => 'Simple',
				'amount' => $base_price
			),
			array(
				'name'   => 'Advanced',
				'amount' => $base_price * 10
			)
		);
		$_download_files = array(
			array(
				'name'      => 'File 1',
				'file'      => 'http://localhost/file1.jpg',
				'condition' => 0,
			),
			array(
				'name'      => 'File 2',
				'file'      => 'http://localhost/file2.jpg',
				'condition' => 'all',
			),
		);
		$meta = array(
			'edd_price'                         => $base_price,
			'_variable_pricing'                 => 1,
			'_edd_price_options_mode'           => 'on',
			'edd_variable_prices'               => array_values( $_variable_pricing ),
			'edd_download_files'                => array_values( $_download_files ),
			'_edd_download_limit'               => 20,
			'_edd_hide_purchase_link'           => 1,
			'edd_product_notes'                 => 'Purchase Notes',
			'_edd_product_type'                 => 'default',
			'_edd_download_earnings'            => 120,
			'_edd_download_sales'               => 6,
			'_edd_download_limit_override_1'    => 1,
			'edd_sku'                          => 'sku_0012',
		);
		foreach ( $meta as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		}
		return get_post( $post_id );
	}

}

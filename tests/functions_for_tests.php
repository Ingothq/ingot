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

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

/**
 * Create groups with variants, optionally added to them.
 *
 * @param bool $add_variants Optional. If true, the default, variants are added to groups via groups object. If false, they are created, IDs are returned but they will nto be associated with a group properly.
 * @param int $total_groups Optional. Default is 5.
 * @param int $variants_per_group Optional. Default is 3.
 *
 * @return array
 */
function ingot_tests_make_groups( $add_variants = true, $total_groups = 5, $variants_per_group = 3 ){
	$groups = [ 'ids' => [], 'variants' => [] ];
	for ( $g = 0; $g <= $total_groups; $g++  ) {
		$variants = [];
		$params = array(
			'name' => $g,
			'type'     => 'click',
			'sub_type' => 'button',
			'meta'     => [ 'link' => 'https://bats.com' ],
		);

		$group_id = \ingot\testing\crud\group::create( $params );

		$groups[ 'ids' ][] =  $group_id;

		for( $v = 0; $v <= $variants_per_group; $v++ ) {
			$params = [
				'type'     => 'click',
				'group_ID' => $group_id,
				'content'  => $v
			];
			$variant_id = \ingot\testing\crud\variant::create( $params );
			$variants[] = $variant_id;

		}

		$groups[ 'variants' ][ $group_id ] = $variants;

		if ( $add_variants ) {
			$obj = new \ingot\testing\object\group( $group_id );
			$obj->update_group( [ 'variants' => $variants ] );
		}

	}

	return $groups;

}

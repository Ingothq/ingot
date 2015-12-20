<?php
/**
 * @TODO What this does.
 *
 * @package   @TODO
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\object;


use SebastianBergmann\Exporter\Exception;

class group {

	private $ID;

	private $levers;

	private $stats;

	private $group;


	public function __construct( $group ) {
		$this->set_group( $group );
	}

	public function get_ID(){
		return $this->ID;
	}

	public function get_levers(){
		if( is_null( $this->levers ) ){
			$this->set_levers();
		}

		return $this->levers;
	}

	public function get_stats() {
		if( is_null( $this->stats ) ){
			$this->set_stats();
		}

		return $this->stats;
	}

	protected function validate_group( $group ){
		if( ! is_array( $group ) ) {
			throw new \Exception( esc_html__( 'Invalid group passed to Ingot group object.', 'ingot' ) );
			return;
		}

		$fields = \ingot\testing\crud\group::get_all_fields();
		foreach( $fields as $field ) {
			if( ! array_key_exists( $field, $group ) ) {
				throw new \Exception( esc_html__( 'Invalid group config passed to Ingot group object.', 'ingot' ) );
				return;
			}

		}

		return true;
	}

	private function set_group( $group ) {
		if( is_numeric( $group ) ) {
			$group = \ingot\testing\crud\group::read( $group );
		}

		$this->validate_group( $group );
		$this->group = $group;
		$this->ID = $this->group[ 'ID' ];

	}

	private function set_stats() {

	}

	private function set_levers(){
		if ( ! empty( $this->group[ 'levers' ] ) ) {
			$this->levers = $this->group[ 'levers' ];
		}else{
			//@todo create if ! empty( $this->group[ 'variants' ]  ) ??
		}
	}
}

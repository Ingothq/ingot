<?php
/**
 * Object representing a sequence with extra, calculated data not in DB.
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\object;


use ingot\testing\crud\group;
use ingot\testing\crud\test;

class sequence {

	/**
	 *
	 * @since 0.0.7
	 *
	 * @access private
	 *
	 * @var int
	 */
	private $a_id;

	/**
	 *
	 * @since 0.0.7
	 *
	 * @access private
	 *
	 * @var int
	 */
	private $b_id;

	/**
	 *
	 * @since 0.0.7
	 *
	 * @access private
	 *
	 * @var string
	 */
	private $test_type;

	/**
	 *
	 * @since 0.0.7
	 *
	 * @access private
	 *
	 * @var int
	 */
	private $a_win;

	/**
	 *
	 * @since 0.0.7
	 *
	 * @access private
	 *
	 * @var int
	 */
	private $b_win;

	/**
	 *
	 * @since 0.0.7
	 *
	 * @access private
	 *
	 * @var int
	 */
	private $a_total;

	/**
	 *
	 * @since 0.0.7
	 *
	 * @access private
	 *
	 * @var int
	 */
	private $b_total;

	/**
	 *
	 * @since 0.0.7
	 *
	 * @access private
	 *
	 * @var int
	 */
	private $initial;

	/**
	 *
	 * @since 0.0.7
	 *
	 * @access private
	 *
	 * @var bool
	 */
	private $completed;

	/**
	 *
	 * @since 0.0.7
	 *
	 * @access private
	 *
	 * @var int
	 */
	private $threshold;

	/**
	 *
	 * @since 0.0.7
	 *
	 * @access private
	 *
	 * @var string
	 */
	private $created;

	/**
	 *
	 * @since 0.0.7
	 *
	 * @access private
	 *
	 * @var string
	 */
	private $modified;

	/**
	 *
	 * @since 0.0.7
	 *
	 * @access private
	 *
	 * @var int
	 */
	private  $group_ID;

	/**
	 * Sequence ID
	 *
	 * @since 0.0.7
	 *
	 * @access private
	 *
	 * @var int
	 */
	private $ID;

	/**
	 *
	 * @since 0.0.7
	 *
	 * @access private
	 *
	 * @var int
	 */
	private $total;


	/**
	 * Extra properties, not stored in DB, we create dynamically as needed.
	 *
	 * @since 0.0.7
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $extra_fields = array(
		'a_win_percentage',
		'b_win_percentage',
		'a_total_percentage',
		'b_total_percentage',
		'total',
		'win_total',
		'a_name',
		'b_name'
	);

	/**
	 * Test A config
	 *
	 * @since 0.0.7
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $a;

	/**
	 * Test B config
	 *
	 * @since 0.0.7
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $b;

	/**
	 * Constructor for class
	 *
	 * @since 0.0.7
	 *
	 * @param array|int $sequence Sequence array as returned by CRUD class or sequence ID.
	 */
	public function __construct( $sequence ) {
		$sequence = $this->verify_sequence( $sequence );
		if( $sequence ){
			$this->set_properties( $sequence );
			$this->set_total();
		}

	}

	/**
	 * Magic getter.
	 *
	 * @since 0.0.7
	 *
	 * @param string $field Name of field
	 *
	 * @return mixed Field value
	 */
	public function __get( $field ) {
		if( in_array( $field, $this->allowed_fields() ) ) {
			if( isset( $this->$field ) ) {
				return $this->$field;
			}elseif( method_exists( $this, $field ) ){
				return call_user_func( array( $this, $field ) );
			}else{
				return false;
			}

		}elseif( 'sequence' == $field ) {
			$sequence = array();
			foreach( \ingot\testing\crud\sequence::get_all_fields() as $field ) {
				$sequence[ $field ] = $this->$field;
			}

			return $sequence;

		}else{
			return false;
		}

	}

	/**
	 * Verify sequence, loading from DB if needed.
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @param array|int $sequence Sequence array as returned by CRUD class or sequence ID.
	 *
	 * @return array|void
	 */
	protected function verify_sequence( $sequence ) {
		if( is_numeric( $sequence ) ) {
			$sequence = \ingot\testing\crud\sequence::read( $sequence );
		}

		if( ! is_wp_error( $sequence ) && ! empty( $sequence ) ) {
			return $sequence;

		}

	}

	/**
	 * Calcualte and return an array of allowed fields for __get()
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected function allowed_fields() {
		$fields =  array_merge( \ingot\testing\crud\sequence::get_all_fields(),$this->extra_fields );
		$fields[] = 'ID';

		return $fields;
	}

	private function set_properties( $sequence ) {

		foreach( \ingot\testing\crud\sequence::get_all_fields() as $field ) {
			//@todo why isn't this happening automattically?
			if( ! in_array( $field, array( 'test_type', 'created', 'modified' ) ) ) {
				$sequence[ $field ] = (int) $sequence[ $field ];
			}


			$this->$field = $sequence[ $field ];

		}

	}

	/**
	 * Calculate test A's win percentage
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @return int
	 */
	protected function a_win_percentage() {
		return $this->percentage( $this->a_win, $this->total );
	}

	/**
	 * Calculate test B's win percentage
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @return int
	 */
	protected function b_win_percentage() {
		return $this->percentage( $this->b_win, $this->total );
	}

	/**
	 * Calculate test A's percentage of times chosen.
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @return int
	 */
	protected function a_total_percentage() {
		return $this->percentage( $this->a_total, $this->total );
	}

	/**
	 * Calculate test B's percentage of times chosen.
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @return int
	 */
	protected function b_total_percentage() {
		return $this->percentage( $this->b_total, $this->total );
	}

	/**
	 * Calculate percentage
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @param  int $one Number
	 * @param int $two Total
	 *
	 * @return float|int
	 */
	protected function percentage( $one, $two ){
		if( 0 == $one || 0 == $two ) {
			return 0;
		}

		$float = $one/$two;
		$percentage = 100 * $float;
		return round( $percentage, 2 );
	}

	/**
	 * Set total property
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @return int
	 */
	protected function set_total() {
		$this->total = $this->a_total + $this->b_total;

	}

	/**
	 * Get name of test A
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 *
	 * @return string
	 */
	protected function a_name() {
		if( ! is_array( $this->a ) ) {
			$this->a = test::read( $this->a_id );
		}

		if ( is_array( $this->b ) ) {
			return $this->a['name'];
		}

	}

	/**
	 * Get name of test B
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 *
	 * @return string
	 */
	protected function b_name() {
		if( ! is_array( $this->b ) ) {
			$this->b = test::read( $this->b_id );
		}

		if ( is_array( $this->b ) ) {
			return $this->b['name'];
		}

	}


}

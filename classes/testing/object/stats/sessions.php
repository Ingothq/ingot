<?php
/**
 * Calculate stats for sessions on this site
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace ingot\testing\object\stats;


use ingot\testing\crud\session;
use Oefenweb\Statistics\Statistics;

class sessions {

	/**
	 * Total sessions by week
	 *
	 * @access private
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	private $weeks = [
		'unique' => [],
		'all' => [],
	];

	/**
	 * Calculated averages
	 *
	 * @access private
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	private $average = [ ];

	/**
	 * Class instance
	 *
	 * @access private
	 *
	 * @since 1.1.0
	 *
	 * @var \ingot\testing\object\stats\sessions
	 */
	private static $instance;

	/**
	 * Construct object and get unique sessions by week
	 *
	 * @access private
	 *
	 * @since 1.1.0
	 */
	private function __construct(){
		$this->find_weeks();
	}

	/**
	 * Get class instance
	 *
	 * @since 1.1.0
	 *
	 * @return \ingot\testing\object\stats\sessions
	 */
	public static  function get_instance(){
		if( is_null( self::$instance ) ){
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Calculate total sessions per week
	 *
	 * @since 1.1.0
	 *
	 * @param int $number Optional. Number of weeks to calculate for. Default is 4.
	 * @param bool $unique Optional. Use unique sessions only. Default is true
	 */
	public function find_weeks( $number = 4, $unique = true ){
		$what = $this->what( $unique );
		$start =  time();
		for( $i = 0; $i < $number; $i++ ) {
			$end = $this->week_before( $start );
			$total = $this->total_for_week( $start, $end, $unique );
			if( 0 == $total ){
				break;
			}else{
				$this->weeks[ $what ][ $i ] = $total;
				$start = $end;
			}

		}

	}

	/**
	 * Get total sessions for a week
	 *
	 * @access protected
	 *
	 * @since 1.1.0
	 *
	 * @param int $start Start date as timestamp
	 * @param int $end End date as time stamp
	 * @param bool $unique Optional. To get unique values or not. Default is true.
	 *
	 * @return int
	 */
	protected function total_for_week( $start, $end, $unique ){
		$total = 0;
		$key = md5( __CLASS__ . __METHOD__ . $start . $end . $unique );
		if(  INGOT_DEV_MODE || false == ( $total = get_option( $key, false ) ) ){
			$total = $this->find_range( $start, $end, $unique  );
			if (  ! INGOT_DEV_MODE ) {
				update_option( $key, (int) $total );
			}
		}

		return $total;
	}

	/**
	 * Get one of the averages
	 *
	 * @since 1.1.0
	 *
	 * @param bool $unique Optional. To get unique values or not. Default is true.
	 *
	 * @return int
	 */
	public function get_average( $unique = true ){
		$what = $this->what( $unique );
		if( ! isset ( $this->average[ $what ] ) ){
			$this->calculate_average( $unique );
		}

		return $this->average[ $what ];
	}

	/**
	 * Calculate the average sessions and set in average property
	 *
	 * @access private
	 *
	 * @since 1.1.0
	 *
	 * @param bool $unique Optional. To get unique values or not. Default is true.
	 */
	private function calculate_average( $unique = true ){

		$what = $this->what( $unique );
		if( empty( $this->weeks[ $what ] ) ){
			$this->find_weeks( 4, $unique );
		}

		if( 1 >= count( $this->weeks[ $what ] ) && isset( $this->weeks[ $what ][0] ) ){
			$this->average[ $what ] = $this->weeks[ $what ][0];
		}elseif( 1 >= count( $this->weeks[ $what ] ) && ! isset( $this->weeks[ $what ][0] ) ){
			$this->average[ $what ] = 0;
		}
		else{
			$this->average[ $what ] = Statistics::mean( $this->weeks[ $what ] );
		}

	}

	/**
	 * Get time a week before a time
	 *
	 * @access protected
	 *
	 * @since 1.1.0
	 *
	 *
	 * @param int $start Start time
	 *
	 * @return int
	 */
	protected function week_before( $start ){
		$end = $start - WEEK_IN_SECONDS;
		return $end;
	}

	/**
	 * Format time as string
	 *
	 * @access protected
	 *
	 * @since 1.1.0
	 *
	 * @param $time
	 *
	 * @return string
	 */
	protected function time_to_string( $time ){
		return date("Y-m-d H:i:s", $time);
	}

	/**
	 * Find number of sessions in a range
	 *
	 * @access protected
	 *
	 * @since 1.1.0
	 *
	 * @param int $max Max range timestamp
	 * @param int $min Min range timestap
	 * @param bool $unique Optional. To get unique values or not. Default is true.
	 *
	 * @return int
	 */
	protected function find_range( $max, $min, $unique = true ){
		$min = $this->time_to_string( $min );
		$max = $this->time_to_string( $max );
		global $wpdb;
		$table_name = session::get_table_name();
		if( $unique ){
			$select = 'DISTINCT `ingot_ID`';
		}else{
			$select = '`ID`';
		}

		$sql = sprintf( 'SELECT %s FROM `%s` WHERE `created` BETWEEN "%s" AND "%s"', $select, $table_name, $min, $max );
		$results = $wpdb->query( $sql, ARRAY_A );
		if ( is_int( $results ) ) {
			return $results;
		}elseif( is_array( $results ) && ! empty( $results ) ){
			return count( $results );
		}else{
			return 0;
		}

	}

	/**
	 * Should we get unique results or all results?
	 *
	 * @access private
	 *
	 * @since 1.1.0
	 *
	 * @param bool $unique Optional. To get unique values or not. Default is true.
	 *
	 * @return string
	 */
	private function what( $unique ) {
		if ( $unique ) {
			$for = 'unique';
		} else {
			$for = 'all';
		}

		return $for;

	}

}

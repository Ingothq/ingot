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


class session {


	protected $ID;

	protected $session;

	protected $ingot_id;

	private static $instance;


	private function __construct( $id = null ){
		$this->set_up_session ( $id );
	}

	public static function instance( $id = null ) {
		if( is_null( self::$instance ) ) {
			self::$instance = new self( $id );
		}

		return self::$instance;

	}

	public function get_session_info(){
		return [
			'ID' => $this->ID,
			'ingot_ID' => $this->ID,
		];
	}

	private function set_up_session( $id ) {
		if( is_null(  $id ) || ! is_array( \ingot\testing\crud\session::read( $id ) ) ) {
			$this->ID = \ingot\testing\crud\session::create( [
				'uID' => get_current_user_id(),
				'IP' => ingot_get_ip()
			] );

			$this->session = \ingot\testing\crud\session::read( $this->ID );
		}


		$this->ID = $this->session[ 'ID' ];

		$this->ingot_id = $this->session[ 'ingot_ID' ];

	}


}

<?php
/**
 * Bandit for content tests
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\bandit;




use ingot\testing\object\group;

class content extends bandit {

	/**
	 * @var \ingot\testing\object\group
	 */
	private $obj;


	/**
	 * Create persistor object
	 *
	 * @since 0.4.0
	 *
	 * @access protected
	 *
	 * @return \ingot\testing\bandit\persistor
	 */
	 protected function create_persistor(){
		 $this->set_group_obj();

		 $persistor = new persistor(
			 $this->get_ID(),
			 [ '\ingot\testing\crud\group', 'get_levers' ],
			 [ '\ingot\testing\crud\group', 'save_levers' ]
		 );
		 return $persistor;


	}

	/**
	 * Record a victory for a variant
	 *
	 * @since 0.4.0
	 *
	 * @param int $val ID of variant to record victory for
	 */
	public function record_victory( $val ){
		$val = $this->find_lever_by_id( (int) $val );
		parent::record_victory( $val );
	}

	/**
	 * Find \MaBandit\Lever object by ID
	 *
	 * @since 0.4.0
	 *
	 * @access protected
	 *
	 * @param $lever_id
	 *
	 * @return bool|\MaBandit\Lever
	 */
	protected function find_lever_by_id( $lever_id ) {
		$levers = $this->get_levers( $this->get_ID() );
		$id = (int) $this->get_ID();

		if( isset( $levers[ $id ] ) && isset( $levers[ $id ][ (int) $lever_id ] ) ){
			$lever = $levers[ $id ][ (int) $lever_id ];
			return $lever;

		}elseif( isset( $levers[ $id ] ) ) {
			foreach( $levers[ $id ] as $i => $lever ) {
				if( $lever->getValue() == $lever_id ) {
					return $lever;
				}

			}


		}else{
			return false;

		}


	}


	/**
	 * @return array
	 */
	public function get_levers( $id ) {
		if ( $id == $this->get_ID() ) {
			$levers = $this->obj->get_levers();
			return $levers;

		}else{
			return false;

		}

	}



	public function save_levers( $id, $levers  ) {
		if ( $id == $this->get_ID() ) {
			$this->obj->update_levers( $levers );

		}else{
			return false;

		}

	}

	private function set_group_obj(){
		$this->obj = new group( $this->get_ID() );
	}

	protected function create_experiment() {
		parent::create_experiment();

		$levers[ $this->get_ID() ] = $this->experiment->getLevers();
		$this->obj->update_levers( $levers );
	}

}

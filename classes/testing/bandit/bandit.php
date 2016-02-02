<?php
/**
 * Base class for running a Bandit test on
 *
 * @package   @TODO
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\bandit;


use ingot\testing\bandit\strategy\initial_random_EG;
use ingot\testing\bandit\strategy\random;
use ingot\testing\crud\group;
use ingot\testing\object\initial;
use ingot\testing\object\sessions;
use ingot\testing\utility\helpers;
use MaBandit\CreateExperiment;

abstract class bandit {

	/**
	 * Current experiment object
	 *
	 * @since 0.4.0
	 *
	 * @access private
	 *
	 * @var \MaBandit\Experiment|object
	 */
	protected $experiment;

	/**
	 * Mabandit object
	 *
	 * @since 0.4.0
	 *
	 * @access private
	 *
	 * @var \MaBandit\MaBandit|object
	 */
	private $bandit;

	/**
	 * Group ID
	 *
	 * @since 0.4.0
	 *
	 * @access private
	 *
	 * @var int
	 */
	private $ID;

	/**
	 * Group object
	 *
	 * @since 0.4.0
	 *
	 * @var \ingot\testing\object\group
	 */
	protected $obj;

	/**
	 * Are we in totally random mode
	 *
	 * @since 1.1.0
	 *
	 * @var bool
	 */
	protected $random_mode = false;


	/**
	 * Construct object
	 *
	 * @since 0.4.0
	 *
	 * @param int $id Group ID

	 */
	public function __construct( $id ){
		$this->ID = $id;
		$this->go();
	}

	/**
	 * Record a victory for a variant
	 *
	 * @since 0.4.0
	 *
	 * @param int|\MaBandit\Lever $val Lever or ID of lever to record conversion for.
	 */
	public function record_victory( $val ){

		if ( ! is_a( $val, '\MaBandit\Lever') ) {
			$val = $this->bandit->getLeverByExperimentAndValue( $this->ID, $val );
		}
		$this->bandit->registerConversion($val);
	}


	/**
	 * Choose a variant
	 *
	 * @since 0.4.0
	 *
	 * @return mixed
	 */
	public function choose() {
		$record = ! ingot_is_no_testing_mode();
		if ( ! $this->random_mode ) {
			$val    = $this->bandit->chooseLever( $this->experiment, $record )->getValue();
		}else{
			if( is_null( $this->obj ) ){
				$this->set_group_obj();
			}

			$val = $this->random_lever( $this->obj->get_levers() );

		}

		return $val;

	}

	/**
	 *
	 *
	 * @param $levers
	 *
	 * @return mixed
	 */
	protected function random_lever( $levers ){
		if( isset( $levers[ $this->get_ID() ] ) && is_array( $levers[ $this->get_ID() ] ) ){
			$levers = $levers[ $this->get_ID() ];
		}

		if ( ingot_is_no_testing_mode() ) {
			reset( $levers );
			$key = key( $levers );
			$lever = $levers[ $key ];
		}else{
			$key = array_rand( $levers );
			$lever = $levers[ $key ];
			/** @var \MaBandit\Lever $lever */
			$lever->incrementDenominator();
			$lever =  $this->bandit->validateLever( $this->bandit->getPersistor()->saveLever( $lever ) );

		}

		return $lever;
		
	}

	/**
	 * Get group ID
	 *
	 * @since 0.4.0
	 *
	 * @return int
	 */
	public function get_ID(){
		return $this->ID;
	}

	/**
	 * Start up the bandit.
	 *
	 * @since 0.4.0
	 *
	 * @access protected

	 */
	protected function go() {
		$this->set_random();
		$strategy = \MaBandit\Strategy\EpsilonGreedy::withExplorationEvery( 3 );

		$persistor = $this->create_persistor();
		$this->bandit = \MaBandit\MaBandit::withStrategy($strategy)->withPersistor($persistor);

		try {
			$this->experiment = $this->bandit->getExperiment( (string) $this->ID );
		} catch( \MaBandit\Exception\ExperimentNotFoundException $e ) {
			$this->create_experiment();

		}

	}

	/**
	 * Should variant selection be at random?
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	protected function set_random(){
		$this->random_mode = false;
	}

	/**
	 * Create persistor object
	 *
	 * @since 0.4.0
	 *
	 * @access protected
	 *
	 * @return \ingot\testing\bandit\persistor
	 */
	abstract protected function create_persistor();

	/**
	 * Set obj property of class with group object
	 *
	 * @since 0.4.0
	 *
	 * @access protected
	 */
	protected function set_group_obj(){
		$this->obj = new \ingot\testing\object\group( $this->get_ID() );
	}


	protected function create_experiment() {
		$group = group::read( $this->ID );
		if ( ! empty( $group[ 'variants' ] ) ) {
			$variants         = helpers::make_array_values_numeric( $group[ 'variants' ], true );
			$creator = new CreateExperiment( $variants, $this->ID, $this->bandit );
			$this->experiment = $creator->get_experiment();
		}
	}

}

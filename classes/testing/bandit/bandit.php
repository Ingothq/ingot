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


use ingot\testing\crud\group;
use ingot\testing\utility\helpers;

abstract class bandit {

	/**
	 * Current experiment object
	 *
	 * @since 0.4.0
	 *
	 * @access private
	 *
	 * @var \MaBandit\Experiment
	 */
	private $experiment;

	/**
	 *
	 *
	 * @since 0.4.0
	 *
	 * @access private
	 *
	 * @var
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
	 * @param int $val ID of variant to record victory for
	 */
	public function record_victory( $val ){
		$l = $this->bandit->getLeverByExperimentAndValue( $this->ID, $val);
		$this->bandit->registerConversion($l);
	}

	/**
	 * Choose a victory for a variant
	 *
	 * @since 0.4.0
	 *
	 * @return mixed
	 */
	public function choose() {
		$val = $this->bandit->chooseLever($this->experiment)->getValue();
		return $val;
	}

	/**
	 * Get group ID
	 *
	 * @since 0.4.0
	 *
	 * @access protected
	 *
	 * @return int
	 */
	protected function get_ID(){
		return $this->ID;
	}

	/**
	 * Start up the bandit.
	 *
	 * @since 0.4.0
	 *
	 * @access protected
	 *
	 * @param array $variants Optional. Variants to add. Required if $id is not an existing group.
	 */

	protected function go() {

		$strategy = \MaBandit\Strategy\EpsilonGreedy::withExplorationEvery(3);
		$persistor = $this->create_persistor();
		$this->bandit = \MaBandit\MaBandit::withStrategy($strategy)->withPersistor($persistor);

		//@todo remove this. Should never be used to create? Or move creation to seperate object
		try {
			$this->experiment = $this->bandit->getExperiment( (string) $this->ID );
		} catch( \MaBandit\Exception\ExperimentNotFoundException $e ) {
			$group = group::read( $this->ID );
			if( ! empty( $group[ 'variants' ] ) )  {
				$variants = helpers::make_array_values_numeric( $group[ 'variants' ], true );
				$this->experiment = $this->bandit->createExperiment( (string) $this->ID, $variants  );
			}

		}




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
}

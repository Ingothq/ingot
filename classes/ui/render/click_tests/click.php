<?php
/**
 * Base class for rendering click tests.
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\ui\render\click_tests;


use ingot\testing\api\rest\test;
use ingot\testing\bandit\content;
use ingot\testing\crud\group;
use ingot\testing\crud\sequence;
use ingot\testing\crud\variant;
use ingot\testing\tests\chance;
use ingot\testing\utility\helpers;

abstract class click {

	/**
	 * Group config
	 *
	 * @since 0.0.5
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected $group;

	/**
	 * Rendered HTML
	 *
	 * @since 0.0.5
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $html;

	/**
	 * MaBandit object
	 *
	 * @since 0.4.0
	 *
	 * @access protected
	 *
	 * @var \ingot\testing\bandit\content|object
	 */
	protected $bandit;

	/**
	 * Chosen variant array
	 *
	 * @since 0.4.0
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected $variant;

	/**
	 * Constructor for class
	 *
	 * @since 0.0.5
	 *
	 * @param int|array $group ID of group to render, or group array
	 * @Param int|null|array\MaBandit|Lever $variant Optional. Variant ID to render with. If null, one will be chosen
	 */
	public function __construct( $group, $variant = null ){

		$this->set_group( $group );
		if( $this->group ) {
			$this->set_variant( $variant );
			if( $this->variant ){
				$this->make_html();
			}

		}

	}

	/**
	 * Get prepared HTML
	 *
	 * Also increases count for test in this sequence.
	 *
	 * @since 0.0.5
	 *
	 * @return string
	 */
	public function get_html(){
		return $this->html;
	}

	/**
	 * Get the type of test this group uses
	 *
	 * @since 0.0.5
	 *
	 * @return string|void
	 */
	public function get_group_type() {
		if( is_array( $this->group ) ) {
			return $this->group[ 'type' ];
		}

	}

	/**
	 * Get the ID of test the chosen variant
	 *
	 * @since 0.4.0
	 *
	 * @return int
	 */
	public function get_chosen_variant_id(){
		if( is_null( $this->variant ) ){
			$this->set_bandit();
			$this->choose();
		}
		return (int) $this->variant[ 'ID' ];
	}

	/**
	 * Get variant content
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	protected function get_variant_content(){
		/**
		 * Filter the content of the chosen variant before rendering the test
		 *
		 * @since 1.1.0
		 *
		 * @param string $content The content
		 * @param int $variant_ID The variant ID
		 * @param int $gorup_ID The group ID
		 */
		return apply_filters( 'ingot_click_test_content', $this->variant[ 'content'], $this->variant[ 'ID' ], $this->group[ 'ID'] );
	}

	/**
	 * Get variant property
	 *
	 * @since 0.4.0
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected function get_variant() {
		return $this->variant;

	}

	/**
	 * Get group property
	 *
	 * @since 0.4.0
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected function get_group(){
		return $this->group;
	}

	/**
	 * Set variant property
	 *
	 * @param int|array|\MaBandit\lever|null $variant Variant ID, config, or lever. If null, one will be chosen
	 */
	protected function set_variant( $variant ){
		if( is_numeric( $variant ) && true === ( variant::valid($_variant =  variant::read( $variant ) ) ) ){
			$this->variant = $_variant;
		}elseif( is_array( $variant ) && variant::valid( $variant )){
			$this->variant = $variant;
		}elseif( is_a( $variant, 'MaBandit\lever') ){
			/** @var \MaBandit\Lever $variant */
			$this->variant = variant::read( $variant->getValue() );
		}else{
			$this->set_bandit();
			$this->choose();
		}
	}

	/**
	 * Choose a variant and set in the variant property
	 *
	 * @since 0.4.0
	 */
	protected function choose(){
		$variant =  $this->bandit->choose();
		$this->set_variant( $variant );

	}

	/**
	 * Make HTML for to output and set in the html property of this class
	 *
	 * Should ovveride in final class to avoid outputting nothing, which is bad.
	 *
	 * @since 0.0.5
	 *
	 * @access protected
	 */
	abstract protected function make_html();

	/**
	 * Set group property
	 *
	 * @since 0.0.5
	 *
	 * @access private
	 *
	 * @param int|array $group
	 */
	private function set_group( $group ) {
		// @todo maybe use group object here since it will validate
		if( is_array( $group ) ) {
			$this->group = $group;
		}else{
			$this->group = group::read( $group );
		}
	}


	/**
	 * Set bandit property
	 *
	 * @since 0.4.0
	 *
	 * @access private
	 */
	private function set_bandit(){
		if ( is_null( $this->bandit ) ) {
			$this->bandit = new content( (int) $this->group[ 'ID' ] );
		}
	}

	/**
	 * Attribute for outermost element
	 *
	 * @since 0.3.0
	 *
	 * @access protected
	 *
	 * @return string
	 */
	protected function attr_id() {
		return sprintf( 'ingot-test-%s', $this->variant[ 'ID' ] );
	}

	/**
	 * Get link from click group config
	 *
	 * @since 0.4.0
	 *
	 * @return string
	 */
	protected function link(){
		return helpers::get_link_from_meta( $this->group );
	}

}

<?php
/**
 * Handles post association by group ID
 *
 * @package   @ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\object;


use ingot\testing\utility\helpers;

class posts {

	/**
	 * Meta key to store in
	 *
	 * @since 1.1.0
	 *
	 * @access private
	 *
	 * @var string
	 */
	private $meta_key = 'ingot_groups';

	/**
	 * Groups associated with this post
	 *
	 * @since 1.1.0
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $groups = [];

	/**
	 * Post object for this collection
	 *
	 * @since 1.1.0
	 *
	 * @access private
	 *
	 * @var \WP_Post
	 */
	private $post;

	/**
	 * Construct object
	 *
	 * @since 1.1.0
	 *
	 * @param |WP_Post|integer $post
	 */
	public function __construct( $post ){
		$this->set_post( $post );
		if( $this->post ) {
			$this->set_groups();
		}

	}

	/**
	 * Get IDs of Ingot test groups associated with this post
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	public function get_groups(){
		return $this->groups;
	}

	/**
	 * Add group(s) to association
	 *
	 * @since 1.1.0
	 *
	 * @param int|array $id Group ID or array of IDs to add.
	 */
	public function add( $id ){
		if( is_numeric( $id ) ) {
			$this->groups[] = $id;
			$this->update();
		}

		if( is_array( $id ) ) {
			if( empty( $this->groups ) ) {
				$this->groups = helpers::make_array_values_numeric( $id );
			}else{
				foreach( $id as $i ){
					if ( is_numeric( $i ) ) {
						$this->groups[] = (int) $i;
					}
				}

				$this->groups = array_unique( $this->groups );
			}
			$this->update();
		}

	}

	/**
	 * Group to add to association
	 *
	 * @since 1.1.0
	 *
	 * @param int $id Group ID to remove.
	 */
	public function remove( $id ){
		if( is_numeric( $id ) ) {
			$key = array_search( $id, $this->groups );
			if ( false !== $key ) {
				unset( $this->groups[ $key ] );
				$this->update();
			}

		}

	}

	/**
	 * Save groups back into post meta
	 *
	 * @since 1.1.0
	 *
	 * @access protected
	 */
	protected function update( ) {
		update_post_meta( $this->post->ID, $this->meta_key, $this->groups );
		if (! empty( $this->groups ) ) {
			$this->groups = array_values( $this->groups );
		}
	}

	/**
	 * Set groups property
	 *
	 * @since 1.1.0
	 *
	 * @access private
	 */
	private function set_groups(){
		$this->groups = helpers::make_array_values_numeric( get_post_meta( $this->post->ID, $this->meta_key, true ) );
	}

	/**
	 * Set posts property
	 *
	 * @since 1.1.0
	 *
	 * @access private
	 *
	 * @param |WP_Post|integer $post
	 */
	private function set_post( $post ){
		if( is_numeric( $post ) ) {
			$this->set_post( get_post( $post ) );
		}

		if( is_a( $post, 'WP_Post' ) ) {
			$this->post = $post;
		}

	}


}

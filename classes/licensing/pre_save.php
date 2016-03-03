<?php
/**
 * Before saving a group check if plan type supports it
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace ingot\licensing;


class pre_save extends filters {

	/**
	 * Add hooks
	 *
	 * @since 1.1.0
	 */
	protected function add_hooks(){
		add_filter( 'ingot_crud_create', [ $this, 'pre_create' ], 1, 2 );
	}

	/**
	 * On create group, check if limitations are exceeded.
	 *
	 * @since 1.1.0
	 *
	 * @uses "ingot_crud_create" filter
	 *
	 * @param $data
	 * @param $what
	 *
	 * @return \WP_Error|array
	 */
	public function pre_create( $data, $what ){
		if( 'group' == $what && ! $this->plan->is_full() ){
			$type = $data[ 'sub_type' ];
			if( $this->plan->is_ecommerce() ){
				$can = $this->can_ecommerce( $type );
			}elseif( $this->plan->is_nugget() ){
				$can = $this->can_nugget( $type );
			}else{
				$can = false;
			}

			if( ! $can  ){
				$data = $this->make_error( $type );
			}


		}

		return $data;

	}

	/**
	 * Determine if test group can be created when on an eCommerce plan.
	 *
	 * @since 1.1.0
	 *
	 * @param string $type Click sub_type
	 *
	 * @return bool
	 */
	protected function can_ecommerce( $type ){
		switch( $type ) {
			case 'destination' :
				$count = count::destination();
				break;
			default :
				$count = count::cta();
				break;
		}

		return $this->can( $count );

	}

	/**
	 * Check if a test group can be created in nugget plan
	 *
	 * @since 1.1.0
	 *
	 * @param string $type Click sub_type
	 *
	 * @return bool
	 */
	protected function can_nugget( $type ){
		if( $type != 'destination' ) {
			return $this->can( count::cta() );
		}

	}

	/**
	 * Check if count is legit
	 *
	 * @since 1.1.0
	 *
	 * @param int $count
	 *
	 * @return bool
	 */
	protected function can( $count ){
		if( 0 == $count ){
			return true;
		}

	}

	/**
	 * Create error for when can not haz
	 *
	 * @since 1.1.0
	 *
	 * @param string $type
	 *
	 * @return \WP_Error
	 */
	protected function make_error( $type ){
		$message = __(  'Group can not be saved. You must upgrade your Ingot plan.' );

		/**
		 * Filter message for account exceeded.
		 *
		 * @since 1.1.0
		 *
		 * @param string $type Click sub_type
		 */
		$message = apply_filters( 'ingot_limitation_save_message', $message, $type );
		return new \WP_Error( 'ingot-save-limitation', $message );
	}

}

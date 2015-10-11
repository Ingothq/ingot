<?php
/**
 * Base class for admin
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\ui;


abstract class admin {

	/**
	 * Verify admin ajax nonce
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @param bool|true $get
	 *
	 * @return bool|false|int
	 */
	protected function check_nonce( $get = true ) {
		if( $get && isset( $_GET[ '_nonce' ] ) ) {
			$nonce = $_GET[ '_nonce' ];
		}elseif( ! $get && isset( $_POST[ '_nonce' ] ) ) {
			$nonce = $_POST[ '_nonce' ];
		}else{
			return false;
		}

		$verify = wp_verify_nonce( $nonce );
		return $verify;

	}

	/**
	 * Get path to partials dir
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @return string
	 */
	protected function partials_dir_path() {
		return dirname( __FILE__ ) . '/admin/partials/';
	}

	/**
	 * Edit link for groups
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @param bool|false $id
	 *
	 * @return string
	 */
	protected function group_edit_link( $id = false ) {
		if ( false === $id || 0 == absint( $id ) ){
			$id = 'new';
		}

		$page = $this->admin_page_link();

		$link = add_query_arg( 'group', $id, $page );
		return $link;
	}

	/**
	 * Main admin page link
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @param int $page_number
	 *
	 * @return string
	 */
	protected function admin_page_link( $page_number = 1 ) {
		$args = array(
			'page' => 'ingot',
			'page_number' => absint( $page_number ),
			'_nonce' => wp_create_nonce()
		);

		return add_query_arg( $args, admin_url( 'admin.php' ) );

	}

	/**
	 * Stats page link
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @param $group_id
	 *
	 * @return string
	 */
	protected function stats_page_link( $group_id ) {
		$args = array(
			'group_id' => $group_id,
			'stats' => true
		);

		return add_query_arg( $args, $this->admin_page_link( ) );

	}

}

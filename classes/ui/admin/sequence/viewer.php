<?php
/**
 * Make a sequence results table.
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\ui\admin\sequence;


use ingot\testing\crud\group;
use ingot\testing\crud\sequence;
use ingot\ui\admin;

class viewer extends admin {

	/**
	 * Constructor
	 *
	 * @todo Either actually load these via AJAX or get rid of this constructor
	 */
	public function __construct(){
		add_action( 'wp_ajax_ingot_sequence_view', array( $this, 'get_view' ) );
	}

	/**
	 * Get the view for all sequences of this group
	 *
	 * @since 0.0.7
	 *
	 * @param int|null $group_id Optional. Group ID. If null, the default, $_GET[ 'group_id' ] will be used
	 *
	 * @return string|void
	 */
	public function get_view( $group_id = null ) {
		if ( is_null( $group_id ) ) {
			if ( $this->check_nonce() ) {
				if ( isset( $_GET['group_id'] ) ) {
					$group_id = absint( $_GET['group_id'] );
				}
			}
		}

		if( $group_id ) {
			return $this->view( $group_id );
		}

	}


	/**
	 * Create view HTML
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @param int $group_id Group ID
	 *
	 * @return string HTML to echo
	 */
	protected function view( $group_id ) {
		$sequences = sequence::get_items(
			array(
				'group_ID' => $group_id,
				'limit' => -1
			)
		);
		$sequence_tables = '';
		if ( ! empty( $sequences ) ) {
			foreach ( $sequences as $sequence ) {
				$sequence_tables .= sprintf( '<div>%s</div>', $this->get_table( $sequence ) );
			}
		} else {
			return __( 'No results to display', 'ingot' );
		}

		$group_id = $group_id;

		$main_link = $this->click_group_admin_page_link();

		$group_meta = $this->group_meta( $sequence );

		ob_start();
		include_once( $this->partials_dir_path() . 'stats.php' );
		return ob_get_clean();


	}

	/**
	 * Get a results table for a sequence
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @param array $sequence
	 *
	 * @return string Table HTML
	 */
	protected function get_table( $sequence ) {
		$table = new table( $sequence );
		return $table->get_table();
	}

	/**
	 * Get meta info for group
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @param array $sequence Sequence config
	 *
	 * @return string Meta info HTML
	 */
	protected function group_meta( $sequence ) {
		$group = group::read( $sequence[ 'group_ID' ] );
		$id = $group[ 'ID' ];
		$name = $group[ 'name' ];
		$link = $this->click_group_edit_link( $group[ 'ID' ] );
		$type = $this->type( $group );
		ob_start();

		include_once( $this->partials_dir_path() ). 'group-meta.php';

		return ob_get_clean();

	}

	/**
	 * HTML for type info
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @param array $group Group config
	 *
	 * @return string
	 */
	protected function type( $group ) {
		if( 'click' == $group[ 'type' ] ) {
			return sprintf( '%s -- %s', __( 'Click Test', 'ingot' ), ucwords( $group[ 'click_type' ] ) );
		}else{
			return __( 'Price Test', 'ingot' );
		}
	}



}

<?php
/**
 * Make results table for a sequence
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\ui\admin\sequence;


use ingot\testing\object\sequence;

class table extends sequence {

	/**
	 * Get the table for a sequence results
	 *
	 * @since 0.0.7
	 *
	 * @return string
	 */
	public function get_table() {

		return	$this->make_table();

	}

	/**
	 * Make table for sequence results
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @return string Table HTML
	 */
	protected function make_table() {
		return sprintf( '<table class="sequence-table" id="sequence-table-%d">%s</table>', $this->ID, $this->table_body() );
	}

	/**
	 * Make table body
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @return string Table body HTML
	 */
	protected function table_body() {
		return $this->header() . $this->id_row() . $this->total_row() . $this->win_row();
	}

	/**
	 * Make table header
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @return string Table header row HTML
	 */
	protected function header(){
		$columns[] = '<td class="empty"> </td>';
		$columns[] = sprintf( '<td>%s</td>', __( 'Test A', 'ingot' ) );
		$columns[] = sprintf( '<td>%s</td>', __( 'Test B', 'ingot' ) );
		return $this->row( $columns );
	}

	/**
	 * Make table ID row
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @return string Table ID row HTML
	 */
	protected function id_row() {
		$columns[] = sprintf( '<td>%s</td>', __( 'ID', 'ingot' ) );
		$columns[] = sprintf( '<td>%s</td>', $this->a_id );
		$columns[] = sprintf( '<td>%s</td>', $this->b_id );
		return $this->row( $columns );

	}

	/**
	 * Make total row
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @return string Table total row HTML
	 */
	protected function total_row() {
		$columns[] = sprintf( '<td>%s</td>', __( 'Total', 'ingot' ) );
		$columns[] = sprintf( '<td>%s %s</td>', $this->a_total_percentage(), '%' );
		$columns[] = sprintf( '<td>%s %s</td>', $this->b_total_percentage, '%' );
		return $this->row( $columns );

	}

	/**
	 * Make table win row
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @return string Table win row HTML
	 */
	protected function win_row() {
		$columns[] = sprintf( '<td>%s</td>', __( 'Win', 'ingot' ) );
		$columns[] = sprintf( '<td>%s %s</td>', $this->a_win_percentage(), '%' );
		$columns[] = sprintf( '<td>%s %s</td>', $this->b_win_percentage(), '%' );
		return $this->row( $columns );

	}

	/**
	 * Create a row
	 *
	 * @since 0.0.7
	 *
	 * @access protected
	 *
	 * @param string $columns Columns with TDs
	 *
	 * @return string Row HTML
	 */
	protected function row( $columns ) {
		return '<tr>' . implode( '', $columns ) . '</tr>';
	}

}

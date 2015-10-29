<?php
/**
 * Admin screens for click tests
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\ui\admin;


use ingot\testing\crud\group;
use ingot\testing\utility\defaults;
use ingot\ui\admin;
use ingot\testing\crud\test;

use ingot\testing\utility\helpers;


class click extends admin{



	/**
	 * AJAX handler for getting CLICK main page
	 *
	 * @uses "wp_ajax_test_field_group"
	 *
	 * @since 0.0.6
	 */
	public function get_main_page() {

		echo $this->main_page( absint( helpers::v( 'page_number', 'get', 1 ) ) );


		die();
	}

	/**
	 * Render main admin page
	 *
	 * @since 0.0.6
	 *
	 * @param int $page_number Page number for group list
	 *
	 * @return string
	 */
	public function main_page( $page_number = 1 ) {
		ob_start();


		$limit = 10;

		$groups = group::get_items( array(
			'page' => $page_number,
			'limit' => $limit
		) );

		$groups_inner_html = '';
		if ( ! empty( $groups ) ) {
			foreach ( $groups as $group ) {
				$groups_inner_html .= $this->click_test_list_item( $group );
			}
		}
		$next_button = $this->next_groups_button( $page_number, $limit );
		$prev_button = $this->previous_groups_button( $page_number, $limit );
		$new_link = $this->click_group_edit_link( false );
		$settings_form = $this->get_settings_form();
		$main_page_link = $this->main_page_link();
		include_once( dirname( __FILE__ ) . '/partials/click-test-list.php');



		$html = ob_get_clean();
		return $html;
	}

	/**
	 * Get the next group button HTML if valid
	 *
	 * @param int $current_page
	 * @param int $limit
	 *
	 * @return string
	 */
	protected function next_groups_button( $current_page, $limit ){
		$page = $current_page + 1;
		$groups = group::get_items( array(
			'page' => $page,
			'limit' => $limit
		) );
		if( empty( $groups ) ) {
			$class = 'button button-disabled';
		}else{
			$class = 'button button-secondary';
		}

		return sprintf( '<a href="%s" class="%s" id="next-page">%s</a>', esc_url( $this->click_group_admin_page_link( $page ) ), $class, __( 'Next Page', 'ingot' ) );

	}

	/**
	 * Get the previous group button HTML if valid
	 *
	 * @param int $current_page
	 * @param int $limit
	 *
	 * @return string
	 */
	function previous_groups_button( $current_page, $limit ) {
		$page = $current_page - 1;
		if( 0 == $page ) {
			$class = 'button button-disabled';
		}else{
			$groups = group::get_items( array(
				'page'  => $page,
				'limit' => $limit
			) );
			if ( empty( $groups ) ) {
				$class = 'button button-disabled';
			} else {
				$class = 'button button-secondary';
			}
		}

		return sprintf( '<a href="%s" class="%s" id="next-page">%s</a>', esc_url( $this->click_group_admin_page_link( $page ) ),  $class, __( 'Previous Page', 'ingot' ) );
	}


	/**
	 * AJAX handler for getting click page
	 *
	 * @uses "wp_ajax_get_click_page"
	 *
	 * @since 0.0.6
	 */
	public function get_click_page() {
		if ( $this->check_nonce() ){
			if ( isset( $_GET['group_id'] ) && is_numeric( $_GET['group_id'] ) ) {
				$group = group::read( absint( $_GET['group'] ) );
			}
			echo $this->click_group_page( $group );

		}
		die();
	}

	/**
	 * Render group edit page
	 *
	 * @since 0.0.6
	 *
	 * @param null|array $group
	 */
	public function click_group_page( $group = null ) {
		ob_start();
		$back_link = $this->click_group_admin_page_link();

		if( is_null( $group ) && isset( $_GET[ 'group_id' ] ) ) {
			$group = (int)  $_GET[ 'group_id' ];
		}

		if( 0 != $group && is_numeric( $group ) ) {
			$group = group::read( $group );
		}

		if( 0 != $group &&  ! is_array( $group ) ){
			$error =  new \WP_Error( 'ingot-admin-click-no-group', sprintf( '%s %s', __( 'Can not find group', 'ingot' ),  $group ) );
			return $error->get_error_message();
		}

		$group = wp_parse_args(
			$group,
			array(
				'ID' => 0,
				'name' => '',
				'parts' => array(),
				'results' => array(),
				'type' => 'link',
				'link' => '',
				'order'=> array(),
				'meta' => array(),
				'click_type' => 'button'
			)
		);

		$background_color = helpers::get_background_color_from_meta( $group );
		$color = helpers::get_color_from_meta( $group );

		$color_test_text = helpers::v( 'color_test_text', $group[ 'meta' ], '' );

		$tests = $this->get_markup_for_saved_tests( $group[ 'order' ] );

		$click_options = array_combine( array_keys( $this->click_types() ), wp_list_pluck( $this->click_types(), 'label' )  );

		$stats_link = $this->stats_page_link( $group[ 'ID' ] );
		include_once( INGOT_UI_PARTIALS_DIR . 'click-test-group.php' );
		$out = ob_get_clean();
		echo $out;

	}

	/**
	 * Get data to make click types dropdown
	 *
	 * @since 0.0.6
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected function  click_types() {
		return array(
			'link' => array(
				'label' => __( 'Link', 'ingot' ),
				'desc' => __( 'A link, with testable text.', 'ingot' )
			),
			'button' => array(
				'label' => __( 'Button Text', 'ingot' ),
				'desc' => __( 'A clickable button, with testable text.', 'ingot' )
			),
			'button_color' => array(
				'label' => __( 'Button Color', 'ingot' ),
				'desc' => __( 'A clickable button, with testable color options.', 'ingot' )
			)/*
			'text' => array(
				'label' => __( 'Text', 'ingot' ),
				'desc' => __( 'Testable text, with another element as the click test.', 'ingot' )
			)*/
		);
	}

	/**
	 * Get group inputs via AJAX
	 */
	public function get_test_field_group() {
		if ( $this->check_nonce() ) {
			echo $this->test_field_group();
		}
		die();
	}

	/**
	 * Get HTML for group input group
	 *
	 * @since 0.0.6
	 *
	 * @access protected
	 *
	 * @return string
	 */
	protected function click_test_list_item( $group ) {
		ob_start();
		?>
		<div class="ingot-config-group" id="group-{{ID}}">
			<p>{{name}}</p>
			<pre>[ingot id="{{ID}}"]</pre>
			<div class="button-pair">
				<span>
					<a href="{{link}}" class="group-edit button button-secondary" data-group-id="{{ID}}">
						<?php _e( 'Edit Group', 'ingot' ); ?>
					</a>
				</span>
				<span>
					<a href="{{stats}}" class="group-stats button button-secondary" data-group-id="{{ID}}">
						<?php _e( 'Group Stats', 'ingot' ); ?>
					</a>
				</span>
				<span>
					<a href="#" class="group-delete button button-secondary" data-group-id="{{ID}}">
						<?php _e( 'Delete Group', 'ingot' ); ?>
					</a>
				</span>
			</div>
		</div>
		<?php
		$html = ob_get_clean();
		foreach( array( 'ID', 'name' ) as $field ) {
			$html = str_replace( '{{' . $field . '}}', $group[ $field ], $html );
		}

		$id = $group[ 'ID' ];
		$link = $this->click_group_edit_link( (int) $id );

		$html = str_replace( '{{link}}', $link, $html  );
		$html = str_replace( '{{stats}}', $this->stats_page_link( $id ), $html );

		return $html;
	}

	/**
	 * @param array $part_config
	 * @param string $default_botton_color
	 *
	 * @return mixed|string
	 */
	protected function test_field_group( $part_config = array() ) {
		$new = false;
		if( empty( $part_config ) || ! isset( $part_config[ 'ID' ] ) ){
			$new = true;
		}

		$part_config = wp_parse_args(
			$part_config,
			array(
				'ID' => '-ID_' . rand(),
				'name' => null,
				'text' => null,
				'meta' => array()
			)
		);


		$background_color = helpers::get_background_color_from_meta( $part_config );
		$color = helpers::get_color_from_meta( $part_config );

		$current = array_intersect_key( $part_config, array_flip( array( 'ID', 'color', 'text' ) ) );
		ob_start();

		include( INGOT_UI_PARTIALS_DIR . 'click-test-part.php' );
		$template = ob_get_clean();

		return $template;

	}

	protected function get_markup_for_saved_tests( $tests ) {
		$out = '';
		if ( ! empty( $tests ) && is_array( $tests ) ) {
			foreach ( $tests as $test_id ) {
				$test = test::read( $test_id );
				if ( is_array( $test ) ) {
					$out .= $this->test_field_group( $test );
				}

			}

		}

		if ( empty( $out ) ) {
			$out = $this->test_field_group( array() );
		}

		return $out;
	}


}

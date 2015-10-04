<?php
/**
 * UI elements and a ton of other shit for admin that needs broken up into other classes.
 *
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\ui\admin;


use ingot\testing\crud\group;
use ingot\testing\crud\test;
use ingot\testing\tests\click\click;
use ingot\testing\utility\helpers;

class screens {

	/**
	 * @since 0.0.6
	 */
	function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'wp_ajax_test_field_group', array( $this, 'get_test_field_group' ) );
		add_action( 'wp_ajax_get_click_page', array( $this, 'get_click_page' ) );
	}

	/**
	 * Add menu page
	 */
	public function add_menu() {

		add_menu_page(
			__( 'Ingot', 'ingot'),
			__( 'Ingot', 'ingot'),
			'manage_options',
			'ingot',
			array( $this, 'ingot_page' ),
			'dashicons-smiley',
			40
		);
	}

	/**
	 * Render the ingot admin page, by context
	 */
	function ingot_page() {
		if ( $this->check_nonce() ) {
			if ( isset( $_GET['group' ] ) ) {
				echo $this->click_group_page();
			}else{
				echo $this->main_page();
			}
		}else{
			status_header( 500 );
		}

		die();

	}

	/**
	 * AJAX handler for getting main page
	 *
	 * @uses "wp_ajax_test_field_group"
	 *
	 * @since 0.0.6
	 */
	public function get_main_page() {
		if( $this->check_nonce() ) {
			echo $this->main_page( absint( helpers::v( 'page_number', 'get', 1 ) ) );

		}

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
		$new_link = $this->group_edit_link( false );
		include_once( dirname( __FILE__ ) . '/views/click-test-list.php');

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

		return sprintf( '<a href="%s" class="%s" id="next-page">%s</a>', esc_url( $this->admin_page_link( $page ) ), $class, __( 'Next Page', 'ingot' ) );

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

		return sprintf( '<a href="%s" class="%s" id="next-page">%s</a>', esc_url( $this->admin_page_link( $page ) ),  $class, __( 'Previous Page', 'ingot' ) );
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
			if ( isset( $_GET['group'] ) && is_numeric( $_GET['group'] ) ) {
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
		$back_link = $this->admin_page_link();

		$group = wp_parse_args(
			$group,
			array(
				'ID' => 0,
				'name' => '',
				'parts' => array(),
				'results' => array(),
				'type' => 'link',
				'initial' => 50,
				'threshold' => 20,
				'selector' => '',
				'link' => '',
				'order'=> array(),
			)
		);

		$tests = $this->get_markup_for_saved_tests( $group[ 'order' ] );

		$click_options = array_combine( array_keys( $this->click_types() ), wp_list_pluck( $this->click_types(), 'label' )  );
		include_once( dirname( __FILE__ ) . '/views/click-test.php' );
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
				'label' => __( 'Button', 'ingot' ),
				'desc' => __( 'A clickable button, with testable text.', 'ingot' )
			),
			'text' => array(
				'label' => __( 'Text', 'ingot' ),
				'desc' => __( 'Testable text, with another element as the click test.', 'ingot' )
			)
		);
	}

	/**
	 * Get group inputs via AJAX
	 */
	public function get_test_field_group() {
		echo $this->test_field_group();
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
			<div class="button-pair">
				<span>
					<a href="{{link}}" class="group-edit button button-secondary" data-group-id="{{ID}}">
						<?php _e( 'Edit Group', 'ingot' ); ?>
					</a>
				</span>
				<span>
					<a href="#" class="group-stats button button-secondary" data-group-id="{{ID}}">
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
		$link = $this->group_edit_link( (int) $id );

		$html = str_replace( '{{link}}', $link, $html  );

		return $html;
	}

	/**
	 * @param array $part_config
	 *
	 * @return mixed|string
	 */
	protected function test_field_group( $part_config = array() ) {
		$part_config = wp_parse_args(
			$part_config,
			array(
				'ID' => '-ID_' . rand(),
				'name' => null,
				'text' => null,
			)
		);
		$current = array_intersect_key( $part_config, array_flip( array( 'ID', 'name', 'text', ) ) );
		ob_start();

		?>
		<div class="test-part" id="{{ID}}" data-current="<?php echo esc_attr( wp_json_encode( $current ) ); ?>">
			<input type="hidden"  class="test-part-id" value="{{ID}}" aria-hidden="true" id="part-hidden-id-{{ID}}">
			<div class="ingot-config-group">
				<label>
					<?php _e( 'Name', 'ingot' ); ?>
				</label>
				<input type="text" class="test-part-name" value="{{name}}" required aria-required="true" id="name-{{ID}}">
			</div>
			<div class="ingot-config-group">
				<label>
					<?php _e( 'Text', 'ingot' ); ?>
				</label>
				<input type="text" class="test-part-text" value="{{text}}" required aria-required="true" id="text-{{ID}}">
				<a href="#" class="button part-remove" alt="<?php esc_attr_e( 'Click To Remove Test', 'ingot' ); ?>" data-part-id="{{ID}}" id="remove-{{ID}}">
					<?php _e( 'Remove' ); ?>
				</a>
			</div>


		</div>
		<?php
			$template = ob_get_clean();
			$id = $part_config[ 'ID' ];
			foreach( array( 'name', 'text', 'ID' ) as $field ) {
				if ( isset( $part_config[ $field ] ) ) {
					$value = esc_attr( $part_config[ $field ] );
				}else{
					if( 'ID' == $field ) {
						$value = $id;
					}else{
						$value = '';
					}
				}

				$template = str_replace( '{{' . $field . '}}', $value, $template );
			}

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
			$out = $this->test_field_group( );
		}

		return $out;
	}

	protected function group_edit_link( $id = false ) {
		if ( false === $id || 0 == absint( $id ) ){
			$id = 'new';
		}

		$page = $this->admin_page_link();

		$link = add_query_arg( 'group', $id, $page );
		return $link;
	}

	protected function admin_page_link( $page_number = 1 ) {
		$args = array(
			'page' => 'ingot',
			'page_number' => absint( $page_number )
		);
		return add_query_arg( $args, admin_url( 'admin.php' ) );
	}

	public function scripts() {
		wp_enqueue_script( 'swal', '//cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.0/sweetalert.min.js', array( 'jquery') );
		wp_enqueue_style( 'swal', '//cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.0/sweetalert.min.css');
		wp_enqueue_style( 'ingot-click-test-style', INGOT_URL . 'assets/admin/css/click-test-admin.css' );
		wp_enqueue_script( 'ingot-click-test', INGOT_URL . 'assets/admin/js/click-test-admin.js', array( 'jquery', 'swal'), rand() );
		wp_localize_script( 'ingot-click-test', 'INGOT', array(
				'api_url' => rest_url( 'ingot/v1'),
				'test_field' => esc_url_raw( add_query_arg( 'action', 'test_field_group', admin_url( 'admin-ajax.php' ) ) ),
				'nonce' => wp_create_nonce( 'wp_rest' ),
				'test_group_page_title' => __( 'Ingot Test Group: ', 'ingot' ),
				'success' => __( 'Group Saved', 'ingot' ),
				'fail' => __( 'Could Not Save', 'ingot' ),
				'close' => __( 'Close', 'ingot' ),
				'saved' => __( 'Saved Group: ', 'ingot'),
				'cant_remove' => __( 'At this time, you can not remove a test from a group.', 'ingot' ),
				'beta_error_header' => __( 'Beta Limitation Encountered', 'ingot' ),
				'no_stats' => __( 'We do not have a functional stats viewer yet.', 'ingot' ),
				'deleted' => __( 'Test Group Deleted', 'ingot' ),

			)
		);


	}

	private function check_nonce() {
		return true;
	}
}

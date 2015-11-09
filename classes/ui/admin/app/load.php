<?php
/**
 * Load our admin Angular App
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\ui\admin\app;


use ingot\testing\api\rest\util;
use ingot\testing\utility\helpers;

class load {

	/**
	 * Menu slug
	 *
	 * @since 0.2.0
	 *
	 * @access protected
	 *
	 * @var string
	 */
	protected $menu_slug = 'ingot-admin-app';

	/**
	 * Constructors this class
	 *
	 * @since 0.2.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		if( $this->menu_slug === helpers::v( 'page', $_GET, 0 ) ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );;
		}
	}

	/**
	 * Add menu page
	 *
	 * @uses "admin_menu"
	 *
	 * @since 0.2.0
	 */
	public function add_menu() {
		add_menu_page(
			__( 'Ingot', 'ingot' ),
			__( 'Ingot', 'ingot' ),
			'manage_options',
			$this->menu_slug,
			array( $this, 'ingot_page' ),
			'dashicons-smiley',
			40
		);
	}

	/**
	 *
	 */
	public function scripts() {


		wp_enqueue_script( 'angular', '//ajax.googleapis.com/ajax/libs/angularjs/1.3.0/angular.min.js', array( 'jquery') );
		wp_enqueue_script( 'angular-ui-route', '//cdnjs.cloudflare.com/ajax/libs/angular-ui-router/0.2.15/angular-ui-router.min.js', array( 'angular') );
		wp_enqueue_script( 'angular-ui-bootstrap', '//cdnjs.cloudflare.com/ajax/libs/angular-ui-bootstrap/0.14.3/ui-bootstrap.min.js');
		wp_enqueue_style( 'bootstrap', '//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/css/bootstrap.min.css' );
		wp_enqueue_style( 'selectize', '//cdnjs.cloudflare.com/ajax/libs/selectize.js/0.8.5/css/selectize.default.css');
		wp_enqueue_style( 'bootstrap-colorpicker-module', '//cdnjs.cloudflare.com/ajax/libs/angular-bootstrap-colorpicker/3.0.19/css/colorpicker.min.css');
		wp_enqueue_script( 'bootstrap-colorpicker-module', '//cdnjs.cloudflare.com/ajax/libs/angular-bootstrap-colorpicker/3.0.19/js/bootstrap-colorpicker-module.js');
		wp_enqueue_script( 'bootstrap', '//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js', array( 'jquery') );
		wp_enqueue_script( 'swal', '//cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.0/sweetalert.min.js', array( 'jquery') );
		wp_enqueue_style( 'swal', '//cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.0/sweetalert.min.css');

		wp_enqueue_script( 'ingot-admin-app', INGOT_URL . 'assets/admin/js/admin-app.js', array( 'angular', 'jquery', 'swal' ), rand() );

		//data to use in admin app
		wp_localize_script( 'ingot-admin-app', 'INGOT_ADMIN', $this->vars()
		);

		//translation strings for admin app
		wp_localize_script( 'ingot-admin-app', 'INGOT_I10N', $this->translation_strings()
		);



	}

	/**
	 * Initial markup for admin page
	 *
	 * @TODO move this into a partial
	 *
	 * @since 0.2.0
	 */
	public function ingot_page() {?>
		<div class="container" id="ingot-admin-app" ng-app="ingotApp">
			<nav class="navbar navbar-default">
				<div class="container-fluid">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
							<span class="sr-only"><?php _e( 'Toogle Navigation', 'ingot' ); ?></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<a class="navbar-brand active" href="#"><?php _e( 'Ingot', 'ingot' ); ?></a>
					</div>
					<div id="navbar" class="navbar-collapse collapse">
						<ul class="nav navbar-nav">
							<li><a ui-sref="clickTests"><?php _e( 'Click Tests', 'ingot' ); ?></a></li>
							<li><a ui-sref="state2"><?php _e( 'Price Tests', 'ingot' ); ?></a></li>
						</ul>
						<ul class="nav navbar-nav navbar-right">
							<li><a href="#"><?php _e( 'Settings', 'ingot' ); ?></a></li>
							<li><a href="#"><?php _e( 'Support', 'ingot' ); ?></a></li>
						</ul>
					</div>
				</div>
			</nav>
			<div ui-view></div>

		</div>

	<?php
	}



	/**
	 * Translation strings
	 *
	 * @since 0.2.0
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected function translation_strings() {
		return array(
			'api_url'                  => rest_url( 'ingot/v1' ),
			'test_group_page_title'    => __( 'Ingot Test Group: ', 'ingot' ),
			'success'                  => __( 'Group Saved', 'ingot' ),
			'fail'                     => __( 'Could Not Save', 'ingot' ),
			'close'                    => __( 'Close', 'ingot' ),
			'saved'                    => __( 'Saved Group: ', 'ingot' ),
			'cant_remove'              => __( 'At this time, you can not remove a test from a group.', 'ingot' ),
			'beta_error_header'        => __( 'Beta Limitation Encountered', 'ingot' ),
			'no_stats'                 => __( 'We do not have a functional stats viewer yet.', 'ingot' ),
			'deleted'                  => __( 'Test Group Deleted', 'ingot' ),
			'are_you_sure'             => __( 'Are You Sure About That?', 'ingot' ),
			'delete_confirm'           => __( 'Deleting all groups is not reversible or undoable.', 'ingot' ),
			'delete'                   => __( 'Delete', 'ingot' ),
			'cancel'                   => __( 'Cancel', 'ignot' ),
			'canceled'                 => __( 'Canceled', 'ingot' ),
			'spinner_alt'              => __( 'Loading Spinner', 'ingot' ),
			'no_tests'                 => __( 'This group has no tests', 'ingot' ),
			'invalid_price_test_range' => __( 'Please enter a number between -.99 and .99', 'ingot' ),

		);
	}

	/**
	 * Data needed in app
	 *
	 * @since 0.2.0
	 *
	 * @access protected
	 *
	 * @return array
	 */
	protected function vars() {
		return array(
			'api'                 => esc_url_raw( util::get_url() ),
			'nonce'               => wp_create_nonce( 'wp_rest' ),
			'partials'            => esc_url_raw( INGOT_URL . 'assets/admin/partials/' ),
			'spinner_url'         => trailingslashit( INGOT_URL ) . 'assets/img/loading.gif',
			'edd_active'          => esc_attr( ingot_is_edd_active() ),
			'woo_active'          => esc_attr( ingot_is_woo_active() ),
			'price_tests_enabled' => esc_attr( ingot_enable_price_testing() )
		);
	}

}

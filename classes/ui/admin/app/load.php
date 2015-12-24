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
use ingot\testing\types;
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
	 * Load scripts
	 *
	 * @todo Figure out how much of this is uneeded, switch the rest to local files managed with Bower or whatever
	 *
	 * @uses "admin_enqueue_scripts
	 *
	 * @since 0.2.0
	 */
	public function scripts() {
		$version = INGOT_VER;
		$min = '.min';
		if( SCRIPT_DEBUG ) {
			$version = rand();
			$min = '';
		}
		$bower = INGOT_URL . '/bower_components/';
		
		//jquery ui
		wp_enqueue_script('jquery-ui-core');
		//wp_enqueue_script('jquery-ui-slider');
		//wp_enqueue_style('jquery-ui-css', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css' );

		//angular
		wp_enqueue_script( "angular", $bower . "angular/angular{$min}.js", array(), true, $version  );
		wp_enqueue_script( "angular-ui-route", $bower . "angular-ui-router/release/angular-ui-router{$min}.js", array( "angular" ), true, $version );
		wp_enqueue_script( "angular-aria",  $bower . "angular-aria/angular-aria{$min}.js", array( "angular" ), true, $version );
		wp_enqueue_script( "angular-resource", $bower . "angular-resource/angular-resource{$min}.js", array( "angular" ) );
		wp_enqueue_script( "messageformat", $bower . "messageformat/messageformat.js", array( "angular" ) );
		wp_enqueue_script( "angular-sanitize", $bower . "angular-sanitize/angular-sanitize{$min}.js", array( "angular" ) );
		wp_enqueue_script( "angular-translate", $bower . "angular-translate/angular-translate{$min}.js", array( "angular" ) );
		wp_enqueue_script( "angular-translate-interpolation-messageformat", $bower . "angular-translate-interpolation-messageformat/angular-translate-interpolation-messageformat{$min}.js", array( "angular" ) );
		wp_enqueue_script( "angular-translate-storage-local", $bower . "angular-translate-storage-local/angular-translate-storage-local{$min}.js", array( "angular" ) );
		wp_enqueue_style( "angular-bootstrap-colorpicker", $bower . "angular-bootstrap-colorpicker/css/colorpicker{$min}.css" );
		wp_enqueue_script( "angular-bootstrap-colorpicker", $bower . "angular-bootstrap-colorpicker/js/bootstrap-colorpicker-module{$min}.js" );

		//bootstrap
		wp_enqueue_script( "angular-ui-bootstrap", $bower . "angular-bootstrap/ui-bootstrap{$min}.js" );
		wp_enqueue_style( "bootstrap", $bower ."bootstrap/dist/css/bootstrap{$min}.css" );
		//wp_enqueue_script( "bootstrap", "//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js", array( "jquery" ) );


		// ChartJS
		wp_enqueue_script( "chart-js", $bower . "Chart.js/Chart{$min}.js", array( "angular", "jquery" ), rand() );

		//sweet alert --Josh: Don't unmin.
		wp_enqueue_script( 'sweet-alert', $bower . 'sweetalert/dist/sweetalert.min.js', array( 'jquery' ) );
		wp_enqueue_style( 'sweet-alert', $bower . 'sweetalert/dist/sweetalert.css' );

		//ingot
		wp_enqueue_script( 'ingot-admin-app', INGOT_URL . 'assets/admin/js/admin-app.js', array( 'angular', 'jquery', 'sweet-alert' ), rand() );
		wp_enqueue_style( 'ingot-admin-app', INGOT_URL . 'assets/admin/css/admin-app.css' );

		//data to use in admin app
		wp_localize_script( 'ingot-admin-app', 'INGOT_ADMIN', $this->vars() );

		//translation strings for admin app
		wp_localize_script( 'ingot-admin-app', 'INGOT_TRANSLATION', $this->translation_strings() );



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
							<span class="sr-only">
								<?php _e( 'Toogle Navigation', 'ingot' ); ?>
							</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<a class="navbar-brand active" ui-sref="otherwise" >
							<?php
								_e( 'Ingot', 'ingot' );
								printf( ' <small>%s</small>',  INGOT_VER );
							?>
						</a>
					</div>
					<div id="navbar" class="navbar-collapse collapse">
						<ul class="nav navbar-nav">
							<li ng-class="isActiveNav('clickTests');">
								<a ui-sref="clickTests">
									<?php _e( 'Content Tests', 'ingot' ); ?>
								</a>
							</li>
							<li ng-class="isActiveNav('price');">
								<a ui-sref="priceTests">
									<?php _e( 'Price Tests', 'ingot' ); ?>
								</a>
							</li>
						</ul>
						<ul class="nav navbar-nav navbar-right">
							<li>
								<a ui-sref="settings">
									<?php _e( 'Settings', 'ingot' ); ?>
								</a>
							</li>
							<li>
								<a ui-sref="support">
									<?php _e( 'Support', 'ingot' ); ?>
								</a>
							</li>
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
			'group_saved'              => __( 'Group Saved', 'ingot' ),
			'fail'                     => __( 'Could Not Save', 'ingot' ),
			'sorry'                    => __( 'Please try again and/or contact support', 'ingot' ),
			'close'                    => __( 'Close', 'ingot' ),
			'saved'                    => __( 'Saved Group: ', 'ingot' ),
			'cant_remove'              => __( 'At this time, you can not remove a test from a group.', 'ingot' ),
			'beta_error_header'        => __( 'Beta Limitation Encountered', 'ingot' ),
			'beta_message'              => __( 'Sorry about that but Ingot is still in beta. We will be adding this feature soon.', 'ingot' ),
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
			'settings_saved'           => __( 'Settings Saved', 'ingot' ),
			'groups' => array(
				'click_group_page_title' => __( 'Content Test Groups', 'ingot' ),
				'price_group_page_title' => __( 'Price Test Groups', 'ingot' ),
				'show_all'               => __( 'Show All', 'ingot' ),
				'create_new'             => __( 'Create New', 'ingot' ),
				'edit'  => __( 'Edit Group', 'ingot' ),
				'stats' => __( 'Group Stats', 'ingot' ),
				'delete' => __( 'Delete Group', 'ingot' ),
			),
			'group' => array(
				'save_group'                           => __( 'Save Group', 'ingot' ),
				'type'                                 => __( 'Type', 'ingot' ),
				'name'                                 => __( 'Name', 'ingot' ),
				'group_settings_header'                => __( 'Group Settings', 'ingot' ),
				'link_label_group_setting'             => __( 'Link', 'ingot' ),
				'text_label_group_setting'             => __( 'Text (Used For All Buttons)', 'ingot' ),
				'color_label_group_setting'            => __( 'Color (Used For All Buttons)', 'ingot' ),
				'background_color_label_group_setting' => __( 'Background (Used For All Buttons)', 'ingot' ),
				'tests_header'                         => __( 'Tests', 'ingot' ),
				'text_label_test_setting'              => __( 'Text', 'ingot' ),
				'color_label_test_setting'             => __( 'Button Text Color', 'ingot' ),
				'background_color_label_test_setting'  => __( 'Button Background Color', 'ingot' ),
				'add_test'                             => __( 'Add Test', 'ingot' ),
				'plugin' => __( 'eCommerce Plugin', 'ingot' ),
				'product' => __( 'Product', 'ingot' ),
				'price_variation' => __( 'Price Variation (percentage)', 'ingot '),
				'delete' => __( 'Delete', 'ingot' )
			),
			'settings' => array(
				'page_header' => __( 'Settings', 'ingot' ),
				'cache_mode_label' => __( 'Work around caching', 'ingot' ),
				'cache_mode_desc' => __( 'If you are using a static HTML cache testing will not work properly, since the same version of your site is shown to all visitors. Use this mode to work around this issue.', 'ingot' ),
				'click_tracking_label' => __( 'Advanced Click Tracking', 'ingot' ),
				'click_tracking_desc' => __( 'Ingot always tracks clicks, in advanced mode, more details are tracked. This takes up more space in the database, but enables Ingot to be more powerful.', 'ingot' ),
				'anon_tracking_label' => __( 'Share Your Data Anonymously', 'ingot' ),
				'anon_tracking_desc' => __( 'When enabled, your test data is shared with Ingot to help us improve the service.', 'ingot' ),
				'license_code_label' => __( 'License Code', 'ingot' ),
				'license_code_desc' => __( 'Enter your license code to enable support and updates.', 'ingot' ),
				'save' => __( 'Save Settings', 'ingot' )

			),
			'welcome' => array(
				'banner'                   => esc_url( INGOT_URL . 'assets/img/Ingot-logo-dark.png' ),
				'banner_alt' => __( 'Ingot Banner Logo', 'ingot' ),
				'header' => __( 'Ingot: Do Less, Convert More', 'ingot' ),
				'links_header' => __( 'Helpful Links', 'ingot' ),
				'video_header' => __( 'Watch This Short Video To Learn How To Use Ingot', 'ingot' ),
				'price_tests' => __( 'Price Tests', 'ingot' ),
				'click_tests' => __( 'Click Tests', 'ingot' ),
				'learn_more' => __( 'Learn more about Ingot', 'ingot' ),
				'docs' => __( 'Documentation', 'ingot' ),
				'support' => __( 'Support', 'ingot' )
			)

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
			'price_tests_enabled' => esc_attr( ingot_enable_price_testing() ),
			'click_type_options'  => types::allowed_click_types( true ),
			'price_type_options'  => types::allowed_price_types(),
		);
	}

}

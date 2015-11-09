<?php
/**
 * @TODO What this does.
 *
 * @package   @TODO
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\ui\admin\app;


use ingot\testing\api\rest\util;
use ingot\testing\utility\helpers;

class load {

	protected $menu_slug = 'ingot-admin-app';


	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		if( $this->menu_slug === helpers::v( 'page', $_GET, 0 ) ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
			add_action( 'admin_head', array( $this, 'inline' ) );
		}
	}

	/**
	 * Add menu page
	 */
	public function add_menu() {

		add_menu_page(
			__( 'Ingot', 'ingot'),
			__( 'Ingot', 'ingot'),
			'manage_options',
			$this->menu_slug,
			array( $this, 'ingot_page' ),
			'dashicons-smiley',
			40
		);
	}

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

		wp_localize_script( 'ingot-admin-app', 'INGOT_ADMIN', array(
				'api' => esc_url_raw( util::get_url() ),
				'nonce' => wp_create_nonce( 'wp_rest' ),
				'partials' => esc_url_raw( INGOT_URL . 'assets/admin/partials/' )
			)
		);



	}

	public function inline() {
		return;
		?>
		<script type="text/javascript">
			angular.element( document.getElementsByTagName( 'head' ) ).append( angular.element( '<base href="' + window.location.pathname + '" />' ) );
		</script>
		<?php
	}

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
					</div><!--/.nav-collapse -->
				</div><!--/.container-fluid -->
			</nav>
			<div ng-controller="alerts">
				<uib-alert ng-repeat="alert in alerts" type="{{alert.type}}" close="closeAlert($index)">{{alert.msg}}</uib-alert>

			</div>
			<div ui-view></div>

		</div>

	<?php
	}

	protected function admin_url( $hash = false) {
		$url = add_query_arg( 'page', $this->menu_slug, admin_url( 'admin.php' ) );
		if( $hash ) {
			$url = untrailingslashit( $url ) . $hash;
		}

		return $url;
	}

}

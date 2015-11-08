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
		wp_enqueue_script( 'angular-route', '//ajax.googleapis.com/ajax/libs/angularjs/1.3.0/angular-route.js', array( 'angular') );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'swal', '//cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.0/sweetalert.min.js', array( 'jquery') );
		wp_enqueue_style( 'swal', '//cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.0/sweetalert.min.css');

		wp_enqueue_script( 'ingot-admin-app', INGOT_URL . 'assets/admin/js/admin-app.js', array( 'angular', 'jquery', 'swal', 'wp-color-picker' ), rand() );


		wp_localize_script( 'ingot-admin-app', 'INGOT_ADMIN', array(
				'api_url' => esc_url_raw( util::get_url() ),
				'rest_nonce' => wp_create_nonce( 'wp_rest' ),
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
		<div ng-app="ingotApp">
			<div>
				<ul>
					<li><a href="<?php echo esc_url( $this->admin_url( '' ) ); ?>">Home</a></li>`
					<li><a href="<?php echo esc_url( $this->admin_url( '#!click-groups' ) ); ?>">Click Groups</a></li>
				</ul>
			</div>
			<div ng-controller="clickGroups">
				<article ng-repeat="group in groups">
					<h3>{{ group.name }}</h3>
					<a href="#click-groups/{{group.ID}}" class="button">Edit</a>
					<a href="#click-groups/stats/{{group.ID}}" class="button">Stats</a>
				</article>
			</div>



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

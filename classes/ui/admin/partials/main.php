<?php
/**
 * Markup for main admin
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */
?>
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
						printf( '<img src="%s" alt="%s" class="nav-logo" />', INGOT_URL . 'assets/img/ingot-logo-s.png', __( 'Ingot', 'ingot' ) );
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
						<?php
							/**
							 * Fires, in admin markup, after left side nav items -- inside of ul
							 *
							 * @since 1.1.0
							 */
							do_action( 'ingot_nav_items_left' );
						?>
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
						<?php
							/**
							 * Fires, in admin markup, after right side nav items -- inside of ul
							 *
							 * @since 1.1.0
							 */
							do_action( 'ingot_nav_items_right' );
						?>
					</ul>
				</div>
			</div>
		</nav>
		<?php
			/**
			 * Fires, in admin markup, before ui-view.
			 *
			 * @since 1.1.0
			 */
			do_action( 'ingot_nav_before_ui_view' );
		?>
		<div ui-view></div>
		<?php
			/**
			 * Fires, in admin markup, before ui-view.
			 *
			 * @since 1.1.0
			 */
			do_action( 'ingot_nav_after_ui_view' );
		?>
	</div>


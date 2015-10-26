<?php
/**
 * Admin page for listing price tests
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
?>
<div id="price-test-group-admin" class="ingot-admin-wrap">
	<h1>
		<?php _e( 'All Price Tests', 'ingot' ); ?>
	</h1>
	<section id="groups-list">
		<?php echo $groups_inner_html; ?>
	</section>
	<section id="options">
		<a href="<?php echo esc_url( $new_link ); ?>" id="new-group" data-group-type="click" class="button button-secondary">
			<?php _e( 'New Group', 'ingot' ); ?>
		</a>
		<a href="<?php echo esc_url( $main_page_link ); ?>" id="main-page-link" class="button button-secondary">
			<?php _e( 'Back To Main Ingot Page', 'ingot' ); ?>
		</a>
		<div id="options-area">
			<h3>
				<?php _e( 'Options', 'ingot' ); ?>
			</h3>
			<a href="#" id="delete-all-groups" data-group-type="price" class="button button-secondary">
				<?php _e( 'Delete All', 'ignot' ); ?>
			</a>
			<?php echo $settings_form; ?>
		</div>
	</section>
	<div class="clear"></div>
	<div class="navigation button-pair">
		<?php echo $next_button.$prev_button; ?>
	</div>
</div>



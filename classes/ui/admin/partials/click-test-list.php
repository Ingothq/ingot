<?php
/**
 * Admin page for listing click tests
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
?>
<div id="test-group-admin" class="ingot-admin-wrap">


	<h1>
		<?php _e( 'All Click Tests', 'ingot' ); ?>
	</h1>
	<section id="groups-list">
		<?php echo $groups_inner_html; ?>
	</section>
	<section id="options">
		<a href="<?php echo esc_url( $new_link ); ?>" id="new-group" class="button button-secondary">
			<?php _e( 'New Group', 'ingot' ); ?>
		</a>
		<div id="options-area">
			<h3>
				<?php _e( 'Options', 'ingot' ); ?>
			</h3>
			<a href="#" id="delete-all-groups" class="button button-secondary">
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



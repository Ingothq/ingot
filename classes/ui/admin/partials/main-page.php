<?php
/**
 * Main admin page
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
?>
<div id="main-admin" class="ingot-admin-wrap">


	<h1>
		<?php _e( 'Ingot', 'ingot' ); ?>
	</h1>
	<section id="main-options">
		<div class="main-options-group">
			<a href="<?php echo esc_url( $new_click_link ); ?>" id="new-group" class="button button-secondary">
			<?php _e( 'New Click Test Group', 'ingot' ); ?>
			</a>
			<a href="<?php echo esc_url( $all_click_link ); ?>" id="new-group" class="button button-secondary">
				<?php _e( 'All Click Tests', 'ingot' ); ?>
			</a>
		</div>
		<div class="main-options-group">
			<a href="<?php echo esc_url( $new_price_link ); ?>" id="new-group" class="button button-secondary">
				<?php _e( 'New Price Test Group', 'ingot' ); ?>
			</a>
			<a href="<?php echo esc_url( $all_price_link ); ?>" id="new-group" class="button button-secondary">
				<?php _e( 'All Prices Tests', 'ingot' ); ?>
			</a>
		</div>
	</section>
	<section id="options">
		<div id="options-area">
			<h3>
				<?php _e( 'Ingot Settings', 'ingot' ); ?>
			</h3>
			<?php echo $settings_form; ?>
		</div>
	</section>
	<div class="clear"></div>
</div>


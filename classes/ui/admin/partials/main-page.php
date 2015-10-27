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
			<h3>
				<?php _e( 'Click Tests', 'ingot' ); ?>
			</h3>
			<p>
				<?php _e( 'Use different text, buttons, colors and Ingot will figure out what generates the most clicks.', 'ingot' ); ?>
			</p>
			<a href="<?php echo esc_url( $new_click_link ); ?>" id="new-click-group" class="button button-secondary">
			<?php _e( 'New Click Test Group', 'ingot' ); ?>
			</a>
			<a href="<?php echo esc_url( $all_click_link ); ?>" id="all-click-group" class="button button-secondary">
				<?php _e( 'All Click Tests', 'ingot' ); ?>
			</a>
		</div>
		<div class="main-options-group">
			<h3>
				<?php _e( 'Price Tests', 'ingot' ); ?>
			</h3>
			<div id="price-tests-disabled" style="visibility: hidden;" aria-hidden="true">
				<p>
					<strong><?php _e( 'Price Tests Are Disabled.', 'ingot' ); ?></strong>
					<?php _e( 'Please activate a compatible eCommerce plugin.', 'ingot' ); ?>
				</p>
			</div>
			<p>
				<?php _e( 'Test product pricing to increase revenue.', 'ingot' ); ?>
			</p>
			<a href="<?php echo esc_url( $new_price_link ); ?>" id="new-price-group" class="button button-secondary">
				<?php _e( 'New Price Test Group', 'ingot' ); ?>
			</a>
			<a href="<?php echo esc_url( $all_price_link ); ?>" id="all-price-group" class="button button-secondary">
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


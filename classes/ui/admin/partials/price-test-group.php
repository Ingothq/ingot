<?php
/**
 * Admin screen for a price test
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
?>
<div id="price-group-admin" class="ingot-admin-wrap" xmlns="http://www.w3.org/1999/html">


	<h1>
		<?php _e( 'Price Test Group', 'ingot' ); ?>
	</h1>
	<section id="general">
		<form id="ingot-price-test-group" class="ingot-admin-form" name="ingot-price-group-editor">
			<input id="test-group-id" value="<?php echo esc_attr( $group[ 'ID'] ); ?>" type="hidden">
			<input id="test-product-id" value="<?php echo esc_attr( $group[ 'product_ID'] ); ?>" type="hidden">
			<input id="test-group-plugin" value="<?php echo esc_attr( $group[ 'plugin' ] ); ?>" type="hidden">

			<h3>
				<?php _e( 'General', 'ingot' ); ?>
			</h3>
			<div class="ingot-config-group" id="group-name-group">
				<label for="group-name">
					<?php _e( 'Group Name', 'ingot' ); ?>
				</label>
				<input id="group-name" type="text" required value="<?php esc_attr_e( $group[ 'group_name' ] ); ?>">
			</div>
			<div class="ingot-config-group">
				<label for="initial">
					<?php _e( 'Initial', 'ingot' ); ?>
				</label>
				<input id="initial" type="number" value="<?php echo esc_attr( $group[ 'initial' ] ); ?>" min="0" required>
			</div>

			<div class="ingot-config-group">
				<label for="threshold">
					<?php _e( 'Threshold', 'ingot' ); ?>
				</label>
				<input id="threshold" type="number" value="<?php echo esc_attr( $group[ 'threshold' ] ); ?>" min="0" max="100" required>
			</div>


			<input type="submit" class="button button-primary" id="save-group" value="<?php _e( 'Save', 'ingot' ); ?>" name="save">

		</form>
	</section>
	<section id="parts" aria-live="assertive">
		<h3>
			<?php _e( 'Price Tests', 'ingot' ); ?>
		</h3>
		<div>
			<a hre="#price-tests" id="add-price-test" class="button button-secondary">
				<?php _e( 'Add Price Test To Group', 'ingot' ); ?>
			</a>
		</div>
		<div id="price-tests">
			<?php echo $price_tests; ?>
		</div>


	</section>
	<div class="clear"></div>



	<div id="spinner" style="display: none; visibility: hidden" aria-hidden="true">
		<img src="<?php echo esc_url( INGOT_URL . '/assets/img/loading.gif' ); ?>" />
	</div>
	<div id="status"></div>



	<section id="options" style="margin-top:8px;">
		<a href="<?php echo esc_url( $back_link); ?>" class="button button-secondary">
			<?php _e( 'Back', 'ingot' ); ?>
		</a>
		<a href="<?php echo esc_url( $stats_link); ?>" class="button button-secondary">
			<?php _e( 'View Stats', 'ingot' ); ?>
		</a>
	</section>

	<div class="clear"></div>
</div>

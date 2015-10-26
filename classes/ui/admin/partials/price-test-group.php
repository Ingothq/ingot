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
<div id="price-group-admin" class="ingot-admin-wrap" >


	<h1>
		<?php _e( 'Price Test Group', 'ingot' ); ?>
	</h1>
	<form id="ingot-click-test" class="ingot-admin-form" name="ingot-group-editor">
		<input id="test-group-id" value="<?php echo esc_attr( $group[ 'ID'] ); ?>" type="hidden">
		<section id="general">
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
		</section>

		<section id="parts">
			<h3>
				<?php _e( 'Prices', 'ingot' ); ?>
			</h3>

		</section>

		<div class="clear"></div>
		<input type="submit" class="button button-primary" id="save-group" value="<?php _e( 'Save', 'ingot' ); ?>" name="save">

	</form>
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

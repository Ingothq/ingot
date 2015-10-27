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
		<?php _e( 'New Price Test Group', 'ingot' ); ?>
	</h1>
	<form id="ingot-price-test-new" class="ingot-admin-form" name="ingot-group-editor">
		<section id="general">
			<div class="ingot-config-group" id="group-name-group">
				<label for="group-name">
					<?php _e( 'Group Name', 'ingot' ); ?>
				</label>
				<input id="group-name" type="text" required />
			</div>
			<div class="ingot-config-group" id="group-plugin-group">
				<label for="plugin">
					<?php _e( 'Plugin', 'ingot' ); ?>
				</label>
				<select id="group-plugin" required="true">
					<?php echo $plugin_options; ?>
				</select>
			</div>
			<div class="ingot-config-group" id="group-product_ID-group" style="visibility: hidden" aria-hidden="false">
				<label for="group-product_ID">
					<?php _e( 'Product', 'ingot' ); ?>
				</label>
				<select id="group-product_ID"></select>
			</div>

		</section>

		<section id="parts">

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
	</section>

	<div class="clear"></div>
</div>

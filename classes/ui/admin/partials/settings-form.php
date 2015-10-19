<?php
/**
 * Settings form in admin
 *
 * @package   @ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

?>
<form id="ingot-settings">
	<div class="ingot-config-group">
		<label for="click_tracking">
			<?php _e( 'Detailed Click Tracking', 'ingot' ); ?>
		</label>
		<input type="checkbox" name="click_tracking" id="click_tracking" <?php if ( $click_tracking ): echo "checked"; endif; ?> />
		<p class="description">
			<?php _e( 'Ingot always tracks clicks, but with this option enabled, details including time, browser and IP address will be tracked for each click.', 'ingot' ); ?>
		</p>
	</div>
	<div class="ingot-config-group">
		<label for="anon_tracking">
			<?php _e( 'Share Anonymous Usage Data', 'ingot' ); ?>
		</label>
		<input type="checkbox" name="anon_tracking" id="anon_tracking" <?php if ( $anon_tracking ): echo "checked"; endif; ?> value="<?php echo esc_attr( $anon_tracking ); ?>" />
		<p class="description">
			<?php _e( 'Share your data, anonymously with Ingot to help us improve the service.', 'ingot' ); ?>
		</p>
	</div>
	<div class="ingot-config-group">
		<label for="license_code">
			<?php _e( 'Ingot License Code', 'ingot' ); ?>
		</label>
		<input type="text" name="license_code" id="license_code" value="<?php echo esc_attr( $license_code ); ?> " value="<?php echo esc_attr( $license_code ); ?>" />
		<p class="description">
			<?php _e( 'A valid license code entitles you to automatic updates and support.', 'ingot' ); ?>
		</p>
	</div>
	<div class="ingot-config-group">
		<p id="license-message" aria-hidden="true"></p>
	</div>

	<div class="ingot-config-group">
		<input type="submit" class="button button-secondary" value="<?php esc_attr_e( 'Save', 'ingot' ); ?>" />
   </div>
	<div id="ingot-settings-spinner" style="display: none; visibility: hidden" aria-hidden="true">
		<img src="<?php echo esc_url( INGOT_URL . '/assets/img/loading.gif' ); ?>" />
	</div>
</form>

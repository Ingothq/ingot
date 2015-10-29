<?php
/**
 * Field for editing/ creating test in a click group
 *
 * @package   @ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
if( ! isset( $part_config, $current ) ){
	return;
}

if( ! isset( $id ) ){
	$id = $part_config[ 'ID' ];
}
?>
<div class="test-part" id="<?php echo esc_attr( $id ); ?>" data-current="<?php echo esc_attr( wp_json_encode( $current ) ); ?>" aria-live="assertive">
	<div class="test-left">
		<input type="hidden" class="test-part-id" value="<?php echo esc_attr( $id ); ?>" aria-hidden="true" id="<?php echo esc_attr( 'paart-hidden-id' . $id ); ?>">
		<?php if ( false == $new ) : ?>
			<div class="ingot-config-group">
				<label>
					<?php _e( 'ID', 'ingot' ); ?>
				</label>
				<div><pre></pre><?php echo $part_config[ 'ID']; ?></pre></div>
			</div>
		<?php endif; ?>
		<div class="ingot-config-group">
			<label for="<?php echo esc_attr( 'text-'. $id ); ?>" >
				<?php _e( 'Text', 'ingot' ); ?>
			</label>
			<input type="text" class="test-part-text" value="<?php echo esc_attr( $part_config['text'] ); ?>" required aria-required="true"
			       id="<?php echo esc_attr( 'text-'. $id ); ?>">
		</div>
		<div class="ingot-config-group button-color">
			<label for="<?php echo esc_attr( 'color-'. $id ); ?>" >
				<?php _e( 'Button Color ', 'ingot' ); ?>
			</label>
			<input type="text" class="test-part-color ingot-color-field" value="<?php echo esc_attr( $part_config['text'] ); ?>" required aria-required="true"
			       id="<?php echo esc_attr( 'color-'. $id ); ?>">
		</div
	</div>
	<div class="test-right">
		<a href="#" class="button part-remove" alt="<?php esc_attr_e( 'Click To Remove Test', 'ingot' ); ?>" data-part-id="<?php echo esc_attr( $part_config['ID'] ); ?>" id="<?php echo esc_attr( 'remove-'. $part_config['ID'] ); ?>">
			<?php _e( 'Remove' ); ?>
		</a>
	</div>
	<div class="clear"></div>
</div>
<div class="clear"></div>

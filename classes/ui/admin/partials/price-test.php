<?php
/**
 * Form for creating and modifying an individual price test
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
?>
<form id="ingot-price-test" class="ingot-admin-form" name="ingot-price-test-editor">
	<input id="test-group-id" value="<?php echo esc_attr( $group[ 'ID'] ); ?>" type="hidden">
	<section id="default-price">
		<h3>
			<?php _e( 'Default', 'ingot' ); ?>
		</h3>

	</section>
	<section id="price-variables" <?php if( ! $variable ) : ?> aria-hidden="true" style="visibility: hidden" <?php endif; ?> >
		<a href="#" class="button button-secondary" id="enable-variable">
			<?php __e( 'Enable Variable Price Tests', 'ingot' ); ?>
		</a>

	</section>

	<div class="clear"></div>
	<input type="submit" class="button button-primary" id="save-group" value="<?php _e( 'Save', 'ingot' ); ?>" name="save">

</form>
<div class="clear"></div>

<div id="spinner" style="display: none; visibility: hidden" aria-hidden="true">
	<img src="<?php echo esc_url( INGOT_URL . '/assets/img/loading.gif' ); ?>" />
</div>
<div id="status"></div>

<?php
/**
 * Admin screen for a click test
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
?>
<div id="test-group-admin" class="ingot-admin-wrap" >


	<h1>
		<?php _e( 'Click Test Group', 'ingot' ); ?>
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
				<input id="group-name" type="text" required value="<?php esc_attr_e( $group[ 'name' ] ); ?>">
			</div>

			<div class="ingot-config-group" id="group-type-group">
				<label for="group-type">
					<?php _e( 'Group Type', 'ingot' ); ?>
				</label>
				<select id="group-type">
					<?php echo ingot\ui\util::select_options( $click_options, $group[ 'click_type' ] ); ?>
				</select>
			</div>
			<div class="ingot-config-group" id="color-test-text-wrap">
				<label for="group-type">
					<?php _e( 'Default Text', 'ingot' ); ?>
				</label>
				<input id="color-test-text" type="text" value="<?php esc_attr_e( $color_test_text ); ?>">
				<p class="description">
					<?php _e( 'Text to be used for buttons in button color tests', 'ingot' ); ?>
				</p>
			</div>
		</section>

		<section id="details">
			<h3>
				<?php _e( 'Details', 'ingot' ); ?>
			</h3>

			<div class="ingot-config-group" id="link-wrap">
				<label for="link">
					<?php _e( 'Link', 'ingot' ); ?>
				</label>
				<input id="link" type="text" value="<?php echo esc_attr( $group[ 'link' ] ); ?>" >
			</div>
			<div class="ingot-config-group" id="button-color-wrap" aria-live="assertive" class="button-text-test">
				<label for="button-color" class="button-text-test">
					<?php _e( 'Text Color For Buttons', 'ingot' ); ?>
				</label>
				<input id="button-color" type="text" class="ingot-color-field button-color button-text-test" value="<?php echo esc_attr( $background_color ); ?>" />
			</div>
			<br />

			<div class="ingot-config-group" id="background-color-wrap" aria-live="assertive" class="button-text-test">
				<label for="background-color" class="button-text-test">
					<?php _e( 'Background Color For Buttons', 'ingot' ); ?>
				</label>
				<input id="background-color" type="text" class="ingot-color-field button-color button-text-test" value="<?php echo esc_attr( $color ); ?>" />
			</div>
		</section>

		<section id="parts">
			<h3>
				<?php _e( 'Tests', 'ingot' ); ?>
			</h3>
			<div class="ingot-config-group" id="group-parts-wrap">
				<a href="#" alt="<?php esc_attr_e( 'Add a test to this group', 'ingot' );?>" class="button" id="add-group">
					<?php _e( 'Add Test', 'ingot' ); ?>
				</a>
				<div class="ingot-config-group" id="group-parts">
					<?php echo $tests; ?>
				</div>
			</div>
		</section>


		<div class="clear"></div>



		<input type="submit" class="button button-primary" id="save-group" value="<?php _e( 'Save', 'ingot' ); ?>" name="save">

	</form>
	<div class="clear"></div>
</div>

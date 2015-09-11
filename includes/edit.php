<?php
/**
 * Main edit interface for single items.
 *
 * @package   Ingot
 * @author    Josh Pollock
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

$ingot = ingot\options::get_single( strip_tags( $_GET['edit'] ) );

?>
<div class="wrap ingot-wordpressmain-canvas" id="ingot-main-canvas">
	<span class="wp-baldrick spinner" style="float: none; display: block;" data-target="#ingot-main-canvas" data-before="ingot_canvas_reset" data-callback="ingot_canvas_init" data-type="json" data-request="#ingot-live-config" data-event="click" data-template="#main-ui-template" data-autoload="true"></span>
</div>

<div class="clear"></div>

<input type="hidden" class="clear" autocomplete="off" id="ingot-live-config" style="width:100%;" value="<?php echo esc_attr( json_encode($ingot) ); ?>">

<script type="text/html" id="main-ui-template">
	<?php
		/**
		 * Include main UI
		 */
		include INGOT_PATH . 'includes/templates/main-ui.php';
	?>	
</script>


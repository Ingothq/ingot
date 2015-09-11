<?php
/**
 * Main edit interface for admin page.
 *
 * @package   Ingot
 * @author    Josh Pollock
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

$ingot = ingot\options::get_single( 'ingot' );
// simplyfy creation
if( false === $ingot ){
	$ingot = array(
		'id' => 'ingot'
	);
}
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
		include INGOT_PATH . 'includes/templates/ingot-main-ui.php';
	?>	
</script>

<script type="text/javascript">
	function ingot_start_importer(){
		return {};
	}
	function ingot_create_ingot(){
		jQuery('#ingot-field-sync').trigger('refresh');
		jQuery('#ingot-save-button').trigger('click');
	}
</script>
<script type="text/html" id="import-ingot-form">
	<div class="import-tester-config-group">
		<input id="new-ingot-import-file" type="file" class="regular-text">
		<input id="new-ingot-import" value="" name="import" type="hidden">
	</div>
	{{#script}}
		jQuery( function($){

			$('#ingot_import_init').prop('disabled', true).addClass('disabled');

			$('#new-ingot-import-file').on('change', function(){
				$('#ingot_import_init').prop('disabled', true).addClass('disabled');
				var input = $(this),
					f = this.files[0],
				contents;

				if (f) {
					var r = new FileReader();
					r.onload = function(e) { 
						contents = e.target.result;
						var data;
						 try{ 
						 	data = JSON.parse( contents );
						 } catch(e){};
						 
						 if( !data || ! data['ingot-setup'] ){
						 	alert("<?php echo esc_attr( __('Not a valid Ingot export file.', 'ingot') ); ?>");
						 	input[0].value = null;
							return false;
						 }

						$('#ingot-live-config').val( contents );						
						$('#ingot_import_init').prop('disabled', false).removeClass('disabled');
					}
					if( f.type !== 'application/json' ){
						alert("<?php echo esc_attr( __('Not a valid Ingot export file.', 'ingot') ); ?>");
						this.value = null;
						return false;
					}
					r.readAsText(f);
				} else { 
					alert("Failed to load file");
					return false;
				}
			});

		});
	{{/script}}
</script>

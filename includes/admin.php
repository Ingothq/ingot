<?php
/**
 * Main admin interface for selecting items to edit/ creating or deleting items.
 *
 * @package   Ingot
 * @author    Josh Pollock
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
?>

<div class="wrap ingot-wordpressadmin-wrap" id="ingot-admin--wrap">
	<div class="ingot-main-headerwordpress">
		<h2>
			<?php _e( 'Ingot', 'ingot' ); ?>
			<span class="ingot-version">
				<?php echo INGOT_VER; ?>
			</span>
			<span class="add-new-h2 wp-baldrick" data-modal="new-ingot" data-modal-height="192" data-modal-width="402" data-modal-buttons='<?php _e( 'Create Ingot', 'ingot' ); ?>|{"data-action":"ingot_create_ingot","data-before":"ingot_create_new_ingot", "data-callback": "bds_redirect_to_ingot"}' data-modal-title="<?php _e('New Ingot', 'ingot') ; ?>" data-request="#new-ingot-form">
				<?php _e('Add New', 'ingot') ; ?>
			</span>
			<span class="ingot-nav-separator"></span>
			<span class="add-new-h2 wp-baldrick" data-modal="import-ingot" data-modal-height="auto" data-modal-width="380" data-modal-buttons='<?php _e( 'Import Ingot', 'ingot' ); ?>|{"id":"ingot_import_init", "data-action":"ingot_create_ingot","data-before":"ingot_create_new_ingot", "data-callback": "bds_redirect_to_ingot"}' data-modal-title="<?php _e('Import Ingot', 'ingot') ; ?>" data-request="ingot_start_importer" data-template="#import-ingot-form">
				<?php _e('Import', 'ingot') ; ?>
			</span>
		</h2>
	</div>

<?php

	$ingots = ingot\options::get_registry();
	if( empty( $ingots ) ){
		$ingots = array();
	}

	global $wpdb;
	
	foreach( $ingots as $ingot_id => $ingot ){

?>

	<div class="ingot-card-item" id="ingot-<?php echo $ingot[ 'id' ]; ?>">
		<span class="dashicons dashicons-smiley ingot-card-icon"></span>
		<div class="ingot-card-content">
			<h4>
				<?php echo $ingot[ 'name' ]; ?>
			</h4>
			<div class="description">
				<?php echo $ingot[ 'slug' ]; ?>
			</div>
			<div class="description">&nbsp;</div>
			<div class="ingot-card-actions">
				<div class="row-actions">
					<span class="edit">
						<a href="?page=ingot&amp;download=<?php echo $ingot[ 'id' ]; ?>&ingot-export=<?php echo wp_create_nonce( 'ingot' ); ?>" target="_blank"><?php _e('Export', 'ingot'); ?></a> |
					</span>
					<span class="edit">
						<a href="?page=ingot&amp;edit=<?php echo $ingot[ 'id' ]; ?>"><?php _e('Edit', 'ingot'); ?></a> |
					</span>
					<span class="trash confirm">
						<a href="?page=ingot&amp;delete=<?php echo $ingot[ 'id' ]; ?>" data-block="<?php echo $ingot[ 'id' ]; ?>" class="submitdelete">
							<?php _e('Delete', 'ingot'); ?>
						</a>
					</span>
				</div>
				<div class="row-actions" style="display:none;">
					<span class="trash">
						<a class="wp-baldrick" style="cursor:pointer;" data-action="ingot_delete_ingot" data-callback="ingot_remove_deleted" data-block="<?php echo $ingot['id']; ?>" class="submitdelete"><?php _e('Confirm Delete', 'ingot'); ?></a> | </span>
					<span class="edit confirm">
						<a href="?page=ingot&amp;edit=<?php echo $ingot['id']; ?>">
							<?php _e('Cancel', 'ingot'); ?>
						</a>
					</span>
				</div>
			</div>
		</div>
	</div>

	<?php } ?>

</div>
<div class="clear"></div>
<script type="text/javascript">
	
	function ingot_create_new_ingot(el){
		var ingot 	= jQuery(el),
			name 	= jQuery("#new-ingot-name"),
			slug 	= jQuery('#new-ingot-slug')
			imp 	= jQuery('#new-ingot-import'); 

		if( imp.length ){
			if( !imp.val().length ){
				return false;
			}
			ingot.data('import', imp.val() );
			return true;
		}

		if( slug.val().length === 0 ){
			name.focus();
			return false;
		}
		if( slug.val().length === 0 ){
			slug.focus();
			return false;
		}

		ingot.data('name', name.val() ).data('slug', slug.val() );

	}

	function bds_redirect_to_ingot(obj){
		
		if( obj.data.success ){

			obj.params.trigger.prop('disabled', true).html('<?php _e('Loading Ingot', 'ingot'); ?>');
			window.location = '?page=ingot&edit=' + obj.data.data.id;

		}else{

			jQuery('#new-block-slug').focus().select();
			
		}
	}
	function ingot_remove_deleted(obj){

		if( obj.data.success ){
			jQuery( '#ingot-' + obj.data.data.block ).fadeOut(function(){
				jQuery(this).remove();
			});
		}else{
			alert('<?php echo __('Sorry, something went wrong. Try again.', 'ingot'); ?>');
		}
	}
	function ingot_start_importer(){
		return {};
	}
</script>
<script type="text/html" id="new-ingot-form">
	<div class="ingot-config-group">
		<label>
			<?php _e('Ingot Name', 'ingot'); ?>
		</label>
		<input type="text" name="name" id="new-ingot-name" data-sync="#new-ingot-slug" autocomplete="off">
	</div>
	<div class="ingot-config-group">
		<label>
			<?php _e('Ingot Slug', 'ingot'); ?>
		</label>
		<input type="text" name="slug" id="new-ingot-slug" data-format="slug" autocomplete="off">
	</div>

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

						$('#new-ingot-import').val( contents );
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

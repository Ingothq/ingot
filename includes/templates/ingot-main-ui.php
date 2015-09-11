<div class="ingot-main-headerwordpress">
		<h2 class="ingot-main-title">
		<?php _e( 'Ingot - Ingot', 'ingot' ); ?>
		<span class="ingot-version">
			<?php echo INGOT_VER; ?>
		</span>
		<span class="ingot-nav-separator"></span>
			
		<span class="add-new-h2 wp-baldrick" data-action="ingot_save_config" data-load-element="#ingot-save-indicator" data-callback="ingot_handle_save" data-before="ingot_get_config_object" >
			<?php _e('Save Test Group', 'ingot') ; ?>
		</span>

		<span class="ingot-nav-separator"></span>

		<a class="add-new-h2" href="?page=ingot&amp;download=<?php echo $ingot[ 'id' ]; ?>&ingot-export=<?php echo wp_create_nonce( 'ingot' ); ?>"><?php _e('Export', 'ingot'); ?></a>

		<span class="add-new-h2 wp-baldrick" data-modal="import-ingot" data-modal-height="auto" data-modal-width="380" data-modal-buttons='<?php _e( 'Import Ingot', 'ingot' ); ?>|{"id":"ingot_import_init", "data-request":"ingot_create_ingot", "data-modal-autoclose" : "import-ingot"}' data-modal-title="<?php _e('Import Ingot', 'ingot') ; ?>" data-request="ingot_start_importer" data-template="#import-ingot-form">
			<?php _e('Import', 'ingot') ; ?>
		</span>

		<span class="ingot-nav-separator"></span>
		
		<span style="position: absolute; top: 5px;" id="ingot-save-indicator">
			<span style="float: none; margin: 10px 0px -5px 10px;" class="spinner"></span>
		</span>

	</h2>


			<div class="updated_notice_box">
			<?php _e( 'Updated Successfully', 'ingot' ); ?>
		</div>
		<div class="error_notice_box">
			<?php _e( 'Could not save changes.', 'ingot' ); ?>
		</div>
		<div class="subsubsub ingot-nav-tabs">
					
					<a class="{{#is _current_tab value="#ingot-panel-about_ingot"}}current {{/is}}" href="#ingot-panel-about_ingot">
			<?php _e('About Ingot', 'ingot') ; ?>
		</a>
		<a style="color:#666">|</a>
		<a class="{{#is _current_tab value="#ingot-panel-support"}}current {{/is}}" href="#ingot-panel-support">
			<?php _e('Support', 'ingot') ; ?>
		</a>
		<a style="color:#666">|</a>
		<a class="{{#is _current_tab value="#ingot-panel-license"}}current {{/is}}" href="#ingot-panel-license">
			<?php _e('License', 'ingot') ; ?>
		</a>
		<a style="color:#666">|</a>
		
		</div>		
		<div class="clear"></div>

	<span class="wp-baldrick" id="ingot-field-sync" data-event="refresh" data-target="#ingot-main-canvas" data-before="ingot_canvas_reset" data-callback="ingot_canvas_init" data-type="json" data-request="#ingot-live-config" data-template="#main-ui-template"></span>
</div>

<form class="wordpress-main-form " id="ingot-main-form" action="?page=ingot" method="POST">
	<?php wp_nonce_field( 'ingot', 'ingot-setup' ); ?>
	<input type="hidden" value="ingot" name="id" id="ingot-id">

	<input type="hidden" value="{{_current_tab}}" name="_current_tab" id="ingot-active-tab">

		<div id="ingot-panel-about_ingot" class="ingot-editor-panel" {{#is _current_tab value="#ingot-panel-about_ingot"}}{{else}} style="display:none;" {{/is}}>		
		<h4>
			<?php _e(' ', 'ingot') ; ?>
			<small class="description">
				<?php _e('About Ingot', 'ingot') ; ?>
			</small>
		</h4>
		<?php
			/**
			 * Include the about_ingot-panel
			 */
			include INGOT_PATH . 'includes/templates/ingot-about_ingot-panel.php';
		?>
	</div>
	<div id="ingot-panel-support" class="ingot-editor-panel" {{#is _current_tab value="#ingot-panel-support"}}{{else}} style="display:none;" {{/is}}>		
		<h4>
			<?php _e('Get help with Ingot', 'ingot') ; ?>
			<small class="description">
				<?php _e('Support', 'ingot') ; ?>
			</small>
		</h4>
		<?php
			/**
			 * Include the support-panel
			 */
			include INGOT_PATH . 'includes/templates/ingot-support-panel.php';
		?>
	</div>
	<div id="ingot-panel-license" class="ingot-editor-panel" {{#is _current_tab value="#ingot-panel-license"}}{{else}} style="display:none;" {{/is}}>		
		<h4>
			<?php _e('Manage your Ingot License', 'ingot') ; ?>
			<small class="description">
				<?php _e('License', 'ingot') ; ?>
			</small>
		</h4>
		<?php
			/**
			 * Include the license-panel
			 */
			include INGOT_PATH . 'includes/templates/ingot-license-panel.php';
		?>
	</div>


		

</form>

{{#unless _current_tab}}
	{{#script}}
		jQuery(function($){
			$('.ingot-nav-tab').first().trigger('click').find('a').trigger('click');
		});
	{{/script}}
{{/unless}}

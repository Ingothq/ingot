<div class="ingot-main-headerwordpress">
		<h2>
		<span id="ingot-name-title">{{name}}</span>
		<span class="ingot-subline">{{slug}}</span>
		<span class="ingot-nav-separator"></span>
		<span class="add-new-h2 wp-baldrick" data-action="ingot_save_config" data-load-element="#ingot-save-indicator" data-callback="ingot_handle_save" data-before="ingot_get_config_object" >
			<?php _e('Save Test Group', 'ingot') ; ?>
		</span>
		<span class="ingot-nav-separator"></span>

	</h2>
	<span style="position: absolute; margin-left: -18px;" id="ingot-save-indicator">
		<span style="float: none; margin: 16px 0px -5px 10px;" class="spinner"></span>
	</span>
			<div class="updated_notice_box">
			<?php _e( 'Updated Successfully', 'ingot' ); ?>
		</div>
		<div class="error_notice_box">
			<?php _e( 'Could not save changes.', 'ingot' ); ?>
		</div>
		<div class="subsubsub ingot-nav-tabs">
					
					<a class="{{#is _current_tab value="#ingot-panel-general"}}current {{/is}}" href="#ingot-panel-general">
			<?php _e('General', 'ingot') ; ?>
		</a>
		<a style="color:#666">|</a>
		
		</div>		
		<div class="clear"></div>

	<span class="wp-baldrick" id="ingot-field-sync" data-event="refresh" data-target="#ingot-main-canvas" data-before="ingot_canvas_reset" data-callback="ingot_canvas_init" data-type="json" data-request="#ingot-live-config" data-template="#main-ui-template"></span>
</div>
<div class="ingot-sub-headerwordpress">
	<h2 class="ingot-sub-tabs ingot-nav-tabs nav-tab-wrapper">
		<a class="{{#is _current_tab value="#ingot-panel-test_config"}}nav-tab-active {{/is}}ingot-nav-tab nav-tab" href="#ingot-panel-test_config">
			<?php _e('Test Config', 'ingot') ; ?>
		</a>
		<a class=" ingot-price-test-only {{#is _current_tab value="#ingot-panel-ecommerce_test_config"}}nav-tab-active {{/is}}ingot-nav-tab nav-tab" href="#ingot-panel-ecommerce_test_config">
			<?php _e('eCommerce Test Config', 'ingot') ; ?>
		</a>

		<a class="ingot-click-test-only {{#is _current_tab value="#ingot-panel-click_test_parts"}}nav-tab-active {{/is}}ingot-nav-tab nav-tab" href="#ingot-panel-click_test_parts">
			<?php _e('Click Test Components', 'ingot') ; ?>
		</a>

	</h2>
</div>

<form class="wordpress-main-form has-sub-nav" id="ingot-main-form" action="?page=ingot" method="POST">
	<?php wp_nonce_field( 'ingot', 'ingot-setup' ); ?>
	<input type="hidden" value="{{id}}" name="id" id="ingot-id">

	<input type="hidden" value="{{_current_tab}}" name="_current_tab" id="ingot-active-tab">

		<div id="ingot-panel-general" class="ingot-editor-panel" {{#if _current_tab}}{{#is _current_tab value="#ingot-panel-general"}}{{else}} style="display:none;" {{/is}}{{/if}}>
		<h4>
			<?php _e( 'General Settings', 'ingot' ); ?>
			<small class="description">
				<?php _e( 'General', 'ingot' ); ?>
			</small>
		</h4>
		<?php
			/**
			 * Include general settings template
			 */
			include INGOT_PATH . 'includes/templates/general-settings.php';
		?>
	</div>
	<div id="ingot-panel-click_test_parts" class="ingot-editor-panel" {{#is _current_tab value="#ingot-panel-click_test_parts"}}{{else}} style="display:none;" {{/is}}>		
		<h4>
			<?php _e('Add components to test', 'ingot') ; ?>
			<small class="description">
				<?php _e('Click Test Components', 'ingot') ; ?>
			</small>
		</h4>
		<?php
			/**
			 * Include the click_test_parts-panel
			 */
			include INGOT_PATH . 'includes/templates/click_test_parts-panel.php';
		?>
	</div>
	<div id="ingot-panel-test_config" class="ingot-editor-panel" {{#is _current_tab value="#ingot-panel-test_config"}}{{else}} style="display:none;" {{/is}}>		
		<h4>
			<?php _e('Configure your test', 'ingot') ; ?>
			<small class="description">
				<?php _e('Test Config', 'ingot') ; ?>
			</small>
		</h4>
		<?php
			/**
			 * Include the test_config-panel
			 */
			include INGOT_PATH . 'includes/templates/test_config-panel.php';
		?>
	</div>
	<div id="ingot-panel-ecommerce_test_config" class="ingot-editor-panel" {{#is _current_tab value="#ingot-panel-ecommerce_test_config"}}{{else}} style="display:none;" {{/is}}>		
		<h4>
			<?php _e('Configure your eCommerce Test', 'ingot') ; ?>
			<small class="description">
				<?php _e('eCommerce Test Config', 'ingot') ; ?>
			</small>
		</h4>
		<?php
			/**
			 * Include the ecommerce_test_config-panel
			 */
			include INGOT_PATH . 'includes/templates/ecommerce_test_config-panel.php';
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

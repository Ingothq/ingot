		<div class="ingot-config-group">
			<label for="ingot-name">
				<?php _e( 'Ingot Name', 'ingot' ); ?>
			</label>
			<input type="text" name="name" value="{{name}}" data-sync="#ingot-name-title" id="ingot-name" required>
		</div>
		<div class="ingot-config-group">
			<label for="ingot-slug">
				<?php _e( 'Ingot Slug', 'ingot' ); ?>
			</label>
			<input type="text" name="slug" value="{{slug}}" data-format="slug" data-sync=".ingot-subline" data-master="#ingot-name" id="ingot-slug" required>
		</div>

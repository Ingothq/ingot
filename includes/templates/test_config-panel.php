

<?php
	$types = array(
		'click' => __( 'Click Test', 'ingot' ),
		'price' => __( 'Price Test', 'ingot' ),
	);

?>
<div class="ingot-config-group">
	<label for="ingot-test_config-test_type">
		<?php _e( 'Test Type', 'ingot' ); ?>
	</label>

	<select style="width:395px;" placeholder="<?php _e( 'Select Type', 'ingot' ); ?>" name="test_config[test_type]"  id="ingot-test_config-test_type" required="required">
		<?php
		foreach($types as $id => $label ){
			if ( $id === $ingot[ 'test_config'][ 'test_type' ] ) {
				$selected = 'selected="selected"';
			}else{
				$selected = '';
			}

			echo '<option value="' . esc_attr( $id ) . '" ' . $selected . ' >' . esc_html( $label ) . '</option>';
		}
		?>
	</select>

	<p class="description" style="margin-left: 190px;">
		What kind of test is this?
	</p>
</div>



<?php
$types = array(
	'link' => array(
		'label' => __( 'Link', 'ingot' ),
		'desc' => __( 'A link, with testable text.', 'ingot' )
	),
	'button' => array(
		'label' => __( 'Button', 'ingot' ),
		'desc' => __( 'A clickable button, with testable text.', 'ingot' )
	),
	'text' => array(
		'label' => __( 'Text', 'ingot' ),
		'desc' => __( 'Testable text, with another element as the click test.', 'ingot' )
	),
);
?>

<div class="ingot-config-group ingot-click-test-only">
	<label for="ingot-test_config-click_type">
		<?php _e( 'Click Type', 'ingot' ); ?>
	</label>
	<select style="width:395px;" placeholder="<?php _e( 'Select Type', 'ingot' ); ?>" id="ingot-test_config-click_type" name="test_config[click_type]" required="required">
		<?php
		foreach($types as $id => $option ){
			if ( $id === $ingot[ 'test_config' ][ 'click_type' ] ) {
				$selected = 'selected="selected"';
			}else{
				$selected = '';
			}

			echo '<option value="' . esc_attr( $id ) . '" ' . $selected . ' >' . esc_html( $option[ 'label' ] ) . '</option>';
		}
		?>
	</select>
		<?php
		foreach($types as $id => $option ){
			if ( $id === $ingot[ 'test_config' ][ 'click_type' ] ) {
				$visibility = 'none';
				$aria = 'false';
			}else{
				$visibility = 'hidden';
				$aria = 'true';
			}

			printf( '<div id="%1s" class="ingot-test_config-click_type-desc" style="visibility:%2s;" aria-hidden="%3s" >%4s</div>', 'ingot-click-type-desc' . $id, $visibility, $aria, $option[ 'desc' ] );
		}
		?>

	<p class="description" style="margin-left: 190px;">
		<?php _e( 'What kind of click test is this?', 'ingot' ); ?>
	</p>
</div>

<div class="ingot-config-group">
		<label for="ingot-test_config-inital_tests">
			<?php _e( 'Initial Tests Per Sequence', 'ingot' ); ?>
		</label>
		<input id="ingot-test_config-inital_tests" type="text" class="regular-text" name="test_config[inital_tests]" value="{{test_config/inital_tests}}" required="required">
		<p class="description" style="margin-left: 190px;"> How many tests to run before weighting one option over another?</p>
	</div>

	<div class="ingot-config-group">
		<label for="ingot-test_config-threshold">
			<?php _e( 'Victory Threshold', 'ingot' ); ?>
		</label>
		<input id="ingot-test_config-threshold" type="text" class="regular-text" name="test_config[threshold]" value="{{test_config/threshold}}" required="required">
		<p class="description" style="margin-left: 190px;"> What percentage of tests must a option win to be considered the winner? </p>
	</div>


	<div class="ingot-config-group ingot-click-test-only" id="ingot-test_config-click_target-wrap">
		<label for="ingot-test_config-click_target">
			<?php _e( 'Click target', 'ingot' ); ?>
		</label>
		<input id="ingot-test_config-click_target" type="text" class="regular-text" name="test_config[click_target]" value="{{test_config/click_target}}" required="required">
		<p class="description" style="margin-left: 190px;"> What selector should we use to track clicks?</p>
	</div>

	<div class="ingot-config-group ingot-click-test-only">
		<label for="ingot-test_config-link">
			<?php _e( 'Link', 'ingot' ); ?>
		</label>
		<input id="ingot-test_config-link" type="text" class="regular-text" name="test_config[link]" value="{{test_config/link}}" required="required">
		<p class="description" style="margin-left: 190px;"> Link to use</p>
	</div>




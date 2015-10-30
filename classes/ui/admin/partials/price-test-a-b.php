<?php
/**
 * Individual A & B fields for a price test
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
if( ! isset( $id ) ){
	$id = 'new_' . rand();
}

if( ! isset( $index ) ){
	$index = 'default';
}
$a_id = $id . '-a';
$b_id = $id . '-b';

if( ! isset( $test ) ){
	$test = array(
		$index => array(
			'a' => 0.1,
			'b' => -0.1
		)
	);
}
?>

<span class="test-id" data-test-id="<?php echo esc_attr( $id ); ?>" aria-hidden="true"></span>
<div class="ingot-config-group">
	<label for="<?php echo esc_attr( $a_id ); ?>" class="a-label">
		<?php _e( 'Price Variation', 'ingot' ); ?>
	</label>
	<input id="<?php echo esc_attr( $a_id ); ?>" type="number" class="number-field test-a" value="<?php echo esc_attr( $test[ $index ][ 'a' ] ); ?>" min="-0.99" max="0.99" required />
</div><!--/a-->
<div class="ingot-config-group" style="visibility: hidden;display: ">
	<label for="<?php echo esc_attr( $b_id ); ?>" class="b-label">
		<?php _e( 'Price Variation For B', 'ingot' ); ?>
	</label>
	<input id="<?php echo esc_attr( $b_id ); ?>" class="number-field test-b" type="number" value="<?php echo esc_attr( $test[ $index ][ 'b' ] ); ?>" min="-0.99" max="0.99"  />
</div><!--/b-->

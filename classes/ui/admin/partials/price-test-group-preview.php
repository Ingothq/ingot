<?php
/**
 * Price Group Preview
 *
 * @package   @TODO
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
if( ! isset( $group ) ){
	return;
}

?>

<div class="ingot-config-group" id="<?php echo esc_attr( $id ); ?>" class="price-group-config">
	<h5>
		<?php echo $group[ 'group_name' ]; ?>
	</h5>
	<div class="button-pair">
			<span>
				<a href="<?php echo esc_url( $edit_link ); ?>" class="group-edit button button-secondary" data-group-id="<?php echo esc_attr( $id ); ?>" >
					<?php _e( 'Edit Group', 'ingot' ); ?>
				</a>
			</span>
			<span>
				<a href="<?php echo esc_url( $stats_link ); ?>" class="price-group-stats button button-secondary" data-group-id="<?php echo esc_attr( $id ); ?>"">
					<?php _e( 'Group Stats', 'ingot' ); ?>
				</a>
			</span>
			<span>
				<a href="#" class="price-group-delete group-delete button button-secondary" data-group-type="price" data-group-id="<?php echo esc_attr( $id ); ?>"">
					<?php _e( 'Delete Group', 'ingot' ); ?>
				</a>
			</span>
	</div>
</div><!--/#<?php echo esc_attr( $id ); ?>-->

<?php
/**
 * Partial for group meta
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
?>
<div class="group-meta" id="group-meta-<?php echo esc_attr( $id ); ?>">
	<div>
		<div>
			<?php printf( '%s: %s', __( 'Name', 'ingot' ), $name ); ?>
		</div>

		<div>
			<?php printf( '%s: %s', __( 'ID', 'ingot' ), $name ); ?>
		</div>
		<div>
			<?php printf( '%s: %s', __( 'Type', 'ingot' ), $type ); ?>
		</div>
	</div>

	<div>
		<span>
			<?php printf( '<a href="%s" class="button button-secondary">%s</a>', $link, __( 'Edit', 'ingot' ) ); ?>
		</span>
	</div>
</div>

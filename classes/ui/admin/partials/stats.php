<?php

/**
 * Admin page for displaying sequence stats
 *
 * @package   @ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
?>
<div id="test-group-results-admin" class="ingot-admin-wrap">
	<h1>
		<?php _e( 'Test Group Results', 'ingot' ); ?>
	</h1>
	<section id="sequences">
		<h3>
			<?php _e( 'Sequence Stats', 'ingot' ); ?>
		</h3>
		<?php echo $sequence_tables; ?>
	</section>
	<section id="group-meta">
		<h3>
			<?php _e( 'Group Details', 'ingot' ); ?>
		</h3>
		<?php echo $group_meta; ?>
	</section>
	<div class="clear"></div>
	<div class="navigation">
		<a href="<?php echo esc_attr( esc_url( $main_link )  ); ?>" class="button button-primary" class="close-stats">
			<?php _e( 'Close', 'ingot' ); ?>
		</a>
	</div>
</div>

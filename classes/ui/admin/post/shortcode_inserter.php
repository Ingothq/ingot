<?php
/**
 * @TODO What this does.
 *
 * @package   @TODO
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */

namespace ingot\ui\admin\post;


use ingot\testing\crud\group;
use ingot\ui\admin;

class shortcode_inserter {

	public static function button() {

		global $post;
		if ( ! empty( $post ) ) {
			printf( '
		<button type="button"
			id="ingot-shortcode-inserter-button"
		     data-modal="ingotShortcodeModal"
		     data-title="%s"
		     data-footer="#ingot-post-modal-footer"
		     data-content="#ingot-post-modal"
		     style="background-color: #72386A;color: #FFF;border: 1px solid #6c7761;border-color: #6c7761;padding: 4px 12px;border-radius: 4px; background-image:url( \'%s\');background-size:50px 20px;background-repeat: no-repeat;  font-weight: 800;font-variant: small-caps;"
		     onmouseover="this.style.backgroundColor=\'#72386A\'"
		     onmouseout="this.style.backgroundColor=\'#659B44\'"
		     >
		     %s
		</button>',
				esc_html__( 'Add An Ingot Test Group', 'ingot' ),
				esc_url_raw( trailingslashit( INGOT_URL ) .'assets/img/ingot-g.png'  ),
				esc_html__( 'Ingot', 'Ingot' )
			);
		}
	}

	public static function modal() {
		$screen = get_current_screen();

		if ( $screen->base === 'post' ) {
			$groups = group::get_items( [
				'type' => 'click'
			] );
			echo admin::get_partial( 'shortcode-modal.php', [ 'groups' => $groups ] );

		}

	}

}

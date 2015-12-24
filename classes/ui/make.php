<?php
/**
 *Load admin screens
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */


namespace ingot\ui;



use ingot\ui\admin\app\load;


class make {

	/**
	 * Make UI go
	 *
	 * JOSH: This class isn't being used, but let's keep it around for a bit, mkay?
	 *
	 * @since 0.0.6
	 */
	public function __construct() {
		if ( is_admin() ) {
			new load();
		}

	}

}

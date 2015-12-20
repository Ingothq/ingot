<?php
/**
 * Bandit for content tests
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\bandit;




class content extends bandit {


	/**
	 * Create persistor object
	 *
	 * @since 0.4.0
	 *
	 * @access protected
	 *
	 * @return \ingot\testing\bandit\persistor
	 */
	 protected function create_persistor(){

		 $persistor = new persistor(
			 $this->get_ID(),
			 [ '\ingot\testing\crud\group', 'get_levers' ],
			 [ '\ingot\testing\crud\group', 'save_levers' ]
		 );
		 return $persistor;
	}

}

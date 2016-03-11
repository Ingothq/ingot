<?php
/**
 * Load up the Ingot
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */
class ingot_bootstrap {

	/**
	 * Loads ingot if not already loaded.
	 *
	 * @since 0.0.5
	 */
	public static function maybe_load() {
		if ( did_action( 'ingot_loaded' ) ) {
			return;
		}

		if ( ! defined( 'INGOT_DEV_MODE' ) ){
			/**
			 * Puts Ingot into dev mode
			 *
			 * Don't use on a live site -- makes API totally open
			 *
			 * @since 0.0.5
			 */
			define( 'INGOT_DEV_MODE', false );
		}

		$load = true;
		if ( ! version_compare( PHP_VERSION, '5.5.0', '>=' ) ) {
			$load = false;
		}

		$autoloader = dirname( __FILE__ ) . '/vendor/autoload.php';
		if ( ! file_exists( $autoloader ) ) {
			$load = false;
		}

		if ( $load ) {

			include_once( $autoloader );
			$tables_existed = self::maybe_add_tables();
			self::maybe_upgrade();
			self::maybe_load_trial();
			ingot_init_plan();

			if( $tables_existed || \ingot\testing\db\delta::check_if_tables_exist() ) {
				ingot\testing\ingot::instance();

				//make admin go in admin
				if ( is_admin() ) {
					new ingot\ui\make();
				}


				if ( ingot_is_front_end() || ingot_is_admin_ajax() ) {
					//setup destination tests
					$destination_tests = \ingot\testing\tests\click\destination\init::set_tracking();
					//run cookies
					add_action( 'ingot_loaded', function () {
						/**
						 * Disable running cookies
						 *
						 * @since 1.1.0
						 *
						 * @param bool $run
						 */
						if ( true == (bool) apply_filters( 'ingot_run_cookies', true ) && ! did_action( 'ingot_cookies_set' ) ) {
							if ( ! empty( $destination_tests ) ) {
								\ingot\testing\tests\click\destination\init::setup_cookies( $destination_tests );
							}

							\ingot\testing\cookies\set::run();
						}

					});
				}

				/**
				 * Runs when Ingot has loaded.
				 *
				 * @since 0.0.5
				 */
				do_action( 'ingot_loaded' );

			}else{
				if ( is_admin() ) {
					printf( '<div class="error"><p>%s</p></div>', __( 'Ingot Not Loaded', 'ingot' ) );

				}

				/**
				 * Runs if Ingot failed to load
				 *
				 * @since 0.3.0
				 *
				 */
				do_action( 'ingot_loaded_failed' );

				return;

			}

		}

	}

	/**
	 * Add tables to DB if needed
	 *
	 * @since 1.1.0
	 *
	 * @access protected
	 *
	 * @return bool
	 */
	protected static function maybe_add_tables(){
		if( false == \ingot\testing\db\delta::check_if_tables_exist() ){
			\ingot\testing\db\delta::add_tables();
			return false;
		}

		return true;

	}

	/**
	 * Maybe run DB updater
	 *
	 * @access protected
	 *
	 * @since 1.0.1
	 */
	protected static function maybe_upgrade(){
		if( INGOT_VER != get_option( 'ingot_version', 0 ) ) {
			$updater = new \ingot\testing\db\upgrade( INGOT_VER );
			$updater->run();
			update_option( 'ingot_version', INGOT_VER, false );
		}


	}



	/**
	 * If trial mode is a thing, load the trial system
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	public static function maybe_load_trial(){
		return false;

	}


}

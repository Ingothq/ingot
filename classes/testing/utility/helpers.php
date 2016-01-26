<?php
/**
 * Utility functions for Ingot
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\testing\utility;


class helpers {

	/**
	 * Get key/property from array or object with fallback.
	 *
	 * This method wishes it was pods_v()
	 *
	 * @since 0.0.3
	 *
	 * @param string $key Key/index/property to search for.
	 * @param array|object $thing Object or array to search for index/key/property in.
	 * @param null|mixed $default Default if not set.
	 * @param bool|array|string Optional. If true, santize method of this class is used to sanatized input. If callaballe output is passed through that, else no extra sanitization.
	 *
	 *
	 * @return mixed
	 */
	public static function v( $key, $thing, $default = null, $sanitize = false ) {
		$return = $default;
		if ( ! empty( $thing ) ) {
			if ( is_array( $thing ) ) {
				if ( isset( $thing[ $key ] ) ) {
					$return = $thing[ $key ];

				}
			}

			if ( is_object( $thing ) ) {
				if ( isset( $thing->$key ) ) {
					$return = $thing->$key;


				}

			}

		}

		if( true == $sanitize ){
			return self::sanitize( $return );
		}elseif( is_callable( $sanitize ) ) {
			if ( ! empty( $return ) ) {
				call_user_func( $sanitize, $return );
			}
		}else{
			return $return;

		}

	}

	/**
	 * Get key/property from array or object with fallback and recursive sanitzation.
	 *
	 * This method wishes it was pods_v_sanitized()
	 *
	 * @since 0.0.3
	 *
	 * @param string $key Key/index/property to search for.
	 * @param array|object $thing Object or array to search for index/key/property in.
	 * @param null|mixed $default Default if not set.
	 *
	 * @return mixed
	 */
	public static function v_sanitized( $input, $thing, $default ){
		return self::v( $input, $thing, $default, true );

	}

	/**
	 * Filter input and return sanitized output
	 *
	 * Pretty much copypasta of pods_sanitize()
	 *
	 * @since 0.0.9
	 *
	 * @param mixed $input The string, array, or object to sanitize
	 * @param array $params Optional Additional options
	 *
	 * @return array|mixed|object|string|void
	 */
	public static function sanitize( $input, $params = array() ) {
		if ( '' === $input || is_int( $input ) || is_float( $input ) || empty( $input ) ) {
			return $input;
		}

		$output = array();

		$defaults = array(
			'nested' => false,
			'type' => null // %s %d %f etc
		);

		if ( !is_array( $params ) ) {
			$defaults[ 'type' ] = $params;

			$params = $defaults;
		}
		else {
			$params = array_merge( $defaults, (array) $params );
		}

		if ( is_object( $input ) ) {
			$input = get_object_vars( $input );

			$n_params = $params;
			$n_params[ 'nested' ] = true;

			foreach ( $input as $key => $val ) {
				$output[ self::sanitize( $key ) ] = self::sanitize( $val, $n_params );
			}

			$output = (object) $output;
		}
		elseif ( is_array( $input ) ) {
			$n_params = $params;
			$n_params[ 'nested' ] = true;

			foreach ( $input as $key => $val ) {
				$output[ self::sanitize( $key ) ] = self::sanitize( $val, $n_params );
			}
		}
		elseif ( !empty( $params[ 'type' ] ) && false !== strpos( $params[ 'type' ], '%' ) ) {
			/**
			 * @var $wpdb wpdb
			 */
			global $wpdb;

			$output = $wpdb->prepare( $params[ 'type' ], $output );
		}
		elseif ( function_exists( 'wp_slash' ) ) {
			$output = wp_slash( $input );
		}
		else {
			$output = esc_sql( $input );
		}

		return $output;

	}

	/**
	 * Get default button color from meta
	 *
	 * @since 0.1.0
	 *
	 * @param array $config
	 * @param bool|true $with_hash
	 *
	 * @return string
	 */
	public static function get_color_from_meta( $config, $with_hash = true ){
		if( is_wp_error( $config ) ){
			return false;
		}
		$color = defaults::text_color();
		if( isset( $config[ 'meta' ] ) && ( isset( $config[ 'meta' ][ 'color'] ) || isset(  $config[ 'meta' ][ 'button_color' ] ) ) ){
			if ( isset( $config[ 'meta' ][ 'color'] ) ) {
				$color = $config['meta']['color'];
			} elseif( isset(  $config[ 'meta' ][ 'button_color' ] ) ) {
				$color =  $config[ 'meta' ][ 'button_color' ];
			}else{
				$color = 'ffffff';
			}
		}

		return self::prepare_color( $color, $with_hash );

	}

	/**
	 * Get default button color from meta
	 *
	 * @since 0.1.0
	 *
	 * @param array $config
	 * @param bool|true $with_hash
	 *
	 * @return string
	 */
	public static function get_background_color_from_meta( $config, $with_hash = true ){
		$color = '';
		if( isset( $config[ 'meta' ] ) && ( isset( $config[ 'meta' ][ 'color'] ) || isset(  $config[ 'meta' ][ 'button_color' ] ) ) ){
			if ( isset( $config[ 'meta' ][ 'background_color'] ) ) {
				$color = $config['meta'][ 'background_color'];
			}else{
				$color = '';
			}
		}

		return self::prepare_color( $color, $with_hash );

	}

	/**
	 * Get link from click group config
	 *
	 * @since 0.4.0
	 *
	 * @param array $config Group config
	 * @param bool $validate Optional. By default, link is validated. Set to false to skip.
	 *
	 * @return string
	 */
	public static function get_link_from_meta( $config, $validate = true ) {
		$link = '';
		if( isset( $config[ 'meta' ], $config[ 'meta' ][ 'link' ] ) ){
			$link = $config[ 'meta' ][ 'link' ];
			if( $validate ){
				if( ! filter_var( $link, FILTER_VALIDATE_URL ) ) {
					$link = '';
				}

			}

		}

		return $link;

	}

	/**
	 * Prepare a color hex
	 *
	 * @since 0.1.1
	 *
	 * @param string $color Color hex
	 * @param bool|true $with_hash return with or without hex
	 *
	 * @return string color, or if invalid, default color.
	 */
	public static function prepare_color( $color, $with_hash = true ) {
		if( self::is_color_word( $color ) ){
			$color = self::color_word_to_hex( $color );
		}

		if( $with_hash ){
			$color = self::maybe_hash_hex_color( $color );
		}

		if( $with_hash ){
			$color =  self::sanitize_hex_color( $color );
		}else{
			$color =  self::sanitize_hex_color_no_hash( $color );
		}

		if( empty( $color ) ) {
			$color = defaults::color();
			if( $with_hash ){
				$color = self::maybe_hash_hex_color( $color );
			}

		}

		return $color;

	}

	/**
	 * Sanitizes a hex color.
	 *
	 * Returns either '', a 3 or 6 digit hex color (with #), or nothing.
	 * For sanitizing values without a #, see sanitize_hex_color_no_hash().
	 *
	 * @since 0.1.1
	 *
	 * @param string $color
	 * @return string|void
	 */
	protected static  function sanitize_hex_color( $color ) {
		if ( '' === $color ) {
			return '';

		}

		// 3 or 6 hex digits, or the empty string.
		if ( preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
			return $color;

		}

	}

	/**
	 * Sanitizes a hex color without a hash. Use sanitize_hex_color() when possible.
	 *
	 * Saving hex colors without a hash puts the burden of adding the hash on the
	 * UI, which makes it difficult to use or upgrade to other color types such as
	 * rgba, hsl, rgb, and html color names.
	 *
	 * Returns either '', a 3 or 6 digit hex color (without a #), or null.
	 *
	 * @since 0.1.1
	 *
	 * @param string $color
	 * @return string|null
	 */
	protected static  function sanitize_hex_color_no_hash( $color ) {
		$color = ltrim( $color, '#' );

		if ( '' === $color ) {
			return '';

		}

		return self::sanitize_hex_color( '#' . $color ) ? $color : null;

	}

	/**
	 * Ensures that any hex color is properly hashed.
	 * Otherwise, returns value untouched.
	 *
	 * This method should only be necessary if using sanitize_hex_color_no_hash().
	 *
	 * @since 0.1.1
	 *
	 * @param string $color
	 * @return string
	 */
	protected static  function maybe_hash_hex_color( $color ) {
		if ( $unhashed = self::sanitize_hex_color_no_hash( $color ) ) {
			return '#' . $unhashed;
		}

		return $color;

	}

	/**
	 * Colors as RGBs
	 *
	 * Muchos Gracias:http://stackoverflow.com/a/5925612/1469799
	 *
	 * @since 0.1.1
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected static $colors = array(
		'aliceblue'            => 'F0F8FF',
		'antiquewhite'         => 'FAEBD7',
		'aqua'                 => '00FFFF',
		'aquamarine'           => '7FFFD4',
		'azure'                => 'F0FFFF',
		'beige'                => 'F5F5DC',
		'bisque'               => 'FFE4C4',
		'black'                => '000000',
		'blanchedalmond '      => 'FFEBCD',
		'blue'                 => '0000FF',
		'blueviolet'           => '8A2BE2',
		'brown'                => 'A52A2A',
		'burlywood'            => 'DEB887',
		'cadetblue'            => '5F9EA0',
		'chartreuse'           => '7FFF00',
		'chocolate'            => 'D2691E',
		'coral'                => 'FF7F50',
		'cornflowerblue'       => '6495ED',
		'cornsilk'             => 'FFF8DC',
		'crimson'              => 'DC143C',
		'cyan'                 => '00FFFF',
		'darkblue'             => '00008B',
		'darkcyan'             => '008B8B',
		'darkgoldenrod'        => 'B8860B',
		'darkgray'             => 'A9A9A9',
		'darkgreen'            => '006400',
		'darkgrey'             => 'A9A9A9',
		'darkkhaki'            => 'BDB76B',
		'darkmagenta'          => '8B008B',
		'darkolivegreen'       => '556B2F',
		'darkorange'           => 'FF8C00',
		'darkorchid'           => '9932CC',
		'darkred'              => '8B0000',
		'darksalmon'           => 'E9967A',
		'darkseagreen'         => '8FBC8F',
		'darkslateblue'        => '483D8B',
		'darkslategray'        => '2F4F4F',
		'darkslategrey'        => '2F4F4F',
		'darkturquoise'        => '00CED1',
		'darkviolet'           => '9400D3',
		'deeppink'             => 'FF1493',
		'deepskyblue'          => '00BFFF',
		'dimgray'              => '696969',
		'dimgrey'              => '696969',
		'dodgerblue'           => '1E90FF',
		'firebrick'            => 'B22222',
		'floralwhite'          => 'FFFAF0',
		'forestgreen'          => '228B22',
		'fuchsia'              => 'FF00FF',
		'gainsboro'            => 'DCDCDC',
		'ghostwhite'           => 'F8F8FF',
		'gold'                 => 'FFD700',
		'goldenrod'            => 'DAA520',
		'gray'                 => '808080',
		'green'                => '008000',
		'greenyellow'          => 'ADFF2F',
		'grey'                 => '808080',
		'honeydew'             => 'F0FFF0',
		'hotpink'              => 'FF69B4',
		'indianred'            => 'CD5C5C',
		'indigo'               => '4B0082',
		'ivory'                => 'FFFFF0',
		'khaki'                => 'F0E68C',
		'lavender'             => 'E6E6FA',
		'lavenderblush'        => 'FFF0F5',
		'lawngreen'            => '7CFC00',
		'lemonchiffon'         => 'FFFACD',
		'lightblue'            => 'ADD8E6',
		'lightcoral'           => 'F08080',
		'lightcyan'            => 'E0FFFF',
		'lightgoldenrodyellow' => 'FAFAD2',
		'lightgray'            => 'D3D3D3',
		'lightgreen'           => '90EE90',
		'lightgrey'            => 'D3D3D3',
		'lightpink'            => 'FFB6C1',
		'lightsalmon'          => 'FFA07A',
		'lightseagreen'        => '20B2AA',
		'lightskyblue'         => '87CEFA',
		'lightslategray'       => '778899',
		'lightslategrey'       => '778899',
		'lightsteelblue'       => 'B0C4DE',
		'lightyellow'          => 'FFFFE0',
		'lime'                 => '00FF00',
		'limegreen'            => '32CD32',
		'linen'                => 'FAF0E6',
		'magenta'              => 'FF00FF',
		'maroon'               => '800000',
		'mediumaquamarine'     => '66CDAA',
		'mediumblue'           => '0000CD',
		'mediumorchid'         => 'BA55D3',
		'mediumpurple'         => '9370D0',
		'mediumseagreen'       => '3CB371',
		'mediumslateblue'      => '7B68EE',
		'mediumspringgreen'    => '00FA9A',
		'mediumturquoise'      => '48D1CC',
		'mediumvioletred'      => 'C71585',
		'midnightblue'         => '191970',
		'mintcream'            => 'F5FFFA',
		'mistyrose'            => 'FFE4E1',
		'moccasin'             => 'FFE4B5',
		'navajowhite'          => 'FFDEAD',
		'navy'                 => '000080',
		'oldlace'              => 'FDF5E6',
		'olive'                => '808000',
		'olivedrab'            => '6B8E23',
		'orange'               => 'FFA500',
		'orangered'            => 'FF4500',
		'orchid'               => 'DA70D6',
		'palegoldenrod'        => 'EEE8AA',
		'palegreen'            => '98FB98',
		'paleturquoise'        => 'AFEEEE',
		'palevioletred'        => 'DB7093',
		'papayawhip'           => 'FFEFD5',
		'peachpuff'            => 'FFDAB9',
		'peru'                 => 'CD853F',
		'pink'                 => 'FFC0CB',
		'plum'                 => 'DDA0DD',
		'powderblue'           => 'B0E0E6',
		'purple'               => '800080',
		'red'                  => 'FF0000',
		'rosybrown'            => 'BC8F8F',
		'royalblue'            => '4169E1',
		'saddlebrown'          => '8B4513',
		'salmon'               => 'FA8072',
		'sandybrown'           => 'F4A460',
		'seagreen'             => '2E8B57',
		'seashell'             => 'FFF5EE',
		'sienna'               => 'A0522D',
		'silver'               => 'C0C0C0',
		'skyblue'              => '87CEEB',
		'slateblue'            => '6A5ACD',
		'slategray'            => '708090',
		'slategrey'            => '708090',
		'snow'                 => 'FFFAFA',
		'springgreen'          => '00FF7F',
		'steelblue'            => '4682B4',
		'tan'                  => 'D2B48C',
		'teal'                 => '008080',
		'thistle'              => 'D8BFD8',
		'tomato'               => 'FF6347',
		'turquoise'            => '40E0D0',
		'violet'               => 'EE82EE',
		'wheat'                => 'F5DEB3',
		'white'                => 'FFFFFF',
		'whitesmoke'           => 'F5F5F5',
		'yellow'               => 'FFFF00',
		'yellowgreen'          => '9ACD32'
	);

	/**
	 * Check if a word can be converted to a color
	 *
	 * @since 0.1.1
	 *
	 * @access protected
	 *
	 * @param $word
	 *
	 * @return bool
	 */
	protected static function is_color_word( $word ) {
		return  isset( self::$colors[ strtolower( $word ) ]  );
	}

	/**
	 * Convert a word to a color, if possible.
	 *
	 * @since 0.1.1
	 *
	 * @access protected
	 *
	 * @param string $word
	 *
	 * @return string Returns hex or possible. Else empty string.
	 */
	protected static function color_word_to_hex( $word ){

		if( self::is_color_word( $word ) ){
			return self::$colors[ strtolower( $word ) ];
		}else{
			return '';
		}
	}

	/**
	 * Utility function to make all keys of an array integers (recursively)
	 *
	 * @since 0.4.0
	 *
	 * @param $array
	 *
	 * @return array
	 */
	public static function make_array_values_numeric( $array, $make_numeric_strings = false ) {
		if ( ! empty( $array ) ) {
			foreach( $array as $k => $v ) {
				if ( ! is_array( $v ) ) {
					if ( ! is_numeric( $v ) ) {
						$v = 0;
					} else {
						$v = (int) $v;
					}
					if( $make_numeric_strings ) {
						$array[ $k ] = (string) $v;
					}else {
						$array[ $k ] = (int) $v;
					}
				}else{
					$array[ $k ] = self::make_array_values_numeric( $v );
				}

			}

		}

		if ( empty( $array ) ) {
			$array = array();
		}

		return $array;
	}

}

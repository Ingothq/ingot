<?php
/**
 * Wrapper for all translation strings
 *
 * @package   ingot
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2015 Josh Pollock
 */

namespace ingot\ui\admin\app;


use ingot\ui\admin;

class translations {

	/**
	 * Get all translation strings
	 *
	 * In admin app, this array is available as $rootScope.translate
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	static public function strings() {
		$translation_strings = [
			'group_saved'              => esc_html__( 'Group Saved', 'ingot' ),
			'fail'                     => esc_html__( 'Could Not Save', 'ingot' ),
			'sorry'                    => esc_html__( 'Please try again and/or contact support', 'ingot' ),
			'close'                    => esc_html__( 'Close', 'ingot' ),
			'saved'                    => esc_html__( 'Saved Group: ', 'ingot' ),
			'cant_remove'              => esc_html__( 'At this time, you can not remove a test from a group.', 'ingot' ),
			'beta_error_header'        => esc_html__( 'Beta Limitation Encountered', 'ingot' ),
			'beta_message'             => esc_html__( 'Sorry about that but Ingot is still in beta. We will be adding this feature soon.', 'ingot' ),
			'no_stats'                 => esc_html__( 'We do not have a functional stats viewer yet.', 'ingot' ),
			'deleted'                  => esc_html__( 'Test Group Deleted', 'ingot' ),
			'are_you_sure'             => esc_html__( 'Are You Sure About That?', 'ingot' ),
			'delete_confirm'           => esc_html__( 'Deleting all groups is not reversible or undoable.', 'ingot' ),
			'delete'                   => esc_html__( 'Delete', 'ingot' ),
			'cancel'                   => esc_html__( 'Cancel', 'ignot' ),
			'canceled'                 => esc_html__( 'Canceled', 'ingot' ),
			'spinner_alt'              => esc_html__( 'Loading Spinner', 'ingot' ),
			'no_tests'                 => esc_html__( 'This group has no tests', 'ingot' ),
			'invalid_price_test_range' => esc_html__( 'Please enter a number between -.99 and .99', 'ingot' ),
			'settings_saved'           => esc_html__( 'Settings Saved', 'ingot' ),
			'price_coming_soon'              => esc_html__( 'Ingot Price Testing Is Coming Soon.', 'ingot' ),
			'stats'                    => [
				'stats_for' => esc_html__( 'Stats For Group', 'ingot' ),
				'no_stats'  => esc_html__( 'No Stats for this group yet', 'ingot' ),
				'c_rate'    => esc_html__( 'Conversion Rate', 'ingot' ),
				'variant'   => esc_html__( 'Variant', 'ingot' ),
				'g_avg_c_rate' => esc_html__( 'Group Average Conversion Rate', 'ingot' ),
				'group_header' => esc_html__( 'Group Totals', 'ingot' ),
				'group_total' => esc_html__( 'Iterations', 'ingot' ),
				'group_conversions' => esc_html__( 'Conversions', 'ingot' ),
				'g_c_rate' => esc_html__( 'Conversion Rate', 'ingot')

			],
			'groups'                   => [
				'click_group_page_title' => esc_html__( 'Content Test Groups', 'ingot' ),
				'price_group_page_title' => esc_html__( 'Price Test Groups', 'ingot' ),
				'show_all'               => esc_html__( 'Show All', 'ingot' ),
				'create_new'             => esc_html__( 'Create New', 'ingot' ),
				'edit'                   => esc_html__( 'Edit Group', 'ingot' ),
				'stats'                  => esc_html__( 'Group Stats', 'ingot' ),
				'delete'                 => esc_html__( 'Delete Group', 'ingot' ),
				'no_groups'              => esc_html__( 'There are no groups, create one.', 'ingot' ),
				'click_copy'             => esc_html__( 'Click To Copy', 'ingot' ),
				'page'                   => esc_html__( 'Page', 'ingot' ),
				'inactive'                 => esc_html__( 'Plugin Not Active', 'ingot' ),
			],
			'group'                    => [
				'edit_group_tests'                     => esc_html__( 'Edit Group Tests', 'ingot' ),
				'save_group'                           => esc_html__( 'Save Group', 'ingot' ),
				'type'                                 => esc_html__( 'Type', 'ingot' ),
				'name'                                 => esc_html__( 'Name', 'ingot' ),
				'group_settings_header'                => esc_html__( 'Group Settings', 'ingot' ),
				'link_label_group_setting'             => esc_html__( 'Link', 'ingot' ),
				'text_label_group_setting'             => esc_html__( 'Text (Used For All Buttons)', 'ingot' ),
				'color_label_group_setting'            => esc_html__( 'Color (Used For All Buttons)', 'ingot' ),
				'background_color_label_group_setting' => esc_html__( 'Background (Used For All Buttons)', 'ingot' ),
				'tests_header'                         => esc_html__( 'Variations', 'ingot' ),
				'tests_header_price'                   => esc_html__( 'Price Variations', 'ingot' ),
				'text_label_test_setting'              => esc_html__( 'Text', 'ingot' ),
				'color_label_test_setting'             => esc_html__( 'Button Text Color', 'ingot' ),
				'background_color_label_test_setting'  => esc_html__( 'Button Background Color', 'ingot' ),
				'add_test'                             => esc_html__( 'Add Variant', 'ingot' ),
				'plugin'                               => esc_html__( 'eCommerce Plugin', 'ingot' ),
				'product'                              => esc_html__( 'Product', 'ingot' ),
				'price_variation'                      => esc_html__( 'Price Variation (percentage)', 'ingot ' ),
				'delete'                               => esc_html__( 'Delete', 'ingot' ),
				'content_test'                         => esc_html__( 'Content Test', 'ingot' ),
				'data_and_tests'                       => esc_html__( 'Data and Tests', 'ingot' ),
				'group_name'                           => esc_html__( 'Group Name', 'ingot' ),
				'group_type'                           => esc_html__( 'Group Type', 'ingot' ),
				'group_data_and_tests'                 => esc_html__( 'Group Data and Tests', 'ingot' ),
				'test_name_instructions'               => esc_html__( 'Name of your new test, be specific for easy reference later.', 'ingot' ),
				'test_name'                            => esc_html__( 'Test Name', 'ingot' ),
				'edit_group_type'                      => esc_html__( 'Edit Group Type', 'ingot' ),
				'type_instructions'                    => esc_html__( 'Type of test you want to perform', 'ingot' ),
				'sample_button'                        => esc_html__( 'Sample Button', 'ingot' ),
				'new_content_test'                     => esc_html__( 'New Content Test', 'ingot' ),
				'result_price'                         => esc_html__( 'Resulting Price', 'ingot' ),
				'add_price_test'                       => esc_html__( 'Add Price Variation', 'ingot' ),
				'destination_header'                  => esc_html__( 'Destination Type', 'ingot' ),
				'is_tagline' => esc_html__( 'Change site tagline?', 'ingot'),
				'destination_page' => esc_html__( 'Page ID for conversions.', 'ingot' ),
				'destination_page_results' => esc_html__( 'Select a page:', 'ingot' ),
				'destination_page_desc' => esc_html__( 'Enter the ID of a page or search by title. When a visitor reaches this page, a conversion will be recorded.', 'ingot' ),
				'destination_hook' => esc_html__( 'Action hook to track conversions with.', 'ingot' ),
				'must_name' => esc_html__( 'Please name this group', 'ingot' ),
				'must_type' => esc_html__( 'Please choose a group type', 'ingot')
			],
			'settings'                 => [
				'page_header'          => esc_html__( 'Settings', 'ingot' ),
				'cache_mode_label'     => esc_html__( 'Work around caching', 'ingot' ),
				'cache_mode_desc'      => esc_html__( 'If you are using a static HTML cache testing will not work properly, since the same version of your site is shown to all visitors. Use this mode to work around this issue.', 'ingot' ),
				'click_tracking_label' => esc_html__( 'Advanced Click Tracking', 'ingot' ),
				'click_tracking_desc'  => esc_html__( 'Ingot always tracks clicks, in advanced mode, more details are tracked. This takes up more space in the database, but enables Ingot to be more powerful.', 'ingot' ),
				'anon_tracking_label'  => esc_html__( 'Share Your Data Anonymously', 'ingot' ),
				'anon_tracking_desc'   => esc_html__( 'When enabled, your test data is shared with Ingot to help us improve the service.', 'ingot' ),
				'license_code_label'   => esc_html__( 'License Code', 'ingot' ),
				'license_code_desc'    => esc_html__( 'Enter your license code to enable support and updates.', 'ingot' ),
				'save'                 => esc_html__( 'Save Settings', 'ingot' ),
				'edit_name'            => esc_html__( 'Edit Group Name', 'ingot' ),
				'license_notice'       => esc_html__( 'License not active or not valid', 'ingot' ),
				'license_active'      => esc_html__( ' License active', 'ingot' ),

			],
			'welcome'                  => [
				'banner'       => esc_url( INGOT_URL . 'assets/img/ingot-logo.png' ),
				'banner_alt'   => esc_html__( 'Ingot Banner Logo', 'ingot' ),
				'header'       => esc_html__( 'Ingot: Do Less, Convert More', 'ingot' ),
				'links_header' => esc_html__( 'Helpful Links', 'ingot' ),
				'video_header' => esc_html__( 'Watch This Short Video To Learn How To Use Ingot', 'ingot' ),
				'price_tests'  => esc_html__( 'Price Tests', 'ingot' ),
				'click_tests'  => esc_html__( 'Click Tests', 'ingot' ),
				'learn_more'   => esc_html__( 'Learn more about Ingot', 'ingot' ),
				'docs'         => esc_html__( 'Documentation', 'ingot' ),
				'support'      => esc_html__( 'Support', 'ingot' )
			],
			'support'                  => [
				'for_support' => esc_html__( 'For support please use:', 'ingot' )
	],
			'descriptions' => [
				'click'         => admin::get_partial( 'click-description.php' ),
				'price_testing' => esc_html__( 'Raise or lower a product\'s price to maximize revenue', 'ingot' )
			]
		];

		/**
		 * Filter translation strings used in the admin app
		 *
		 * @since 1.1.0
		 *
		 * @param array $translation_strings The array of strings
		 */
		return apply_filters( 'ingot_ui_translation_strings', $translation_strings );
	}



}

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


class translations {

	/**
	 * Get all translation strings
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	static public function strings() {
		return [
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
			'stats'                    => array(
				'no_stats' => esc_html__( 'No Stats for this group yet', 'ingot' ),
			),
			'groups'                   => array(
				'click_group_page_title' => esc_html__( 'Content Test Groups', 'ingot' ),
				'price_group_page_title' => esc_html__( 'Price Test Groups', 'ingot' ),
				'show_all'               => esc_html__( 'Show All', 'ingot' ),
				'create_new'             => esc_html__( 'Create New', 'ingot' ),
				'edit'                   => esc_html__( 'Edit Group', 'ingot' ),
				'stats'                  => esc_html__( 'Group Stats', 'ingot' ),
				'delete'                 => esc_html__( 'Delete Group', 'ingot' ),
			),
			'group'                    => array(
				'save_group'                           => esc_html__( 'Save Group', 'ingot' ),
				'type'                                 => esc_html__( 'Type', 'ingot' ),
				'name'                                 => esc_html__( 'Name', 'ingot' ),
				'group_settings_header'                => esc_html__( 'Group Settings', 'ingot' ),
				'link_label_group_setting'             => esc_html__( 'Link', 'ingot' ),
				'text_label_group_setting'             => esc_html__( 'Text (Used For All Buttons)', 'ingot' ),
				'color_label_group_setting'            => esc_html__( 'Color (Used For All Buttons)', 'ingot' ),
				'background_color_label_group_setting' => esc_html__( 'Background (Used For All Buttons)', 'ingot' ),
				'tests_header'                         => esc_html__( 'Tests', 'ingot' ),
				'text_label_test_setting'              => esc_html__( 'Text', 'ingot' ),
				'color_label_test_setting'             => esc_html__( 'Button Text Color', 'ingot' ),
				'background_color_label_test_setting'  => esc_html__( 'Button Background Color', 'ingot' ),
				'add_test'                             => esc_html__( 'Add Test', 'ingot' ),
				'plugin'                               => esc_html__( 'eCommerce Plugin', 'ingot' ),
				'product'                              => esc_html__( 'Product', 'ingot' ),
				'price_variation'                      => esc_html__( 'Price Variation (percentage)', 'ingot ' ),
				'delete'                               => esc_html__( 'Delete', 'ingot' )
			),
			'settings'                 => array(
				'page_header'          => esc_html__( 'Settings', 'ingot' ),
				'cache_mode_label'     => esc_html__( 'Work around caching', 'ingot' ),
				'cache_mode_desc'      => esc_html__( 'If you are using a static HTML cache testing will not work properly, since the same version of your site is shown to all visitors. Use this mode to work around this issue.', 'ingot' ),
				'click_tracking_label' => esc_html__( 'Advanced Click Tracking', 'ingot' ),
				'click_tracking_desc'  => esc_html__( 'Ingot always tracks clicks, in advanced mode, more details are tracked. This takes up more space in the database, but enables Ingot to be more powerful.', 'ingot' ),
				'anon_tracking_label'  => esc_html__( 'Share Your Data Anonymously', 'ingot' ),
				'anon_tracking_desc'   => esc_html__( 'When enabled, your test data is shared with Ingot to help us improve the service.', 'ingot' ),
				'license_code_label'   => esc_html__( 'License Code', 'ingot' ),
				'license_code_desc'    => esc_html__( 'Enter your license code to enable support and updates.', 'ingot' ),
				'save'                 => esc_html__( 'Save Settings', 'ingot' )

			),
			'welcome'                  => array(
				'banner'       => esc_url( INGOT_URL . 'assets/img/Ingot-logo-dark.png' ),
				'banner_alt'   => esc_html__( 'Ingot Banner Logo', 'ingot' ),
				'header'       => esc_html__( 'Ingot: Do Less, Convert More', 'ingot' ),
				'links_header' => esc_html__( 'Helpful Links', 'ingot' ),
				'video_header' => esc_html__( 'Watch This Short Video To Learn How To Use Ingot', 'ingot' ),
				'price_tests'  => esc_html__( 'Price Tests', 'ingot' ),
				'click_tests'  => esc_html__( 'Click Tests', 'ingot' ),
				'learn_more'   => esc_html__( 'Learn more about Ingot', 'ingot' ),
				'docs'         => esc_html__( 'Documentation', 'ingot' ),
				'support'      => esc_html__( 'Support', 'ingot' )
			)
		];
	}


}

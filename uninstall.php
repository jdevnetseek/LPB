<?php
/** 
 * Fired when the plugin is uninstalled
 *
 * Delete registered options.
 * 
 * @package 		Landing_Page_Booster
 * @since           1.0.1.0
 * @author 			Netseek Pty Ltd
 */
 
/* If uninstall not called from WordPress, then exit. */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) { exit; }

/**
 * Remove Data Results Section Heading Settings
 */

delete_option('_deactivate_checkbox_key');
delete_option('kr_license_key_activated');
delete_option('_keywordreplacer');
delete_option('_license_key');
delete_option('kr_license_key');
delete_option('kr_license_notices_1');
delete_option('kr_license_check_notices');
delete_option('_transient_lpb_license_key');
delete_option('_transient_lpb_license_key_status_3.2.2.5_lpb2.6bin.com');
delete_option('_lpb_license_info');

$args = array(
	'role' => 'administrator',
);
$users = get_users($args);
foreach ($users as $user) {
	$user->remove_role( 'kr_admin_role' );
}

if( !defined('KR_PlUGIN_PATH') ) {define('KR_PlUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) )); }
require_once KR_PlUGIN_PATH . '/app.php';
require_once KR_PlUGIN_PATH . '/model/lpb-license-manager-class.php';
require_once KR_PlUGIN_PATH . '/model/lpb-api.php';
delete_transient( "lpb_sl_key" );
delete_transient( "lpb_license_key" );
delete_transient( LPBLicenseManager::generate_transient_name() );
delete_transient( "lpb_license_info" );
delete_transient( lpb_transient_name() );
delete_transient( "lpb_sl_license_info" );

<?php

$plugin_data = get_plugin_data(KR_PlUGIN_PATH()."/app.php");
if( !defined('KR_SERVER_URL') ) {define('KR_SERVER_URL', 'https://lpb2.6bin.com'); }
if( !defined('KR_PLUGIN_VERSION') ) { define('KR_PLUGIN_VERSION', $plugin_data["Version"] ); }
if( !defined('KR_SECRET_KEY') ) {	define('KR_SECRET_KEY', '58f862dcac7da6.74677125'); }
if( !defined('KR_ITEM_REFERENCE') ) {define('KR_ITEM_REFERENCE', 'Landing Page Booster Plugin'); }
class LPBLicenseManager{
	public static $error_message = null;

	public function __construct() {
		if (is_null(self::$error_message)) {
			self::$error_message = '<p>An unexpected error occurred. Please contact the <a href="http://www.netseek.com.au/">plugin developer</a>.</p>';
		}
	}
	
	public static function generate_transient_name(){
		$url = self::remove_protocol( KR_SERVER_URL );
		return 'lpb_license_key_status_' . KR_PLUGIN_VERSION . '_' . $url;
	}
	
	private static function remove_protocol( $url ){
		return preg_replace('#^https?://#', '', $url );
	}
	
	public static function check_license_info( $license_key = '' ){
		$status = 'success';
		$output = '';

		$check_api_params = array(
			'slm_action' => 'slm_check',
			'secret_key' => KR_SECRET_KEY,
			'license_key' => $license_key,
			'registered_domain' => $_SERVER['SERVER_NAME'],
			'item_reference' => urlencode(KR_ITEM_REFERENCE),
		);
		$check_query = esc_url_raw( add_query_arg( $check_api_params, KR_SERVER_URL ) );
		
		$check_response = wp_remote_get( $check_query, array( 'timeout' => 20, 'sslverify' => false ) );

		if ( is_wp_error( $check_response ) ){
			$status = 'error';
			$output = self::wp_error_message( $check_response );
		}
		else{
			$output = json_decode( wp_remote_retrieve_body( $check_response ) );
		}

		return array( 'status' => $status, 'output' => $output );
	}
	
	/**
	 * Return error message.
	 * 
	 * @return string wordpress error message.
	 */
	public static function wp_error_message( $error_object = '' ){
		return $error_object->get_error_message();
	}
	
	public static function license_action( $slm_action, $license_key ){
		$status = 'success';
		$output = '';

		$api_params = array(
			'slm_action' => $slm_action,
			'secret_key' => KR_SECRET_KEY,
			'license_key' => $license_key,
			'registered_domain' => $_SERVER['SERVER_NAME'],
			'item_reference' => urlencode(KR_ITEM_REFERENCE),
		);

		$query = esc_url_raw( add_query_arg( $api_params, KR_SERVER_URL ) );
		$response = wp_remote_get( $query, array( 'timeout' => 20, 'sslverify' => false ) );

		if ( is_wp_error( $response ) ){
			$status = 'error';
			$output = self::wp_error_message( $response );
		}
		else{
			$license_data = json_decode(wp_remote_retrieve_body($response));

			if( !empty( $license_data ) ){
				if($license_data->result == 'success'){
					if( $slm_action == 'slm_activate' ){
						self::add_capabilities( $license_key );
					}
					else{
						self::remove_capabilities( $license_key );
					}
					$output = '<p>'.$license_data->message.'</p>';
					$status = 'success';
				}
				else{
					$output = '<p>'.$license_data->message.'</p>';
					$status = 'error';
				}
			}
			else{
				$output = self::$error_message;
				$status = 'error';
			}
		}

		return array( 'status' => $status, 'output' => $output );
	}
	
	public static function add_capabilities( $license_key = '' ){

		if( empty( $license_key ) ){
			self::run_deactivate();
		}
		else{
			update_option('kr_license_key', $license_key);

			/* Save Transients */
			$license_info_check = self::check_license_info( $license_key );
			$license_info = array();

			if( $license_info_check['status'] = 'success' ){

				$license_info_obj = $license_info_check['output'];

				$license_check_info = self::parse_license_info( $license_info_obj );
				if( is_array( $license_check_info ) && !empty( $license_check_info ) && array_key_exists( 'status', $license_check_info ) ){
					$parsed_license_info = $license_check_info;
					
					set_transient( 'lpb_license_key', $license_key, 0 );
					
					set_transient( self::generate_transient_name(), $parsed_license_info, 0 );

					set_transient( "lpb_license_info",  $license_info_obj, HOUR_IN_SECONDS * 6 );
				}
			}

			$args = array(
				'role' => 'administrator',
			);
			$users = get_users($args);
			foreach ($users as $user) {
				$user->add_role( 'kr_admin_role' );
			}
		}
	}
	
	public static function remove_capabilities( $license_key = '', $from = 'deactivated' ){
		update_option('kr_license_key', '');
		update_option( 'kr_license_check_notices', '' );
		update_option("_lpb_license_info",'');

		/* Remove Transients */
		delete_transient( "lpb_license_info");

		if( $from == 'deactivated' ){
			delete_transient( "lpb_license_key");
		}

		$args = array(
			'role' => 'administrator',
		);
		$users = get_users($args);
		foreach ($users as $user) {
			$user->remove_role( 'kr_admin_role' );
		}
	}
	
	public static function parse_license_info( $license_data ){
		$license_info = array();

		if ( is_object( $license_data ) ) {
			$license_data_status = $license_data->status;
			$eligibility = 'eligible';
			if( $license_data_status != 'active' ){
				$eligibility = 'not-eligible';
			}
			
			if(property_exists($license_data,"registered_domains")){
				foreach( $license_data->registered_domains as $value )
				{
					//Check if current domain, then get current domain and license key
					if( $value->registered_domain == $_SERVER['SERVER_NAME'] ){
						//domain
						$license_info['installation_url'] = $value->registered_domain;
						//license
						$license_info['license_key'] = $value->lic_key; 
						$license_info['eligibility'] = $eligibility; 
						break;
					}
				}
			}
			
	        if( !empty( $license_info ) ){
	    		$license_info['status'] = $license_data_status;
	    		$license_info['date_expiry'] = $license_data->date_expiry;
	        }
        }

        return $license_info;
	}
	
	public static function check_activated_deactivated_status(){
		$lpb_license_info = get_transient("lpb_license_info");
		$lpb_license_key = get_transient("lpb_license_key");
		if(false === $lpb_license_info && false === $lpb_license_key ){
			return "deactivated";
		}
		return "activated";
	}
}
?>
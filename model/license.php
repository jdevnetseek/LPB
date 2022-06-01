<?php 
/**
 * LandingPageBooster SL_Manager
 *
 * update API Method
 *
 * @class 		SL_Manager
 * @version		1.0.0
 * @package		LandingPageBooster/Classes
 * @category	Class
 * @author 		Netseek
 */ 
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
} 
require_once( ABSPATH . '/wp-load.php' );
if ( ! class_exists( 'SL_Manager' ) ) :
	class SL_Manager 
	{

		 protected static $_instance = null;
		 private $license_data = null;

		protected static $_instance = null;
		private $license_data = null;

		 /**
		 * @var KRLicenseManager $dataKey
		 */
		public static $dataKey;
		/**
		 * @var KRLicenseManager $license
		 */
		public static $license; 
		
		/**
		 * @var KRLicenseManager $email
		 */
		public static $email;
				
		/**
		 * @var KRLicenseManager $deactivateCheckboxKey
		 */
		public static $deactivateCheckboxKey;
		
		/**

		 * @var KRLicenseManager $deactivateCheckboxKey
		 */
		public static $activatedKey;
		
		/**
		 * @var KRLicenseManager $deactivateCheckboxKey
		 */
		public static $action_act = "on";
		public static $action_deact = "off";
		public static $key_act = "Activated";
		public static $key_deact = "Dectivated";
		public static $domain_url = "https://lpb2.6bin.com";

		 * @var KRLicenseManager $activatedKey
		 */
		public static $activatedKey;
		
		
		/**
		 * @var KRLicenseManager $action_act
		 */
		public static $action_act = "on";
		
		/**
		 * @var KRLicenseManager $action_deact
		 */
		public static $action_deact = "off";
		
		/**
		 * @var KRLicenseManager $key_act
		 */
		public static $key_act = "Activated";
		
		/**
		 * @var KRLicenseManager $key_deact
		 */
		public static $key_deact = "Dectivated";
		
		/**
		 * @var KRLicenseManager $domain_url
		 */
		public static $domain_url = "https://lpb2.6bin.com";
		
		/**
		 * @var KRLicenseManager $secret_key
		 */

		public static $secret_key = "58f862dcac7da6.74677125";
		/**
		 * @var KRLicenseManager $timetocheck_key
		 */
		public static $timetocheck_key = '_timetocheck_key';

		public  $lpb_user = null;
		 public static function instance() 
		{
			if ( is_null( self::$_instance ) )
				self::$_instance = new self();
				
			return self::$_instance;
		}
		function __construct( ){
			self::$dataKey 			= '_keywordreplacer';
			$email 		= get_option( '_activation_email' );
			$license 	= get_option( '_license_key' );
			self::$deactivateCheckboxKey 	= '_deactivate_checkbox_key';
			self::$activatedKey 			= 'kr_license_key_activated';
			$options = get_option( self::$dataKey );
		}
		public static function inactive_notice(){
			if ( is_admin() ) {

		/**
		 * @var KRLicenseManager $lpb_user
		 */
		public  $lpb_user = null;
		
		
		/**
		 * @var KRLicenseManager instance
		 */
		public static function instance() 
		{
			if ( is_null( self::$_instance ) )
				self::$_instance = new self();
			return self::$_instance;
		}
		
		/**
		* Constructor for the cart class. Loads options and hooks in the init method.
		*
		* @access public
		* @return void
		*/
		function __construct()
		{
			
			self::$dataKey 			= '_keywordreplacer';
			self::$email 		= get_option( '_activation_email' );
			self::$license 	= get_option( '_license_key' );
			self::$deactivateCheckboxKey 	= '_deactivate_checkbox_key';
			self::$activatedKey 			= 'kr_license_key_activated';
			$options = get_option( self::$dataKey );
			
		}
		
		/**
		* Disyplay Admin Notice
		*
		* @access public
		* @return string
		*/	
		public static function inactive_notice()
		{
			if ( is_admin() ) 
			{

				if ( isset( $_GET['page'] ) && 'krSetup' == $_GET['page'] ) return;
				echo sprintf( '<div class="error"><p>' . __( 'The Landing Page Booster License Key has not been activated. The plugin is inactive! %sClick here%s to activate the license key and the plugin.</div></p>', 'kr' ), '<a href="' . esc_url( admin_url( 'admin.php?page=krSetup' ) ) . '">', '</a>' );
			}
		}

		public static function act_deact_notice(){

		/**
		* Cron Check License
		*
		* @access public
		* 
		*/	
		public static function landpagebooster_cron_hook()
		{
			$check_license = SL_Manager::check_license_info( get_option( '_license_key' ), get_option( '_activation_email' ));
			$Checkbox_Key = get_option( '_deactivate_checkbox_key' ); 
			$Check_key_act = get_option( 'kr_license_key_activated' ); 
			if($Checkbox_Key == 'on' && $Check_key_act == "Activated")
			{
				if($check_license->status == "expired")
				{
					
					SL_Manager::remove_license();
					
				}
				else if($check_license->status == "blocked")
				{
					SL_Manager::remove_license();
				}
				else{}
			} 
			if(isset($check_license->status))
			{
				if ( isset( $_GET['page'] ) && 'krSetup' == $_GET['page'] ) 
				{
					if($check_license->status == "expired")
						echo  sprintf( '<div class="%s"><p>' . __( '%s' ) . '</p></div>', 'error' , " Your License key has expired "  );
					if($check_license->status == "blocked")
						echo  sprintf( '<div class="%s"><p>' . __( '%s' ) . '</p></div>', 'error' , " Your License key is blocked "  );
				}
			}	 
		}
		
		/**
		* Remotely Eliminate License 
		*
		* @access public
		* 
		*/
		public static function remove_license()
		{
			$api_params = array
						(
							'slm_action' => 'slm_deactivate' ,
							'secret_key' => "58f862dcac7da6.74677125",
							'license_key' => get_option( '_license_key' ),//"58fcfb4179526"
							'registered_domain' => get_site_url() ,
							'item_reference' => urlencode("Wordpress LandingPageBooster Plugin"),
						);
			// Send query to the license manager server
			$query = esc_url_raw(add_query_arg($api_params,  "https://lpb2.6bin.com"));
			$response = wp_remote_get($query, array('timeout' => 20, 'sslverify' => false)); 
			$license_data = json_decode(wp_remote_retrieve_body($response)); 
			
			if ( is_wp_error( $response ) )
			{
				 return null;
			}
			if($license_data->result == "success" )
			{
				 update_option( "_deactivate_checkbox_key", "off" );	
				 update_option( "kr_license_key_activated", "Deactivated" );	
				 $args = array
						(
							'role' => 'kr_LPB_admin',
						);
				 $users = get_users($args);
				 foreach ($users as $user) 
				 {
					 $lpb_user = new WP_User( $user->ID );
					 $lpb_user->remove_cap( 'kr_LPB_admin' );
					
				 }
				 $timestamp = wp_next_scheduled( 'landpagebooster_cron_hook' );
				 wp_unschedule_event( $timestamp, 'landpagebooster_cron_hook' );
			}
		}
		
		/**
		* License Notice
		*
		* @access public
		* 
		*/
		public static function act_deact_notice()
		{

			if ( is_admin() ) 
			{
				if ( isset( $_GET['page'] ) && 'krSetup' != $_GET['page'] ) return;
				$data 				= SL_M()->get_license_info();

				
				if ( isset( $_GET['result']) && isset( $_GET['action']  ) ){
					if($_GET['action'] == "activate" ){

				if ( isset( $_GET['result']) && isset( $_GET['action']  ) )
				{
					if($_GET['action'] == "activate" )
					{

						if($_GET['result'] == "success" && $data[ 'deactivate_checkbox' ] == "on")
							echo  sprintf( '<div class="%s"><p>' . __( '%s' ) . '</p></div>', 'updated' , "License Activated"  ); 
						else
							echo  sprintf( '<div class="%s"><p>' . __( '%s' ) . '</p></div>', 'error' , "Invalid License"  ); 
					}
				}

				else if(isset( $_GET['action']  )){
					if($_GET['action'] == "deactivate" && $data[ 'deactivate_checkbox' ] == "off")
						echo  sprintf( '<div class="%s"><p>' . __( '%s' ) . '</p></div>', 'error' , "License Deactivated"  ); 
		
				}
				else if(isset( $_GET['result']  )){
					if($_GET['result'] == "error_domain")
						echo  sprintf( '<div class="%s"><p>' . __( '%s' ) . '</p></div>', 'error' , "An unexpected error occurred. Please contact the <a href='http://www.netseek.com.au/"  ); 
		
				}
				else{}
			}
			
		}
		 public function  trigger_act($email ,$licence_key){
			$api_params = array(
				'slm_action' => 'slm_check',
				'secret_key' => self::$secret_key,
				'license_key' => $licence_key,//"58fcfb4179526"
				'registered_domain' => self::$domain_url ,
			);
			// Send query to the license manager server
			$query = esc_url_raw(add_query_arg($api_params,  self::$domain_url));
			$response = wp_remote_get($query, array('timeout' => 20, 'sslverify' => false));
		
			return $this->request($licence_key,$email,$response);

		}
		 public function trigger_deact($email ,$licence_key){
			 update_option( "_deactivate_checkbox_key", self::$action_deact );	
			 update_option( "kr_license_key_activated", self::$key_deact );	
			  $license_data =(object)  array(
							"result" => "error ",
							"message" => "License Key Deactivated",
							"error_code" => 60 ,
						);
			$get_current_user_id = get_current_user_id();
			$lpb_user = new WP_User( $get_current_user_id );
			
			$lpb_user->remove_cap('kr_LPB_admin');
			header('Location: '.get_site_url().'/wp-admin/admin.php?page=krSetup&action=deactivate');		
		
			return $license_data;
		 }
		private function request($licence_key, $email,$params = array() )
		{
			$license_data = json_decode(wp_remote_retrieve_body($params));
			$get_current_user_id = get_current_user_id();
			$lpb_user = new WP_User( $get_current_user_id );
			 if ( is_wp_error( $params ) ){
				
				header('Location: '.get_site_url().'/wp-admin/admin.php?page=krSetup&result=error_domain');		
				
				return $license_data;
			}
			
			 if($license_data->result == "error " ){
				return $license_data;
			 }
			 else if(isset($license_data->email) && isset($license_data->status)){
				  
				 if($email == $license_data->email && $license_data->status == "active"){
					
					update_option( "_deactivate_checkbox_key", self::$action_act );	
					update_option( "kr_license_key_activated", self::$key_act );	
					
					$lpb_user->add_role( 'kr_LPB_admin' );
					$this->set_options($licence_key, $params ); 	
				}
				else{
					header('Location: '.get_site_url().'/wp-admin/admin.php?page=krSetup&result=error&action=activate');		
				} 
			 }
			 
			 else{
				header('Location: '.get_site_url().'/wp-admin/admin.php?page=krSetup&result=error&action=activate');		
			 }
			return $license_data;
		}
		
		public function set_options($licence_key, $params = array() ){
			$options = get_option( self::$dataKey );
			$license_data = json_decode(wp_remote_retrieve_body($params));
			
			if( ! empty ( $options ) )
			{
				update_option( '_license_key', $licence_key );
				update_option( '_activation_email', $license_data->email);

				else if(isset( $_GET['action']  ))
				{
					if($_GET['action'] == "deactivate" && $data[ 'deactivate_checkbox' ] == "off")
						echo  sprintf( '<div class="%s"><p>' . __( '%s' ) . '</p></div>', 'updated' , "The license key has been deactivated for this domain"  ); 
				}
				else if(isset( $_GET['result']  ))
				{
					if($_GET['result'] == "error")
					{
						if($_GET['error_code'] == "1")
							echo  sprintf( '<div class="%s"><p>' . __( '%s' ) . '</p></div>', 'error' , "An unexpected error occurred. Please contact the <a href='http://www.netseek.com.au/"  ); 
						if($_GET['error_code'] == "60")
							echo  sprintf( '<div class="%s"><p>' . __( '%s' ) . '</p></div>', 'error' , " Invalid license key"  ); 
						if($_GET['error_code'] == "2")
							echo  sprintf( '<div class="%s"><p>' . __( '%s' ) . '</p></div>', 'error' , " Invalid Email "  ); 
						if($_GET['error_code'] == "40")
							echo  sprintf( '<div class="%s"><p>' . __( '%s' ) . '</p></div>', 'error' , " License key already in use on https://lpb2.6bin.com "  ); 
						if($_GET['error_code'] == "80")
							echo  sprintf( '<div class="%s"><p>' . __( '%s' ) . '</p></div>', 'error' , " The license key on this domain is already inactive "  ); 
						if($_GET['error_code'] == "20")
							echo  sprintf( '<div class="%s"><p>' . __( '%s' ) . '</p></div>', 'error' , " Your License key is blocked "  ); 
						if($_GET['error_code'] == "30")
							echo  sprintf( '<div class="%s"><p>' . __( '%s' ) . '</p></div>', 'error' , " Your License key has expired "  ); 
						if($_GET['error_code'] == "50")
							echo  sprintf( '<div class="%s"><p>' . __( '%s' ) . '</p></div>', 'error' , " Reached maximum allowable domains "  ); 
					
					}
				}
				else{}
			}
		}
		/**
		* Trigger Activation
		*
		* @access public
		* 
		*/
		 public static function  trigger_act($email ,$licence_key)
		 {
			 $response_email = isset(SL_Manager::check_license_info($licence_key, $email)->email)?SL_Manager::check_license_info($licence_key, $email)->email:null;
			 if(isset($response_email))
			 {
				if( $email == $response_email )
				{
					$api_params = array
								  (
									'slm_action' => 'slm_activate' ,
									'secret_key' => self::$secret_key,
									'license_key' => $licence_key,//"58fcfb4179526"
									'registered_domain' => get_site_url() ,
									'item_reference' => urlencode("Wordpress LandingPageBooster Plugin"),
								  );
					// Send query to the license manager server
					$query = esc_url_raw(add_query_arg($api_params,  self::$domain_url));
					$response = wp_remote_get($query, array('timeout' => 20, 'sslverify' => false));
					return SL_Manager::request("trigger_act",$licence_key,$email,$response);
				}
				else if($email != $response_email)
				{
					header('Location: '.get_site_url().'/wp-admin/admin.php?page=krSetup&result=error&error_code=2');	
				}
				else
				{

				}
			}
			else
			{
				header('Location: '.get_site_url().'/wp-admin/admin.php?page=krSetup&result=error&error_code=60');	
					
			}

		}
		
		/**
		* Trigger Dectivation
		*
		* @access public
		* 
		*/
		public static function trigger_deact($email ,$licence_key)
		{
			$api_params = array
						  (
							'slm_action' => 'slm_deactivate' ,
							'secret_key' => self::$secret_key,
							'license_key' => $licence_key,//"58fcfb4179526"
							'registered_domain' => get_site_url(),
							'item_reference' => urlencode("Wordpress LandingPageBooster Plugin"),
						  );
			// Send query to the license manager server
			$query = esc_url_raw(add_query_arg($api_params,  self::$domain_url));
			$response = wp_remote_get($query, array('timeout' => 20, 'sslverify' => false));
			return SL_Manager::request("trigger_deact",$licence_key,$email,$response);
		}
		
		
	    /**
	    *  SLM Remote Call Request
	    *
	    * @access public
	    * @return array
	    */	
		private static function request($action , $licence_key, $email,$params = array())
		{
			$license_data = json_decode(wp_remote_retrieve_body($params));
			if($action == "trigger_act"){
				
				$get_current_user_id = get_current_user_id();
				$lpb_user = new WP_User( $get_current_user_id );
				if ( is_wp_error( $params ) ){
					header('Location: '.get_site_url().'/wp-admin/admin.php?page=krSetup&result=error_domain');		
					return $license_data;
				}
				if($license_data->result == "error" )
				{
					header('Location: '.get_site_url().'/wp-admin/admin.php?page=krSetup&result='.$license_data->result.'&error_code='.$license_data->error_code);	
					return $license_data;
				}
				else if(isset($license_data->result))
				{
					if( $license_data->result == 'success' )
					{
						if ( ! wp_next_scheduled( 'landpagebooster_cron_hook' ) ) {
							wp_schedule_event( time(), 'daily', 'landpagebooster_cron_hook' );
						}
						update_option( "_deactivate_checkbox_key", self::$action_act );	
						update_option( "kr_license_key_activated", self::$key_act );	
						$args = array
						(
							'role' => 'administrator',
						);
						 $users = get_users($args);
						 foreach ($users as $user) 
						 {
							 $lpb_user = new WP_User( $user->ID );
							 $lpb_user->add_role( 'kr_LPB_admin' );
							
						 }
						
						
						
						
						
						SL_Manager::set_options( $email,$licence_key, $params ); 	
					}
					else
					{
						header('Location: '.get_site_url().'/wp-admin/admin.php?page=krSetup&result=error&error_code=2');		
					} 
				}
				else
				{
					header('Location: '.get_site_url().'/wp-admin/admin.php?page=krSetup&result=error&action=activate');		
				}
				return $license_data;
			}
			if($action == "trigger_deact")
			{
				$license_data = json_decode(wp_remote_retrieve_body($params));
					
				if ( is_wp_error( $params ) )
				{
					
					header('Location: '.get_site_url().'/wp-admin/admin.php?page=krSetup&result=error_domain');		
					
					return $license_data;
				}
				else if(isset($license_data->result))
				{
					 if($license_data->result == 'success' )
					 {
						 $get_current_user_id = get_current_user_id();
						 $lpb_user = new WP_User( $get_current_user_id );
						 update_option( "_deactivate_checkbox_key", self::$action_deact );	
						 update_option( "kr_license_key_activated", self::$key_deact );	
						 $lpb_user->remove_cap('kr_LPB_admin'); 
						 $timestamp = wp_next_scheduled( 'landpagebooster_cron_hook' );
						 wp_unschedule_event( $timestamp, 'landpagebooster_cron_hook' );
						 header('Location: '.get_site_url().'/wp-admin/admin.php?page=krSetup&action=deactivate');	
						 return null;
					 }
					 if($license_data->result == 'error' || $license_data->error_code == '80'  )
					 {
						  $get_current_user_id = get_current_user_id();
						  $lpb_user = new WP_User( $get_current_user_id );
						  update_option( "_deactivate_checkbox_key", self::$action_deact );	
						  update_option( "kr_license_key_activated", self::$key_deact );	
						  $lpb_user->remove_cap('kr_LPB_admin');  
						  $timestamp = wp_next_scheduled( 'landpagebooster_cron_hook' );
						  wp_unschedule_event( $timestamp, 'landpagebooster_cron_hook' );
						  header('Location: '.get_site_url().'/wp-admin/admin.php?page=krSetup&result='.$license_data->result.'&error_code='.$license_data->error_code);	
						return null;
					 }
				}
				else{}
				return $license_data;
			}
		}
		
		/**
		* License Get Information API Request
		*
		* @access public
		* @return array
		*/	
		public static function check_license_info($licence_key, $email )
		{
			$api_params = array
						(
							'slm_action' => 'slm_check' ,
							'secret_key' => self::$secret_key,
							'license_key' => $licence_key,//"58fcfb4179526"
							'registered_domain' => get_site_url() ,
							'item_reference' => urlencode("Wordpress LandingPageBooster Plugin"),
						);
			// Send query to the license manager server
			$query = esc_url_raw(add_query_arg($api_params,  self::$domain_url));
			$response = wp_remote_get($query, array('timeout' => 20, 'sslverify' => false));
			return json_decode(wp_remote_retrieve_body($response));
		}
		
		/**
		* Save Option Setting
		*
		* @access public
		* 
		*/
		public static function set_options($email,$licence_key, $params = array() )
		{
			$options = get_option( self::$dataKey );
			if( ! empty ( $options ) )
			{
				update_option( '_license_key', $licence_key );
				update_option( '_activation_email', $email);

				update_option( self::$timetocheck_key, strtotime("+30 days") );	 			
			}
			else
			{
				add_option( '_keywordreplacer', self::$dataKey );		
				add_option( '_license_key', $licence_key);
				add_option( '_activation_email', $license_data->email );
				add_option( 'current_version_key',  self::$current_version );		
				add_option( self::$timetocheck_key, strtotime("+30 days"));			
			} 
			 header('Location: '.get_site_url().'/wp-admin/admin.php?page=krSetup&result=success&action=activate');		
			
		}
		public function get_license_info()

				add_option( self::$timetocheck_key, strtotime("+30 days"));			
			} 
			header('Location: '.get_site_url().'/wp-admin/admin.php?page=krSetup&result=success&action=activate');		
			
		}
		/**
		* Get License Information
		*
		* @access public
		* 
		*/
		public static function get_license_info()

		{
			$options = get_option( self::$dataKey );
			$params = array();

			if ( ! empty( $options ) && $options !== false ) {
			
		
				$params[ 'license' ] 	= get_option( '_license_key' ); 
				$params[ 'email' ] 		= get_option( '_activation_email' ); 		

				//$params[ 'deactivateCheckbox' ] 		= self::$deactivateCheckbox; 	
				$params[ 'deactivate_checkbox' ] = get_option( self::$deactivateCheckboxKey ); 
				
				// $params[ 'activated' ] 		= self::$activated;
			
			} else {
				$params = array();
			}
			
			return  $params;
		
		}
		
}
endif;


function SL_M() {
	return SL_Manager::instance();
}
 if (is_admin())
        {
SL_M();
		}
				
				$params[ 'deactivate_checkbox' ] = get_option( self::$deactivateCheckboxKey ); 
				
				
			
			} else 
			{
				$params = array();
			}
			return  $params;
		}
	}
endif;

function SL_M() 
{
	return SL_Manager::instance();
}
if (is_admin())
{
	SL_M();
}
?>
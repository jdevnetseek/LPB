<?php
/**
 * LandingPageBooster KRLicenseManager
 *
 * License Manager API Method
 *
 * @class 		KRLicenseManager
 * @version		2.4.4
 * @package		LandingPageBooster/Classes
 * @category	Class
 * @author 		Netseek
 */ 



require_once KR_PlUGIN_PATH() . '/model/lpb_revert.php';

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
} 
if ( ! class_exists( 'KRLicenseManager' ) ) :
class KRLicenseManager {
	/**
	 * @var KRLicenseManager $licenseUrl
	 */
	public static $licenseUrl 		= 'http://www.landingpagedboodster.com/';
	
	/**
	 * @var KRLicenseManager $wcapi
	 */
	public static $wcapi 				= 'am-software-api';
	
	/**
	 * @var KRLicenseManager $version
	 */
	public static $version 			= '2.6.5';
	
	/**
	 * @var KRLicenseManager $productName
	 */
	public static $productName 		= 'Landing Page Booster';
	
	/**
	 * @var KRLicenseManager $dataKey
	 */
	public static $dataKey;
	
	/**
	 * @var KRLicenseManager $licenseKey
	 */
	public static $licenseKey;
	
	/**
	 * @var KRLicenseManager $activationEmail
	 */
	public static $new_version_cookies;
	/**
	 * @var KRLicenseManager $activationEmail
	 */
	public static $activationEmail;
	
	/**
	 * @var KRLicenseManager $productIdKey
	 */
	public static $productIdKey;
	
	/**
	 * @var KRLicenseManager $instanceKey
	 */
	public static $instanceKey;
	
	/**
	 * @var KRLicenseManager $deactivateCheckboxKey
	 */
	public static $deactivateCheckboxKey;
	
	/**
	 * @var KRLicenseManager $activatedKey
	 */
	public static $activatedKey;
	
	/**
	 * @var KRLicenseManager $currentVersionKey
	 */
	public static $currentVersionKey;
	
	/**
	 * @var KRLicenseManager $timetocheck_key
	 */
	public static $timetocheck_key = '_timetocheck_key';
	
	/**
	 * @var KRLicenseManager $errorMessage
	 */
	public static $errorMessage;

	/**
	 * @var KRLicenseManager $options
	 */
	public static $options;
	
	/**
	 * @var KRLicenseManager $pluginName
	 */
	public static $pluginName;
	
	/**
	 * @var KRLicenseManager $productId
	 */
	public static $productId; 
	
	/**
	 * @var KRLicenseManager $license
	 */
	public static $license; 
	
	/**
	 * @var KRLicenseManager $email
	 */
	public static $email;
	
	/**
	 * @var KRLicenseManager $timetocheck
	 */
	public static $timetocheck;	
	
	/**
	 * @var KRLicenseManager $deactivateCheckbox
	 */
	public static $deactivateCheckbox;
	
	/**
	 * @var KRLicenseManager $activated
	 */
	public static $activated;
	
	/**
	 * @var KRLicenseManager $instance
	 */
	public static $instance;

	/**
	 * @var KRLicenseManager $noticeId
	 */
	public static $noticeId;
	
	/**
	 * @var KRLicenseManager $noticeMessage
	 */
	public static $noticeMessage;
	
	public static $platform;
	
	public static $current_version;
	
	protected static $_instance = null;

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

	public function __construct()
	{

		if ( is_admin() ) {
		   	
			self::$platform 		= site_url();
			self::$dataKey 			= '_keywordreplacer';
			self::$licenseKey  		= '_license_key';
			self::$activationEmail 	= '_activation_email';
			self::$productIdKey 	= '_product_id_key';
			self::$instanceKey 		= '_instance_key';
			self::$deactivateCheckboxKey 	= '_deactivate_checkbox_key';
			self::$activatedKey 			= 'kr_license_key_activated';
			self::$currentVersionKey 		= 'current_version_key';
			$options = get_option( self::$dataKey );
			$email 		= get_option( '_activation_email' );
			$license 	= get_option( '_license_key' );
			$productId = get_option( '_product_id_key' );
			if ( ! empty( $options ) && $options !== false ) {
				
				self::$options 				= get_option( self::$dataKey );
				self::$pluginName 			= untrailingslashit( plugin_basename( __FILE__ ) );
				self::$productId 			= get_option( self::$productIdKey ); 
				self::$license				= get_option( self::$licenseKey ); 
				self::$email				= get_option( self::$activationEmail ); 
				self::$instance				= get_option( self::$instanceKey ); 		
				self::$current_version		= get_option( self::$currentVersionKey ); 	
				
				self::$deactivateCheckbox 	=  get_option( self::$deactivateCheckboxKey ); 
				self::$activated 			=  get_option( self::$activatedKey ); 
				
				self::$timetocheck = get_option( self::$timetocheck_key );
				
			
			
			
			
			 $this->check_update_domain( $email , $license , $productId );
				
			
			
			
				
				 if( self::$timetocheck <= time() ){ 
					
					$result 	= $this->update_check( $email , $license , $productId );
					
			 }
				
				
			}
		}
			
	}
	
	
	function strbits($string){
		return (strlen($string)*8);
	}

	/** 
	 * Validate Activation Key.
	 *
	 * @access public
	 * @return boolean
	 */
	
	public static function validate_app()
	{
		$valid = get_option( '_deactivate_checkbox_key' );
		
		if( $valid == 'on' ){
			$result = true;
		} else {
			$result = false;
		}
		
		return $result;
	}
	
	 /**
	 * License Activation Method.
	 *
	 * @access public
	 * @return array
	 */
	
	public function activation( $email , $licence_key , $software_title )
	{
	
		$Password = new KRPassword_Management();
		$instance = $Password->generate_password( 12, false );
		
		$params[ 'wc-api' ] 		= 'am-software-api';
		$params[ 'request' ] 		= 'activation';
		$params[ 'email' ] 			= trim( $email );
		$params[ 'licence_key' ] 	= trim( $licence_key );
		$params[ 'product_id' ] 	= trim( $software_title );
		$params[ 'platform' ] 		= self::$platform;
		$params[ 'instance' ] 		= $instance;
		$params[ 'software_version' ] = self::$version;

		return $this->request( $params ); 
							
	}

	/**
	 * License deactivation Method.
	 *
	 * @access public
	 * @return array
	 */
	
	public function deactivation( $email , $licence_key , $software_title )
	{	
		$params[ 'wc-api' ] 		= 'am-software-api';
		$params[ 'request' ] 		= 'deactivation';
		$params[ 'email' ] 			= $email;
		$params[ 'licence_key' ] 	= $licence_key;
		$params[ 'product_id' ] 	= $software_title;
		$params[ 'platform' ] 		= self::$platform;
		$params[ 'instance' ] 		= self::$instance; 

		return $this->request( $params ); 
	}
	
	/**
	 * Display Updates and Error Message.
	 *
	 * @access public
	 * @return array
	 */	
	
	public function get_updates_errorMessages( $error_key )
	{

		
		$my_account = self::$licenseUrl."/members/my-account/";
		$erros['expired_license_error_notice'] 		= sprintf(  __( 'The license key for %s has expired. You can reactivate or purchase a license key from your account <a href="%s" target="_blank">dashboard</a>.' ) , self::$productName , $my_account ) ;
		$erros['on_hold_subscription_error_notice'] = sprintf(  __( 'The subscription for %s is on-hold. You can reactivate the subscription from your account <a href="%s" target="_blank">dashboard</a>.') , self::$productName , $my_account ) ;
		$erros['cancelled_subscription_error_notice'] 	= sprintf(  __( 'The subscription for %s has been cancelled. You can renew the subscription from your account <a href="%s" target="_blank">dashboard</a>. A new license key will be emailed to you after your order has been completed.' ) , self::$productName , $my_account ) ;
		$erros['expired_subscription_error_notice'] 	= sprintf(  __( 'The subscription for %s has expired. You can reactivate the subscription from your account <a href="%s" target="_blank">dashboard</a>.') , self::$productName , $my_account ) ;
		$erros['suspended_subscription_error_notice'] 	= sprintf(  __( 'The subscription for %s has been suspended. You can reactivate the subscription from your account <a href="%s" target="_blank">dashboard</a>.') , self::$productName , $my_account ) ;
		$erros['pending_subscription_error_notice'] 	= sprintf(  __( 'The subscription for %s is still pending. You can check on the status of the subscription from your account <a href="%s" target="_blank">dashboard</a>.') , self::$productName , $my_account ) ;
		$erros['trash_subscription_error_notice'] 		= sprintf(  __( 'The subscription for %s has been placed in the trash and will be deleted soon. You can purchase a new subscription from your account <a href="%s" target="_blank">dashboard</a>.' ) ,self::$productName , $my_account ) ;
		$erros['no_subscription_error_notice'] 			= sprintf(  __( 'A subscription for %s could not be found. You can purchase a subscription from your account <a href="%s" target="_blank">dashboard</a>.' ) , self::$productName , $my_account ) ;
		$erros['no_key_error_notice'] 					= sprintf(  __( 'A license key for %s could not be found. Maybe you forgot to enter a license key when setting up %s, or the key was deactivated in your account. You can reactivate or purchase a license key from your account <a href="%s" target="_blank">dashboard</a>.' ) , self::$productName , $pluginName, $my_account ) ;
		$erros['download_revoked_error_notice'] 		= sprintf(  __( 'Download permission for %s has been revoked possibly due to a license key or subscription expiring. You can reactivate or purchase a license key from your account <a href="%s" target="_blank">dashboard</a>.' ) , self::$productName , $my_account ) ;
		$erros['no_activation_error_notice'] 			= sprintf(  __( '%s has not been activated. Go to the settings page and enter the license key and license email to activate %s.' ) , self::$productName, self::$productName ) ;
		$erros['switched_subscription_error_notice'] 	= sprintf(  __( 'You changed the subscription for %s, so you will need to enter your new API License Key in the settings page. The License Key should have arrived in your email inbox, if not you can get it by logging into your account <a href="%s" target="_blank">dashboard</a>.' ) , self::$productName , $my_account ) ;
		
		return 	$erros[$error_key];	
	}

	
	/**
	 * Check user logged in 
	 *
	 * @access private
	 * @return boolean
	 */	
 function is_user_log()
 {
	require_once( ABSPATH . 'wp-includes/pluggable.php' );
	return is_user_logged_in();
 }

/**
 * Returns current plugin version.
 * 
 * @return string Plugin version
 */
function plugin_get_version() {
	if ( ! function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$plugin_data = get_plugin_data(ABSPATH . 'wp-content/plugins/landing-page-booster/app.php' );
	$plugin_version = $plugin_data['Version'];
	return $plugin_version;   
}

	/**
	 * License API Remote Call Request
	 *
	 * @access public
	 * @return array
	 */	
	
	public function request( $params = array() )
	{
			$url 		=  self::$licenseUrl .'?'. http_build_query( $params ) ;
		$request 	= wp_remote_get( $url );
		$array_error = json_decode(json_encode($request, true), true);

		if(array_key_exists('errors', $array_error)){
			add_action( 'admin_notices', array( $this , 'admin_check_server' )); 
			return null;
		} 
		if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
		
			if(  $request[ 'response' ][ 'code' ] == 500 ) {
				//$errorMessage 				= "Can't Connect to License Server.";
				$errorMessage 				= "could not establish a secure connection to LPB License Server.";
			} else {
				$errorMessage 				= $request->get_errorMessage();
				//$errorMessage 				= $request[ 'response' ][ 'message' ];
			}				
			//$errorMessage 				= $request->get_errorMessage();
			$results[ 'errorMessage' ]	 	= sprintf( __( "Landing Page Booster Error: %s" ), $errorMessage );
			
		}else{
		
			$response = wp_remote_retrieve_body( $request );
			$response = json_decode( $response );
		
			if( ! empty ( $response->error ) ){
				$err_codes = array( 100, 101, 102, 103, 104, 105, 106 );
				if( in_array( $response->code , $err_codes ) ) {
					update_option( self::$deactivateCheckboxKey, 'off' );
					update_option( self::$activatedKey, 'Deactivated' );
					
					if( $response->code == '103' ) {
						$results[ 'errorMessage' ] 		= __( $response->error . '. Login to your <a href="'.self::$licenseUrl.'/members/my-account/" target="_blank">My Account</a> > My API Keys page to deactivate/delete licenses.' );
					} else {
						$results[ 'errorMessage' ] 		= __( $response->error );
					}
				
				} else {
					$results[ 'errorMessage' ] 		= __( 'License Error: ' . $response->error );
				}	
				
				
			} else {
				
				if( $params[ 'request' ]  == 'activation' ){
					
					$results[ 'activation_extra' ] 	=  $response->activation_extra;
					$results[ 'instance' ] 	=  $response->instance;
					$results[ 'activated' ] =  $response->activated;
					$results[ 'timestamp' ] =  $response->timestamp;
					$results[ 'activated' ] =  $response->activated;
			
					$results[ 'email' ] 		= $params[ 'email' ];
					$results[ 'licence_key' ] 	= $params[ 'licence_key' ];
					$results[ 'product_id' ] 	= $params[ 'product_id' ];
					$results[ 'platform' ] 		= $params[ 'platform' ];
					$results[ 'software_version' ] = $params[ 'software_version' ];
					$results[ 'deactivate_checkbox' ] = (  $response->activated == 1 ? 'on' : 'off' );
					$results[ 'message' ]	 	= "API License Key Activated!";
					
					update_option( self::$deactivateCheckboxKey, 'on' );
					
					$this->setOptions( $results ); 	
							 
						
				} elseif( $params[ 'request' ]  == 'status' ){
					
					$results[ 'status_check' ] =  $response->status_check;
					$results[ 'status_extra' ] =  $response->status_extra;
					
				} elseif( $params[ 'request' ]  == 'deactivation' ){
					
					$results[ 'deactivated' ] =  $response->deactivated;
					$results[ 'deactivate_checkbox' ] = (  $response->deactivated == 1 ? 'off' : 'on' );
					update_option( self::$deactivateCheckboxKey, 'off' );
					update_option( self::$activatedKey, 'Deactivated' );
					$results[ 'message' ] 	= "API License Key Deactivated!";
				
				}

			}
			
		}
		
		return $results; 
	}
	
	/**
	 * Check server LandingPageBooster
	 *
	 * @access public
	 * 
	 */	
	public function admin_check_server()
	{
		echo sprintf( '<div class="error"><p>' . __( 'The Landing Page Booster server has been shut down.</div></p>', 'kr' ), '<a href="' .$licenseUrl  . '">', '</a>' );
	}
	
	/**
	 * Reset Meta Option Values
	 *
	 * @access public
	 * 
	 */	
	
	public function kr_reset_options()
	{
		update_option( '_license_key', '' );
		update_option( '_activation_email', '' );
		update_option( '_product_id_key', '' );
		update_option( '_instance_key', '' );
		update_option( '_timetocheck_key', '' );	 			
		update_option( 'kr_license_key_activated',  ''  );
	}
	
	
	/**
	 * Set License Manager Options Values
	 *
	 * @access public
	 * @return void
	 */	
	

	public function setOptions( $params )
	{	
		$options = get_option( self::$dataKey );
		
		if( ! empty ( $options ) ){
			
			update_option( '_license_key', $params[ 'licence_key' ] );
			update_option( '_activation_email', $params[ 'email' ] );
			update_option( '_product_id_key', $params[ 'product_id' ]);
			update_option( '_instance_key', $params[ 'instance' ]);
			update_option( self::$timetocheck_key, strtotime("+30 days") );	 			
			update_option( 'kr_license_key_activated', 'Activated' );
		
		}else{
		
			add_option( '_keywordreplacer', self::$dataKey );		
			add_option( '_license_key', $params[ 'licence_key' ] );
			add_option( '_activation_email', $params[ 'email' ] );
			add_option( '_product_id_key', $params[ 'product_id' ]);
			add_option( '_instance_key', $params[ 'instance' ] );
			add_option( '_deactivate_checkbox_key', 'on' );
			add_option( 'kr_license_key_activated', 'Activated' );
			add_option( 'current_version_key',  self::$current_version );		
			add_option( self::$timetocheck_key, strtotime("+30 days"));			
		}
		 
		$curr_ver = get_option( 'current_version_key' );
			
		if ( version_compare( self::$version, $curr_ver, '>' ) ) {
			update_option( self::$currentVersionKey, self::$version );
		}	
				
	}

	/**
	 * License Get Status API Request
	 *
	 * @access public
	 * @return array
	 */	

	
	public function get_status()
	{
		$params[ 'wc-api' ] 		= 'am-software-api';
		$params[ 'request' ] 		= 'status';
		$params[ 'email' ]  		= self::$email;
		$params[ 'licence_key' ] 	= self::$license;
		$params[ 'product_id' ] 	= self::$productId;				
		$params[ 'platform' ] 		= self::$platform;
		$params[ 'instance' ] 		= self::$instance;
		
		return $this->request( $params ); 
	
	}
	
	/**
	 * License Get Information API Request
	 *
	 * @access public
	 * @return array
	 */	
	
	public function get_license_info()
	{
		
		$options = get_option( self::$dataKey );
		$params = array();

		if ( ! empty( $options ) && $options !== false ) {
		
			$params[ 'product_id' ] = self::$productId; 
			$params[ 'license' ] 	= self::$license; 
			$params[ 'email' ] 		= self::$email; 		
			$params[ 'deactivate_checkbox' ] = get_option( self::$deactivateCheckboxKey ); 
			
			$params[ 'activated' ] 		= self::$activated;
		
		} else {
			$params = array();
		}
		
		return  $params;
	
	}

	/**
	 * Get Keyword Limits by License product id
	 *
	 * @access public
	 * @return object
	 */	
	
	public static function get_limits() 
	{
		$productId = trim( get_option( '_product_id_key' ) );
		
		$limits = new stdClass;
		switch( $productId ){
			case 'LandingPageBooster-Silver';
				$limits->pages = 3;
				$limits->links = 30; 
				break;
			case 'LandingPageBooster-Gold';
				$limits->pages = 9; 
				$limits->links = 60; 
				break;
			case 'LandingPageBooster-Platinum';
				$limits->pages = 10000; 
				$limits->links = 60; 
				break;
			default:			
				$limits->pages = 0;
				$limits->links = 0; 
				break;
		}
		
	return $limits;
		
	}

	/**
	 * License Update and Check API Request
	 *
	 * @access public
	 * @return object
	 */	
	
	public function update_check( $email , $licence_key , $software_title ) 
	{
		$params[ 'wc-api' ] 		= 'upgrade-api';
		$params[ 'request' ] 		= 'plugininformation';
		$params[ 'plugin_name' ] 	= self::$pluginName;
		$params[ 'version' ] 		= self::$current_version;
		$params[ 'product_id' ] 	    = $software_title;
		$params[ 'api_key' ] 		= $licence_key;
		$params[ 'activation_email' ]  = $email;
		$params[ 'instance' ] 		= self::$instance;
		$params[ 'domain' ] 		= self::$platform;
		$params[ 'software_version' ] 	= self::$version;
		
		return $this->update_request( $params ); 
	
	}
	
	
	/**
	 * License Update and Check Domain Activation
	 *
	 * @access public
	 * @return object
	 */	
	
	public function check_update_domain( $email , $licence_key , $software_title ) 
	{
	
		$result = $this->get_status( $email , $license , $productId );
	
		 if($result['status_check'] == 'inactive'){update_option(self::$activatedKey, '' );update_option( self::$deactivateCheckboxKey, 'off' );} 
		return $result;  
	}
	
	/**
	 * Disyplay Admin Notice
	 *
	 * @access public
	 * @return string
	 */	
	
	public function admin_notice()
	{	
		if ( is_admin() ) {
			if ( isset( $_GET['page'] ) && 'krSetup' == $_GET['page'] ) return;
				echo  sprintf( '<div class="%s"><p>' . __( '%s' , 'kr'  ) . '</p></div>', self::$noticeId , self::$noticeMessage  ) ;
		}	
	}	
	
	/**
	 * Disyplay Inactive Notice
	 *
	 * @access public
	 * @return string
	 */	
	
	public static function inactive_notice() { 
		if ( is_admin() ) {
			if ( isset( $_GET['page'] ) && 'krSetup' == $_GET['page'] ) return;
			echo sprintf( '<div class="error"><p>' . __( 'The Landing Page Booster License Key has not been activated. The plugin is inactive! %sClick here%s to activate the license key and the plugin.</div></p>', 'kr' ), '<a href="' . esc_url( admin_url( 'admin.php?page=krSetup' ) ) . '">', '</a>' );
		}
	}
	
	/**
	 * Get License URL Server
	 *
	 * @access public
	 * @return string
	 */	
	public static function lisense_url_server() { 
		return self::$licenseUrl;
	}
	
	function doer_of_stuff() {
    return new WP_Error( 'broke', __( "I've fallen and can't get up", "my_textdomain" ) );
	}
	/**
	 * License Remote Call Update Request
	 *
	 * @access public
	 * @return array
	 */	
	
public function update_request( $params = array() )
	{
		
		$url 		=  self::$licenseUrl .'?'. http_build_query( $params ) ;
		$request 	= wp_remote_get( $url );
		
		if( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
		
			if(  $request[ 'response' ][ 'code' ] == 500 ) {
				//$errorMessage 				= "Can't Connect to License Server.";
				$errorMessage 				= "Could not establish a secure connection to LPB License Server.";
			} else {
				$errorMessage 				= $request->get_errorMessage();
				//$errorMessage 				= $request[ 'response' ][ 'message' ];
			}

			self::$noticeId 			= "error";
			self::$noticeMessage	 	= sprintf( __( "Landing Page Booster Error: %s" ), $errorMessage );
			
			add_action( 'admin_notices', array( $this , 'admin_notice' ) );		

		}else{
		
			$response = wp_remote_retrieve_body( $request );
			$response = unserialize( $response );

			
			if( ! empty ( $response->errors ) ){
				
				update_option( self::$deactivateCheckboxKey, 'off' );
			
				if ( isset( $response->errors['no_key'] ) && $response->errors['no_key'] == 'no_key' && isset( $response->errors['no_subscription'] ) && $response->errors['no_subscription'] == 'no_subscription' ) {
		
					 $error_key = 'no_key_error_notice';
					 $error_msg = $this->get_updates_errorMessages( $error_key );
					 
					 $error_key = 'no_subscription_error_notice';
					 $error_msg = $this->get_updates_errorMessages( $error_key );

				} else if ( isset( $response->errors['exp_license'] ) && $response->errors['exp_license'] == 'exp_license' ) {

					 $error_key = 'expired_license_error_notice';
					 $error_msg = $this->get_updates_errorMessages( $error_key );

				}  else if ( isset( $response->errors['hold_subscription'] ) && $response->errors['hold_subscription'] == 'hold_subscription' ) {
					
					$error_key = 'on_hold_subscription_error_notice';
					$error_msg = $this->get_updates_errorMessages( $error_key );

				} else if ( isset( $response->errors['cancelled_subscription'] ) && $response->errors['cancelled_subscription'] == 'cancelled_subscription' ) {
	
					$error_key = 'cancelled_subscription_error_notice';
					$error_msg = $this->get_updates_errorMessages( $error_key );

				} else if ( isset( $response->errors['exp_subscription'] ) && $response->errors['exp_subscription'] == 'exp_subscription' ) {

					$error_key = 'expired_subscription_error_notice';
					$error_msg = $this->get_updates_errorMessages( $error_key );

				} else if ( isset( $response->errors['suspended_subscription'] ) && $response->errors['suspended_subscription'] == 'suspended_subscription' ) {
					
					$error_key = 'suspended_subscription_error_notice';
					$error_msg = $this->get_updates_errorMessages( $error_key );

				} else if ( isset( $response->errors['pending_subscription'] ) && $response->errors['pending_subscription'] == 'pending_subscription' ) {

					$error_key = 'pending_subscription_error_notice';
					$error_msg = $this->get_updates_errorMessages( $error_key );

				} else if ( isset( $response->errors['trash_subscription'] ) && $response->errors['trash_subscription'] == 'trash_subscription' ) {

					$error_key = 'trash_subscription_error_notice';
					$error_msg = $this->get_updates_errorMessages( $error_key );

				} else if ( isset( $response->errors['no_subscription'] ) && $response->errors['no_subscription'] == 'no_subscription' ) {

					$error_key = 'no_subscription_error_notice';
					$error_msg = $this->get_updates_errorMessages( $error_key );

				} else if ( isset( $response->errors['no_activation'] ) && $response->errors['no_activation'] == 'no_activation' ) {

					$error_key = 'no_activation_error_notice';
					$error_msg = $this->get_updates_errorMessages( $error_key );

				} else if ( isset( $response->errors['no_key'] ) && $response->errors['no_key'] == 'no_key' ) {

					$error_key = 'no_key_error_notice';
					$error_msg = $this->get_updates_errorMessages( $error_key );

				} else if ( isset( $response->errors['download_revoked'] ) && $response->errors['download_revoked'] == 'download_revoked' ) {

					$error_key = 'download_revoked_error_notice';
					$error_msg = $this->get_updates_errorMessages( $error_key );

				} else if ( isset( $response->errors['switched_subscription'] ) && $response->errors['switched_subscription'] == 'switched_subscription' ) {

					$error_key = 'switched_subscription_error_notice';
					$error_msg = $this->get_updates_errorMessages( $error_key );

				}
				
				self::$noticeId 			= "error";
				self::$noticeMessage	 	= __( $error_msg );
				add_action( 'admin_notices', array( $this , 'admin_notice' ) );					
				
			} else {
				
				update_option( self::$timetocheck_key, strtotime("+30 days") );	

			}
		
		}
	return $response;
		
	}
	
	/**
	 * Disyplay Inactive Notice
	 *
	 * @access public
	 * @return string
	 */	
	function get_oldversion($str,$char)
	{
		$sub = substr($str, strpos($str,$char)+strlen($char),strlen($str));
		return preg_replace("/\([^)]+\)/","", substr($sub,0,strpos($sub,$char)));
	}
	function admin_script_modal()
	{
	?>
	<script>
			$(".modal-content").draggable({
				  handle: ".modal-header"
			});
			$("#alertBox").draggable({
				  handle: "#alertBox h1" 
			});
	</script>
	<?php
	}
	function init_link()
	{
		wp_enqueue_style ( 'ns_bootstrap');
		wp_register_style( 'ns_update',  plugins_url().'/landing-page-booster/assets/lpb-update.css', false,   '1.0.0' );
		wp_enqueue_style ( 'ns_update' );
		wp_register_style( 'ns_roolback',  plugins_url().'/landing-page-booster/assets/revert-version.css', false,  '1.0.0');
		wp_enqueue_style ( 'ns_roolback' );
		wp_enqueue_script( 'ns_js_maxcdn_bootstrapcdn', 'http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js', array(),'3.3.6');
		wp_enqueue_script( 'ns_jquery_1_11_2', '//code.jquery.com/ui/1.11.2/jquery-ui.js', array(),'1.11.2');
		wp_enqueue_script( 'ns_roolback_js', plugins_url().'/landing-page-booster/assets/Custom-lpbjquery.js', array(),'1.0.0');
	}
	
	function rollback_plugin_page()
	{
		?>
		<script src="//code.jquery.com/jquery-1.10.2.js"></script>
		<script>
		$( document ).ready(function() 
		{
			$("tr[data-slug='landing-page-booster'] .plugin-title .row-actions").append( '| <span class="rollback"><a style="cursor: pointer;" onclick="showdialog()" data-toggle="modal" data-target="#myModal" class="rollback" aria-label="Edit Landing Page Booster">Rollback</a></span>' );
		});	
		</script>
		<?php
	}
	function skip_function()
	{
		?>
			<style>
				.updated-slide{ 
					max-height: 0; 
					overflow: hidden;
					padding: 0!important;
					transition-property: all;
					transition-duration: 0.5s;
					transition-timing-function: cubic-bezier(0, 1, 0.5, 1);
			}
			</style>
			<script>
			function myFunction(clicked_id) { 
				var now = new Date();
				var time = now.getTime();
				time += 3600 * 1000;
				now.setTime(time);
				 var expires = "; expires="+now.toGMTString();
				 document.cookie = "version_cookie=<?php echo $this->new_version_cookies; ?>"+expires;
				 $('#'+clicked_id).parent().parent().addClass("updated-slide");
			}
			</script>
		<?php
	}
	
}
endif;

function KRLM() {
	return KRLicenseManager::instance();
}
 if (is_admin())
        {
KRLM();
		}
?>
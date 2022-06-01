<?php
/**
 * License Setup and Activation 
 *
 * @author 		Netseek
 * @category 	Admin
 * @package 	LandingPageBooster/Admin/View
 * @version     2.4.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$path = plugin_basename( __FILE__ );
$path = explode("/",$path);
$folder_name = $path[0];
if( !defined('LPB_FOLDER_NAME') ) {define('LPB_FOLDER_NAME', $folder_name); }


kr_settings_page();

function kr_admin_tabs( $current = 'license' ) { 
    $tabs = array( 'license' => 'License'); 
    $links = array();
    echo '<div id="icon-themes" class="icon32"><br></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='?page=krSetup&tab=$tab'>$name</a>";
        
    } 
    echo '</h2>';
}
function set_selected($desired_value, $new_value)
{
    if($desired_value==$new_value)
    {
        echo ' selected="selected"';
    }
}

/**
 * Returns current plugin version.
 * 
 * @return string Plugin version
 */
function plugin_get_version() {
	if ( ! function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	
	$plugin_data = get_plugin_data(ABSPATH . 'wp-content/plugins/'.LPB_FOLDER_NAME.'/app.php' );
	$plugin_version = $plugin_data['Version'];
	return $plugin_version;   
}

function kr_settings_page() {
	if(isset($_POST["slm_action"])){
		kr_admin_activate_deactivate_license();
	}
	?>
	<div class="wrap">
		<span style="float: right;"><?php _e( "Landing Page Booster ".plugin_get_version(), 'netseek' );//show current version ?></span>
		<h2 style="font-size: 23px;font-weight: 400;margin-bottom: 15px;">Landing Page Booster</h2>
	<?php
	$get_current_user_id = get_current_user_id();
    $kr_show_button = 'Deactivate';
    
    $message = LPBLicenseManager::$error_message;
	
	$lpb_license_key = get_transient( 'lpb_license_key' );
    $kr_license_key = get_option( 'kr_license_key', '' );

    /* Check if license is Active */
    if( $kr_license_key == '' ){
        $lpb_license_key = '';
        $kr_show_button = 'Activate';
    }
	
	if(  false === ( $get_license_info = get_transient( LPBLicenseManager::generate_transient_name() ) ) || (get_option('kr_license_key', '') == '' && false === get_transient( 'lpb_license_info' ) ) ){
        echo '<div class="card">
            <p><strong>Welcome to Landing Page Booster Plugin.</strong> To get started , Please provide the license key below so we can verify the legitimacy of this copy. You will only need to do this once.</p>
        </div>';
    }else
	{
		if( is_array( $get_license_info ) && !empty( $get_license_info ) ){
            if( $get_license_info['status'] == 'expired' || $get_license_info['status'] == 'blocked' ){
                $kr_show_button = 'Deactivate';
                $lpb_license_key = $get_license_info['license_key'];
            }
			 ?>
            <table class="wp-list-table widefat striped importers ndf_license_info">
                <tr>
                    <td class="import-system"><strong>License Status</strong></td>
                    <td><strong><?php echo ucfirst( $get_license_info['status'] ); ?><strong></td>
                </tr>
                <tr>
                    <td><strong>License Key</strong></td>
                    <td><strong><?php echo $lpb_license_key; ?></strong></td>
                </tr>
                <tr>
                    <td>Registered Domain</td>
                    <td><?php echo $get_license_info['installation_url']; ?></td>
                </tr>
                <?php if( $get_license_info['date_expiry'] != '0000-00-00' ){ ?>
                <tr>
                    <td class="import-system">Date Expiry</td>
                    <td><?php echo date( 'F j, Y', strtotime( $get_license_info['date_expiry'] ) ); ?></td>
                </tr>
                <?php } ?>
            </table>
            <?php
        }
        else{
            $message = LPBLicenseManager::$error_message;
        }
    }

	?>
		<div class="card">
			<form action="<?php admin_url( 'admin.php?page=krSetup' ); ?>" method="post">
				<?php wp_nonce_field( '_kr_license_nonce', 'kr_license_nonce' ); ?>
				<input type="hidden" name="action" value="kr_activate_deactivate_license">
				<table class="form-table">
					<tr>
						<th style="width:100px;">License Key</th>
						<td ><input class="regular-text" type="text" id="kr_license_key" name="kr_license_key"  value="<?php echo esc_attr__( $lpb_license_key ); ?>" ></td>
					</tr>
				</table>
				<p class="submit">
					<input type="submit" name="slm_action" value="<?php echo $kr_show_button; ?>" class="button-primary"  />
				</p>
			</form>
		</div>
	</div>
    <?php
	
}

/**
 * Plugin license activate and deactivate hook.
 * 
 * @since 1.1.2.0
 */
function kr_admin_activate_deactivate_license() {
	if ( ! isset( $_POST['kr_license_nonce'] ) || ! wp_verify_nonce( $_POST['kr_license_nonce'], '_kr_license_nonce' ) ) wp_redirect( admin_url( 'admin.php?page=krSetup&error' ) );

	$license_key = $_POST['kr_license_key'];
    $slm_action = ( $_POST['slm_action'] == 'Deactivate' ) ? 'slm_deactivate' : 'slm_activate';
	$get_current_user_id = get_current_user_id();

	$message = LPBLicenseManager::$error_message;
	$type = 'notice notice-error is-dismissible';
	
	/**
	 * LICENSE CHECK BEFORE ACTIVATION 
	 * 
	 * Compare license date_expiry and current date.
	 */
	$get_license_info = LPBLicenseManager::check_license_info( $license_key );
	update_option("_lpb_license_info",$get_license_info);
	if( $get_license_info['status'] == 'success' ){
		$check_license_data = $get_license_info['output'];
		if( !empty( $check_license_data ) ){
			if($check_license_data->result == 'success'){
				
				if( strtotime( $check_license_data->date_expiry ) >= time() || $slm_action == 'slm_deactivate' ){
					$do_license_action = LPBLicenseManager::license_action( $slm_action, $license_key );

					if( $do_license_action['status'] == 'success' ){
						$message = $do_license_action['output'];
    					$type = 'notice notice-success is-dismissible';
    				}
    				else{
    					$message = $do_license_action['output'];
    				}
				}
				else{
					$message = '<p>Your license key has expired</p>';
				}
				
			}
			else if($check_license_data->result == 'error'){
				$message = '<p>'.$check_license_data->message.'</p>';
			}
		}
	}
	else{
    	$message = '<p>'.$get_license_info['output'].'</p>';
	}

	/* EO LICENSE CHECK BEFORE ACTIVATION */

    update_option( 'kr_license_notices_'.$get_current_user_id, array( $type, $message ) );
	wp_redirect( admin_url( 'admin.php?page=krSetup' ) );
}

?>

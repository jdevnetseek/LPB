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
	$plugin_data = get_plugin_data(ABSPATH . 'wp-content/plugins/landing-page-booster/app.php' );
	$plugin_version = $plugin_data['Version'];
	return $plugin_version;   
}

function kr_settings_page() {
	global $pagenow;
	global $sql;
	global $core;
	global $utilities;
	global $wpdb;
	global $KRLicenseManager;   
	?>
	
	<div class="wrap">
		<span style="float: right;"><?php _e( "Landing Page Booster ".plugin_get_version(), 'netseek' );//show current version ?></span>
		<h2>Landing Page Booster Setup</h2>
		<?php
			$count_Page 	= $sql->count_Pagedefault_active();
			$license = $sql->GetLicenseKeyCounter();
			$status = md5("inactive");
		 	$activate 		= ( !empty($_POST['Activate']) ? $_POST['Activate'] : FALSE  );
			$deactivate 	= ( !empty($_POST['Deactivate']) ? $_POST['Deactivate'] : FALSE  );
			$update			= ( !empty($_POST['Update']) ? $_POST['Update'] : FALSE  );
			$email 			= ( !empty( $_POST[ 'email' ] ) ? $_POST[ 'email' ] : '' );
			$licence_key 	= ( !empty( $_POST[ 'licence_key' ] ) ? $_POST[ 'licence_key' ] : ''  );
			$software_title = ( !empty( $_POST[ 'software_title' ] ) ? $_POST[ 'software_title' ] : '' ) ;
			if( $activate ){
				
				$response = KRLM()->activation( $email , $licence_key , $software_title );
				if( ! empty( $response[ 'message' ] ) && ! empty( $response[ 'activation_extra' ] )){
					echo  sprintf( '<div class="%s"><p>' . __( '%s' ) . '</p></div>', 'updated' , $response[ 'message' ]  ); 
						$get_limit_license = $core->get_limitby_name($software_title);
					if($get_limit_license['pages'] < $count_Page[0]->Tags)
					{
						$sql = "UPDATE {$wpdb->prefix}ns_tags SET status =  '{$status}'";
						$wpdb->query($sql);
						echo  sprintf( '<div class="%s"><p>' . __( '%s' ) . '</p></div>', 'error' ,"Cleared all landing page defaults" );
					}
				} elseif ( ! empty ( $response[ 'errorMessage' ] ) ){
					echo  sprintf( '<div class="%s"><p>' . __( '%s' ) . '</p></div>', 'error' ,$response[ 'errorMessage' ]   ) ;
				}
				else
				{
					echo  sprintf( '<div class="%s"><p>' . __( '%s' ) . '</p></div>', 'error' ,"Entered API key and email") ;
				}
			
			} elseif( $deactivate ) {
				$response = KRLM()->deactivation( $email , $licence_key , $software_title );
				
				if( ! empty( $response[ 'message' ] ) ){
					echo  sprintf( '<div class="%s"><p>' . __( '%s' ) . '</p></div>', 'error' , $response[ 'message' ]  ); 
				} elseif ( ! empty ( $response[ 'error_message' ] ) ){
					echo  sprintf( '<div class="%s"><p>' . __( '%s'  ) . '</p></div>', 'error' ,$response[ 'error_message' ]   ) ;
				} 
			}

			$data 				= KRLM()->get_license_info();
			
			$product_id			= ( ! empty( $data[ 'product_id' ] ) ? $data[ 'product_id' ] : '' );
			$license 			= ( ! empty( $data[ 'license' ] ) ? $data[ 'license' ] : '' );
			$email_2				= ( ! empty( $data[ 'email' ] ) ? $data[ 'email' ] : '' ); 
			$deactivate_checkbox_2  = ( ! empty( $data[ 'deactivate_checkbox' ]) ? $data[ 'deactivate_checkbox' ] : ''  );
			
			$software_title_txt = ( ! empty( $software_title ) ? $software_title : $product_id );
			$licence_key_txt 	= ( ! empty( $licence_key ) ? $licence_key : $license );
			$email_txt 			= ( ! empty( $email ) ? $email : $email_2 );
			$deactivate_checkbox = ( $deactivate_checkbox_2 == 'on' ?  'on' :  'off' );
		 
		 	       
			if ( isset ( $_GET['tab'] ) ) kr_admin_tabs($_GET['tab']); else kr_admin_tabs('license');
		?>

		<div id="poststuff">
			<form method="post" action="<?php admin_url( 'admin.php?page=krSetup' ); ?>">
				<?php
				wp_nonce_field( "kr-settings-page" ); 
				
				if ( $pagenow == 'admin.php' && $_GET['page'] == 'krSetup' ){ 
				
					if ( isset ( $_GET['tab'] ) ) $tab = $_GET['tab']; 
					else $tab = 'license'; 
					
					echo '<table class="form-table">';
					switch ( $tab ){
						case 'support' :
							?>
                                                        <tr>
								<th><label for="kr_tag_class">Phone (Australia)</label></th>
								<td><span class="description">1800 180 183</span></td>
							</tr>
							<tr>
								<th><label for="kr_tag_class">Phone (International)</label></th>
								<td><span class="description">+61 2 9209 4055</span></td>
							</tr>
                                                        <tr>
								<th><label for="kr_tag_class">Fax</label></th>
								<td><span class="description">1800 180 184</span></td>
							</tr>
							<tr>
								<th><label for="kr_tag_class">Email:</label></th>
								<td><span class="description"><a href="http://www.landingpagebooster.com/support" target="_blank">Contact Us</a></span></td>
							</tr>
							<?php
						break; 
						case 'license' : 
							?>
							<tr>
                    <td width="150" align="left" class="key">Software Title:</td>
                    <td>
                       	<?php $selected_value = $software_title_txt; ?>
						<select name="software_title" class="text_area" style="width:500px!important" >
								  <option value="LandingPageBooster-Silver" <?php set_selected('LandingPageBooster-Silver', $selected_value); ?>>	 LandingPageBooster-Silver	</option>
								  <option value="LandingPageBooster-Gold" <?php set_selected('LandingPageBooster-Gold', $selected_value); ?> >		 LandingPageBooster-Gold	</option>
								  <option value="LandingPageBooster-Platinum" <?php set_selected('LandingPageBooster-Platinum', $selected_value); ?>>LandingPageBooster-Platinum</option>
							</select>
                    </td>
                  </tr>
                   <tr>
                    <td width="150" align="left" class="key">API License Key:</td>
                    <td>
                        <input type="text" name="licence_key"  class="text_area"  style="width:500px" value="<?=$licence_key_txt;?>" /> 
                       
                    </td>
                  </tr>
                  <tr>
                    <td width="150" align="left" class="key">API License Email:</td>
                    <td>
                        <input type="text" name="email"  class="text_area"  style="width:500px" value="<?=$email_txt;?>" /> 
                    </td>
                  </tr>
                   <tr>
                    <td width="150" align="right" class="key"></td>
                    <td>
                        
                        <?php
                        if ( $deactivate_checkbox == 'on' ){
                        ?>
                        	<input type="submit" value="Deactivate" class="button-primary" name="Deactivate">
                        <?php
                        }else{
                        ?>
							<input type="submit" value="Activate" class="button-primary" name="Activate">
                        <?php
                        }
                        ?>

                    </td>
                  </tr>
							<?php
						break;
					}
					echo '</table>';
				}
				
				?>
				
			</form>
		</div>

	</div>
<?php
}


?>

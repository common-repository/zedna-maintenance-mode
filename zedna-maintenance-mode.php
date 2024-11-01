<?php
/*
  Plugin Name: Zedna Maintenance Mode
  Plugin URI: https://profiles.wordpress.org/zedna#content-plugins
  Text Domain: zedna-maintenance-mode
  Domain Path: /languages
  Description: Set you website under maintenance to lock content while development.
  Version: 1.0
  Author: Radek Mezulanik
  Author URI: https://www.mezulanik.cz
  License: GPL3
*/

// CREATE Zedna Maintenance Mode options
//Set options
add_option( 'zednamm_maintenance_role', 'none',  '', 'yes' );
add_option( 'zednamm_redirect_to_login', 'yes',  '', 'yes' );
add_option( 'zednamm_message', '<div><div style="text-align: center;"><strong>'.__('Website is under maintenance','zedna-maintenance-mode').'</strong></div><div style="text-align: center;">We are currently making updates. Everything will be online shortly.</div></div>',  '', 'yes' );


// #CREATE Zedna Maintenance Mode options
add_action( 'plugins_loaded', 'zednamm_load_textdomain' );
/**
 * Load plugin textdomain.
 *
 * @since 1.0
 */
function zednamm_load_textdomain() {
  load_plugin_textdomain( 'zedna-maintenance-mode', false, basename( dirname( __FILE__ ) ) . '/languages' ); 
}

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'zednamm_links' );

function zednamm_links( $links ) {
  $links[] = '<a href="/wp-admin/admin.php?page=zednamm">Settings</a>';
   $links[] = '<a href="https://profiles.wordpress.org/zedna/#content-plugins" target="_blank">More plugins by Radek Mezulanik</a>';
   return $links;
}

function ismyrole($zednamm_maintenance_role,$current_user){
   //Administrator
  if($zednamm_maintenance_role === 'administrator') {
    return true;
  }else
  //Editor
  if($zednamm_maintenance_role === 'editor' && ($current_user->level_8 === 0 ||  !is_user_logged_in())) {
    return true;
  }else
  //Author
  if($zednamm_maintenance_role === 'author' && ($current_user->level_3 === 0 ||  !is_user_logged_in())) {
    return true;
  }else
  //Contributor
  if($zednamm_maintenance_role === 'contributor' && ($current_user->level_2 === 0 ||  !is_user_logged_in())) {
    return true;
  }else
  //Subscriber
  if($zednamm_maintenance_role === 'subscriber' && ($current_user->level_1 === 0 ||  !is_user_logged_in())) {
    return true;
  }else
  //Anonym
  if($zednamm_maintenance_role === 'anonym' && !is_user_logged_in()) {
    return true;
  }else{
    return false;
  }
}

function zednamm_maintenace_mode() {
  /* Do the stuff */
  global $current_user;
  $zednamm_maintenance_role = get_option('zednamm_maintenance_role');
  $zednamm_redirect_to_login = get_option('zednamm_redirect_to_login');
  $zednamm_message = wp_kses_post(get_option('zednamm_message'));
  get_currentuserinfo();

  if(ismyrole($zednamm_maintenance_role, $current_user)) {
    if($zednamm_redirect_to_login === 'yes') {
    wp_redirect( wp_login_url( get_permalink()) );
    exit;
  }else{
    wp_die('<div id="message">'.$zednamm_message.'</div>');
  }  
}
}
add_action('wp_head', 'zednamm_maintenace_mode');

//Add admin page
add_action('admin_menu', 'zednamm_setttings_menu');

if( !defined('ABSPATH') ) die('-1');

function zednamm_setttings_menu(){        
    add_menu_page( __('Zedna Maintenance Mode Settings page','zedna-maintenance-mode'), __('Maintenance Mode','zedna-maintenance-mode'), 'manage_options', 'zednamm', 'zednamm_init','dashicons-hammer'  );
  // Call update_zednamm function to update database
  add_action( 'admin_init', 'update_zednamm' );
}

// Create function to register plugin settings in the database
if( !function_exists("update_zednamm") )
{
function update_zednamm() {
  register_setting( 'zednamm-settings', 'zednamm_maintenance_role' );
  register_setting( 'zednamm-settings', 'zednamm_redirect_to_login' );
  register_setting( 'zednamm-settings', 'zednamm_message' );
}
}
function zednamm_init(){
  $zednamm_maintenance_role = (get_option('zednamm_maintenance_role') !== '') ? get_option('zednamm_maintenance_role') : 'none';
  $zednamm_redirect_to_login = (get_option('zednamm_redirect_to_login') !== '') ? get_option('zednamm_redirect_to_login') : 'yes';
  $zednamm_message = (get_option('zednamm_message') !== '') ? get_option('zednamm_message') : '<div><div style="text-align: center;">'.__('<strong>Website is under maintenance</strong></div><div style="text-align: center;">We are currently making updates. Everything will be online shortly.','zedna-maintenance-mode').'</div></div>';
?>
<h1><?php print __('Zedna Maintenance Mode Settings','zedna-maintenance-mode');?></h1>
<div class="wrap">
  <form method="post" action="options.php">
    <?php
    settings_fields('zednamm-settings'); ?>
    <?php
    do_settings_sections('zednamm-settings'); ?>
    <style>
    .form-table{
      background-color: #fff;
      padding: 16px;
      max-width: 96%;
    }
    .row{
      padding: 16px 0;
    }

    .row.first{
      padding: 0;
    }
  </style>
    <div class="form-table">
      <div valign="top">
      <div class="row first"><h4><?php print __('Set maintenance mode for users with minimum role:','zedna-maintenance-mode');?></h4></th>
      <div>
    <?php
    $zednamm_maintenance_role = get_option('zednamm_maintenance_role');
    ?>
        <p>
          <label>
            <input type="radio" name="zednamm_maintenance_role" value="administrator" <?php if ($zednamm_maintenance_role == 'administrator') {echo ' checked ';}?> />
            <?php print __('Administrator','zedna-maintenance-mode');?>
          </label>
        </p>
        <p>
          <label>
            <input type="radio" name="zednamm_maintenance_role" value="editor" <?php if ($zednamm_maintenance_role == 'editor') {echo ' checked ';}?> />
            <?php print __('Editor','zedna-maintenance-mode');?>
          </label>
        </p>
        <p>
          <label>
            <input type="radio" name="zednamm_maintenance_role" value="author" <?php if ($zednamm_maintenance_role == 'author') {echo ' checked ';}?> />
            <?php print __('Author','zedna-maintenance-mode');?>
          </label>
        </p>
        <p>
          <label>
            <input type="radio" name="zednamm_maintenance_role" value="contributor" <?php if ($zednamm_maintenance_role == 'contributor') {echo ' checked ';}?> />
            <?php print __('Contributor','zedna-maintenance-mode');?>
          </label>
        </p>
        <p>
          <label>
            <input type="radio" name="zednamm_maintenance_role" value="subscriber" <?php if ($zednamm_maintenance_role == 'subscriber') {echo ' checked ';}?> />
            <?php print __('Subscriber','zedna-maintenance-mode');?>
          </label>
        </p>
        <p>
          <label>
            <input type="radio" name="zednamm_maintenance_role" value="anonym" <?php if ($zednamm_maintenance_role == 'anonym') {echo ' checked ';}?> />
            <?php print __('Anonym user','zedna-maintenance-mode');?>
          </label>
        </p>
        <p>
          <label>
            <input type="radio" name="zednamm_maintenance_role" value="none" <?php if ($zednamm_maintenance_role == 'none') {echo ' checked ';}?> />
            <?php print __('Turn off maintenance mode','zedna-maintenance-mode');?>
          </label>
        </p>
      </div>
      </div>

      <div class="row"><strong><?php print __('Redirect to login page','zedna-maintenance-mode');?></strong>
      
      <?php
        $zednamm_redirect_to_login = get_option('zednamm_redirect_to_login');
      ?>
          <label><input type="checkbox" name="zednamm_redirect_to_login" value="yes" <?php if ($zednamm_redirect_to_login == 'yes') {echo ' checked ';}?>/></label>
      </div>
      
      <div class="row"><h4><?php print __('Maintenance message','zedna-maintenance-mode');?></h4></th>
      <div>
      <?php
          $zednamm_message = wp_kses_post(get_option('zednamm_message'));
          wp_editor( $zednamm_message, 'zednamm_message', $settings = array('textarea_rows'=> '10') );
       ?>
      </div>
      </div>

    </div>
  <?php
    submit_button(); ?>
  </form>
</div>
<p><?php print __('If you like this plugin, please donate us for faster upgrade','zedna-maintenance-mode');?></p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
  <input type="hidden" name="cmd" value="_s-xclick">
  <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----MIIHFgYJKoZIhvcNAQcEoIIHBzCCBwMCAQExggEwMIIBLAIBADCBlDCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20CAQAwDQYJKoZIhvcNAQEBBQAEgYB56P87cZMdKzBi2mkqdbht9KNbilT7gmwT65ApXS9c09b+3be6rWTR0wLQkjTj2sA/U0+RHt1hbKrzQyh8qerhXrjEYPSNaxCd66hf5tHDW7YEM9LoBlRY7F6FndBmEGrvTY3VaIYcgJJdW3CBazB5KovCerW3a8tM5M++D+z3IDELMAkGBSsOAwIaBQAwgZMGCSqGSIb3DQEHATAUBggqhkiG9w0DBwQIqDGeWR22ugGAcK7j/Jx1Rt4pHaAu/sGvmTBAcCzEIRpccuUv9F9FamflsNU+hc+DA1XfCFNop2bKj7oSyq57oobqCBa2Mfe8QS4vzqvkS90z06wgvX9R3xrBL1owh9GNJ2F2NZSpWKdasePrqVbVvilcRY1MCJC5WDugggOHMIIDgzCCAuygAwIBAgIBADANBgkqhkiG9w0BAQUFADCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20wHhcNMDQwMjEzMTAxMzE1WhcNMzUwMjEzMTAxMzE1WjCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20wgZ8wDQYJKoZIhvcNAQEBBQADgY0AMIGJAoGBAMFHTt38RMxLXJyO2SmS+Ndl72T7oKJ4u4uw+6awntALWh03PewmIJuzbALScsTS4sZoS1fKciBGoh11gIfHzylvkdNe/hJl66/RGqrj5rFb08sAABNTzDTiqqNpJeBsYs/c2aiGozptX2RlnBktH+SUNpAajW724Nv2Wvhif6sFAgMBAAGjge4wgeswHQYDVR0OBBYEFJaffLvGbxe9WT9S1wob7BDWZJRrMIG7BgNVHSMEgbMwgbCAFJaffLvGbxe9WT9S1wob7BDWZJRroYGUpIGRMIGOMQswCQYDVQQGEwJVUzELMAkGA1UECBMCQ0ExFjAUBgNVBAcTDU1vdW50YWluIFZpZXcxFDASBgNVBAoTC1BheVBhbCBJbmMuMRMwEQYDVQQLFApsaXZlX2NlcnRzMREwDwYDVQQDFAhsaXZlX2FwaTEcMBoGCSqGSIb3DQEJARYNcmVAcGF5cGFsLmNvbYIBADAMBgNVHRMEBTADAQH/MA0GCSqGSIb3DQEBBQUAA4GBAIFfOlaagFrl71+jq6OKidbWFSE+Q4FqROvdgIONth+8kSK//Y/4ihuE4Ymvzn5ceE3S/iBSQQMjyvb+s2TWbQYDwcp129OPIbD9epdr4tJOUNiSojw7BHwYRiPh58S1xGlFgHFXwrEBb3dgNbMUa+u4qectsMAXpVHnD9wIyfmHMYIBmjCCAZYCAQEwgZQwgY4xCzAJBgNVBAYTAlVTMQswCQYDVQQIEwJDQTEWMBQGA1UEBxMNTW91bnRhaW4gVmlldzEUMBIGA1UEChMLUGF5UGFsIEluYy4xEzARBgNVBAsUCmxpdmVfY2VydHMxETAPBgNVBAMUCGxpdmVfYXBpMRwwGgYJKoZIhvcNAQkBFg1yZUBwYXlwYWwuY29tAgEAMAkGBSsOAwIaBQCgXTAYBgkqhkiG9w0BCQMxCwYJKoZIhvcNAQcBMBwGCSqGSIb3DQEJBTEPFw0xNTA2MjUwOTM4MzRaMCMGCSqGSIb3DQEJBDEWBBQe9dPBX6N8C2F2EM/EL1DwxogERjANBgkqhkiG9w0BAQEFAASBgAz8dCLxa+lcdtuZqSdM+s0JJBgLgFxP4aZ70LkZbZU3qsh2aNk4bkDqY9dN9STBNTh2n7Q3MOIRugUeuI5xAUllliWO7r2i9T5jEjBlrA8k8Lz+/6nOuvd2w8nMCnkKpqcWbF66IkQmQQoxhdDfvmOVT/0QoaGrDCQJcBmRFENX-----END PKCS7-----
">
  <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit"
    alt="PayPal - The safer, easier way to pay online!">
  <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
<?php
}
?>

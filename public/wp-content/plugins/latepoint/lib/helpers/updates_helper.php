<?php 

class OsUpdatesHelper {


  public static function update_latepoint(){
  	return;
    $url = 'https://s3.amazonaws.com/latepoint/wp-addons/latepoint-addon.zip';
    $plugin_path = 'latepoint-addon/latepoint-addon.php';
    $version = '1.0.0';

    include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
    $upgrader = new Plugin_Upgrader();
    $result   = $upgrader->upgrade( $url );
    $result   = activate_plugin( $plugin_path );
    if ( is_wp_error( $result ) ) {
        // Process Error
    }
  }

}
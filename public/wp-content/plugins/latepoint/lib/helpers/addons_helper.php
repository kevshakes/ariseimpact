<?php 

class OsAddonsHelper {

  public static function get_addon_download_info($addon_name){
    if(empty($addon_name)) return false;

    $license = OsLicenseHelper::get_license_info();

    $post = array(
      '_nonce'        => wp_create_nonce('addon_download'),
      'license_key'   => $license['license_key'], 
      'domain'        => $_SERVER['SERVER_NAME'],
      'user_ip'       => OsUtilHelper::get_user_ip(),
      'addon_name'    => $addon_name,
    );


    $request = wp_remote_post( LATEPOINT_SERVER."/wp/addons/get-download-info", array('body' => $post, 'sslverify ' => false));

    if( !is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200){
      $response = json_decode($request['body'], true);
      $url = $response['addon_info']['url'];
      $plugin_path = $response['addon_info']['plugin_path'];
      $version = $response['addon_info']['version'];
      return ['url' => $url, 'plugin_path' => $plugin_path, 'version' => $version];
    }else{
      return __('Connection Error. Please try again in a few minutes or contact us via email addons@latepoint.com. KLJSD734', 'latepoint');
    }

  }

  public static function get_addon_plugin_path($addon_path){
    if ( ! is_file( $addon_dir = WPMU_PLUGIN_DIR . '/'. $addon_path ) ) {
        if ( ! is_file( $addon_dir = WP_PLUGIN_DIR . '/'. $addon_path ) )
            $addon_dir = $addon_path;
    }
    return $addon_dir;
  }


  // addon_info['url', 'plugin_path', 'version']
  public static function install_addon($addon_info){
    if($addon_info['url'] && $addon_info['plugin_path']){
      include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
      include_once( LATEPOINT_ABSPATH . 'lib/helpers/plugin_upgrader.php' );
      $upgrader = new OsPluginUpgrader(new WP_Ajax_Upgrader_Skin());
      if(is_plugin_active($addon_info['plugin_path'])){
        // already installed, update if version is lower
        $installed_plugin_data = get_plugin_data(self::get_addon_plugin_path($addon_info['plugin_path']));
        if(version_compare($addon_info['version'], $installed_plugin_data['Version']) > 0){
          // updating
          $result = $upgrader->upgrade_from_url($addon_info['plugin_path'], $addon_info['url'] );
        }else{
          // already same version
          $result = true;
        }
      }else{
        // install
          $result = $upgrader->install( $addon_info['url'] );
          if ( !is_wp_error( $result ) ) {
            $result = activate_plugin( $addon_info['plugin_path'] );
            if ( !is_wp_error( $result ) ) $result = true;
          }
      }
      return $result;
    }else{
      return new WP_Error('invalid_addon', __('Error installing addon! Invalid info KFE73463', 'latepoint'));
    }

  }

}
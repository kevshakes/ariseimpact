<?php 

class OsSocialHelper {



  public static function get_google_user_info_by_token($token){
  	
      $url = "https://www.googleapis.com/oauth2/v3/tokeninfo?id_token={$token}";
      $ch = curl_init();
      $curlConfig = array(
          CURLOPT_URL            => $url,
          CURLOPT_RETURNTRANSFER => true,
      );
      curl_setopt_array($ch, $curlConfig);
      $result = curl_exec($ch);
      curl_close($ch);
      $userinfo = json_decode( $result, true );

    	$user = array();

      if($userinfo['sub']){
				$user['social_id'] = $userinfo['sub'];
      	$user['first_name'] = $userinfo['given_name'];
				$user['last_name'] = $userinfo['family_name'];
				$user['email'] = $userinfo['email'];
				$user['avatar_url'] = $userinfo['picture'];
      }else{
      	$user['error'] = $userinfo['error_description'];
      }

      return $user;

  }

  public static function get_facebook_user_info_by_token($token){

      $url = "https://graph.facebook.com/me?fields=id,email,last_name,first_name,picture.width(1000)&access_token={$token}";
      $ch = curl_init();
      $curlConfig = array(
          CURLOPT_URL            => $url,
          CURLOPT_RETURNTRANSFER => true,
      );
      curl_setopt_array($ch, $curlConfig);
      $result = curl_exec($ch);
      curl_close($ch);
      $userinfo = json_decode( $result, true );

    	$user = array();

      if($userinfo['id']){
				$user['social_id'] = $userinfo['id'];
      	$user['first_name'] = $userinfo['first_name'];
				$user['last_name'] = $userinfo['last_name'];
				$user['email'] = $userinfo['email'];
				$user['avatar_url'] = isset($userinfo['picture']) ? $userinfo['picture']['data']['url'] : '';
      }else{
      	$user['error'] = $userinfo['error_description'];
      }

      return $user;
  }


}
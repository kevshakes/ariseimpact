<?php 

class OsDebugHelper {
    public static function log_braintree_exception($e){
        $body = $e->getJsonBody();
        $err  = $body["error"];
        $return_array = [
            "status" =>  $e->getHttpStatus(),
            "type" =>  $err["type"],
            "code" =>  $err["code"],
            "param" =>  $err["param"],
            "message" =>  $err["message"],
        ];
        $error_msg = json_encode($return_array);
        error_log($error_msg);
    }

	public static function log_stripe_exception($e){
		$body = $e->getJsonBody();
        $err  = $body["error"];
        $return_array = [
            "status" =>  $e->getHttpStatus(),
            "type" =>  $err["type"],
            "code" =>  $err["code"],
            "param" =>  $err["param"],
            "message" =>  $err["message"],
        ];
        $error_msg = json_encode($return_array);
        error_log($error_msg);
	}


	public static function log($message){
		if(!OsSettingsHelper::is_env_dev()) return;
    		
        if (is_array($message) || is_object($message)) {
            error_log(print_r($message, true));
        } else {
            error_log($message);
        }
	}
}
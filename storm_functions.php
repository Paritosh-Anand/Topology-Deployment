<?php

include_once 'storm_log.php';

function send_curl($URL, $_USERNAME_, $_PASSWORD_, $_POSTFIELDS_, $_ACTION_) {
	
	PLog("URL = $URL \n USERNAME = $_USERNAME_ \n PASSWORD = $_PASSWORD_ \n ACTION = $_ACTION_");
	$ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        
	if($_USERNAME_ != "" && $_PASSWORD_ != "") {
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        	curl_setopt($ch, CURLOPT_USERPWD, "$_USERNAME_:$_PASSWORD_");
	}

	if($_ACTION_ == "execute_plan" || $_ACTION_ == "uplaod_model" || $_ACTION_ == "generate_plan" || $_ACTION_ == "kill_topology") {
		PLog("SET request as POST");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
	}

	if($_POSTFIELDS_ != "") {
		curl_setopt($ch, CURLOPT_POSTFIELDS, $_POSTFIELDS_);                                                                  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		
		if($_ACTION_ != "generate_plan") {
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
				'Content-Type: application/json',                                                                                
				'Content-Length: ' . strlen($_POSTFIELDS_))                                                                       
			);
		}
	}

        $result         = curl_exec($ch);
        $request_info   = curl_getinfo($ch);
	$result		= ($request_info["http_code"] >= 200 && $request_info["http_code"] < 400) ? $result : "Error(901)";
        curl_close($ch);

	return $result;
}

function glu_upload_model($model) {

	$json = json_encode($model);
	$_USERNAME_ = "release";
	$_PASSWORD_ = "release";
	$URL = "http://v-services-dev1:8080/console/rest/v1/storm/model/static";
	return  send_curl($URL, $_USERNAME_, $_PASSWORD_, $json, "uplaod_model");

}

function glu_genrate_plan($agent, $mountPoint, $planAction) {

	PLog("Agent = $agent and mountPoint = $mountPoint and planAction = $planAction");	

	$post = "planAction=$planAction&systemFilter=or{tags.hasAll('$mountPoint')};agent='$agent'";
	Plog("Generate Plan -- " . $post);
	$_USERNAME_ = "release";
	$_PASSWORD_ = "release";
	$URL = "http://v-services-dev1.nextagqa.com:8080/console/rest/v1/storm/plans";

	return  send_curl($URL, $_USERNAME_, $_PASSWORD_, $post, "generate_plan");

}

function glu_execute_plan($plan_id) {

	PLog("Plan ID - $plan_id");
	$_USERNAME_ = "release";
	$_PASSWORD_ = "release";

	$URL = "http://v-services-dev1.nextagqa.com:8080/console/rest/v1/storm/plan/$plan_id/execution";

	return send_curl($URL, $_USERNAME_, $_PASSWORD_, "", "execute_plan");
}

function glu_authenticate($user_name, $password, $console) {
	
	$login = false;
	$res = send_curl($console, $user_name, $password, '', 'login');
	$pos = strpos($res, 'Error 401 Unauthorized');
	
	if($pos > 0) {
		$login = false;
	} else {
		$login = true;
	}

	PLog("Login == $login");
	return $login;
}

?>

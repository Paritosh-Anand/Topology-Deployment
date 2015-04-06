<?php

	require 'storm_functions.php';

	$_ACTION_		= (isset($_REQUEST['action']) ? $_REQUEST['action'] : '');
	
	$resp 		= "";
	$retVal		= "";
	$_USERNAME_     = "";
	$_PASSWORD_     = "";
	
	if($_ACTION_ == 'get_agents' || $_ACTION_ == 'get_model') {
		$_USERNAME_     = "release";
		$_PASSWORD_     = "release";
	}

	$topologyName	= ($_ACTION_ == "get_topology_state" || $_ACTION_ == "kill_topology") ? $_REQUEST["topology_name"] : "";
	$agentNode	= ($_ACTION_ == "kill_topology") ? $_REQUEST["agentNode"] : "";
	$URL            = (isset($_REQUEST['url']) ? $_REQUEST['url'] : '');

	if($_ACTION_ != '' && $URL != '') {
		$resp = send_curl($URL, $_USERNAME_, $_PASSWORD_, "", $_ACTION_);
	}

	if($resp != "Error(901)") {
		if($_ACTION_ == 'get_agents') {
			$resp = json_decode($resp, true);
			foreach($resp as $k => $v) {
				$retVal .= $k . ",";
			}
		} else if($_ACTION_ == 'get_model') {
			$retVal = $resp;
		} else if ($_ACTION_ == 'get_topology_state') {
			PLog("get_topology_state = " . $resp);
			$retVal = $resp;
		} else if($_ACTION_ == 'kill_topology') {
			$retVal = $resp;
			PLog("KILL Topology -- " + $resp);

			// Execute stop phase in Glu
			$plan_create = glu_genrate_plan($agentNode, $topologyName,"stop");
			// Execute plan
			$execute_plan = glu_execute_plan($plan_create);
		}
	} else {
		$retVal = $resp;
	}
	
	print $retVal;

?>

<?php
    require 'global.php';
	include_once 'storm_functions.php';

	$result         = "Error - No Model in Request !!";
	$plan_create	= "Plan ID in NULL";
	$execute_plan	= "Deployment ID is NULL";
    $username       = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
    $logged_in_time = isset($_SESSION['logged_in_time']) ? $_SESSION['logged_in_time'] : '';
    $ip             = $_SERVER['REMOTE_ADDR'];

        $agentNode              = isset($_REQUEST['agentNode']) ? $_REQUEST['agentNode'] : '';
        $mountPoint             = isset($_REQUEST['mountPoint']) ? $_REQUEST['mountPoint'] : '';
        $dataCenter             = isset($_REQUEST['dataCenter']) ? $_REQUEST['dataCenter'] : 'QA';
        $jarName                = isset($_REQUEST['jarName']) ? $_REQUEST['jarName'] : '';
        $version                = isset($_REQUEST['version']) ? $_REQUEST['version'] : '';
        $topologyClassName      = isset($_REQUEST['className']) ? $_REQUEST['className'] : '';
        $topologyTable          = isset($_REQUEST['topologyTable']) ? $_REQUEST['topologyTable'] : '';
        $topologyEnvironment    = isset($_REQUEST['topologyEnvironment']) ? $_REQUEST['topologyEnvironment'] : '';
        $topologyMode           = isset($_REQUEST['topologyMode']) ? $_REQUEST['topologyMode'] : '';
        $topologyDataCenter     = isset($_REQUEST['topologyDataCenter']) ? $_REQUEST['topologyDataCenter'] : '';
        $topologyRegion         = isset($_REQUEST['topologyRegion']) ? $_REQUEST['topologyRegion'] : '';
        $param                  = isset($_REQUEST['param']) ? $_REQUEST['param'] : '';
        $zkUrls                 = isset($_REQUEST['zkUrls']) ? $_REQUEST['zkUrls'] : '';
        $zkPath                 = isset($_REQUEST['zkPath']) ? $_REQUEST['zkPath'] : '';
        $workers                = isset($_REQUEST['workers']) ? $_REQUEST['workers'] : '';
        $tags          	 	= isset($_REQUEST['tags']) ? $_REQUEST['tags'] : '';
        $topologyName          	= isset($_REQUEST['topologyName']) ? $_REQUEST['topologyName'] : '';
        $topologyType           = isset($_REQUEST['topologyType']) ? $_REQUEST['topologyType'] : '';

        $model                  = isset($_REQUEST['model']) ? $_REQUEST['model'] : '';
        $_MODEL_ 		= ($model != "") ? json_decode($model, true) : "";


        if($_MODEL_ != "" && $username != "") {
        	$match_found = 0;
                foreach($_MODEL_["entries"] as $index => $arr) {
                        $match_found = ($arr["agent"] == $agentNode && $arr["mountPoint"] == $mountPoint) ? 1 : 0;
                        if($match_found > 0) {
                                foreach($arr as $k => $v) {
                                        if ($k == "initParameters") {
                                                $_MODEL_["entries"][$index][$k]["jarName"]              = ($v["jarName"] != $jarName) ? $jarName : $v["jarName"];
                                                $_MODEL_["entries"][$index][$k]["version"]              = ($v["version"] != $version) ? $version : $v["version"];
                                                $_MODEL_["entries"][$index][$k]["topologyTable"]        = ($v["topologyTable"] != $topologyTable) ? $topologyTable : $v["topologyTable"];
                                                $_MODEL_["entries"][$index][$k]["topologyClassName"]    = ($v["topologyClassName"] != $topologyClassName) ? $topologyClassName : $v["topologyClassName"];
                                                $_MODEL_["entries"][$index][$k]["topologyEnvironment"]  = ($v["topologyEnvironment"] != $topologyEnvironment) ? $topologyEnvironment : $v["topologyEnvironment"];
                                                $_MODEL_["entries"][$index][$k]["topologyName"] 	= ($v["topologyName"] != $topologyName) ? $topologyName : $v["topologyName"];
                                                $_MODEL_["entries"][$index][$k]["topologyType"] 	= ($v["topologyType"] != $topologyType) ? $topologyType : $v["topologyType"];
                                                $_MODEL_["entries"][$index][$k]["zkUrls"]	 	= ($v["zkUrls"] != $zkUrls) ? $zkUrls : $v["zkUrls"];
                                                $_MODEL_["entries"][$index][$k]["zkPath"]	 	= ($v["zkPath"] != $zkPath) ? $zkPath : $v["zkPath"];
                                                $_MODEL_["entries"][$index][$k]["workers"]	 	= ($v["workers"] != $workers) ? $workers : $v["workers"];
                                                $_MODEL_["entries"][$index][$k]["topologyRegion"]	= ($v["topologyRegion"] != $topologyRegion) ? $topologyRegion : $v["topologyRegion"];
                                        } else if ($k == "metadata") {
                                                $_MODEL_["entries"][$index][$k]["version"]             	= ($v["version"] != $version) ? $version : $v["version"];
                                                $_MODEL_["entries"][$index][$k]["topologyEnvironment"]  = ($v["topologyEnvironment"] != $topologyEnvironment) ? $topologyEnvironment : $v["topologyEnvironment"];
					} else if ($k == "tags") {
						// Check if mountPoint value also exists in tags
						$tags_arr = explode(",", $tags);
						if(!in_array($mountPoint, $tags_arr)) {
							array_push($tags_arr, $mountPoint);
						}
						$_MODEL_["entries"][$index][$k]				= $tags_arr;
					}
                                }
				
				PLog("111===match found : " . $match_found);
				break;
                        }
                }
		if($match_found == 0) {
			 $NEW_ENTRY =
				 array (
						 'agent' => $agentNode,
						 'initParameters' => array (
							 'dataCenter' 		=> $dataCenter,
							 'jarName' 		=> $jarName,
							 'topologyType' 	=> $topologyType,
							 'releaseDir' 		=> "/cacheDir/releases",
							 'topologyClassName' 	=> $topologyClassName,
							 'topologyDataCenter' 	=> $topologyDataCenter,
							 'topologyEnvironment' 	=> $topologyEnvironment,
							 'topologyMode' 	=> $topologyMode,
							 'topologyName' 	=> $topologyName,
							 'topologyRegion' 	=> $topologyRegion,
							 'topologyTable' 	=> $topologyTable,
							 'version' 		=> $version,
							 'workers' 		=> $workers,
							 'zkPath' 		=> $zkPath,
							 'zkUrls' 		=> $zkPath
						),
						 'metadata' => array ('version' => $version, 'env' => 'QA'),
						 'mountPoint' => $mountPoint,
						 'script'=> $_TOPOLOGY_SCRIPT,
						 'tags' => explode(",", $tags)
				);

			 array_push($_MODEL_["entries"], $NEW_ENTRY);
		}
                unset($_MODEL_["id"]);

		//upload model
		$result = glu_upload_model($_MODEL_);

		//Generate plan
		$plan_create = glu_genrate_plan($agentNode, $mountPoint,"redeploy");

		// Execute plan
		$execute_plan = glu_execute_plan($plan_create);

		// Check if topology is running
		$topology_url = "http://" . $agentNode . ":8080/api/v1/topology/summary";
		$topology_summary       = send_curl($topology_url, "", "", "", "topology_summary");
		$topology_json          = json_decode($topology_summary, true);
        $topology_status        = "FALSE";

		foreach ($topology_json["topologies"] as $key => $value) {
			foreach ($value as $k => $v) {
       	        		if($k == "name" && $v == $topologyName) {
					PLog("TOPOLOGY FOUND : $k=====$v");
                    $topology_status = "TRUE";
				}
			}
		}
		
		// Log action in DB - Paritosh
        $insert_query = "INSERT INTO topology_logs (username,logged_in_time,ip,datacenter,agent,topology_type,mount_point,version,class_name,topology_datacenter,topology_table,topology_environment,topology_mode,topology_region,topology_name,tags,zkUrls,zkPath,workers,upload_model,genrate_plan,execute_plan,topology_status) VALUES('$username','$logged_in_time','$ip','$topologyDataCenter','$agentNode','$topologyType','$mountPoint','$version','$topologyClassName','$topologyDataCenter','$topologyTable','$topologyEnvironment','$topologyMode','$topologyRegion','$topologyName','$tags','$zkUrls','$zkPath','$workers','$result','$plan_create','$execute_plan','$topology_status')";

        PLog("Topology Insert Query = " . $insert_query);
        $db_obj->insert_query($insert_query);
		
        }

?>

<html>
  <head>
    <title>Submit Topology</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
  </head>

<body>
    <div class="container">
	<div class="well" id="pre">
		<pre>Change Glu Model - Response: <?=$result ?></pre>
		<pre>Generate Plan - Respone: <?=$plan_create ?></pre>
		<pre>Execute Plan - Respone: <?=$execute_plan ?></pre>
	</div>
	<a href="storm_api_poc.php" class="btn btn-primary btn-default pull-right" role="button">Home</a>
    </div>
</body>

</html>

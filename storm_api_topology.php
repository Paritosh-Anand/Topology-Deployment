<?php

	require_once 'global.php';
	require_once 'storm_functions.php';

	$node  	= ((isset($_REQUEST["node"]) && !empty($_REQUEST["node"]))?$_REQUEST["node"]:"");
	$id	= ((isset($_REQUEST["id"]) && !empty($_REQUEST["id"]))?$_REQUEST["id"]:"");

	if($node != "" && $id != "") { 
		$topology_detail_url	= "http://" . $node . ":8080/api/v1/topology/" . $id;
		PLog("topology_detail_url = " . $topology_detail_url);	
		$topology_detail	= json_decode(send_curl($topology_detail_url, "", "", "", "topology_detail"), true);

	}

?>

<!DOCTYPE html>
<html>
  <head>
    <title><?=$id ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link type="text/css" rel="stylesheet" href="css/style.css">
    <link type="text/css" rel="stylesheet" href="css/bootstrap.css">
    <script src="js/jquery-1.6.2.min.js" type="text/javascript"></script>
    <script src="js/jquery.tablesorter.min.js" type="text/javascript"></script>
    <script src="js/jquery.cookies.2.2.0.min.js" type="text/javascript"></script>
    <script src="js/jquery.mustache.js" type="text/javascript"></script>


    <script type="text/javascript" src="js/storm.js"></script>
  </head>

  <body>
	<form method="GET" class="form-inline">
		<div class="form-group pull-right">
			<?php if(isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])) { ?>
                        	<button type="submit" name = "sign_out" class="btn btn-info"><?=strtoupper($_SESSION['user_name']) ?> [sign out]</button>&nbsp;
                  	<?php } else { ?>
                       		<a href="storm_login.php"class="btn btn-info">SignIn</a>&nbsp;
                 	<?php } ?>
		</div>
	</form>
	<h3>Topology Summary</h3>
	<div id="topology-summary">
		<table id="topology-summary-table" class="table table-hover">
			<tr>
				<th>Name</th>
				<th>ID</th>
				<th>Status</th>
				<th>Uptime</th>
				<th>Num workers</th>
				<th>Num executors</th>
				<th>Num tasks</th>
			</tr>
			<?php
				print 	"<tr>
						<td>" . $topology_detail["name"] . "</td>
						<td>" . $topology_detail["id"] . "</td>
						<td>" . $topology_detail["status"] . "</td>
						<td>" . $topology_detail["uptime"] . "</td>
						<td>" . $topology_detail["workersTotal"] . "</td>
						<td>" . $topology_detail["executorsTotal"] . "</td>
						<td>" . $topology_detail["tasksTotal"] . "</td>
					</tr>";
			?>
		</table>
	</div>
	<h3>Topology Stats</h3>
	<div id="topology-stats">
		<table id="topology-stats-table" class="table table-hover">
			<tr>
				<th>Window</th>
				<th>Emitted</th>
				<th>Transferred</th>
				<th>Complete Latency (ms)</th>
				<th>Acked</th>
				<th>Failed</th>
			</tr>
			<?php foreach($topology_detail['topologyStats'] as $k => $v) { ?>
				<tr>
					<td><?=$topology_detail['topologyStats'][$k]['windowPretty'] ?></td>
					<td><?=$topology_detail['topologyStats'][$k]['emitted'] ?></td>
					<td><?=$topology_detail['topologyStats'][$k]['transferred'] ?></td>
					<td><?=$topology_detail['topologyStats'][$k]['completeLatency'] ?></td>
					<td><?=$topology_detail['topologyStats'][$k]['acked'] ?></td>
					<td><?=$topology_detail['topologyStats'][$k]['failed'] ?></td>
				</tr>
			<?php } ?>
		</table>
	</div>
	<h3>Spouts (All time)</h3>
	<div id="spouts">
		<table id="spout-table" class="table table-hover">
			<tr>
				<th>Id</th>
				<th>Executors</th>
				<th>Tasks</th>
				<th>Emitted</th>
				<th>Transferred</th>
				<th>Complete Latency (ms)</th>
				<th>Acked</th>
				<th>Failed</th>
				<th>Last error</th>
			</tr>
			<?php foreach($topology_detail['spouts'] as $k => $v) { ?>
				<tr>
					<td><?=$topology_detail['spouts'][$k]['spoutId'] ?></td>
					<td><?=$topology_detail['spouts'][$k]['executors'] ?></td>
					<td><?=$topology_detail['spouts'][$k]['emitted'] ?></td>
					<td><?=$topology_detail['spouts'][$k]['transferred'] ?></td>
					<td><?=$topology_detail['spouts'][$k]['completeLatency'] ?></td>
					<td><?=$topology_detail['spouts'][$k]['acked'] ?></td>
					<td><?=$topology_detail['spouts'][$k]['failed'] ?></td>
					<td><?=$topology_detail['spouts'][$k]['lastError'] ?></td>
				</tr>
			<?php } ?>
		</table>
	</div>
	<h3>Bolts (All time)</h3>
	<div id="bolts">
		<table id="bolt-table" class="table table-hover">
			<tr>
				<th>Id</th>
				<th>Executors</th>
				<th>Tasks</th>
				<th>Emitted</th>
				<th>Transferred</th>
				<th>Capacity (last 10m)</th>
				<th>Execute Latency (ms)</th>
				<th>Executed</th>
				<th>Process Latency (ms)</th>
				<th>Acked</th>
				<th>Failed</th>
				<th>Last error</th>
			</tr>
			<?php foreach($topology_detail['bolts'] as $k => $v) { ?>
				<tr>
					<td><?=$topology_detail['bolts'][$k]['boltId'] ?></td>
					<td><?=$topology_detail['bolts'][$k]['executors'] ?></td>
					<td><?=$topology_detail['bolts'][$k]['tasks'] ?></td>
					<td><?=$topology_detail['bolts'][$k]['emitted'] ?></td>
					<td><?=$topology_detail['bolts'][$k]['transferred'] ?></td>
					<td><?=$topology_detail['bolts'][$k]['executeLatency'] ?></td>
					<td><?=$topology_detail['bolts'][$k]['executed'] ?></td>
					<td><?=$topology_detail['bolts'][$k]['processLatency'] ?></td>
					<td><?=$topology_detail['bolts'][$k]['acked'] ?></td>
					<td><?=$topology_detail['bolts'][$k]['failed'] ?></td>
					<td><?=$topology_detail['bolts'][$k]['lastError'] ?></td>
				</tr>
			<?php } ?>
		</table>
	</div>
	<h3>Topology Configuration</h3>
	<div id="topology_configuration">
		<table id="topology-configuration-table" class="table table-hover">
			<tr>
				<th>Key</th>
				<th>Value</th>
			</tr>
			<?php foreach($topology_detail['configuration'] as $k => $v) { ?>
			<tr>
				<td><?=$k ?></td>
				<td><?=$v ?></td>
			</tr>
			<?php } ?>
		</table>
	</div>
  </body>
</html>

<?php

	require_once 'global.php';
	require 'storm_functions.php';

	$node 			= ((isset($_REQUEST["node"]) && !empty($_REQUEST["node"]))?$_REQUEST["node"]:"qa-stm1");

	$topology_url 		= "http://" . $node . ":8080/api/v1/topology/summary";
	$cluster_url  		= "http://" . $node . ":8080/api/v1/cluster/summary";
	
	$topology_summary 	= send_curl($topology_url, "", "", "", "topology_summary");
	$cluster_summary 	= send_curl($cluster_url, "", "", "", "cluster_summary");

	$topology_json 		= json_decode($topology_summary, true);
	$cluster_json 		= json_decode($cluster_summary, true);

?>
<html>
<head>
	<title>Storm Me</title>
	<link type="text/css" rel="stylesheet" href="css/style.css">
	<link type="text/css" rel="stylesheet" href="css/bootstrap.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="js/bootstrap.js"></script>

</head>

<body>
	<form method="GET" class="form-inline">
        	<div class="form-group pull-right">
        	  <select class="form-control" id="node" name="node">
        	    <option value = "">Select Storm Node</option>
        	    <option value="qa-stm1" <?=($node=="qa-stm1")?"selected":"" ?> >qa-stm1</option>
        	    <option value="stm1" <?=($node=="stm1")?"selected":"" ?> >stm1</option> 
        	  </select>
        	  <button type="submit" class="btn btn-default">Submit</button>&nbsp;
		  <?php if(isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])) { ?>
			<a href="storm_api_new_topology.php" class="btn btn-primary btn-default" role="button">Submit Topology</a>&nbsp;
		  	<button type="submit" name = "sign_out" class="btn btn-info"><?=strtoupper($_SESSION['user_name']) ?> [sign out]</button>&nbsp;
        	  <?php } else { ?>
		  	<a href="storm_login.php"class="btn btn-info">SignIn</a>&nbsp;
		  <?php } ?>
			<a href="storm_log_archive.php" target="_blank" class="btn btn-primary btn-default" role="button">Topology Archive</a>&nbsp;
	  	</div>
    	</form>

	<h2>Cluster Summary</h2>
	<div id="cluster-summary">
	<table id="cluster-summary-table" class="table table-hover">
		<tr>
			<th>Version</th>
			<th>nimbusUptime</th>
			<th>supervisors</th>
			<th>slotsTotal</th>
			<th>slotsUsed</th>
			<th>slotsFree</th>
			<th>executorsTotal</th>
			<th>tasksTotal</th>
		</tr>
		<tr>
			<?php foreach ($cluster_json as $k => $v) { ?>	
				<td><?=$v ?></td>
			<?php } ?>
		</tr>
	</table>
	</div>

	<h2>Topology Summary</h2>
	<div id="topology-summary">
	<table id="topology-summary-table" class="table table-hover">
		<tr>
			<th>name</th>
			<th>status</th>
			<th>uptime</th>
			<th>tasksTotal</th>
			<th>workersTotal</th>
			<th>executorsTotal</th>
		</tr>
		<?php foreach ($topology_json["topologies"] as $key => $value) { ?>
			<tr>
				<?php 
				$_TOPOLOGY_ID = "";
				foreach ($value as $k => $v) { 
					if($k == "id") {
						$_TOPOLOGY_ID = $v;
					} else if($k == "name") {
						print "<td><a href = '$_TOPOLOGY_PAGE?node=$node&id=$_TOPOLOGY_ID' target='_blank'>$v</a></td>";
					} else {
						print "<td>$v</td>";
					}
				}
				?>
			</tr>
		<?php } ?>
	</table>
	</div>
</body>
</html>

<?php

	require_once 'global.php';

	$rs = $db_obj->query("SELECT id,username,logged_in_time,ip,datacenter,agent,topology_type,mount_point,version,class_name,topology_datacenter,topology_table,topology_environment,topology_mode,topology_region,topology_name,tags,zkUrls,zkPath,workers,upload_model,genrate_plan,execute_plan,topology_status,topology_start_time FROM topology_logs");

?>
<html>
<head>
        <title>Archive</title>
        <link type="text/css" rel="stylesheet" href="css/style.css">
        <link type="text/css" rel="stylesheet" href="css/bootstrap.css">

</head>

<body>
	<h2>Topology Archive</h2>
	<div id="topology-archive">
        <table id="topology-archive-table" class="table table-hover">
		<tr>
                        <th>topology_name</th>
                        <th>topology_type</th>
                        <th>username</th>
                        <th>topology_start_time</th>
                        <th>topology_status</th>
                </tr>

		<?php while($row = mysql_fetch_object($rs)) { ?>
		<tr>
			<td><?=$row->topology_name ?></td>
			<td><?=$row->topology_type ?></td>
			<td><?=$row->username ?></td>
			<td><?=$row->topology_start_time ?></td>
			<td><?=$row->topology_status ?></td>
		</tr>
		<?php } ?>
	</table>
	</div>
</body>

</html>	

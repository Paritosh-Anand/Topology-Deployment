<?php

	require 'global.php';
	require 'storm_functions.php';

?>
<!DOCTYPE html>
<html>
  <head>
    <title>Submit Topology</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link type="text/css" rel="stylesheet" href="css/style.css">
    <link type="text/css" rel="stylesheet" href="css/bootstrap.css">
    <script type="text/javascript" src="js/storm.js"></script>
  </head>

  <body>
    <div class="container">
	<form method="GET" class="form-inline">
                <div class="form-group pull-right">
                        <?php if(isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])) { ?>
                                <button type="submit" name = "sign_out" class="btn btn-info"><?=strtoupper($_SESSION['user_name']) ?> [sign out]</button>&nbsp;
                        <?php } else { ?>
                                <a href="storm_login.php"class="btn btn-info">SignIn</a>&nbsp;
                        <?php } ?>
      			<a href="storm_api_poc.php" class="btn btn-primary btn-default" role="button">Home</a>&nbsp;
                </div>
        </form>

      <h4>Submit Topology</h4>
      <hr>
      <form method="POST" action="storm_submit_topology.php" id="new_topology" class="form-horizontal" role="form">
      	<div class="well well-sm" id="well" style="display:none;"></div>
        <div class="form-group">
	  <label for="dataCenter" class="col-sm-2 control-label">Data Center</label>
	  <div class="col-sm-6">
	  <select class="form-control" name="dataCenter" id="dataCenter" onchange="getGluData(this.value,document.getElementById('agentNode'))">
		<option value="">DataCenter</option>
		<?php
		foreach($_DC_CONF as $dc => $url) {
			print "<option value='" . $url . "storm/' >" . $dc . "</option>";
		}
		?>
	  </select>
	  </div>
        </div>
        <div class="form-group">
	  <label for="agentNode" class="col-sm-2 control-label">Agent</label>
	  <div class="col-sm-6">
          <select class="form-control" id="agentNode" name="agentNode">
		<option value="">Agent</option>
	  </select>
          </div>
        </div>
        <div class="form-group">
	  <label for="topologyType" class="col-sm-2 control-label">Topology Type</label>
	  <div class="col-sm-6">
          <select class="form-control" id="topologyType" name="topologyType">
		<option value="">Topology Type</option>
		<?php
                foreach($topologyTypeHash as $key => $topology_type) {
                        print "<option value='" . $topology_type . "'>" . $topology_type . "</option>";
                }
                ?>
	  </select>
	  </div>
        </div>
        <div class="form-group">
	  <label for="mountPoint" class="col-sm-2 control-label">Mount Point</label>
	  <div class="col-sm-6">
          	<input type="text" class="form-control" id="mountPoint" name="mountPoint" placeholder="Mount Point" value="<?=$mountPoint ?>" >
	  </div>
        </div>
	<hr >
        <div class="form-group">
	  <label for="jarName" class="col-sm-2 control-label">JAR</label>
	  <div class="col-sm-6">
         	 <input type="text" class="form-control" id="jarName" name="jarName" placeholder="JAR Name" value="<?=$jarName ?>" >
	  </div>
        </div>
        <div class="form-group">
	  <label for="version" class="col-sm-2 control-label">Version</label>
	  <div class="col-sm-6">
          	<input type="text" class="form-control" id="version" name="version" placeholder="Version" value="<?=$version ?>" >
	  </div>
        </div>
        <div class="form-group">
	  <label for="className" class="col-sm-2 control-label">Class Name</label>
	  <div class="col-sm-6">
          <select class="form-control" id="className" name="className" >
		<option value="">Class Name</option>
		<?php
		foreach($classNameHash as $key => $class_name) {
			print "<option value='" . $class_name . "'>" . $class_name . "</option>";
		}
		?>
	  </select>
	  </div>
        </div>
        <div class="form-group">
	  <label for="topologyDataCenter" class="col-sm-2 control-label">Topology Data Center</label>
	  <div class="col-sm-6">
          <select class="form-control" id="topologyDataCenter" name="topologyDataCenter" >
		<option value="">Topology DataCenter</option>
		<?php
		foreach($topologyDataCenterHash as $key => $topology_datacenter) {
			print "<option value='" . $topology_datacenter . "'>" . $topology_datacenter . "</option>";
		}
		?>
	  </select>
	  </div>
        </div>
        <div class="form-group">
	  <label for="topologyTable" class="col-sm-2 control-label">Topology Table</label>
	  <div class="col-sm-6">
          	<input type="text" class="form-control" id="topologyTable" name="topologyTable" placeholder="Topology Table" value="<?=$topologyTable ?>" >
	  </div>
        </div>
        <div class="form-group">
	  <label for="topologyEnvironment" class="col-sm-2 control-label">Topology Environment</label>
	  <div class="col-sm-6">
          <select class="form-control" id="topologyEnvironment" name="topologyEnvironment">
		<option value="">Topology Environment</option>
		<?php
                foreach($topologyEnvironmentHash as $key => $topology_environment) {
                        print "<option value='" . $topology_environment . "'>" . $topology_environment . "</option>";
                }
                ?>
	  </select>
	  </div>
        </div>
        <div class="form-group">
	  <label for="topologyMode" class="col-sm-2 control-label">Topology Mode</label>
	  <div class="col-sm-6">
          <select class="form-control" id="topologyMode" name="topologyMode" >
		<option value="">Topology Mode</option>
		<?php
		foreach($topologyModeHash as $key => $topology_mode) {
			print "<option value='" . $topology_mode . "'>" . $topology_mode . "</option>";
		}
		?>
	  </select>
	  </div>
        </div>
        <div class="form-group">
	  <label for="topologyRegion" class="col-sm-2 control-label">Topology Region</label>
	  <div class="col-sm-6">
          <select class="form-control" id="topologyRegion" name="topologyRegion" >
		<option value="">Topology Region</option>
		<?php
		foreach($topologyRegionHash as $key => $topology_region) {
			print "<option value='" . $topology_region . "'>" . $topology_region . "</option>";
		}
		?>
	  </select>
	  </div>
        </div>
        <div class="form-group">
	  <label for="topologyName" class="col-sm-2 control-label">Topology Name</label>
	  <div class="col-sm-6">
          	<input type="text" class="form-control" id="topologyName" name="topologyName" placeholder="Topology Name" value="<?=$topologyName ?>" >
		<button type="button" class="btn btn-danger pull-right" onclick="check_topology_running(document.getElementById('topologyName').value, document.getElementById('agentNode').value)">Check Status</button>
	  </div>
        </div>
        <div class="form-group">
	  <label for="tags" class="col-sm-2 control-label">Tags</label>
	  <div class="col-sm-6">
         	 <input type="text" class="form-control" id="tags" name="tags" placeholder="tags" value="<?=$tags ?>" >
          </div>
	</div>
        <div class="form-group">
	  <label for="zkUrls" class="col-sm-2 control-label">ZK Urls</label>
	  <div class="col-sm-6">
          	<input type="text" class="form-control" id="zkUrls" name="zkUrls" placeholder="zkUrls" value="<?=$zkUrls ?>" >
       	  </div>
	 </div>
        <div class="form-group">
	  <label for="zkPath" class="col-sm-2 control-label">ZK Path</label>
	  <div class="col-sm-6">
          	<input type="text" class="form-control" id="zkPath" name="zkPath" placeholder="zkPath" value="<?=$zkPath ?>" >
       	  </div>
	 </div>
        <div class="form-group">
	  <label for="workers" class="col-sm-2 control-label">Workers</label>
	  <div class="col-sm-6">
          	<input type="text" class="form-control" id="workers" name="workers" placeholder="workers" value="<?=$workers ?>" >
       	  </div>
	 </div>
	<input type="hidden" name="model" id="model" value="" />
        <button type="submit" name="topology_form" value="submit_new_topology" onclick = "return validate_form(document.getElementById('new_topology'))" class="btn btn-default">Submit</button>
      </form>
    </div>
		<div id ="mask" class="mask">
		<span><h2>Please wait while your data is populated...</h2></span></div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
  </body>
</html>

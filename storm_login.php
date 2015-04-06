<?php

	require 'global.php';
	require_once 'storm_functions.php';
	require_once 'storm_log.php';

	$login_succes = false;

	$console   		= $_NODE_GLU_MAP["default"];

    if(isset($_POST['sign_in']) && $user_name != '' && $_POST['password'] != '') { 	// USER is not logged in DB
		$login_success = glu_authenticate($user_name, $password, $console);
		if($login_success == true || $login_success > 0) {
			PLog("login is success ...starting session");
			session_start();
			$_SESSION['user_name'] 	= $user_name;
			$session_id     	= session_id();
			$rs             	= $db_obj->insert_query("INSERT INTO logged_in_user(username,session_id,ip) VALUES('$user_name','$session_id','$user_ip')");
			PLog("started session :- session_id = " . session_id());
			header('Location: storm_api_poc.php');
		} else {
			$message = "Incorrect Credentials !!!";
		}
	} 

?>
<!DOCTYPE html>
<html>
  <head>
    	<title>User Login</title>
    	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="css/style.css">
    	<link rel="stylesheet" href="css/bootstrap.css">
    	<script type="text/javascript" src="js/storm.js"></script>
    	<script type="text/javascript">
    		<?php
    			if($is_logged_in > 0) {
    				print "window.location = 'http://localhost/storm/storm_api_poc.php';";
    			}
    		?>
    	</script>
  </head>
	<body>
		<div id="login" class="container">
			<div class="col-lg-offset-2"><h2><span>Storm App</span>&nbsp; &nbsp;<a href="storm_api_poc.php" class="btn btn-info">< Home</a></h2></div>
			&nbsp;
		<form method="POST" class="form-horizontal">
			<div class="form-group">
				<label for="user_name" class="col-sm-2 control-label col-sm-offset-2">Username &nbsp;</label>
				<div class="col-sm-5">
					<input type="text" class="form-control" name="user_name" id="user_name" placeholder="Username">
				</div>
			</div>
			<div class="form-group">
				<label for="password" class="col-sm-2 control-label col-sm-offset-2">Password &nbsp;</label>
				<div class="col-sm-5">
					<input type="password" class="form-control" name="password" id="password" placeholder="Password">
				</div>
			</div>
			<div>
				<input type="hidden" name="hidden_user_name" id="hidden_user_name" value="<?=$user_name ?>">
			</div>
			<div class="form-group">
				<div class="col-sm-5 col-sm-offset-4">
					&nbsp;
					<button type="submit" name="sign_in" class="btn btn-primary btn-block">Sign in</button>
				</div>
			</div>
		</form>
		</div>
	</body>
</html>

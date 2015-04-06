<?php

	require_once 'mysql_database.php';

	$_NODE_GLU_MAP = array(
		"qa-stm1"	=> "http://v-services-dev1:8080/console/rest/v1/",
		"default"	=> "http://v-services-dev1:8080/console/rest/v1/-"
	);
	
	$_DC_CONF = array(
	    "QA"    => 	"http://v-services-dev1:8080/console/rest/v1/"
	);
	
	$topologyTypeHash	= array("CACHE","INDEX");
	
	$topologyRegionHash 	= array("US","EU","ROW");
	$classNameHash		= array("com.nextag.platform.datastore.importer.ImporterTopology","com.wizecommerce.userevent.topology.ClickTopology");
	$topologyEnvironmentHash= array("QA","PRODUCTION");
	$topologyModeHash	= array("INCREMENTAL","FULL","VALIDATION","DELETE");
	$topologyDataCenterHash	= array("SVS","SDX");


	$_USERNAME_     = "release";
        $_PASSWORD_     = "release";


	$_TOPOLOGY_PAGE = "http://localhost/storm/storm_api_topology.php";
	$_TOPOLOGY_SCRIPT = "http://v-services-dev1.nextagqa.com:8080/glu/repository/scripts/StormScript.groovy";

	define("FETCH_ASSOC", "FETCH_ASSOC");

	//Check if a session exists for the user in DB. If it does then enforces the same session.
	
	$is_logged_in   = 0;
        $session_id     = (null != session_id()) ? session_id() : '';
        $logged_in_time = "";
        $user_ip   = $_SERVER['REMOTE_ADDR'];

	$user_name              = (isset($_POST['user_name']) ? $_POST['user_name'] : '');
        $hidden_user_name       = (isset($_POST['hidden_user_name']) ? $_POST['hidden_user_name'] : '');
        $user_name              = empty($user_name) ? $hidden_user_name : $user_name;
	$password               = (isset($_POST['password']) ? $_POST['password'] : '');

	$db_obj = new Database();
        $db_obj->select_db("storm");

        PLog($_SERVER['PHP_SELF'] . ": session_id = " . $session_id);
	
	session_save_path('/var/log/session');
	ini_set('session.gc_probability', 1);	

       	$rs = $db_obj->query("SELECT count(*) as count ,username,session_id,logged_in_time,ip FROM logged_in_user WHERE ip = '" . $user_ip . "'");
      	while($row = mysql_fetch_object($rs)) {
       		$is_logged_in = $row->count;
       		if($is_logged_in) {
			$user_name = $row->username;
       			$session_id = $row->session_id;
       			$logged_in_time = $row->logged_in_time;
		}
        }


	if ($is_logged_in > 0) {                                                                                // USER is Already logged in DB
                //PLog("====== USER already logged in === " . $session_id);
                session_id($session_id);
                session_start();
                $_SESSION['user_name'] = $user_name;
                $_SESSION['logged_in_time'] = $logged_in_time;

		if(isset($_REQUEST['sign_out'])) {									// USER wants to sign out
			session_unset();
	                session_destroy();
	                PLog("SIGN OUT !!! Session id 2 = " . session_id());
	
        	        // Remove entry from DB--
        	        $db_obj->delete_query("DELETE FROM logged_in_user WHERE ip = '" . $user_ip . "'");

			header('Location: storm_login.php');
        	}
        }

?>

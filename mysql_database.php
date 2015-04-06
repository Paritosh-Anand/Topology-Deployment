<?php
	require_once 'global.php';
	include_once 'storm_log.php';

	class Database{

		private $link = "";

		function __construct() {

			$this->link = mysql_connect('v-services-dev1', 'glua', 'nextag');
			if (!$this->link) {
				PLog('Could not connect: ' . mysql_error());
			}
			return $this->link;
		}


		function __destruct() {
			//PLog("Going to close MySQL link = " . $this->link);
			mysql_close($this->link);
		}

		function select_db($db_name) {
			//PLog("Select database - " . $db_name);
			mysql_select_db($db_name);
		}

		function query($query) {
			$rs = mysql_query($query);
			//PLog("query - $query \n rs -- $rs");
			return $rs;
		}

		function insert_query($query) {
			$rs = mysql_query($query);
			return $rs;
		}
		
		function delete_query($query) {
			$rs = mysql_query($query);
			return $rs;
		}
	}


?>

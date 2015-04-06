<?php

date_default_timezone_set("Asia/Kolkata");

function PLog($log) {
        $f = fopen("/var/log/storm", "a+");
        fwrite($f, date("Y-m-d H:i:s") . " " . $log . "\n");
        fclose($f);
}

?>

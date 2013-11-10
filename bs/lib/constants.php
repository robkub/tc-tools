<?php
$url_prefix = "./";
if(strpos($_SERVER["SCRIPT_NAME"], "admin") !== FALSE)
	$url_prefix = "../";
define("DIR_PREFIX", $url_prefix);

define("BANNER_PATH", DIR_PREFIX."pics/");

define("BANNER_WIDTH", 50);
define("BANNER_HEIGHT", 20);
?>

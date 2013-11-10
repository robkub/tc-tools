<?php echo '<?xml version="1.0" encoding="ISO-8859-1"?>' ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
require_once("connect.php");
require_once("constants.php");
require_once("functions.php");

foreach($_POST as $name => $value) {
	if(is_array($value))
		$_POST[$name] = array_unique($value);
}

?>
<html>
	<head>
		<title>TC-Banner</title>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
		<link rel="stylesheet" type="text/css" href="<? echo DIR_PREFIX ?>screen.css" media="screen" />
		<script type="text/javascript" src="<? echo DIR_PREFIX ?>lib/jquery.js"></script>
		<script type="text/javascript" src="<? echo DIR_PREFIX ?>lib/jquery.ajaxfileupload.js"></script>
		<script type="text/javascript" src="<? echo DIR_PREFIX ?>lib/config.js"></script>
		<script type="text/javascript">
		<!--
			DIR_PREFIX = "<?php echo DIR_PREFIX; ?>";
		// -->
		</script>
	</head>
	<body>
		<div id="container">
			<div id="header">
				<div class="hide">
					<h1 class="hide">TC-Banner</h1>
					<h2>Die ewige Sammlung der TC-Banner</h2>
				</div>
			</div>
			<div id="toplinks">
				<img name="header4" src="<? echo DIR_PREFIX ?>images/header4.gif" width="940" height="50" border="0" usemap="#m_header4" alt="">
				<map name="m_header4">
					<area shape="rect" coords="694,13,770,37" href="http://www.schwertkriege.de/forum/threads/3831-Die-Ewige-Sammlung-Der-TC-Banner?p=45664#post45664" target="_blank">
					<area shape="rect" coords="557,13,678,37" href="<? echo DIR_PREFIX ?>impressum.php">
					<area shape="rect" coords="343,13,543,37" href="<? echo DIR_PREFIX ?>upp.php">
					<area shape="rect" coords="138,13,322,37" href="<? echo DIR_PREFIX ?>./">
				</map>
			</div>
			<div id="top-frame"></div>
			<div id="content"">

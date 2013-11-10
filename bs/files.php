<?php
require_once("lib/connect.php");
require_once("lib/constants.php");
function get_ally_of_file($file) {
	$sql = "SELECT `a`.`tag`, `a`.`name`, `b`.`url`, `b`.`id` as `bannerID`"
		." FROM `banner` as `b` INNER JOIN `ally` as `a` ON (`a`.`allyID` = `b`.`allyID`)"
		." WHERE `url` LIKE '%". $file ."'";
	$res = mysql_query($sql) or die(mysql_error());
	if(mysql_num_rows($res) > 0) {
		$ally = mysql_fetch_assoc($res);
//		$sql = "UPDATE `banner` SET `url` = '". basename($file) ."' WHERE `id` = '". $ally["bannerID"] ."'";
//		mysql_query($sql);
		return $ally;
	}
	else
		return NULL;
}

function get_free_allies($notin) {
	if(count($notin) == 0)
		return NULL;
	$sql = "SELECT `b`.`tag`, `b`.`name`, `b`.`url`"
		." FROM `banner` as `b`"
		." WHERE `url` NOT IN (". implode(", ", $notin) .")"
		."  AND ("
		."   SELECT COUNT(`b`.`tag`) FROM `banner` as `b2`"
		."   WHERE `b`.`allyID` = `b2`.`allyID` AND `b2`.`url` != `b`.`url`"
		."  ) = 0"
		." GROUP BY `tag`, `name`";
//	echo $sql;
	$res = mysql_query($sql);
	if(mysql_num_rows($res) > 0) {
		$ret = array();
		while($ret[] = mysql_fetch_assoc($res));
		return $ret;
	}
	else
		return array();	
}

$dir = dir(BANNER_PATH);
$imgWithAlly = array();
$imgWithoutAlly = array();
$notin = array();

while (false !== ($entry = $dir->read())) {
	
 	if(is_file(BANNER_PATH.$entry)) {
		$ally = get_ally_of_file($entry);
		if(isset($ally)) {
			$imgWithAlly[BANNER_PATH.$entry] = $ally;
			$notin[] = "'". $ally["url"] ."'";
		}
		else
			$imgWithoutAlly[] = BANNER_PATH.$entry;
	}
}
$dir->close();

$notins = get_free_allies($notin);

ksort($imgWithAlly);
sort($imgWithoutAlly);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en_US" xml:lang="en_US">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=latin1" />
	<meta http-equiv="Content-Language" content="en" />
	<meta name="GENERATOR" content="PHPEclipse 1.2.0" />
	<title>Banner</title>
</head>
<body>
	<div id="imgWithAlly">
		<h1>Banner with ally</h1>
		<? foreach($imgWithAlly as $img => $one_ally) { ?>
			<p>
				<img src="<? echo $img ?>" /> (<? echo $img ?>)
				Ally: <? echo $one_ally["tag"] ?> - <? echo $one_ally["name"] ?> (<? echo $one_ally["url"] ?>) 
			</p>
		<? } ?>
	</div>
	<div id="imgWithoutAlly">
		<h1>Banner without ally</h1>
		<? foreach($imgWithoutAlly as $img) { ?>
			<p>
				<img src="<? echo $img ?>" /> (<? echo $img ?>)
			</p>
		<? } ?>
	</div>
	<div id="notins">
		<h1>Ally without img</h1>
		<? if(isset($notins)) {?>
			<? foreach($notins as $one_ally) { ?>
				<p>
					Ally: <? echo $one_ally["tag"] ?> - <? echo $one_ally["name"] ?> (<? echo $one_ally["url"] ?>) 
				</p>
			<? } ?>
		<? } else { ?>
			<p>Keine ally die kein Bild hat</p>  
		<? } ?>
	</div>
</body>
</html>


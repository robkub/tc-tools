<?php
require_once("../lib/header.php");

echo '"'. $_POST["tag"] .'"';

/*
	Verkn�pft eine Allainz (allyID) mit den Runden (Array von rundenIDs)
*/
function insert_runden($allyID, $runden) {
	// alte Verkn�pfungen l�schen
	mysql_query("DELETE FROM `teil_der_runde` WHERE `allyID` = '". $allyID ."'");
	if(!is_array($runden))
		$runden = array($runden);
	foreach($runden as $rundenID) {
		$sql = "INSERT INTO `teil_der_runde` (`allyID`, `rundenID`) VALUES ('". $allyID ."', '". $rundenID ."')";
		mysql_query($sql);
	}
}

$tag = $HTTP_POST_VARS["tag"];
$name = $HTTP_POST_VARS["name"];
$runde = $HTTP_POST_VARS["runde"];
$art = $HTTP_POST_VARS["art"];

if ( $art=="url")
{
  $url = $HTTP_POST_VARS["url"];
  echo "<img src=".$url.">\n<br />";

}
else {
	$datei = $_FILES['grafik']['name'];
  if ($datei != ''){
    if (isset($_FILES['grafik']) and ! $_FILES['grafik']['error']) {
      move_uploaded_file($_FILES['grafik']['tmp_name'], "pics/".$datei);
      printf("Die Datei %s w�rde erfolgreich hochgeladen.<br />\n",
      $_FILES['grafik']['name']);
      printf("Sie ist %u Bytes gro� und vom Typ %s.<br />\n",
      $_FILES['grafik']['size'], $_FILES['grafik']['type']);
      $url = "http://tc.aurele.de/pics/".$datei;
      echo "<img src=".$url.">\n<br />";
    }
  }
}

if ($art == "url" or $art == "upp")
{

			$sql = "INSERT INTO banner
  			  (url,tag,name)
			VALUES
 			   ('$url',
 			    '[". $tag ."]',
 			    '$name')";
			$result = mysql_query($sql) OR die(mysql_error());
			$allyID = mysql_insert_id();
			insert_runden($allyID, $_POST["runde"]);
}

$id = $HTTP_POST_VARS["id"];
$edit = $HTTP_POST_VARS["edit"];
$url_n = $HTTP_POST_VARS["url_n"];
$tag_n = $HTTP_POST_VARS["tag_n"];
$name_n = $HTTP_POST_VARS["name_n"];
$runde_n = $HTTP_POST_VARS["runde_n"];

if ($edit == '1'){
	$sql = "UPDATE banner
	SET `url` = '".$url_n."',
	`tag` = '[".$tag_n."]',
	`name` = '".$name_n."'
  WHERE id = ".$id."";
	$result = mysql_query($sql) OR die(mysql_error());
	insert_runden($id, $_POST["runde_n"]);

	echo "Die Daten wurden Erfolgreich ge�ndert<br />\n";
	echo "<img src=\"".$url_n."\">".$tag_n.$name_n.$runde_n;
}

// wenn eine neue Runde eingetragen werden soll - by ROBO
if (isset($_POST["neue_runde"])) {
 	$rundenname = trim($_POST["rundenname"]);
	// Test ob Rundenname schon existiert
	$qry = "SELECT count(`rundenID`) FROM `runde` WHERE `rundenname` = '". $rundenname ."'";
	$res = mysql_query($qry);
	list($anz) = mysql_fetch_row($res);
	if($anz == 0) { // Wenn rundenname noch nicht vorhanden, dann eintragen
	  $qry = "INSERT INTO `runde` (`rundenname`) VALUE ('". $rundenname ."')";
	  $res = mysql_query($qry);
	  ?>
	    Die Runde "<?php echo $rundenname; ?>" wurde angelegt.
	  <?php
	}
	else
		echo 'Eine Runde mit dem Namen "'. $rundenname .'" existiert bereits.';
}
// Wenn Runde gel�scht werden soll
// 	L�scht auch alle Verkn�pfungen
if (isset($_POST["del_runde"])) {
  $rundenID = $_POST["rundenID"];
	// aus `runde`-tabelle entfernen
  $qry = "DELETE FROM `runde` WHERE `rundenID` = '". $rundenID ."'";
	mysql_query($qry);
	// Verkn�pfungen mit Allianzen l�schen
	$qry = "DELETE FROM `teil_der_runde` WHERE `rundenID` = '". $rundenID ."'";
	mysql_query($qry);

	echo "Die Runde wurde entfernt.";
}

 ?>
 <p>
	 <a href="index.php">Banner Anzeigen</a> - <a href="index.php?include=edit">Banner Eintragen / Editieren</a>
 </p>
 <? require_once("../lib/footer.php"); ?>
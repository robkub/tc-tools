<? require_once("lib/header.php"); ?>
<?php
$this_site = "index.php";
$runden = alle_runden();
if(isset($_GET["rundenID"])) {
	$rundenID = $_GET["rundenID"];
	if($rundenID === "all")
		$head = "Allianzen aus allen Runden";
	else
		$head = "Ausgew&auml;hlte Runde: ". runde_by_rundenid(intval($rundenID));
	$allies = allies_of_runde($rundenID);
}
else {
	$head =  "Auswahl";
}
?>
<h1><? echo $head ?></h1>

<div id="round-select">
	<h2>Rundenauswahl:</h2>
	<ul>
		<li><a href="?rundenID=all">Alle Runden</a></li>
  	<? foreach($runden as $runde) { ?>
    	<li><a href="?rundenID=<? echo $runde["id"] ?>"><? echo $runde["name"] ?></a></li>
	<? } ?>
	</ul>
</div>

<? if(isset($allies)) { ?>
	<div id="allies">
  	<? if($allies === NULL) { ?>
		<p>Runde mit der ID <? echo $rundenID ?> nicht bekannt.</p>
	<? } else if(count($allies) > 0) { ?>
		<? foreach($allies as $key => $ally) { ?>
			<div class="ally">
				<div class="name"><span class="desc">Ally:</span><? echo htmlentities($ally["tag"]) ?> <? echo $ally["name"] ?></div>
				<div class="rounds"><span class="desc">Runden:</span> <? echo implode(", ", $ally["runden"]) ?></div>
				<div class="banner">
					<span class="desc">Banner:</span>
    			<? foreach($ally["urls"] as $url) { ?>
	      			<img src="<? echo BANNER_PATH.$url ?>" alt="Banner von <? echo $ally["tag"] ?>" />
    			<? } ?>
				</div>
			</div>
		<? } ?>
	<? } else { ?>
		<p>Zu dieser Runde gibt es keine Allianz in der Datenbank</p>
	<? } ?>
	</div>
<? } else { ?>
	<p>Bitte w&auml;hlen Sie eine Runde aus</p>
<? } ?>
<? require_once("lib/footer.php") ?>
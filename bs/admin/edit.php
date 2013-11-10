<?
require_once("../lib/header.php");

$action = array();
$error = array();
if(isset($_GET["action"])) {
	if($_GET["action"] == "del-message") {
		if(delete_message($_GET["id"]))
			$action[] = "Die Information wurde gel&ouml;scht.";
		else
			$error[] = "Die Information konnte nicht gelöscht werden.". mysql_error();		
	}
}

if(isset($_POST["allySubmit"])) {
	if(is_array($_POST["runden"]))
		$p_runden = array_map("intval", array_unique($_POST["runden"]));
	else
		$error[] = "Du musst Runden f&uuml;r die Allianz markieren!";
	if(is_array($_POST["banner"]))
		$p_banner = array_map("intval", array_unique($_POST["banner"]));
	else
		$error[] = "Du musst Banner f&uuml;r die Allianz markieren!";

	if(count($error) > 0)
		$del = 1;
	elseif($_POST["action"] == "new") {
		$p_tag = trim($_POST["tag"]);
		if(strlen($p_tag) <= 2 || !preg_match("#\[.{1,3}\]#", $p_tag)) {
			$error[] = "Der Allianztag <b>". $p_tag ."</b> ist falsch. Richtig ist beispielsweise: [tag] (vorne und hinten [ ])";
		}
		$p_name = trim($_POST["name"]);
		if(strlen($p_name) == 0) {
			$error[] = "Sie m&uuml;ssen ein Allianzname angeben.";
		}
		
		if(search_ally($p_tag, $p_name) !== null) {
			$error[] = "Allianz <b>". $p_tag ." ". stripslashes($p_name) ."</b> existiert bereits.";
		}
		
		if(empty($error)) {
			new_ally($p_tag, $p_name, $p_banner, $p_runden);
			$action[] = "Allianz <b>". $p_tag ." ". stripslashes($p_name) ."</b> wurde erstellt.";
		}
	}
	elseif ($_POST["action"] == "edit") {
		$p_tag = trim($_POST["edit_tag"]);
		if(strlen($p_tag) <= 2 || !preg_match("#\[.{1,3}\]#", $p_tag)) {
			$error[] = "Der Allianztag <b>". $p_tag ."</b> ist falsch. Richtig ist beispielsweise: [tag] (vorne und hinten [ ])";
		}
		$p_name = trim($_POST["edit_name"]);
		if(strlen($p_name) == 0) {
			$error[] = "Sie m&uuml;ssen ein Allianzname angeben.";
		}
		$p_allyid = intval($_POST["ally"]);
		if($p_allyid <= 0)
			$error[] = "Sie m&uuml;ssen eine Allianz ausw&aumlhlen.";
		
		$edit_ally = search_ally($p_tag, $p_name);
		if($edit_ally !== null && intval($edit_ally["id"]) !== $p_allyid) {
			$error[] = "Allianz <b>". $p_tag ." ". stripslashes($p_name) ."</b> existiert bereits.";
		}
		
		if(empty($error)) {
			edit_ally($p_allyid, $p_tag, $p_name, $p_banner, $p_runden);
			$action[] = "Allianz <b>". $p_tag ." ". stripslashes($p_name) ."</b> bearbeitet.";
		}
	}
}
if(count($action) == 0)
	unset($action);
if(count($error) == 0)
	unset($error);
	
if(count($_POST) > 0 && is_array($error)) {
	foreach($_POST as $name => $value) {
		if(!is_array($value)) {
			$_POST[$name] = stripslashes($value);
		}
	}
?>
	<script type="text/javascript" language="JavaScript">
	<!--
		$(function() {
			var post = <?php echo json_encode($_POST) ?>;

			for(var name in post) {
				var value = post[name];
				var inp = $("[name^="+ name +"]");
				
				if(inp.get(0).tagName == "SELECT") 
					inp.children("option[value="+ value +"]").attr("selected", "selected");
				else if(inp.is("[type=checkbox], [type=radio]")) {
					var func = function(i, val) {
						if(!inp.filter("[value="+ val +"]").parent().hasClass("selected"))
							inp.filter("[value="+ val +"]:first").trigger("change");
					}
					if(typeof value == "object")
						jQuery.each(value, func);
					else
						func(0, value);
				}
				else {
					inp.val(value);
				}
			}
		});
	// -->
	</script>
<?php 	
}

$allies = allies_of_runde("all");
$runden = alle_runden();
$banner = get_all_banner();

$messages = get_messages();

?>

<h1>Banner eintragen</h1>

<?php if(is_array($error)) { ?>
	<div class="error">
		<p>Deine Angaben sind leider falsch.</p>
		<ul>
		<?php foreach($error as $text) { ?>
			<li><?php echo $text ?></li>
		<?php } ?>
		</ul>
	</div>
<?php } ?>

<?php if(is_array($action)) { ?>
	<div class="success">
		<p>Folgende Aktionen wurden erfolgreich durchgef&uuml;hrt.</p>
		<ul>
		<?php foreach($action as $text) { ?>
			<li><?php echo $text ?></li>
		<?php } ?>
		</ul>
	</div>
<?php } ?>


<div id="editbox">
	<form method="POST" action="edit.php">
		<div class="tabs">
			<label for="action-new" onclick="showFields('allianz-new', this); $('#allianz-edit-box').slideUp(); $('#banner-belong-button').hide();" class="selected"><input type="radio" name="action" value="new" id="action-new" checked="checked" />Neue Allainz</label>
			<label for="action-edit" onclick="showFields('allianz-edit', this); if($('#allianz-edit-box').hasClass('selected')) {$('#allianz-edit-box').slideDown();$('#banner-belong-button').show()}"><input type="radio" name="action" value="edit" id="action-edit" />Allianz editieren</label>
		</div>
		<div class="tabsfield box">
			<fieldset>
				<legend>Metadaten</legend>
				<div id="allianz-new">
					<h3 class="js-hidden">Daten zur neuen Allianz</h3>
					<table>
						<tbody>
							<tr>
								<th>Allianztag</th>
								<td><input type="text" name="tag" id="allytag" maxlength="5" size="5" value="[tag]" /> <i>Angabe mit Klammern</i></td>
							</tr>
							<tr>
								<th>Allianzname</th>
								<td><input type="text" name="name" id="allyname" maxlength="100" /></td>
							</tr>
						</tbody>
					</table>
				</div>
				<div id="allianz-edit" class="js-hidden">
					<h3 class="js-hidden">Vorhandene allianz w&auml;hlen</h3>
					<b>Vorhandene Allianzen: </b><select name="ally" id="ally-select" onchange="loadAllyData(this);">
						<option value="0">Bitte waehlen</option>
					<? foreach($allies as $id => $ally) { ?>
						<option value="<? echo $id ?>"><? echo $ally["tag"] ." ". $ally["name"] ?></option>
					<? } ?>
					</select>
					<image id="ally-laoding" style="display: none" src="../images/loading.gif" alt="loading" />
				</div>
			</fieldset>
			<fieldset id="allianz-edit-box" class="js-hidden">
				<legend>Allianzdaten bearbeiten</legend>
				<div id="allianz-edit-fields">
					<h3 class="js-hidden">Daten zur vorhandenen Allianz</h3>
					<table>
						<tbody>
							<tr>
								<th>Allianztag</th>
								<td><input type="text" name="edit_tag" id="edit_allytag" maxlength="5" size="5" value="[tag]" /> <i>Angabe mit Klammern</i></td>
							</tr>
							<tr>
								<th>Allianzname</th>
								<td><input type="text" name="edit_name" id="edit_allyname" maxlength="100" /></td>
							</tr>
						</tbody>
					</table>
				</div>				
			</fieldset>
			<fieldset class="runden">
				<legend>Runden</legend>
				<h3>Auswahl existierender Runden</h3>
				<div id="runden-exists" class="select">
				<? foreach($runden as $runde) { ?>
					<label for="runden-sel-<? echo $runde["id"] ?>" class="option">
						<input type="checkbox" name="runden[]" value="<? echo $runde["id"] ?>" id="runden-sel-<? echo $runde["id"] ?>" />
						<? echo $runde["name"] ?>
					</label>
				<? } ?>
					<div class="clearer"></div>
				</div>
				<h3>Neue Runden zuordnen</h3>
				<div id="runden-new"> 
					<input type="text" id="new-runde" />
						<input type="button" onclick="neueRunde('new-runde', 'runden-new-list','runden-loading')" value="neue Runde anlegen" />
						<img id="runden-loading" style="display: none" src="../images/loading.gif" alt="loading" />
					<div id="runden-new-list" class="select hide">
						<div class="clearer"></div>
					</div>
				</div>
			</fieldset>
			<fieldset class="banner">
				<legend>Banner</legend>
				<div class="tabs">
					<span id="banner-belong-button" onclick="showFields('banner-belong', this)" class="js-hidden">Zugeordnete Banner</span>
					<span onclick="showFields('banner-open', this)" class="selected">Offene/Neue Banner</span>
					<span onclick="showFields('banner-all', this)">Alle vorhandene Banner</span>
					<span onclick="showFields('banner-new', this)">Neues Banner hochladen</span>
				</div>
				<div class="tabsfield">	
					<div id="banner-belong" class="select js-hidden">
						<div class="clearer"></div>
					</div>
					<div id="banner-open" class="select">
					<?php foreach($banner["open"] as $row) { ?>
						<label class="option" for="banner-sel-<? echo $row["id"] ?>">
							<input type="checkbox" name="banner[]" value="<? echo $row["id"] ?>" id="banner-sel-<? echo $row["id"] ?>" />
							<img src="<?php echo BANNER_PATH.$row["url"]?>" alt="Banner<?php echo $row["id"] ?>" />
						</label>
					<?php } if(empty($banner["open"])) echo '<div class="empty">leer</div>' ?>
						<div class="clearer"></div>
					</div>
					<div id="banner-all" class="select js-hidden">
					<?php $merge = array_merge($banner["assigned"], $banner["open"]); foreach($merge as $row) { ?>
						<label for="banner-sel-<? echo $row["id"] ?>" class="option">
							<input type="checkbox" name="banner[]" value="<? echo $row["id"] ?>" id="banner-sel-<? echo $row["id"] ?>" />
							<img src="<?php echo BANNER_PATH.$row["url"]?>" alt="Banner<?php echo $row["id"] ?>" />
						</label>
					<?php } if(empty($merge)) echo '<div class="empty">leer</div>' ?>
						<div class="clearer"></div>
					</div>
					<div id="banner-new" class="js-hidden">
						<input id="uploadBanner" name="uploadBanner" type="file" size="50" accept="image/*">
							<button id="buttonUpload" onclick="return imageUpload('<? echo DIR_PREFIX ?>');">Hochladen</button>
							<image id="upload-laoding" style="display: none" src="../images/loading.gif" alt="loading" />
						<div id="banner-new-list" class="select hide">
							<div class="clearer"></div>
						</div>
					</div>
				</div>
				<div class="editfields">
				</div>
			</fieldset>
			<div class="buttons">
				<input type="submit" name="allySubmit" value="Allianzdaten &uuml;bernehmen" />
				<input type="reset" />
			</div>
		</div>
	</form>
</div>

<?php if(count($messages) > 0) {?>
<div id="messages">
	<p>Folgende Informationen haben Leute hinterlassen</p>
	<?php foreach($messages as $row) {?>
		<div class="del-message"><a href="?action=del-message&id=<?php echo $row["id"]?>">L&ouml;schen</a></div>
		<div class="message"><?php echo str_replace("./pics", "../pics", $row["message"]) ?></div>
	<?php }?>
</div>
<?php } ?>
<? require_once("../lib/footer.php"); ?>

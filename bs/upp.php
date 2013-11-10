<?
require_once("lib/header.php");

if(isset($_POST["allySubmit"])) {
	if(strlen($_POST["person_name"]) == 0) 
		$error[] = "Sie m&uuml;ssen einen Namen angeben.";

	if(strlen($_POST["person_email"]) == 0) 
		$error[] = "Sie m&uuml;ssen eine E-Mailadresse angeben.";
	
	if(!is_array($error)) {
		if(is_array($_POST["runden"]))
			$p_runden = array_map("runde_by_rundenid", array_map("intval", array_unique($_POST["runden"])));
		if(is_array($_POST["banner"]))
			$p_banner = array_map("get_banner", array_map("intval", array_unique($_POST["banner"])));
	
		if($_POST["action"] === "new") {
			$p_tag = trim($_POST["tag"]);
			$p_name = trim($_POST["name"]);
		} elseif ($_POST["action"] === "edit") {
			$org_ally = get_ally(intval($_POST["ally"]));
			$p_tag = trim($_POST["edit_tag"]);
			$p_name = trim($_POST["edit_name"]);
		}
		
		$msg = '<p class="head"><b>'. htmlentities($_POST["person_name"]) .'</b> ('. htmlentities($_POST["person_email"]) .', '. $HTTP_SERVER_VARS["REMOTE_ADDR"] .') schrieb am '. date("d.m.y") .' um '. date("H:i:s") .'</p>'
			.'<div class="data">';
			
		if(isset($org_ally) && ($org_ally["tag"] !== $p_tag || $org_ally["name"] !== $p_name))
			$msg .= '<p class="ally">Die Allianz <b>'. $org_ally["tag"] .' '. $org_ally["name"] .'</b> hei&szlig;t eigentlich <b>'. $p_tag .' '. $p_name .'</b> und hat folgende Daten.</p>';
		else
			$msg .= '<p class="ally">Die '. ((isset($org_ally)) ? '<i>bereits bekannte</i> ' : '') .'Allianz <b>'. $p_tag .' '. $p_name .'</b> hat folgende Daten.</p>';
			
		if(count($p_runden) > 0) {
			$msg .= '<p class="runden">Die Allianz war/ist in den Runden: ';
			foreach($p_runden as $r)
				$msg .= '<b>'. $r .'</b>, ';
			$msg .= '</p>';
		}
		if(count($p_banner) > 0) {
			$msg .= '<p class="runden">Die Allianz hat/hatte folgende Banner: ';
			foreach($p_banner as $b)
				$msg .= '<img src="'. BANNER_PATH.$b["url"] .'" alt="Banner '. $b[id] .'"/> ';
			$msg .= '</p>';
		}
		$msg .= "</div>";
		add_message($msg);
	}
}

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

<?php if(isset($msg)) { ?>
	<p>Folgende Nachricht wurde hinterlegt. Erst nachdem ein Admin diese Eingaben gepr&uuml;ft und eingetragen hat, sind die &Auml;nderungen sichtbar.</p>
	<div class="message"><?php echo $msg ?></div>
<?php } ?>


<div id="editbox">
	<form method="POST" action="upp.php">
		<div class="tabs">
			<label for="action-new" onclick="showFields('allianz-new', this); $('#allianz-edit-box').slideUp(); $('#banner-belong-button').hide();" class="selected"><input type="radio" name="action" value="new" id="action-new" checked="checked" />Neue Allainz</label>
			<label for="action-edit" onclick="showFields('allianz-edit', this); if($('#allianz-edit-box').hasClass('selected')) {$('#allianz-edit-box').slideDown();$('#banner-belong-button').show()}"><input type="radio" name="action" value="edit" id="action-edit" />Allianz editieren</label>
		</div>
		<div class="tabsfield box">
			<fieldset>
				<legend>Angaben zur Person</legend>
				<table>
					<tbody>
						<tr>
							<th><label for="person_name">Name:</label></th>
							<td><input id="person_name" cols="30" class="efeld" type="text" name="person_name" /></td>
						</tr>
						<tr>
							<th><label for="person_email">Email:</label></th>
							<td><input id="person_email" cols="30" class="efeld" type="text" name="person_email" /></td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="2"><i>Ihre pers&ouml;nlichen Daten werden nur f&uuml;r R&uuml;cksprachen gespeichert.</i></td>
						</tr>
					</tfoot>
				</table>
			</fieldset>
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
					<image id="ally-laoding" style="display: none" src="./images/loading.gif" alt="loading" />
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
			</fieldset>
			<fieldset class="banner">
				<legend>Banner</legend>
				<div class="tabs">
					<span onclick="showFields('banner-all', this)" class="selected">Alle vorhandene Banner</span>
					<span onclick="showFields('banner-new', this)">Neues Banner hochladen</span>
				</div>
				<div class="tabsfield">	
					<div id="banner-all" class="select">
					<?php $merge = array_merge($banner["assigned"], $banner["open"]); foreach($merge as $row) { ?>
						<label for="banner-sel-<? echo $row["id"] ?>" class="option">
							<input type="checkbox" name="banner[]" value="<? echo $row["id"] ?>" id="banner-sel-<? echo $row["id"] ?>" />
							<img src="<?php echo BANNER_PATH.rawurlencode($row["url"]) ?>" alt="Banner<?php echo $row["id"] ?>" />
						</label>
					<?php } if(empty($merge)) echo '<div class="empty">leer</div>' ?>
						<div class="clearer"></div>
					</div>
					<div id="banner-new" class="js-hidden">
						<input id="uploadBanner" name="uploadBanner" type="file" size="50" accept="image/*">
							<button id="buttonUpload" onclick="return imageUpload('<? echo BANNER_PATH ?>');">Hochladen</button>
							<image id="upload-laoding" style="display: none" src="./images/loading.gif" alt="loading" />
						<div id="banner-new-list" class="select hide">
							<div class="clearer"></div>
						</div>
					</div>
				</div>
				<div class="editfields">
				</div>
			</fieldset>
			<div class="buttons">
				<input type="submit" name="allySubmit" value="Allianzdaten &uuml;bermitteln" />
				<input type="reset" />
			</div>
		</div>
	</form>
</div>
<? require_once("lib/footer.php"); ?>

<?php
// //////////////////////////////////
// RUNDEN
// //////////////////////////////////

function alle_runden() {
	$sql = "SELECT `rundenID`, `rundenname` FROM `". DB_PREFIX ."runde` ORDER BY `rundenname`";
	$select = mysql_query($sql);
	$runden = array();
	echo mysql_error();
	while(list($id, $name) = mysql_fetch_row($select))
		$runden[] = array("id" => $id, "name" => $name);
	return $runden;
}

function runde_by_rundenid($rundenID) {
	$sql = "SELECT `rundenname` FROM `". DB_PREFIX ."runde` WHERE `rundenID` = '". $rundenID ."'";
	$select = mysql_query($sql);
	list($name) = mysql_fetch_row($select);
	return $name;
}

function runde_by_name($name) {
	$sql = "SELECT `r`.`rundenID`, `r`.`rundenname` FROM `". DB_PREFIX ."runde` as `r` WHERE `rundenname` = '". $name ."'";
	$res = mysql_query($sql);
	if(mysql_num_rows($res) > 0)
		return mysql_fetch_assoc($res);
	else
		return null;
}

function new_runde($name) {
	$sql = "INSERT INTO `". DB_PREFIX ."runde` (`rundenname`) VALUES ('". $name ."')";
	if(mysql_query($sql))
		return mysql_insert_id();
	else	
		return false;
}

function runden_optionen($rundenIDs) {
	$ret = "";
	$sql = "SELECT `rundenID`, `rundenname` FROM `". DB_PREFIX ."runde` ORDER BY `rundenname`";
	$select = mysql_query($sql);
	while(list($id, $name) = mysql_fetch_row($select)) {
		if(is_array($rundenIDs) && in_array($id, $rundenIDs))
			$ret .= '<option value="'. $id .'" selected>'. $name .'</option>';
		else
			$ret .= '<option value="'. $id .'">'. $name .'</option>';
	}
	return $ret;
}

// //////////////////////////////////
// Allianzen
// //////////////////////////////////

function allies_of_runde($rundenID) {
	if(intval($rundenID) > 0) {
		$sql = "SELECT `a`.`allyID`, `a`.`tag`, `a`.`name`, `b`.`url`"
			." FROM `". DB_PREFIX ."teil_der_runde` as `tdr`" 
			."  INNER JOIN `". DB_PREFIX ."ally` as `a` ON (`tdr`.`allyID` = `a`.`allyID`)"
			."  LEFT JOIN (`". DB_PREFIX ."banner_of_ally` as `ba`"
			."   INNER JOIN `". DB_PREFIX ."banner` as `b` ON (`b`.`id` = `ba`.`bannerID`))"
			."    ON (`ba`.`allyID` = `tdr`.`allyID`)"
			." WHERE `tdr`.`rundenID` = ". intval($rundenID)
			." ORDER BY `a`.`tag`";
	}
	else if ($rundenID === "all") {
		$sql = "SELECT `a`.`allyID`, `a`.`tag`, `a`.`name`, `b`.`url`"
			." FROM `". DB_PREFIX ."ally` as `a`"
			."  LEFT JOIN (`". DB_PREFIX ."banner_of_ally` as `ba`"
			."   INNER JOIN `". DB_PREFIX ."banner` as `b` ON (`b`.`id` = `ba`.`bannerID`))"
			."    ON (`ba`.`allyID` = `a`.`allyID`)"
			." ORDER BY `a`.`tag`";
	}
	if(isset($sql)) {
		$select = mysql_query($sql);
		$allies = array();
		while(list($id, $tag, $name, $url) = mysql_fetch_row($select)) {
		    if(empty($allies[$id]))
				$allies[$id] = array("tag" => $tag, "name" => $name, "urls" => array(), "runden" => runden_of_ally($id, false));
			if(isset($allies[$id]) && $url != null)
				$allies[$id]["urls"][] = $url;
		}
		return $allies;
	}
	else
		 return NULL;
}

function get_ally($allyID, $switch = false) {
	$allyID = intval($allyID);
	if($allyID > 0) {
		$sql = "SELECT `a`.`allyID`, `a`.`tag`, `a`.`name`, `b`.`id` as `bannerID`, `b`.`url`"
			." FROM `". DB_PREFIX ."ally` as `a`"
			."  LEFT JOIN (`". DB_PREFIX ."banner_of_ally` as `ba`"
			."    INNER JOIN `". DB_PREFIX ."banner` as `b` ON (`b`.`id` = `ba`.`bannerID`))"
			."   ON (`ba`.`allyID` = `a`.`allyID`)"
			." WHERE `a`.`allyID` = '". $allyID ."'";
//		echo $sql;
		$result = mysql_query($sql);
		if(mysql_num_rows($result) > 0) {
			while(list($id, $tag, $name, $bannerID, $url) = mysql_fetch_row($result))
				if(empty($ally))
					$ally = array("id" => $id, "tag" => $tag, "name" => $name, "urls" => array($bannerID => $url), "runden" => runden_of_ally($id, $switch));
				else
					$ally["urls"][$bannerID] = $url;
			return $ally;
		}
		else
			return NULL;
		
	}
	else
		return NULL;
}

/**
Gibt zu einer allianzID die Runden zurueck
 	@param int $allyID - ID der Allianz
	@param boolean $switch - true, dann werden nur die Ids zurÃ¼ckgegeben, bei false nur die Rundennamen
	@return array mit allen Runden der Allianz
 */
function runden_of_ally($allyID, $switch) {
	$ret = array();
	$sql = "SELECT `". DB_PREFIX ."runde`.`rundenID`, `". DB_PREFIX ."runde`.`rundenname` FROM `". DB_PREFIX ."runde`, `". DB_PREFIX ."teil_der_runde` WHERE `". DB_PREFIX ."teil_der_runde`.`allyID` = '". $allyID ."' AND `". DB_PREFIX ."runde`.`rundenID` = `". DB_PREFIX ."teil_der_runde`.`rundenID`";
	$select = mysql_query($sql);
	while(list($id, $name) = mysql_fetch_row($select)) {
		if($switch)
			$ret[] = $id;
		else
			$ret[$id] = $name;
	}
	return $ret;
}

/**
 * Sucht eine Allainz nach Tag und Name.
 * 
 * @param string $tag - Tag (mit Klammern []) der gesuchten Allianz.
 * @param string $name - Name der gesuchten Allianz. (Bereits mit htmlspecialchars formatiert)
 * @return array - Daten der gesuchten Allianz, oder null wenn Allianz nicht gefunden.
 */
function search_ally($tag, $name) {
	$sql = "SELECT `allyID` FROM `". DB_PREFIX ."ally` WHERE `tag` = '". $tag ."' AND `name` = '". $name ."'";
	$res = mysql_query($sql);
	
	if(mysql_num_rows($res) > 0) {
		list($allyID) = mysql_fetch_row($res);
		return get_ally($allyID);
	}
	else
		return null;
}

function new_ally($tag, $name, $banner, $runden) {
	
	$sql = "INSERT INTO `". DB_PREFIX ."ally` (`tag`, `name`) VALUES ('". $tag ."', '". $name ."')";
	$res = mysql_query($sql);
	if(!$res)
		return false;
	$allyID = mysql_insert_id();
	
	$sql = "INSERT INTO `". DB_PREFIX ."banner_of_ally` VALUES";
	foreach($banner as $id)
		$sql.=" ('". $id ."', '". $allyID ."'),";
	$res = mysql_query(substr($sql,0,-1));
	
	$sql = "INSERT INTO `". DB_PREFIX ."teil_der_runde` VALUES";
	foreach($runden as $id)
		$sql.=" ('". $allyID ."', '". $id ."'),";
	$res = mysql_query(substr($sql,0,-1));
	
	return $allyID;
}

function edit_ally($allyID, $tag, $name, $banner, $runden) {
	
	$sql = "UPDATE `". DB_PREFIX ."ally` SET `tag` = '". $tag ."', `name` = '". $name ."' WHERE `allyID` = '". $allyID ."'";
	$res = mysql_query($sql);
	
	mysqL_query("DELETE FROM `". DB_PREFIX ."banner_of_ally` WHERE `allyID` = '". $allyID ."'");
	$sql = "INSERT INTO `". DB_PREFIX ."banner_of_ally` VALUES";
	foreach($banner as $id)
		$sql.=" ('". $id ."', '". $allyID ."'),";
	$res = mysql_query(substr($sql,0,-1));
	
	mysqL_query("DELETE FROM `". DB_PREFIX ."teil_der_runde` WHERE `allyID` = '". $allyID ."'");
	$sql = "INSERT INTO `". DB_PREFIX ."teil_der_runde` VALUES";
	foreach($runden as $id)
		$sql.=" ('". $allyID ."', '". $id ."'),";
	$res = mysql_query(substr($sql,0,-1));	
}

// /////////////////////////
// Banner
// /////////////////////////

/**
 * @return An array of all existing banner, grouped by status (open, assigned)
 * 
 */
function get_all_banner() {
	$sql = "SELECT `b`.`id`, `b`.`url`, `ba`.`allyID`, `a`.`tag`, `a`.`name`"
		." FROM `". DB_PREFIX ."banner` as `b`"
		."  LEFT JOIN (`". DB_PREFIX ."banner_of_ally` as `ba`"
		."    INNER JOIN `". DB_PREFIX ."ally` as `a` ON (`ba`.`allyID` = `a`.`allyID`))"
		."   ON (`b`.`id` = `ba`.`bannerID`)"
		." ORDER BY `b`.`id`";
	$res = mysql_query($sql) OR DIE (mysql_error());
	$ret = array("open" => array(), "assigned" => array());
	while($row = mysql_fetch_assoc($res)) {
		if($row["allyID"] == 0)
			$ret["open"][$row["id"]] = $row;
		else if($row["allyID"] > 0) {
			$allyData = array("allyID" => $row["allyID"], "tag" => $row["tag"], "name" => $row["name"]);
			if(isset($ret["assigned"][$row["id"]]))
				$ret["assigned"][$row["id"]]["allies"][$row["allyID"]] = $allyData;
			else
				$ret["assigned"][$row["id"]] = array("id" => $row["id"], "url" => $row["url"], "allies" => array($row["allyID"] => $allyData));
		}
	}
	return $ret;
}

function get_banner($id) {
	$sql = "SELECT * FROM `". DB_PREFIX ."banner` WHERE `id` = '". $id ."'";
	$res = mysql_query($sql) OR DIE (mysql_error());
	if(mysql_num_rows($res) === 1)
		return mysql_fetch_assoc($res);
	else
		return null;	
}

function upload($file, $ally_tag, $ally_name) {
	$path_info = pathinfo($file["name"]);
	$ally_tag = "[". save_filename(substr($ally_tag, 1, -1)) ."]";
	$ally_name = save_filename($ally_name);
	$filename = $ally_tag ." ". $ally_name .".". $path_info["extension"];
	if(file_exists(BANNER_PATH.$filename))
		$filename = find_free_filename($filename);
	move_uploaded_file($file["tmp_name"],BANNER_PATH.stripcslashes($filename));
	@chmod(BANNER_PATH.$filename,0755);
	$sql = "INSERT INTO `". DB_PREFIX ."banner` (`url`) VALUES ('". $filename ."')";
	$exe = mysql_query($sql);
	$id = mysql_insert_id();
	return $id;
}

function find_free_filename($filename) {
	$count = 1; $path_info = pathinfo($filename);
	$basename_len = strlen($path_info["basename"])-strlen($path_info["extension"])-1;
	$filename = substr($path_info["basename"], 0, $basename_len).$count++.".".$path_info["extension"];
	do{
		$path_info = pathinfo($filename);
		$filename = substr($path_info["basename"], 0, $basename_len);
		$filename = $filename.$count++.".".$path_info["extension"];
	} while(file_exists(BANNER_PATH.$filename));
	return $filename;
}

function save_filename($str) {
	$from = array("Ä", "Ö", "Ü", "ä", "ö", "ü", "ß");
	$to = array("Ae", "Oe", "Ue", "ae", "oe", "ue", "ss");
	return str_replace($from, $to, $str);
} 

// /////////////////////////
// Messages
// /////////////////////////
function add_message($msg) {
	$sql = "INSERT INTO `". DB_PREFIX ."message` (`message`) VALUES ('". $msg ."')";
	mysql_query($sql);
}
function get_messages() {
	$sql = "SELECT * FROM `". DB_PREFIX ."message`";
	$res = mysql_query($sql);
	while($row = mysql_fetch_assoc($res))
		$ret[] = $row;
	return $ret;
}
function delete_message($id) {
	$id = intval($id);
	$sql = "DELETE FROM `". DB_PREFIX ."message` WHERE `id` = '". $id ."'";
	if(mysql_query($sql))
		return true;
	else
		return false;
}

?>

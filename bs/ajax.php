<?php
if(empty($_SERVER["HTTP_REFERER"])) {
	header("HTTP/1.0 403 Forbidden");
	DIE("Forbidden");
}
	
$url = parse_url($_SERVER["HTTP_REFERER"]);
if($url["host"] !== $_SERVER["HTTP_HOST"]) {
	header("HTTP/1.0 403 Forbidden");
	DIE("Forbidden");
}

// header('Content-type: application/json');

if(empty($_GET["action"])) 
	DIE(json_encode(array("error" => "IMPLEMENTATION ERROR: No action specified.")));
	

define("UPLOAD_BANNER_ID", 'uploadBanner');

require_once("lib/constants.php");
require_once("lib/connect.php");
require_once("lib/functions.php");

switch($_GET["action"]) {
	case "upload" : doUpload(); break;
	case "newRound" : createRound(); break;
	case "getAlly" : getAlly(); break;
	default: DIE(json_encode(array("error" => "IMPLEMENTATION ERROR: Action ". $_GET["action"] ." unknown.")));
}

function doUpload() {
	// Metadaten zu Banner angegeben?
	if(empty($_GET["tag"]) || empty($_GET["name"]))
		DIE(json_encode(array("error" => "FORM VALIDATION: Kein Allianzname oder kein Allianztag angegeben.")));
	
	// Datei hochgeladen?
	if(empty($_FILES[UPLOAD_BANNER_ID]))
		DIE(json_encode(array("error" => "FORM VALIDATION: Keine Datei zum Hochladen ausgew&aauml;hlt.")));
		
	// Fehler beim upload
	if(isset($_FILES[UPLOAD_BANNER_ID]['error']) && intval($_FILES[UPLOAD_BANNER_ID]['error']) > 0) {
		switch($_FILES[UPLOAD_BANNER_ID]['error'])
		{

			case '1':
				$error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
				break;
			case '2':
				$error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
				break;
			case '3':
				$error = 'The uploaded file was only partially uploaded';
				break;
			case '4':
				$error = 'No file was uploaded.';
				break;

			case '6':
				$error = 'Missing a temporary folder';
				break;
			case '7':
				$error = 'Failed to write file to disk';
				break;
			case '8':
				$error = 'File upload stopped by extension';
				break;
			case '999':
			default:
				$error = 'No error code avaiable';
		}
		DIE(json_encode(array("error" => "UPLOAD ERROR: ". $error .".")));
	}
	
	$tmp_filename = $_FILES[UPLOAD_BANNER_ID]['tmp_name'];
	$img_size = getimagesize($tmp_filename);
	
	// Datei ist Bild?
	if(!is_array($img_size))
		DIE(json_encode(array("error" => "UPLOAD ERROR: Die hochgeladene Datei ist kein Bild.")));
	
	// Datei hat richtige Größe?
	if($img_size[0] != BANNER_WIDTH || $img_size[1] != BANNER_HEIGHT)
		DIE(json_encode(array("error" => "UPLOAD ERROR: Die hochgeladene Datei hat nicht die richtige Gr&ouml;&szlig;e. (Breite: "+ BANNER_WIDTH +", H&ouml;he: "+ BANNER_HEIGHT +")")));
	
	// Allright, copy file and return info
	$id = upload($_FILES[UPLOAD_BANNER_ID], $_GET["tag"], $_GET["name"]);
	$banner = get_banner($id);
	echo '{"success": true, id: "'. $id .'", "filename": "'. rawurlencode($banner["url"]) .'", "path": "'. rawurlencode(BANNER_PATH.$banner["url"]) .'"}';
}

function createRound() {
	$name = $_POST["name"];
	if(empty($name))
		echo '{"error": "Kein Name angegeben."}';
	elseif(runde_by_name($name) != null)
		echo '{"error": "Runde mit diesem Namen ('. $name .') existiert bereits."}';
	else {
		$id = new_runde($name);
		if($id === false)
			echo '{"error": "IMPLEMENTATION ERROR: Error during query execution."}';
		else
			echo '{"success": true, "id": '. $id .', "name": "'. $name .'"}';
	}
}

function getAlly() {
	$id = intval($_POST["id"]);
	
	$allyData = get_ally($id, true);
	if($allyData == null)
		echo '{"error": "Allianz nicht bekannt"}';
	else {
		$allyData["success"] = true;
		echo json_encode($allyData);
	}
}
?>

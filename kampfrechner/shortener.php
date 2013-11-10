<?php
// echo "http://is.gd/api.php?longurl=". $_GET["longurl"]; // Debug
echo file_get_contents("http://is.gd/api.php?longurl=". stripslashes($_GET["longurl"]));
?>
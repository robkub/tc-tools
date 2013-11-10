<?php
  $db_server = "localhost";
  $db_name = "bs";
  $db_user = "bs";
  $db_passwort = "bspw";
  // when change this, then need to edit schema.sql
  define("DB_PREFIX", "bs_");

  $db = @MYSQL_CONNECT($db_server,$db_user,$db_passwort) or die ("Konnte keine Verbindung zur Datenbank herstellen");
  $db_select = @MYSQL_SELECT_DB($db_name);
?>

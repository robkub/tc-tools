# Bannersammlung

Die Bannersammlung kann genutzt werden um ein Archiv von the Crown Banner anzulegen

## Vorraussetzung

Funktiomniert sicher mit

* PHP 4+
* Mysql 5+
* Apache 2

## Installation

1. Kopiere alle Dateien in das Webserver-Verzeichnis
2. Füge das Sql-Skript "schema.sql" im Mysql-Server aus
3. Gebe in "lib/connect.php" die Zugangsdaten zur MySQL-Datenbank an
  * "lib/constants.php" sollte nicht angepasst werden
4. Füge ein Verzeichnisschutz zum Verzeichnis "admin" hinzu

   Siehe [Apache Doc](http://httpd.apache.org/docs/2.2/howto/auth.html)
   
   * Gehe zu *http://host/admin/edit.php* für Administration

5. Achte darauf das alle Schreibrechte auf das Verzeichnis "pics" haben

## Daten

Siehe *Downloads* für Daten.



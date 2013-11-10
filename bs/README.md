# Bannersammlung

Die Bannersammlung kann genutzt werden um ein Archiv von the Crown Banner anzulegen

## Vorraussetzung

Funktiomniert sicher mit

* PHP 4+
* Mysql 5+
* Apache 2

## Installation

1. Kopiere alle Dateien in das Webserver-Verzeichnis
2. Führe das Sql-Skript "schema.sql" in der mysql-Datenbank aus
3. Gebe in "lib/connect.php" die Zugangsdaten zur MySQL-Datenbank an
   * "lib/constants.php" sollte nicht angepasst werden
4. Füge ein Verzeichnisschutz zum Verzeichnis "admin" hinzu.  
   Siehe [Apache Doc](http://httpd.apache.org/docs/2.2/howto/auth.html)
   
   * Gehe zu *http://host/admin/edit.php* für die Administration

5. Achte darauf das alle Schreibrechte (0666 oder 0777) auf das Verzeichnis "pics" haben

## Daten

Siehe *../data* für Daten.

## Beitragende (vor release auf github)

* *Smoke-a-lot* - Initiale Idee und Umsetzung
* *Lord_Bloodwin* - Design
* *robkub* - Erweiterung der Implementierung

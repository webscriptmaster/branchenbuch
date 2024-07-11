<?php
/*
include('sql.inc');

echo "Verbinde mit Datenbank...";
define('DB_HOST', '78.46.39.71');
define('DB_NAME', 'stosch');
define('DB_USER', 'stosch');
define('DB_PASS', 'Sto2662Roc');



// Globalisieren der Connection-Variable,
// damit sql.inc darauf zugreifen kann
global $connection;
$connection = mysql_connect(DB_HOST, DB_USER, DB_PASS);

// Wenn Verbindung zum Server erfolgreich, mit Datenbank verbinden
if ($connection) {
	$database = mysql_select_db(DB_NAME, $connection);
} else {
	echo "Fehler\n";
}

$geocode = $_REQUEST['x'].' E, '.$_REQUEST['y'].' N';

$sql = "UPDATE t_com_firma2 SET geocode='$geocode' WHERE id='".$_REQUEST['dbid']."'";
$request = new sql_query($sql);
*/
?>
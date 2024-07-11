<?php 

error_reporting(E_ALL);

include 'inc/sql.inc';

if ($_REQUEST['validierung']=='1') {
$callernr = mysql_real_escape_string($_REQUEST['caller']);

if (strpos($callernr, '49')===0) {
    $callernr2 = '0'.substr($callernr, 2);
}

if ($callernr2)
    $sql = "SELECT * FROM usr_entry WHERE telefon='".$callernr."' OR telefon='".$callernr2."'  ORDER BY modate DESC LIMIT 1";
else
    $sql = "SELECT * FROM usr_entry WHERE telefon='".$callernr."' ORDER BY modate DESC LIMIT 1";

$request = new sql_query($sql);
$valRequest = 0;
if($result=$request->fetch_object()) {
if($result->verified < 1) {
$valRequest = 1;
if($result->valcode == $_REQUEST['pin']) {
$valid=1;
}else{
$valid=-1;
}
$sql = "UPDATE usr_entry SET verified='".$valid."' WHERE id='".$result->id."'";
$update = new sql_query($sql);
}
}
$sql = "INSERT INTO usr_entry_vallog SET tstamp='".mysql_real_escape_string($_REQUEST['timestamp'])."', caller='".mysql_real_escape_string($_REQUEST['caller'])."', pin='".mysql_real_escape_string($_REQUEST['pin'])."',
	validation='".$valid."', val_request='".$valRequest."'";
$insert = new sql_query($sql);
}
if(isset($_REQUEST['a_nummer']) && isset($_REQUEST['billsec_ab'])) {
$sql = "INSERT INTO number_evn SET `start`='".mysql_real_escape_string($_REQUEST['start'])."', `ende`='".mysql_real_escape_string($_REQUEST['ende'])."',
        a_nummer='".mysql_real_escape_string($_REQUEST['a_nummer'])."', b_nummer='".mysql_real_escape_string($_REQUEST['b_nummer'])."', c_nummer='".mysql_real_escape_string($_REQUEST['c_nummer'])."',
	billsec_ab='".mysql_real_escape_string($_REQUEST['billsec_ab'])."', billsec_bc='".mysql_real_escape_string($_REQUEST['billsec_bc'])."' ";
$insert = new sql_query($sql);
}
ob_start();
print_r($_REQUEST);
$request = mysql_real_escape_string(ob_get_contents());
ob_end_clean();

$sql = "INSERT INTO sample (reqval) VALUES ('$request')";
$query = new sql_query($sql);
?>
<?php

require_once(dirname(__FILE__)."/configs/config.inc.php");


set_time_limit(0);


$handle = fopen ("kunden.csv","r");
$c=0;

echo "starte Kundenimport<br>";
flush();ob_flush();

while ( ($data = fgetcsv ($handle, 4096, ";", '"')) !== FALSE ) {
	$c++;
	if(count($data)<>1){
		
		if($c>1){
    		$debTime = microtime(true);
			$check=$GLOBALS['dbapi']->get_member($data[0]);
			echo "zeit: ".microtime(true)-$debTime;
			if(empty($check)){
			
				echo "NF".$data[0]."<br>";
				flush();ob_flush();
				$arrData = array('id'=>$data[0], 
							'password'=>$data[1],
							'firma'=>$data[2],
							'app_vorname'=>$data[3],
							'app_name'=>$data[4],
							'app_anrede'=>$data[5],
							'strasse'=>$data[6],
							'ort'=>$data[7],
							'plz'=>$data[8],
							'tel'=>$data[9],
							'fax'=>$data[10],
							'mobil'=>$data[11],
							'email'=>$data[12],
							'www_shop'=>$data[13],
							'www'=>$data[14],
							'firmenprofil'=>$data[15],
							'logo'=>$data[16],
							'bildfirma'=>$data[17],
							'firmennews'=>$data[18],
							'eintr_art'=>$data[19],
							'rfirma'=>$data[20],
							'rstrasse'=>$data[21],
							'rplz'=>$data[22],
							'rort'=>$data[23],
							'emailnotpublic'=>$data[24],
							'aktiv'=>$data[25],
							'indate'=>substr($data[26], 6, 4).'-'.substr($data[26], 3, 2).'-'.substr($data[26], 0,2),
							'vpn'=>$data[27],
							'neu'=>$data[28],
							'branchen'=>$data[29],
							'metawords'=>$data[30],
							'affilinet'=>$data[31],
							'domain'=>$data[32],
							'EINSTGEBUEHR'=>$data[33],
							'LASTRMAIL'=>$data[34],
							'OID'=>$data[35],
							'BID'=>$data[36],
							'X'=>$data[37]);
				DBAPI::store_kunden_neu_import($arrData);
			} else {
				echo "GF".$data[0]."<br>";				
				flush();ob_flush();
			}
		}
	} else {
		$info_upload="Feld Fehler!";
		break;
	}
}
fclose ($handle);

echo "all:".$c."<br>";
flush();ob_flush();



/*
$res=DBAPI::get_all_kunden_neu_import();

$out=array();
for($i=0;$i<=count($res)-1;$i++){
	$check=DBAPI::get_member($res[$i]['id']);
	if(empty($check)){
		$out[]=$res[$i]['id'];
		echo "Fehlt:".$res[$i]['id']."<br>";
		flush();ob_flush();
	} else {
		echo $res[$i]['id']."<br>";
		flush();ob_flush();
	}
}
print_r($out);
*/

?>
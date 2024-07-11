<?php
//=========================================
// Script	: Auktion
// File		: index.php
// Version	: 0.1
// Author	: Matthias Franke
// Email	: info@matthiasfranke.com
// Website	: http://www.matthiasfranke.com
//=========================================
// Copyright (c) 2007 Matthias Franke
//=========================================

function sys_make_member_dir($member_id) {

	umask(007);
	if (strlen($member_id)<6) {
		return false;
	}
	$path ="/".substr($member_id,0,2);
	if (!is_dir(MB_DIR.$path)) {
		if (!mkdir(MB_DIR.$path,0777)) {
			return false;
		} else {
			chmod(MB_DIR.$path,0777);
		}
	}
	$path.="/".substr($member_id,2,2);
	if (!is_dir(MB_DIR.$path)) {
		if (!mkdir(MB_DIR.$path,0777)) {
			return false;
		} else {
			chmod(MB_DIR.$path,0777);
		}
	}
	$path.="/".substr($member_id,4,2);
	if (!is_dir(MB_DIR.$path)) {
		if (!mkdir(MB_DIR.$path,0777) ) {
			return false;
		} else {
			chmod(MB_DIR.$path,0777);
		}
	}

	if (!is_dir(MB_DIR.$path.'/logs')) {
		if (!mkdir(MB_DIR.$path.'/logs',0777)) {
			return false;
		} else {
			chmod(MB_DIR.$path.'/logs',0777);
		}
	}
	if (!is_dir(MB_DIR.$path.'/images')) {
		if (!mkdir(MB_DIR.$path.'/images',0777)) {
			return false;
		} else {
			chmod(MB_DIR.$path.'/images',0777);
		}
	}
	/*
	if (!is_dir(MB_DIR.$path.'/mail')) {
	if (!mkdir(MB_DIR.$path.'/mail',0777)) {
	return false;
	}
	}
	*/
	return true;
}

function sys_get_member_dir($member_id) {

	if (strlen($member_id)<6) {
		return false;
	}
	$path ="/".substr($member_id,0,2);
	$path.="/".substr($member_id,2,2);
	$path.="/".substr($member_id,4,2);

	return MB_DIR.$path;
}

function sys_make_dir($member_id,$prefix) {

	umask(007);
	if (strlen($member_id)<6) {
		return false;
	}
	$path ="/".substr($member_id,0,2);
	if (!is_dir($prefix.$path)) {
		if (!mkdir($prefix.$path,0777)) {
			return false;
		}
	}
	$path.="/".substr($member_id,2,2);
	if (!is_dir($prefix.$path)) {
		if (!mkdir($prefix.$path,0777)) {
			return false;
		}
	}
	$path.="/".substr($member_id,4,2);
	if (!is_dir($prefix.$path)) {
		if (!mkdir($prefix.$path,0777) ) {
			return false;
		}
	}

	return true;
}

function sys_get_dir($member_id,$prefix) {

	if (strlen($member_id)<6) {
		return false;
	}
	$path ="/".substr($member_id,0,2);
	$path.="/".substr($member_id,2,2);
	$path.="/".substr($member_id,4,2);

	return $prefix.$path;
}

/**
* Schreibt Informationen in Logdatein des Kunden
*
* @param string $target_member Member ID des Kunden in dessen Log geschrieben wird
* @param string $event Event ID
* @param string $descr Beschreibungstext
* @param string $from_id Member ID des Kunden der die Gelogte Aktion ausgelöst hat
*/

function sys_user_log($target_member,$event,$descr="",$from_id="") {

	$auth = new AUTH();

	$uname=($auth->isLoggedin())?$auth->member_id():session_id();

	if (!empty($from_id)) {
		$uname=$from_id;
	}
	if (!empty($target_member)) {
		$log_file=sys_get_member_dir($target_member).USER_LOG;
	} else {
		$log_file=ERR_LOG;
	}
	if (empty($event)) {
		$event="(0)";
	} else {
		$event=(defined($event))?"(".constant($event).")":"($event)";
	}
	error_log(date("Y.m.d H:i:s")." [$uname]: $event $descr\n",3,$log_file);

}

/**
* Liefert die Menge von bestimmten Logeinträgen eines Kunden
*
* @param string $member_id Member ID des Kunden
* @param string $event Event ID
* @param string $dvon Datum von
* @param string $dbis Datum bis
* @param string $user Kunde der den Log ausgelöst hat
* @param string $only_count definiert den Rückgabewert
* @return string Menge/Daten als array
*/

function sys_count_usrlog($member_id,$event,$dvon,$dbis,$user,$only_count=true)
{
	$co=0;$match_v=false;$match_b=false;$match_e=false;
	$path=sys_get_member_dir($member_id).USER_LOG;
	if (file_exists($path)) {
		$fh = fopen ($path, "r");
		while (!feof($fh)) {
			$buffer = fgets($fh, 300);
			list($datum,$time,$uid,$txt,$txt2)=explode(' ',$buffer,5);
			if (isset($dvon)) {
				$match_v=(strnatcmp($datum,$dvon)>=0)?true:false;
			}
			if (isset($dbis)) {
				$match_b=(strnatcmp($datum,$dbis)<=0)?true:false;
			}
			if (isset($user)) {
				$match_i=(strpos($uid,"[$user]")!==false)?true:false;
			}
			if (isset($event)) {
				$match_e=false;
				if (!is_array($event)) {$event=array($event);}
				foreach ($event as $value) {
					if (strpos($txt,"($value)")!==false) {$match_e=true;}
				}
			}
			if (!(isset($dvon) xor $match_v) && !(isset($dbis) xor $match_b) && !(isset($event) xor $match_e) && !(isset($user) xor $match_i)) {
				if ($only_count) {
					$co++;
				} else {
					if (!empty($datum)) {
						$arr[]=array('datum'=>str_replace('.','-',$datum),'zeit'=>$time,'uid'=>str_parse('[',']',$uid),'event'=>str_parse('(',')',$txt),'text'=>$txt2);
					}
				}
			}
		}
		fclose($fh);
		return ($only_count)?$co:$arr;
	} else {
		return false;
	}
}

/**
* Liefert den Inhalt zwischen zwei beliebigen Werten in einer Zeichenkette
*
* @param string $tag1 Wert 1
* @param string $tag2 Wert 2
* @param string $str Zeichenkette
* @return string
*/

function str_parse($tag1,$tag2,$str){
    
    if($tag1!=""){
        
        $var1=strstr($str,$tag1);
        $len1=strlen($tag1);
        $var1=substr($var1,$len1);
        
        return substr($var1,0,strpos($var1,$tag2));
        
    } else {
        
        return substr($str,0,strpos($str,$tag2));
    }
    
}

function sortByField($multArray,$sortField,$sort){
	if($sort=="desc"){
		$desc=true;
	} else {
		$desc=false;
	}
	$tmpKey='';
	$ResArray=array();

	$maIndex=@array_keys($multArray);
	$maSize=count($multArray)-1;

	for($i=0; $i < $maSize ; $i++) {

		$minElement=$i;
		$tempMin=$multArray[$maIndex[$i]][$sortField];
		$tmpKey=$maIndex[$i];

		for($j=$i+1; $j <= $maSize; $j++)
		if($multArray[$maIndex[$j]][$sortField] < $tempMin ) {
			$minElement=$j;
			$tmpKey=$maIndex[$j];
			$tempMin=$multArray[$maIndex[$j]][$sortField];

		}
		$maIndex[$minElement]=$maIndex[$i];
		$maIndex[$i]=$tmpKey;
	}

	if($desc)
	for($j=0;$j<=$maSize;$j++)
	$ResArray[$maIndex[$j]]=$multArray[$maIndex[$j]];
	else
	for($j=$maSize;$j>=0;$j--)
	$ResArray[$maIndex[$j]]=$multArray[$maIndex[$j]];

	foreach ($ResArray as $FieldArray){
		$out[]=$FieldArray;
	}

	return $out;
}

function rebuildUrl($url = false, $args = array()) {
	if(empty($url)) $url = "https://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
	list($path, $query) = split("\?", $url);
	parse_str($query, $params);
	foreach($args as $k => $v) {
		if($v == NULL) {
			unset($params[$k]);
		} else {
			$params[$k] = $v;
		}
	}
	//return $path.'?'.toQueryString($params, "&");
	return $path.'?'.http_build_query($params);
}


function toQueryString($params, $glue = " ", $quote = "", $prefix = "")
{
	$temp = array();
	foreach($params as $key => $value)
	{
    if(!is_string($value) && !is_numeric($value)) continue;
		if(is_string($value)) {
			$value = urlencode($value);
		}
		$temp[] = $key."=".$quote.$value.$quote;
	}
	return join($glue, $temp);
}

if (!function_exists('http_build_query')) {
function http_build_query($data, $prefix='', $sep='', $key='') {
    $ret = array();
    foreach ((array)$data as $k => $v) {
        if (is_int($k) && $prefix != null) $k = urlencode($prefix . $k);
        if (!empty($key)) $k = $key.'['.urlencode($k).']';
       
        if (is_array($v) || is_object($v))
            array_push($ret, http_build_query($v, '', $sep, $k));
        else    array_push($ret, $k.'='.urlencode($v));
    }

    if (empty($sep)) $sep = ini_get('arg_separator.output');
    return implode($sep, $ret);
}}
?>

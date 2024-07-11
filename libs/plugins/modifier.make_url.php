<?php

/*
*/
function smarty_modifier_make_url($str)
{
	$str = strtolower($str);
	$str = preg_replace('/ä/','ae',$str);
	$str = preg_replace('/ü/','ue',$str);
	$str = preg_replace('/ö/','oe',$str);
	$str = preg_replace('/ß/','ss',$str);
	$str = preg_replace("/[ _]/", "-", $str);
	$str = preg_replace("/[^a-z0-9-_]/", "", $str);
	return $str;
}

/* vim: set expandtab: */

?>

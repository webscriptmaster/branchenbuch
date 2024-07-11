<?php

/*
*/
function smarty_modifier_make_url($str)
{
	$str = strtolower($str);
	$str = preg_replace('/�/','ae',$str);
	$str = preg_replace('/�/','ue',$str);
	$str = preg_replace('/�/','oe',$str);
	$str = preg_replace('/�/','ss',$str);
	$str = preg_replace("/[ _]/", "-", $str);
	$str = preg_replace("/[^a-z0-9-_]/", "", $str);
	return $str;
}

/* vim: set expandtab: */

?>

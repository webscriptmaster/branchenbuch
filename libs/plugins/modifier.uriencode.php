<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     upper
 * Purpose:  convert string to uppercase
 * -------------------------------------------------------------
 */
function smarty_modifier_uriencode($string)
{
	$string = strtolower($string);
	$string = str_replace('ä','ae',$string);
	$string = str_replace('ü','ue',$string);
	$string = str_replace('ö','oe',$string);
	$string = str_replace('Ä','ae',$string);
	$string = str_replace('Ü','ue',$string);
	$string = str_replace('Ö','oe',$string);
	$string = str_replace('ß','ss',$string);
	return urlencode($string);
}

?>

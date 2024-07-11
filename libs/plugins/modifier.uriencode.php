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
	$string = str_replace('�','ae',$string);
	$string = str_replace('�','ue',$string);
	$string = str_replace('�','oe',$string);
	$string = str_replace('�','ae',$string);
	$string = str_replace('�','ue',$string);
	$string = str_replace('�','oe',$string);
	$string = str_replace('�','ss',$string);
	return urlencode($string);
}

?>

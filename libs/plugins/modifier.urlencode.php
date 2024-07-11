<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     upper
 * Purpose:  convert string to uppercase
 * -------------------------------------------------------------
 */
function smarty_modifier_urlencode($string)
{
	$string = preg_replace('/referer=[^&]*/', '', $string);
	return urlencode($string);
}

?>

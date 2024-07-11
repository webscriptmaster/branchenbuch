<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     upper
 * Purpose:  convert string to uppercase
 * -------------------------------------------------------------
 */
function smarty_modifier_delbadhtmlchars($string)
{
	$string = str_replace('"',"'",$string);
	return $string;
}

?>

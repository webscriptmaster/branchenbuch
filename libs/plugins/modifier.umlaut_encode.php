<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty umlaut modifier plugin
 *
 */
function smarty_modifier_umlaut_encode($string)
{
	$string = str_replace('�', 'AE', $string);
	$string = str_replace('�', 'OE', $string);
	$string = str_replace('�', 'UE', $string);
	return $string;
}

?>

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
	$string = str_replace('Ä', 'AE', $string);
	$string = str_replace('Ö', 'OE', $string);
	$string = str_replace('Ü', 'UE', $string);
	return $string;
}

?>

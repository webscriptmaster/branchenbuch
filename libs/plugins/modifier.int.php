<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     int
 * Version:  1.0
 * Date:     Feb 24, 2003
 * Author:	 Monte Ohrt <monte@ispi.net>
 * Purpose:  catentate a value to a variable
 * Input:    string to catenate
 * Example:  {$var|const}
 * -------------------------------------------------------------
 */
function smarty_modifier_int($string)
{
	return (int)$string;
}

/* vim: set expandtab: */

?>

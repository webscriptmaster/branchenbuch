<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     const
 * Version:  1.0
 * Date:     Feb 24, 2003
 * Author:	 Monte Ohrt <monte@ispi.net>
 * Purpose:  catentate a value to a variable
 * Input:    string to catenate
 * Example:  {$var|const}
 * -------------------------------------------------------------
 */
function smarty_modifier_const($string)
{
	return constant($string);
}

/* vim: set expandtab: */

?>

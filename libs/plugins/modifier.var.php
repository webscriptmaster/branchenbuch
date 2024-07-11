<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     var_group
 * Version:  1.0
 * Date:     Feb 24, 2003
 * Author:	 Monte Ohrt <monte@ispi.net>
 * Purpose:  catentate a value to a variable
 * Input:    string to catenate
 * Example:  {$var|order_by:"field"}
 * -------------------------------------------------------------
 */
function smarty_modifier_var($var_id, $iso=DEFAULT_LANG)
{	
	if(is_numeric($var_id)){
		return $GLOBALS['dbapi']->get_var_txt($var_id, $iso);
	} else {
		return $var_id;
	}
}

/* vim: set expandtab: */

?>

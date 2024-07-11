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
function smarty_modifier_mailtpl_member_fetch($string,$id)
{
	$smarty=new Smarty();
	$smarty->assign($GLOBALS['dbapi']->get_member($id));
	return smarty_function_eval(array('var' => $string), $smarty);
}

/* vim: set expandtab: */

?>

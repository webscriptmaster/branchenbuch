<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

function smarty_function_setandshowtime($params, &$smarty)
{
	if (!$GLOBALS["_debugTime"])
		$GLOBALS["_debugTime"] = microtime(true);
		
	return "Zeit: ".(microtime(true) - $GLOBALS["_debugTime"]);
}

/* vim: set expandtab: */

?>

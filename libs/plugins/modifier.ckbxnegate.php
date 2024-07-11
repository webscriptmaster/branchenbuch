<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


function smarty_modifier_ckbxnegate($boolean)
{
	if($boolean) {
		return '';		
	} else {
		return 'checked="checked"';
	}
}

/* vim: set expandtab: */

?>

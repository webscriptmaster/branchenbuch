<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     lower
 * Purpose:  convert string to lowercase
 * -------------------------------------------------------------
 */
function smarty_modifier_branche($id)
{
	
	return DBApi::get_branche_name_neu($id);
}

?>

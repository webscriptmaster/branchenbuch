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
function smarty_modifier_log_event($id)
{
	switch ($id){
		case LOG_MEMBER_LOAD_PIC:
		return "Bild hochladen";
		break;
		
		case LOG_MEMBER_DEL_PIC:
		return "Blid löschen";
		break;
	}
}

/* vim: set expandtab: */

?>

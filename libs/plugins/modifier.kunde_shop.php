<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     iso
 * Purpose:  Replace all repeated spaces, newlines, tabs
 *           with a single space or supplied replacement string.
 * Example:  {$var|strip} {$var|strip:"&nbsp;"}
 * Author:   Monte Ohrt <monte@ispi.net>
 * Version:  1.0
 * Date:     September 25th, 2002
 * -------------------------------------------------------------
 */
function smarty_modifier_kunde_shop($id)
{
	$res=DBAPI::get_member($id);
	
	if(strstr($res['www_shop'],'http://')){
		return $res['www_shop'];	
	} else {
		return 'http://'.$res['www_shop'];
	}
}

?>

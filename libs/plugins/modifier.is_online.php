<?php

/*
* Smarty plugin
* -------------------------------------------------------------
* Type:     modifier
* Name:     nl2br
* Version:  1.0
* Date:     Feb 26, 2003
* Author:	 Monte Ohrt <monte@ispi.net>
* Purpose:  convert \r\n, \r or \n to <br />
* Input:    contents = contents to replace
*           preceed_test = if true, includes preceeding break tags
*           in replacement
* Example:  {$text|nl2br}
* -------------------------------------------------------------
*/
function smarty_modifier_is_online($id)
{
	$res=$GLOBALS['dbapi']->get_member($id);
	if(empty($res['member_lastactiv'])) return false;
	if(time()-$res['member_lastactiv'] <= MEMBER_LAST_ACTIV_TIME) {
		return true;
	}
	return false;
}

/* vim: set expandtab: */

?>

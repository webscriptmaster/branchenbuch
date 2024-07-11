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
function smarty_modifier_status_info($id)
{
	if($id==1) {
		return '<div class="status_info_true"></div>';
	} elseif ($id==2) {
		return '<div class="status_info_view"></div>';
	} elseif ($id==0) {
		return '<div class="status_info_false"></div>';
	}
	return '<div class="status_info_false"></div>';
}

/* vim: set expandtab: */

?>

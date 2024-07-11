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
function smarty_modifier_id2short($id)
{
    	$out[3]="bb";
	$out[420]="be";
	$out[1]="bw";
	$out[2]="by";
	$out[421]="hb";
	$out[4]="he";
	$out[422]="hh";
	$out[5]="mv";
	$out[6]="ni";
	$out[7]="nw";
	$out[8]="rp";
	$out[9]="sh";
	$out[12]="sl";
	$out[10]="sn";
	$out[11]="st";
	$out[13]="th";
	
	return $out[$id];
}

/* vim: set expandtab: */

?>

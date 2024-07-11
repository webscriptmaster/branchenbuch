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
function smarty_modifier_short2id($short)
{
    	$out['bb']=3;
	$out['be']=420;
	$out['bw']=1;
	$out['by']=2;
	$out['hb']=421;
	$out['he']=4;
	$out['hh']=422;
	$out['mv']=5;
	$out['ni']=6;
	$out['nw']=7;
	$out['rp']=8;
	$out['sh']=9;
	$out['sl']=12;
	$out['sn']=10;
	$out['st']=11;
	$out['th']=13;
	
	return $out[$short];
}

/* vim: set expandtab: */

?>

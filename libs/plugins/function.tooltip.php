<?php

/*
* Smarty plugin
* -------------------------------------------------------------
* Type:     function
* Name:     cycle
* Version:  1.3
* Date:     May 3, 2002
* Author:	 Monte Ohrt <monte@ispi.net>
* Credits:  Mark Priatel <mpriatel@rogers.com>
*           Gerard <gerard@interfold.com>
*           Jason Sweat <jsweat_php@yahoo.com>
* Purpose:  cycle through given values
* Input:    name = name of cycle (optional)
*           values = comma separated list of values to cycle,
*                    or an array of values to cycle
*                    (this can be left out for subsequent calls)
*
*           reset = boolean - resets given var to true
*			 print = boolean - print var or not. default is true
*           advance = boolean - whether or not to advance the cycle
*           delimiter = the value delimiter, default is ","
*           assign = boolean, assigns to template var instead of
*                    printed.
*
* Examples: {cycle values="#eeeeee,#d0d0d0d"}
*           {cycle name=row values="one,two,three" reset=true}
*           {cycle name=row}
* -------------------------------------------------------------
*/
function smarty_function_tooltip($params, &$smarty)
{
	extract($params);
	if(empty($text)){
		return "<img src=\"/images/button-help_18.gif\" width=\"18\" height=\"18\" border=\"0\" onmouseover=\"TagToTip('".$id."', ".HELP_LAYER_CONFIG.");\" style=\"cursor:pointer;margin:0px 0px 2px 10px\" align=\"absmiddle\">";
	} else {
		return "<img src=\"/images/button-help_18.gif\" width=\"18\" height=\"18\" border=\"0\" onmouseover=\"Tip('".$text."', ".HELP_LAYER_CONFIG.");\" style=\"cursor:pointer;margin:0px 0px 2px 10px\" align=\"absmiddle\">";
	}
}
/* vim: set expandtab: */

?>

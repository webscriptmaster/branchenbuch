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
function smarty_function_spaw($params, &$smarty)
{
	extract($params);
	if(empty($height)) $height=400;
	if(empty($mode)) $mode = 'standard';
	$sw = new SPAW_Wysiwyg($name,stripslashes($content),'de',$mode,'default','100%',$height.'px');
	if(isset($params['hideHTML'])) $sw->hideModeStrip();
	return $sw->getHtml();
}

/* vim: set expandtab: */

?>

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
function smarty_function_admin_sort_field($params, &$smarty)
{
	extract($params);
	
	$border_desc="CCCCCC";
	$border_asc="CCCCCC";
	
	if($_GET['sort']=="desc" && $_GET['sort_field']==$field){
		$border_desc="EEEEEE";
		$border_asc="CCCCCC";
	}
	
	if($_GET['sort']=="asc" && $_GET['sort_field']==$field){
		$border_desc="CCCCCC";
		$border_asc="EEEEEE";
	}
	
	if(!empty($field)){
		$out="<div style=\"float:left;overflow:hidden;width:15px;background:#CCCCCC;\">";
		$out.="<a href=\"".rebuildUrl(false,array('sort_field'=>$field,'sort'=>'desc'))."\"><img src=\"/admin/images/desc_order.png\" width=\"7\" height=\"7\" border=\"0\" style=\"border:1px #".$border_desc." solid;margin:2px 0px 0px 2px;\"></a>";
		$out.="<div class=\"h_space_1\"></div>";
		$out.="<a href=\"".rebuildUrl(false,array('sort_field'=>$field,'sort'=>'asc'))."\"><img src=\"images/asc_order.png\" width=\"7\" height=\"7\" border=\"0\" style=\"border:1px #".$border_asc." solid;margin:0px 0px 2px 2px;\"></a>";
		$out.="</div><div style=\"float:left;overflow:hidden;padding-top:3px;\">";
		$out.="<div style=\"white-space:nowrap;\">".$name."</div>";
		$out.="</div>";
	} else {
		$out=$name;
	}
	
	return $out;
}

/* vim: set expandtab: */

?>

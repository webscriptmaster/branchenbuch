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
function smarty_function_admin_set_sort($params, &$smarty)
{
	extract($params);
	
	$res=$GLOBALS['dbapi']->GetFields($table);
	if($res==false) return false;
	if(!$res[$field]) return false;
	if(!$res['sort_field']) return false;
	
	if(!empty($field) && !empty($table) && !empty($id)){
		$out="<div style=\"float:left;overflow:hidden;width:15px;\">";
		$out.="<a href=\"".rebuildUrl(false,array('field'=>$field,'table'=>$table,'new_sort'=>'top','sort_id'=>$id))."\"><img src=\"/admin/images/desc_order.gif\" width=\"7\" height=\"7\" border=\"0\" style=\"margin:2px 0px 0px 2px;\"></a>";
		$out.="<div class=\"h_space_3\"></div>";
		$out.="<a href=\"".rebuildUrl(false,array('field'=>$field,'table'=>$table,'new_sort'=>'bottom','sort_id'=>$id))."\"><img src=\"images/asc_order.gif\" width=\"7\" height=\"7\" border=\"0\" style=\"margin:0px 0px 2px 2px;\"></a>";
		$out.="</div><div style=\"float:left;overflow:hidden;padding-top:3px;\">";
		$out.="<div style=\"white-space:nowrap;\">".$name."</div>";
		$out.="</div>";
	}
	
	return $out;
}

?>

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
function smarty_function_content($params, &$smarty)
{
	$auth = new AUTH();
	
	extract($params);

	$res=$GLOBALS['dbapi']->get_cms($id,$auth->isAdminLoggedin());
	
	if($res['cms_status']==2 && !$auth->isAdminLoggedin()){
		$res=$GLOBALS['dbapi']->get_last_cms_history($id);
		
	}
	
	if(!empty($res)){
		$content=stripcslashes($res['cms_content']);
		$content=smarty_function_eval(array('var' => $content), $smarty);
		
	} else {
		return "";
	}
	
	if($auth->isAdminLoggedin()){
		$content="<div id=\"content_".$id."\">".$content."<div style=\"height:0px;width:1px; font-size:1px; line-height:1px;clear:both;\"></div></div>";
		$content.="<div style=\"height:3px;width:1px; font-size:1px; line-height:1px;clear:both;\"></div>";
		$content.="<a href=\"http://".ROOT_URL."/admin/index.php?page_id=6&sub_page_id=61&id=".$id."&front_referer=".urlencode(rebuildUrl())."&referer=".urlencode("http://".ROOT_URL."/admin/index.php?page_id=6")."\">";
		$content.="<img src=\"/admin/images/support_16.gif\" height=\"16\" width=\"16\" alt=\"Content ID ".$id." bearbeiten\" title=\"Content ID ".$id." bearbeiten\" style=\"cursor:pointer;\" onMouseOver=\"document.getElementById('content_".$id."').style.background='#EEEEEE';\" onMouseOut=\"document.getElementById('content_".$id."').style.background='';\">";
		$content.="</a>";
		if($res['cms_status']==0){
			$content.="<img src=\"/admin/images/close_16.gif\" height=\"16\" width=\"16\" alt=\"Content Offline\" title=\"Content Offline\" style=\"cursor:pointer;margin-left:5px;\" onMouseOver=\"document.getElementById('content_".$id."').style.background='#EEEEEE';\" onMouseOut=\"document.getElementById('content_".$id."').style.background='';\">";
		} elseif($res['cms_status']==1) {
			$content.="<img src=\"/admin/images/check_16.gif\" height=\"16\" width=\"16\" alt=\"Content Online\" title=\"Content Online\" style=\"cursor:pointer;margin-left:5px;\" onMouseOver=\"document.getElementById('content_".$id."').style.background='#EEEEEE';\" onMouseOut=\"document.getElementById('content_".$id."').style.background='';\">";
		} else {
			$content.="<img src=\"/admin/images/wait_16.gif\" height=\"16\" width=\"16\" alt=\"Content Vorschau\" title=\"Content Vorschau\" style=\"cursor:pointer;margin-left:5px;\" onMouseOver=\"document.getElementById('content_".$id."').style.background='#EEEEEE';\" onMouseOut=\"document.getElementById('content_".$id."').style.background='';\">";
		}
	} else {
		$content = htmlentities($content);
   		$content = htmlspecialchars_decode($content); 
	}
	
	return $content;
}

/* vim: set expandtab: */

?>

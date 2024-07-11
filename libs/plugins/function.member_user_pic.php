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
*           require_once $smarty->_get_plugin_filepath('function', 'html_options');
* -------------------------------------------------------------
*/

function smarty_function_member_user_pic($params, &$smarty)
{
  $width = PIC_WIDTH;
  if(isset($params['width'])) $width = $params['width'];
  $member = $params['member'];
  if(empty($member)) {
    $member_id = $params['member_id'];
    require_once $smarty->_get_plugin_filepath('modifier', 'member');
    require_once $smarty->_get_plugin_filepath('modifier', 'member_status');
    $member = smarty_modifier_member($member_id);
    $status = smarty_modifier_member_status($member['member_status_id']);
    $tip='onmouseover="Tip(\''.$member['member_firstname'].' '.$member['member_name'].'<br>'.$status.'<br>'.$member['member_city'].'\','.HELP_LAYER_CONFIG.');"';
  }
	if($member['member_pic']==true) {
	 $html = sprintf('<img src="/pic.php?member_id=%s&member_pic_type=%s" width="%s" border="0" '.$tip.'>', $member_id, $member['member_pic_type'], $width);
  } else {
    $smarty->assign('gender', $member['member_gender_id']);
    $smarty->assign('status', $member['member_status_id']);
    $smarty->assign('last_status', $member['member_last_status_id']);
    $smarty->assign('width', $width);
    $smarty->assign('tip', $tip);
  	$html = $smarty->fetch(TPL_MEMBER_DEFAULT_PIC);
  }
  
  require_once $smarty->_get_plugin_filepath('modifier', 'is_fellow');
  if(smarty_modifier_is_fellow($member_id)) {
    $html .= sprintf('<br /><img src="/img/fellow-bottom-big.gif" width="%s" style="padding-bottom:10px;" border="0">', $width);  
  }
  if(isset($params['link'])) {
    return '<a href="/index.php?p='.URL_LINK_MEMBER_VIEW_PROFILE.'&member_id='.$member_id.'&referer='.urlencode($PHP_SELF.'?'.$_SERVER['QUERY_STRING']).'">'.$html.'</a>';
  } else {
    return $html;
  }
}

/* vim: set expandtab: */

?>

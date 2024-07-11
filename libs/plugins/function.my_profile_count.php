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
function smarty_function_my_profile_count($params, &$smarty)
{
		extract($params);
		$db=&DB::connect(DB_DSN);
		$vars = $smarty->get_template_vars();

		if(!isset($vars['_templates'])) {
			// meine Vorlagen
			$where1 = $data1 = array();
			$where1['AND'][] = 'auction_status = {?}';	  $data1[] = AUCTION_STATUS_TEMPLATE;
			$where1['AND'][] = 'auction_member_id = {?}';			$data1[] = AUTH::member_id();
			$where = buildWhere($db, $where1, $data1);
			$res = $db->getOne('Select count(*) from auction where '.$where.' order by auction_time_end ASC');
			$smarty->assign('_templates', $res);
		}
		
		if(!isset($vars['_betting'])) {
			// Auktionen bei denen ich biete
			$where1 = $data1 = array();
			$where1['AND'][] = 'auction_time_start < NOW()';
			$where1['AND'][] = 'auction_time_end > NOW()';
			$where1['AND'][] = 'auction_instant_only = 0';
			$where1['AND'][] = 'auction_status = {?}';	  $data1[] = AUCTION_STATUS_RUNNING;
			$where1['AND'][] = 'bet_member_id = {?}';			$data1[] = AUTH::member_id();
			$where = buildWhere($db, $where1, $data1);
			$res = $db->getOne('Select count(*) from auction LEFT JOIN bet ON auction.auction_id = bet.bet_auction_id where '.$where.' order by auction_time_end ASC');
			$smarty->assign('_betting', $res);
		}

		if(!isset($vars['_running'])) {
			// nur laufende Auktionen des Nutzers
			$where1 = $data1 = array();
			$where1['AND'][] = 'auction_time_start < NOW()';
			$where1['AND'][] = 'auction_time_end > NOW()';
			$where1['AND'][] = 'auction_member_id = {?}';	$data1[] = AUTH::member_id();
			$where1['AND'][] = 'auction_status = {?}';	  $data1[] = AUCTION_STATUS_RUNNING;
			$where = buildWhere($db, $where1, $data1);
			$res = $db->getOne('Select count(*) from auction where '.$where.' order by auction_time_end ASC');

			$smarty->assign('_running', $res);
		}

		if(!isset($vars['_ended'])) {
			// nur beendete Auktionen des Nutzers
			$where1 = $data1 = array();
			$where1['OR'][] = '(auction_time_start < NOW() AND auction_time_end < NOW())';
			$where1['OR'][] = 'auction_status = {?}';	$data1[] = AUCTION_STATUS_ENDED;
			$where = buildWhere($db, $where1, $data1);
			$where1 = $data1 = array();
			$where1['AND'][] = $where;
			$where1['AND'][] = 'auction_member_id = {?}'; $data1[] = AUTH::member_id();
			$where1['AND'][] = 'auction_status != {?}';	$data1[] = AUCTION_STATUS_TEMPLATE;
			$where = buildWhere($db, $where1, $data1);
			$res = $db->getOne('Select count(*) from auction where '.$where.' order by auction_time_end ASC');
			$smarty->assign('_ended', $res);
		}


		if(!isset($vars['_won'])) {
			// nur gewonnene Auktionen des Nutzers
			$where1 = $data1 = array();
			$where1['OR'][] = '(auction_time_start < NOW() AND auction_time_end < NOW())';
			$where1['OR'][] = 'auction_status = {?}';	$data1[] = AUCTION_STATUS_ENDED;
			$where = buildWhere($db, $where1, $data1);
			$where1 = $data1 = array();
			$where1['AND'][] = $where;
			$where1['AND'][] = 'auction_buyer_id = {?}'; $data1[] = AUTH::member_id();
			$where = buildWhere($db, $where1, $data1);

			//$where = buildWhere($db, $where1, $data1);
			$res = $db->getOne('Select count(*) from auction where '.$where.' order by auction_time_end ASC');
			$smarty->assign('_won', count($res));
		}

		if(!isset($vars['_observation'])) {
			$res = DBApi::get_observation(AUTH::member_id());
			$smarty->assign('_observation', count($res));
		}
		
		if(!isset($vars['_messages'])) {
			$res = DBApi::get_messages(AUTH::member_id());
			$smarty->assign('_messages', count($res));
		}
		return;
}
/* vim: set expandtab: */
?>
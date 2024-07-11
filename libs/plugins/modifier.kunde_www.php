<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     iso
 * Purpose:  Replace all repeated spaces, newlines, tabs
 *           with a single space or supplied replacement string.
 * Example:  {$var|strip} {$var|strip:"&nbsp;"}
 * Author:   Monte Ohrt <monte@ispi.net>
 * Version:  1.0
 * Date:     September 25th, 2002
 * -------------------------------------------------------------
 */
function smarty_modifier_kunde_www($id,$subdomain=true)
{
	$DB = NewADOConnection(DB_DSN);
	$pages = $DB->getAssoc('SELECT id,title from page WHERE kunden_id = ? AND online=1', array($id));
	if(!$pages) $pages = array();
	list($page, $title) = each($pages);
	//$member = $DB->getRow('SELECT * from kunden_neu WHERE id=?', array($id));
	$member = $DB->getRow('SELECT * from subdomain WHERE id=?', array($id));
	
	//if(!empty($member['www'])) return $member['www'];
	if($subdomain){
		if(count($pages) > 0 && empty($member['subdomain'])) return rebuildurl(false, array('p' => 'show_page', 'page' => $page));
		if(count($pages) > 0 && !empty($member['subdomain'])) return 'http://'.$member['subdomain'].'.bbd24.de';
	} else {
		return rebuildurl(false, array('p' => 'show_page', 'page' => $page));
	}
	return false;
}

?>

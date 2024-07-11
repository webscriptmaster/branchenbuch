<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     upper
 * Purpose:  convert string to uppercase
 * -------------------------------------------------------------
 */
function smarty_modifier_url_friendly($url)
{
	list($path, $query) = split("\?", $url);
	$query = str_replace('=', '/', $query);
	$query = str_replace('&', '/', $query);
	return "/".$query."/";
}

?>

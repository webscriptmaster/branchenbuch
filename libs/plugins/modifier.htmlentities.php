<?php

/*
*/
function smarty_modifier_htmlentities($str)
{
	$str = htmlentities($str);
	$str = nl2br($str);
	$str = preg_replace("/\[url\=(.*)\](.*)\[\/url\]/i", "<a href=\"$1\">$2</a>", $str);
	return $str;
}

/* vim: set expandtab: */

?>

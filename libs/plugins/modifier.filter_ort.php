<?php

function smarty_modifier_filter_ort($string)
{

	$pos=strpos($string,' (');
	if($pos) $string = substr($string,0,$pos);
	
	$pos=strpos($string,' - ');
	if($pos) return substr($string,0,$pos);
	return htmlentities($string);
}

?>

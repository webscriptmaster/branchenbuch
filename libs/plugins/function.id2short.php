<?php

/*
*/
function smarty_function_id2short($id, &$smarty)
{
	
	$out[3]="bb";
	$out[420]="be";
	$out[1]="bw";
	$out[2]="by";
	$out[421]="hb";
	$out[4]="he";
	$out[422]="hh";
	$out[5]="mv";
	$out[6]="ni";
	$out[7]="nw";
	$out[8]="rp";
	$out[9]="sh";
	$out[12]="sl";
	$out[10]="sn";
	$out[11]="st";
	$out[13]="th";
	
	return $out[$id];
	
}

/* vim: set expandtab: */

?>

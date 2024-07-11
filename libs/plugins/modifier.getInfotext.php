<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     int
 * Version:  1.0
 * Date:     Feb 24, 2003
 * Author:	 Monte Ohrt <monte@ispi.net>
 * Purpose:  catentate a value to a variable
 * Input:    string to catenate
 * Example:  {$var|const}
 * -------------------------------------------------------------
 */
function smarty_modifier_getInfotext($firma, $ort='', $stadtteil='', $branche1='', $branche2='', $branche3='', $amtsgericht='', $handelsregisternummer='', $geocode2='', $einwohner='', $flaeche='', $kfz='')
{
	$output = "Das Unternehmen ".$firma." befindet sich in ".$ort;
	if (!empty($stadtteil))
		$output .= ", Stadtteil ".$member->stadtteil;
		
	if (!empty($member->branche1))
	{
		$output .= " und ist in der Branche ".$member->branche1;
		
		if (!empty($member->branche2))
			$output .= ", ".$member->branche2;
		if (!empty($member->branche3))
			$output .= " und ".$member->branche3;
			
		$output .= " eingetragen";
	}
	$output .= ".";

	if (!empty($member->amtsgericht) || !empty($member->handelsregisternummer))
	{
		$output .= " Sie ist ";
		if (!empty($member->amtsgericht))
			$output .= "im Amtsgericht ".$member->amtsgericht." ";
		if (!empty($member->handelsregisternummer))
			$output .= "unter der Handelsregisternummer ".$member->handelsregisternummer." ";
		$output .= "eingetragen. ";
	}
	if (!empty($member->geocode2))
		$output .= "Die Geokoordinaten lauten ".$member->geocode2.". ";
	if (!empty($member->einwohner))
		$output .= $member->ort." hat ".$member->einwohner." Einwohner auf einer Fl&auml;che von ".$member->flaeche." km&sup2;. ";
	if (!empty($member->kfz))
		$output .= "Das KFZ-Kennzeichen lautet ".$member->kfz.".";
		
	return $output;
}

?>

<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     lower
 * Purpose:  convert string to lowercase
 * -------------------------------------------------------------
 */
function smarty_modifier_department($staff_id)
{
	$res=$GLOBALS['dbapi']->get_mpi_department_staff($staff_id);
	$out="";
	for ($i=0;$res[$i];$i++){
		$out.=$res[$i]['department_description'];
		if($res[$i+1]){
			$out.=", ";
		}
	}
	return $out;
}

?>

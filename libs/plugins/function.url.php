<?php

/*
*/
function smarty_function_url($params, &$smarty)
{
	
	if(isset($params['p'])) {
		if($params['p'] == "self") {
	 		if(isset($_GET['p'])) {
			 $params['p'] = $_GET['p'];
			} else {
				unset($params['p']);
			}
		 } else {
		 	if(defined($params['p'])) {
				$params['p'] = constant($params['p']);
			} else {
				unset($params['p']);
			}
		}
	}
	
	if(!isset($params['site'])) {
		$url = $_SERVER['PHP_SELF'];
	} else {
		$url = $params['site'];
		unset($params['site']);
	}
	
	foreach($params as $k=>$v) {
	  if($params[$k] == null) unset($params[$k]);
	}
	
	$query = "";
	$query = http_build_query($params);
	if(!empty($query)) $query = "?".$query;
	$url .= $query;
	return $url;
}

/* vim: set expandtab: */

?>

<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.eightball.php
 * Type:     function
 * Name:     eightball
 * Purpose:  outputs a random magic answer
 * -------------------------------------------------------------
 */
function smarty_function_pager($params, &$smarty)
{
	
	
    if($params['count'] > 0) {
    	$pages = $_GET['pages'];
  		$tpl = 'pager2.html';
  		if(isset($params['tpl'])) $tpl = $params['tpl'];
      $ad['max'] = $params['max'];
  		$ad['all'] = $params['count'];
  		$ad['steps'] = ceil($ad['all'] / $ad['max']);
  		$ad['start'] = $ad['max'] * $_GET['pages'];
  		$ad['pages'] = $_REQUEST['pages'];

			$range = array($pages);
			$c = PAGER_NUMBER_PAGES;
			$x = 0;
			for($i = 1; $i <= $c; $i++) {
				if($x == $c) break;
				if($pages - $i >= 0)	{
					$range[] = $pages - $i;
					$x++;
				}
				if($x == $c) break;
				if($pages + $i <= $ad['steps'] - 1) {
					$range[] = $pages + $i;
					$x++;
				}
			}
			sort($range);
  		$ad['range'] = $range;
      $smarty->assign($ad);
  		
  		$pager = array();
  		$pager['first_id'] = 0;
  		$pager['last_id'] = $ad['steps']-1;
  		$pager['prev_id'] = $_GET['pages']-1;
  		if($pager['prev_id'] < 0) $pager['prev_id']=0;
  		$pager['next_id'] = $_GET['pages']+1;
  		if($pager['next_id'] > $pager['last_id']) 

  			$pager['next_id'] = $pager['last_id'];
		//if(!$smarty->get_template_vars('pager_url')) {

			$pager_uri = getenv('REQUEST_URI');

			if(strpos($pager_uri, '.html'))

				$pager_uri = substr($pager_uri, 0, strrpos($pager_uri, '/'));

			// Aus URL den alten Pages-Wert entfernen

			//$pager_uri = ereg_replace('&pages=[^&]', '', $pager_uri);
			$pager_uri = ereg_replace('&pages=[0-9]+', '', $pager_uri);
			
			$smarty->assign('pager_url', $pager_uri);
		//}
      $smarty->assign('pager',$pager);
      if($params['calc_only'] != 1) {
        return $smarty->fetch($tpl);
      }
    }
}
?>

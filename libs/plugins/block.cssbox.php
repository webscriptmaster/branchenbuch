<?php

function smarty_block_cssbox($params, $content, &$smarty) {
  if (is_null($content)) {
      return;
  }
  
  $buttons = (array)$smarty->get_template_vars('__cssbutton_cache');
  $temp = array();
  //$c = $smarty->get_template_vars('__cssbutton_count');
  //if(empty($c)) $c = 1;
  foreach($buttons as $button) {
    $button['id'] = uniqid(rand(), true);    
    if($button['js'] != true) $button['href'] = "location.href='".$button['href']."';";
    $temp[] = $button;
  }
  // Button liste umkehre, wegen den Unergründlichen weiten des CSS
  $buttons = array_reverse($temp);
  
  //$smarty->assign('__cssbutton_count', $c);
  $smarty->assign('cssbox_tab1', $params['tab1']);
  $smarty->assign('cssbox_tab2', $params['tab2']);
  $smarty->assign('cssbox_tab3', $params['tab3']);
  $smarty->assign('cssbox_tab4', $params['tab4']);
  
  $smarty->assign('cssbox_tab1_activ', $params['tab1_activ']);
  $smarty->assign('cssbox_tab2_activ', $params['tab2_activ']);
  $smarty->assign('cssbox_tab3_activ', $params['tab3_activ']);
  $smarty->assign('cssbox_tab4_activ', $params['tab4_activ']);
  
  $smarty->assign('cssbox_tab1_event', $params['tab1_event']);
  $smarty->assign('cssbox_tab2_event', $params['tab2_event']);
  $smarty->assign('cssbox_tab3_event', $params['tab3_event']);
  $smarty->assign('cssbox_tab4_event', $params['tab4_event']);
  
  $smarty->assign('cssbox_tab1_id',uniqid(''));
  $smarty->assign('cssbox_tab2_id',uniqid(''));
  $smarty->assign('cssbox_tab3_id',uniqid(''));
  $smarty->assign('cssbox_tab4_id',uniqid(''));
  
  $smarty->assign('cssbox_title', $params['title']);
  $smarty->assign('cssbox_title2', $params['title2']);
  $smarty->assign('cssbox_content', $content);
  $smarty->assign('cssbox_buttons', $buttons);
  $smarty->assign('content_box', $params['content_box']);
  $smarty->assign('__cssbutton_cache', array());
  if(isset($params['tpl'])) {
    return $smarty->fetch($params['tpl']);
  } else {
    return $smarty->fetch('cssbox_default.html');
  }
}

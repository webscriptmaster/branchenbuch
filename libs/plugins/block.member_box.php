<?php

function smarty_block_member_box($params, $content, &$smarty) {
  if (is_null($content)) {
      return;
  }
  
  $links = (array)$smarty->get_template_vars('__memberboxlink_cache');
  $smarty->assign('_member_box_active', $params['member']);
  $smarty->assign('member_box_links', $links);
  $smarty->assign('__memberboxlink_cache', array());
  if(isset($params['tpl'])) {
    return $smarty->fetch($params['tpl']);
  } else {
    return $smarty->fetch('member_box_default.html');
  }
}

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
function smarty_function_ShowPopupConfirm($params, &$smarty)
{
    extract($params);
    $smarty->assign("text", $message);
    if($js){
    	$smarty->assign("href", 'javascript:'.$onok);
    } else {
    	$smarty->assign("href", $onok);
    }
    $text = $smarty->fetch(TPL_CSSBOX_CONFIRM);
    $text = preg_replace("/\\r/", "", $text);
    $text = preg_replace("/\\n/", "", $text);

    $text = htmlspecialchars($text);
    $text = addslashes($text);
    //$html = "showPopup('".$title."', '#TB_inline?height=&width=300&inlineId=dyn_popup_text&modal=false','".$text."');";
    $html = "showPopup_1('".$title."','',300);new Ajax.Updater('Popup_1_Content', '/index.php?p=".URL_LINK_CSSBOX_CONFIRM."&text=".$message."&href=".urlencode($onok)."');";
    if(isset($assign) && !empty($assign)) {
      $smarty->assign($assign, $html);
    } else {    
      return $html;
    }
}
?>
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
function smarty_function_ShowPopupTPL($params, &$smarty)
{
    extract($params);
    if(!empty($tpl)){
    	if(empty($width)) $width=400;
    	return "showPopup('".$title."', '#TB_inline?height=&width=".$width."&inlineId=&modal=".$modal."','wadas',1);";
    } else {
    	return false;
    }
}
?>

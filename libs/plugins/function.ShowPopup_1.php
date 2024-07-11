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
function smarty_function_ShowPopup_1($params, &$smarty)
{
    $id = "dyn_popup_text";
    extract($params);
    if(empty($width)) $width=400;
    $out = "showPopup_1('".$title."','".$text."',$width);";
    if(isset($tpl)){
    	$out.="new Ajax.Updater('Popup_1_Content', '/index.php?p=".$tpl."');";
    }
    
    return $out;
}
?>

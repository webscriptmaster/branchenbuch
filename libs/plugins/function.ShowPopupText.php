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
function smarty_function_ShowPopupText($params, &$smarty)
{
    $id = "dyn_popup_text";
    extract($params);
    if(empty($width)) $width=400;
    $out = "showPopup('".$title."', '#TB_inline?height=&width=".$width."&inlineId=".$id."&modal=".$modal."','".$text."', '$id');";
    if(isset($tpl)){
    	$out.="format='php';sidx='index';con=new Array('/','.','?');new Ajax.Updater('TB_ajaxContent', con[0] + sidx + con[1] + format + con[2] + 'p' + '=' + '".$tpl."');";
    }
    
    return $out;
}
?>

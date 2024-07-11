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
function smarty_function_ShowPopupID($params, &$smarty)
{
    extract($params);
    return "showPopup('".$title."', '#TB_inline?height=&width=400&inlineId=".$id."&modal=false');";
}
?>

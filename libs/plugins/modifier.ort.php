<?php

function smarty_modifier_ort($id)
{
    $res=DBAPI::get_orte_name($id);
    return $res;
}

/* vim: set expandtab: */

?>

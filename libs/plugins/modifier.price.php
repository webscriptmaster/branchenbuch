<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     price
 * Version:  1.0
 * Date:     Feb 26, 2003
 * Author:	 Monte Ohrt <monte@ispi.net>
 * Purpose:  convert \r\n, \r or \n to <br />
 * Input:    contents = contents to replace
 *           preceed_test = if true, includes preceeding break tags
 *           in replacement
 * Example:  {$text|price}
 * -------------------------------------------------------------
 */
function smarty_modifier_price($string)
{
    return number_format($string, 2, ',', '.');
}

/* vim: set expandtab: */

?>

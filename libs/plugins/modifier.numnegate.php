<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Include the {@link shared.make_timestamp.php} plugin
 */

function smarty_modifier_numnegate($boolean)
{
	return strval(intval(!$boolean));
}

/* vim: set expandtab: */

?>

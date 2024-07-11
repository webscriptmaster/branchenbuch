<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {eval} function plugin
 *
 * Type:     function<br>
 * Name:     eval<br>
 * Purpose:  evaluate a template variable as a template<br>
 * @link http://smarty.php.net/manual/en/language.function.eval.php {eval}
 *       (Smarty online manual)
 * @author Monte Ohrt <monte at ohrt dot com>
 * @param array
 * @param Smarty
 */
function smarty_function_MakeMenu($params, &$smarty)
{
		require_once $smarty->_get_plugin_filepath('function', 'eval');
		$res = "";
		foreach((array)$params['from'] as $p) {
			$smarty->assign($p);
			$html = '<blockquote><div class="menu">
			  {if $url}
					<a href="{$url}" class="handler">{$category_title}</a>
				{else}
					<div class="handler">{$category_title}</div>
				{/if}
				{if $category_text}<p>{$category_text}</p>{/if}
				{MakeMenu from=$sub}
			</div></blockquote>';
		  $_contents = smarty_function_eval(array('var' => $html), $smarty);
		  $res .= $_contents;
		}
    return $res;
}

?>

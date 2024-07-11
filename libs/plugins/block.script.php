<?php
function smarty_block_script($params, $content, &$smarty, &$repeat)
{
	if (is_null($content)) {
		return;
	}
	print_r($content);
	if(rand(1,2) == 1) $repeat=true;
}
?>

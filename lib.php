<?php
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

function getArgs($args) {
 $out = array();
 $last_arg = null;
    for($i = 1, $il = sizeof($args); $i < $il; $i++) {
        if( (bool)preg_match("/^--(.+)/", $args[$i], $match) ) {
         $parts = explode("=", $match[1]);
         $key = preg_replace("/[^a-z0-9]+/", "", $parts[0]);
            if(isset($parts[1])) {
             $out[$key] = $parts[1];   
            }
            else {
             $out[$key] = true;   
            }
         $last_arg = $key;
        }
        else if( (bool)preg_match("/^-([a-zA-Z0-9]+)/", $args[$i], $match) ) {
            for( $j = 0, $jl = strlen($match[1]); $j < $jl; $j++ ) {
             $key = $match[1]{$j};
             $out[$key] = true;
            }
         $last_arg = $key;
        }
        else if($last_arg !== null) {
         $out[$last_arg] = $args[$i];
        }
    }
 return $out;
}

function make_unique($size=8) {
  $pass_word = "";
	$pool = "qwertzupasdfghkyxcvbnm";
	$pool .= "23456789";
	$pool .= "WERTZUPLKJHGFDSAYXCVBNM";
	srand ((double)microtime()*1000000);
	for($index = 0; $index < $size; $index++)
	{
		$pass_word .= substr($pool,(rand()%(strlen ($pool))), 1);
	}
	return $pass_word;
}

function xml_to_array($content){
	$parser = xml_parser_create();
	xml_parser_set_option( $parser, XML_OPTION_CASE_FOLDING, 0 );
	xml_parser_set_option( $parser, XML_OPTION_SKIP_WHITE, 1 );
	xml_parse_into_struct( $parser, $content, $tags );
	xml_parser_free( $parser );
	
	$elements = array();
	$stack = array();
	foreach ($tags as $tag){
		$index = count( $elements );
		if ($tag['type'] == "complete" || $tag['type'] == "open"){
			$elements[$index] = array();
			$elements[$index]['name'] = $tag['tag'];
			$elements[$index]['attributes'] = $tag['attributes'];
			$elements[$index]['content'] = $tag['value'];
			if ($tag['type']=="open"){
				$elements[$index]['children'] = array();
				$stack[count($stack)] = &$elements;
				$elements = &$elements[$index]['children'];
			}
		}
		
		if ($tag['type']=="close"){
			$elements = &$stack[count($stack) - 1];
			unset($stack[count($stack) - 1]);
		}
	}
	return $elements[0];
}
?>

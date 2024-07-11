<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     isolang
 * Purpose:  Replace all repeated spaces, newlines, tabs
 *           with a single space or supplied replacement string.
 * Example:  {$var|strip} {$var|strip:"&nbsp;"}
 * Author:   Monte Ohrt <monte@ispi.net>
 * Version:  1.0
 * Date:     September 25th, 2002
 * -------------------------------------------------------------
 */
function smarty_modifier_sonderzeichen($text)
{

	/*echo "<br>1. text: $text<br>";*/
	if(stristr($text,"<")){
		$tex=str_replace("<", '&lt;',$text);
		$text=$tex;

	}
	if(stristr($text,">")){
		$tex=str_replace(">", '&gt;',$text);
		$text=$tex;

	}
	if(stristr($text,"\ge")){
		$tex=str_replace("\ge", '>=',$text);
		$text=$tex;

	}
	if(stristr($text,"\le")){
		$tex=str_replace("\le", '<=',$text);
		$text=$tex;

	}
	if(stristr($text,"\alpha")){
		$tex=str_replace("\alpha", '<font face="SYMBOL">&#097;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\beta")){
		$tex=str_replace("\beta", '<font face="SYMBOL">&#098;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\gamma")){
		$tex=str_replace("\gamma", '<font face="SYMBOL">&#103;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\delta")){
		$tex=str_replace("\delta", '<font face="SYMBOL">&#100;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\epsilon")){
		$tex=str_replace("\epsilon", '<font face="SYMBOL">&#101;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\zeta")){
		$tex=str_replace("\zeta", '<font face="SYMBOL">&#122;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\eta")){
		$tex=str_replace("\eta", '<font face="SYMBOL">&#104;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\theta")){
		$tex=str_replace("\theta", '<font face="SYMBOL">&#074;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\vartheta")){
		$tex=str_replace("\vartheta", '<font face="SYMBOL">&#113;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\iota")){
		$tex=str_replace("\iota", '<font face="SYMBOL">&#105;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\kappa")){
		$tex=str_replace("\kappa", '<font face="SYMBOL">&#107;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\lambda")){
		$tex=str_replace("\lambda", '<font face="SYMBOL">&#108;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\mu")){
		$tex=str_replace("\mu", '<font face="SYMBOL">&#109;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\nu")){
		$tex=str_replace("\nu", '<font face="SYMBOL">&#110;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\xi")){
		$tex=str_replace("\xi", '<font face="SYMBOL">&#120;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\pi")){
		$tex=str_replace("\pi", '<font face="SYMBOL">&#112;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\rho")){
		$tex=str_replace("\rho", '<font face="SYMBOL">&#114;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\sigma")){
		$tex=str_replace("\sigma", '<font face="SYMBOL">&#115;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\varsigma")){
		$tex=str_replace("\varsigma", '<font face="SYMBOL">&#086;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\tau")){
		$tex=str_replace("\tau", '<font face="SYMBOL">&#116;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\ypsilon")){
		$tex=str_replace("\ypsilon", '<font face="SYMBOL">&#117;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\phi")){
		$tex=str_replace("\phi", '<font face="SYMBOL">&#102;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\chi")){
		$tex=str_replace("\chi", '<font face="SYMBOL">&#099;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\psi")){
		$tex=str_replace("\psi", '<font face="SYMBOL">&#121;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\omega")){
		$tex=str_replace("\omega", '<font face="SYMBOL">&#119;</font>',$text);
		$text=$tex;
	}




	if(stristr($text,"\Alpha")){
		$tex=str_replace("\Alpha", '<font face="SYMBOL">&#065;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\Beta")){
		$tex=str_replace("\Beta", '<font face="SYMBOL">&#066;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\Gamma")){
		$tex=str_replace("\Gamma", '<font face="SYMBOL">&#071;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\Delta")){
		$tex=str_replace("\Delta", '<font face="SYMBOL">&#068;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\Epsilon")){
		$tex=str_replace("\Epsilon", '<font face="SYMBOL">&#069;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\Zeta")){
		$tex=str_replace("\Zeta", '<font face="SYMBOL">&#096;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\Eta")){
		$tex=str_replace("\Eta", '<font face="SYMBOL">&#072;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\Theta")){
		$tex=str_replace("\Theta", '<font face="SYMBOL">&#081;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\Iota")){
		$tex=str_replace("\Iota", '<font face="SYMBOL">&#073;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\Kappa")){
		$tex=str_replace("\Kappa", '<font face="SYMBOL">&#075;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\Lambda")){
		$tex=str_replace("\Lambda", '<font face="SYMBOL">&#076;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\Mu")){
		$tex=str_replace("\Mu", '<font face="SYMBOL">&#077;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\Nu")){
		$tex=str_replace("\Nu", '<font face="SYMBOL">&#078;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\Xi")){
		$tex=str_replace("\Xi", '<font face="SYMBOL">&#088;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\Pi")){
		$tex=str_replace("\Pi", '<font face="SYMBOL">&#080;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\Rho")){
		$tex=str_replace("\Rho", '<font face="SYMBOL">&#082;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\Sigma")){
		$tex=str_replace("\Sigma", '<font face="SYMBOL">&#083;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\Tau")){
		$tex=str_replace("\Tau", '<font face="SYMBOL">&#084;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\Ypsilon")){
		$tex=str_replace("\Ypsilon", '<font face="SYMBOL">&#085;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\Phi")){
		$tex=str_replace("\Phi", '<font face="SYMBOL">&#070;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\Chi")){
		$tex=str_replace("\Chi", '<font face="SYMBOL">&#076;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\Psi")){
		$tex=str_replace("\Psi", '<font face="SYMBOL">&#089;</font>',$text);
		$text=$tex;
	}

	if(stristr($text,"\Omega")){
		$tex=str_replace("\Omega", '<font face="SYMBOL">&#087;</font>',$text);
		$text=$tex;
	}


	if(stristr($text,"\&")){
		$tex=str_replace("\&", '&',$text);
		$text=$tex;
	}
	if(stristr($text,"\&")){
		$tex=str_replace("\&", '&',$text);
		$text=$tex;
	}


	if(stristr($text,"\%")){
		$tex=str_replace("\%", '%',$text);
		$text=$tex;
	}
	if(stristr($text,"\AA")){
		$tex=str_replace("\AA", '&Aring;',$text);
		$text=$tex;

	}

	if(stristr($text,"{\circ}")){
		$tex=str_replace("{\circ}", '&deg;',$text);
		$text=$tex;

	}

	if(stristr($text,"{\deg}")){
		$tex=str_replace("{\deg}", '&deg;',$text);
		$text=$tex;

	}


	if(stristr($text,"\~a")){
		$tex=str_replace("\~a", '&atilde;',$text);
		$text=$tex;

	}
	if(stristr($text,"\~n")){
		$tex=str_replace("\~n", '&ntilde;',$text);
		$text=$tex;

	}

	if(stristr($text,"\emptyset")){
		$tex=str_replace("\emptyset", '&Oslash;',$text);
		$text=$tex;

	}

	if(stristr($text,"\approx")){
		$tex=str_replace("\approx", '~',$text);
		$text=$tex;

	}

	if(stristr($text,"\sim")){
		$tex=str_replace("\sim", '~',$text);
		$text=$tex;

	}


	if(stristr($text,"\pm")){
		$tex=str_replace("\pm", '&plusmn;',$text);
		$text=$tex;

	}


	if(stristr($text,"\rightarrow")){
		echo "Achtung $text";

		$tex=str_replace("\rightarrow", '&rarr;',$text);
		$text=$tex;
		echo $text;

	}
	if(stristr($text,"\leftarrow")){
		$tex=str_replace("\leftarrow", '&larr;',$text);
		$text=$tex;

	}


	/*echo "2. text: $text<br>";*/



	$textarr=explode("$",$text);
	$te='';
	while(list($k, $v)=each($textarr)){

		/*if($k==1 || $k==3 ||$k==5 ||$k==5 ||$k==7 ||$k==9 ||$k==11 || $k==13 || $k==15 ||$k==17 ||$k==19 ||$k==21 ){*/
		if($k==1 || $k>1 and $k%2<>0){
			if(substr($v,0,2)=="_{"){
				$le=strlen($v);
				$v=str_replace("_{","<sub>",$v);
				$x=strrchr($v,"}");
				$v=substr($v,0,-1);
				$v.="</sub>";
				/*echo "laenge: $le, klammer: $x , $v<br>";	*/
			}

			if(substr($v,0,1)=="_" ){
				$v=str_replace("_","<sub>",$v);
				$v.="</sub>";
			}




			if(substr($v,0,2)=="^{"){
				$le=strlen($v);
				$v=str_replace("^{","<sup>",$v);
				$x=strrchr($v,"}");
				$v=substr($v,0,-1);
				$v.="</sup>";
				/*echo "laenge: $le, klammer: $x , $v<br>";	*/
			}
			if(substr($v,0,1)=="^" ){
				$v=str_replace("^","<sup>",$v);
				$v.="</sup>";
			}

		}
		$te.=$v;
		$f=0;
		while($f==0){
			if(stristr($te,"\\textit")){

				/*echo "te vor textit: $te<br>";*/
				$lg=strlen($te);
				$t1=stristr($te,"\\textit");
				$l1=strlen($t1);
				$l2=$lg-$l1;
				$t2=substr($te,0,$l2);
				$t1="<i>".substr($t1,8);


				$x11=strpos($t1,"}");

				$t11="</i> ".substr($t1,$x11);
				$t12=substr($t1,0,$x11);
				/*echo "t12: $t12<br>";
				echo "t11: $t11<br>";*/
				$te=$t2.$t12.$t11;

			} else {
				$f=1;
			}

		}



	} /* Ende while-Schleife */


	$te=str_replace("{","",$te);
	$te=str_replace("}","",$te);


	if(stristr($te,"^")){
		$tea=explode("^",$te);
		while(list($k, $v)=each($tea)){
			if($k>0){
				$v="<sup>".substr($v,0,1)."</sup>".substr($v,1);
			}

			$tei.=$v;
		}
		$te=$tei;
	}
	/*echo "te davor: $te<br>";*/
	if(stristr($te,"_")){
		$tea=explode("_",$te);
		while(list($k, $v)=each($tea)){
			if($k>0){
				$v="<sub>".substr($v,0,1)."</sub>".substr($v,1);
			}
			/*echo "k: $k - v: $v<br>";	*/
			$tei.=$v;
		}
		$te=$tei;
	}


	/*echo "te: $te<br>";*/

	return $te;
}

?>

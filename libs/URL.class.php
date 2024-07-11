<?php
//=========================================
// Script	: SHOP[fach]
// File		: URL.class.php
// Version	: 0.1
// Author	: Matthias Franke
// Email	: info@matthiasfranke.com
// Website	: http://www.matthiasfranke.com
//=========================================
// Copyright (c) 2006 Matthias Franke
//=========================================
class URL {
	
	function removeSpecialCharacter($str,$ersatz="-") {
		
		$str = strtolower($str);
		$str=str_replace('ä','ae',$str);
		$str=str_replace('ü','ue',$str);
		$str=str_replace('ö','oe',$str);
		$str=str_replace('ß','ss',$str);
		$str=str_replace(' - ','-',$str);
		$str2 = preg_replace("/[a-z0-9]/i", "", $str);
		$sonderzeichen = array();
		for ($i=0; $i<strlen($str2); $i++) {
			if (!in_array($str2[$i], $sonderzeichen)) {
				$sonderzeichen[] = addslashes($str2[$i]);
			}
		}
		
		for ($i=0; $i<count($sonderzeichen); $i++) {
			$str = str_replace(stripslashes($sonderzeichen[$i]), $ersatz, $str);
		}
		return $str;
	}
	
	function redirect($filename) {
		if (!headers_sent())
		header('Location: '.$filename);
		else {
			echo '<script type="text/javascript">';
			echo 'window.location.href="'.$filename.'";';
			echo '</script>';
			echo '<noscript>';
			echo '<meta http-equiv="refresh" content="0;url='.$filename.'" />';
			echo '</noscript>';
		}
	}
	
	
	function get_url($url){
		
		$category_id=array();
		$parent_id=array();
		$link=array();
		
		// Sprachen start
		if(preg_match("/^\/(.*?)\//i",$url,$preg)) $lang=$GLOBALS['dbapi']->get_iso_language($preg[1]);
		if(empty($lang)) $lang=$GLOBALS['dbapi']->get_default_language();
		$url=str_replace($preg[0],"/",$url);
		$_SESSION['language']=$lang;
		// Sprachen ende
		
		if($res=preg_split("/\//",$url,-1,PREG_SPLIT_NO_EMPTY)){
			$temp=array_pop($res);
			if(strpos($temp,'.html')){
				$file=$temp;
				if(preg_match("/(.*?).html/i",$file,$preg)) $var=$preg[1];
			} else {
				$res[]=$temp;
			}
			
			for($i=0;$res[$i];$i++){
				$parent_id[$i]=$category_id[$i-1];
				if($res[0]==DIR_NAME_SHOP){
					$group=$GLOBALS['dbapi']->get_url_group_id($res[$i],$_SESSION['language']['shop_language_iso'],$parent_id[$i]);
					$category_id[$i]=$group['shop_group_id'];
					$category_name[$i]=$group['shop_group_name'];
				} else {
					$group=$GLOBALS['dbapi']->get_url_content_id($res[$i],$_SESSION['language']['shop_language_iso'],$parent_id[$i]);
					$category_id[$i]=$group['shop_content_id'];
					$category_name[$i]=$group['shop_content_name'];
				}
				$link[$i]=$this->make_url($category_id[$i],$_SESSION['language']['shop_language_iso'],$res[0]);
			}
			
			$temp=array_reverse($category_id);
			$last_category_id=$temp[0];
			$parent_id[]=$last_category_id;
			
			$var=preg_split("/-/",$var,-1,PREG_SPLIT_NO_EMPTY);
			if($var[0]<>"get"){
				$temp=$var;
				$file_id=array_pop($temp);
			}
			return array('dir'=>'/'.strtolower($_SESSION['language']['shop_language_iso']).str_replace($file,'',$url),'last_category_id'=>$last_category_id,'category'=>$res,'category_name'=>$category_name,'file'=>$file,'var'=>$var,'category_id'=>$category_id,'parent_id'=>$parent_id,'link'=>$link,'file_id'=>$file_id);
		} else {
			return false;
		}
	}
	
	function make_url($id,$iso,$type=DIR_NAME_SHOP){
		
		if($type==DIR_NAME_SHOP){
			$res=$GLOBALS['dbapi']->get_group_reverse($id,$iso);
		} else {
			$res=$GLOBALS['dbapi']->get_content_reverse($id,$iso);
		}
		$res=array_reverse($res);
		$url="/".$type."/";
		for($i=0;$res[$i];$i++){
			if($type==DIR_NAME_SHOP){
				$url.=$res[$i]['shop_group_url_name']."/";
			} else {
				$url.=$res[$i]['shop_content_url_name']."/";
			}
		}
		return '/'.strtolower($iso).$url;
	}
	
	function make_article_file($id,$iso){
		
		if(empty($id)) return false;
		
		$res=$GLOBALS['dbapi']->get_group_article_view($id,$iso);
		$article_file=$this->removeSpecialCharacter($res['shop_article_name']).'-';
		if(!empty($res['shop_article_key_1'])) $article_file.=$this->removeSpecialCharacter($res['shop_article_key_1']).'-';
		if(!empty($res['shop_article_key_2'])) $article_file.=$this->removeSpecialCharacter($res['shop_article_key_2']).'-';
		if(!empty($res['shop_article_key_3'])) $article_file.=$this->removeSpecialCharacter($res['shop_article_key_3']).'-';
		$article_file.=$res['shop_article_id'];
		
		return $article_file;
	}
	
}

?>
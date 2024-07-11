<?php
//=========================================
// Script	: fellowweb
// File		: const.inc.php
// Version	: 0.1
// Author	: Matthias Franke
// Email	: info@matthiasfranke.com
// Website	: http://www.matthiasfranke.com
//=========================================
// Copyright (c) 2007 Matthias Franke
//=========================================

class IMAGES {

	function chunk_path($x, $len = 10, $chunk_size = 2) {
		$x = (string)$x;
		if(strlen($x) < $len) $x = str_repeat('0', $len - strlen($x)).$x;  
		$mdir = '/'.chunk_split($x, $chunk_size, '/');
		if(substr($mdir, -1) != '/') $mdir.= '/';
		return $mdir;
	}

	//This is the function which is doing the search...
	function directory_tree($address,$comparedate, $callback=false, $rec=false){
	
	 @$dir = opendir($address);
	  if(!$dir){ return 0; }
	    while($entry = readdir($dir)){
	      if(is_dir("$address/$entry") && ($entry != ".." && $entry != ".")){                            
	      	if($rec == true) IMAGES::directory_tree("$address/$entry",$comparedate, $callback, $rec);
	      } else   {
	        if($entry != ".." && $entry != ".") {
	      
	          $fulldir=$address.'/'.$entry;
	          $last_modified = filemtime($fulldir);
	          $last_modified_str= date("Y-m-d h:i:s", $last_modified);
	
	             if($comparedate > $last_modified)  {
	                //echo $fulldir.'=>'.$last_modified_str;
	                //echo "<BR>";
	                if(is_callable($callback)) call_user_func_array($callback, array($fulldir));
	             }
	
	       }
		  	}
	  }	
	}

	function create_tree($path, $mode=0777) {
		if(is_dir($path)) return;
		if(substr($path, -1) == '/') $path = substr($path, 0, -1);
		$pos = strrpos($path, '/');
		if($pos === false) return;
		$parent = substr($path, 0, $pos+1);
		if(!empty($parent)) IMAGES::create_tree($parent, $mode);
		if(is_dir($parent)) {
			mkdir($path, $mode);
			chmod($path, $mode);
		}
	}


	function get_tmp_pic($id){
		$out=array();
		$handle=opendir(TPL_DIR_C_NEW_PIC);
		while ($file = readdir ($handle)) {
			if ($file != "." && $file != ".." && substr($file,0,strpos($file,'-'))==$id) {
				$out[]=$file;
			}
		}
		closedir($handle);
		return $out;
	}

	function del_tmp_pic($id){
		$pic = IMAGES::get_tmp_pic($id);
		for($i=0;$pic[$i];$i++){
			@unlink(TPL_DIR_C_NEW_PIC.'/'.$pic[$i]);
		}
		return;
	}

	function new_create_resize_pic($newb,$newl,$qpfad,$ziel,$lox,$loy,$epixr,$epixu,$quality,$type=""){
		$epixu=$epixu+$loy;
		$epixr=$epixr+$lox;

		if($type=="image/gif"){
			$img_origem = @imagecreatefromgif($qpfad);
		} elseif ($type=="image/png") {
			$img_origem = @imagecreatefrompng($qpfad);
		} else {
			$img_origem = @imagecreatefromjpeg($qpfad);
		}
		
		if (!$img_origem) {
			$img_origem = ImageCreate (150, 30);
			$bgc = ImageColorAllocate ($img_origem, 255, 255, 255);
			$tc  = ImageColorAllocate ($img_origem, 0, 0, 0);
			ImageFilledRectangle ($img_origem, 0, 0, 150, 30, $bgc);

			ImageString($img_origem, 1, 5, 5, "Fehler beim Öffnen von: ".$qpfad, $tc);
		}
		$img_destino = imagecreatetruecolor($newb,$newl);
		imagecopyresampled($img_destino,$img_origem,0,0,$lox,$loy,$newb+1,$newl+1,imagesx($img_origem)-$epixr,imagesy($img_origem)-$epixu);
		return imagejpeg($img_destino,$ziel,$quality);
	}

	function scale_img_w($hfest,$pic_src){
		$size = getimagesize($pic_src);
		if($hfest < $size[1]){
			$x = $hfest / $size[1];
			$bfest = round($size[0] * $x);
		} else {
			$bfest=$size[0];
			$hfest=$size[1];
		}
		return array('width'=>$bfest,'height'=>$hfest);
	}

	function scale_img_h($bfest,$pic_src){
		$size = getimagesize($pic_src);
		if($bfest < $size[0]){
			$x = $bfest / $size[0];
			$hfest = round($size[1] * $x);
		} else {
			$bfest=$size[0];
			$hfest=$size[1];
		}
		return array('width'=>$bfest,'height'=>$hfest);
	}

	function gif2jpeg($p_fl, $p_new_fl='', $bgcolor=false){
		list($wd, $ht, $tp, $at)=getimagesize($p_fl);
		$img_src=imagecreatefromgif($p_fl);
		$img_dst=imagecreatetruecolor($wd,$ht);
		$clr['red']=255;
		$clr['green']=255;
		$clr['blue']=255;
		if(is_array($bgcolor)) $clr=$bgcolor;
		$kek=imagecolorallocate($img_dst,
		$clr['red'],$clr['green'],$clr['blue']);
		imagefill($img_dst,0,0,$kek);
		imagecopyresampled($img_dst, $img_src, 0, 0,
		0, 0, $wd, $ht, $wd, $ht);
		$draw=true;
		if(strlen($p_new_fl)>0){
			if($hnd=fopen($p_new_fl,'w')){
				$draw=false;
				fclose($hnd);
			}
		}
		if(true==$draw){
			header("Content-type: image/jpeg");
			imagejpeg($img_dst);
		}else imagejpeg($img_dst, $p_new_fl);
		imagedestroy($img_dst);
		imagedestroy($img_src);
	}

}

?>
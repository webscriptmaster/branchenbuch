<?php

class MyTagCloud {
  var $debug = false;
  var $minFS = 11;
  var $maxFS = 15;

  var $minWgt = 200;
  var $maxWgt = 900;
  
  var $sizeSuffix = 'px';
  var $fontType = 'Arial';
  var $fontColor = '666666';
  var $_elements = array();
  var $connector = "&nbsp ";
  
  function MyTagCloud($options = array()) {
    foreach($options as $k => $v) $this->$k = $v;
  }
  
  function addElement($name, $link, $value, $style = array()) {
    $this->_elements[] = array($name, $link, $value, $style);  
  }
  
  function _calc() {
    $_x = array();
    $_neu = array();
    foreach((array)$this->_elements as $p) {
      list($k, $l, $v) = $p;
      if(strlen($k) < 1) continue;
      $_x[] = $v;
      $_neu[] = $p;
    }
    $this->_elements = $_neu;
    $this->_min = @min($_x);
    $this->_max = @max($_x);
    $this->debug('min: '.$this->_min.', max: '.$this->_max);
  }
  
  function debug($text) {
    if(is_array($text)) $text = var_export($text,1);
    if($this->debug) echo "<br />".$text;
  }
  
  function getSize($count) {
    $size = $this->minFS + round((sqrt($count) - sqrt($this->_min) ) * $this->_factor_size,0);
    $this->debug($count.", ".$size);
    return $size;
  }
  
  function getWeight($count) {
    $x = ((sqrt($count) - sqrt($this->_min) ) * $this->_factor_weight);
    $x = round($x/100,0)*100;
    return $this->minWgt + $x; 
  }
  
  function buildAll() {
    if(!is_array($this->_elements)) return "";
    $this->_calc();
    if($this->_max == 0) return "";


    $range_size = $this->maxFS - $this->minFS;
    $range_weight = $this->maxWgt - $this->minWgt;
    if ($this->_max != $this->_min) {
        $this->_factor_size = $range_size / (sqrt($this->_max) - sqrt($this->_min));
        $this->_factor_weight = $range_weight / (sqrt($this->_max) - sqrt($this->_min));
    } else {
        $this->_factor_size = 1;
        $this->_factor_weight = 1;
    }
    $this->debug('F. size: '.$this->_factor_size.', F. weight: '.$this->_factor_weight);

    $cloud = "";
    for($i = 0; $i < count($this->_elements); $i++) {
      list($k, $l, $v, $s) = $this->_elements[$i];
      if(strlen($k) < 1) continue;

      //$size = round(($v/$this->_max)*($this->maxFS-$this->minFS),0) + $this->minFS;
      //$weight = (round(((($v/$this->_max)*800)/100),0)*100)+100;
      $size = $this->getSize($v);
      $weight = $this->getWeight($v);
      
      $_style = array();
      $_style['font-size'] = $size.$this->sizeSuffix;
      $_style['font-weight'] = $weight;
      $_style['font-family'] = $this->fontType;
      $_style['color'] = $this->fontColor;
      
      // Userstyles drüber legen
      foreach((array)$s as $sk => $sv) {
        $_style[strtolower($sk)] = $sv;
      }
      // Zusammenschrauben
      $style = "";
      foreach($_style as $sk => $sv) {
        $style .= $sk.":".$sv.";";
      }
      
      $con = $this->connector;
      if($i == count($this->_elements)-1) $con = "";
      $this->debug($k.': '.$style);
      if(!empty($l)) { 
        $cloud .= sprintf('<a href="%s" style="%s">%s%s</a>', $l, $style, $k, $con);
      } else {
        $cloud .= sprintf('<span style="%s">%s%s</span>', $style, $k, $con);
      }
    }
    return $cloud;
  }
}

?>

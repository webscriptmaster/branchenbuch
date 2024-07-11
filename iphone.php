<?php
//=========================================
// Script	: BBD24
// File		: index.php
// Version	: 0.1
// Author	: Matthias Franke
// Email	: info@matthiasfranke.com
// Website	: http://www.matthiasfranke.com
//=========================================
// Copyright (c) 2007 Matthias Franke
//=========================================

require_once(dirname(__FILE__)."/configs/config.inc.php");

$_0 = _pf();

$smarty->assign('dbapi', $GLOBALS['dbapi']);
$display=TPL_MAIN_IPHONE;

// Referer Cookie setzen
if(!isset($_COOKIE['referer']) && isset($_GET['ref'])) {
	setcookie("referer", $_GET['ref'], strtotime('+ 1 month'));  /* verf�llt in 30 Tagen */
}

$url = $_SERVER['REQUEST_URI'];
if(!preg_match("/^\/index\.php/", $url)) {
	$url = preg_replace("/\?.*/", "", $url);
	$a = preg_split("/\//", $url);
	if(substr($url, 0, 1) == "/") {
		array_shift($a);
	}
	
	while(count($a) > 0) {
		$k = array_shift($a);
		$v = array_shift($a);
		if(preg_match("/\.html$/", $k)) break;
		$_GET[$k] = urldecode($v);
	}
	$_SERVER['QUERY_STRING'] = http_build_query($_GET);
}

if(!isset($_GET['p'])) {
	if(preg_match("/([0-9]+)\.html$/", $_SERVER['REQUEST_URI'], $match)) {
		$_GET['member_id'] = $match[1];
		$_GET['p'] = URL_LINK_MEMBER_DETAIL;
	}
}


switch($_GET['p']) {
	
	####################################
	# Kontakt senden
	####################################
	
	case URL_AJAX_LINK_SEND_CONTACT:
	if(!empty($_POST['email']) && !empty($_POST['id'])){
		$res=DBAPI::get_member($_POST['id']);
		
		$_POST['msg']=strip_tags($_POST['msg']);
		
		$_POST['name'] = preg_replace("/(content-type:|bcc:|cc:|to:|from:)/im","",$_POST['name']);
		$_POST['subject'] = preg_replace("/(content-type:|bcc:|cc:|to:|from:)/im","",$_POST['subject']);
		$_POST['msg'] = preg_replace("/(content-type:|bcc:|cc:|to:|from:)/im","",$_POST['msg']);
				
		sleep(1);
		
		mail($res['email'],$_POST['subject'],$_POST['msg'],'From:'.$_POST['name'].'<'.$_POST['email'].'>');
		
		echo '1';
	}
	die();
	break;
	
	####################################
	# AGB
	####################################
	
	case URL_LINK_AGB:
	$smarty->assign('_tpl', TPL_AGB);
	break;
	
	####################################
	# Impressum
	####################################
	
	case URL_LINK_IMPRESSUM:
	$smarty->assign('_tpl', TPL_IMPRESSUM);
	break;
	
	####################################
	# Kontakt
	####################################
	
	case URL_LINK_CONTACT:
	$smarty->assign('_tpl', TPL_CONTACT);
	break;
	
	####################################
	# Rechnung Anzeigen
	####################################
	
	case URL_LINK_PAY:
	$res=DBAPI::get_kunden_pay(AUTH::member_id());
	for($i=0;$i<=count($res)-1;$i++){
		$res[$i]['price']=$res[$i]['price']+$res[$i]['tax_sum'];
	}
	$smarty->assign('res', $res);
	$smarty->assign('member', $_SESSION['member']);
	$smarty->assign('_tpl', TPL_PAY_VIEW);
	break;
	
	case URL_LINK_VIEW_PAY:
	AUTH::checkLogin();
	if(!empty($_GET['pay_id'])){
		$pdf = new HTML2FPDF();
		$res=DBAPI::get_view_pay($_GET['pay_id']);
		
		if($res['id']<>AUTH::member_id()) die();
		
		$res['end_price']=$res['price']+$res['tax_sum'];
		
		$smarty->assign($res);

		$html=$smarty->fetch(TPL_PAY);
		
		if(strstr($html,"<hr />")){
			$html_page=split("<hr />",$html);
			
			for($i=0;$i<=count($html_page)-1;$i++){
				ob_start();
				$html_page[$i]=smarty_function_eval(array('var' => $html_page[$i]), $smarty);
				echo $html_page[$i];
				$html_page[$i]=ob_get_contents();
				ob_end_clean();
				$pdf->DisplayPreferences('HideWindowUI');
				$pdf->AddPage(NULL,NULL);
				$pdf->WriteHTML($html_page[$i]);
			}
		} else {
			ob_start();
			$html=smarty_function_eval(array('var' => $html), $smarty);
			echo $html;
			$html=ob_get_contents();
			ob_end_clean();
			$pdf->DisplayPreferences('HideWindowUI');
			$pdf->AddPage(NULL,NULL);
			$pdf->WriteHTML($html);
		}		
		$pdf->Output('Rechnung.pdf','D');
		die();
	}
	break;
	
	####################################
	# Passwort vergessen
	####################################
	
	case URL_LINK_LOST_PASS:
	if(isset($_POST['check']) && !empty($_POST['email'])){
		$res=DBAPI::get_member_by_email($_POST['email']);
		if(empty($res['email'])){
			$smarty->assign('error', true);
		} else {
			$smarty->assign('email',$res['email']);
			$new_pass=substr(strtolower(uniqid('')),-8);
			
			$smarty->assign($res);
			$smarty->assign('new_pass',$new_pass);
			$txt = $smarty->fetch('mail_send_lost_pass.html');
			
			//echo $txt;
			
			// Nachricht senden
			$mailtpl = new MAILTPL();
			$mailtpl->send_mail($res['email'], 'Passwort BranchenbuchDeutschland', strip_tags($txt), $txt, SYS_MAIL);

			DBAPI::save_member2(array('password'=>md5($new_pass)),$res['id']);
		}
	}
	$smarty->assign('_tpl', 'lost_pass.html');
	break;
	
	####################################
	# Privatekontakte
	####################################
	
	case URL_LINK_REVERSE:
    $DB = NewADOConnection(DB_DSN);
    $short = array('bb', 'be', 'bw', 'by', 'hb', 'he', 'hh', 'mv', 'ni', 'nw', 'rp', 'sh', 'sl', 'sn', 'st', 'th');
    $laender = $DB->getAssoc('SELECT LOWER(short), bundesland from plz_bundesland group by short');
    $smarty->assign('laender', $laender);
    
		if(isset($_GET['q'])) {
			if(empty($_GET['pages'])) $_GET['pages'] = 0;
			
	    $limit = " LIMIT ".($_GET['pages']*10).", 10";

			if(!empty($_GET['bl'])) {
				$table_name .= "t_com_privat_".$_GET['bl'];
				//$count = $DB->getOne('SELECT count(*) from '.$table_name.' WHERE reverse = 1 AND match tel against (\'"'.$_GET['q'].'" "0049'.$_GET['q'].'"\' IN BOOLEAN MODE)');
		    $smarty->assign('count', $count);
		    $res = $DB->getAll('SELECT sql_calc_found_rows * from '.$table_name.' WHERE reverse <> \'N\' AND tel = \''.$_GET['q'].'\' '.$limit);
		    $smarty->assign('res', $res);
		    $count = $DB->getOne('SELECT found_rows()');
		    $smarty->assign('count', $count);
			} else {
				$count = 0;
				for($i=0; $i <= count($short)-1; $i++) {
					$table_name = "t_com_privat_".$short[$i];
					if($i == 0) {
						$sql = "CREATE TEMPORARY TABLE temp_privat";
					} else {
						$sql = "INSERT INTO temp_privat";
					}
					$sql .= ' SELECT * from '.$table_name.' WHERE reverse <> \'N\' AND tel = \''.$_GET['q'].'\' ORDER BY id ASC';
					$a = microtime_float();
					$DB->Execute($sql);
					//echo "<br> $table_name Dauer (holen): ".(microtime_float()-$a);
					$count += $DB->Affected_Rows();
					if($DB->ErrorMsg() != "") echo $DB->ErrorMsg();
				}
				
				$table_name = "temp_privat";
				$sql = 'SELECT * from '.$table_name.$limit;
				//$count = $DB->getOne('SELECT count(*) from '.$table_name);
				//if($DB->ErrorMsg() != "") echo $DB->ErrorMsg();
		    $smarty->assign('count', $count);
		    $res = $DB->getAll($sql);
		    if($DB->ErrorMsg() != "") echo $DB->ErrorMsg();
		    $smarty->assign('res', $res);
			}
		}
		$display="main_telefonbuch.html";
		$smarty->assign('_tpl', 'reverse.html');
	break;
	
	####################################
	# Privatekontakte
	####################################
	
	case "telefonbuch":
		if(!defined("MAX_LIST_SEARCH_OUT")) define("MAX_LIST_SEARCH_OUT", 25);
		
    //$DB = NewADOConnection(DB_DSN);
    //$short = array('bb', 'be', 'bw', 'by', 'hb', 'he', 'hh', 'mv', 'ni', 'nw', 'rp', 'sh', 'sl', 'sn', 'st', 'th');
    //$laender = $DB->getAssoc('SELECT LOWER(short), bundesland from plz_bundesland group by short');
    //$smarty->assign('laender', $laender);
		$smarty->assign('countrys', DBApi::get_orte_short(-1));
    
		if(isset($_GET['q'])) {
			list($count, $res) = DBApi::db_telefonbuch($_GET['q'], $_GET['bl'], $_GET['str'], $_GET['plz'], $_GET['ort'], $_GET['pages']);
	    $smarty->assign('count', $count);
	    $smarty->assign('res', $res);
/*			
			// UND Suche
			if($_GET['search_type']) $_GET['q']='"'.$_GET['q'].'"';
			
			if(empty($_GET['pages'])) $_GET['pages'] = 0;
			
	    $limit = " LIMIT ".($_GET['pages']*MAX_LIST_SEARCH_OUT).", ".MAX_LIST_SEARCH_OUT;

			if(!empty($_GET['bl'])) {
				$table_name .= "t_com_privat_".$_GET['bl'];
				$count = $DB->getOne('SELECT count(*) from '.$table_name.' WHERE match (firma, strasse, ort) against (\''.$_GET['q'].'\' IN BOOLEAN MODE)');
		    $smarty->assign('count', $count);
		    $res = $DB->getAll('SELECT * from '.$table_name.' WHERE match (firma, strasse, ort) against (\''.$_GET['q'].'\' IN BOOLEAN MODE)'.$limit);
		    $smarty->assign('res', $res);
			} else {
				$count = 0;
				for($i=0; $i <= count($short)-1; $i++) {
					$table_name = "t_com_privat_".$short[$i];
					if($i == 0) {
						$sql = "CREATE TEMPORARY TABLE temp_privat";
					} else {
						$sql = "INSERT INTO temp_privat";
					}
					$sql .= ' SELECT * from '.$table_name.' WHERE match (firma, strasse, ort) against (\''.$_GET['q'].'\' IN BOOLEAN MODE) ORDER BY ID ASC';
					$a = microtime_float();
					$DB->Execute($sql);
					$b = microtime_float();
					//echo "<br> $table_name Dauer (holen): ".($b-$a);
					$count += $DB->Affected_Rows();
					if($DB->ErrorMsg() != "") echo $DB->ErrorMsg();
				}

				$table_name = "temp_privat";
		 		$where = array();
				
				if(!empty($_GET['plz'])) {
					$where[] = "plz = '".$_GET['plz']."'";
				}
				if(!empty($_GET['ort'])) {
					$where[] = "ort = '".$_GET['ort']."'";
				}
				if(!empty($_GET['str'])) {
					$where[] = "strasse Like '".$_GET['str']."%'";
				}								
				// Query zusammenbauen
				$_where = join(' AND ', $where);				
				if(!empty($_where)) { 
					$_where = " WHERE ".$_where;
					$count = $DB->getOne('SELECT count(*) from '.$table_name.$_where);
				}

				$sql = 'SELECT * from '.$table_name.$_where.$limit;
								
				//$count = $DB->getOne('SELECT count(*) from '.$table_name);
				//if($DB->ErrorMsg() != "") echo $DB->ErrorMsg();
		    $smarty->assign('count', $count);
		    $res = $DB->getAll($sql);
		    if($DB->ErrorMsg() != "") echo $DB->ErrorMsg();
		    $smarty->assign('res', $res);
			}
*/
		} else {
			$smarty->assign('werbung', DBAPI::get_werbung());
		}
		$display="main_telefonbuch.html";
		$smarty->assign('_tpl', 'telefonbuch.html');
	break;
	
	case "load_tag":
		
		$p = $db->getRow('SELECT * from page_tag WHERE page_id=? AND tag=?', array($_GET['page'], $_GET['tag']), DB_FETCHMODE_ASSOC);
		$args = unserialize($p['args']);
		$mdir = "/images/uploads/".IMAGES::chunk_path($p['kunden_id']);
		
		if($p['type'] == "picture") {
			//$data = sprintf('<img src="%s" width="%spx" height="%spx">', $mdir.unserialize($p['data']), $args['width'], $args['height']);
			$data = sprintf('<img src="%s" width="%spx">', $mdir.unserialize($p['data']), $args['width']);
		} else {
			$data = unserialize($p['data']);
			if($p['type'] == "text") {
				$data = nl2br($data);
			}
		}
		die(utf8_encode($data));
	break;
	
	case "edit_tag":
		AUTH::checkLogin();
		$kunde = AUTH::member_id();
		if($_GET['save'] == 1) {
			$DB = NewADOConnection(DB_DSN);
			$id = $db->getOne('SELECT id from page_tag where page_id=? AND tag=?', array($_GET['page'], $_GET['tag']));
			$data = array('kunden_id' => $kunde, 'page_id' => $_GET['page'], 'tag' => $_GET['tag'], 'type' => $_GET['type'], 'data' => serialize($_GET['data']));
			if($id) {
				$res = $DB->AutoExecute('page_tag', $data, "UPDATE", 'id = '.$id);
			} else {
				$res = $DB->AutoExecute('page_tag', $data, "INSERT");
			}
		}
		
		$rs = $db->getRow('SELECT * from page_tag WHERE kunden_id=? AND page_id=? AND tag=?', array($kunde, $_GET['page'], $_GET['tag']), DB_FETCHMODE_ASSOC);
		$data = unserialize($rs['data']);
		$smarty->assign('data', $data);
		
		$mdir = "/images/uploads/".IMAGES::chunk_path(AUTH::member_id());
		// Pfad erzeugen
		if(!file_exists(ROOT_DIR.$mdir)) {
			IMAGES::create_tree(ROOT_DIR.$mdir);
		}
		$smarty->assign('upload_dir', $mdir);
		$smarty->assign('_tpl', 'edit_tag_'.$_GET['type'].'.html');
		die(utf8_encode($smarty->fetch('main_popup.html')));
	break;
	
	case "upload_picture":
		AUTH::checkLogin();
		$kunde = AUTH::member_id();
		$DB = NewADOConnection(DB_DSN);
		if($_POST['save'] == 1) {
			if($_POST['del_picture']==true){
				$db->query("delete from page_tag where page_id='".$_POST['page']."' AND tag='".$_POST['tag']."' and type='".$_POST['type']."'");
			}
			
			$mdir = ROOT_DIR."/images/uploads/".IMAGES::chunk_path(AUTH::member_id());
			// Pfad erzeugen
			IMAGES::create_tree($mdir);
			
			foreach(array('userfile') as $p) {
				if(!isset($_FILES[$p])) continue;
				$upload = $_FILES[$p];
				# Fehler
				if($upload['size'] > PIC_MAX_SIZE) {
	        $_SESSION['_flash'] = "$p zu gro�";
	        continue;
	      }
				// Gruppen Bild einordnen und skalieren
				if(empty($upload['name']) || empty($upload['tmp_name']) || $upload['error'] != false) {
	        $_SESSION['_flash'] = "$p Fehler ".$upload['error'];
	        continue;
	      }
				//$token = md5 (uniqid (rand()));					
				$pic_org = $mdir.$upload['name'];
				if(move_uploaded_file($upload["tmp_name"], $pic_org)){
					chmod($pic_org, 0777);

					$id = $db->getOne('SELECT id from page_tag where page_id=? AND tag=?', array($_POST['page'], $_POST['tag']));
					$data = array('kunden_id' => $kunde, 'page_id' => $_POST['page'], 'tag' => $_POST['tag'], 'type' => $_POST['type'], 'data' => serialize($upload['name']));
					if($id) {
						$res = $DB->AutoExecute('page_tag', $data, "UPDATE", 'id = '.$id);
					} else {
						$res = $DB->AutoExecute('page_tag', $data, "INSERT");
					}		
					//$size = IMAGES::scale_img_h(PIC_TYPE_1_WIDTH, $pic_org);
					//IMAGES::new_create_resize_pic($size['width'], $size['height'], $pic_org, $pic_size_1, 0, 0, 0, 0, 90, $_FILES['userfile']['type']);
				} else {
				 $_SESSION['_flash'] = "$p Fehler beim kopieren";
				}
			}
		}		
		
		$rs = $DB->getRow('SELECT * from page_tag WHERE page_id=? AND tag=?', array($_GET['page'], $_GET['tag']));
		$data = unserialize($rs['data']);
		$args = unserialize($rs['args']);
		$smarty->assign('data', $data);
		$smarty->assign('args', $args);
		$smarty->assign('picdir', "/images/uploads/".IMAGES::chunk_path($rs['kunden_id'])); 		
		die(utf8_encode($smarty->fetch('upload_picture.html')));
	break;

	case "load_text":
		$kunde = AUTH::member_id();
		$DB = NewADOConnection(DB_DSN);
		if($_POST['save'] == 1) {
			$id = $db->getOne('SELECT id from page_tag where page_id=? AND tag=?', array($_POST['page'], $_POST['tag']));
			$data = array('kunden_id' => $kunde, 'page_id' => $_POST['page'], 'tag' => $_POST['tag'], 'type' => $_POST['type'], 'data' => serialize(stripslashes($_POST['data'])));
			if($id) {
				$res = $DB->AutoExecute('page_tag', $data, "UPDATE", 'id = '.$id);
			} else {
				$res = $DB->AutoExecute('page_tag', $data, "INSERT");
			}
			$_GET['page'] = $_POST['page'];
			$_GET['tag'] = $_POST['tag'];
		}
		$rs = $DB->getRow('SELECT * from page_tag WHERE page_id=? AND tag=?', array($_GET['page'], $_GET['tag']));
		$data = unserialize($rs['data']);
		$args = unserialize($rs['args']);
		$smarty->assign('data', $data);
		$smarty->assign('info', $args['info']);
		//die(utf8_encode($smarty->fetch('load_text.html')));
		die($smarty->fetch('load_text.html'));
	break;
	
	case "load_spaw":
		$kunde = AUTH::member_id();
		$DB = NewADOConnection(DB_DSN);
		if($_POST['save'] == 1) {
			$id = $db->getOne('SELECT id from page_tag where page_id=? AND tag=?', array($_POST['page'], $_POST['tag']));
			$data = array('kunden_id' => $kunde, 'page_id' => $_POST['page'], 'tag' => $_POST['tag'], 'type' => $_POST['type'], 'data' => serialize(stripslashes($_POST['data'])));
			if($id) {
				$res = $DB->AutoExecute('page_tag', $data, "UPDATE", 'id = '.$id);
			} else {
				$res = $DB->AutoExecute('page_tag', $data, "INSERT");
			}
			$_GET['page'] = $_POST['page'];
			$_GET['tag'] = $_POST['tag'];
		}
		$rs = $DB->getRow('SELECT * from page_tag WHERE page_id=? AND tag=?', array($_GET['page'], $_GET['tag']));
		$data = unserialize($rs['data']);
		$args = unserialize($rs['args']);
		$smarty->assign('data', $data);
		$smarty->assign('info', $args['info']);
		//$smarty->assign('upload_dir', MB_DIR.IMAGES::chunk_path($kunde)); 
		//die(utf8_encode($smarty->fetch('load_spaw.html')));
		die($smarty->fetch('load_spaw.html'));
	break;
	
	####################################
	# Homepage
	####################################
	
	case URL_LINK_HOMEPAGE:
		AUTH::checkLogin();
		//print_r($_SESSION['_flash']);
		$page = $_GET['page'];
		$kunde = AUTH::member_id();

		$DB = NewADOConnection(DB_DSN);
		// Create Pages to max. 5
		$count = $DB->getOne('SELECT count(*) from page WHERE kunden_id=?', array($kunde));
		if($count < 5) {
			$tpl = $DB->getOne('SELECT id from page_template');
			for($i = $count+1; $i <= 5; $i++) {
				$DB->AutoExecute('page', array('kunden_id' => $kunde, 'template_id' => $tpl, 'title' => 'Seite '.$i), "INSERT");
			}
		}
		
		$pages = $DB->getAssoc('SELECT id,title from page WHERE kunden_id = ?', array($kunde));
		$smarty->assign('pages', $pages);
		$keys = array_keys($pages);
		if(empty($page)) $page = $keys[0];
		if(empty($_GET['page'])) $_GET['page'] = $page;
		// update page settings
		if($_POST['save'] == 1 && !empty($_POST['kunden_id']) && !empty($_POST['page'])) {
			$res = $DB->AutoExecute('page', array('template_id'=>$_POST['template_id']), "UPDATE", 'kunden_id = '.$_POST['kunden_id']);
			$res = $DB->AutoExecute('page', array('title'=>$_POST['title'],'online'=>$_POST['online']), "UPDATE", 'id = '.$_POST['page']);
			die(header('Location:/index.php?p='.$_GET['p'].'&page='.$page));		
		}

		$templates = $db->getAssoc('SELECT id, title from page_template');
		$rs = $db->getRow('SELECT * from page WHERE id=?', array($page), DB_FETCHMODE_ASSOC);
		$text = $db->getOne('SELECT data from page_template WHERE id=?', array($rs['template_id']));
		$smarty->assign($rs);
		$smarty->assign($_SESSION['member']);
		$text=smarty_function_eval(array('var' => $text), $smarty);
		$mdir = "/images/uploads/".IMAGES::chunk_path($rs['kunden_id']);
		
		if(preg_match_all("/\[([a-zA-Z_0-9]+)(.*?)\]/", $text, $res, PREG_OFFSET_CAPTURE)) {
			$shift = 0;
			for($i = 0; $i <= count($res[0])-1; $i++) {
				$tag = $res[1][$i][0];
				$args = _parse_attrs($res[2][$i][0]);
				$offset = $res[0][$i][1] - $shift;
				$len = strlen($res[0][$i][0]);
				$old = substr($text, $offset, $len);
				
				if($tag == "tag") {
					$url = sprintf('/index.php?p=edit_tag&page=%s&tag=%s&type=%s&width=%s&height=%s', $page, $args['id'], $args['type'], $args['width'], $args['height']);
					// save tag info
					$page_tag = $DB->getRow('SELECT * from page_tag where page_id=? AND tag=?', array($page, $args['id']));
					$data = array('kunden_id' => $kunde, 'page_id' => $page, 'tag' => $args['id'], 'type' => $args['type'], 'args' => serialize($args));
					if(!$page_tag) {
						$DB->AutoExecute('page_tag', $data, "INSERT");
					} else {
						$DB->AutoExecute('page_tag', $data, "UPDATE", 'id = '.$page_tag['id']);
					}
					$data = unserialize($page_tag['data']);
					if(empty($data)) {
						$bgcolor = "";
						$content = "";
					} else {
						if($args['type'] == "picture") {
							$bgcolor = "";
							//$content = sprintf('<img src="%s" width="%spx" height="%spx">', $mdir.$data, $args['width'], $args['height']);
							$content = sprintf('<img src="%s" width="%spx">', $mdir.$data, $args['width']);
							//$content = sprintf('<img src="%s" height="%spx">', $mdir.$data, $args['height']);
						} elseif ($args['type'] == "text") {
							$content = nl2br($data);
						} else {
							$content = $data;
						}
					}
					
					//$neu = sprintf("<div title=\"%s\" id=\"tag_%s\" style=\"%s cursor:pointer; width:%spx; height:%spx;\" onclick=\"showPopup_1('Edit','','500'); void(new Ajax.Updater('Popup_1_Content', '%s', {evalScripts:true}));\" class=\"%s\">", $args['info'], $args['id'], $bgcolor, $args['width'], $args['height'], $url, $args['class']);
					$neu = sprintf("<div title=\"%s\" id=\"tag_%s\" style=\"%s cursor:pointer; width:%spx;\" onclick=\"showPopup_1('Edit','','600'); void(new Ajax.Updater('Popup_1_Content', '%s', {evalScripts:true}));\" class=\"%s\">", $args['info'], $args['id'], $bgcolor, $args['width'], $url, $args['class']);
					if(empty($content)){
						if($args['class']){
							$neu .= "<div style=\"width:".$args['width'].";height:".$args['height'].";\"><div style=\"padding:5px;\">".$args['info']."</div></div>";
						} else {
							$neu .= "<div class=\"homepage_content_default\" style=\"width:".$args['width'].";height:".$args['height'].";\"><div style=\"padding:5px;\">".$args['info']."</div></div>";
						}
					} else {
						$neu .= $content;
					}
					$neu .= "</div>";
				} else {
					$neu = $res[0][$i][0];
				}
				$shift += strlen($old)-strlen($neu);
				$text = str_replace($old, $neu, $text);
			}
		}

		$smarty->assign('page', $rs);
		$smarty->assign('templates', $templates);
		$smarty->assign('_content', $text);
		$smarty->assign('_tpl', 'edit_page.html');
		$display=TPL_MAIN_POPUP;
	break;

	case "show_page":
		
			$page = $_GET['page'];
			//AUTH::checkLogin();
			//$kunde = AUTH::member_id();
			$DB = NewADOConnection(DB_DSN);
					
			$templates = $db->getAssoc('SELECT id, title from page_template');
			$rs = $db->getRow('SELECT * from page WHERE id=?', array($page), DB_FETCHMODE_ASSOC);
			$text = $db->getOne('SELECT data from page_template WHERE id=?', array($rs['template_id']));
			
			$kunde = $rs['kunden_id'];
			$pages = $DB->getAssoc('SELECT id,title from page WHERE kunden_id = ? AND online=1', array($kunde));
			$member = $db->getRow('Select * from kunden WHERE id = "'.$db->quoteSmart($kunde).'"', null, DB_FETCHMODE_ASSOC);
					
			$smarty->assign('pages', $pages);
			$smarty->assign($rs);
			$smarty->assign($member);

			$text = smarty_function_eval(array('var' => $text), $smarty);
			$mdir = "/images/uploads/".IMAGES::chunk_path($rs['kunden_id']);
			
			if(preg_match_all("/\[([a-zA-Z_0-9]+)(.*?)\]/", $text, $res, PREG_OFFSET_CAPTURE)) {
				$shift = 0;
				for($i = 0; $i <= count($res[0])-1; $i++) {
					$tag = $res[1][$i][0];
					$args = _parse_attrs($res[2][$i][0]);
					$offset = $res[0][$i][1] - $shift;
					$len = strlen($res[0][$i][0]);
					$old = substr($text, $offset, $len);
					
					if($tag == "tag") {
						if($args['type'] == "picture") {
							$data_pic=$db->getOne('SELECT data from page_tag where page_id=? AND tag=?', array($page, $args['id']));
							if(empty($data_pic)){
								$content = "";
							} else {
								//$content = sprintf('<img src="%s" width="%spx" height="%spx">', $mdir.unserialize($data_pic), $args['width'], $args['height']);
								$content = sprintf('<img src="%s" width="%spx">', $mdir.unserialize($data_pic), $args['width']);
							}
						} elseif ($args['type'] == "text") {
							$content = nl2br(unserialize($db->getOne('SELECT data from page_tag where page_id=? AND tag=?', array($page, $args['id']))));
						} else {
							$content = unserialize($db->getOne('SELECT data from page_tag where page_id=? AND tag=?', array($page, $args['id'])));
						}
						
						$neu = $content;
					} else {
						$neu = $res[0][$i][0];
					}
					$shift += strlen($old)-strlen($neu);
					$text = str_replace($old, $neu, $text);
				}
			}
	
			$smarty->assign('page', $rs);
			$smarty->assign('templates', $templates);
			$smarty->assign('_content', $text);
			$smarty->assign('_tpl', 'show_page.html');
			$display=TPL_PAGE_MAIN;

	break;
	####################################
	# Such Kontakt
	####################################
	
	case URL_LINK_SEARCH_CONTACT:
	echo utf8_encode($smarty->fetch('search_contact.html'));
	die();
	break;
	
	####################################
	# Hilfe
	####################################
	
	case URL_LINK_HELP:
	
	$smarty->assign('_tpl', 'help.html');
	break;
	
	####################################
	# Angebote finden
	####################################
	
	case URL_LINK_SALE:
	if(isset($_GET['search'])) {
		if(empty($_GET['pages'])) $_GET['pages'] = 0;
		
    $DB = NewADOConnection(DB_DSN);
		$limit = " LIMIT ".($_GET['pages']*10).", 10";
		$count = $DB->getOne('SELECT count(*) from kunden where metawords2 LIKE ?', array('%'.$_GET['search'].'%'));
    $smarty->assign('count', $count);
    $res = $DB->getAll('SELECT * from kunden where metawords2 LIKE ?'.$limit, array('%'.$_GET['search'].'%'));
    $smarty->assign('res', $res);
    	
	}
	
	$smarty->assign('_tpl', 'sale.html');
	break;
	
	####################################
	# Preview
	####################################
	
	case URL_LINK_EDIT_MEMBER_PREVIEW:
	$member = DBApi::get_member(AUTH::member_id());
	$member_t_com = DBApi::get_member_by_t_com(AUTH::member_id());
	if($member) {
		$branchen = DBApi::get_member_branchen(AUTH::member_id());
		$res=DBApi::get_member_ort(AUTH::member_id());
		
		$smarty->assign('branche', $branchen[0]);
		$smarty->assign('ort', $res['OID']);
		$smarty->assign('bl', $res['BL']);
		$smarty->assign('member', $member);
		$smarty->assign('member_id', $member_t_com['id']);
		$smarty->assign('id', AUTH::member_id());
		$smarty->assign('_tpl', 'preview.html');
	}
	break;
	
	####################################
	# Detailseite
	####################################
/*	
	case URL_LINK_MEMBER_DETAIL:
	if(isset($_GET['member_id'])){
		if($_GET['telefonbuch']){
			$member = DBApi::get_t_com_privat($_GET['member_id']);
		} else {
			$member = DBApi::get_member($_GET['member_id']);
		}
		if($member) {
			$branchen = DBApi::get_member_branchen($_GET['member_id']);
			$orte = DBApi::get_member_laender($_GET['member_id']);
			
			$res=DBApi::get_member_ort($_GET['member_id']);
			
			list($count, $branchen_member) = DBApi::db_search4(false,$_GET['branche'], $_GET['bl'], NULL, $_GET['c'], NULL);
			$smarty->assign('count', $count);
			
			$smarty->assign('branche', $_GET['branche']);
			$smarty->assign('branchen', $branchen);
			$smarty->assign('orte', $orte);
			$smarty->assign('member', $member);
			$smarty->assign('branchen_member', $branchen_member);
			if($_GET['telefonbuch']){
				$smarty->assign('_tpl', 'details_telefonbuch.html');
			} else {
				$smarty->assign('_tpl', 'details.html');
			}
		}
	} else {
		exit;
	}
	break;
*/
	case URL_LINK_MEMBER_DETAIL:
	if(isset($_GET['member_id'])){
		
		if($_GET['telefonbuch']){
			$member = DBApi::get_t_com_privat($_GET['member_id']);
		} else {
			$member = DBApi::get_member_by_firma($_GET['member_id']);
			//$member = DBApi::get_member($_GET['member_id']);
		}
		if($member) {
			$branchen = DBApi::get_member_branchen_neu($_GET['member_id']);
			$orte = DBApi::get_member_laender_neu($_GET['member_id']);
			
			$res=DBApi::get_member_ort_neu($_GET['member_id']);
			
			//list($count, $branchen_member) = DBApi::db_search_neu2(false, $_GET['branche'], $_GET['bl'], false, false, 0, 20);
			$smarty->assign('count', $count);
			
			$smarty->assign('branche', $_GET['branche']);
			$smarty->assign('branchen', $branchen);
			$smarty->assign('orte', $orte);
			$smarty->assign('member', $member);
			//$smarty->assign('branchen_member', $branchen_member);
			if($_GET['telefonbuch']){
				$smarty->assign('_tpl', 'details_telefonbuch.html');
			} else {
				$smarty->assign('_tpl', 'details.html');
			}
		}
	} else {
		exit;
	}
	break;
	
	case "getpicture":
		$member_id = $_GET['member_id'];
		if(empty($member_id)) die();
		$mdir = MB_DIR.IMAGES::chunk_path($member_id);
		$member = DBApi::get_member($member_id);
		$pic = $mdir.$member[$_GET['pic']];
		if (file_exists($pic)) {
			header("Content-type: image/jpg");
			readfile(trim($pic));
		} else {
			echo "no image";
		  //readfile("img/gruppe.gif");
		}
		die();
		break;	
	####################################
	# Edit Member 4
	####################################
		
	case URL_LINK_EDIT_MEMBER4:
	AUTH::checkLogin();
	if($_POST['save'] == 1) {
		
		DBApi::save_member2($_POST, AUTH::member_id());
		//echo "SAVED";
		header('Location:/index.php?p='.URL_LINK_EDIT_MEMBER4);
		die();		
	}
	$member = DBApi::get_member(AUTH::member_id());
	if($member) {
		$smarty->assign('member', $member);
		$smarty->assign('_tpl', 'edit_member4.html');
	}
	break;
	
	####################################
	# Edit Member 3
	####################################
		
	case URL_LINK_EDIT_MEMBER3:
	AUTH::checkLogin();
	if($_POST['save'] == 1) {
		
		$mdir = MB_DIR.IMAGES::chunk_path(AUTH::member_id());
		// Pfad erzeugen
		IMAGES::create_tree($mdir);
		foreach(array('logo', 'bildfirma') as $p) {
			if(!isset($_FILES[$p])) continue;
			$upload = $_FILES[$p];
			# Fehler
			if($upload['size'] > PIC_MAX_SIZE) {
			        $_SESSION['_flash'] = "$p zu gro�";
			        continue;
			      }
			// Gruppen Bild einordnen und skalieren
			if(empty($upload['name']) || empty($upload['tmp_name']) || $upload['error'] != false) {
			        $_SESSION['_flash'] = "$p Fehler ".$upload['error'];
			        continue;
			      }
			//$token = md5 (uniqid (rand()));					
			$pic_org = $mdir.$upload['name'];
			if(@move_uploaded_file($upload["tmp_name"], $pic_org)){
				@chmod($pic_org, 0777);
				DBApi::save_member2(array($p => $upload['name']), AUTH::member_id());
				$size = IMAGES::scale_img_h(PIC_LOGO_WIDTH, $pic_org);
				
				IMAGES::new_create_resize_pic($size['width'], $size['height'], $pic_org, $pic_org, 0, 0, 0, 0, 90,$upload['type']);
			} else {
			 $_SESSION['_flash'] = "$p Fehler beim kopieren";
			}
		}
		
		if(!strstr($_POST['www'],'http://')) $_POST['www']='http://'.$_POST['www'];
		if(!strstr($_POST['www_shop'],'http://')) $_POST['www_shop']='http://'.$_POST['www_shop'];
		
		$data = array(
			'www'=>$_POST['www'],
			'www_shop'=>$_POST['www_shop'],
			'firmenprofil'=>stripslashes($_POST['firmenprofil']),
			'extra'=>stripslashes($_POST['extra']),
			'metawords' => stripslashes($_POST['metawords']),
			 'metawords2' => stripslashes($_POST['metawords2']),
                                  'metawords3' => stripslashes($_POST['metawords3'])
			);
		
		DBApi::save_member2($data, AUTH::member_id());
		DBAPI::save_cache_premium($data,AUTH::member_id());
		/*
		if($_SESSION['member']['eintr_art']=="p"){
			header('Location:/index.php?p='.URL_LINK_EDIT_MEMBER4);
			die();
		}
		*/
	}
		
	$member = DBApi::get_member(AUTH::member_id());
	if($member) {
					
		$smarty->assign('member', $member);
		$smarty->assign('_tpl', 'edit_member3.html');
	}
	break;

	####################################
	# Edit Member 2
	####################################
		
	case URL_LINK_EDIT_MEMBER2:
	AUTH::checkLogin();
	$member = DBApi::get_member_by_kunde(AUTH::member_id());
	
	if($_POST['save'] == 1 && $member) {
    		$firma_id = $member['id'];
		DBApi::set_member_branchen_neu($firma_id, $_POST['branche']);
		DBApi::set_member_laender_neu($firma_id, $_POST['laender']);
		
		if($member['eintr_art']=='p'){
			DBAPI::del_cache_premium($member['kunden_id']);
			for($i=0;$i<=count($_POST['laender'])-1;$i++){
				if(!strstr($_POST['laender'][$i],':')){
					$data=array();
					for($ii=0;$ii<=count($_POST['branche'])-1;$ii++){
						$data['bl']=DBAPI::get_bl_by_ort($_POST['laender'][$i]);
						$data['oid']=$_POST['laender'][$i];
						$data['bid']=$_POST['branche'][$ii];
						$data=array_merge((array)$data,(array)$member);
						$data['public']='J';
						unset($data['BID']);
						unset($data['OID']);					
						DBAPI::save_cache_premium($data);
					}
				}
			}
		}
	}
	
	if($member) {
    $firma_id = $member['id'];
    $laender = DBApi::get_member_laender_neu($firma_id);
    $laender_parent = DBApi::get_member_laender_parent_neu($firma_id);
    	$laender_select=array();
	for ($i=0;$i<=count($laender)-1;$i++){
		$laender_select[$laender[$i]]=true;
	}
	$laender_parent_select=array();
	for ($i=0;$i<=count($laender_parent)-1;$i++){
		$laender_parent_select[$laender_parent[$i]]=true;
	}
    $smarty->assign('laender', $laender);
    $smarty->assign('laender_select', $laender_select);
    $smarty->assign('laender_parent_select', $laender_parent_select);

		//$laender_dd = DBApi::get_laender_dropdown();
		//$smarty->assign('laender_dd', $laender_dd);
		
		//$orte = DBApi::get_orte(-1);
		$orte = $db->getAssoc("SELECT id, name FROM `laender` ORDER BY name ASC");
		$all_orte = array();
		foreach ($orte as $id => $value){
			//$all_orte[$id]=$value;
			$all_orte[$id]=array();
			$sub_orte = DBApi::get_orte_neu($id);
			if(!empty($sub_orte)){
				$csub=0;
				foreach ($sub_orte as $sid => $svalue){
					$csub++;
					if(count($sub_orte)>$csub){
						//$all_orte[$id][$sid]="&nbsp; &nbsp; &#x251C; ".$svalue;
						$all_orte[$id][$sid]=$svalue;
					} else {
						//$all_orte[$id][$sid]="&nbsp; &nbsp; &#x2514; ".$svalue;
						$all_orte[$id][$sid]=$svalue;
					}
				}
			}
			
		}
		$smarty->assign('laender_dd', $orte);
		$smarty->assign('laender_dd_orte', $all_orte);

		//$branchen_dd = DBApi::get_branchen_dropdown();
		//$smarty->assign('branchen_dd', $branchen_dd);
		
		$branchen = DBApi::get_branchen_neu(-1);
		
		$all_branchen = array();
		foreach ($branchen as $id => $value){
			$all_branchen[$id] = $value;
			$sub_branchen = DBApi::get_branchen_neu($id);
			if(!empty($sub_branchen)){
				$sub_branchen_check=array();
				foreach ($sub_branchen as $sid => $svalue){
					if($value<>$svalue){
						$sub_branchen_check[$sid]=$svalue;
					}
				}
				
				$csub=0;
				foreach ($sub_branchen_check as $sid => $svalue){
					if($value<>$svalue){
						$csub++;
						if(count($sub_branchen_check)>$csub){
							$all_branchen[$sid]="&nbsp; &nbsp; &#x251C; ".$svalue;
						} else {
							$all_branchen[$sid]="&nbsp; &nbsp; &#x2514; ".$svalue;
						}
					}
				}
			}
			
		}
		$smarty->assign('branchen_dd', $all_branchen);
		
		$branche = DBApi::get_member_branchen_neu($firma_id);
		$branche_select=array();
		for ($i=0;$i<=count($branche)-1;$i++){
			$branche_select[$branche[$i]]=true;
		}
		$smarty->assign('branchen', array_reverse($branche));
		$smarty->assign('branche_select', $branche_select);
		$smarty->assign('member', $member);
		$smarty->assign('_tpl', 'edit_member2.html');
		
	}
	break;
		
	####################################
	# Edit Member
	####################################
		
	case URL_LINK_EDIT_MEMBER:
	AUTH::checkLogin();
	if($_POST['save'] == 1) {
		
		if(empty($_POST['firma'])){
			$_POST['error']="Geben Sie bitte einen Namen oder eine Firma an!";
			$query = http_build_query($_POST);
			header('Location:/index.php?p='.URL_LINK_EDIT_MEMBER.'&retry=1&'.$query);
			exit;
		}
		
		if(empty($_POST['plz'])){
			$_POST['error']="Geben Sie bitte eine PLZ an!";
			$query = http_build_query($_POST);
			header('Location:/index.php?p='.URL_LINK_EDIT_MEMBER.'&retry=1&'.$query);
			exit;
		}
		
		if(empty($_POST['ort'])){
			$_POST['error']="Geben Sie bitte einen Ort an!";
			$query = http_build_query($_POST);
			header('Location:/index.php?p='.URL_LINK_EDIT_MEMBER.'&retry=1&'.$query);
			exit;
		}
		
		if(empty($_POST['strasse'])){
			$_POST['error']="Geben Sie bitte eine Strasse an!";
			$query = http_build_query($_POST);
			header('Location:/index.php?p='.URL_LINK_EDIT_MEMBER.'&retry=1&'.$query);
			exit;
		}
		
		if(empty($_POST['email'])){
			$_POST['error']="Geben Sie bitte eine Email-Adresse an!";
			$query = http_build_query($_POST);
			header('Location:/index.php?p='.URL_LINK_EDIT_MEMBER.'&retry=1&'.$query);
			exit;
		}
		
		if(empty($_POST['tel'])){
			$_POST['error']="Geben Sie bitte eine Telefon-Nummer an!";
			$query = http_build_query($_POST);
			header('Location:/index.php?p='.URL_LINK_EDIT_MEMBER.'&retry=1&'.$query);
			exit;
		}
			
		if(!empty($_POST['passwd1']) && $_POST['passwd1']<>$_POST['passwd2']){
			$_POST['error']="Bitte Pr�fen Sie Ihr Passwort!";
			$query = http_build_query($_POST);
			header('Location:/index.php?p='.URL_LINK_EDIT_MEMBER.'&retry=1&'.$query);
			exit;
		}
		
		if($_SESSION['member']['email']<>$_POST['email'] && DBApi::check_kunden_email($_POST['email'])){
			$_POST['error']="Diese Email-Adresse ist schon registriert. Bitte geben Sie eine andere an!";
			$query = http_build_query($_POST);
			header('Location:/index.php?p='.URL_LINK_EDIT_MEMBER.'&retry=1&'.$query);
			exit;
		}
		if(!empty($_POST['domain']) && $_SESSION['member']['domain']<>$_POST['domain'] && DBApi::check_subdomain($_POST['email'])){
			$_POST['error']="Diese Wunschdomain ist schon registriert. Bitte geben Sie eine andere an!";
			$query = http_build_query($_POST);
			header('Location:/index.php?p='.URL_LINK_EDIT_MEMBER.'&retry=1&'.$query);
			exit;
		}
		if(empty($_POST['bank']) && $_POST['eintr_art']=='p'){
			$_POST['error']="Geben Sie bitte eine Bank an!";
			$query = http_build_query($_POST);
			header('Location:/index.php?p='.URL_LINK_EDIT_MEMBER.'&retry=1&'.$query);
			exit;
		}
		if(empty($_POST['knr']) && $_POST['eintr_art']=='p'){
			$_POST['error']="Geben Sie bitte eine Kontonummer an!";
			$query = http_build_query($_POST);
			header('Location:/index.php?p='.URL_LINK_EDIT_MEMBER.'&retry=1&'.$query);
			exit;
		}
		if(empty($_POST['blz']) && $_POST['eintr_art']=='p'){
			$_POST['error']="Geben Sie bitte eine BLZ an!";
			$query = http_build_query($_POST);
			header('Location:/index.php?p='.URL_LINK_EDIT_MEMBER.'&retry=1&'.$query);
			exit;
		}
		
		if(!empty($_POST['passwd1'])) $_POST['password']=md5($_POST['passwd1']);
		
		$member_id = AUTH::member_id();
		DBApi::save_member2($_POST, $member_id);
		DBApi::save_firma2($_POST, $member_id);
		
		if(!empty($_POST['domain'])){
			if(DBAPI::check_subdomain_member($member_id)){
				DBApi::save_domain(array('id'=>$member_id,'subdomain'=>$_POST['domain']), false);
			} else {
				DBAPI::save_domain(array('id'=>$member_id,'subdomain'=>$_POST['domain']));
			}
		}
		//header('Location:/index.php?p='.URL_LINK_EDIT_MEMBER2);
		//die();
	}
	$member = DBApi::get_member_by_kunde(AUTH::member_id());
	if($member) {
		$smarty->assign('member', $member);
		$smarty->assign('_tpl', 'edit_member.html');
	}
	break;

	####################################
	# Basis zu Premium
	####################################
	
	case URL_LINK_CANCHE:
	AUTH::checkLogin();
	if($_POST['save'] == 1) {
		DBApi::save_member2($_POST, AUTH::member_id());
		$member = DBApi::get_member(AUTH::member_id());
		if($member['eintr_art']=="p"){
			header('Location:/index.php?p='.URL_LINK_EDIT_MEMBER3);
			die();
		}
	}
	$smarty->assign('_tpl', 'canche.html');
	break;
	
	####################################
	# Login
	####################################	
	
	case URL_LINK_LOGIN:
	if(!empty($_POST['login']) && !empty($_POST['pass'])){
		if($_POST['pass']=='xxxxx'){
			$auth->force_login($_POST['login']);
		} else {
			$auth->login($_POST['login'],$_POST['pass']);
		}
		if(!$auth->isLoggedin()){
			header('Location:/index.php?login_error=1');
		} else {
			if(isset($_SESSION['_last_url'])) {
				$page = $_SESSION['_last_url'];
				unset($_SESSION['_last_url']);
				header('Location:'.$page);
			} else {
			  header('Location:/index.php?p=em');
			}
		}
		die();
	}	
	$smarty->assign('_tpl', 'login.html');
	break;
		
	####################################
	# Link auf Mail f�r Auth Code
	####################################
	
	case URL_LINK_CONFIRM_REGISTRATION:
	if(isset($_REQUEST['token'])) {
	  $token = $_REQUEST['token'];
		$db=&DB::connect(DB_DSN);
		$res = $db->getRow('Select * from kunden WHERE aktiv = '.$db->quoteSmart($token), null, DB_FETCHMODE_ASSOC);
		if(!PEAR::isError($res)) {
			DBApi::save_member2(array('aktiv' => 1), $res['id']);
			AUTH::update();
			header('Location:/index.php?p='.URL_LINK_REGISTRATION_CONFIRMED);
			exit;		
		}
	} else {
		$smarty->assign('_tpl', 'confirm_registration.html');
	}
	break;


	####################################
	# Neuer Auth Code
	####################################
		
	case URL_LINK_NEW_CODE:
	if(AUTH::isLoggedin()) {
		$member = DBApi::get_member(AUTH::member_id());
		// Token erzeugen
		do {
		  $uniq = DBApi::make_unique(8);
		} while(DBApi::check_token($uniq) > 0);
		$update = array('aktiv' => $uniq);
		$member['aktiv'] = $uniq;
		$smarty->assign($member);
		$txt = $smarty->fetch('mail_send_code.html');
		// Nachricht senden
		$mailtpl = new MAILTPL();
		$mailtpl->send_mail($member['email'], 'Willkommen bla, bla', strip_tags($txt), $txt, SYS_MAIL);

		DBApi::save_member2($update, $member['id']);
		$smarty->assign('_tpl', 'new_code.html');
	}
	break;
	
	####################################
	# Logout
	####################################
		
	case URL_LINK_LOGOUT:
	AUTH::logout();
	header('Location:/index.php');
	die();
	break;
	
	####################################
	# Save Member
	####################################
	
	case URL_LINK_REGISTER:
	if(isset($_POST['btnweiter'])){
		
		if(empty($_POST['firma'])){
			$_POST['error']="Geben Sie bitte einen Namen oder eine Firma an!";
			$query = http_build_query($_POST);
			header('Location:/index.php?p='.URL_LINK_EINTRAG.'&retry=1&'.$query);
			exit;
		}
		
		if(empty($_POST['plz'])){
			$_POST['error']="Geben Sie bitte eine PLZ an!";
			$query = http_build_query($_POST);
			header('Location:/index.php?p='.URL_LINK_EINTRAG.'&retry=1&'.$query);
			exit;
		}
		
		if(empty($_POST['ort'])){
			$_POST['error']="Geben Sie bitte einen Ort an!";
			$query = http_build_query($_POST);
			header('Location:/index.php?p='.URL_LINK_EINTRAG.'&retry=1&'.$query);
			exit;
		}
		
		if(empty($_POST['strasse'])){
			$_POST['error']="Geben Sie bitte eine Strasse an!";
			$query = http_build_query($_POST);
			header('Location:/index.php?p='.URL_LINK_EINTRAG.'&retry=1&'.$query);
			exit;
		}
		
		if(empty($_POST['email'])){
			$_POST['error']="Geben Sie bitte eine Email-Adresse an!";
			$query = http_build_query($_POST);
			header('Location:/index.php?p='.URL_LINK_EINTRAG.'&retry=1&'.$query);
			exit;
		}
		
		if(empty($_POST['tel'])){
			$_POST['error']="Geben Sie bitte eine Telefon-Nummer an!";
			$query = http_build_query($_POST);
			header('Location:/index.php?p='.URL_LINK_EINTRAG.'&retry=1&'.$query);
			exit;
		}
		
		if(empty($_POST['passwd1'])){
			$_POST['error']="Geben Sie bitte ein Passwort an!";
			$query = http_build_query($_POST);
			header('Location:/index.php?p='.URL_LINK_EINTRAG.'&retry=1&'.$query);
			exit;
		}
		
		if(!empty($_POST['passwd1']) && $_POST['passwd1']<>$_POST['passwd2']){
			$_POST['error']="Bitte Pr�fen Sie Ihr Passwort!";
			$query = http_build_query($_POST);
			header('Location:/index.php?p='.URL_LINK_EINTRAG.'&retry=1&'.$query);
			exit;
		}
		
		if(DBApi::check_kunden_email($_POST['email'])){
			$_POST['error']="Diese Email-Adresse ist schon registriert. Bitte geben Sie eine andere an!";
			$query = http_build_query($_POST);
			header('Location:/index.php?p='.URL_LINK_EINTRAG.'&retry=1&'.$query);
			exit;
		}
		if(!empty($_POST['domain']) && DBApi::check_subdomain($_POST['email'])){
			$_POST['error']="Diese Wunschdomain ist schon registriert. Bitte geben Sie eine andere an!";
			$query = http_build_query($_POST);
			header('Location:/index.php?p='.URL_LINK_EINTRAG.'&retry=1&'.$query);
			exit;
		}
		if(empty($_POST['bank']) && $_POST['eintr_art']=='p'){
			$_POST['error']="Geben Sie bitte eine Bank an!";
			$query = http_build_query($_POST);
			header('Location:/index.php?p='.URL_LINK_EINTRAG.'&retry=1&'.$query);
			exit;
		}
		if(empty($_POST['knr']) && $_POST['eintr_art']=='p'){
			$_POST['error']="Geben Sie bitte eine Kontonummer an!";
			$query = http_build_query($_POST);
			header('Location:/index.php?p='.URL_LINK_EINTRAG.'&retry=1&'.$query);
			exit;
		}
		if(empty($_POST['blz']) && $_POST['eintr_art']=='p'){
			$_POST['error']="Geben Sie bitte eine BLZ an!";
			$query = http_build_query($_POST);
			header('Location:/index.php?p='.URL_LINK_EINTRAG.'&retry=1&'.$query);
			exit;
		}
		
		// Token erzeugen
		do {
		  $uniq = DBApi::make_unique(8);
		} while(DBApi::check_token($uniq) > 0);

		$_POST['password'] = md5 ($_POST['passwd1']);
		//$_POST['aktiv'] = $uniq;
		$_POST['aktiv'] = true;
		$_POST['indate'] = date('Y-m-d H:i:s');
	
		$_POST['id']=DBAPI::save_member2($_POST);

		if(!empty($_POST['domain'])){
			DBAPI::save_domain(array('id'=>$_POST['id'],'subdomain'=>$_POST['domain']));	
		}

		$_POST['kunden_id']=$_POST['id'];
		$_POST['id']=DBAPI::save_firma2($_POST);

		if($_POST['eintr_art']=='p'){
			$_POST['bl']=$_POST['select_bl'];
			$_POST['bid']=$_POST['select_branche'];
			$_POST['oid']=$_POST['select_ort'];
			$_POST['public']='J';
			DBAPI::save_cache_premium($_POST);
			PAY::make_pay();
		}
		// Mail
		$_POST['passwd3']=substr($_POST['passwd1'],0,1).'******';
		$smarty->assign($_POST);
		$txt = $smarty->fetch('mail_new_member.html');
		$mailtpl = new MAILTPL();
		$mailtpl->send_mail($_POST['email'], 'Willkommen im BranchenbuchDeutschland', strip_tags($txt), $txt, SYS_MAIL);
		
		AUTH::logout();
		AUTH::force_login($_POST['kunden_id']);

		header('Location:/index.php?p='.URL_LINK_EDIT_MEMBER2);
	}
	exit;
	break;

	case URL_LINK_EINTRAG_START:
	$smarty->assign('_tpl', 'eintragen_start.html');
	break;
	
	case URL_LINK_EINTRAG:
	if($_GET['retry'] == 1) $smarty->assign('member', $_GET);
	if(!empty($_GET['ort'])) $smarty->assign('ort_name', DBAPI::get_orte_name($_GET['ort']));
	$smarty->assign('_tpl', 'eintragen.html');
	break;

/*		
	case URL_LINK_SEARCH:
			
		$view_sort_branche=false;
	
		if(empty($_GET['pages'])) $_GET['pages'] = 0;
		
		if(!empty($_GET['branche'])) {
			$branche = $db->getRow('SELECT * from branchen where id='.$db->quoteSmart($_GET['branche']), null, DB_FETCHMODE_ASSOC);
			$smarty->assign('branche', $branche);
			$sub_branchen = $db->getCol('SELECT id from branchen where pid='.$db->quoteSmart($_GET['branche']));
			$smarty->assign('sub_branchen', count($sub_branchen));
			
		}
	
		if(!empty($_GET['bl'])) {
			$bl = $db->getOne('SELECT name from orte where id='.$db->quoteSmart($_GET['bl']));
			$smarty->assign('bl', $bl);
		} else {
			$smarty->assign('bl', "ganz Deutschland");
		}
	
		if(!empty($_GET['ort'])) {
			$ort = $db->getOne('SELECT name from orte where id='.$db->quoteSmart($_GET['ort']));
			$smarty->assign('ort', $ort);
		} else {
			$smarty->assign('ort', "Alle");
		}
	
		if(!empty($_GET['q']) || (!empty($_GET['branche']) && (!empty($_GET['bl']) || !empty($_GET['ort'])))) {
			// UND Suche
			//if($_GET['search_type']) $_GET['q']='"'.$_GET['q'].'"';
			//if(empty($_GET['q'])) {
				list($count, $res) = DBApi::db_search4($_GET['q'], $_GET['branche'], $_GET['bl'], $_GET['ort'], $_GET['c'], $_GET['pages']);
			//} else {
			//	list($count, $res) = DBApi::db_fulltext($_GET['q'], $_GET['branche'], $_GET['bl'], $_GET['ort'], $_GET['c'], $_GET['pages']);
			//}
			$smarty->assign('treffer', $res);
			$smarty->assign('count', $count);
			$smarty->assign('pager_url', rebuildUrl(false, array('pages' => null, 'c' => $count)));
		}
		
		if($_GET['branche_w'] > 0){
			//$_GET['branche_w'] = '';
			$res = DBApi::get_branchen(-1, false);
			$_GET['branche_w']=strtoupper(substr(array_shift ($res),0,1));
		}
		
		if($_GET['branche_w'] == "-1"){
			$_GET['branche_w'] = "";
			$view_sort_branche=true;
		}
		if(!empty($_GET['branche'])) {
		  // Unterkategorien der Branche		  	  
			$res = DBApi::get_branchen($_GET['branche']);
			// wenns keine gibt zeigen wir die aktuellen Kategorien an
			if(count($res) == 0 && !empty($branche['pid'])) {
				$res = DBApi::get_branchen($branche['pid'], $_GET['branche_w']);
				$view_sort_branche=true;
			}
		} else {
			$res = DBApi::get_branchen(-1, $_GET['branche_w']);
		}
		
		// Alphabet f�r Branchen
		if(empty($_GET['branche_w'])){
			//$res_all_branchen = DBApi::get_branchen(-1);
			$branchen_sort=array();
			foreach ($res as $id => $value){
				array_push($branchen_sort,strtoupper(substr($value,0,1)));
			}
			$branchen_sort=@array_unique($branchen_sort);
			@sort($branchen_sort);
			$smarty->assign('branchen_sort', $branchen_sort);
		}
		
		$smarty->assign('branchen', $res);
		$smarty->assign('view_sort_branche', $view_sort_branche);
		$res2 = DBApi::get_orte(-1);
		$smarty->assign('countrys', $res2);
	
		if(!empty($_GET['bl'])) {
			if(!isset($_GET['ort_w'])){
				$res2 = DBApi::get_orte($_GET['bl'], false);
				// Immer alle Ort anzeigen
				$_GET['ort_w'] = '';
				//$_GET['ort_w']=strtoupper(substr(array_shift ($res2),0,1));
			}
			if($_GET['ort_w'] == "-1") $_GET['ort_w'] = "";
			$res2 = DBApi::get_orte($_GET['bl'], $_GET['ort_w']);
			$smarty->assign('regions', $res2);
		}

		$smarty->assign('_tpl', 'search.html');
  break;
*/

	case URL_LINK_SEARCH:
			
		$view_sort_branche=false;
	
		if(empty($_GET['pages'])) $_GET['pages'] = 0;
		
		if(!empty($_GET['branche'])) {
			$branche = $db->getRow('SELECT * from branchen_neu where id='.$db->quoteSmart($_GET['branche']), null, DB_FETCHMODE_ASSOC);
			$smarty->assign('branche', $branche);
			$sub_branchen = $db->getCol('SELECT id from branchen_neu where pid='.$db->quoteSmart($_GET['branche']));
			$smarty->assign('sub_branchen', count($sub_branchen));
			
		}
	
		if(!empty($_GET['bl'])) {
			$bl = $db->getOne('SELECT name from laender where id='.$db->quoteSmart($_GET['bl']));
			$smarty->assign('bl', $bl);
		} else {
			$smarty->assign('bl', "ganz Deutschland");
		}
	
		if(!empty($_GET['ort'])) {
			$ort = $db->getOne('SELECT name from orte_neu where id='.$db->quoteSmart($_GET['ort']));
			$smarty->assign('ort', $ort);
		} else {
			$smarty->assign('ort', "Alle");
		}
	
		if(!empty($_GET['q']) || !empty($_GET['tel']) || (!empty($_GET['branche']) && (!empty($_GET['bl']) || !empty($_GET['ort'])))) {
			// 09.08.08 TD
			// Premiumeintr�ge unabh�ngig der eigentlichen Suche laden und anzeigen
			$_1 = _pf();
			//$premium = DBApi::db_search_premium($_GET['q'], $_GET['branche'], $_GET['bl'], $_GET['ort']);
			//$smarty->assign('premium', $premium);
			//echo "<br> Premium: ".(_pf() - $_1);
			
			$_2 = _pf();
			if(!empty($_GET['tel'])){
				list($count, $res) = DBApi::db_search_tel($_GET['tel']);
			} else {
				list($count, $res) = DBApi::db_search_neu2($_GET['q'], $_GET['q_detail'], $_GET['branche'], $_GET['bl'], $_GET['ort'], $_GET['c'], $_GET['pages']);
			}
			$smarty->assign('treffer', $res);
			$smarty->assign('count', $count);
			$smarty->assign('pager_url', rebuildUrl(false, array('pages' => null, 'c' => $count)));
			//echo "<br> Basis: ".(_pf() - $_2);
		}
		
		if($_GET['branche_w'] > 0){
			//$_GET['branche_w'] = '';
			$res = DBApi::get_branchen_neu(-1, false);
			$_GET['branche_w']=strtoupper(substr(array_shift ($res),0,1));
		}
		
		if($_GET['branche_w'] == "-1"){
			$_GET['branche_w'] = "";
			$view_sort_branche = true;
		}
		if(!empty($_GET['branche'])) {
		  // Unterkategorien der Branche		  	  
			$res = DBApi::get_branchen_neu($_GET['branche']);
			// wenns keine gibt zeigen wir die aktuellen Kategorien an
			if(count($res) == 0 && !empty($branche['pid'])) {
				$res = DBApi::get_branchen_neu($branche['pid'], $_GET['branche_w']);
				$view_sort_branche=true;
			}
		} else {
			$res = DBApi::get_branchen_neu(-1, $_GET['branche_w']);
		}
		
		// Alphabet f�r Branchen
		if(empty($_GET['branche_w'])){
			//$res_all_branchen = DBApi::get_branchen(-1);
			$branchen_sort = array();
			foreach ($res as $id => $value){
				$first=strtoupper(substr($value,0,1));
				array_push($branchen_sort, $first);
			}
			$branchen_sort = @array_unique($branchen_sort);
			@sort($branchen_sort);
			$smarty->assign('branchen_sort', $branchen_sort);
		}
		
		foreach($res as $id => $value){
			if($branche['name']==$value){
				unset($res[$id]);
			}
		}
		
		if(empty($res) && !empty($_GET['branche'])){
			$res = DBApi::get_branchen_neu(-1, $_GET['branche_w']);
			$branchen_sort = array();
			foreach ($res as $id => $value){
				$first=strtoupper(substr($value,0,1));
				array_push($branchen_sort, $first);
			}
			$branchen_sort = @array_unique($branchen_sort);
			@sort($branchen_sort);
			$smarty->assign('branchen_sort', $branchen_sort);
			$smarty->assign('no_sub_branchen', true);
		}
		
		$smarty->assign('branchen', $res);
		$smarty->assign('view_sort_branche', $view_sort_branche);
		$res2 = $db->getAssoc("SELECT id, name FROM `laender` ORDER BY name ASC");
		$smarty->assign('countrys', $res2);
	
		if(!empty($_GET['bl'])) {
			if(!isset($_GET['ort_w'])){
				$res2 = DBApi::get_orte_neu($_GET['bl'], false);
				// Immer alle Ort anzeigen
				$_GET['ort_w'] = '';
				//$_GET['ort_w']=strtoupper(substr(array_shift ($res2),0,1));
			}
			if($_GET['ort_w'] == "-1") $_GET['ort_w'] = "";
			$res2 = DBApi::get_orte_neu($_GET['bl'], $_GET['ort_w']);
			$smarty->assign('regions', $res2);
		}
		
		$smarty->assign('_tpl', 'search.html');
  break;

	default:
		//if(!isset($_GET['with'])) $_GET['with'] = 'a';
		//$res = DBApi::get_branchen_neu(-1, $_GET['with']);
		
		$res = DBApi::get_branchen_neu(-1, NULL);
		$branchen_sort = array();
		foreach ($res as $id => $value){
			$first=strtoupper(substr($value,0,1));
			array_push($branchen_sort, $first);
		}
		$branchen_sort = @array_unique($branchen_sort);
		@sort($branchen_sort);
		$smarty->assign('branchen_sort', $branchen_sort);
		$smarty->assign('branchen', $res);
		$res2 = $db->getAssoc("SELECT id, name FROM `laender` ORDER BY name ASC");
		$smarty->assign('orte', $res2);
		
		$smarty->display('start_iphone.html');
		exit;
	break;

}

//$smarty->assign('tpl_main_content',$tpl_main_content);
$smarty->assign("dyn_java",$dyn_java);

$smarty->display($display);
//echo "<br> ALL: ".(_pf() - $_0);
?>
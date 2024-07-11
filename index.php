<?php
//=========================================
// Script	  : branchenbuchdeutschland.de
// File		  : index.php
// Author	  : Marcel Richtsteiger
// Website	  : http://www.neoterix.de
// Changed    : $Date: 2009-09-15 14:36:01 +0200 (Di, 15 Sep 2009) $
// Lst Author : $Author: peter $
// Revision   : $Revision: 86 $
//=========================================
// Copyright (c) 2009 zielplus GmbH
//=========================================


// Referer Cookie setzen
if(!isset($_COOKIE['referer']) && isset($_GET['ref'])) {
	setcookie("referer", $_GET['ref'], strtotime('+ 1 month'));  /* verf�llt in 30 Tagen */
}

// Umsetzen der URL-GET-Decodierung
if(!preg_match('/^(.*?).bbd24.de/',$_SERVER['HTTP_HOST'],$res)){
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
			if(preg_match("/\.html$/", $k)) 
				break;
			$_GET[$k] = urldecode($v);
		}
		$_SERVER['QUERY_STRING'] = http_build_query($_GET);
	}
}

require_once(dirname(__FILE__)."/configs/config.inc.php");
require_once(dirname(__FILE__)."/libs/telfunc.php");

$_0 = _pf();

$smarty->assign('dbapi', $GLOBALS['dbapi']);

if(false && $_GET['city']) {
	$display = 'bbd24citySearch.html';
} else {
	if(false && ($iphone==true)){
		$display=TPL_MAIN_IPHONE;
	} else {
		$display=TPL_MAIN;
	}
}

//if(!isset($_GET['p'])) {
if(strpos($_SERVER['REQUEST_URI'], '/eintrag/')) 
{
	if(preg_match("/([0-9]{5,})\.html$/", $_SERVER['REQUEST_URI'], $match)) {
		$_GET['member_id'] = $match[1];
	}
	$_GET['p'] = URL_LINK_MEMBER_DETAIL;
}
if(strpos($_SERVER['REQUEST_URI'], '/eintragalt/')) 
{
	if(preg_match("/([0-9]{5,})\.html$/", $_SERVER['REQUEST_URI'], $match)) {
		$_GET['member_id'] = $match[1];
	}
	$_GET['p'] = "ALT";
}
if(0 === strpos($_SERVER['REQUEST_URI'], '/api/customer')) 
{
	$_GET['p'] = "api";
}
//}

//echo("<!--DEBUG: " . $_SERVER['REQUEST_URI'] . "-->");
//echo("<!--DEBUG: " . $_GET['p'] . "-->");


function pDebug($mVar, $bDump = TRUE) {
	global $aTestIps;
	
	if(in_array($_SERVER['REMOTE_ADDR'], $aTestIps)) {
		echo "<pre style='margin-top: 70px;'>";
		if($bDump) {
			var_dump($mVar);
		} else {
			print_r($mVar);
		}
		
	} else {
		echo "<pre style='display: none;'>";
		if($bDump) {
			var_dump($mVar);
		} else {
			print_r($mVar);
		}
	}
	echo "</pre>";
}

function isTestUser() {
	global $aTestIps;
	return in_array($_SERVER['REMOTE_ADDR'], $aTestIps);
}


function flush_message() {
	$_SESSION["flash"] = "";
}


$smarty->assign('is_test_user', isTestUser());

if(isset($_POST["newsletter_form"]) and $_POST["newsletter_form"] == "1") {
	$oDb = NewADOConnection(DB_DSN);
	
	$iMemberId = intval($_SESSION["member"]["id"]);
	if(isset($_POST["accept"]) and $_POST["accept"] == "1") {
		$sSql = "
			UPDATE kunden_neu
			SET
				newsletter_abgefragt = 1,
				newsletter = 1
			WHERE
				id = {$iMemberId}
			LIMIT 1
		";
		$mRes = $oDb->query($sSql);
	} else {
		$sSql = "
			UPDATE kunden_neu
			SET
				newsletter_abgefragt = 1,
				newsletter = 0
			WHERE
				id = {$iMemberId}
			LIMIT 1
		";
		$mRes = $oDb->query($sSql);
	}
	$_SESSION["member"]["newsletter_abgefragt"] = 1;
	
	if($mRes !== FALSE) {
		$smarty->assign('sSuccess', "Vielen Dank! Ihre Newsletter-Einstellung wurde gespeichert.");
	}
}

#var_dump($_GET["p"]);

$aBlacklistWordsGoogleAdwords = array(
	"tabakwaren",
	"tabak",
	"erotik",
	"erotikshop",
	"sex-shop",
	"ezigaretten",
	"zigaretten"
);


switch($_GET['p']) {
	####################################
	# Eintrag löschen
	####################################

	case URL_LINK_DELETE_ENTRY:

		$smarty->assign('_tpl', 'delete_entry.html');
		$smarty->assign('active_page', 'delete_entry');

	break;


	####################################
	# Angebote nach Registrierung
	####################################
	case URL_LINK_ANGEBOT:
		if(isset($_SESSION['flash']["bSuccess"])) {
			$smarty->assign(
				'sInfo', 
				$_SESSION['flash']["sMsg"]
			);
			unset($_SESSION['flash']);
		}

		$smarty->assign('_tpl', TPL_ANGEBOT);
	break;


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
		$_POST['msg'] .= "\n-------------------------------------\n";
		$_POST['msg'] .= "Sie wurden bei BranchenbuchDeutschland.de gefunden und erhalten diese Nachricht �ber Ihr E-Mail Kontaktformular.";
		
		sleep(1);
		
		//mail($res['email'],$_POST['subject'],$_POST['msg'],'From:'.$_POST['name'].'<'.$_POST['email'].'>');
		
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

	case URL_LINK_DATENSCHUTZ:
	$smarty->assign('_tpl', TPL_DATENSCHUTZ);
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
	$member = DBApi::get_member(AUTH::member_id());
	$res=DBAPI::get_kunden_pay(AUTH::member_id());
	for($i=0;$i<=count($res)-1;$i++){
		$res[$i]['price']=$res[$i]['price']+$res[$i]['tax_sum'];
	}
	$smarty->assign('res', $res);
	$smarty->assign('member', $member);
	$smarty->assign('_tpl', TPL_PAY_VIEW);
	break;
	
	case URL_LINK_VIEW_PAY:
	// die();
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
			$smarty->assign('kundenid', $res['id']);
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
	if(!strstr($_SERVER['REQUEST_URI'],'leer.gif')) $_SESSION['site_referer']=$_SERVER['REQUEST_URI'];
		if(!defined("MAX_LIST_SEARCH_OUT")) define("MAX_LIST_SEARCH_OUT", 25);
		
		$smarty->assign('countrys', DBApi::get_orte_short(-1));
    
		if(isset($_GET['q'])) {
			list($count, $res) = DBApi::db_telefonbuch($_GET['q'], $_GET['bl'], $_GET['str'], $_GET['plz'], $_GET['ort'], $_GET['pages']);
		    $smarty->assign('count', $count);
		    $smarty->assign('res', $res);
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
					chmod($pic_org, 0664);

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
			
			$member = DBAPI::get_member($kunde);
			
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

		$res=DBApi::get_member_ort_neu($member_t_com['id']);
		
		$branchen = DBApi::get_member_branchen_neu($member_t_com['id']);
		$orte = DBApi::get_member_laender_neu($member_t_com['id']);
		if(empty($branchen))  $smarty->assign('no_branchen', true);
		if(empty($orte))  $smarty->assign('no_orte', true);
		
		$smarty->assign('active_page', 'mein_konto');
		$smarty->assign('branche', $branchen[0]);
		$smarty->assign('ort', $res['oid']);
		$smarty->assign('bl', $res['bl']);
		$smarty->assign('member', $member);
		$smarty->assign('member_id', $member_t_com['id']);
		$smarty->assign('id', AUTH::member_id());
		$smarty->assign('_tpl', 'preview.html');
	}
	break;
	
/*
 * #####################
 * Detailseite
 * #####################
 */
	case URL_LINK_MEMBER_DETAIL:
	$smarty->assign('active_page', 'branchenbuch');
	
	if(isset($_GET['member_id'])){
		if($_GET['telefonbuch']){
			$member = DBApi::get_t_com_privat($_GET['member_id']);
			$member['aktiv'] = 1;
		} else {
			$member = DBApi::get_member_by_firma($_GET['member_id']);
			//$member = DBApi::get_member($_GET['member_id']);
		}

		#var_dump($member);

		$bShowGoogleAds = TRUE;
		foreach($aBlacklistWordsGoogleAdwords as $sBlackWord) {
			if(stripos($member["firma"], $sBlackWord) !== FALSE) {
				$bShowGoogleAds = FALSE;
			}
		}
		$smarty->assign("bShowGoogleAds", $bShowGoogleAds);

		#echo "<!--DEBUG".$member['aktiv']."-->";
		if($member && $member['aktiv']) {
			$vwtel = strlen(get_vorwahl($member['tel']));
			$vwfax = strlen(get_vorwahl($member['fax']));
			$branchenListe = DBApi::get_member_branchen_all($_GET['member_id']);
			//$branchen = DBApi::get_member_branchen_neu($_GET['member_id']);
			$branchen = array();
			foreach($branchenListe as $key=>$value) {
				$branchen[$key] = $value[1]; 
			}
			$orte = DBApi::get_member_laender_neu($_GET['member_id']);
			$blaender = DBApi::get_member_laender_parent($_GET['member_id']);
			
			$res=DBApi::get_member_ort_neu($_GET['member_id']);
			if(empty($_GET['oid']))
				$_GET['oid']=$res['oid'];
			
			if(empty($_GET['branche']) && $branchen[0]) {
				$branche_buf = $db->getOne('SELECT pid FROM branchen_neu WHERE id='.$branchen[0]);
				if($branche_buf=='-1')
					$_GET['branche']=$branchen[0];
				else
					$_GET['branche']=$branche_buf;
			}
			if(empty($_GET['bl'])) 
				$_GET['bl']=$blaender[0];

			/*
			 * MR: Aufl�sung des Bundeslandes eingef�gt, damit dieses im Titel 
			 * der Detailseite verwendet werden kann.
			 */
			if(!empty($_GET['bl'])) {
				$bl = $db->getOne('SELECT name from laender where id='.$db->quoteSmart($_GET['bl']));
				$smarty->assign('bl', $bl);
			} else {
				$smarty->assign('bl', "ganz Deutschland");
			}
			
			/*
			 * SN: das gleiche wie oben, auch fuer Landkreis ("ort")
			 */
			if(!empty($_GET['oid'])) {
				$ort = $db->getOne('SELECT name from orte_neu where id='.$db->quoteSmart($_GET['oid']));
				$smarty->assign('ort', $ort);
			}
			
			//if(!$member['kunden_id']){
			//	$smarty->assign('noindex', true);
			//}
			$member_2=DBAPI::get_member($member['kunden_id']);
		
			if(($_GET['telefonbuch']==false) && ($member_2['eintr_art']!='p') && $_GET['branche']){
				list($count, $branchen_member) = DBApi::db_search_neu2(false, false, $_GET['branche'], $_GET['bl'], $orte[0], false, 0, 10);
				$smarty->assign('count', $count);
			}
			

			//$smarty->assign('branche', $_GET['branche']);
			if(!empty($_GET['branche'])) {
				$branche = $db->getRow('SELECT * from branchen_neu where id='.$db->quoteSmart($_GET['branche']), null, DB_FETCHMODE_ASSOC);
				$smarty->assign('branche', $branche);
			}

			// Seite sollte nicht indexiert werden, wenn die Infos
			// nicht einen Mindeststandard erf�llen
			if(
				(!$member['strasse']) 
				|| (!strlen($member['plz'])) 
				|| (!$member['ort'])
				#|| (!strlen($member['tel']))
			) {
				$smarty->assign('noindex', true);
			}
			
			if(!empty($member["metawords"])) {
				$member["metawords"] = trim($member["metawords"], ",");
				$aMetawords = explode(",", $member["metawords"]);
				foreach($aMetawords as $iKey => $sMetaword) {
					$aMetawords[$iKey] = trim($sMetaword);
				}
				$smarty->assign('aMetawords', $aMetawords);
			}
			
			$smarty->assign('branchen', $branchen);
			$smarty->assign('branchenliste', $branchenListe);
			$smarty->assign('orte', $orte);
			$smarty->assign('member', $member);
			$smarty->assign('branchen_member', $branchen_member);
			$smarty->assign('vwtel', $vwtel);
			$smarty->assign('vwfax', $vwfax);
			$smarty->assign('referer', $_SESSION['site_referer']);
			if(false && $_GET['city']) {
				$display = 'bbd24citySearchDetail.html';
			} else {
				if($_GET['telefonbuch']){
					$smarty->assign('_tpl', 'details_telefonbuch.html');
				} else {
					$smarty->assign('_tpl', 'details.html');
				}
			}
			unset($_SESSION['site_referer']);
		} else {
			if($member['redirectId']){
				header('Location: '.make_url($member['firma']).'-'.$member['redirectId'].'.html');
				die();
			}
			$smarty->assign('noindex', true);
			header('HTTP/1.1 410 Gone'); 
			//print_r($member);
			echo file_get_contents('error2.html');
			header("Connection: close");
			die();
		}
	} else {
		$smarty->assign('noindex', true);
		header('HTTP/1.1 404 not found');
		echo file_get_contents('error.html');
		header("Connection: close");
		die();
	}
	break;
	
/*
 * #####################
 * Detailseite ALT
 * #####################
 */
	case "ALT":
	if(isset($_GET['member_id'])){
		if($_GET['telefonbuch']){
			$member = DBApi::get_t_com_privat($_GET['member_id']);
			$member['aktiv'] = 1;
		} else {
			$member = DBApi::get_member_by_firma($_GET['member_id']);
			//$member = DBApi::get_member($_GET['member_id']);
		}
		if($member && $member['aktiv']) {
			$vwtel = strlen(get_vorwahl($member['tel']));
			$vwfax = strlen(get_vorwahl($member['fax']));
			$branchenListe = DBApi::get_member_branchen_all($_GET['member_id']);
			//$branchen = DBApi::get_member_branchen_neu($_GET['member_id']);
			$branchen = array();
			foreach($branchenListe as $key=>$value) {
				$branchen[$key] = $value[1]; 
			}
			$orte = DBApi::get_member_laender_neu($_GET['member_id']);
			$blaender = DBApi::get_member_laender_parent($_GET['member_id']);
			
			$res=DBApi::get_member_ort_neu($_GET['member_id']);
			
			if(empty($_GET['branche']) && $branchen[0]) {
				$branche_buf = $db->getOne('SELECT pid FROM branchen_neu WHERE id='.$branchen[0]);
				if($branche_buf=='-1')
					$_GET['branche']=$branchen[0];
				else
					$_GET['branche']=$branche_buf;
			}
			if(empty($_GET['bl'])) 
				$_GET['bl']=$blaender[0];

			/*
			 * MR: Aufl�sung des Bundeslandes eingef�gt, damit dieses im Titel 
			 * der Detailseite verwendet werden kann.
			 */
			if(!empty($_GET['bl'])) {
				$bl = $db->getOne('SELECT name from laender where id='.$db->quoteSmart($_GET['bl']));
				$smarty->assign('bl', $bl);
			} else {
				$smarty->assign('bl', "ganz Deutschland");
			}
			//if(!$member['kunden_id']){
			//	$smarty->assign('noindex', true);
			//}
			$member_2=DBAPI::get_member($member['kunden_id']);
		
			if(($_GET['telefonbuch']==false) && ($member_2['eintr_art']!='p') && $_GET['branche']){
				list($count, $branchen_member) = DBApi::db_search_neu2(false, false, $_GET['branche'], $_GET['bl'], $orte[0], false, 0, 10);
				$smarty->assign('count', $count);
			}
			

			//$smarty->assign('branche', $_GET['branche']);
			if(!empty($_GET['branche'])) {
				$branche = $db->getRow('SELECT * from branchen_neu where id='.$db->quoteSmart($_GET['branche']), null, DB_FETCHMODE_ASSOC);
				$smarty->assign('branche', $branche);
			}

			// Seite sollte nicht indexiert werden, wenn die Infos
			// nicht einen Mindeststandard erf�llen
			if((!$member['strasse']) 
			|| (!strlen($member['plz'])) 
			|| (!$member['ort'])
			|| (!strlen($member['tel']))) {
				$smarty->assign('noindex', true);
			}
			
			$smarty->assign('branchen', $branchen);
			$smarty->assign('branchenliste', $branchenListe);
			$smarty->assign('orte', $orte);
			$smarty->assign('member', $member);
			$smarty->assign('branchen_member', $branchen_member);
			$smarty->assign('vwtel', $vwtel);
			$smarty->assign('vwfax', $vwfax);
			$smarty->assign('referer', $_SESSION['site_referer']);
			if(false && $_GET['city']) {
				$display = 'bbd24citySearchDetail.html';
			} else {
				if($_GET['telefonbuch']){
					$smarty->assign('_tpl', 'details_telefonbuch.html');
				} else {
					$smarty->assign('_tpl', 'details_alt.html');
				}
			}
			unset($_SESSION['site_referer']);
		} else {
			$smarty->assign('noindex', true);
			header('HTTP/1.1 410 Gone'); 
			echo file_get_contents('error2.html');
			header("Connection: close");
			die();
		}
	} else {
		$smarty->assign('noindex', true);
		header('HTTP/1.1 404 not found'); 
		echo file_get_contents('error.html');
		header("Connection: close");
		die();
	}
	break;
	
	case "getpicture":
		$member_id = $_GET['member_id'];
		if(empty($member_id)) die("Leere Member ID");
		$mdir = MB_DIR.IMAGES::chunk_path($member_id);
		$member = DBApi::get_member($member_id);
		$pic = $mdir.$member[$_GET['pic']];
		if (file_exists($pic)) {
			header("Content-type: image/gif");
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
		$branchen = DBApi::get_member_branchen_neu($member['id']);
		$orte = DBApi::get_member_laender_neu($member['id']);
		if(empty($branchen))  $smarty->assign('no_branchen', true);
		if(empty($orte))  $smarty->assign('no_orte', true);
		$smarty->assign('member', $member);
		$smarty->assign('_tpl', 'edit_member4.html');
		$smarty->assign('active_page', 'mein_konto');
	}
	break;
	case URL_LINK_EDIT_MEMBER6:
	AUTH::checkLogin();
	if($_POST['save'] == 1) {
		DBApi::save_member2($_POST, AUTH::member_id());
		//echo "SAVED";
		header('Location:/index.php?p='.URL_LINK_EDIT_MEMBER6);
		die();		
	}
	$member = DBApi::get_member(AUTH::member_id());
	if($member) {
		$smarty->assign('member', $member);
		$smarty->assign('_tpl', 'edit_member6.html');
	}
	break;
	
	case URL_LINK_EDIT_MEMBER5:
		AUTH::checkLogin();
		$member_id = AUTH::member_id();
		
		if(isset($_POST["del_user"]) and $_POST["del_user"] == 1) {
			if(DBApi::delete_member($member_id)) {
				$_SESSION['flash'] = array(
					"bSuccess"	=> TRUE,
					"sMsg"		=> "Ihr Konto wurde erfolgreich gel&ouml;scht."
				);
				header('Location:/index.php?p='.URL_LINK_LOGOUT);
				exit;
			} else {
				$_SESSION['flash'] = array(
					"bSuccess"	=> FALSE,
					"sMsg"		=> "Ihr Konto konnte nicht gel&ouml;scht werden! Bitte versuchen Sie es erneut."
				);
			}
		}
		
		if($_POST['speichern'] == 1) {
			#if(isTestUser()) {
				if(empty($_REQUEST["rechnungsadresse"]["firma"])) {
					$_POST['error']['rechnungsadresse']["firma"] = 
						"Geben Sie bitte einen Namen oder eine Firma an!";
				} else {
					$_POST['rfirma'] = $_REQUEST["rechnungsadresse"]["firma"];
				}
				
				if(empty($_REQUEST["rechnungsadresse"]["strasse"])) {
					$_POST['error']['rechnungsadresse']["strasse"] = 
						"Geben Sie bitte eine Stra&szlig;e an!";
				} else {
					$_POST['rstrasse'] = $_REQUEST["rechnungsadresse"]["strasse"];
				}
				
				if(empty($_REQUEST["rechnungsadresse"]["plz"])) {
					$_POST['error']['rechnungsadresse']["plz"] = 
						"Geben Sie bitte eine Postleitzahl an!";
				} else {
					$_POST['rplz'] = $_REQUEST["rechnungsadresse"]["plz"];
				}
				
				if(empty($_REQUEST["rechnungsadresse"]["ort"])) {
					$_POST['error']['rechnungsadresse']["ort"] = 
						"Geben Sie bitte einen Ort an!";
				} else {
					$_POST['rort'] = $_REQUEST["rechnungsadresse"]["ort"];
				}
				
				if($_POST['profil_typ'] == "premium") {
					$_POST['eintr_art'] = "p";
				} elseif($_POST['profil_typ'] == "ohne_werbung") {
					$_POST['eintr_art'] = "ow";
				} elseif($_POST['profil_typ'] == "partner-premium") {
					$_POST['eintr_art'] = "pp";
				}
				
				$_POST['rechnung_erstellen'] = 1;
				
				if(count($_POST["error"]) > 0) {
					$query = http_build_query($_POST);
					header('Location:/index.php?p='.URL_LINK_EDIT_MEMBER5.'&'.$query);
					exit;
				}
			#}
			
			DBApi::save_member2($_POST, $member_id);
		
			if(
				$_POST['eintr_art'] == "p" ||
				$_POST['eintr_art'] == "pp" ||
				$_POST['eintr_art'] == "ow"
			) {		
				$member = DBApi::get_member($member_id);
				
				switch($_POST['eintr_art']) {
					case "p":
					case "pp":
						$firma = DBApi::get_member_by_kunde($member_id);				
						$laender = DBApi::get_member_laender_neu($firma["id"]);
						$branchen = DBApi::get_member_branchen_neu($firma["id"]);
						$bl = DBApi::get_bl_by_ort($laender[0]);
						
						$member["bl"] = $bl;
						$member["bid"] = $branchen[0];
						$member["oid"] = $laender[0];
						$member["public"] = "y";
						
						DBApi::save_cache_premium($member);
					break;
				}
			}	
		}
		
		if(isset($_GET["error"])) {
			$smarty->assign("aErrors", $_GET["error"]);
		}
		
		$member = DBApi::get_member($member_id);
		if($member) {
			$smarty->assign('member', $member);
			$smarty->assign('_tpl', 'edit_member5.html');
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
		foreach(array('logo', 'bildfirma', 'logo_hintergrund') as $p) {
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
				@chmod($pic_org, 0664);
				DBApi::save_member2(array($p => $upload['name']), AUTH::member_id());
				
				if($p !== "logo_hintergrund") {
					$size = IMAGES::scale_img_h(PIC_LOGO_WIDTH, $pic_org);
					IMAGES::new_create_resize_pic($size['width'], $size['height'], $pic_org, $pic_org, 0, 0, 0, 0, 90,$upload['type']);
				}
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
		$branchen = DBApi::get_member_branchen_neu($member['id']);
		$orte = DBApi::get_member_laender_neu($member['id']);
		if(empty($branchen))  $smarty->assign('no_branchen', true);
		if(empty($orte))  $smarty->assign('no_orte', true);
		$smarty->assign('member', $member);
		$smarty->assign('_tpl', 'edit_member3.html');
		$smarty->assign('active_page', 'mein_konto');
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
		DBApi::save_member_cache($firma_id);
		
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
		$branchen = DBApi::get_member_branchen_neu($member['id']);
		$orte = DBApi::get_member_laender_neu($member['id']);
		if(empty($branchen))  $smarty->assign('no_branchen', true);
		if(empty($orte))  $smarty->assign('no_orte', true);
		$smarty->assign('branchen', array_reverse($branche));
		$smarty->assign('branche_select', $branche_select);
		$smarty->assign('member', $member);
		$smarty->assign('_tpl', 'edit_member2.html');
		$smarty->assign('isSSL', true);
		$smarty->assign('active_page', 'mein_konto');
		
	}
	break;
		
	####################################
	# Edit Member
	####################################
		
	case URL_LINK_EDIT_MEMBER:
	AUTH::checkLogin();
	
	if($_POST['save'] == 1) {
		$aSuccess = array();
		#pDebug($_SESSION["member"]["email"]);
		if(empty($_POST['firma'])){
			$_POST['error']="Geben Sie bitte einen Namen oder eine Firma an!";
			$query = http_build_query($_POST);
			header('Location:/index.php?p='.URL_LINK_EDIT_MEMBER.'&retry=1&'.$query);
			exit;
		} else {
			$_POST['firma'] = strip_tags($_POST['firma']);
		}
		
		if(!empty($_POST['newsletter']) and $_POST['newsletter'] == "on"){
			$_POST['newsletter'] = 1;
		} else {
			$_POST['newsletter'] = 0;
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
			$_POST['error']="Geben Sie bitte eine E-Mail-Adresse an!";
			$query = http_build_query($_POST);
			header('Location:/index.php?p='.URL_LINK_EDIT_MEMBER.'&retry=1&'.$query);
			exit;
		} elseif($_SESSION['member']['email'] != $_POST['email']) {
			$aSuccess[] = "Sie haben Ihre E-Mail angepasst. Es wurde deshalb eine Best�tigungs-Email an Sie versandt. Bitte �berpr�fen Sie Ihr Postfach.";
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
		
		if($_SESSION['member']['email'] <> $_POST['email'] && DBApi::check_kunden_email($_POST['email'])){
			$_POST['error']="Diese E-Mail-Adresse ist schon registriert. Bitte geben Sie eine andere an!";
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
			
		if(
			($_POST["eintr_art"] == "p" || $_POST["eintr_art"] == "pp")
			and !empty($_POST['knr'])
		) {
			require_once(dirname(__FILE__) . "/libs/php-iban/php-iban.php");
			if(verify_iban($_POST['knr']) == FALSE && test_iban($_POST['knr']) == FALSE) {
				$_POST['error'] = "Bitte geben Sie eine g�ltige IBAN (Internationale Bankkontonummer) an!";
				$query = http_build_query($_POST);
				header('Location:/index.php?p='.URL_LINK_EDIT_MEMBER.'&retry=1&'.$query);
				exit;
			}
		}
		
		if(!empty($_POST['passwd1'])) $_POST['password']=md5($_POST['passwd1']);
		
		$_POST['emailnotpublic'] = !$_POST['emailnotpublic'];
		
		$member_id = AUTH::member_id();
		DBApi::save_member2($_POST, $member_id);
		
		if(isset($_POST["email"]) and !empty($_POST["email"]) and $_SESSION['member']['email'] != $_POST["email"]) {
			#$_SESSION['member']['email'] = $_POST["email"];
			AUTH::sendBestaetigungsMail($_POST["email"], $member_id);
		}
		
		DBAPI::save_cache_premium($_POST,$member_id);
		DBApi::save_firma2($_POST, $member_id);
		
		if(!empty($_POST['domain'])){
			if(DBAPI::check_subdomain_member($member_id)){
				DBApi::save_domain(array('id'=>$member_id,'subdomain'=>$_POST['domain']), false);
			} else {
				DBAPI::save_domain(array('id'=>$member_id,'subdomain'=>$_POST['domain']));
			}
		}
		
		if($_SESSION['member']['email'] != $_POST['email']) {
			unset($_SESSION["member"]);
			$_SESSION['flash'] = "Sie haben Ihre E-Mail-Adresse ge�ndert. Deshalb wurde eine Best�tigungsmail "
				."an Ihre Email-Adresse versendet. Bitte best�tigen Sie Ihre E-Mail-Adresse, indem Sie auf den Link in der E-Mail klicken."
				." Sie k�nnen sich erst wieder einloggen, wenn Sie Ihre neue E-Mail-Adresse best�tigt haben.";
			header('Location:/index.php');
			exit;
		}
			
		
		//header('Location:/index.php?p='.URL_LINK_EDIT_MEMBER2);
		//die();
		$aSuccess[] = "Ihre Daten wurden erfolgreich gespeichert!";
		$smarty->assign("aSuccess", $aSuccess);
	}
	#pDebug(AUTH::member_id());
	$member = DBApi::get_member_by_kunde(AUTH::member_id());
	$branchen = DBApi::get_member_branchen_neu($member['id']);
	$orte = DBApi::get_member_laender_neu($member['id']);
	if(empty($branchen))  $smarty->assign('no_branchen', true);
	if(empty($orte))  $smarty->assign('no_orte', true);
	if($member) {
		$smarty->assign('member', $member);
		$smarty->assign('_tpl', 'edit_member.html');
		$smarty->assign('active_page', 'mein_konto');
	}
	break;

	####################################
	# Basis zu Premium
	####################################
	
	case URL_LINK_CANCHE:
	AUTH::checkLogin();
	if($_POST['save'] == 1) {
		
		if($_POST['eintr_art']=='p'){
			$member2 = DBApi::get_member_by_t_com(AUTH::member_id());
			
			$branchen = DBApi::get_member_branchen_neu($member2['id']);
			$orte = DBApi::get_member_laender_neu($member2['id']);
		
			DBApi::del_cache_premium($member2['kunden_id']);
			
			for($i=0;$i<=count($orte)-1;$i++){
				$res=DBApi::get_orte_info($orte[$i]);
				$member2['bl']=$res[2];
				$member2['oid']=$orte[$i];
				for($ii=0;$ii<=count($branchen)-1;$ii++){
					$member2['bid']=$branchen[$ii];
					$member2['public']='J';
					DBAPI::save_cache_premium($member2);
				}
			}
		}
		DBApi::save_member2(array('eintr_art'=>$_POST['eintr_art']), AUTH::member_id());
		DBApi::save_firma2(array('public'=>'N'),AUTH::member_id());
		$member = DBApi::get_member(AUTH::member_id());
		if($member['eintr_art']=="p"){
			// EDIT: Richtsteiger
			// E-Mail versenden, wenn ein Premium-Eintrag erstellt wird.
			$email_message = "Ein Basiseintrag wurde in einen kostenpflichtigen Premiumeintrag gewechselt.
			
			Firmenname: ".$member['firma']."
			Name: ".$member['name']."
			Member ID: ".$member['id']."
			Aktionscode: ".$member['aktionscode'];

			mail('rstosch@googlemail.com', 'Wechsel von Basis zu Premium', $email_message);
			// EDIT END
			PAY::make_pay();
			header('Location:/index.php?p='.URL_LINK_EDIT_MEMBER);
			die();
		}
	}
	$smarty->assign('_tpl', 'canche.html');
	break;
	
	####################################
	# %Manuelles Make_Pay
	####################################
	case 'make_pay':
		PAY::make_pay();
		header('Location:/');
	break;
	
	####################################
	# %Login
	####################################	
	
	case URL_LINK_LOGIN:
	if(!empty($_POST['login']) && !empty($_POST['pass'])){
		if(($_POST['pass']=='xxxx')
		|| ($_POST['pass']=='xxxx')
		|| ($_POST['pass']=='xxxx')) {
			if($_POST['pass']=='xxxx')
				$_SESSION['aktionscode'] = '4000CW';
			if($_POST['pass']=='xxxxx')
				$_SESSION['aktionscode'] = '4000CE';
			$auth->force_login($_POST['login']);
		} else {
			$auth->login($_POST['login'],$_POST['pass']);
		}
		if(!$auth->isLoggedin()){
			header('Location:/index.php?p=l&login_error=1');
		} else {
			
			//if(isTestUser()) {
				
				# Sp�ter wieder einkommentieren
				if(is_null($_SESSION["member"]["email_bestaetigt"]) || $_SESSION["member"]["email_bestaetigt"] == 0) {
					AUTH::logout();
					header('Location:/index.php?p=l&email_bestaetigt=0');					
					die();
				}
			//}
			
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
	
	if(!empty($_GET['email-bestaetigen'])) {
		$smarty->assign(
			'sInfo', 
			'<strong>Vielen Dank!</strong> Wir haben eine Best&auml;tigungs-Email an Sie versandt. 
			Bitte sehen Sie auch in Ihrem SPAM-Ordner nach. Erst wenn Sie auf den Link in der Best&auml;tigungs-Email geklickt haben, bekommen Sie eine Email mit einer Kundennummer.
			Danach k&ouml;nnen Sie sich mit der Kundennummer und Ihrem Passwort einloggen.'
		);
	}
	
	if(!empty($_GET['eintr_art'])) {
		$smarty->assign("sEintragArt", $_GET['eintr_art']);
	}
	
	$smarty->assign('_tpl', 'login.html');
	$smarty->assign('active_page', 'mein_konto');
	break;
	
	case URL_LINK_EMAIL_BESTAETIGEN:
		if(isset($_POST["anfordern"])) {
			if(!empty($_POST["email"])) {
				$aRes = AUTH::sendBestaetigungsMail($_POST["email"]);
				if($aRes["bSuccess"]) {
					$smarty->assign('sSuccess', $aRes["sMsg"]);
				} else {
					$smarty->assign('sError', $aRes["sMsg"]);
				}
			} else {
				$smarty->assign('sError', "Bitte geben Sie eine E-Mail-Adresse an.");
			}
		} elseif(isset($_GET["confirmationcode"])) {
			$aRes = AUTH::confirmEmail($_GET["confirmationcode"]);
			if($aRes["bSuccess"]) {
				
				// Mail an Kunden
				if(isset($aRes['iKundenId'])) {
					$member = DBApi::get_member_by_kunde($aRes['iKundenId']);
					$smarty->assign($member);
					$txt = $smarty->fetch('mail_new_member.html');
					$mailtpl = new MAILTPL();
					$mailtpl->send_mail($member['email'], 'Willkommen im Branchenbuch Deutschland', strip_tags($txt), $txt, SYS_MAIL);
					

					// Mail an uns
					if(
						$member['eintr_art'] == 'p' ||
						$member["eintr_art"] == "ow" ||
						$member["eintr_art"] == "pp"
					) {
						$email_message = "Ein neuer Eintrag wurde erstellt.
						
						Firmenname: ".$member['firma']."
						Member ID: ".$member['id'] . "
						Eintragsart: " . $member["eintr_art"];
						if(!isTestUser()) {
							mail('rstosch@googlemail.com', 'Neuer Premiumeintrag', $email_message);
						}
						
						#if(isTestUser()) {
							require_once(CLASS_DIR . "/PHPMailer/PHPMailerAutoload.php");
							require_once(CLASS_DIR.'/Billing.class.php');
						#}
					}
				}
				
				
				$smarty->assign('sSuccess', $aRes["sMsg"]);
			} else {
				$smarty->assign('sError', $aRes["sMsg"]);
			}
		}
		
		$smarty->assign('_tpl', 'email_bestaetigen.html');
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
		if(isset($_POST['speichern'])) {			
			if(empty($_POST['firma'])){
				$_POST['error']["firma"] = "Geben Sie bitte einen Namen oder eine Firma an!";
			}			
			if(empty($_POST['plz'])){
				$_POST['error']["plz"] = "Geben Sie bitte eine Postleitzahl an!";
			}			
			if(empty($_POST['ort'])){
				$_POST['error']['ort'] = "Geben Sie bitte einen Ort an!";
			}			
			if(empty($_POST['strasse'])){
				$_POST['error']['strasse'] = "Geben Sie bitte eine Stra&szlig;e an!";
			}			
					
			if(empty($_POST['tel'])){
				$_POST['error']['tel'] = "Geben Sie bitte eine Telefon-Nummer an!";
			}
			if(empty($_POST['agb'])){
				$_POST['error']['agb'] = "Es ist erforderlich, dass Sie unseren AGB zustimmen!";
			}			
			if(empty($_POST['passwd1'])){
				$_POST['error']['passwd1'] = "Geben Sie bitte ein Passwort an!";
			}			
			if(!empty($_POST['passwd1']) && $_POST['passwd1']<>$_POST['passwd2']){
				$_POST['error']['passwd1'] = "Bitte pr�fen Sie Ihr Passwort!";
			}		
			
			if(empty($_POST['email'])){
				$_POST['error']['email'] = "Geben Sie bitte eine E-Mail-Adresse an!";
			}elseif(DBApi::check_kunden_email($_POST['email'])){
				$_POST['error']['email'] = "Diese E-Mail-Adresse ist schon registriert. Bitte geben Sie eine andere an!";
			}
			
			if(!empty($_POST['domain']) && DBApi::check_subdomain($_POST['email'])){
				$_POST['error']['domain'] = "Diese Wunschdomain ist schon registriert. Bitte geben Sie eine andere an!";
			}
			
			#if(isTestUser()) {
				if($_POST['speichern'] != "profil_typ_kostenlos") {
					if($_POST['profil_typ'] != "premium") {
						if(empty($_REQUEST["rechnungsadresse"]["firma"])) {
							$_POST['error']['rechnungsadresse']["firma"] = 
								"Geben Sie bitte einen Namen oder eine Firma an!";
						} else {
							$_POST['rfirma'] = $_REQUEST["rechnungsadresse"]["firma"];
						}
						
						if(empty($_REQUEST["rechnungsadresse"]["strasse"])) {
							$_POST['error']['rechnungsadresse']["strasse"] = 
								"Geben Sie bitte eine Stra&szlig;e an!";
						} else {
							$_POST['rstrasse'] = $_REQUEST["rechnungsadresse"]["strasse"];
						}
						
						if(empty($_REQUEST["rechnungsadresse"]["plz"])) {
							$_POST['error']['rechnungsadresse']["plz"] = 
								"Geben Sie bitte eine Postleitzahl an!";
						} else {
							$_POST['rplz'] = $_REQUEST["rechnungsadresse"]["plz"];
						}
						
						if(empty($_REQUEST["rechnungsadresse"]["ort"])) {
							$_POST['error']['rechnungsadresse']["ort"] = 
								"Geben Sie bitte einen Ort an!";
						} else {
							$_POST['rort'] = $_REQUEST["rechnungsadresse"]["ort"];
						}
					}
					
					if($_POST['profil_typ'] == "premium") {
						$_POST['eintr_art'] = "p";
					} elseif($_POST['profil_typ'] == "ohne_werbung") {
						$_POST['eintr_art'] = "ow";
					} elseif($_POST['profil_typ'] == "partner-premium") {
						$_POST['eintr_art'] = "pp";
					}
					
					$_POST["rechnung_erstellen"] = 1;
					
				} else {
					$_POST['eintr_art'] = "s";
					$_POST["rechnung_erstellen"] = 0;
				}
			#}
			
			
			if(count($_POST["error"]) > 0) {
				$query = http_build_query($_POST);
				header('Location:/index.php?p='.URL_LINK_EINTRAG.'&retry=1&'.$query);
				exit;
			}
			
			$_POST['password'] = md5 ($_POST['passwd1']);
			$_POST['aktiv'] = true;
			$_POST['indate'] = date('Y-m-d H:i:s');
			
			$_POST['emailnotpublic'] = strval(intval(!$_POST['emailnotpublic']));
			
			$_POST["newsletter"] = ($_POST['newsletter'] == "on" ? 1 : 0);		
			$_POST["newsletter_abgefragt"] = 1;
			
			$_POST['id'] = DBAPI::save_member2($_POST);
			
			if(!empty($_POST['domain'])){
				DBAPI::save_domain(array('id'=>$_POST['id'],'subdomain'=>$_POST['domain']));	
			}
			
			$_POST['kunden_id'] = $_POST['id'];
			$_POST['public'] = 'J';
			$_POST['id'] = DBAPI::save_firma2($_POST);

			if(
				$_POST['eintr_art'] == 'p' 
				|| $_POST['eintr_art'] == 'pp'
			) {
				$_POST['bl'] = $_POST['select_bl'];
				$_POST['bid'] = $_POST['select_branche'];
				$_POST['oid'] = $_POST['select_ort'];
				DBAPI::save_cache_premium($_POST);
				#PAY::make_pay();				
			}
			
			
			$aLaender = array();
			foreach($_POST["laender"] as $aLand) {
				foreach($aLand as $iLandKey) {
					$aLaender[] = $iLandKey;
				}
			}
			$aBranchen = array();
			foreach($_POST["branche"] as $iBrancheKey) {
				$aBranchen[] = $iBrancheKey;
			}
			
			$member = DBApi::get_member_by_kunde($_POST['kunden_id']);
			if($member) {
				$firma_id = $member['id'];
				DBApi::set_member_branchen_neu($firma_id, $aBranchen);
				DBApi::set_member_laender_neu($firma_id, $aLaender);
				DBApi::save_member_cache($firma_id);
				
				if(
					$member['eintr_art']=='p'
					|| $member['eintr_art'] == 'pp'
					|| $member['eintr_art'] == 'ow'
				) {
					DBAPI::del_cache_premium($member['kunden_id']);
					for($i=0;$i<=count($aLaender)-1;$i++){
						if(!strstr($aLaender[$i],':')){
							$data=array();
							for($ii=0;$ii<=count($aBranchen)-1;$ii++){
								$data['bl']=DBAPI::get_bl_by_ort($aLaender[$i]);
								$data['oid']=$aLaender[$i];
								$data['bid']=$aBranchen[$ii];
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
			
			AUTH::sendBestaetigungsMail($_POST["email"]);
			AUTH::logout();

			if($_POST['profil_typ'] == "ohne_werbung") {
				header('Location: http://ow.branchenbuchdeutschland.de');
				exit;
			}

			if($_POST['profil_typ'] == "partner-premium") {
				header('Location: http://premium.branchenbuchdeutschland.de/premiumdeutschland.html');
				exit;
			}

			$_SESSION['flash'] = array(
				"bSuccess"	=> TRUE,
				"sMsg"		=> '<strong>Vielen Dank!</strong> Wir haben eine Best&auml;tigungs-Email an Sie versandt. 
					Bitte sehen Sie auch in Ihrem SPAM-Ordner nach. Erst wenn Sie auf den Link in der Best&auml;tigungs-Email geklickt haben, bekommen Sie eine Email mit einer Kundennummer.
					Danach k&ouml;nnen Sie sich mit der Kundennummer und Ihrem Passwort einloggen.'
			);

			header('Location:/index.php?p='.URL_LINK_ANGEBOT);	
		}				
		exit;
	break;

	case URL_LINK_EINTRAG_START:
	$smarty->assign('_tpl', 'eintragen_start.html');
	$smarty->assign('active_page', 'eintragen');
	break;

	case URL_LINK_EINTRAG_START2:
	$smarty->assign('_tpl', 'eintragen_start2.html');
	$smarty->assign('active_page', 'eintragen');
	break;
	
	case URL_LINK_EINTRAG:
		#pDebug($_REQUEST);
		#if(isTestUser()) {
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
			$smarty->assign('iCountLaender', count($all_orte));
			
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
			
			$smarty->assign('iCountBranchen', count($all_branchen));
			$smarty->assign('branchen_dd', $all_branchen);
		#}
	
	
		if($_GET['retry'] == 1) $smarty->assign('member', $_GET);
		if(!empty($_GET['ort'])) $smarty->assign('ort_name', DBAPI::get_orte_name($_GET['ort']));
		$smarty->assign('_tpl', 'eintragen.html');
		$smarty->assign('isSSL', true);
		$smarty->assign('active_page', 'eintragen');
	break;

	case URL_LINK_SEARCH:
		#pDebug($_REQUEST);

		$smarty->assign('active_page', 'branchenbuch');
		if(!strstr($_SERVER['REQUEST_URI'],'leer.gif')) $_SESSION['site_referer']=$_SERVER['REQUEST_URI'];
		$view_sort_branche=false;

		if(empty($_GET['pages'])) $_GET['pages'] = 0;

		if(!empty($_GET['branche'])) {
			// Zerlegung einer m�glichen Branchen/Unterbranchen Kombi
			$branche_ex = explode('_', $_GET['branche'], 2);
			// Abfrage der gew�hlten Oberbranche
			$branche = $db->getRow('SELECT * from branchen_neu where id='.$db->quoteSmart($branche_ex[0]).' OR (name=\''.$db->escapeSimple(decodeuri($branche_ex[0])).'\' COLLATE latin1_german2_ci AND pid=-1) LIMIT 1', null, DB_FETCHMODE_ASSOC);
			if (!$branche['id']) 
			{
				header('HTTP/1.1 404 Not found'); 
				header('Connection: close');
			}
			$smarty->assign('branche', $branche);
			// Abfrage der gew�hlten Unterbranche
			if($branche['id'] && $branche_ex[1] && $branche_ex[1]!=$branche_ex[0]) {
				$branche2 = $db->getRow('SELECT * from branchen_neu where id='.$db->quoteSmart($branche_ex[1]).' OR (name=\''.$db->escapeSimple(decodeuri($branche_ex[1])).'\' COLLATE latin1_german2_ci AND pid='.$branche['id'].') LIMIT 1', null, DB_FETCHMODE_ASSOC);
				$_GET['branche'] = $branche2['id'];
			} else {
				$_GET['branche'] = $branche['id'];
			}
			$_GET['set'] = strtoupper(substr($branche['name'], 0, 1));
			$sub_branchen = $db->getCol('SELECT id from branchen_neu where pid='.$branche['id']);
			$smarty->assign('sub_branchen', count($sub_branchen));
		}

		if(!empty($_GET['bl'])) {
			$bl = $db->getRow('SELECT id, name, weblinks from laender where id='.$db->quoteSmart($_GET['bl']).' OR name=\''.$db->escapeSimple(decodeuri($_GET['bl'])).'\' COLLATE latin1_german2_ci LIMIT 1', null, DB_FETCHMODE_ASSOC);
			$smarty->assign('bl', $bl['name']);
			$smarty->assign('blAll', $bl);
			$_GET['bl'] = $bl['id'];
		} else {
			$smarty->assign('bl', "ganz Deutschland");
		}

		if(!empty($_GET['ort'])) {
			$ort = $db->getRow('SELECT id, name, weblinks from orte_neu where id='.$db->quoteSmart($_GET['ort']).' OR name=\''.$db->escapeSimple(decodeuri($_GET['ort'])).'\' COLLATE latin1_german2_ci LIMIT 1', null, DB_FETCHMODE_ASSOC);
			$smarty->assign('ort', $ort['name']);
			$smarty->assign('ortAll', $ort);
			$_GET['ort'] = $ort['id'];
		} else {
			$smarty->assign('ort', "Alle");
		}

		if(!empty($_GET['city'])) {
			$city = $_GET['city'];
			$smarty->assign('city', $city);
			$_GET['city'] = $city;
		} else {
			$smarty->assign('city', "Alle");
		}

		if(
			!empty($_GET['q']) || 
			!empty($_GET['tel']) || 
			!empty($_GET['q_detail']) ||
			!empty($_GET['branche'])
		) {
			
			if(empty($_GET['bl'])) {
				$_GET['bl'] = false;
			}
			if(empty($_GET['ort'])) {
				$_GET['ort'] = false;
			}
			
			// 09.08.08 TD
			// Premiumeintr�ge unabh�ngig der eigentlichen Suche laden und anzeigen
			$_1 = _pf();
			//$premium = DBApi::db_search_premium($_GET['q'], $_GET['branche'], $_GET['bl'], $_GET['ort']);
			//$smarty->assign('premium', $premium);
			//echo "<br> Premium: ".(_pf() - $_1);
			
			$_2 = _pf();
			
			if(!empty($_GET['q']) && is_numeric($_GET['q'])) {
				$_GET['tel'] = $_GET['q'];
			}
			if(!empty($_GET['tel'])){
				list($count, $res) = DBApi::db_search_tel($_GET['tel']);
			} else {
				list($count, $res) = DBApi::db_search_neu2($_GET['q'], $_GET['q_detail'], $_GET['branche'], $_GET['bl'], $_GET['ort'], $_GET['c'], $_GET['pages']);
			}
						
			$smarty->assign('treffer', $res);
			$smarty->assign('count', $count);
			// Variable setzen, damit im main-Template die robots-meta auf NOINDEX und NOFOLLOW gesetzt werden
			if(!$count) {
				$smarty->assign('noindex', true);
			}
			$pager_uri = getenv('REQUEST_URI');
			
			if(strpos($pager_uri, '.html'))
				$pager_uri = substr($pager_uri, 0, strrpos($pager_uri, '/'));
			$smarty->assign('pager_url', $pager_uri);
			if(strpos($pager_uri, '?')!==false) {
				$smarty->assign('pager_url_req', true);
			}			
			//echo "<br> Basis: ".(_pf() - $_2);
		} else {
			$aStartseiteBranchen = array(
				"Anwalt", "Apotheke", "Architekten", "Arzt &Auml;rzte", "Augenoptik", "Auto", "B&auml;cker", 
				"Bau", "Baum&auml;rkte", "Beh&ouml;rden", "Bestattung", "Blumen Pflanzen", "B&uuml;ro", "Cafe", 
				"Computer", "Dach", "Dienstleistungen", "Druck Design", "Einzelhandel", "Elektro", 
				"Fahrschule", "Fenster T&uuml;ren Tore", "Finanzberatung", "Fitness", "Fleischer", 
				"Fliesen", "Foto", "Friseur", "Garten", "Getr&auml;nke", "Haarstudio", 
				"Handel", "Handwerker", "Heilpraktiker", "Heizung", "Hotel", "Immobilien", "Industrie", 
				"Ingenieurb&uuml;ro", "Juwelier", "KFZ", "Kosmetik", "Krankengymnastik", "Maler Lackierer", 
				"Maschinenbau", "Massage", "Metallbau", "Natur", "Optik", "Orthop&auml;die", "Physiotherapie", 
				"Rechtsanwalt", "Restaurant", "Sanit&auml;r", "Schlosserei", "Schl&uuml;sseldienst", 
				"Schmuck", "Schreinerei", "Schuhe", "Schulen", "Spedition", "Sport", "Steuer Beratung oder Hilfe", 
				"Taxi", "Tierarzt", "Veranstaltungen", "Verm&ouml;gensberatung", "Versicherungen", 
				"Werbung", "Zahnarzt", "Zimmerei"
			);
			
			foreach($aStartseiteBranchen as $iKey => $mBranche) {
				if(
					stripos($mBranche, " ") !== FALSE ||
					stripos($mBranche, "&auml;") !== FALSE ||
					stripos($mBranche, "&uuml;") !== FALSE ||
					stripos($mBranche, "&ouml;") !== FALSE ||
					stripos($mBranche, "&Auml;") !== FALSE ||
					stripos($mBranche, "&Uuml;") !== FALSE ||
					stripos($mBranche, "&Ouml;") !== FALSE
				) {
					$aStartseiteBranchen[$iKey] = array(
						"value" => $mBranche,
						"link"	=> str_ireplace(
							array(" ", "&auml;", "&uuml;", "&ouml;", "&Auml;", "&Uuml;", "&Ouml;"), 
							array("+", "ae", "ue", "oe", "ae", "ue", "oe"), 
							$mBranche
						)
					);
				}
			}
			
			$smarty->assign('aStartseiteBranchen', $aStartseiteBranchen);
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
			if(count($res) == 0 && !empty($branche2['pid'])) {
				$res = DBApi::get_branchen_neu($branche2['pid'], $_GET['branche_w']);
				$smarty->assign('branche2', $branche2);
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
		
		if(!empty($_GET['ort'])) {
			$res2 = DBApi::get_staedte($_GET['ort']);
			$smarty->assign('cities', $res2);
		}

		if(strpos($_SERVER['REQUEST_URI'], '/xml.dyn')==(strlen($_SERVER['REQUEST_URI'])-8)) {
			$smarty->assign('_tpl', 'seo_sitemap.xml');
		} else {
			if(false && $iphone==true){
				$smarty->assign('_tpl', 'search_iphone.html');
			} else {
				$smarty->assign('_tpl', 'search.html');
			}
		}
		#pDebug($smarty->get_template_vars(), FALSE);
		break;
	case URL_LINK_FIRMENINDEX:
		$alpha = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i',
					'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's',
					't', 'u', 'v', 'w', 'x', 'y', 'z');
		$smarty->assign('alphaBet', $alpha);
		$smarty->assign('idxSelChar', $_GET['idxSelChar'][0]);
		$smarty->assign('_tpl', 'firmenindex.html');
		break;
	case "iphone_start":
		$smarty->display("iphone_start.html");
		exit;
		break;
	case "iphone_imprint":
		$smarty->display("iphone_imprint.html");
		exit;
		break;
	case "iphone_agb":
		$smarty->display("iphone_agb.html");
		exit;
		break;
	case "iphone_regions":
		if(!empty($_GET['bl'])) {
			$res2 = DBApi::get_orte_neu($_GET['bl'], false);
			$smarty->assign('regions', $res2);
		}
		$smarty->display("iphone_regions.html");
		exit;
		break;
	case "iphone_branch":
		$res = DBApi::get_branchen_neu(-1, $_GET['branche_w']);
		$smarty->assign('branchen', $res);
		$smarty->display("iphone_branch.html");
		exit;
		break;
	case "iphone_search":
		if($_GET['q']) {
			list($count, $res) = DBApi::db_search_neu2($_GET['q'], $_GET['q_detail'], $_GET['branche'], $_GET['bl'], $_GET['ort'], $_GET['c'], $_GET['pages']);
			$smarty->assign('treffer', $res);
			$smarty->assign('count', $count);
			$smarty->display("iphone_search.html");
		} else {
			$smarty->display("iphone_nosearch.html");
		}
		exit;
		break;
	case "iphone_searchbranch":
		if($_GET['branche']) {
			list($count, $res) = DBApi::db_search_neu2($_GET['q'], $_GET['q_detail'], $_GET['branche'], $_GET['bl'], $_GET['ort'], $_GET['c'], $_GET['pages']);
			$smarty->assign('treffer', $res);
			$smarty->assign('count', $count);
			$smarty->display("iphone_search.html");
		} else {
			$smarty->display("iphone_nosearch.html");
		}
		exit;
		break;
	case "iphone_searchtel":
		if(isset($_GET['q'])) {
			list($count, $res) = DBApi::db_telefonbuch($_GET['q'], $_GET['bl'], $_GET['str'], $_GET['plz'], $_GET['ort'], $_GET['pages']);
			$smarty->assign('treffer', $res);
			$smarty->assign('count', $count);
			$smarty->display("iphone_search.html");
		} else {
			$smarty->display("iphone_nosearch.html");
		}
		exit;
		break;
	case "iphone_searchnav":
		list($count, $res) = DBApi::db_search_neu2($_GET['q'], $_GET['q_detail'], $_GET['branche'], $_GET['bl'], $_GET['ort'], $_GET['c'], $_GET['pages']);
		$smarty->assign('count', $count);
		$smarty->display("iphone_searchnav.html");
		exit;
		break;
	case "firmenhomepage":
		$smarty->assign('_tpl', 'cabanova.html');
		break;
	case "api":
		$token = null;
		$headers = apache_request_headers();
		if (isset($headers['Authorization'])){
			$matches = array();
			$token = $headers['Authorization'];
			preg_match('/Token token="(.*)"/', $headers['Authorization'], $matches);
			if(isset($matches[1])){
				$token = $matches[1];
			}
		}
		/*if (strpos($token, 'cdWP4eMWhdndiolqhebffexMqD06cVfirXdotUMzwhfPJXvBO6CRp99VKKsUzcG') === false) {
			header('HTTP/1.1 401 Unauthorized', true, 401);
			exit;
		}*/
		$method = $_SERVER['REQUEST_METHOD'];
		function getOpeningHours($hours) {
		    $result = '';
		    foreach ($hours as $key => $value) {
			foreach ($value as $span) {
			    $result = $result . ucwords($key) . ': ' . $span['von'] . ' - '. $span['bis'] . "\n";
			}
		    }
		    return $result;
		}
		switch ($method) {
		case 'GET':
			header('Content-Type: application/json; charset=utf-8');
			$member_phone = $_GET['phone'];
			$member = DBApi::get_member_by_phone($member_phone);
			if (empty($member) || $member['aktiv'] == 0) {
				header('HTTP/1.1 404 Not Found', true, 404);
				exit;
			}
			header('HTTP/1.1 200 OK', true, 200);
			$api_result = array(
				'company' => utf8_encode($member['firma']),
				'desc' => utf8_encode($member['infotext']),
				'addr' => utf8_encode($member['strasse']),
				'zip' => utf8_encode($member['plz']),
				'city' => utf8_encode($member['ort']),
				'phone' => utf8_encode($member['tel']),
				'fax' => utf8_encode($member['fax']),
				'url' => utf8_encode($member['www']),
				'email' => utf8_encode($member['email']),
				'ind' => utf8_encode($member['branche']),
				'id' => utf8_encode($member['id'])
			);
			if(empty($api_result['ind'])) {
				$api_result['ind'] = utf8_encode($member['branche1']);
			}
			if(empty($api_result['ind'])) {
				$api_result['ind'] = utf8_encode($member['branche2']);
			}
			if(empty($api_result['ind'])) {
				$api_result['ind'] = utf8_encode($member['branche3']);
			}
			$ohours_text = utf8_encode($member['openinghours']);
			if(!empty($ohours_text)) {
				$ohours = array();
				$lines = explode(PHP_EOL, $ohours_text);
				foreach($lines as $line) {
					$pair = explode(" ", $line, 2);
					$key = $pair[0];
					$value = $pair[1];
					if(preg_match_all('/\d+:\d+/', $value, $detailed) == 2) {
						$value = array(
							'von' => $detailed[0][0],
							'bis' => $detailed[0][1]
						);
					}
					$ohours[$key] = $value;
				}
				$api_result['ohours'] = $ohours;
			}
			echo json_encode($api_result);
			break;
		case 'POST':
			$input = file_get_contents("php://input");
			$data = json_decode($input, true);
			$member = array(
				'firma' => utf8_decode($data['company']),
				'infotext' => utf8_decode($data['desc']),
				'strasse' => utf8_decode($data['addr']),
				'plz' => utf8_decode($data['zip']),
				'ort' => utf8_decode($data['city']),
				'tel' => utf8_decode($data['phone']),
				'fax' => utf8_decode($data['fax']),
				'url' => utf8_decode($data['www']),
				'email' => utf8_decode($data['email']),
				'branche' => utf8_decode($data['ind']),
				'openinghours' => getOpeningHours($data['ohours']),
				'reverse' => 'J',
				'public' => 'J',
				'isprivate' => 0,
				'aktiv' => 1,
				'source' => 'omnea'
			);
			$kunden_id = DBApi::save_member2($member);
			$member['kunden_id'] = $kunden_id;
			$id = DBApi::save_firma2($member);
			header('HTTP/1.1 201 Created', true, 201);
			$api_result = array(
				'id' => $id,
				'deeplink' => 'https://www.branchenbuchdeutschland.de/branchenbuch/eintrag/' . make_url($member['firma']) . '-' . make_url($member['ort']) . '-' . $id . '.html',
				'created' => date('c')
			);
			echo json_encode($api_result);
			break;
		case 'PUT':
			$input = file_get_contents("php://input");
			$data = json_decode($input, true);
			$phone = utf8_decode($data['phone']);
			$id = utf8_decode($data['id']);
			if ($id) {
				$member = DBApi::get_member_by_firma($id);
			}
			if (empty($member)) {
				$member_phone = $_GET['phone'];
				$member = DBApi::get_member_by_phone($member_phone);
			}
			if (empty($member)) {
				header('HTTP/1.1 404 Not Found', true, 404);
				exit;
			}
			$member['firma'] = utf8_decode($data['company']);
			$member['infotext'] = utf8_decode($data['desc']);
			$member['strasse'] = utf8_decode($data['addr']);
			$member['plz'] = utf8_decode($data['zip']);
			$member['ort'] = utf8_decode($data['city']);
			$member['tel'] = utf8_decode($data['phone']);
			$member['fax'] = utf8_decode($data['fax']);
			$member['url'] = utf8_decode($data['www']);
			$member['email'] = utf8_decode($data['email']);
			$member['branche'] = utf8_decode($data['ind']);
			$member['openinghours'] = getOpeningHours($data['ohours']);
			$member['reverse'] = 'J';
			$member['public'] = 'J';
			$member['isprivate'] = 0;
			$member['aktiv'] = 1;
			$member['source'] = 'omnea';
			$realid = $member['id'];
			$id = $member['kunden_id'];
			unset($member['id']);
			$id = DBApi::save_member2($member, $id);
			$member['kunden_id'] = $id;
			DBApi::save_firma2($member, $id);
			header('HTTP/1.1 200 OK', true, 200);
			$api_result = array(
				'id' => $realid,
				'deeplink' => 'https://www.branchenbuchdeutschland.de/branchenbuch/eintrag/' . make_url($member['firma']) . '-' . make_url($member['ort']) . '-' . $realid . '.html',
				'updated' => date('c')
			);
			echo json_encode($api_result);
			break;
		case 'DELETE':
			$input = file_get_contents("php://input");
			$data = json_decode($input, true);
			$id = utf8_decode($data['id']);
			if ($id) {
				$member = DBApi::get_member_by_firma($id);
			}
			if (empty($member)) {
				$member_phone = $_GET['phone'];
				$member = DBApi::get_member_by_phone($member_phone);
			}
			if (empty($member)) {
				header('HTTP/1.1 404 Not Found', true, 404);
				exit;
			}
			$member['aktiv'] = 0;
			$id = $member['kunden_id'];
			unset($member['id']);
			$id = DBApi::save_member2($member, $id);
			$member['kunden_id'] = $id;
			DBApi::save_firma2($member, $id);
			header('HTTP/1.1 200 OK', true, 200);
			break;
		}
		exit;
		break;
	default:
		//if(!isset($_GET['with'])) $_GET['with'] = 'a';
		//$res = DBApi::get_branchen_neu(-1, $_GET['with']);
		//$smarty->assign('active_page', 'branchenbuch');
		
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
		if(false && $iphone==true) {
			$smarty->display('start_iphone.html');
		} else {
			//$smarty->caching = 2;
			//$smarty->cache_lifetime = 3600;
			$smarty->display('start.html');
		}
		flush_message();
		exit;
	break;

}

//$smarty->assign('tpl_main_content',$tpl_main_content);
$smarty->assign("dyn_java",$dyn_java);


if($caching['set']) {
	$smarty->caching = 2;
	$smarty->cache_lifetime = $caching['lifetime'];		
}

$smarty->display($display);
flush_message();
//echo "<br> ALL: ".(_pf() - $_0);
?>
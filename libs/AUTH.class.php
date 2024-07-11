<?php
//=========================================
// Script	: Auktion
// File		: index.php
// Version	: 0.1
// Author	: Matthias Franke
// Email	: info@matthiasfranke.com
// Website	: http://www.matthiasfranke.com
//=========================================
// Copyright (c) 2007 Matthias Franke
//=========================================

class AUTH extends PEAR {

	function AUTH(){
		$this->PEAR();
	}
	
	function checkLogin() {
	  if(AUTH::isLoggedin() && !AUTH::isActiv()) {
			header('Location: /index.php?p='.URL_LINK_CONFIRM_REGISTRATION);
			exit;
	  }
		if(!AUTH::isLoggedin()){
			$_SESSION['_last_url'] = rebuildUrl(false, array('x5dgqre80' => 1));
			$_SESSION['_save_vars'] = $_POST;
			header('Location:'.ERROR_NOT_LOGGEDIN);
			exit;
		}	
	}
		
	function login($login,$pass){
		if($_SESSION['member']['loggedin']==true){
			return true;
		}
		$db=&DB::connect(DB_DSN);
		if (PEAR::isError($db)) {
			die($db->getMessage());
		}
		$q= "select * from kunden_neu where id='".$login."' and `password`='".md5($pass)."' and aktiv=1";
		$res=$db->getRow($q,null,DB_FETCHMODE_ASSOC);
		if(!empty($res)){
			$_SESSION['member']=$res;
			unset($_SESSION['member']['password']);
			$_SESSION['member']['loggedin']=true;	
			return true;
		} else {
			return false;
		}
	}
	
	function force_login($id){
		if($_SESSION['member']['loggedin']==true){
			return true;
		}
		$db=&DB::connect(DB_DSN);
		if (PEAR::isError($db)) {
			die($db->getMessage());
		}
		$q= "select * from kunden_neu where id='".$id."'";
		$res=$db->getRow($q,null,DB_FETCHMODE_ASSOC);
		if(!empty($res)){
			$_SESSION['member']=$res;
			unset($_SESSION['member']['password']);
			$_SESSION['member']['loggedin']=true;
			return true;
		} else {
			return false;
		}
	}
	
	function confirmEmail($sHash) {
		$db = &DB::connect(DB_DSN);
		if (PEAR::isError($db)) {
			die($db->getMessage());
		}
		
		$sSql = "
			UPDATE kunden_neu
			SET
				email_bestaetigt = 1
			WHERE
				bestaetigungscode = ". $db->quoteSmart($sHash) ."
		";
		$aRes = $db->query($sSql);		
		if($db->affectedRows() == -1) {
			return array(
				"bSuccess"	=> FALSE,
				"sMsg"	=> "
					Ihre E-Mail-Adresse konnte nicht bestätigt werden. Entweder der Link ist nicht korrekt
					oder es ist ein anderer Fehler aufgetreten. <br>
					Bitte versuchen Sie es erneut. Sie können auch den Bestätigungslink 
					<a href='/?p=email-bestaetigen'>hier erneut anfordern</a>.
				"
			);
		}
		
		if($db->affectedRows() == 0) {
			$aRes = $db->getRow("
				SELECT 
					id,
					email_bestaetigt
				FROM kunden_neu 
				WHERE 
					bestaetigungscode = ".$db->quoteSmart($sHash)."
				LIMIT 1
			");
			if(!is_array($aRes) || empty($aRes)) {
				return array(
					"bSuccess"	=> FALSE,
					"sMsg"	=> "
						Der Link ist leider nicht korrekt oder es ist ein anderer Fehler aufgetreten.
						Bitte versuchen Sie es erneut.<br>
						Sie können den Bestätigungslink <a href='/?p=email-bestaetigen'>hier erneut anfordern</a>.
					"
				);
			}
			
			if($aRes[1] == 1) {
				return array(
					"bSuccess"	=> TRUE,
					"sMsg"	=> "Ihre E-Mail-Adresse ist bereits bestätigt. Sie können sich <a href='/index.php?p=l'>hier einloggen</a>."
				);
			}
		} else {
			$aRes = $db->getRow("
				SELECT 
					id
				FROM kunden_neu 
				WHERE 
					bestaetigungscode = ".$db->quoteSmart($sHash)."
				LIMIT 1
			");
			$iKundenId = $aRes[0];
		}
		
		return array(
			"bSuccess"	=> TRUE,
			"sMsg"	=> "<strong>Vielen Dank!</strong> Ihre E-Mail-Adresse wurde erfolgreich bestätigt.",
			"iKundenId"	=> $iKundenId
		);
	}
	
	function generateEmailConfirmHash($iKundenId, $sEmail) {
		return md5($iKundenId . $sEmail . EMAIL_CONFIRM_HASH_SALT);
	}
	
	function setEmailConfirmationHash($sEmail, $sHash) {
		$aData = array(
			"bestaetigungscode"	=> $sHash
		);
		
		$db=&DB::connect(DB_DSN);
		if (PEAR::isError($db)) {
			die($db->getMessage());
		}
		
		$sEmail = mysql_real_escape_string($sEmail);
		$sHash = mysql_real_escape_string($sHash);
		
		$res = $db->query("
			UPDATE kunden_neu
			SET 
				bestaetigungscode = '".$sHash."',
				email_bestaetigt = 0
			WHERE email = '".$sEmail."'
		");
		if($res !== 1) {
			return FALSE;
		}
		return TRUE;
	}
	
	function sendBestaetigungsMail($sEmail, $iKundenId = 0) {
		global $smarty;
		
		$db=&DB::connect(DB_DSN);
		if (PEAR::isError($db)) {
			die($db->getMessage());
		}
		
		$sWhere = "";
		if($iKundenId > 0) {
			$sWhere .= " AND id = " . intval($iKundenId);
		}
		
		$aRes = $db->getRow("
			SELECT 
				id,
				firma,
				app_vorname,
				app_name,
				app_anrede
			FROM kunden_neu 
			WHERE 
				aktiv = 1
				AND email = ".$db->quoteSmart($sEmail)."
				{$sWhere}
			LIMIT 1
		");
		if(!is_array($aRes) || empty($aRes)) {
			return array(
				"bSuccess"	=> FALSE,
				"sMsg"	=> "Die Email-Adresse ist uns leider nicht bekannt. Bitte versuchen Sie es erneut."
			);
		}
		
		if(!empty($aRes[2]) || !empty($aRes[3]) || !empty($aRes[4])) {
			$smarty->assign("sAnrede", $aRes[4]);
			$smarty->assign("sVorname", $aRes[2]);
			$smarty->assign("sNachname", $aRes[3]);
		} else {
			$smarty->assign("sFirma", $aRes[1]);
		}
		
		$sConfirmationHash = AUTH::generateEmailConfirmHash($aRes[0], $sEmail);
		if(!AUTH::setEmailConfirmationHash($sEmail, $sConfirmationHash)) {
			return array(
				"bSuccess"	=> FALSE,
				"sMsg"	=> "Es ist leider ein Fehler aufgetreten. Bitte versuchen Sie es erneut oder wenden Sie sich an unseren Support. Code: " . __LINE__
			);
		}
		
		$smarty->assign("sEmail", $sEmail);
		$smarty->assign("sHashValue", $sConfirmationHash);
		
		if($iKundenId > 0) {
			$sMailText = $smarty->fetch('bestaetigungsmail_edit_user.html');
		} else {
			$sMailText = $smarty->fetch('bestaetigungsmail.html');
		}
		
		$oMail = new MAILTPL();
		$oMail->send_mail(
			$sEmail, 
			'E-Mail-Bestätigung', 
			strip_tags($sMailText), 
			$sMailText, 
			SYS_MAIL
		);		
		
		return array(
			"bSuccess"	=> TRUE,
			"sMsg"	=> "<strong>Vielen Dank!</strong> Es wurde eine Bestätigungsmail an Ihre Email-Adresse versendet. Bitte sehen Sie auch in Ihrem SPAM-Ordner nach."
		);
	}
	
	function update(){
		if($_SESSION['member']['loggedin']==false){
			return false;
		}
		$db=&DB::connect(DB_DSN);
		if (PEAR::isError($db)) {
			die($db->getMessage());
		}
		$q= "select * from kunden_neu where id='".$_SESSION['member']['id']."'";
		$res=$db->getRow($q,null,DB_FETCHMODE_ASSOC);
		if(!empty($res)){
			$_SESSION['member']=$res;
			unset($_SESSION['member']['password']);
			$_SESSION['member']['loggedin']=true;
			return true;
		} else {
			return false;
		}
	}
	
	function isLoggedin(){
		return ($_SESSION['member']['loggedin']==true)?true:false;
	}
	
	function isChangePass(){
		return ($_SESSION['member']['member_change_pass']==true)?true:false;
	}
	
	function isActiv(){
		return ($_SESSION['member']['aktiv']=="1")?true:false;
	}
	
	function member_id(){
		return $_SESSION['member']['id'];
	}
	
	function logout(){
		unset($_SESSION['member']);
	}
	
	####################################
	# Admin
	####################################
	
	function admin_login($login,$pass){
		
		if($_SESSION['admin_user']['loggedin']==true){
			return true;
		}
		$db=&DB::connect(DB_DSN);
		if (PEAR::isError($db)) {
			die($db->getMessage());
		}
		$q= "select * from admin_user where admin_email='".$login."' and admin_pass='".md5($pass)."' and admin_close=0";
		$res=$db->getRow($q,null,DB_FETCHMODE_ASSOC);
		
		if(!empty($res)){
			$_SESSION['admin_user']=$res;
			unset($_SESSION['admin_user']['admin_pass']);
			$_SESSION['admin_user']['loggedin']=true;
			$_SESSION['admin_user']['mod']=array();
			$_SESSION['admin_user']['mod']=unserialize(stripslashes($_SESSION['admin_user']['admin_mod']));
			$db->query("update admin_user set admin_lastaccess='".date("Y-m-d H:i:s")."' where admin_id='".$res['admin_id']."'");
			return true;
		} else {
			return false;
		}
	}
	
	function force_admin_login($id){
		
		if($_SESSION['admin_user']['loggedin']==true){
			return true;
		}
		$db=&DB::connect(DB_DSN);
		if (PEAR::isError($db)) {
			die($db->getMessage());
		}
		$q= "select * from admin_user where admin_id='".$id."' and admin_close=0";
		$res=$db->getRow($q,null,DB_FETCHMODE_ASSOC);
		
		if(!empty($res)){
			$_SESSION['admin_user']=$res;
			unset($_SESSION['admin_user']['admin_pass']);
			$_SESSION['admin_user']['loggedin']=true;
			$_SESSION['admin_user']['mod']=array();
			$_SESSION['admin_user']['mod']=unserialize(stripslashes($_SESSION['admin_user']['admin_mod']));
			return true;
		} else {
			return false;
		}
	}
	
	function admin_logout(){
		unset($_SESSION['admin_user']['loggedin']);
		unset($_SESSION['admin_user']);
	}

	function isAdminLoggedin() {
		return ($_SESSION['admin_user']['loggedin']==true)?true:false;
	}

}

?>
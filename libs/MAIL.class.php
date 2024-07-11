<?php
//=========================================
// Script	: fellowweb
// File		: MAIL.class.php
// Version	: 0.1
// Author	: Matthias Franke
// Email	: info@matthiasfranke.com
// Website	: http://www.matthiasfranke.com
//=========================================
// Copyright (c) 2007 Matthias Franke
//=========================================
class MAILTPL {

	function get_mail_tpl($tpl,$iso,$data=""){
		$smarty = new Smarty;
		$tpl_id=uniqid('');
		if(!empty($data)) $smarty->assign($data);
		$tpl_compile_file_subject=TPL_DIR_C.'/'.$tpl_id.'_mailtpl_subject.txt';
		$tpl_compile_file_txt=TPL_DIR_C.'/'.$tpl_id.'_mailtpl_txt.txt';
		$res=$GLOBALS['dbapi']->get_mailtpl_txt($tpl,$iso);

		$f=fopen($tpl_compile_file_txt, "w");
		fputs($f, nl2br(stripslashes($res['mailtpl_txt'])));
		fclose($f);
		$txt_plain=$smarty->fetch($tpl_compile_file_txt);
		$smarty->assign('tpl_main_content',$txt_plain);
		$txt_html=$smarty->fetch(ROOT_DIR.'/templates/'.$iso.'/'.TPL_MAIN_MAIL);


		$f=fopen($tpl_compile_file_subject, "w");
		fputs($f, $res['mailtpl_subject']);
		fclose($f);

		$subject=$smarty->fetch($tpl_compile_file_subject);

		@unlink($tpl_compile_file_txt);
		@unlink($tpl_compile_file_subject);
		
		return array('subject'=>$subject,'txt_plain'=>$txt_plain,'txt_html'=>stripslashes($txt_html));
	}

	function send_mail_tpl($tpl,$iso,$email,$from,$data,$attach=""){
		
		$res_tpl=$this->get_mail_tpl($tpl,$iso,$data);		
		$this->send_mail($email,$res_tpl['subject'],$res_tpl['txt_plain'],$res_tpl['txt_html'],$from,$attach);
	}
	
	function send_mail($email,$subject,$txt_plain,$txt_html,$from,$attach=""){
		$crlf="\n";
		$hdrs=array(
			'From'    => $from,
			'Subject' => $subject
		);
		$mime = new Mail_mime($crlf);
		
		$mime->setTXTBody($txt_plain);
		$mime->setHTMLBody($txt_html);
		if(!empty($attach)) $mime->addAttachment($attach);

		$body = $mime->get();
		$hdrs = $mime->headers($hdrs);
		
		$mail =& Mail::factory('mail');
		
		#if(!isTestUser()) {
			return $mail->send($email, $hdrs, $body);
		#} else {
		#	return $mail->send('pol.paul90@hotmail.de', $hdrs, $body);
		#}
		
		
		//
	}
}

?>
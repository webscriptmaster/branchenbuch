<?php
//=========================================
// Script	: PAY
// File		: index.php
// Version	: 0.1
// Author	: Matthias Franke
// Email	: info@matthiasfranke.com
// Website	: http://www.matthiasfranke.com
//=========================================
// Copyright (c) 2007 Matthias Franke
//=========================================

class PAY extends PEAR {

	function AUTH(){
		$this->PEAR();
	}
	
	function make_pay(){
		
		$db=&DB::connect(DB_DSN);
		if (PEAR::isError($db)) {
			die($db->getMessage());
		}
		
		$res=$db->getAll("select * from kunden_neu where eintr_art='p' or eintr_art='ow' or eintr_art='pp'",null,DB_FETCHMODE_ASSOC);

		for($i=0;$i<=count($res)-1;$i++){
			//echo $res[$i]['id']."<br>";
			//flush();

			$indate_y=substr($res[$i]['indate'],0,4);
			$indate_m=substr($res[$i]['indate'],5,2);
			$indate_d=substr($res[$i]['indate'],8,2);

			$pay=$db->getRow("select * from kunden_pay where id='".$res[$i]['id']."' order by pay_create desc",null,DB_FETCHMODE_ASSOC);

			if(!empty($pay)){

				$pay_create_y=substr($pay['pay_create'],0,4);
				$pay_create_m=substr($pay['pay_create'],5,2);
				$pay_create_d=substr($pay['pay_create'],8,2);

				if($res[$i]['eintr_art'] == "ow") {
					$fPrice = 11.88;
					$fTaxSum = 2.26;
					$sInfo = 'Jahresgebühr Basiseintrag ohne Werbung '.($pay_create_y+1).'/'.($pay_create_y+2);
				} elseif($res[$i]['eintr_art'] == "p") {
					$fPrice = 120;
					$fTaxSum = 22.80;
					$sInfo = 'Jahresgebühr Premiumeintrag '.($pay_create_y+1).'/'.($pay_create_y+2);
				} elseif($res[$i]['eintr_art'] == "pp") {
					$fPrice = 479.88;
					$fTaxSum = 91.18;
					$sInfo = 'Jahresgebühr Deutschland-Premiumeintrag '.($pay_create_y+1).'/'.($pay_create_y+2);
				}
				
				if(date('Y')>$pay_create_y){
					$pay_create=strtotime(($pay_create_y+1).'-'.$pay_create_m.'-'.$pay_create_d);
					if($pay_create<time()){
						//echo $res[$i]['id']."<br>";
						//flush();

						$next=$db->getRow("select max(pay_id) as id from kunden_pay",null,DB_FETCHMODE_ASSOC);
						$pay_id = $next['id']+1;
						$field_values=array(
						'pay_id'=>$pay_id,
						'id'=>$res[$i]['id'],
						'price'=> $fPrice,
						'tax'=>19,
						'tax_sum'=> $fTaxSum,
						'info'=> $sInfo,
						'pay_create'=>date('Y-m-d',$pay_create),
						'pay_status'=>true,
						);
						$db->autoExecute($db->quoteIdentifier('kunden_pay'), $field_values, DB_AUTOQUERY_INSERT);
					}
				} else {
					if($pay["price"] < $fPrice) {
						$next=$db->getRow("select max(pay_id) as id from kunden_pay",null,DB_FETCHMODE_ASSOC);
						$pay_id = $next['id']+1;
						
						$fPrice = $fPrice - $pay["price"];
						$fTaxSum = $fTaxSum - $pay["tax_sum"];
						
						$field_values=array(
						'pay_id'=>$pay_id,
						'id'=>$res[$i]['id'],
						'price'=> $fPrice,
						'tax'=>19,
						'tax_sum'=> $fTaxSum,
						'info'=> $sInfo,
						'pay_create'=>date('Y-m-d',$pay_create),
						'pay_status'=>true,
						);
						$db->autoExecute($db->quoteIdentifier('kunden_pay'), $field_values, DB_AUTOQUERY_INSERT);
					}
				}

			} else {
				//echo $res[$i]['id']."<br>";
				//flush();

				$next=$db->getRow("select max(pay_id) as id from kunden_pay",null,DB_FETCHMODE_ASSOC);
				$pay_id = $next['id']+1;

				if($res[$i]['eintr_art'] == "ow") {
					$fPrice = 11.88;
					$fTaxSum = 2.26;
					$sInfo = 'Jahresgebühr Basiseintrag ohne Werbung '.date("Y").'/'.(date("Y")+1);
				} elseif($res[$i]['eintr_art'] == "p") {
					$fPrice = 120;
					$fTaxSum = 22.80;
					$sInfo = 'Jahresgebühr Premiumeintrag '.date("Y").'/'.(date("Y")+1);
				} elseif($res[$i]['eintr_art'] == "pp") {
					$fPrice = 479.88;
					$fTaxSum = 91.18;
					$sInfo = 'Jahresgebühr Deutschland-Premiumeintrag '.date("Y").'/'.(date("Y")+1);
				}
				
				$field_values=array(
				'pay_id'=>$pay_id,
				'id'=>$res[$i]['id'],
				'price'=> $fPrice,
				'tax'=>19,
				'tax_sum'=> $fTaxSum,
				'info'=> $sInfo,
				'pay_create'=>date("Y-m-d"),
				'pay_status'=>true,
				);
				$db->autoExecute($db->quoteIdentifier('kunden_pay'), $field_values, DB_AUTOQUERY_INSERT);
			}
		}
	}
}

?>
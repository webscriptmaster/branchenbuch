<?php

class Billing {
	
	public static $db;
	
	public static function generateNew() {
		self::$db = &DB::connect(DB_DSN);
		if (PEAR::isError(self::$db)) {
			die(self::$db->getMessage());
		}
		
		$aKunden = self::$db->getAll(
			"
			SELECT
				* 
			FROM kunden_neu 
			WHERE 
				(
					eintr_art = 'p' 
					OR eintr_art = 'ow' 
					OR eintr_art = 'pp'
				)
				AND email_bestaetigt = 1
				AND rechnung_erstellen = 1
			",
			null,
			DB_FETCHMODE_ASSOC
		);
		
		foreach($aKunden as $aKunde) {
			$aRechnung = self::$db->getRow("
				SELECT
					kr.*,
					krp.name
				FROM kunden_rechnungen AS kr
				LEFT JOIN kunden_rechnungen_pos AS krp
					ON kr.id = krp.rechnung_id
				WHERE
					kr.kunden_id = ". self::$db->quoteSmart($aKunde["id"]) ."
				ORDER BY kr.datum DESC, kr.id DESC
				LIMIT 1
			", NULL, DB_FETCHMODE_ASSOC);
			
			if(is_null($aRechnung)) {
				$sSql = "
					SELECT
						*,
						(price * 12) AS price
					FROM produkte
					WHERE
						code = ". self::$db->quoteSmart($aKunde['eintr_art']) ."
					LIMIT 1
				"; 
				$aProdukt = self::$db->getRow($sSql, NULL, DB_FETCHMODE_ASSOC);
							
				self::createNew($aKunde["id"], $aProdukt["tax"], array(
					0 => array(
						"price_netto" => $aProdukt["price"],
						"name"	=> $aProdukt["name"]
					)
				));
			} else {
				$oTZ = new DateTimeZone("Europe/Berlin");
				$oDate = new DateTime("now", $oTZ);
				$oDateLastBilling = new DateTime($aRechnung["datum"], $oTZ);
				
				if($aKunde["rechnung_erstellen"]) {
					$fDiffTime = $oDate->getTimestamp() - $oDateLastBilling->getTimestamp();
					$iDaysAlreadyPayed = ceil($fDiffTime/60/60/24);
					
					$fBereitsGezahlt = $aRechnung["price_sum"] / 365 * $iDaysAlreadyPayed;
					
					$fGutschrift = (-1) * round($aRechnung["price_sum"] - $fBereitsGezahlt, 2);
					
					$sSql = "
						SELECT
							*,
							(price * 12) AS price
						FROM produkte
						WHERE
							code = ". self::$db->quoteSmart($aKunde['eintr_art']) ."
						LIMIT 1
					"; 
					$aProdukt = self::$db->getRow($sSql, NULL, DB_FETCHMODE_ASSOC);
					
					self::createNew($aKunde["id"], $aProdukt["tax"], array(
						0 => array(
							"price_netto" => $aProdukt["price"],
							"name"	=> $aProdukt["name"]
						),
						1 => array(
							"price_netto" => $fGutschrift,
							"name"	=> "Guthaben " . $aRechnung["name"]
						)
					));
				} elseif(
					$oDate->format("Y") > $oDateLastBilling->format("Y") &&
					(
						$oDate->format("m") > $oDateLastBilling->format("m") ||
						(
							$oDate->format("d") > $oDateLastBilling->format("d") &&
							$oDate->format("m") == $oDateLastBilling->format("m")
						)
					)
				) {
					$sSql = "
						SELECT
							*,
							(price * 12) AS price
						FROM produkte
						WHERE
							code = ". self::$db->quoteSmart($aKunde['eintr_art']) ."
						LIMIT 1
					"; 
					$aProdukt = self::$db->getRow($sSql, NULL, DB_FETCHMODE_ASSOC);
					
					self::createNew($aKunde["id"], $aProdukt["tax"], array(
						0 => array(
							"price_netto" => $aProdukt["price"],
							"name"	=> $aProdukt["name"]
						)
					));
				}
			}
			
			self::$db->query("
				UPDATE kunden_neu 
				SET 
					rechnung_erstellen = 0 
				WHERE 
					id = ". self::$db->quoteSmart($aKunde["id"]) ."
				LIMIT 1
			");
		}	
	}
	
	public static function createNew($iKundenId, $fTaxRate, $aPositions) {
		self::$db = &DB::connect(DB_DSN);
		if (PEAR::isError(self::$db)) {
			die(self::$db->getMessage());
		}
		
		$fSumNetto = 0;
		$fSumTax = 0;
		foreach($aPositions as $aPos) {
			$fSumNetto += $aPos["price_netto"];
			$fSumTax += round($aPos["price_netto"] * ($fTaxRate / 100), 2);
		}
		
		$res = self::$db->query("BEGIN");
		if(PEAR::isError($res)) {
			return FALSE;
		}
		
		
		$iNewId = self::storeBilling($iKundenId, $fSumNetto, $fSumTax, $fTaxRate);
		if(!$iNewId) {
			self::$db->query("ROLLBACK");
			return FALSE;
		}
		
		if(!self::storePositions($iNewId, $fTaxRate, $aPositions)) {
			self::$db->query("ROLLBACK");
			return FALSE;
		}
		
		self::$db->query("COMMIT");
		return TRUE;
	}
	
	private static function getNextId() {
		$sSql = "
			SELECT
				MAX(id)+1 AS id
			FROM kunden_rechnungen
		";
		$res = self::$db->getRow($sSql);
		if(PEAR::isError($res)) {
			return FALSE;
		}
		return (intval($res[0]) == 0 ? 1 : intval($res[0]));
	}
	
	private static function storeBilling($iKundenId, $fSumNetto, $fSumTax, $fTaxRate) {
		$iNewId = self::getNextId();
		if($iNewId === FALSE) {
			return FALSE;
		}
	
		$sSql = "
			INSERT INTO kunden_rechnungen 
			SET
				id = ?,
				kunden_id = ?,
				price_sum = ?,
				tax_sum = ?,
				tax_rate = ?,
				datum = NOW()
		"; 
		$res = self::$db->query(
			$sSql, 
			array(
				$iNewId,
				$iKundenId, 
				$fSumNetto,
				$fSumTax,
				$fTaxRate
			)
		);
		if(PEAR::isError($res)) {
			return FALSE;
		}
		
		return $iNewId;
	}
	
	private static function storePositions($iRechnungId, $fTaxRate, $aPositions) {
		$aInserts = array();
		foreach($aPositions as $aPos) {
			$aInserts[] = "(
				". intval($iRechnungId) .",
				".self::$db->quoteSmart($aPos["name"]).",
				".floatval($aPos["price_netto"]).",
				".round(floatval($aPos["price_netto"] * ($fTaxRate / 100)), 2)."				
			)";
		}
		
		$sSql = "
			INSERT INTO kunden_rechnungen_pos (
				rechnung_id,
				name,
				price,
				tax
			)
			VALUES ".implode(",", $aInserts)."
				
		"; 
		$res = self::$db->query($sSql);
		if(PEAR::isError($res)) {
			return FALSE;
		}
		
		return TRUE;
	}
	
	
}
<?php
/**
 * Klasse Datenbank
 * 
 * Alle Rechte vorbehalten. Dieser Quelltext und die daraus resultierenden
 * Seiten dürfen nicht verändert, kopiert, wiederveröffentlicht, übertragen,
 * verbreitet oder gespeichert werden. Jede Benutzung dieses Quelltextes bedarf
 * der Zustimmung des Authors.
 * 
 * @package kernel.inc
 * @subpackage sql.inc
 * @version $Id: sql.inc,v 1.19 2006/08/31 15:15:45 peter Exp $
 * @copyright Copyright (c) 2008, neoterix GmbH
 * 
 */

/**
 * Verarbeitet MySQl-Datenbankabfragen 
 * 
 * @package kernel.inc
 * @author Marcel Richtsteiger
 * @author Peter Brückner
 */
class sql_query {
	
	/**
	 * Ergebnis einer Abfrage
	 * @var resource
	 */
	var $result;
	
	/**
	 * Fehlercode der letzten Abfrage
	 * var int
	 */
	var $errno;
	
	/**
	 * Fehlermeldung der letzten Abfrage
	 * @var string
	 */ 
	var $error;
	
	/**
	 * Anzahl der Datensätze des Ergenisses
	 * @var int
	 */
	var $affected_rows;
	
	/**
	 * Anzahl der Felder des Ergebnisses
	 * @var int
	 */ 
	var $affected_cols;
	
	/**
	 * zugewiesene ID beim INSERT-Befehl
	 * @var int
	 */
	var $insert_id; 
	
	/**
	 * der SQL-Abfragestring
	 * @var string
	 */
	var $request; 

	/**
	* Konstruktor. Erzeugt eine neue MySQL-Datenbankabfrage.
	* 
	* Im Falle eines Fehlers wird bei aktivierten Debug-Modus die Fehlermeldung
	* von der MySQL formatiert ausgegeben(nur wenn User Administratorstatus
	* besitzt) und in die error_sql_query Tabelle abgelegt
	* 
	* @author Marcel Richtsteiger
	* 
	* @param string $query_string Die eigentliche SQL-Abfrage 
	* @param boolean $debug  true ,wenn Fehlermeldungen ausgegeben werden sollen
	* @param int $db_link optionale Angabe der Verbindungskennung zur Datenbank
	*/
	function sql_query($query_string, $debug = false, $db_link = null) {   		
   		if ($db_link == null) {
			$this->result = @ mysql_query($query_string, $GLOBALS['connection']);
			$this->error = @ mysql_error($GLOBALS['connection']);
			$this->errno = @ mysql_errno($GLOBALS['connection']);
			$this->insert_id = @ mysql_insert_id($GLOBALS['connection']);
		} else {
			$this->result = @ mysql_query($query_string, $db_link);
			$this->error = @ mysql_error($db_link);
			$this->errno = @ mysql_errno($db_link);
			$this->insert_id = @ mysql_insert_id($db_link);
		}
		$isselect = !(stristr($query_string, "INSERT"));
		if ($isselect === false) {
			$this->affected_rows = @ mysql_affected_rows();
			$this->affected_cols = 0;
		} else {
			$this->affected_rows = @ mysql_num_rows($this->result);
			$this->affected_cols = @ mysql_num_fields($this->result);
		}
		$this->affected_rows = $this->get_affected_rows();
		$this->affected_cols = $this->get_affected_cols();
		$this->request = $query_string;
		if($this->errno) {
			echo "\n".$this->error;
		}
		if ($debug) {
			$sql = "INSERT INTO err_sql_query (query)
					VALUES ('".mysql_real_escape_string($this->request)."')";
			$send = mysql_query($sql);	
		} 
	}
	
	/**
	* Gibt aktuelle Fehlermeldung zurück
	* 
	* @return string unformatierte Fehlermeldung
	*/
	function get_error() {
		return $this->error;
	}
	
	/**
	* Gibt aktuelle Fehlernummer zurück
	* 
	* @return int Fehlernummer
	*/
	function get_errno() {
		return $this->errno;
	}
	
	/**
	* Gibt eine vorformatierte Fehlermeldung inkl. Fehlernummer zurück
	* 
	* @return string formatierte Fehlermeldung
	*/
	function get_error_message() {
		if (!$this->error)
			return "";
		else
			return "mySQL Fehler #".$this->errno." mySQL sagte: ".$this->error;
	}
	
	/**
	* Gibt die Anzahl der Ergebnisszeilen zurück
	* 
	* @return int Anzahl der Ergebnisszeilen
	*/
	function get_affected_rows() {
		return $this->affected_rows;
	}
	
	/**
	* Gibt die Anzahl der verarbeitenden Felder zurück
	* 
	* @return int Anzahl der verarbeitenden Felder
	*/
	function get_affected_cols() {
		return $this->affected_cols;
	}
	
	/**
	* Gibt das Ergebnis unaufbereitet zurück
	* 
	* @return unaufbereitetes Ergebnis der Abfrage
	*/
	function get_result() {
		@mysql_data_seek($this->result, 0);
		return $this->result;
	}
	
	/**
	* Gibt ein Ergenisobjekt zurück, welches die aktuelle
	* Ergebniszeile enthält, oder die Ergebniszeile, welche mit
	* $index angegeben wurde
	* 
	* @param int $index Ergebniszeile
	* 
	* @return object Objekt enthält als Klassenvariablen die ausgelesenen Werte
	*/
	function fetch_object($index = null) {
		if ($index == null)
			return @mysql_fetch_object($this->result);
		else {
			$this->seek($index);
			return @mysql_fetch_object($this->result);
		}
	}

	/**
	* Gibt ein Ergenisarray zurück, welches die aktuelle
	* Ergebniszeile enthält, oder die Ergebniszeile, welche mit
	* $index angegeben wurde
	* 
	* @param int $index Ergebniszeile
	* 
	* @return array Ergebnisarray
	*/
	function fetch_array($index = null) {
		if ($index == null)
			return @mysql_fetch_array($this->result);
		else {
			@$this->seek($index);
			return @mysql_fetch_array($this->result);
		}
	}
	
	/**
	 * Gibt ein Array aus Ergebnisobjekten zurück. Das Ergebnis wird komplett in
	 * das Array übetragen.
	 * 
	 * @return array Alle ausgelesenen Einträge
	 */
	function fetch_all_object() {
		while($entry = $this->fetch_object())
			$entrys[] = $entry;
		return $entrys;	
	}
	
	/**
	 * Setzt den Ergebniszeiger auf die mit $index angegebene Ergebniszeile
	 */
	function seek($index) {
		@mysql_data_seek($this->result, $index);
	}
	
	/**
	* Gibt die durch eine INSERT Funktion von MySQL erzeugte ID zurück
	* 
	* @return int DB-id des neu erzeugtem Eintrags
	*/
	function get_insert_id() {
		return $this->insert_id;
	}
	
	/**
	* Gibt die SQL-Abfrage ansich als String zurück
	* 
	* @return string SQL-Abfrage
	*/
	function get_request() {
		return $this->request;
	}	
}

?>
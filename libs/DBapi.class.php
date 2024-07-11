<?php
//=========================================
// Script	: DBapi Branchenbuchdeutschland.de
// File		: DBapi.class.php
// Version	: 1.0
// Authoren	: Marcel Richtsteiger, Matthias Franke
//=========================================
// Copyright (c) 2009 zielplus GmbH
//=========================================
require_once(dirname(__FILE__)."/adodb/adodb.inc.php");

if(!function_exists("microtime_float")) {
	function microtime_float() {
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
}

function _pf() { return microtime_float(); }

function _parse_attrs($args) {
	$php_str = "?".">";
	// String in doppelten Anführungszeichen
	$double_str = '"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"';
	// String in einfachen Anführungszeichen
	$single_str = '\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'';
	// ein String
	$str_pattern = '(?:' . $double_str . '|' . $single_str . ')';

	/* Tokenize tag attributes. */
	preg_match_all('~(?:'.$str_pattern.'|('.$php_str.'[^"\'=\s]+))+|[=]~x', $args, $match);
	$tokens  = $match[0];
	$attrs = array();
	/* Parse state:
	0 - expecting attribute name
	1 - expecting '='
	2 - expecting attribute value (not '=') */
	$state = 0;

	foreach ((array)$tokens as $token) {
		switch ($state) {
			case 0:
			/* If the token is a valid identifier, we set attribute name
			and go to state 1. */
			if (preg_match('~^\w+$~', $token)) {
				$attr_name = $token;
				$state = 1;
			} else {
				return $attrs;
			}
			break;

			case 1:
			/* If the token is '=', then we go to state 2. */
			if ($token == '=') {
				$state = 2;
			} else {
				return $attrs;
			}
			break;

			case 2:
			/* If token is not '=', we set the attribute value and go to
			state 0. */
			if ($token != '=') {
				// ein String, also Gänsefüsschen entfernen
				if(preg_match('~^' . $str_pattern . '$~', $token)) {
					$token = substr($token, 1, -1);
					$token = stripslashes($token);
				}
				/*
				// Variablen aus dem Variablen Cache ersetzen
				if($token[0] == "%")  {
				$token = substr($token, 1);
				$token = $this->_parse_vars($token);
				}
				*/
				// Escaped Trennzeichen entfernen
				$token = str_replace(array('\[', '\]'), array('[', ']'), $token);
				$attrs[$attr_name] = $token;
				$state = 0;
			} else {
				return $attrs;
			}
			break;
		}
		$last_token = $token;
	}

	if($state != 0) {
		if($state == 1) {
			echo "Syntax Error: expecting '=' after attribute name '$last_token'";
		} else {
			echo "Syntax Error: missing attribute value";
		}
	}
	return $attrs;
}

function checkError($res) {
	if(PEAR::isError($res)) {
		echo 'DBMS/Message: ' . $res->getMessage() . "<br />";
		echo 'DBMS/User Message: ' . $res->getUserInfo() . "<br />";
		echo 'DBMS/Debug Message: ' . $res->getDebugInfo() . "<br />";
		return true;
	}
	return false;
}

function buildWhere($db, $where, &$data) {
	if(!is_array($data)) $data = array();
	$_where = '';
	foreach((array)$where as $type => $part) {
		foreach((array)$part as $stmt) {
			$subpart = "";
			if(is_array($stmt)) {
				$subpart = buildWhere($db, $stmt, $data);
			} else {
				while(preg_match("/[{]([?!&%]+)[}]/", $stmt, $match)) {
					$val = array_shift($data);
					if(is_array($val)) {
						if(is_object($db) && $match[1] != "!") for($i=0;$i<count($val);$i++) $val[$i] = $db->quoteSmart($val[$i]);
						$val = '('.join(', ', $val).')';
					} else {
						if($match[1] == "&") $val = "%$val%";
						if($match[1] == "%&") $val = "%$val";
						if($match[1] == "&%") $val = "$val%";
						if(is_object($db) && $match[1] != "!") $val = $db->quoteSmart($val);
					}
					$pos = strpos($stmt, $match[0]);
					if($pos !== false) {
						$stmt = substr_replace($stmt, $val, $pos, strlen($match[0]));
					}
					//$stmt = str_replace($match[0], $val, $stmt);
				}
				$subpart = $stmt;
			}
			if(empty($_where)) {
				$_where = $subpart;
			} else {
				$_where .= " $type ".$subpart;
			}
		}
	}
	if(!empty($_where)) return "(".$_where.")";
}

function AdoBuildWhere($db, $where, &$data) {
	if(!is_array($data)) $data = array();
	$_where = '';
	foreach((array)$where as $type => $part) {
		foreach((array)$part as $stmt) {
			$subpart = "";
			if(is_array($stmt)) {
				$subpart = buildWhere($db, $stmt, $data);
			} else {
				while(preg_match("/[{]([?!&%]+)[}]/", $stmt, $match)) {
					$val = array_shift($data);
					if(is_array($val)) {
						if(is_object($db) && $match[1] != "!") for($i=0;$i<count($val);$i++) $val[$i] = $db->Quote($val[$i]);
						$val = '('.join(', ', $val).')';
					} else {
						if($match[1] == "&") $val = "%$val%";
						if($match[1] == "%&") $val = "%$val";
						if($match[1] == "&%") $val = "$val%";
						if(is_object($db) && $match[1] != "!") $val = $db->Quote($val);
					}
					$pos = strpos($stmt, $match[0]);
					if($pos !== false) {
						$stmt = substr_replace($stmt, $val, $pos, strlen($match[0]));
					}
					//$stmt = str_replace($match[0], $val, $stmt);
				}
				$subpart = $stmt;
			}
			if(empty($_where)) {
				$_where = $subpart;
			} else {
				$_where .= " $type ".$subpart;
			}
		}
	}
	if(!empty($_where)) return "(".$_where.")";
}

class DBAPI extends PEAR {

	function DBAPI(){
		$this->PEAR();
	}

	function make_unique($size=8) {
		$pass_word = "";
		$pool = "qwertzupasdfghkyxcvbnm";
		$pool .= "23456789";
		$pool .= "WERTZUPLKJHGFDSAYXCVBNM";
		srand ((double)microtime()*1000000);
		for($index = 0; $index < $size; $index++)
		{
			$pass_word .= substr($pool,(rand()%(strlen ($pool))), 1);
		}
		return $pass_word;
	}

	function GetFields($table){
		$db=&DB::connect(DB_DSN);
		$res=$db->getAll("show columns from $table",null,DB_FETCHMODE_ASSOC);
		if (DB::isError($res)) { return false; }
		if (count($res)>0) {
			foreach ($res as $row) {
				$fields[$row['Field']]=$row['Type'];
			}
			return $fields;
		} else { return false; }
	}

	function GetSortFields($data){
		$db=&DB::connect(DB_DSN);
		return $db->getRow("select sort_field from ".$data['table']." where ".$data['field']."='".$data['sort_id']."'",null,DB_FETCHMODE_ASSOC);
	}

	function GetSortFieldsToTop($data){
		$db=&DB::connect(DB_DSN);
		return $db->getRow("select ".$data['field']." as sort_id from ".$data['table']." where sort_field='".($data['sort_field']-1)."'",null,DB_FETCHMODE_ASSOC);
	}

	function GetSortFieldsToBottom($data){
		$db=&DB::connect(DB_DSN);
		return $db->getRow("select ".$data['field']." as sort_id from ".$data['table']." where sort_field='".($data['sort_field']+1)."'",null,DB_FETCHMODE_ASSOC);
	}

	function UpdateSortFields($data){
		$db=&DB::connect(DB_DSN);
		$res=$db->getAll("select ".$data['field']." as field from ".$data['table']." order by sort_field asc",null,DB_FETCHMODE_ASSOC);
		for($i=0;$i<=count($res);$i++){
			$res_update=$db->query("update ".$data['table']." set sort_field=".($i+1)." where ".$data['field']."='".$res[$i]['field']."'");
			if($res_update==false) return false;
		}
		return true;
	}

	function get_laender_dropdown($parent = -1, $level = 0, $prefix = "") {
		/*
		$liste = array();
		$db=&DB::connect(DB_DSN);
		$res = $db->getAll('SELECT * FROM `orte` WHERE pid = '.$db->quoteSmart($parent).' ORDER BY name ASC', null, DB_FETCHMODE_ASSOC);
		if(!PEAR::isError($res) && is_array($res)) {
		foreach($res as $p) {
		$liste[$p['id']] = $prefix.$p['name'];
		foreach((array)DBApi::get_laender_dropdown($p['id'], $level+1, $prefix.$p['name']."/") as $k => $v) {
		$liste[$k] = $v;
		}
		}
		}
		return (array)$liste;
		*/
		$db=&DB::connect(DB_DSN);
		$res = $db->getAssoc('SELECT t1.id, concat(t2.name, "/", t1.name) as name FROM orte AS t1 LEFT JOIN orte AS t2 ON t1.pid = t2.id HAVING name is not null order by name');
		return $res;
	}

	function get_branchen_dropdown($parent = -1, $level = 0, $prefix = "") {
		/*
		$liste = array();
		$db=&DB::connect(DB_DSN);
		$res = $db->getAll('SELECT * FROM `branchen` WHERE pid = '.$db->quoteSmart($parent).' ORDER BY name ASC', null, DB_FETCHMODE_ASSOC);
		if(!PEAR::isError($res) && is_array($res)) {
		foreach($res as $p) {
		$liste[$p['id']] = $prefix.$p['name'];
		foreach((array)DBApi::get_branchen_dropdown($p['id'], $level+1, $prefix.$p['name']."/") as $k => $v) {
		$liste[$k] = $v;
		}
		}
		}
		return (array)$liste;
		*/
		$db=&DB::connect(DB_DSN);
		$res = $db->getAssoc('SELECT t1.id, concat(t2.name, "/", t1.name) as name FROM branchen AS t1 LEFT JOIN branchen AS t2 ON t1.pid = t2.id HAVING name is not null order by name');
		return $res;

	}

	function check_token($token) {
		$db=&DB::connect(DB_DSN);
		return $db->getOne('Select count(*) from `kunden` WHERE aktiv = '.$db->quoteSmart($token));
	}

	function next_kunden_id(){
		$db=&DB::connect(DB_DSN);
		$res=$db->getRow("select max(id) as id from kunden",null,DB_FETCHMODE_ASSOC);
		return $res['id']+1;
	}

	function check_kunden_email($email){
		$db=&DB::connect(DB_DSN);
		$res=$db->getRow("select * from kunden_neu where email='".$email."'",null,DB_FETCHMODE_ASSOC);
		if(empty($res)){
			return false;
		} else {
			return true;
		}
	}

	function check_registration($data) {
		if(empty($data['email'])) return PEAR::raiseError("email Adresse fehlt");
		if(empty($data['firma'])) return PEAR::raiseError("Firmenbezeichnung fehlt");
		if(empty($data['strasse'])) return PEAR::raiseError("Strasse fehlt");
		if(empty($data['plz'])) return PEAR::raiseError("PLZ fehlt");
		if(empty($data['ort'])) return PEAR::raiseError("Ort fehlt");
		if(empty($data['tel'])) return PEAR::raiseError("Telefonnummer fehlt");

		$db=&DB::connect(DB_DSN);
		$res = $db->getRow('Select * from kunden WHERE email = '.$db->quoteSmart($data['email']), null, DB_FETCHMODE_ASSOC);
		if(is_array($res)) {
			return PEAR::raiseError(ERROR_EMAIL_EXISTS);
		}
		// Token erzeugen
		do {
			$uniq = DBApi::make_unique(8);
		} while(DBApi::check_token($uniq) > 0);

		$data['id'] = DBApi::next_kunden_id();
		$data['password'] = md5 ($data['passwd1']);
		$data['aktiv'] = $uniq;
		$data['indate'] = date('Y-m-d H:i:s');
		return $data;
	}

	function save_member($data, $member_id = false) {
		$db=&DB::connect(DB_DSN);
		$field_values = array();
		$field_defs = $db->tableInfo("kunden", DB_TABLEINFO_ORDER);
		foreach ($data as $field => $value) {
			if(isset($field_defs['order'][$field])){
				$field_values[$field] = $value;
			}
		}
		// speichern
		if($member_id > 0) {
			$res = $db->autoExecute($db->quoteIdentifier('kunden'), $field_values, DB_AUTOQUERY_UPDATE, 'id = '.$db->quoteSmart($member_id));
			return $res;
		} else {
			$res = $db->autoExecute($db->quoteIdentifier('kunden'), $field_values, DB_AUTOQUERY_INSERT);
			return $res;
		}
		checkError($res);
	}
	
	function next_member_id(){
		$db=&DB::connect(DB_DSN);
		$res=$db->getRow("select max(id) as id from kunden_neu",null,DB_FETCHMODE_ASSOC);
		return $res['id']+1;
	}
	
	
	function save_domain($data,$new=true){
		$table="subdomain";
		$db=&DB::connect(DB_DSN);
		$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
		foreach ($data as $field => $value) {
			if(isset($table_field['order'][$field])){
				//$fields_values[$field]=addslashes($value);
				$fields_values[$field]=$value;
			}
		}	
		if(!empty($fields_values)){
			if($new==true){
				$res=$db->autoExecute($table, $fields_values,DB_AUTOQUERY_INSERT);
			} else {
				$res=$db->autoExecute($table, $fields_values,DB_AUTOQUERY_UPDATE, "id = '".$data['id']."'");

			}
		}
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
		return $res;
	}
	
	function delete_member($iMemberId) {
		$DB = NewADOConnection(DB_DSN);
		
		$data = array("aktiv" => "0");
		$res = $DB->AutoExecute('kunden_neu', $data, "UPDATE", 'id = '. intval($iMemberId));
		return $res;
	}
	
	function save_member2($data, $id = false) {
		$DB = NewADOConnection(DB_DSN);
		if($_SESSION['aktionscode']) {
			$data['aktionscode'] = $_SESSION['aktionscode'];
		}
		
		// speichern
		if($id > 0) {
			$res = $DB->AutoExecute('kunden_neu', $data, "UPDATE", 'id = '.$id);
			if(!$res) $_SESSION['flash'] = $DB->ErrorMsg();			
			return $id;
		} else {
			$DB->Execute('LOCK TABLES kunden_neu WRITE;');			
			$new_id = $DB->getOne('SELECT max(id)+1 from kunden_neu');
			$data['id'] = $new_id;
			$res = $DB->AutoExecute('kunden_neu', $data, "INSERT");
			if(!$res) {
				$error = $DB->ErrorMsg();
				$_SESSION['flash'] = $error;
				echo "<br />FEHLER: $error";
				$new_id = false;
			}
			
			$DB->Execute('UNLOCK TABLES;');
			return $new_id;
		}
	
	}
	
	function save_cache_premium($data, $id = false) {
		$DB = NewADOConnection(DB_DSN);
		// speichern
		if($id > 0) {
			$res = $DB->AutoExecute('cache_premium', $data, "UPDATE", 'kunden_id = '.$id);
			if(!$res) $_SESSION['flash'] = $DB->ErrorMsg();
			return $id;
		} else {
			$DB->Execute('LOCK TABLES cache_premium WRITE;');			
			//$new_id = $DB->getOne('SELECT max(id)+1 from cache_premium');
			//$data['id'] = $new_id;
			$res = $DB->AutoExecute('cache_premium', $data, "INSERT");
			if(!$res) {
				$error = $DB->ErrorMsg();
				$_SESSION['flash'] = $error;
				echo "<br />FEHLER: $error";
				$new_id = false;
			}
			
			$DB->Execute('UNLOCK TABLES;');
			return $new_id;
		}
	
	}
	
	function del_cache_premium($id){
		$db=&DB::connect(DB_DSN);
		return $db->query("delete from cache_premium where kunden_id='".$id."'");
	}
	
	function get_next_free_id() {
		$DB = NewADOConnection(DB_DSN);
		$DB->Execute('LOCK TABLES seq_t_com WRITE;');
		$new_id = $DB->getOne('SELECT max(seq)+1 from seq_t_com');
		// sanity check required for external data modifications
		$max_existing = $DB->getOne('SELECT max(id)+1 from t_com_firma2');
		if ($max_existing > $new_id) {
		    $new_id = $max_existing;
		}
		$DB->Execute('UPDATE seq_t_com set seq = ?', array($new_id));
		$DB->Execute('UNLOCK TABLES;');
		return $new_id;
	}

	
	function save_firma2($data, $id = false) {
		$DB = NewADOConnection(DB_DSN);
		// speichern
		if($id > 0) {
			//print_r($data);
			if($data['eintr_art']=='p')
				$data['public']='N';
			$res = @$DB->AutoExecute('t_com_firma2', $data, "UPDATE", 'kunden_id = '.$id);
			if(!$res) $_SESSION['flash'] = $DB->ErrorMsg();
			return $id;
		} else {
			$DB->Execute('LOCK TABLES seq_t_com, t_com_firma2 WRITE;');
			$new_id = $DB->getOne('SELECT max(seq)+1 from seq_t_com');
			// sanity check required for external data modifications
			$max_existing = $DB->getOne('SELECT max(id)+1 from t_com_firma2');
			if ($max_existing > $new_id) {
			    $new_id = $max_existing;
			}
			$data['id'] = $new_id;
			$res = $DB->AutoExecute('t_com_firma2', $data, "INSERT");
			if(!$res) {
				echo $error = $DB->ErrorMsg();
				$_SESSION['flash'] = $error;
				die();
			}
			$DB->Execute('UPDATE seq_t_com set seq = ?', array($new_id));
			$DB->Execute('UNLOCK TABLES;');
			return $new_id;
		}
	}
	
	function save_firma2_id($data, $id = false) {
		$DB = NewADOConnection(DB_DSN);
		// speichern
		if($id > 0) {
			$res = $DB->AutoExecute('t_com_firma2', $data, "UPDATE", 'id = '.$id);
			if(!$res) $_SESSION['flash'] = $DB->ErrorMsg();
			return $id;
		} else {
			$DB->Execute('LOCK TABLES seq_t_com, t_com_firma2 WRITE;');
			$new_id = $DB->getOne('SELECT max(seq)+1 from seq_t_com');
			// sanity check required for external data modifications
			$max_existing = $DB->getOne('SELECT max(id)+1 from t_com_firma2');
			if ($max_existing > $new_id) {
			    $new_id = $max_existing;
			}
			$data['id'] = $new_id;
			$res = $DB->AutoExecute('t_com_firma2', $data, "INSERT");
			if(!$res) {
				echo $error = $DB->ErrorMsg();
				$_SESSION['flash'] = $error;
				die();
			}
			$DB->Execute('UPDATE seq_t_com set seq = ?', array($new_id));
			$DB->Execute('UNLOCK TABLES;');
			return $new_id;
		}
	}
	
	function get_werbung($type=1) {
		$db=&DB::connect(DB_DSN);
		$res = $db->getAll('Select * from werbung WHERE affili_status=1 and affili_type = '.$db->quoteSmart($type).' order by rand() limit 0,5', null, DB_FETCHMODE_ASSOC);
		checkError($res);
		return $res;
	}

	function get_member($member_id) {
		$db=&DB::connect(DB_DSN);
		$res = $db->getRow('
			Select 
				*, 
				id AS kunden_id,
				IFNULL((
					SELECT
						short
					FROM produkte
					WHERE
						code = kunden_neu.eintr_art
					LIMIT 1
				), "Basis") AS eintr_art_name
			from kunden_neu WHERE id = '.$db->quoteSmart($member_id), null, DB_FETCHMODE_ASSOC);
		//$res = $db->getRow('Select * from kunden_neu WHERE id = '.$db->quoteSmart($member_id));
		checkError($res);
		return $res;
	}
	
	function get_kunden_neu_import($member_id){
		$db=&DB::connect(DB_DSN);
		$res = $db->getRow('Select * from kunden_neu_import WHERE id = '.$db->quoteSmart($member_id), null, DB_FETCHMODE_ASSOC);
		checkError($res);
		return $res;
	}
	
	function get_all_kunden_neu_import(){
		$db=&DB::connect(DB_DSN);
		$res = $db->getAll('Select * from kunden_neu_import ', null, DB_FETCHMODE_ASSOC);
		checkError($res);
		return $res;
	}
	
	function get_all_member() {
		$db=&DB::connect(DB_DSN);
		$res = $db->getAll('Select * from kunden_neu', null, DB_FETCHMODE_ASSOC);
		checkError($res);
		return $res;
	}
	
	function get_member_by_admin($id) {
		$db=&DB::connect(DB_DSN);
		$res = $db->getRow('Select * from t_com_firma2 WHERE id = '.$db->quoteSmart($id), null, DB_FETCHMODE_ASSOC);
		if(!empty($res['kunden_id'])){
			$res = $db->getRow('Select * from kunden_neu WHERE id = '.$db->quoteSmart($res['kunden_id']), null, DB_FETCHMODE_ASSOC);
		}
		checkError($res);
		return $res;
	}
	
	function get_member_by_t_com($member_id) {
		$db=&DB::connect(DB_DSN);
		$res = $db->getRow('Select * from t_com_firma2 WHERE kunden_id = '.$db->quoteSmart($member_id), null, DB_FETCHMODE_ASSOC);
		checkError($res);
		return $res;
	}


	function get_member_by_email($email) {
		$db=&DB::connect(DB_DSN);
		$res = $db->getRow('Select * from kunden_neu WHERE email = '.$db->quoteSmart($email), null, DB_FETCHMODE_ASSOC);
		checkError($res);
		return $res;
	}

	function get_member_by_firma($member_id) {
		$db=&DB::connect(DB_DSN);
		$res = $db->getRow('
			Select 
				tcf.*, 
				1 AS aktiv,
				GROUP_CONCAT(bn.name ORDER BY bn.name ASC SEPARATOR ";|") AS branchen_all
			from t_com_firma2 AS tcf
			LEFT JOIN lala_branche AS lb
				ON lb.id = tcf.id
			LEFT JOIN branchen_neu AS bn
				ON lb.bid = bn.id
			WHERE tcf.id = '.$db->quoteSmart($member_id) . "
			GROUP BY tcf.id
			", 
			null, 
			DB_FETCHMODE_ASSOC
		);
		checkError($res);
		if(!empty($res['kunden_id'])) {
			$kunden_id = $res['kunden_id'];
			$res2 = $db->getRow('
				Select 
					*,
					IFNULL((
						SELECT
							short
						FROM produkte
						WHERE
							code = kunden_neu.eintr_art
						LIMIT 1
					), "Basis") AS eintr_art_name
				from kunden_neu WHERE id = '.$db->quoteSmart($kunden_id), null, DB_FETCHMODE_ASSOC);
			unset($res2['id']);
			unset($res2['firma']);
			foreach($res2 as $k => $v) $res[$k] = $v;
		}
		$_GET['q'] = 'Suchbegriff';
		
		$res["branchen"] = explode(";|", $res["branchen_all"]);
		
		return $res;
	}

	function get_member_by_phone($member_phone) {
		$db=&DB::connect(DB_DSN);
		
		$kunde = $db->getRow('Select * from kunden_neu WHERE aktiv = 1 and tel = '.$db->quoteSmart($member_phone), null, DB_FETCHMODE_ASSOC);
		if(!empty($kunde['id'])) {
		    $res = $db->getRow('Select *, 1 AS aktiv from t_com_firma2 WHERE kunden_id = '.$db->quoteSmart($kunde['id']), null, DB_FETCHMODE_ASSOC);
		} else {
		    $res = $db->getRow('Select *, 1 AS aktiv from t_com_firma2 WHERE tel = '.$db->quoteSmart($member_phone), null, DB_FETCHMODE_ASSOC);
		}
		checkError($res);
		if(!empty($res['kunden_id'])) {
			$kunden_id = $res['kunden_id'];
			$res2 = $db->getRow('Select * from kunden_neu WHERE id = '.$db->quoteSmart($kunden_id), null, DB_FETCHMODE_ASSOC);
			unset($res2['id']);
			unset($res2['firma']);
			foreach($res2 as $k => $v) $res[$k] = $v;
		}
		return $res;
	}

	function get_member_by_kunde($member_id) {
		$db=&DB::connect(DB_DSN);
		$res = $db->getRow('Select * from kunden_neu WHERE id = '.$db->quoteSmart($member_id), null, DB_FETCHMODE_ASSOC);
		checkError($res);
		unset($res['id']);
		unset($res['firma']);
		$res2 = $db->getRow('Select * from t_com_firma2 WHERE kunden_id = '.$db->quoteSmart($member_id), null, DB_FETCHMODE_ASSOC);
		if($res2) {
			foreach($res2 as $k => $v) $res[$k] = $v;
		}
		#pDebug($res);
		return $res;
	}

	function check_subdomain_member($id) {
		$db=&DB::connect(DB_DSN);
		$res = $db->getRow('Select * from subdomain WHERE id = '.$id, null, DB_FETCHMODE_ASSOC);
		checkError($res);
		if(!empty($res)){
			return true;
		}
		return false;
	}
	
	function check_subdomain($subdomain) {
		$db=&DB::connect(DB_DSN);
		$res = $db->getRow('Select * from subdomain WHERE subdomain = '.$db->quoteSmart($subdomain), null, DB_FETCHMODE_ASSOC);
		checkError($res);
		if(!empty($res)){
			return true;
		}
		return false;
	}
	
	function get_subdomain($id) {
		$db=&DB::connect(DB_DSN);
		$res = $db->getRow('Select * from subdomain WHERE id = '.$db->quoteSmart($id), null, DB_FETCHMODE_ASSOC);
		return $res['$res'];
	}
	
	function check_member_subdomain($subdomain) {
		$db=&DB::connect(DB_DSN);
		$res = $db->getRow('Select * from subdomain WHERE subdomain = '.$db->quoteSmart($subdomain), null, DB_FETCHMODE_ASSOC);
		checkError($res);
		if(!empty($res)){
			return $res['id'];
		}
		return false;
	}
	
	function check_member_subdomain_page($subdomain) {
		$db=&DB::connect(DB_DSN);
		$res = $db->getRow('Select * from subdomain WHERE subdomain = '.$db->quoteSmart($subdomain), null, DB_FETCHMODE_ASSOC);
		checkError($res);
		if(!empty($res)){
			$pages = $db->getRow('SELECT id,title from page WHERE kunden_id = ? AND online=1', array($res['id']));
			if(!empty($pages)) return rebuildurl(false, array('p' => 'show_page', 'page' => $pages[0]));
		}
		return false;
	}

	function get_t_com_privat($member_id){
		//$db=&DB::connect(DB_DSN);
		//$res = $db->getRow('Select * from t_com_privat WHERE id = '.$db->quoteSmart($member_id), null, DB_FETCHMODE_ASSOC);
		//checkError($res);
		//return $res;
		$DB = NewADOConnection(DB_DSN);
		$short = array('bb', 'be', 'bw', 'by', 'hb', 'he', 'hh', 'mv', 'ni', 'nw', 'rp', 'sh', 'sl', 'sn', 'st', 'th');
		$count = 0;
		for($i=0; $i <= count($short)-1; $i++) {
			$table_name = "t_com_privat_".$short[$i];
			if($i == 0) {
				$sql = "CREATE TEMPORARY TABLE temp_privat";
			} else {
				$sql = "INSERT INTO temp_privat";
			}
			$sql .= ' SELECT * from '.$table_name.' WHERE id='.$member_id;
			$a = microtime_float();
			$DB->Execute($sql);
			$b = microtime_float();
			if($DB->ErrorMsg() != "") echo $DB->ErrorMsg();
			//echo "<br> $table_name Dauer (holen): ".($b-$a);
			if($DB->Affected_Rows() > 0) break;
		}
		$res = $DB->getRow('SELECT * from temp_privat Limit 1');
		if($DB->ErrorMsg() != "") echo $DB->ErrorMsg();
		return $res;
	}

	function get_member_ort_neu($member_id) {
		$db=&DB::connect(DB_DSN);
		$res = $db->getRow('Select * from lala_ort WHERE id = '.$db->quoteSmart($member_id), null, DB_FETCHMODE_ASSOC);
		checkError($res);
		return $res;
	}

	function get_bl_by_ort($id) {
		$db=&DB::connect(DB_DSN);
		$res = $db->getRow('Select * from lala_ort WHERE oid = '.$db->quoteSmart($id), null, DB_FETCHMODE_ASSOC);
		checkError($res);
		return $res['bl'];
	}

	
	function get_member_ort($member_id) {
		$db=&DB::connect(DB_DSN);
		$res = $db->getRow('Select * from K2ORT WHERE KID = '.$db->quoteSmart($member_id), null, DB_FETCHMODE_ASSOC);
		checkError($res);
		return $res;
	}

	function get_orte($parent = -1, $with=false) {
		$like = "";
		if($with) $like = " AND name LIKE '$with%'";
		$db=&DB::connect(DB_DSN);
		$res = $db->getAssoc("SELECT id, name FROM `orte` WHERE pid = ".$db->quotesmart($parent).$like." ORDER BY name ASC");
		return (array)$res;
	}

	function get_orte_short($parent = -1, $with=false) {
		$like = "";
		if($with) $like = " AND name LIKE '$with%'";
		$db=&DB::connect(DB_DSN);
		$res = $db->getAssoc("SELECT short as id, name FROM `orte` WHERE pid = ".$db->quotesmart($parent).$like." ORDER BY name ASC");
		return (array)$res;
	}

	function get_orte_neu($parent = -1, $with=false, $json = false) {
		$like = "";
		if($with) $like = " AND name LIKE '$with%'";
		$db=&DB::connect(DB_DSN);
		//$res = $db->getAssoc("SELECT id, name FROM `orte_neu` WHERE bl = ".$db->quotesmart($parent).$like." ORDER BY name ASC");
		$res = $db->getAssoc("SELECT id, name FROM `orte_neu` WHERE bl = ".$db->quotesmart($parent).$like." ORDER BY name ASC");
		
		if($json == true) {
			foreach($res as $iKey => $sOrt) {
				$res[$iKey] = utf8_encode($sOrt);
			}
			return json_encode($res);
		}
		
		return (array)$res;
	}

	function get_staedte($parent = -1, $with=false, $json = false) {
		$like = "";
		if($with) $like = " AND name LIKE '$with%'";
		$db=&DB::connect(DB_DSN);
		$res = $db->getAssoc("SELECT id, name FROM `staedte` WHERE size > 4 and oid = ".$db->quotesmart($parent).$like." ORDER BY name ASC");
		
		#var_dump($res);
		
		if($json == true) {
			foreach($res as $iKey => $sOrt) {
				$res[$iKey] = utf8_encode($sOrt);
			}
			
			return json_encode($res);
		}
		
		return (array)$res;
	}

	function get_orte_name($id) {
		$db=&DB::connect(DB_DSN);
		$res = $db->getRow("SELECT name FROM `orte_neu` WHERE id = ".$db->quotesmart($id),null,DB_FETCHMODE_ASSOC);	
		return $res['name'];
	}

	function get_bl_name($id) {
		$db=&DB::connect(DB_DSN);
		$res = $db->getRow("SELECT name FROM `laender` WHERE id = ".$db->quotesmart($id),null,DB_FETCHMODE_ASSOC);	
		return $res['name'];
	}
	
	function get_orte_info($id) {
		$db=&DB::connect(DB_DSN);
		return $db->getRow("SELECT * FROM `orte_neu` WHERE id = ".$db->quotesmart($id));	
	}

	function set_member_laender($member_id, $laender = array()) {
		if(empty($member_id)) return false;
		$db=&DB::connect(DB_DSN);
		$res = $db->query('DELETE FROM `K2ORT` WHERE KID = ?', array($member_id));
		if(empty($laender)) {
			$_GET['error'] .= 'Bitte mindestens 1 Landkreis auswählen!<br/>';
		} else {
			foreach($laender as $p) {
				if(empty($p)) continue;
				$br = $db->getRow('select * from orte WHERE id=?', array($p), DB_FETCHMODE_ASSOC);
				if(PEAR::isERROR($br) || !$br || $br['pid'] == -1) continue;
				$data = array('KID' => $member_id, 'OID' => $p, 'BL' => $br['pid']);
				$res=$db->autoExecute('`K2ORT`', $data, DB_AUTOQUERY_INSERT);
			}
		}
	}

	function set_member_laender_neu($firma_id, $laender = array()) {
		if(empty($firma_id)) return false;
		$db=&DB::connect(DB_DSN);
		$res = $db->query('DELETE FROM `lala_ort` WHERE id = ?', array($firma_id));
		checkError($res);
		if(empty($laender)) {
			$_GET['error'] .= 'Bitte mindestens 1 Landkreis auswählen!<br/>';
		} else {
			foreach($laender as $p) {
				if(empty($p)) continue;
				// Nur Bundesland ausgewählt?
				if($p{0} == ":") {
					$p = substr($p, 1);
					$data = array('id' => $firma_id, 'oid' => -1, 'bl' => $p);
				} else {
					$br = $db->getRow('select * from orte_neu WHERE id=?', array($p), DB_FETCHMODE_ASSOC);
	
					if(PEAR::isERROR($br) || !$br || $br['bl'] == -1) continue;
					$data = array('id' => $firma_id, 'oid' => $p, 'bl' => $br['bl']);
				}
				//print_r($data);
				$res=$db->autoExecute('`lala_ort`', $data, DB_AUTOQUERY_INSERT);
				checkError($res);
			}
		}
	}

	function get_member_laender_neu($member_id) {
		$db=&DB::connect(DB_DSN);
		$res = $db->getCol("SELECT oid FROM `lala_ort` WHERE id = ".$db->quotesmart($member_id));
		if(PEAR::isERROR($res)) return array();
		return (array)$res;
	}

	function get_member_laender_parent_neu($member_id) {
		$db=&DB::connect(DB_DSN);
		$res = $db->getCol("SELECT bl FROM `lala_ort` WHERE oid=-1 and id = ".$db->quotesmart($member_id));
		if(PEAR::isERROR($res)) return array();
		return (array)$res;
	}

	function get_member_laender_parent($member_id) {
		$db=&DB::connect(DB_DSN);
		$res = $db->getCol("SELECT bl FROM `lala_ort` WHERE id = ".$db->quotesmart($member_id)." GROUP BY bl");
		if(PEAR::isERROR($res)) return array();
		return (array)$res;
	}
	
	function get_member_laender($member_id) {
		$db=&DB::connect(DB_DSN);
		$res = $db->getCol("SELECT OID FROM `K2ORT` WHERE KID = ".$db->quotesmart($member_id));
		if(PEAR::isERROR($res)) return array();
		return (array)$res;
	}

	function set_member_branchen($member_id, $branchen = array()) {
		if(empty($member_id)) return false;
		$db=&DB::connect(DB_DSN);
		$res = $db->query('DELETE FROM `branche_kunde` WHERE KID = ?', array($member_id));
		foreach($branchen as $p) {
			if(empty($p)) continue;
			$br = $db->getRow('select * from branchen WHERE id=?', array($p), DB_FETCHMODE_ASSOC);
			if(PEAR::isERROR($br) || !$br/* || $br['pid'] == -1*/) continue;
			$data = array('KID' => $member_id, 'BID' => $p, 'PID' => $br['pid']);
			$res=$db->autoExecute('`branche_kunde`', $data, DB_AUTOQUERY_INSERT);
		}
	}

	function set_member_branchen_neu($firma_id, $branchen = array()) {
		if(empty($firma_id)) return false;
		$db=&DB::connect(DB_DSN);
		$res = $db->query('DELETE FROM `lala_branche` WHERE id = ?', array($firma_id));
		if(empty($branchen)) {
			$_GET['error'] .= 'Bitte mindestens 1 Branche auswählen!<br/>';
		} else {
			$branchenNamen = array();
			foreach($branchen as $p) {
				if(empty($p)) continue;
				$br = $db->getRow('select * from branchen_neu WHERE id=?', array($p), DB_FETCHMODE_ASSOC);
				if (PEAR::isERROR($br) || !$br/* || $br['pid'] == -1*/) 
					continue;
				$branchenNamen[] = $br['name'];
				$data = array('id' => $firma_id, 'bid' => $p);
				$res=$db->autoExecute('`lala_branche`', $data, DB_AUTOQUERY_INSERT);
			}
			$branchenNamen = implode(' ', $branchenNamen);
			if ($branchenNamen)
				$res = $db->query("UPDATE t_com_firma2 SET branche='".$branchenNamen."' WHERE id='".$firma_id."'");
		}
	}

	function save_member_cache($firma_id) {
		if(empty($firma_id)) 
			return false;
		$db=&DB::connect(DB_DSN);
		$res = $db->query('DELETE FROM `cache_t_com_firma` WHERE id = ?', array($firma_id));
		$res = $db->query('INSERT INTO `cache_t_com_firma` (SELECT f1.id, b1.bid, o1.bl, o1.oid FROM t_com_firma2 AS f1 JOIN lala_branche AS b1 ON b1.id=f1.id JOIN lala_ort AS o1 ON f1.id=o1.id WHERE f1.id = ?)', array($firma_id));
	}
	
	function get_member_branchen_neu($member_id) {
		$db=&DB::connect(DB_DSN);
		$res = $db->getCol("SELECT bid FROM `lala_branche` WHERE id = ".$db->quotesmart($member_id));
		if(PEAR::isERROR($res)) return array();
		return (array)$res;
	}

	function get_member_branchen_all($member_id) {
		$db=&DB::connect(DB_DSN);
		$res = $db->getAll("SELECT bra1.id AS id, bra1.bid AS bid, bra2.name AS name FROM `lala_branche` AS bra1 LEFT JOIN branchen_neu AS bra2 ON bra1.bid=bra2.id WHERE bra1.id = ".$db->quotesmart($member_id)."ORDER BY bra1.bid ASC");
		if(PEAR::isERROR($res)) return array();
		return (array)$res;
	}
	
	function get_member_branchen($member_id) {
		$db=&DB::connect(DB_DSN);
		$res = $db->getCol("SELECT BID FROM `branche_kunde` WHERE KID = ".$db->quotesmart($member_id));
		if(PEAR::isERROR($res)) return array();
		return (array)$res;
	}

	function get_branchen_member($bid) {
		echo $bid;
		$db=&DB::connect(DB_DSN);
		$res = $db->getCol("SELECT * FROM branche_kunde as a left join kunden as b (a.KID=b.id) WHERE a.BID = ".$db->quotesmart($bid));
		if(PEAR::isERROR($res)) return array();
		return (array)$res;
	}

	function get_branche_name($id) {
		$db=&DB::connect(DB_DSN);
		$res=$db->getRow("SELECT * FROM branchen WHERE id = '".$id."'",null,DB_FETCHMODE_ASSOC);
		return $res['name'];
	}
	
	function get_branche_name_neu($id) {
		$db=&DB::connect(DB_DSN);
		$res=$db->getRow("SELECT name FROM branchen_neu WHERE id = '".$id."'",null,DB_FETCHMODE_ASSOC);
		return $res['name'];
	}

	function get_branche_name_name($name, $isMain) {
		$db=&DB::connect(DB_DSN);
		if($isMain)
			$str_isMain = ' AND pid=-1';
		else
			$str_isMain = '';
		$res=$db->getRow("SELECT name FROM branchen_neu WHERE name = '".$name."'".$str_isMain,null,DB_FETCHMODE_ASSOC);
		return $res['name'];
	}
	
	function get_branche_parent($id) {
		$db=&DB::connect(DB_DSN);
		$res=$db->getRow("SELECT pid FROM branchen_neu WHERE id = '".$id."'",null,DB_FETCHMODE_ASSOC);
		return $res['pid'];
	}
	
	function get_branchen($parent = -1, $with=false) {
		$like = "";
		if($with) $like = " AND name LIKE '$with%'";
		$db=&DB::connect(DB_DSN);
		$res = $db->getAssoc("SELECT id, name FROM `branchen` WHERE pid = ".$db->quotesmart($parent).$like." ORDER BY name ASC");
		return (array)$res;
	}

	function get_branchen_neu($parent = -1, $with = false) {
		$like = "";
		if(strtolower($with) == 'a') $with = '[aä]';
		if(strtolower($with) == 'o') $with = '[oö]';
		if(strtolower($with) == 'u') $with = '[uü]';
		if($with) $like = " AND name REGEXP '^$with'";
		$db=&DB::connect(DB_DSN);
		$res = $db->getAssoc("SELECT id, name FROM `branchen_neu` WHERE pid = ".$db->quotesmart($parent).$like." ORDER BY name COLLATE latin1_german1_ci");
		return (array)$res;
	}

	function &getAdoConnection() {
	    static $db = null;
		if($db === null) $db = &NewADOConnection(DB_DSN);
		return $db;
	}

  /**
  *   09.08.08 TD 
  *   Premiumeinträge aus Cache Tabelle lesen
  *
  *
  **/

	function db_search_premium($_search = false, $_branche = false, $_bl = false, $_ort = false, $max=20000, $_city=false, $search_text = "") {
	  
	  $db = &self::getAdoConnection();
	  //$db->debug = true;
	
	  // MySQL-Injection verhindern
	  $_search = mysql_real_escape_string($_search);
	  
	  // Unterkategorie der Branche suchen
	  $sub_branchen = $db->getCol('SELECT id from branchen_neu where pid = ?', array($_branche));
	
	  // Bei Bedarf die Premiumeintraege noch nach Firma, Ort, PLZ und/oder Strasse filtern
	  
	  //
	  // LEFT JOIN lala_branche sinnlos?
	  //
	  $sOrderColumn = "";
	  if(!empty($_search)) {
		$sOrderColumn = ", MATCH (cp.firma, cp.metawords, cp.strasse, cp.ort) AGAINST ('". $search_text ."' IN NATURAL LANGUAGE MODE) AS search_order";
	  }

		$sql = "
			SELECT 
	  			cp.id, 
	  			cp.kunden_id, 
	  			cp.firma, 
	  			cp.strasse, 
	  			cp.ort, 
	  			cp.plz, 
	  			cp.tel, 
	  			cp.fax, 
	  			cp.www, 
	  			null as branche,
	  			cp.metawords, 
	  			cp.public, 
	  			kn.eintr_art,
				IFNULL((
					SELECT
						short
					FROM produkte
					WHERE
						code = kn.eintr_art
					LIMIT 1
				), 'Basis') AS eintr_art_name,
	  			null as yextspecialofferurl,
	  			null as yextspecialoffermessage,
	  			null as yextlists,
	  			null as yexthideaddress,
	  			null as source,
				kn.logo,
				kn.firmenprofil
				{$sOrderColumn}
	  		FROM cache_premium AS cp 
	  			LEFT JOIN lala_branche AS br ON cp.id=br.id
	  			LEFT JOIN kunden_neu AS kn ON cp.kunden_id=kn.id
	  		WHERE 
				kn.aktiv=1 
				AND (
					kn.eintr_art = 'p'
					OR kn.eintr_art = 'pp'
				)
		";
	  
	  
	  if($_search != false) {
		$sql .= " AND MATCH (cp.firma, cp.metawords, cp.strasse, cp.ort) AGAINST ('\"". $search_text ."\" (".$_search.")' IN BOOLEAN MODE)";
	  }
	  
	  if($_city) {
	  	$sql .= " AND cp.ort LIKE ".$db->Quote($_city);
	  }
	  
	  if(is_numeric($_ort) && $_ort > 0) {
		  $sql .= " AND cp.oid = ".$db->Quote($_ort);
	  } elseif(is_numeric($_bl) && $_bl > 0) {
		  $sql .= " AND cp.bl = ".$db->Quote($_bl);
	  }
	  
	  if(is_numeric($_branche) && $_branche > 0) {
		  $sub_branchen[] = $_branche;
		  $val = '('.join(', ', $sub_branchen).')';        
		  $sql .= " AND br.bid IN ".$val;
	  }
	
	  $sql .= " GROUP BY kunden_id";
	  $a = microtime_float();
	  //$res = $db->Execute($sql);
	  if($db->ErrorMsg() != "") echo $db->ErrorMsg();
	  //echo "<br> gesamt Dauer: ".(microtime_float() - $a);
	  return $sql;
	}

	function get_kunden_pay($id) {
		 $db = &self::getAdoConnection();
		$res = $db->getAll("select * from kunden_pay where id = '".$id."' and pay_status=1 order by pay_create desc");
		if($db->ErrorMsg() != "") echo $db->ErrorMsg();
		$count = $db->affected_Rows();
		return $res;
	}
	
	function get_view_pay($id) {
		 $db = &self::getAdoConnection();
		$res = $db->getRow("select * from kunden_pay as a left join kunden_neu as b on (a.id=b.id) where a.pay_id = '".$id."'");
		if($db->ErrorMsg() != "") echo $db->ErrorMsg();
		$count = $db->affected_Rows();
		return $res;
	}
	
	function get_view_pay2($id) {
		$db = &DB::connect(DB_DSN);
		if (PEAR::isError($db)) {
			die($db->getMessage());
		}
				
		$res = $db->getAll("
			SELECT
				kr.*,
				kr.id AS rechnung_id,
				kr.datum AS rechungsdatum,
				krp.name,
				krp.price,
				krp.tax,
				kn.*
			FROM kunden_rechnungen AS kr
			LEFT JOIN kunden_rechnungen_pos AS krp
				ON kr.id = krp.rechnung_id
			LEFT JOIN kunden_neu AS kn
				ON kn.id = kr.kunden_id
			WHERE
				kr.id = ". $db->quoteSmart($id) ."
		", NULL, DB_FETCHMODE_ASSOC);
		if (PEAR::isError($db)) {
			die($db->getMessage());
		}
		
		$aRes = array();
		foreach($res as $aPos) {
			if(empty($aRes)) {
				$aRes = $aPos;
			}
			
			$aRes["positionen"][] = $aPos;
		}
		
		return $aRes;
	}
	
	function get_produkte() {
		$db = &self::getAdoConnection();
		$res = $db->getAll("
			SELECT 
				* 
			FROM produkte
		");
		if($db->ErrorMsg() != "") echo $db->ErrorMsg();
		
		$aProdukte = array();
		foreach($res as $aProdukt) {
			$aProdukte[$aProdukt["code"]] = $aProdukt;
		}
		
		return $aProdukte;
	}
	
	function get_kunden($page) {
		 $db = &self::getAdoConnection();
		 $limit = " LIMIT ".($page*MAX_LIST_SEARCH_OUT).",".MAX_LIST_SEARCH_OUT;
		$res = $db->getAll("
			select 
				*,
				IFNULL((
					SELECT
						short
					FROM produkte
					WHERE
						code = kunden_neu.eintr_art
					LIMIT 1
				), 'Basis') AS eintr_art_name
			from kunden_neu order by indate desc".$limit);
		//$res = $db->getAll("select * from kunden_neu order by id desc".$limit);
		//$res = $db->getAll("select * from t_com_firma2 order by id desc".$limit);
		//$resc = $db->getRow("select count(*) as c from kunden_neu");
		$resc = $db->getRow("select count(*) as c from kunden_neu");
		if($db->ErrorMsg() != "") echo $db->ErrorMsg();
		$count = $db->affected_Rows();
		return array($resc['c'], $res);
	}

	
	function db_search_tel($_tel, $limit = 20) {
		$db = &self::getAdoConnection();
		//$res = $db->getAll("select * from t_com_firma2 where tel like '%".$_tel."%' Limit ".$limit);
		// Telefonnummer normalierieren
		$_tel = preg_replace("([^0-9])", "", $_tel);
		$res = $db->getAll("select * from t_com_firma2 where tel = '".$_tel."' AND reverse='J' AND public='J' LIMIT ".$limit);
		if($db->ErrorMsg() != "") echo $db->ErrorMsg();
		$count = $db->affected_Rows();
		return array($count, $res);
	}
	
	function db_search_neu2($_search = false, $d_search = false, $_branche = false, $_bl = false, $_ort = false, $_count = 0, $page = 0, $_max = 500, $city = false) {
		// MySQL-Injection verhindern 
		$_search = mysql_real_escape_string($_search);
		$escapedSearch = $_search;
		$booleanModeEscapedSearch = mysql_real_escape_string('"'.$_search.'"');
		if($d_search) {
			foreach($d_search as $key=>$value) {
				$d_search[$key] = mysql_real_escape_string($value);
			}
		}
		
		$a1 = _pf();
		$limit = " LIMIT ".($page*MAX_LIST_SEARCH_OUT_FRONTEND).",".MAX_LIST_SEARCH_OUT_FRONTEND;
		$db = &self::getAdoConnection();
		$sql = "INSERT INTO stat_query (query, branche, bl, ort, `ip`) VALUES ('$_search', '$_branche', '$_bl', '$_ort', '".$_SERVER['REMOTE_ADDR']."')";
		$db->Execute($sql);
		
		if(!empty($_search)) {
			$sTmpSearch = str_replace(" ", "-", $_search);
			$aTemp = explode("-", $sTmpSearch);			
			foreach($aTemp as $iKey => $sTmp) {
				if(strlen($sTmp) > 3) {
					$aTemp[$iKey] = "+" . $sTmp . "*";
				} else {
					$aTemp[$iKey] = "+" . $sTmp;
				}
			}
			$_search = implode(" ", $aTemp);
			/*
			$temp = split(" ", $_search);
			$_search = "+".join("* +", $temp)."*";
			$temp = split("-", $_search);
			$_search = join("* +", $temp);
			*/
		}				
				
		$where = array();
		$data = array();
		$join = array();
		/*  09.08.08 TD
		*   Aus Performancegruenden wird die Auswahl der Premiumeintraege aus einer Extra-Tabelle erzeugt.
		*   Diese wird automatisch oder per Hand unabhaengig von der Seite per Script erzeugt.
		*/    		
		//$join['kunden_neu'] = " LEFT JOIN kunden_neu ON t_com_firma2.kunden_id = kunden_neu.id";

		// Unterkategorie der Branche suchen
		$sub_branchen = $db->getCol('SELECT id from branchen_neu where pid = ?', array($_branche));

		// hat keine Unterkategorien
		if($_branche) {
			$sub_branchen[] = $_branche;
			$where['AND'][] = 'cache_t_com_firma.bid IN {?}';	
			$data[] = $sub_branchen;
			$join['cache_t_com_firma'] = " JOIN cache_t_com_firma ON t_com_firma2.id = cache_t_com_firma.id";
		}

		if($_bl > 0) {
			$where['AND'][] = 'cache_t_com_firma.bl = {?}';	
			$data[] = $_bl;
			if(!$join['cache_t_com_firma'])
				$join['cache_t_com_firma'] = " JOIN cache_t_com_firma ON t_com_firma2.id = cache_t_com_firma.id";
					}

		// Landkreis ausgewaehlt
		if($_ort > 0) {
			$where['AND'][] = 'cache_t_com_firma.oid = {?}';	
			$data[] = $_ort;
			if(!$join['cache_t_com_firma'])
				$join['cache_t_com_firma'] = " JOIN cache_t_com_firma ON t_com_firma2.id = cache_t_com_firma.id";
		}

		$sOrderColumn = "";
		$sOrder = "";
		if(!empty($_search) && empty($d_search['plz']) && empty($d_search['ort']) && empty($d_search['str'])) {
			$where['AND'][] = " MATCH (t_com_firma2.firma, t_com_firma2.ort, t_com_firma2.strasse, t_com_firma2.branche) AGAINST ('\"".$escapedSearch."\" (".$_search.")' IN BOOLEAN MODE)";
			
			$sOrderColumn = ", MATCH (t_com_firma2.firma, t_com_firma2.ort, t_com_firma2.strasse, t_com_firma2.branche) AGAINST ('".$escapedSearch."' IN NATURAL LANGUAGE MODE) AS search_order";
			$sOrder = "ORDER BY (sub.eintr_art = 'p' OR sub.eintr_art = 'pp') DESC, sub.search_order DESC";
		} elseif(!empty($_search) && (!empty($d_search['plz']) || !empty($d_search['ort']) || !empty($d_search['str']))) {
			$where['AND'][] = " MATCH (t_com_firma2.firma, t_com_firma2.branche) AGAINST ('\"".$escapedSearch."\" (".$_search.")' IN BOOLEAN MODE)";
			if(!empty($d_search['ort'])) $where['AND'][] = " t_com_firma2.ort LIKE '".$d_search['ort']."'";
			if(!empty($d_search['ort'])) $where['AND'][] = " t_com_firma2.strasse LIKE '%".$d_search['str']."%'";
			if(!empty($d_search['plz'])) $where['AND'][] = " t_com_firma2.plz LIKE '".$d_search['plz']."'";
			
			$sOrderColumn = ", MATCH (t_com_firma2.firma, t_com_firma2.branche) AGAINST ('".$escapedSearch."' IN NATURAL LANGUAGE MODE) AS search_order";
			$sOrder = "ORDER BY (sub.eintr_art = 'p' OR sub.eintr_art = 'pp') DESC, sub.search_order DESC";
		} elseif(!empty($d_search['ort']) || !empty($d_search['plz'])) {
			if(!empty($d_search['ort'])) $where['AND'][] = " t_com_firma2.ort LIKE '".$d_search['ort']."'";
			if(!empty($d_search['ort'])) $where['AND'][] = " t_com_firma2.strasse LIKE '%".$d_search['str']."%'";
			if(!empty($d_search['plz'])) $where['AND'][] = " t_com_firma2.plz LIKE '".$d_search['plz']."'";
			#$sOrder = "ORDER BY (sub.eintr_art = 'p' OR sub.eintr_art = 'pp') DESC, sub.search_order DESC";
			$sOrder = "ORDER BY (sub.eintr_art = 'p' OR sub.eintr_art = 'pp') DESC"; 
		} else {
			$sOrder = "ORDER BY (sub.eintr_art = 'p' OR sub.eintr_art = 'pp') DESC";
		}

		// Query zusammenbauen
		$_where = AdoBuildWhere($db, $where, $data);
		if(!empty($_where)) 
			$_where = " WHERE ".$_where;
		$_join = join('', $join);

		$a = _pf();

		//$sql = "SELECT t_com_firma2.*, kunden_neu.eintr_art, kunden_neu.eintr_art = 'p' as isprem FROM t_com_firma2 ".$_join.$_where." ORDER BY isprem desc Limit ".$_max;
		// 09.08.08 TD kunden_neu entfaellt, siehe oben
		//$sql = "SELECT t_com_firma2.*, kunden_neu.eintr_art, kunden_neu.eintr_art = 'p' as isprem FROM t_com_firma2 ".$_join.$_where." Limit ".$_max;
		
		// Um die Sortierung zu umgehen, holen wir die Premiumeintraege aus einer statischen Tabelle und
		// mischen sie mit den restlichen Eintraegen per union select, da wir nur so das Pagen gewaehrleisten
		// koennen
		// ACHTUNG: die Spaltenanzahl und Benennung muss in beiden Selects des UNION identisch sein!!!!
		//			im Notfall Spalten mit NULL-Werten auffuellen. Siehe metawords
    		
		if($_GET['telefonbuch']==false && $_GET['no_public']==false) {
			if(!$_where)
				$_where_2=" WHERE t_com_firma2.public!='N'";
			else
				$_where_2=" AND t_com_firma2.public!='N'";
		}
		
		$sql = "
			SELECT
				*
			FROM ((
				" . self::db_search_premium($_search, $_branche, $_bl, $_ort, 20000, $d_search['ort'], $escapedSearch);
				
		$sql .= " 
				LIMIT 1000)
				UNION 
				SELECT 	
					t_com_firma2.id as id,
					t_com_firma2.kunden_id as kunden_id,
					t_com_firma2.firma as firma,
					t_com_firma2.strasse as strasse,
					t_com_firma2.ort as ort,
					t_com_firma2.plz as plz,
					t_com_firma2.tel as tel,
					t_com_firma2.fax as fax,
					t_com_firma2.www as www,
					t_com_firma2.branche as branche,
					null as metawords,
					t_com_firma2.public as public,
					null as eintr_art,
					IFNULL((
						SELECT
							short
						FROM produkte
						WHERE
							code = kunden_neu.eintr_art
						LIMIT 1
					), 'Basis') AS eintr_art_name,
					t_com_firma2.yextspecialofferurl as yextspecialofferurl,
					t_com_firma2.yextspecialoffermessage as yextspecialoffermessage,
					t_com_firma2.yextlists as yextlists,
					t_com_firma2.yexthideaddress as yexthideaddress,
					t_com_firma2.source as source,
					'' AS logo,
					kunden_neu.firmenprofil
					{$sOrderColumn}
				FROM t_com_firma2 
				LEFT JOIN kunden_neu
					ON kunden_neu.id = t_com_firma2.kunden_id
				".$_join.$_where.$_where_2." 
					#AND t_com_firma2.kunden_id > 0
					#AND (
					#	kunden_neu.eintr_art != 'p' AND kunden_neu.eintr_art != 'pp' 
					#)
					#OR eintr_art IS NULL
				GROUP BY t_com_firma2.id
				LIMIT 1000
			) AS sub
			GROUP BY sub.id
			{$sOrder}
			Limit ".$_max;
						
		if($_GET['debug'] == 1) echo "<br> : ".$sql;		
		
		$res = $db->Execute("CREATE TEMPORARY TABLE tmp ".$sql);
		if($db->ErrorMsg() != "") echo $db->ErrorMsg();
		$count = $db->affected_Rows();
		
		if($_GET['debug'] == 1) echo "<br> UNION: ".(_pf() - $a);
		
		#pDebug($count);
		
		/*
		$sql = "SELECT t_com_firma2.*, kunden_neu.eintr_art FROM t_com_firma2 ".$_join.$_where." and kunden_neu.eintr_art = 'p' Limit ".$_max;
		if($_GET['debug'] == 1) echo "<br> : ".$sql;
		$res = $db->Execute("CREATE TEMPORARY TABLE tmp_p ".$sql);
		if($db->ErrorMsg() != "") echo $db->ErrorMsg();
		*/

		$a = _pf();
		//$db->Execute("alter table tmp add index(eintr_art, firma)");
		if($_GET['debug'] == 1) echo "<br> creating index: ".(_pf()-$a);

		$a = _pf();

		//$sql = "SELECT DISTINCT *, eintr_art = 'p' as isprem FROM tmp ORDER BY isprem desc ".$limit;
		$sql = "SELECT DISTINCT * FROM tmp ".$limit;
		$res = $db->getAll($sql);
		if($db->ErrorMsg() != "") echo $db->ErrorMsg();

		/*
		$sql = "SELECT DISTINCT * FROM tmp_p ".$limit;
		$res_p = $db->getAll($sql);
		if($db->ErrorMsg() != "") echo $db->ErrorMsg();
		*/
		
		if($_GET['debug'] == 1) echo "<br> Dauer (holen): ".(_pf() - $a);
		if($_GET['debug'] == 1) echo "<br> gesamt Dauer: ".(_pf() - $a1);
		//$res=array_merge($res_p,$res);

		return array($count, $res);
	}

/*
	function db_search_neu($_search = false, $_branche = false, $_bl = false, $_ort = false, $_count = 0, $page = 0, $_max = 10000) {
		if(!empty($_search)) {
			$temp = split(" ", $_search);
			$_search = "+".join(" +", $temp);
		}
		$a1 = microtime_float();
		$limit = " LIMIT ".($page*MAX_LIST_SEARCH_OUT).",".MAX_LIST_SEARCH_OUT;
		$db = NewADOConnection(DB_DSN);

		$a = microtime_float();
		// hat keine Unterkategorien
		if($_branche) {
			// Unterkategorie der Branche suchen
			$sub_branchen = $db->getCol('SELECT id from branchen_neu where pid = ?', array($_branche));
			$sub_branchen[] = $_branche;
			if(!is_array($sub_branchen) || count($sub_branchen) <= 0) $sub_branchen[] = "-1";
			$str = "(".join(", ", $sub_branchen).")";
			$db->Execute("create temporary table m1 select id from lala_branche where bid in ".$str);
			$db->Execute("alter table m1 add index(id)");
		}
		if($_GET['debug'] == 1) echo "<br> T1: ".(microtime_float()-$a);

		$a = microtime_float();
		// Ort (LK) ausgewÃ¤hlt
		if($_ort > 0) {
			$db->Execute("create temporary table m2 select id from lala_ort where oid = ?", array($_ort));
			$db->Execute("alter table m2 add index(id)");
		} else {
			$db->Execute("create temporary table m2 select id from lala_ort where bl = ?", array($_bl));
			$db->Execute("alter table m2 add index(id)");
		}
		if($_GET['debug'] == 1) echo "<br> T2: ".(microtime_float()-$a);

		$a = microtime_float();
		if(!empty($_search)) {
			$db->Execute("create temporary table m3 select id from t_com_firma2 where MATCH (t_com_firma2.firma, t_com_firma2.ort, t_com_firma2.strasse, t_com_firma2.branche) AGAINST ('".$_search."' IN BOOLEAN MODE)");
			if($db->ErrorMsg() != "") echo $db->ErrorMsg();
			$db->Execute("alter table m3 add index(id)");
			$db->Execute("create temporary table m select distinct m1.id from m1 join m2 join m3 on (m1.id = m2.id and m1.id = m3.id and m2.id = m3.id)");
			if($db->ErrorMsg() != "") echo $db->ErrorMsg();
		} else {
			$db->Execute("create temporary table m select distinct m1.id from m1 join m2 on m1.id = m2.id");
			if($db->ErrorMsg() != "") echo $db->ErrorMsg();
		}
		$count = $db->affected_Rows();
		$db->Execute("alter table m add index(id)");
		if($_GET['debug'] == 1) echo "<br> T3: ".(microtime_float()-$a);

		$a = microtime_float();
		$sql = "SELECT t_com_firma2.*, kunden_neu.eintr_art, kunden_neu.eintr_art = 'p' as isprem FROM t_com_firma2 left join kunden_neu on t_com_firma2.kunden_id = kunden_neu.id join m on t_com_firma2.id = m.id ORDER BY isprem desc ".$limit;
		if($_GET['debug'] == 1) echo "<br> : ".$sql;
		$res = $db->getAll($sql);
		if($db->ErrorMsg() != "") echo $db->ErrorMsg();

		if($_GET['debug'] == 1) echo "<br> Dauer (holen): ".(microtime_float()-$a);

		if($_GET['debug'] == 1) echo "<br> Gesamt: ".(microtime_float() - $a1);
		return array($count, $res);
	}
*/
	function db_fulltext($_search = false, $_branche = false, $_bl = false, $_ort = false, $_count = 0, $page = 0) {
		$temp = split(" ", $_search);
		$_search = "+".join(" +", $temp);

		$a1 = microtime_float();
		$limit = " LIMIT ".($page*MAX_LIST_SEARCH_OUT).",".MAX_LIST_SEARCH_OUT;
		$db=&DB::connect(DB_DSN);

		$where = array();
		$data = array();
		$join = array();

		$strSQL = "SELECT id from kunden_neu WHERE kunden_neu.aktiv = 1 AND MATCH (kunden_neu.firma, kunden_neu.app_name, kunden_neu.ort, kunden_neu.strasse, kunden_neu.metawords) AGAINST ('".$_search."' IN BOOLEAN MODE)";
		$db->query("CREATE TEMPORARY TABLE blurb ".$strSQL." ORDER BY id ASC");

		$_test = $db->getOne("SELECT * from blurb Limit 1");
		if(!PEAR::isERROR($_test)) {
			// Unterkategorie der Branche suchen
			$sub_branchen = $db->getCol('SELECT id from branchen where pid = '.$db->quoteSmart($_branche));

			if($_bl > 0) {
				$where['AND'][] = 'K2ORT.BL = {?}';	$data[] = $_bl;
				$join['K2ORT'] = " LEFT JOIN K2ORT ON K2ORT.KID = blurb.id";
			}
			// Ort (LK) ausgewÃ¤hlt
			if($_ort > 0) {
				$where['AND'][] = 'K2ORT.OID = {?}';	$data[] = $_ort;
				$join['K2ORT'] = " LEFT JOIN K2ORT ON K2ORT.KID = blurb.id";
			}

			// hat keine Unterkategorien
			if($_branche) {
				$sub_branchen[] = $_branche;
				$where['AND'][] = 'branche_kunde.BID IN {?}';	$data[] = $sub_branchen;
				$join['branche_kunde'] = " LEFT JOIN branche_kunde ON branche_kunde.KID = blurb.id";
			}

			// Query zusammenbauen
			$_where = buildWhere($db, $where, $data);
			//echo "<br> init: ".(microtime_float() - $a1);
			if(empty($_where)) $_where = "1";
			$a2 = microtime_float();
			$_where = " WHERE ".$_where;
			$_join = " ".join('', $join);
			$strSQL = "SELECT DISTINCT id FROM blurb".$_join.$_where;
			$db->query("CREATE TEMPORARY TABLE blurb2 ".$strSQL." ORDER BY blurb.id ASC");
			// anzahl der datensätze gesamt, so sparen wir uns das zählen später
			$_count = $db->affectedRows();
			//echo "<br> nach branche ort finden: ".(microtime_float() - $a2);
		}


		$_test = $db->getOne("SELECT * from blurb2 Limit 1");
		if(!PEAR::isERROR($_test)) {
			if($_count <= 0) {
				//echo "<br>zähle: ";
				$a = microtime_float();
				$count = $db->getOne("SELECT count(*) FROM blurb2 LEFT JOIN `kunden_neu` ON blurb2.id = kunden_neu.id");
				//echo "<br> Dauer (zählen): ".(microtime_float()-$a);
			} else {
				$count = $_count;
			}

			//echo "<br> anz: ".$db->getOne("SELECT count(*) from blurb");
			$a = microtime_float();
			$sql = "SELECT kunden_neu.* FROM blurb2 LEFT JOIN `kunden_neu` ON blurb2.id = kunden_neu.id ORDER BY eintr_art asc,kunden_neu.firma ASC".$limit;
			$res = $db->getAll($sql, null, DB_FETCHMODE_ASSOC);
			if(PEAR::isError($res)) print_r($res);
			//echo "<br> Dauer (holen): ".(microtime_float()-$a);
			return array($count, $res);
		}
	}
	
	function db_telefonbuch_admin($_q = false, $_bl = false, $_str = false, $_plz = false, $_ort = false, $_refid = false,  $page = 0) {
		$_debug = false;
		// Debug Modus
		// ein ':' vor dem Suchbegriff startet den Debugmodus
		// Bsp: ':franke'
		if(substr($_q, 0, 1) == ":") {
			$_debug = true;
			$_q = substr($_q, 1);
			for($i=1;$i<=5;$i++) {
				$c = rand(0,strlen($_q)-1);
				$_q{$c} = strtoupper($_q{$c});
			}
		}
		if(!defined("MAX_LIST_SEARCH_OUT")) define("MAX_LIST_SEARCH_OUT", 25);

		$DB = NewADOConnection(DB_DSN);
		$short = array('bb', 'be', 'bw', 'by', 'hb', 'he', 'hh', 'mv', 'ni', 'nw', 'rp', 'sh', 'sl', 'sn', 'st', 'th');
		// UND Suche
		$temp = split(" ", $_q);
		$_q = "+".join(" +", $temp);
		$limit = " LIMIT ".($page * MAX_LIST_SEARCH_OUT).", ".MAX_LIST_SEARCH_OUT;

		if(!empty($_bl)) $short = array($_bl);
		$x = microtime_float();
		$count = 0;
		for($i = 0; $i <= count($short) - 1; $i++) {
			$table_name = "t_com_privat_".$short[$i];
			if($i == 0) {
				$sql = "CREATE TEMPORARY TABLE temp_privat";
			} else {
				$sql = "INSERT INTO temp_privat";
			}

			$where = array();

			if(!empty($_plz)) {
				$where[] = "plz = '".$_plz."'";
			}
			if(!empty($_ort)) {
				$where[] = "ort = '".$_ort."'";
			}
			if(!empty($_str)) {
				$where[] = "strasse Like '".$_str."%'";
			}
			if(!empty($_refid)) {
				$where[] = "refid = '".$_refid."'";
			} else {
				$where[] = 'match (firma) against (\''.$_q.'\' IN BOOLEAN MODE)';
			}
			// Query zusammenbauen
			$_where = join(' AND ', $where);
			if(!empty($_where)) $_where = " WHERE ".$_where;
			
			$sql .= ' SELECT * from '.$table_name.$_where.' Limit 10000';
			$a = microtime_float();
			$DB->Execute($sql);
			if($_debug == 1) echo "<br> $sql : ".(microtime_float()-$a);
			$count += $DB->Affected_Rows();
			if($DB->ErrorMsg() != "") echo $DB->ErrorMsg();
			if($count >= 10000) break;
		}
		/*
		$where = array();

		if(!empty($_plz)) {
		$where[] = "plz = '".$_plz."'";
		}
		if(!empty($_ort)) {
		$where[] = "ort = '".$_ort."'";
		}
		if(!empty($_str)) {
		$where[] = "strasse Like '".$_str."%'";
		}
		// Query zusammenbauen
		$_where = join(' AND ', $where);
		if(!empty($_where)) $_where = " WHERE ".$_where;
		*/
		$a = microtime_float();
		$sql = 'SELECT SQL_CALC_FOUND_ROWS * from temp_privat ORDER BY firma'.$limit;
		$res = $DB->getAll($sql);
		if($DB->ErrorMsg() != "") echo $DB->ErrorMsg();
		if($_debug == 1) echo "<br> $sql : ".(microtime_float()-$a);
		$count = $DB->getOne('select found_rows()');
		if($DB->ErrorMsg() != "") echo $DB->ErrorMsg();
		if($_debug == 1) echo "<br> Gesamt: ".(microtime_float()-$x);
		return array($count, $res);
	}

	function db_telefonbuch($_q = false, $_bl = false, $_str = false, $_plz = false, $_ort = false, $page = 0) {
		// MySQL Injektion verhindern
		$_q = mysql_real_escape_string($_q);
		$_bl = mysql_real_escape_string($_bl);
		$_str = mysql_real_escape_string($_str);
		$_plz = mysql_real_escape_string($_plz);
		$_ort = mysql_real_escape_string($_ort);
		
		$_debug = $_GET['debug'];
		// Debug Modus
		// ein ':' vor dem Suchbegriff startet den Debugmodus
		// Bsp: ':franke'
		if(substr($_q, 0, 1) == ":") {
			$_debug = true;
			$_q = substr($_q, 1);
			for($i=1;$i<=5;$i++) {
				$c = rand(0,strlen($_q)-1);
				$_q{$c} = strtoupper($_q{$c});
			}
		}
		if(!defined("MAX_LIST_SEARCH_OUT")) define("MAX_LIST_SEARCH_OUT", 25);

		$DB = NewADOConnection(DB_DSN);
		$short = array('bb', 'be', 'bw', 'by', 'hb', 'he', 'hh', 'mv', 'ni', 'nw', 'rp', 'sh', 'sl', 'sn', 'st', 'th');
		// UND Suche
		$temp = split(" ", $_q);
		$_q = "+".join(" +", $temp);
		$limit = " LIMIT ".($page * MAX_LIST_SEARCH_OUT).", ".MAX_LIST_SEARCH_OUT;

		if(!empty($_bl)) $short = array($_bl);
		$x = microtime_float();
		$count = 0;
		for($i = 0; $i <= count($short) - 1; $i++) {
			$table_name = "t_com_privat_".$short[$i];
			if($i == 0) {
				$sql = "CREATE TEMPORARY TABLE temp_privat";
			} else {
				$sql = "INSERT INTO temp_privat";
			}

			$where = array();

			if(!empty($_plz)) {
				$where[] = "plz = '".$_plz."'";
			}
			if(!empty($_ort)) {
				$where[] = "ort = '".$_ort."'";
			}
			if(!empty($_str)) {
				$where[] = "strasse Like '".$_str."%'";
			}

			$where[] = 'match (firma) against (\''.$_q.'\' IN BOOLEAN MODE)';

			// Query zusammenbauen
			$_where = join(' AND ', $where);
			if(!empty($_where)) $_where = " WHERE ".$_where;

			$sql .= ' SELECT * from '.$table_name.$_where.' Limit 10000';
			$a = microtime_float();
			$DB->Execute($sql);
			if($_debug == 1) echo "<br> $sql : ".(microtime_float()-$a);
			$count += $DB->Affected_Rows();
			if($DB->ErrorMsg() != "") echo $DB->ErrorMsg();
			if($count >= 10000) break;
		}
		/*
		$where = array();

		if(!empty($_plz)) {
		$where[] = "plz = '".$_plz."'";
		}
		if(!empty($_ort)) {
		$where[] = "ort = '".$_ort."'";
		}
		if(!empty($_str)) {
		$where[] = "strasse Like '".$_str."%'";
		}
		// Query zusammenbauen
		$_where = join(' AND ', $where);
		if(!empty($_where)) $_where = " WHERE ".$_where;
		*/
		$a = microtime_float();
		$sql = 'SELECT SQL_CALC_FOUND_ROWS * from temp_privat ORDER BY firma'.$limit;
		$res = $DB->getAll($sql);
		if($DB->ErrorMsg() != "") echo $DB->ErrorMsg();
		if($_debug == 1) echo "<br> $sql : ".(microtime_float()-$a);
		$count = $DB->getOne('select found_rows()');
		if($DB->ErrorMsg() != "") echo $DB->ErrorMsg();
		if($_debug == 1) echo "<br> Gesamt: ".(microtime_float()-$x);
		return array($count, $res);
	}

	function db_telefonbuch2($_q = false, $_bl = false, $_str = false, $_plz = false, $_ort = false, $page = 0) {
		if(!defined("MAX_LIST_SEARCH_OUT")) define("MAX_LIST_SEARCH_OUT", 25);

		$DB = NewADOConnection(DB_DSN);
		$short = array('bb', 'be', 'bw', 'by', 'hb', 'he', 'hh', 'mv', 'ni', 'nw', 'rp', 'sh', 'sl', 'sn', 'st', 'th');
		// UND Suche
		$temp = split(" ", $_q);
		$_q = "+".join(" +", $temp);

		$limit = " LIMIT ".($page * MAX_LIST_SEARCH_OUT).", ".MAX_LIST_SEARCH_OUT;
		$where = array();
		$where[] = 'match (firma, strasse, ort) against (\''.$_q.'\' IN BOOLEAN MODE)';
		if(!empty($_plz)) {
			$where[] = "plz = '".$_plz."'";
		}
		if(!empty($_ort)) {
			$where[] = "ort = '".$_ort."'";
		}
		if(!empty($_str)) {
			$where[] = "strasse Like '".$_str."%'";
		}
		// Query zusammenbauen
		$_where = join(' AND ', $where);
		if(!empty($_where)) $_where = " WHERE ".$_where;

		if(!empty($_bl)) {
			$table_name .= "t_com_privat_".$_bl;
			$res = $DB->getAll('SELECT SQL_CALC_FOUND_ROWS * from '.$table_name.$_where.$limit);
			$count = $DB->getOne('SELECT found_rows()');
		} else {
			$count = 0;
			$sql = array();
			for($i = 0; $i <= count($short) - 1; $i++) {
				$table_name = "t_com_privat_".$short[$i];
				if($i == 0) {
					$sql[] = '(SELECT SQL_CALC_FOUND_ROWS * from '.$table_name.$_where.')';
				} else {
					$sql[] = '(SELECT * from '.$table_name.$_where.')';
				}
			}
			$sql = join(' UNION ALL ', $sql)." ORDER BY firma ASC".$limit;
			if($_GET['debug'] == 1) echo "<br>".$sql;
			$a = microtime_float();
			$res = $DB->getAll($sql);
			if($_GET['debug'] == 1) echo "<br> Dauer (holen): ".(microtime_float()-$a);
			$count = $DB->getOne('SELECT found_rows()');
			if($DB->ErrorMsg() != "") echo $DB->ErrorMsg();
		}
		return array($count, $res);
	}


	####################################
	# Admin
	####################################

	####################################
	# Übersicht
	####################################

	function get_admin_overview_private($where=""){
		$db=&DB::connect(DB_DSN);
		$res=$db->getRow("select count(*) as c from t_com_privat2 ".$where,null,DB_FETCHMODE_ASSOC);
		return $res['c'];
	}

	function get_admin_overview_branchen($where=""){
		$db=&DB::connect(DB_DSN);
		$res=$db->getRow("select count(*) as c from kunden_neu ".$where,null,DB_FETCHMODE_ASSOC);
		return $res['c'];
	}

	function get_admin_overview_affili($where=""){
		$db=&DB::connect(DB_DSN);
		$res=$db->getRow("select count(*) as c from werbung ".$where,null,DB_FETCHMODE_ASSOC);
		return $res['c'];
	}
	
	####################################
	# Private
	####################################

	function del_admin_private($id){
		$db=&DB::connect(DB_DSN);
		
		$short = array('bb', 'be', 'bw', 'by', 'hb', 'he', 'hh', 'mv', 'ni', 'nw', 'rp', 'sh', 'sl', 'sn', 'st', 'th');
		for($i = 0; $i <= count($short) - 1; $i++) {
			$table_name = "t_com_privat_".$short[$i];
			$db->query("delete from ".$table_name." where id='".$id."'");
		}
	}

	
	####################################
	# Branche
	####################################

	function del_admin_branche($id){
		
		$db=&DB::connect(DB_DSN);
		
		if(substr($id,0,1)=='p'){
			$id = substr($id,1);
			
			
			$res = $db->getRow('Select * from t_com_firma2 WHERE kunden_id = '.$db->quoteSmart($id), null, DB_FETCHMODE_ASSOC);
			
			if(!empty($res['id'])){
				$db->query("delete from t_com_firma2 where id='".$res['id']."'");
				$db->query("delete from kunden_neu where id='".$id."'");
				$db->query("delete from subdomain where id='".$id."'");
				$db->query("delete from cache_premium where kunden_id='".$id."'");
			} else {
				
				
				
				$db->query("delete from kunden_neu where id='".$id."'");
				$db->query("delete from subdomain where id='".$id."'");
				$db->query("delete from cache_premium where kunden_id='".$id."'");
			}
		} else {
			$res = $db->getRow('Select * from t_com_firma2 WHERE id = '.$db->quoteSmart($id), null, DB_FETCHMODE_ASSOC);
			if(!empty($res['kunden_id'])){
				$db->query("delete from t_com_firma2 where id='".$id."'");
				$db->query("delete from kunden_neu where id='".$res['kunden_id']."'");
				$db->query("delete from subdomain where id='".$res['kunden_id']."'");
				$db->query("delete from cache_premium where kunden_id='".$res['kunden_id']."'");
			} else {
				$db->query("delete from t_com_firma2 where id='".$id."'");
			}
		}
	}
	
	function del_admin_member($id){
		$db=&DB::connect(DB_DSN);
		$db->query("delete from t_com_firma2 where kunden_id='".$id."'");
		$db->query("delete from kunden_neu where id='".$id."'");
		$db->query("delete from subdomain where id='".$id."'");
		$db->query("delete from cache_premium where id='".$id."'");
	}

	function next_branche_id(){
		$db=&DB::connect(DB_DSN);
		$res=$db->getRow("select max(id) as id from kunden",null,DB_FETCHMODE_ASSOC);
		return $res['id']+1;
	}

	function store_admin_branche($data,$new=true){
		$table="kunden";
		$db=&DB::connect(DB_DSN);
		$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
		foreach ($data as $field => $value) {
			if(isset($table_field['order'][$field])){
				$fields_values[$field]=stripslashes($value);
			}
		}
		if(!empty($fields_values)){
			if($new==true){
				$res=$db->autoExecute($table, $fields_values,DB_AUTOQUERY_INSERT);
			} else {
				$res=$db->autoExecute($table, $fields_values,DB_AUTOQUERY_UPDATE, "id = '".$data['id']."'");

			}
		}
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
		if(isset($table_field['order']['sort_field'])){
			$this->UpdateSortFields(array('table'=>$table,'field'=>'mailtpl_id'));
		}
		return $res;
	}

	function get_admin_branche($id=""){
		$table="kunden";
		$db=&DB::connect(DB_DSN);
		$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
		if(empty($id)){
			if(!empty($_GET['sort_field']) && !empty($_GET['sort'])){
				$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
				if(in_array($_GET['sort_field'],$table_field['order'])){
					$order_by=" order by ".$_GET['sort_field']." ".$_GET['sort'];
				}
			} else {
				if(isset($table_field['order']['sort_field'])){
					$order_by=" order by sort_field asc";
				} else {
					$order_by=" order by id desc";
				}
			}
			return $db->getALL("select * from ".$table.$order_by." limit 0,10",null,DB_FETCHMODE_ASSOC);
		} else {
			return $db->getRow("select * from ".$table." where id='".$id."'",null,DB_FETCHMODE_ASSOC);
		}
	}

	function search_admin_branche($q){
		//echo "benutze search_admin_branche";
		$table="kunden";
		$db=&DB::connect(DB_DSN);
		$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
		if(!empty($_GET['sort_field']) && !empty($_GET['sort'])){
			$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
			if(in_array($_GET['sort_field'],$table_field['order'])){
				$order_by=" order by ".$_GET['sort_field']." ".$_GET['sort'];
			}
		} else {
			if(isset($table_field['order']['sort_field'])){
				$order_by=" order by sort_field asc";
			} else {
				$order_by=" order by id desc";
			}
		}
		return $db->getALL("select * from ".$table." where (id = '".$q."' or firma like '%".$q."%' or ort like '%".$q."%' or plz like '%".$q."%')".$order_by,null,DB_FETCHMODE_ASSOC);
	}

	####################################
	# Werbung
	####################################

	function del_admin_affili($id){
		$db=&DB::connect(DB_DSN);
		return $db->query("delete from werbung where affili_id='".$id."'");
	}

	function next_affili_id(){
		$db=&DB::connect(DB_DSN);
		$res=$db->getRow("select max(affili_id) as id from werbung",null,DB_FETCHMODE_ASSOC);
		return $res['id']+1;
	}

	function store_admin_affili($data,$new=true){
		$table="werbung";
		$db=&DB::connect(DB_DSN);
		$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
		foreach ($data as $field => $value) {
			if(isset($table_field['order'][$field])){
				$fields_values[$field]=stripslashes($value);
			}
		}
		if(!empty($fields_values)){
			if($new==true){
				$res=$db->autoExecute($table, $fields_values,DB_AUTOQUERY_INSERT);
			} else {
				$res=$db->autoExecute($table, $fields_values,DB_AUTOQUERY_UPDATE, "affili_id = '".$data['affili_id']."'");

			}
		}
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
		if(isset($table_field['order']['sort_field'])){
			$this->UpdateSortFields(array('table'=>$table,'field'=>'affili_id'));
		}
		return $res;
	}

	function get_admin_affili($id=""){
		$table="werbung";
		$db=&DB::connect(DB_DSN);
		$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
		if(empty($id)){
			if(!empty($_GET['sort_field']) && !empty($_GET['sort'])){
				$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
				if(in_array($_GET['sort_field'],$table_field['order'])){
					$order_by=" order by ".$_GET['sort_field']." ".$_GET['sort'];
				}
			} else {
				if(isset($table_field['order']['sort_field'])){
					$order_by=" order by sort_field asc";
				} else {
					$order_by=" order by affili_id desc";
				}
			}
			return $db->getALL("select * from ".$table.$order_by,null,DB_FETCHMODE_ASSOC);
		} else {
			return $db->getRow("select * from ".$table." where affili_id='".$id."'",null,DB_FETCHMODE_ASSOC);
		}
	}

	function search_admin_affili($q){
		$table="werbung";
		$db=&DB::connect(DB_DSN);
		$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
		if(!empty($_GET['sort_field']) && !empty($_GET['sort'])){
			$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
			if(in_array($_GET['sort_field'],$table_field['order'])){
				$order_by=" order by ".$_GET['sort_field']." ".$_GET['sort'];
			}
		} else {
			if(isset($table_field['order']['sort_field'])){
				$order_by=" order by sort_field asc";
			} else {
				$order_by=" order by affili_id desc";
			}
		}
		return $db->getALL("select * from ".$table." where (affili_name = '".$q."' or affili_text like '%".$q."%' or affili_url like '%".$q."%')".$order_by,null,DB_FETCHMODE_ASSOC);
	}

	####################################
	# Rechnungen
	####################################

	function del_admin_pay($id){
		$db=&DB::connect(DB_DSN);
		return $db->query("delete from kunden_pay where pay_id='".$id."'");
	}

	function next_pay_id(){
		$db=&DB::connect(DB_DSN);
		$res=$db->getRow("select max(pay_id) as id from kunden_pay",null,DB_FETCHMODE_ASSOC);
		return $res['id']+1;
	}

	function store_admin_pay($data,$new=true){
		$table="kunden_pay";
		$db=&DB::connect(DB_DSN);
		$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
		foreach ($data as $field => $value) {
			if(isset($table_field['order'][$field])){
				$fields_values[$field]=stripslashes($value);
			}
		}
		if(!empty($fields_values)){
			if($new==true){
				$res=$db->autoExecute($table, $fields_values,DB_AUTOQUERY_INSERT);
			} else {
				$res=$db->autoExecute($table, $fields_values,DB_AUTOQUERY_UPDATE, "pay_id = '".$data['pay_id']."'");

			}
		}
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
		if(isset($table_field['order']['sort_field'])){
			$this->UpdateSortFields(array('table'=>$table,'field'=>'pay_id'));
		}
		return $res;
	}

	function get_admin_pay($id=""){
		$table="kunden_pay";
		$db=&DB::connect(DB_DSN);
		$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
		if(empty($id)){
			if(!empty($_GET['sort_field']) && !empty($_GET['sort'])){
				$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
				if(in_array($_GET['sort_field'],$table_field['order'])){
					$order_by=" order by ".$_GET['sort_field']." ".$_GET['sort'];
				}
			} else {
				if(isset($table_field['order']['sort_field'])){
					$order_by=" order by a.sort_field asc";
				} else {
					$order_by=" order by a.pay_id desc";
				}
			}
			return $db->getALL("select * from ".$table." as a left join kunden_neu as b on (a.id=b.id) ".$order_by,null,DB_FETCHMODE_ASSOC);
		} else {
			return $db->getRow("select * from ".$table." where pay_id='".$id."'",null,DB_FETCHMODE_ASSOC);
		}
	}

	function search_admin_pay($q){
		$table="kunden_pay";
		$db=&DB::connect(DB_DSN);
		$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
		if(!empty($_GET['sort_field']) && !empty($_GET['sort'])){
			$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
			if(in_array($_GET['sort_field'],$table_field['order'])){
				$order_by=" order by ".$_GET['sort_field']." ".$_GET['sort'];
			}
		} else {
			if(isset($table_field['order']['sort_field'])){
				$order_by=" order by sort_field asc";
			} else {
				$order_by=" order by pay_id desc";
			}
		}
		return $db->getALL("select * from ".$table." where (id = '".$q."' or info like '%".$q."%')".$order_by,null,DB_FETCHMODE_ASSOC);
	}
	
	####################################
	# CMS
	####################################

	function get_cms($id,$status=false){
		$db=&DB::connect(DB_DSN);
		$q="select * from cms where cms_id='".$id."'";
		if($status==false){
			$q.=" and (cms_status=1 or cms_status=2)";
		}
		return $db->getRow($q,null,DB_FETCHMODE_ASSOC);
	}

	function get_cms_history($id){
		$db=&DB::connect(DB_DSN);
		$q="select * from cms_history where cms_id='".$id."' order by cms_create desc";
		return $db->getAll($q,null,DB_FETCHMODE_ASSOC);
	}

	function get_cms_history_by_id($id){
		$db=&DB::connect(DB_DSN);
		$q="select * from cms_history where cms_history_id='".$id."'";
		return $db->getRow($q,null,DB_FETCHMODE_ASSOC);
	}

	function get_last_cms_history($id){
		$db=&DB::connect(DB_DSN);
		$q="select * from cms_history where cms_id='".$id."' order by cms_create desc limit 0,1";
		return $db->getRow($q,null,DB_FETCHMODE_ASSOC);
	}

	function get_admin_cms($category,$id=""){
		$table="cms";
		$db=&DB::connect(DB_DSN);
		$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
		if(empty($id)){
			if(!empty($_GET['sort_field']) && !empty($_GET['sort'])){
				$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
				if(in_array($_GET['sort_field'],$table_field['order'])){
					$order_by=" order by ".$_GET['sort_field']." ".$_GET['sort'];
				}
			} else {
				if(isset($table_field['order']['sort_field'])){
					$order_by=" order by sort_field asc";
				} else {
					$order_by=" order by cms_create desc";
				}
			}
			if(!empty($category)){
				$where=" where cms_category='".$category."'";
			}
			return $db->getALL("select * from ".$table.$where.$order_by,null,DB_FETCHMODE_ASSOC);
		} else {
			return $db->getRow("select * from ".$table." where cms_id='".$id."'",null,DB_FETCHMODE_ASSOC);
		}
	}

	function search_admin_cms($q){
		$table="cms";
		$db=&DB::connect(DB_DSN);
		$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
		if(!empty($_GET['sort_field']) && !empty($_GET['sort'])){
			$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
			if(in_array($_GET['sort_field'],$table_field['order'])){
				$order_by=" order by ".$_GET['sort_field']." ".$_GET['sort'];
			}
		} else {
			if(isset($table_field['order']['sort_field'])){
				$order_by=" order by sort_field asc";
			} else {
				$order_by=" order by cms_create desc";
			}
		}
		return $db->getALL("select * from ".$table." where (cms_name like '%".$q."%' or cms_content like '%".$q."%')".$order_by,null,DB_FETCHMODE_ASSOC);
	}

	function next_cms_id(){
		$db=&DB::connect(DB_DSN);
		$res=$db->getRow("select max(cms_id) as id from cms",null,DB_FETCHMODE_ASSOC);
		return $res['id']+1;
	}

	function store_admin_cms_history($data,$new=true){
		$table="cms_history";
		$db=&DB::connect(DB_DSN);
		$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
		foreach ($data as $field => $value) {
			if(isset($table_field['order'][$field])){
				$fields_values[$field]=addslashes($value);
			}
		}
		if(!empty($fields_values)){
			if($new==true){
				$res=$db->autoExecute($table, $fields_values,DB_AUTOQUERY_INSERT);
			} else {
				$res=$db->autoExecute($table, $fields_values,DB_AUTOQUERY_UPDATE, "cms_history_id = '".$data['cms_history_id']."'");

			}
		}
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
		if(isset($table_field['order']['sort_field'])){
			$this->UpdateSortFields(array('table'=>$table,'field'=>'admin_id'));
		}
		return $res;
	}

	function store_kunden_neu_import($data){
		$table="kunden_neu_import";
		$db=&DB::connect(DB_DSN);
		$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
		foreach ($data as $field => $value) {
			if(isset($table_field['order'][$field])){
				$fields_values[$field]=addslashes($value);
			}
		}
		if(!empty($fields_values)){
			$res=$db->autoExecute($table, $fields_values,DB_AUTOQUERY_INSERT);
		}
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
		return $res;
	}

	
	function store_admin_cms($data,$new=true){
		$table="cms";
		$db=&DB::connect(DB_DSN);
		$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
		foreach ($data as $field => $value) {
			if(isset($table_field['order'][$field])){
				$fields_values[$field]=addslashes($value);
			}
		}
		if(!empty($fields_values)){
			if($new==true){
				$res=$db->autoExecute($table, $fields_values,DB_AUTOQUERY_INSERT);
			} else {
				$res=$db->autoExecute($table, $fields_values,DB_AUTOQUERY_UPDATE, "cms_id = '".$data['cms_id']."'");

			}
		}
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
		if(isset($table_field['order']['sort_field'])){
			$this->UpdateSortFields(array('table'=>$table,'field'=>'admin_id'));
		}
		return $res;
	}

	function del_admin_cms($id){
		$db=&DB::connect(DB_DSN);
		return $db->query("delete from cms where cms_id='".$id."'");
	}

	####################################
	# Admin User
	####################################

	function get_admin_user($id=""){
		$table="admin_user";
		$db=&DB::connect(DB_DSN);
		$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
		if(empty($id)){
			if(!empty($_GET['sort_field']) && !empty($_GET['sort'])){
				$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
				if(in_array($_GET['sort_field'],$table_field['order'])){
					$order_by=" order by ".$_GET['sort_field']." ".$_GET['sort'];
				}
			} else {
				if(isset($table_field['order']['sort_field'])){
					$order_by=" order by sort_field asc";
				} else {
					$order_by=" order by admin_create desc";
				}
			}
			return $db->getALL("select * from ".$table.$order_by,null,DB_FETCHMODE_ASSOC);
		} else {
			return $db->getRow("select * from ".$table." where admin_id='".$id."'",null,DB_FETCHMODE_ASSOC);
		}
	}

	function search_admin_user($q){
		$table="admin_user";
		$db=&DB::connect(DB_DSN);
		$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
		if(!empty($_GET['sort_field']) && !empty($_GET['sort'])){
			$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
			if(in_array($_GET['sort_field'],$table_field['order'])){
				$order_by=" order by ".$_GET['sort_field']." ".$_GET['sort'];
			}
		} else {
			if(isset($table_field['order']['sort_field'])){
				$order_by=" order by sort_field asc";
			} else {
				$order_by=" order by admin_create desc";
			}
		}
		return $db->getALL("select * from ".$table." where (admin_name like '%".$q."%' or admin_firstname like '%".$q."%' or admin_email like '%".$q."%')".$order_by,null,DB_FETCHMODE_ASSOC);
	}

	function store_admin_user($data,$new=true){
		$table="admin_user";
		$db=&DB::connect(DB_DSN);
		$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
		foreach ($data as $field => $value) {
			if(isset($table_field['order'][$field])){
				$fields_values[$field]=addslashes($value);
			}
		}
		if(!empty($fields_values)){
			if($new==true){
				$res=$db->autoExecute($table, $fields_values,DB_AUTOQUERY_INSERT);
			} else {
				$res=$db->autoExecute($table, $fields_values,DB_AUTOQUERY_UPDATE, "admin_id = '".$data['admin_id']."'");

			}
		}
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
		if(isset($table_field['order']['sort_field'])){
			$this->UpdateSortFields(array('table'=>$table,'field'=>'admin_id'));
		}
		return $res;
	}

	function del_admin_user($id){
		$db=&DB::connect(DB_DSN);
		return $db->query("delete from admin_user where admin_id='".$id."'");
	}

	####################################
	# Mailtpl
	####################################

	function del_admin_mailtpl($id){
		$db=&DB::connect(DB_DSN);
		return $db->query("delete from mailtpl where mailtpl_id='".$id."'");
	}

	function next_mailtpl_id(){
		$db=&DB::connect(DB_DSN);
		$res=$db->getRow("select max(mailtpl_id) as id from mailtpl",null,DB_FETCHMODE_ASSOC);
		return $res['id']+1;
	}

	function store_admin_mailtpl($data,$new=true){
		$table="mailtpl";
		$db=&DB::connect(DB_DSN);
		$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
		foreach ($data as $field => $value) {
			if(isset($table_field['order'][$field])){
				$fields_values[$field]=stripslashes($value);
			}
		}
		if(!empty($fields_values)){
			if($new==true){
				$res=$db->autoExecute($table, $fields_values,DB_AUTOQUERY_INSERT);
			} else {
				$res=$db->autoExecute($table, $fields_values,DB_AUTOQUERY_UPDATE, "mailtpl_id = '".$data['mailtpl_id']."'");

			}
		}
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
		if(isset($table_field['order']['sort_field'])){
			$this->UpdateSortFields(array('table'=>$table,'field'=>'mailtpl_id'));
		}
		return $res;
	}

	function get_mailtpl_main($id){
		$db=&DB::connect(DB_DSN);
		$res=$db->getRow("select * from mailtpl where mailtpl_id='".$id."'",null,DB_FETCHMODE_ASSOC);
		return $res['mailtpl_main'];
	}

	function get_mailtpl_body($id){
		$db=&DB::connect(DB_DSN);
		$res=$db->getRow("select * from mailtpl where mailtpl_id='".$id."'",null,DB_FETCHMODE_ASSOC);
		return $res['mailtpl_body'];
	}

	function get_mailtpl_subject($id){
		$db=&DB::connect(DB_DSN);
		$res=$db->getRow("select * from mailtpl where mailtpl_id='".$id."'",null,DB_FETCHMODE_ASSOC);
		return $res['mailtpl_subject'];
	}

	function get_admin_mailtpl($id=""){
		$table="mailtpl";
		$db=&DB::connect(DB_DSN);
		$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
		if(empty($id)){
			if(!empty($_GET['sort_field']) && !empty($_GET['sort'])){
				$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
				if(in_array($_GET['sort_field'],$table_field['order'])){
					$order_by=" order by ".$_GET['sort_field']." ".$_GET['sort'];
				}
			} else {
				if(isset($table_field['order']['sort_field'])){
					$order_by=" order by sort_field asc";
				} else {
					$order_by=" order by mailtpl_id desc";
				}
			}
			return $db->getALL("select * from ".$table.$order_by,null,DB_FETCHMODE_ASSOC);
		} else {
			return $db->getRow("select * from ".$table." where mailtpl_id='".$id."'",null,DB_FETCHMODE_ASSOC);
		}
	}

	function search_admin_mailtpl($q){
		$table="mailtpl";
		$db=&DB::connect(DB_DSN);
		$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
		if(!empty($_GET['sort_field']) && !empty($_GET['sort'])){
			$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
			if(in_array($_GET['sort_field'],$table_field['order'])){
				$order_by=" order by ".$_GET['sort_field']." ".$_GET['sort'];
			}
		} else {
			if(isset($table_field['order']['sort_field'])){
				$order_by=" order by sort_field asc";
			} else {
				$order_by=" order by mailtpl_id desc";
			}
		}
		return $db->getALL("select * from ".$table." where (mailtpl_id = '".$q."' or mailtpl_name like '%".$q."%' or mailtpl_subject like '%".$q."%' or mailtpl_body like '%".$q."%')".$order_by,null,DB_FETCHMODE_ASSOC);
	}

	####################################
	# Admin Listen
	####################################

	function del_field_member($table,$id){
		$db=&DB::connect(DB_DSN);
		return $db->query("delete from ".$table." where field_id='".$id."'");
	}

	function get_admin_option_field($table,$id="",$language="DE"){
		$db=&DB::connect(DB_DSN);
		$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
		if(empty($id)){
			if(!empty($_GET['sort_field']) && !empty($_GET['sort'])){
				$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
				if(in_array($_GET['sort_field'],$table_field['order'])){
					$order_by=" order by ".$_GET['sort_field']." ".$_GET['sort'];
				}
			} else {
				if(isset($table_field['order']['sort_field'])){
					$order_by=" order by sort_field asc";
				} else {
					$order_by=" order by field_id desc";
				}
			}
			if(empty($language)){
				return $db->getALL("select * from ".$table.$order_by,null,DB_FETCHMODE_ASSOC);
			} else {
				return $db->getALL("select * from ".$table." where field_iso='".$language."'".$order_by,null,DB_FETCHMODE_ASSOC);
			}
		} else {
			return $db->getRow("select * from ".$table." where field_id='".$id."'",null,DB_FETCHMODE_ASSOC);
		}
	}

	function get_field_member($table,$iso=""){
		$db=&DB::connect(DB_DSN);
		$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
		$q="select * from ".$table;
		if(!empty($iso)) $q.=" where field_iso='".$iso."'";
		if(isset($table_field['order']['sort_field'])){
			$q.=" order by sort_field asc";
		} else {
			$q.=" order by field_id asc";
		}
		return $db->getAll($q,null,DB_FETCHMODE_ASSOC);
	}

	function get_field_member_option($table,$option,$iso=""){
		$db=&DB::connect(DB_DSN);
		$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
		$q="select * from ".$table;
		if(!empty($iso)) $q.=" where field_iso='".$iso."'";
		if(!empty($iso) && !empty($option)){
			foreach ($option as $field => $value){
				$q.=" and ".$field."='".$value."'";
			}
		}
		if(empty($iso) && !empty($option)){
			$q.=" where 1";
			foreach ($option as $field => $value){
				$q.=" and ".$field."='".$value."'";
			}
		}
		if(isset($table_field['order']['field_status'])){
			$q.=" order by field_status desc, field_id asc";
		} else {
			$q.=" order by field_name asc";
		}
		return $db->getAll($q,null,DB_FETCHMODE_ASSOC);
	}

	function get_field_member_id($table,$id,$iso=""){
		$db=&DB::connect(DB_DSN);
		$q="select * from ".$table;
		$q.=" where field_id='".$id."'";
		if(!empty($iso)) $q.=" and field_iso='".$iso."'";
		$q.=" order by field_id asc";
		return $db->getRow($q,null,DB_FETCHMODE_ASSOC);
	}

	function get_field_id_by_name($table,$name,$iso=""){
		$db=&DB::connect(DB_DSN);
		$q="select * from ".$table;
		$q.=" where field_name='".$name."'";
		if(!empty($iso)) $q.=" and field_iso='".$iso."'";
		$q.=" order by field_id asc";
		$res=$db->getRow($q,null,DB_FETCHMODE_ASSOC);
		if(!empty($res)){
			return $res['field_id'];
		} else {
			return false;
		}
	}

	function store_field_member($data,$table,$new=true){
		$db=&DB::connect(DB_DSN);
		$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
		foreach ($data as $field => $value) {
			if(isset($table_field['order'][$field])){
				$fields_values[$field]=$value;
			}
		}
		if(!empty($fields_values)){
			if($new==true){
				$res=$db->autoExecute($table, $fields_values,DB_AUTOQUERY_INSERT);
			} else {
				$res=$db->autoExecute($table, $fields_values,DB_AUTOQUERY_UPDATE, "field_id = '".$data['field_id']."'");

			}
		}
		if (PEAR::isError($res)) {
			die($res->getMessage());
		}
		return $res;
	}

	####################################
	# Dyn API
	####################################

	function get_dyn_api($table,$id=""){
		$db=&DB::connect(DB_DSN);
		$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
		if(empty($id)){
			if(!empty($_GET['sort_field']) && !empty($_GET['sort'])){
				$table_field=$db->tableInfo($table,DB_TABLEINFO_ORDER);
				if(in_array($_GET['sort_field'],$table_field['order'])){
					$order_by=" order by ".$_GET['sort_field']." ".$_GET['sort'];
				}
			} else {
				if(isset($table_field['order']['sort_field'])){
					$order_by=" order by sort_field asc";
				}
			}
			return $db->getALL("select * from ".$table.$order_by,null,DB_FETCHMODE_ASSOC);
		} else {
			return $db->getRow("select * from ".$table." where ".$table."_id='".$id."'",null,DB_FETCHMODE_ASSOC);
		}
	}

}

?>

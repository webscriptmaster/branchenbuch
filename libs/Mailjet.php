<?php
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);



#namespace Mailjet;

require_once(dirname(__FILE__) . "/Mailjet/Client.php");
require_once(dirname(__FILE__) . "/Mailjet/Request.php");
require_once(dirname(__FILE__) . "/Mailjet/Resources.php");
require_once(dirname(__FILE__) . "/Mailjet/Response.php");

echo "test";

class Mailjet {
	/*
	
	API-Key: da1da6b0e74375dc92eb19ddce496fc5
	Geheimer Key: 295314427c8334afb21199e38a255e88
	*/
	
	public function __construct() {
		
	}
	
	
}
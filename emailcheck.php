<?php
   #############################################################################
   #                    SUPERMAILER EMailCheck SCRIPT                          #
   #                                                                           #
   #      Copyright © 2003-2010 Mirko Boeer Softwareentwicklungen Leipzig      #
   #                 http://www.supermailer.de/                                #
   #                                                                           #
   # Dieses Script ist URHEBERRECHTLICH GESCHÜTZT und KEIN Open Source Script! #
   #                                                                           #
   # Es ist NICHT gestattet dieses Script ohne Einverstaendnis des Autors      #
   # weiterzugeben oder in anderen Anwendungen einzusetzen.                    #
   #                                                                           #
   # Dieses Script nutzt Teile der SMTP-Klasse class.smtp.php von Chris Ryan.  #
   #                                                                           #
   #                                                                           #
   #############################################################################

class SMTP
{
    var $SMTP_PORT = 25;
    var $SMTPHost = "";

    var $CRLF = "\r\n";

    var $smtp_conn;
    var $error;
    var $helo_rply;

    var $do_debug;       # the level of debug to perform

    // constructor
    function SMTP() {
        $this->smtp_conn = 0;
        $this->error = null;
        $this->helo_rply = null;

        $this->do_debug = 0;
    }

    function Connect($host,$port=0,$tval=30) {
        # set the error val to null so there is no confusion
        $this->error = null;
        $this->SMTPHost = $host;

        # make sure we are __not__ connected
        if($this->connected()) {
            # ok we are connected! what should we do?
            # for now we will just give an error saying we
            # are already connected
            $this->error =
                array("error" => "Already connected to a server");
            return false;
        }

        if(empty($port)) {
            $port = $this->SMTP_PORT;
        }

        #connect to the smtp server
        $this->smtp_conn = fsockopen($host,    # the host of the server
                                     $port,    # the port to use
                                     $errno,   # error number if any
                                     $errstr,  # error message if any
                                     $tval);   # give up after ? secs
        # verify we connected properly
        if(empty($this->smtp_conn)) {
            $this->error = array("error" => "Failed to connect to server: ".$host,
                                 "errno" => $errno,
                                 "errstr" => $errstr);
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": $errstr ($errno)" . $this->CRLF;
            }
            return false;
        }

        # sometimes the SMTP server takes a little longer to respond
        # so we will give it a longer timeout for the first read
        // Windows still does not have support for this timeout function
        if(substr(PHP_OS, 0, 3) != "WIN")
           socket_set_timeout($this->smtp_conn, $tval, 0);

        # get any announcement stuff
        $announce = $this->get_lines();

        # set the timeout  of any socket functions at 1/10 of a second
        //if(function_exists("socket_set_timeout"))
        //   socket_set_timeout($this->smtp_conn, 0, 100000);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $announce;
        }

        return true;
    }


    function Connected() {
        if(!empty($this->smtp_conn)) {
            $sock_status = socket_get_status($this->smtp_conn);
            if($sock_status["eof"]) {
                # hmm this is an odd situation... the socket is
                # valid but we aren't connected anymore
                if($this->do_debug >= 1) {
                    echo "SMTP -> NOTICE:" . $this->CRLF .
                         "EOF caught while checking if connected";
                }
                $this->Close();
                return false;
            }
            return true; # everything looks good
        }
        return false;
    }

    /**
     * Closes the socket and cleans up the state of the class.
     * It is not considered good to use this function without
     * first trying to use QUIT.
     * @access public
     * @return void
     */
    function Close() {
        $this->error = null; # so there is no confusion
        $this->helo_rply = null;
        if(!empty($this->smtp_conn)) {
            # close the connection and cleanup
            fclose($this->smtp_conn);
            $this->smtp_conn = 0;
        }
    }


    function Hello($host="") {
        $this->error = null; # so no confusion is caused

        if(!$this->connected()) {
            $this->error = array(
                    "smtp_code" => 9999,
                    "error" => "Called Hello() without being connected");
            return false;
        }

        # if a hostname for the HELO wasn't specified determine
        # a suitable one to send
        if(empty($host)) {
            # we need to determine some sort of appopiate default
            # to send to the server
            $host = "localhost";
        }

        // Send extended hello first (RFC 2821)
        if(!$this->SendHello("EHLO", $host))
        {
            if(!$this->SendHello("HELO", $host))
                return false;
        }

        return true;
    }

    /**
     * Sends a HELO/EHLO command.
     * @access private
     * @return bool
     */
    function SendHello($hello, $host) {
        fputs($this->smtp_conn, $hello . " " . $host . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER: " . $this->CRLF . $rply;
        }

        if($code != 250) {
            $this->error =
                array("error" => $hello . " not accepted from server",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }

        $this->helo_rply = $rply;

        return true;
    }

    function Quit($close_on_error=true) {
        $this->error = null; # so there is no confusion

        if(!$this->connected()) {
            $this->error = array(
                    "smtp_code" => 9999,
                    "error" => "Called Quit() without being connected");
            return false;
        }

        # send the quit command to the server
        fputs($this->smtp_conn,"quit" . $this->CRLF);

        # get any good-bye messages
        $byemsg = $this->get_lines();

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $byemsg;
        }

        $rval = true;
        $e = null;

        $code = substr($byemsg,0,3);
        if($code != 221) {
            # use e as a tmp var cause Close will overwrite $this->error
            $e = array("error" => "SMTP server rejected quit command",
                       "smtp_code" => $code,
                       "smtp_rply" => substr($byemsg,4));
            $rval = false;
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $e["error"] . ": " .
                         $byemsg . $this->CRLF;
            }
        }

        if(empty($e) || $close_on_error) {
            $this->Close();
        }

        return $rval;
    }


    /**
     * Starts a mail transaction from the email address specified in
     * $from. Returns true if successful or false otherwise. If True
     * the mail transaction is started and then one or more Recipient
     * commands may be called followed by a Data command.
     *
     * Implements rfc 821: MAIL <SP> FROM:<reverse-path> <CRLF>
     *
     * SMTP CODE SUCCESS: 250
     * SMTP CODE SUCCESS: 552,451,452
     * SMTP CODE SUCCESS: 500,501,421
     * @access public
     * @return bool
     */
    function Mail($from) {
        $this->error = null; # so no confusion is caused

        if(!$this->connected()) {
            $this->error = array(
                    "smtp_code" => 9999,
                    "error" => "Called Mail() without being connected");
            return false;
        }

        fputs($this->smtp_conn,"MAIL FROM:<" . $from . ">" . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if($code != 250) {
            $s = "MAIL not accepted from server";
            if(strlen($rply) > 3)
              $s = $rply;

            $s .= " MX: ".$this->SMTPHost;

            $this->error =
                array("error" => $s,
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }
        return true;
    }

    function Recipient($to) {
        $this->error = null; # so no confusion is caused

        if(!$this->connected()) {
            $this->error = array(
                    "smtp_code" => 9999,
                    "error" => "Called Recipient() without being connected");
            return false;
        }

        fputs($this->smtp_conn,"RCPT TO:<" . $to . ">" . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if($code != 250 && $code != 251) {
            $s = "RCPT not accepted from server";
            if( strlen($rply) > 3 )
               $s = $rply;
            $s .= " MX: ".$this->SMTPHost;
            $this->error =
                array("error" => $s,
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }
        return true;
    }

    /**
     * Sends the RSET command to abort and transaction that is
     * currently in progress. Returns true if successful false
     * otherwise.
     *
     * Implements rfc 821: RSET <CRLF>
     *
     * SMTP CODE SUCCESS: 250
     * SMTP CODE ERROR  : 500,501,504,421
     * @access public
     * @return bool
     */
    function Reset() {
        $this->error = null; # so no confusion is caused

        if(!$this->connected()) {
            $this->error = array(
                    "smtp_code" => 9999,
                    "error" => "Called Reset() without being connected");
            return false;
        }

        fputs($this->smtp_conn,"RSET" . $this->CRLF);

        $rply = $this->get_lines();
        $code = substr($rply,0,3);

        if($this->do_debug >= 2) {
            echo "SMTP -> FROM SERVER:" . $this->CRLF . $rply;
        }

        if($code != 250) {
            $this->error =
                array("error" => "RSET failed",
                      "smtp_code" => $code,
                      "smtp_msg" => substr($rply,4));
            if($this->do_debug >= 1) {
                echo "SMTP -> ERROR: " . $this->error["error"] .
                         ": " . $rply . $this->CRLF;
            }
            return false;
        }

        return true;
    }

    /*******************************************************************
     *                       INTERNAL FUNCTIONS                       *
     ******************************************************************/

    /**
     * Read in as many lines as possible
     * either before eof or socket timeout occurs on the operation.
     * With SMTP we can tell if we have more lines to read if the
     * 4th character is '-' symbol. If it is a space then we don't
     * need to read anything else.
     * @access private
     * @return string
     */
    function get_lines() {
        $data = "";
        while($str = fgets($this->smtp_conn,515)) {
            if($this->do_debug >= 4) {
                echo "SMTP -> get_lines(): \$data was \"$data\"" .
                         $this->CRLF;
                echo "SMTP -> get_lines(): \$str is \"$str\"" .
                         $this->CRLF;
            }
            $data .= $str;
            if($this->do_debug >= 4) {
                echo "SMTP -> get_lines(): \$data is \"$data\"" . $this->CRLF;
            }
            # if the 4th character is a space then we are done reading
            # so just break the loop
            if(substr($str,3,1) == " ") { break; }
        }
        return $data;
    }
}

class EMailCheck {

 // @private
 var $SMTPhost = "";
 var $SMTPport = 25;
 var $SMTPtimeout = 30;
 var $SMTPheloname = "";

 // @public
 var $SMTPFromEMailAddress = "";
 var $SMTPRcpts = array();

 // @private
 var $smtp = NULL;

 // @public
 var $CommonErrorText = "";
 var $SMTPErrorNo = 0;
 var $SMTPErrorString = "";

 var $debug = false;


 function CheckEMailAddress() {

     if ($this->debug == true) {
       $this->SetError('Requested mail action okay, completed', '250', 'Requested mail action okay, completed');
       return true;
     }

     # Connect to smtp server
     if(!$this->SMTPConnect()) {
         $this->SetError($this->smtp->error["error"], $this->smtp->error["smtp_code"], $this->smtp->error["smtp_msg"]);
         return false;
     }

     if(!$this->smtp->Mail($this->SMTPFromEMailAddress))
     {
         $this->SetError($this->smtp->error["error"], $this->smtp->error["smtp_code"], $this->smtp->error["smtp_msg"]);
         $this->SMTPClose();
         return false;
     }

     for ($i=0; $i<count($this->SMTPRcpts); $i++) {
        if(!$this->smtp->Recipient($this->SMTPRcpts[$i]))
        {
            $this->SetError($this->smtp->error["error"], $this->smtp->error["smtp_code"], $this->smtp->error["smtp_msg"]);
            $this->SMTPClose();
            return false;
        }
     }

     $this->smtp->Reset();
     $this->SMTPClose();

     $this->SetError('Requested mail action okay, completed', '250', 'Requested mail action okay, completed');
     return true;
 }

 function SMTPConnect() {
     if($this->smtp == NULL) { $this->smtp = new SMTP(); }

     if ($this->smtp->Connected() == false) {
       if($this->smtp->Connect($this->SMTPhost, $this->SMTPport, $this->SMTPtimeout)) {

          if (!$this->smtp->Hello($this->GetServerHostname($this->SMTPheloname))) {
              $this->SetError($this->smtp->error["error"], $this->smtp->error["smtp_code"], $this->smtp->error["smtp_msg"]);
              $this->SMTPClose();
              return false;
          } else
             return true;

       } else {
          $this->SetError($this->smtp->error["error"], $this->smtp->error["errno"], $this->smtp->error["errstr"]);
          return false;
      }
     }
     return true;
 }

 function SMTPClose() {
     if($this->smtp != NULL)
     {
         if($this->smtp->Connected())
         {
             $this->smtp->Quit();
             $this->smtp->Close();
         }
         $this->smtp->smtp_conn = 0; # set 0
     }
 }

 function GetServerVar($varName) {
     global $HTTP_SERVER_VARS;
     global $HTTP_ENV_VARS;

     if(!isset($_SERVER))
     {
         $_SERVER = $HTTP_SERVER_VARS;
         if(!isset($_SERVER["REMOTE_ADDR"]))
             $_SERVER = $HTTP_ENV_VARS;
     }

     if(isset($_SERVER[$varName]))
         return $_SERVER[$varName];
     else
         return "";
 }

 function GetServerHostname($heloname) {
    if ($heloname != "")
        $result = $heloname;
    elseif ($this->GetServerVar('SERVER_NAME') != "")
        $result = $this->GetServerVar('SERVER_NAME');
    else
        $result = "localhost.localdomain";

    $pos = strpos ($result, "www.");
    if ($pos === false) {
    } else {
      $result = substr($result, $pos + 4);
    }

    return $result;
 }

 function SetError($errortext, $SMTPerrorno, $SMTPerrorstring) {
   $this->CommonErrorText = $errortext;
   $this->SMTPErrorNo = $SMTPerrorno;
   $this->SMTPErrorString = $SMTPerrorstring;
 }

 function GetMXServer($email) {
    $server="";
    $hostnames = explode("@", $email);
    if (count($hostnames) <> 2)
       return $server;
    $hostname = $hostnames[1];
    $mx_avail = getmxrr($hostname, $mx_records, $mx_weight);

    if($mx_avail){
      $mxs = array();

      for($i=0; $i<count($mx_records); $i++)
        $mxs[$mx_weight[$i]] = $mx_records[$i];

      ksort($mxs, SORT_NUMERIC);
      reset($mxs);

      $server_found = 0;
      while (list ($mx_weight, $mx_host) = each ($mxs) ) {
         $fp = @fsockopen($mx_host, 25, $errno, $errstr, 10);
         if($fp){
           $ms_resp = "";
           $ms_resp .= $this->send_command($fp, "HELO ".$this->GetServerHostname($this->SMTPheloname));
           if ( ($ms_resp == "" /* AOL says nothing */) || (substr($ms_resp, 0, 3) == "250") || (substr($ms_resp, 0, 3) == "220") )
             $server_found = 1;
           $this->send_command($fp, "QUIT");
           fclose($fp);
           if ($server_found) {
             $server = $mx_host;
             break;
           }
         }
      }  #while
    } # if
    return $server;
 }

 function send_command($fp, $out){
  fwrite($fp, $out . "\r\n");
  return $this->get_data($fp);
 }

 function get_data($fp){
  $s = "";
  stream_set_timeout($fp, 20);

  for($i=0; $i < 2; $i++)
   $s .= fgets($fp, 1024);
  return $s;
 }

} # class


// support windows platforms
if (!function_exists ('getmxrr') ) {
  function getmxrr($hostname, &$mxhosts, &$mxweight) {
   if (!is_array ($mxhosts) ) {
     $mxhosts = array ();
   }

   if (!empty ($hostname) ) {
     $output = "";
     @exec ("nslookup.exe -type=MX $hostname.", $output);
     $imx=-1;

     foreach ($output as $line) {
       $imx++;
       $parts = "";
       if (preg_match ("/^$hostname\tMX preference = ([0-9]+), mail exchanger = (.*)$/", $line, $parts) ) {
         $mxweight[$imx] = $parts[1];
         $mxhosts[$imx] = $parts[2];
       }
     }
     return ($imx!=-1);
   }
   return false;
  }
}

if (isset($_POST["test"]) || isset($_GET["test"]) ) {
  print "OK\r\n";
  exit;
}
else
  if( isset($_POST["EMail"]) && isset($_POST["FromEMail"]) ) {
    $e = new EMailCheck();

    $email = $_POST["EMail"];

    $host = $e->GetMXServer($email);
    if($host == "") {
      print "999"."\t"."No MX server for domain found."."\t"."No MX server for domain found.";
      exit;
    }

    $e->SMTPHost = $host;
    $e->SMTPFromEMailAddress = $_POST["FromEMail"];
    $e->SMTPRcpts[] = $email;
    $e->SMTPhost = $host;
    $e->CheckEMailAddress();

    print $e->SMTPErrorNo."\t".$e->SMTPerrorstring."\t".$e->CommonErrorText;
  }
  else
   print "Nothing to do.";
?>
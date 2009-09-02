<?php
/**
########################################
#OOB/N1 Framework [©2004,2006]
#
#  @copyright Pablo Micolini
#  @license BSD
######################################## 
*/

/**
 Extension of PHP exception handler, adds a user-message
*/

/*
  Funciones Heredadas:
 ---------------------
   final function getMessage();                // message of exception
   final function getCode();                  // code of exception
   final function getFile();                  // source filename
   final function getLine();                  // source line
   final function getTrace();                  // an array of the backtrace()
   final function getTraceAsString();          // formated string of trace
 ---------------------
 */

 class OOB_Exception extends Exception
{
	private $usermessage;
	private $loged = 0;

/** Throwing and exception
 * message = the message the developer will see (also can be logued)
 * code = numeric value
 * usermessage = the message the user will see at the error page
 * log = (true/false) stores the message/file name/line number to the log file 
 */	
	public function __construct($message, $code = 0, $usermessage, $log = false) {
       $this->usermessage = $usermessage;
       parent::__construct($message, $code);
       if ($log)
       $this->logMessage();
 
   }
   
   /** Returs the user message */
   public function getUserMessage() {
       return $this->usermessage;
   }
   
   /** Stores the exception message on the log file */     
 	public function logMessage ()
 	{
 	if ($this->loged == 0)
 	{
 		$today = date("d/m/Y@H:i:s");
	
	$logmessage = "[" . $today . "] - " . $_SERVER['REMOTE_ADDR'] . " |Exception| M:" . $this->getMessage(). " File:" . $this->getFile(). " Line:" . $this->getLine() . "\r\n"; // " UM:" . $this->getUserMessage() .
	$dir = dirname(__FILE__) . "..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."error.log"; // done like this to work if no config file is found
	error_log( $logmessage, 3, $dir  );
	$this->loged = 1; 
 	}
 	 
 	}
}

class OOB_Exception_400 extends OOB_Exception
{
	function __construct($message){
			parent::__construct( $message, "400", 'Se produjo un error,comun&iacute;quese con el administrador del sistema.', true );	
	}

}

class OOB_Exception_9001 extends OOB_Exception
{
	function __construct($message){
			parent::__construct( $message, "9001", $message, false );	
	}

}

?>

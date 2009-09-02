<?
/**
########################################
#OOB/N1 Framework [©2004,2006]
#
#  @copyright Pablo Micolini
#  @license BSD
######################################## 
*/
/**
 This class can handle user-errors that wont be throwing an exception. 
 That is, that have been taken in consideration by the programer.
 There is a design flaw of the object, where you cant know wich instance
 of a given object is giving the error. That must be fixed.

 */
class OOB_errorhandler {
	
	private $error = array();
	private $logfile;
	
	public function __construct ( $path = '') 	{
		
	$this->logfile = $path . DIRECTORY_SEPARATOR . "error.log";
	}
	/** Adds an error to the handler, you must provide, the "parent (object instance that is having the error)", "message" and (true-false) if you want to log the error. */
	public function addError ($var, $msg, $log = false) {
	$temp['var'] = $var;
	$temp['msg'] = $msg;
	$this->error[] = $temp;
	
		if ($log)
		{
	$today = date("d/m/Y@H:i:s");
	$logmessage = "[" . $today . "] - " . $_SERVER['REMOTE_ADDR'] . " |Error| " . $var . " :: " . $msg . "\r\n";
	
		error_log( $logmessage, 3, $this->logfile );
		}
	}
	
	/** Returs all the errors that the handler has */
	public function getErrors () {
	return $this->error; 
	}
	
	/** Returns true if there are any errors in the handler */ 
	public function areError () {
		if (count($this->error) > 0)
		return true;
		else
		return false;
	}
	/** Returs all the errors that the handler has for a given "parent" (object instance) */
	public function getErrorsfor ($var) {
	$return = array();
	foreach ($this->error as $erro)
	{
	if ($erro["var"] == $var)
	$return[] = $erro["msg"];
	}
	

	
	if (count ($return)>0)
	{return $return;}
	else 
	{return false;}
	}

}


?>
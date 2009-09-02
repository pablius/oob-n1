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
 Provides an objetictive view of the URL
*/ 

/* Apache USAGE, on IIS, i have no clue
*---------------------------------------
** RewriteEngine on
** RewriteRule ^(\/.*)*$  /index.php
**
**
Reference
***********
getModule : Returns the module (string)
getPerspective: Returns the perspective (object)
getVars : Returns an array with the vars after the module 
*/

class OOB_urlhandler {
	
	private $realuri;
	private $module = false;
	private $vars = false;
	private $uri;
	private $perspective = false;
	 
/** extract the url data that is within slashes.  */
	public function __construct ($newurl = false) {
         global $ari;
         // mode name differs from real file name in user mode 
         $mode = 'index';
         	if ($ari->mode == 'admin')
         {$mode = 'admin';}
         
         if (!$newurl){
         	
         // IIS not providing REQUEST_URI
		if (!isset($_SERVER['REQUEST_URI']))
   			{$this->realuri = substr($_SERVER['argv'][0], strpos($_SERVER['argv'][0], ';') + 1);}
		else
			{$this->realuri = $_SERVER['REQUEST_URI'];}
			
		$script = $_SERVER['SCRIPT_NAME'];

        /* remove all the stuff that is before the vars*/

				if(strstr($this->realuri, $mode . '.php')) {
						$uri = explode(".php", $this->realuri);
						$newuri = $uri[1];
				} 
				else
				{ // no me queda mas que pensar que tiene el mod-rewrite bien andando :P
					$newuri = $this->realuri;
				} 

	

         }
         else
        { $newuri = $newurl;}
		 // extract the trailing stuff with the "?" 
		 $newuri = explode("?", $newuri);
		 $this->uri = $newuri[0];

		// extracts the trailing slash.. avoids misinterpretation 
  		if( substr($this->uri, -1 ) == '/' )
			{
			$this->uri = substr($this->uri, 0, strlen( $this->uri ) -1); 
			}

				
	// explode the uri 
		$exp = explode("/", $this->uri);
		$vacio = array_shift($exp); // first is always empty
	
	if (count($exp)>0)
		{
			if (in_array ($exp[0], OOB_perspective::listPerspectives()) && $mode == 'index')
			{
		      		$this->perspective = array_shift($exp);
		   //   	$this->module = array_shift($exp);
			}
			//else
		//	{ 	
				$this->module = array_shift($exp);    	
		//	}
			
			$this->vars = $exp;
		}
	}
        
    /** returns the module name  */
	public function getModule() {

		return $this->module;

   }
   
    /** returns the values to be used within the module  */
   	public function getVars() {
       if (count ($this->vars) > 0)
       		return $this->vars;
       		else
       		return false;

   }
   
	 /** crawl the DB and sees if the url has a redirection made  */
	 // @todo implement!
	public function redirectURL ()
	{
		global $ari;;
		$manejador = $ari->config->get('urlhandler', 'main');
		
		if ($manejador != "")
			{	
				return call_user_func(array($manejador, 'urlHandler'),$this->uri);
			}
		
		else
			{	return false;	}
	//	return $manejador::urlHandler($this->uri); // NO ANDA ASI!!:(

	
	/* 	// search on the DB for the URL
		$redirect['/deportes'] ='/contenido/portada/ver/3';
		$redirect['/cultura'] ='/contenido/portada/ver/4';

		// if found return the URL, else false
		if (array_key_exists ($this->realuri, $redirect))
			{
			return $redirect[$this->realuri];
			}
		else
			{return false;} */
	}
	
/** Provides the full real uri as appears at the user navigation bar */
	public function realURI () {
	return $this->realuri;
	}


	public function getPerspective ()
	{
	return new OOB_perspective ($this->perspective);
	}

}
?>
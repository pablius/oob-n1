<?
/**
########################################
#OOB/N1 Framework [©2004,2009]
#
#  @copyright Pablo Micolini
#  @license BSD
#  @version 1.2
######################################## 
*/

// They say includes go first because they make parsing faster

require_once ('librerias'.DIRECTORY_SEPARATOR.'adodb'.DIRECTORY_SEPARATOR.'adodb.inc.php');
require_once ('librerias'.DIRECTORY_SEPARATOR.'adodb'.DIRECTORY_SEPARATOR.'adodb-errorhandler.inc.php');
require_once ('librerias'.DIRECTORY_SEPARATOR.'smarty'.DIRECTORY_SEPARATOR.'Smarty.class.php');
require_once ('librerias'.DIRECTORY_SEPARATOR.'cache_lite'.DIRECTORY_SEPARATOR.'Lite.php');
require_once ('librerias'.DIRECTORY_SEPARATOR.'adodb'.DIRECTORY_SEPARATOR.'session'.DIRECTORY_SEPARATOR.'adodb-session.php');
require_once ('librerias'.DIRECTORY_SEPARATOR.'classdate'.DIRECTORY_SEPARATOR.'Date.php');
require_once (dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR. 'modulos'.DIRECTORY_SEPARATOR.'seguridad'.DIRECTORY_SEPARATOR.'oo'.DIRECTORY_SEPARATOR.'seguridad.php');

require_once ("OOB_exception.php");
require_once ("OOB_constants.php");

// php-ext path
set_include_path(get_include_path().PATH_SEPARATOR.dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR.'scripts'.DIRECTORY_SEPARATOR.'ext'.DIRECTORY_SEPARATOR.'library'); 

/** Autoloader for engine clases, has no safety check. Use at your own risk  */
function __autoload($class) {
//
	if (eregi('ADODB', $class))
	{
	return false;
	}
	elseif (eregi('PhpExt', $class))
	{
		$elements = explode ("_", $class);
		$file = dirname(dirname(__FILE__)) .DIRECTORY_SEPARATOR. 'scripts' . DIRECTORY_SEPARATOR. 'ext' .DIRECTORY_SEPARATOR. 'library'.DIRECTORY_SEPARATOR. join (DIRECTORY_SEPARATOR, $elements) . '.php';
		
		if (!file_exists($file))
		{
			$file = str_replace('PhpExt', 'PhpExtUx', $file);
		}
		
		require_once ($file);
		
		
	}
	elseif(eregi("OOB_",$class) ) 
	{	
		require_once ($class . ".php");
	}
	else
	{
		$modulo = explode ("_", $class);
		require_once (dirname(dirname(__FILE__)) .DIRECTORY_SEPARATOR. "modulos" . DIRECTORY_SEPARATOR. $modulo[0] .DIRECTORY_SEPARATOR ."oo".DIRECTORY_SEPARATOR .$class. ".php");
	}
}


/**
*	OOB:: Advanced Resources Integration (ARI)
*	This engine provides the main fuctionalities to the framework
*	all the primary objects are accesed from here.
*/
class oob_ari {
	private $tde;
	private $tdl;
	private $expiretime;
	public $config;
	private $debug;
	public $mode;
	
	

	public $db;
	public $t;
	public $user;
	public $error;
	public $module;
	private $url;
	public $perspective;
	public $filename = "main.tpl";
	private $popup = false;
	private $title;
	private $description;
	private $keywords;
	private $author;
	public $adminaddress;
	public $webaddress;
	public $filesdir;
	public $agent;
	public $locale;
	private $allowcache;
	private $plantilla;
	private $mod_content;
	
	public	$cachedir;
	public	$enginedir;
	private	$libsdir;

	/** What makes the ARI start running
	 You must pass the mode (user, admin, cron) to the constructor. */

	private function __construct($mode= 'user') 
	{
		$this->mode= 'user';
		if ($mode == 'admin' || $mode == 'cron')
		{
			$this->mode= $mode;
		}
			
											
		$this->inicioCronometro();			
		$this->loadConfig();
		$this->internalChrono('config'); //

		
		$this->internalChrono('error'); //
		$this->startDB();
		$this->internalChrono('db');
		$this->initializeEnviromental();
		$this->internalChrono('enviromental');
											
		$this->startEEHandler();
											
		$this->agentDetect(); //  oob_module requires the lang, even on cronmode-
		$this->internalChrono('agent');
			
		$this->startTemplates();	
		if ($this->mode == 'cron') 
		{
			$this->perspective = new OOB_perspective ();
		}
		$this->internalChrono('templates');

	}
	/** Loads config vars, and sets general stuff as PATH */

	private function initializeEnviromental() 
	{
		$this->expiretime= $this->config->get('expires', 'metadata');
		$this->allowcache= true;
		if ($this->config->get('allow-cache', 'main') == "false")
		{
			$this->allowcache= false;
		}
		
		# set headers
		//header('Date: '.gmdate('D, d M Y H:i:s \G\M\T', time()));
		header('Last-Modified: '.gmdate('D, d M Y H:i:s \G\M\T', time()));
		//header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + $this->expiretime));
		# set  path 
			
		$this->filesdir = $this->config->get('filesdir', 'location');
		$this->cachedir = $this->config->get('cachedir', 'location');
//		(hace algo?)
//		$path= ini_get("include_path");
//		if (trim($path) != '')
//			$path .= PATH_SEPARATOR.$this->filesdir;
//		else
//			$path= $this->filesdir;
//			ini_set("include_path", $path); 
			
		# directorios
	//		$this->cachedir= $this->filesdir.DIRECTORY_SEPARATOR.'archivos'.DIRECTORY_SEPARATOR.'cache';
		$this->enginedir = $this->filesdir.DIRECTORY_SEPARATOR.'oob';
		$this->libsdir = $this->filesdir.DIRECTORY_SEPARATOR.'oob'.DIRECTORY_SEPARATOR.'librerias';
	


		# set title & metadata
		$this->title= $this->config->get('title', 'main');
		$this->description= $this->config->get('description', 'metadata');
		$this->keywords= $this->config->get('keywords', 'metadata');
		$this->author= $this->config->get('author', 'metadata');
		# set webdir
		$this->webaddress= $this->config->get('webaddress', 'location');
		$this->adminaddress= $this->config->get('adminaddress', 'location');

		#set debug mode
		$this->debug= false;
	
		if ($this->config->get('debug', 'main') == "true")
		{
			$this->debug= true;
		}
			
		# To avoid sending 2 cookies, we disable the session.cookie from php.
		ini_set("session.use_cookies", "0");
	
		
		/* we must send the dB connection object to the session handler!, 
			   and try to use the same session if previously existed!  */


		if ($this->mode != 'cron') 
		{	
			// @todo : update session manager to use something better
			$GLOBALS['ADODB_SESS_CONN'] = $this->db;
			ADODB_Session :: lifetime($this->expiretime); // warn: si el porcentaje gc es muy alto, puede q nunca mueran las sesiones
			

			if (!isset ($_COOKIE["OOB_Session"])) 
			{
				session_start();
				// expire on about 15 days, expire time handled by session
				setcookie("OOB_Session", session_id(), time() + 1209600, "/");

			} 
			else 
			{
				session_id($_COOKIE["OOB_Session"]);
				session_start();
			}
			
	
			// cross-site-scripting protection (phpsecurity consortium, recomendation) 
			// fixed to work when the client does not provide user/agent.
			if (isset ($_SERVER['HTTP_USER_AGENT']))
				$agent = $_SERVER['HTTP_USER_AGENT'];
			else 
				$agent = "unknown";
					
			if (isset ($_SESSION['HTTP_USER_AGENT'])) 
			{
				if ($_SESSION['HTTP_USER_AGENT'] != md5($agent))
				 {
					// si el agente cambia, la sesion se muere
					session_destroy(); 
					//throw new OOB_exception("Sesion no válida desde {$agent}", "403", "Sus datos de comprobación de sesión no concuerdan, vuelva a ingresar al sitio.", true);
				 }
			} 
			else 
			{
				$_SESSION['HTTP_USER_AGENT']= md5($agent);
			}



		}
	}
	
	/** Config File Loader (uses ini file) 
	it's not catched as the native php function is really fast.
	and is separated so can be changed to another system (db or xml) in the future */
	private function loadConfig() {
		$this->config= new OOB_config(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR."oob".DIRECTORY_SEPARATOR."configuracion".DIRECTORY_SEPARATOR."base.ini.php");

}
	
	
	/** Creates the  dB connection Object / AdoDB */
	private function startDB() {
		global $ADODB_CACHE_DIR;
		$ADODB_CACHE_DIR=  $this->config->get('cachedir', 'location') . DIRECTORY_SEPARATOR . 'db';
		//dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'archivos'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'db';

		$dburi= $this->config->get('uri', 'database');
		

		if (!$this->db=  @NewADOConnection($dburi)) 
		{
			//var_dump(mysqli_connect_errno());
			throw new OOB_exception("No se puede conectar a la DB!", "000", "Ha ocurrido un error de ejecución", true);
		}
		
		// fix para encoding!
		$this->db->Execute("SET NAMES utf8;");

	}
	
	
	/** Detects all about the client */
	private function agentDetect() {
		
		$main= $this->config->get('main_lang', 'main');
		$accepted= $this->config->get('accepted_lang', 'main');
		$this->agent= new OOB_agentdetector('', $main, $accepted);
		
		//el usuario quiere cambiar la interfáz
		if (isset ($_GET['idioma']))
		{
		$this->agent->setSystemLanguage($_GET['idioma']);
		}

		// loads locale ini-file and sets locale php data
		$this->locale= new OOB_config($this->enginedir . DIRECTORY_SEPARATOR."idioma".DIRECTORY_SEPARATOR.$this->agent->getLang().".ini");
		$local= $this->locale->get('locale-code', 'general');
		setlocale(LC_TIME, $local);

	}
	

	

	
	
	/** Errors Handler */
	private function startEEHandler() {
		
		$this->error= new OOB_errorhandler($this->filesdir);
	}
	
	/** initializes smary engine, with all its options. */
	private function startTemplates() {
		$this->plantilla = new Smarty;
		$this->plantilla->template_dir= $this->filesdir;

		$this->plantilla->compile_dir= $this->cachedir;
		$this->plantilla->config_dir= $this->enginedir.DIRECTORY_SEPARATOR.'configuracion';
		$this->plantilla->plugins_dir= $this->libsdir.DIRECTORY_SEPARATOR.'smarty'.DIRECTORY_SEPARATOR.'plugins';
		$this->plantilla->debug_tpl= $this->libsdir.DIRECTORY_SEPARATOR.'smarty'.DIRECTORY_SEPARATOR.'debug.tpl';
		$this->plantilla->cache_dir= $this->cachedir;
		// asing the webdir var so the template can knowit (usefull for images)
		if ($this->mode == 'user' || $this->mode == 'cron')
			$this->plantilla->assign("webdir", $this->webaddress );
		if ($this->mode == 'admin')
			$this->plantilla->assign("webdir", $this->adminaddress);

		$this->plantilla->caching= 0;
		
		
		if ($this->debug) {
			$this->plantilla->debugging= true;
			$this->plantilla->force_compile= true;
			
		} else {
			$this->plantilla->debugging= false;
			$this->plantilla->compile_check= false;
			$this->plantilla->force_compile= false;

			if ($this->allowcache && count($_POST) == 0) {
				$this->plantilla->caching= 1;
				$this->plantilla->cache_lifetime= $this->expiretime;
			// @todo: no hace nada? //
			//	$this->plantilla->compile_id= $this->agent->getLang()."_".$this->url->realURI()."__";
			}
		}
		
	}


	/** loads the module that the URL Handler request */
	private function loadModule() {

		$this->module= new OOB_module($this->url->getmodule());


		if ($this->module->isenabled()  && ($this->perspective->isMember ($this->module))|| $this->mode == 'admin') { // ) { //

			//include the url-handler for the module 	
			if ($this->mode == 'user')
				include ($this->module->userdir().DIRECTORY_SEPARATOR.'url.php');

			if ($this->mode == 'admin')
				include ($this->module->admindir().DIRECTORY_SEPARATOR.'url.php');

		} else
			{
				throw new OOB_exception('', "403", 'Módulo no habilitado.');
			}

	}
	
	/** Someone must collect the garbage! */
	public function __destruct() {
		
		@$this->db->CompleteTrans();
		
		if (is_a($this->db ,"ADOConnection" ))
			$this->db->Close(); 

	}
	
	/** Basic Execution Time Chrono (start) */
	private function inicioCronometro() {
		 $this->tde= microtime(true);
		 $this->tdl = $this->tde;
	}
	
	/** Basic Execution Time Chrono (end) */
	public function finCronometro() {
		$timeend= substr((microtime(true) - $this->tde) * 1000, 0, 7); // microtime(true) - $this->tde; //
		if (($timeend > 5000) && ($this->debug == true))
		{
			throw new OOB_exception("Tiempo de ejecución demasiado alto: $timeend mS!", "000", "Ha ocurrido un error de ejecución", true);
		}
		return $timeend;
	}
	
		/** Advanced Execution Time Chrono  */
	public function internalChrono($id = 0) {
		$valor = microtime(true);
				$this->execution[$id]['a'] = substr(($valor - $this->tde) * 1000, 0, 7); // $valor - $this->tde; //
				$this->execution[$id]['b'] = substr(($valor - $this->tdl) * 1000, 0, 7); // $valor - $this->tdl; //
		$this->tdl = $valor;
	}
	
	/** How to read Objects and Vars from the outside (preliminar) */
	public function get($var) {
	/* 	if (isset ($this-> $var))
			return $this-> $var;
		else
			return false; */
		return $this->$var;
	}
	
	public function set_title($var,$append = true)
	{
		if (strlen($var) > 0)
		{
			if ($append)
			{
				$this->title = $var . ' - ' . $this->title;
			}
			else
			{
				$this->title = $var;
			}
		}
	}
	
	public function set_keywords($var,$append = true)
	{
		if (strlen($var) > 0)
		{
			if ($append)
			{
				$this->keywords = $var . ' - ' . $this->keywords;
			}
			else
			{
				$this->keywords = $var;
			}
		}
	}
	
	public function set_description($var,$append = true)
	{
		if (strlen($var) > 0)
		{
			if ($append)
			{
				$this->description = $var . ' - ' . $this->description;
			}
			else
			{
				$this->description = $var;
			}
		}
	}
	
	
	
	/** OOB-Engine Version identifier */
	public function version() {

		return "release 3 (build 1)";
	}

	/** parses content and outputs */
	public function generateOutput() {
		$this->internalChrono('start_generate');
		// shows debug output from DB
		if ($this->debug) {
			$this->db->debug= true;
			$this->db->LogSQL();
		}

		// check user login
		$this->user= oob_user :: islogued();
$this->internalChrono('user_validate');		
		// url handler
		$this->url= new OOB_urlhandler(false, $this->mode);
		$newurl= $this->config->get('homeelement', 'main');

		if ($this->mode == 'user') {
			if ($this->url->redirectURL() != false)
				$this->url= new OOB_urlhandler($this->url->redirectURL());
				
			if ($this->url->getModule() == "")
			$this->url= new OOB_urlhandler($newurl, $this->mode);
		}
		
		if ($this->mode == 'admin' && $this->url->getModule() == "") 
			$this->url= new OOB_urlhandler('/about', $this->mode);

		$this->perspective = $this->url->getPerspective();
		//end url handler
$this->internalChrono('url_handler');

		$this->t = $this->newTemplate();
$this->internalChrono('template_clone');	
	
		if ($this->mode == 'user')
		$this->t->assign("webdir", $this->webaddress . $this->perspective->safeName());
		
		//security check if admin is logued
			if ($this->mode == 'admin' && $this->user == false && !in_array($this->url->realURI(),array('/','/favicon.ico','/seguridad/login_ajax'))) 
				{
					if (!isset ($_SESSION['redirecting']) && $this->url->realURI() !== '/admin/newtab' ) // no queremos que se rediriga al new_tab, xq no es una pantalla
						{
							$_SESSION['redirecting']= $this->url->realURI();
						}
						
						$this->filename= 'login.tpl';
						$this->url= new OOB_urlhandler("/seguridad/login", 'admin');
				}
			
$this->internalChrono('user_check');

//$this->internalChrono('start_ob');
//		//clean output buffer
//		@ob_clean();
//		
//		//start buffering
//		ob_start();
//
//		// ask the module for the real action 
//		$this->loadModule();
//$this->internalChrono('load_module');
//		//send module output buffer to a var
//		$this->mod_content= ob_get_clean();
//$this->internalChrono('get_content');
//		//clean output buffer again
//	
//$this->internalChrono('end_ob');

// as it seems that eval is faster than normal code, i'll just eval this part
$this->mod_content = eval ("@ob_clean();ob_start();\$this->loadModule(); return ob_get_clean();");
$this->internalChrono('eval_loadmodule');
		
		
	  	if ($this->popup == false) 
 			{
 				eval ("ob_start();return \$this->perspective->generateOutput();ob_end_flush();");
 	 		} 
 		else
			{
				eval ("ob_start();print \$this->mod_content;ob_end_flush();");
			}

	
		
$this->internalChrono('perspective_generate');
		
		if ($this->debug) {
			$this->db->debug= false;
			$this->ExecutionMonitor();
			print "</br></br><hr><h2>Performance Monitor</h2></br></br>";
			$perf= NewPerfMonitor($this->db);
			$perf->UI(5);
		}
		
$this->internalChrono('end_generate');

			$this->db->StartTrans();
				session_write_close();
			$this->db->CompleteTrans();
	

	}


	/** returns a clean template */
	public function newTemplate ()
	{
		return clone $this->plantilla;

	}
	
	static public function initEngine ($mode = "user")
	{
		global $ari;
		
		if (!is_a($ari, "oob_ari"))
		$ari = new oob_ari ($mode);
	
	}
	
	

	/**	Clears system cache	*/
	public function clearCache ($all = 'system')
  {
   		$retorno = null;
   		$contenido = ob_get_contents();
 		ob_clean();
 		ob_start();
		
		// dejamos un registro
		$this->user->error()->addError('Borrar cache para: ' . $all,true);
		
 		switch ($all)
 		{
	 		case "menu":
		 	{
			
	 			if ($handle = opendir($this->cachedir)) 
				{
					$files = "";
				   while (false !== ($file = readdir($handle))) 
				   { 
																																									
						if(strripos($file,"cache_default_admin") !== false || strripos($file,"cache_default_menu") !== false ) 						{ 
							
							unlink ($this->cachedir . DIRECTORY_SEPARATOR. $file);
						} 
				   } // end while
				   closedir($handle);
				}
	 			break;
		 	} 
	 		
	 		case "db":
		 	{
	 			$this->db->CacheFlush();
	 			break;
		 	}  
	 		
	 		default:
	 		case "system":
			{
				 if ($handle = opendir($this->cachedir)) 
				  {
				   while (false !== ($file = readdir($handle))) 
				   { 
								
						if ($file != "." && $file != ".." && $file != ".svn")  /// correccion para que no salga el dir de SUBVERSION
						{ 
							if (is_file ($this->cachedir . DIRECTORY_SEPARATOR. $file))
								{
									unlink ($this->cachedir . DIRECTORY_SEPARATOR. $file);
								}
					   } 
				   } // end while
				   closedir($handle);
				  }
				$this->db->CacheFlush();
	 			break;
			}
 		}
 		ob_clean();
 		print $contenido;
 		 
  }
  
/** Shows the execution monitor, including timings for each allocated action */
public function ExecutionMonitor ()
  {
  print '</br></br><hr></br>
					<table width="320"  border="0" cellspacing="0" cellpadding="0">  <tr>  <td colspan="3"><div align="center" class="texto4">OOB-n1 Execution Time Monitor </div></td>
  </tr>
  <tr>
    <td><div align="right" class="h2">Action</div></td>
    <td><div align="center"class="h2">Operation</div></td>
    <td><div align="center"class="h2">Global</div></td>
  </tr>';
			foreach ($this->execution as $key => $value)
			{
			print '  <tr>
    <td><div align="right"><strong>'.$key.'</strong></div></td>
    <td><div align="center">'.$value['b'].'</div></td>
    <td><div align="center">'.$value['a'].'</div></td>
  </tr>' ;
			}
			print "</table>";
  }

/** Unserializing function.
 * $db  => You can specify if you'd like to restore db connection or not ' */
public function __wakeup ()
	{
												$this->inicioCronometro();	
												$this->internalChrono('wakeup_start'); //
			
			$this->startDB();
												 $this->internalChrono('wakeup_db');
			$GLOBALS['ADODB_SESS_CONN'] = $this->db;
							session_id($_COOKIE["OOB_Session"]);
							session_start();
	
			
	//		$this->agentDetect(); 
	//											 $this->internalChrono('wakeup_agent'); //
			
//			if ($this->mode != 'cron') 
//			{
//				$this->startTemplates();		
//			} 
//			else
//			{
//				$this->perspective = new OOB_perspective ();
//			}
												$this->internalChrono('wakeup_end'); //
	}	

// should tell the ammount of memory in use. couldn't verify the results
public function memory ()
{
			   //If its Windows
			   if ( substr(PHP_OS,0,3) == 'WIN')
			   {
				   $output = array();
				   exec( 'tasklist /FI "PID eq ' . getmypid() . '" /FO LIST', $output );
		  
				   return preg_replace( '/[\D]/', '', $output[5] );           
		   		}
			   else
			   {
				   //We now assume the OS is UNIX
				   //Tested on Mac OS X 10.4.6 and Linux Red Hat Enterprise 4
				   //This should work on most UNIX systems
				   $pid = getmypid();
				   exec("ps -eo%mem,rss,pid | grep $pid", $output);
				   $output = explode("  ", $output[0]);
				   //rss is given in 1024 byte units
				   return $output[1];
			   }
}
	
}//end class
?>
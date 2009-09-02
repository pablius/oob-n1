<?php
/**
########################################
#OOB/N1 Framework [©2004,2006]
#
#  @copyright Pablo Micolini, based on original work by Philip Iezzi <pipo@phpee.com>
#  @license LGPL
######################################## 
*/

/**
* Detects user agent strings by using common known patterns and
* returns information like agent name, system type, browser version,...
*
* @author       Philip Iezzi <pipo@phpee.com>
* @copyright    Copyright (c) 2000-2004 PHPEE.COM
* @license      http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
*/

class OOB_agentdetector {

	
	private $agent = '';
	private $name = '';
	private $isBot = false;

	// Browser version
	private $v = 0; 
	
	// Browser version (as string)
	private $v_str = '';

	// Operating system
	private $sys = '';
	
	private $browserMap = array(
		'IE'     => 'Internet Explorer',
		'NS'     => 'Netscape',
		'MZ'     => 'Mozilla',
		'OP'     => 'Opera',
		'KONQ'   => 'Konqueror',
		'OMNI'   => 'OmniWeb',
		'ICAB'   => 'iCab',
		'LX'     => 'Lynx',
		'NPOS'   => 'NetPositive',
		'FASTWC' => 'FAST-WebCrawler',
		'W3C'    => 'W3C Validator'
	);


	private $systemMap = array(
		'Win'         => 'Windows',
		'Win2000'     => 'Windows 2000',
		'Win95'       => 'Windows 95',
		'Win98'       => 'Windows 98',
		'WinMe'       => 'Windows Me',
		'WinXP'       => 'Windows XP',
		'WinServ2003' => 'Windows Server 2003',
		'WinNT'       => 'Windows NT',
		'WinNT4.0'    => 'Windows NT4',
		'Mac'         => 'Mac OS',
		'MacOSX'      => 'Mac OS X',
		'Linux'      => 'Linux',
		'Solaris'      => 'Solaris',
		'Syllable'      => 'Syllable'
	);
	private $mainlanguage;
	private $acceptedlang;	

	private $parsed = false;

	private  $agt;
	
	public function __construct($agt = '', $main = 'es-ar' , $accepted = '')
	{
		$this->agent  = '';
		$this->name   = '';
		$this->isBot  = false;
		$this->v      = 0;
		$this->v_str  = '';
		$this->sys    = '';
		$this->agt = $agt;
		
		$this->mainlanguage = $main;
		$this->acceptedlang = $accepted;

	//	$this->parse($agt);
		$this->detectLang();
	}
	

	
	

	/**
	* Extract browser, version, and OS from a given agent string
	*
	* If no agent string specified, try to get the current agent information
	* from $_SERVER["HTTP_USER_AGENT"].
	*
	* @param    string       $agt   agent string
	* @return   array               agent properties array
	*/
	private function parse()
	{
	


// @todo get correct version of Safari browser
	$_agents = array(
		array("(opera) ([0-9]{1,2}.[0-9]{1,3}){0,1}"               , false , "OP"     , "%f"),
		array("(opera/)([0-9]{1,2}.[0-9]{1,3}){0,1}"               , false , "OP"     , "%f"),
		array("(konqueror)/([0-9]{1,2}.[0-9]{1,3})"                , false , "KONQ"   , "%f", "Linux"),
		array("(konqueror)/([0-9]{1,2})"                           , false , "KONQ"   , "%f", "Linux"),
		array("(NetPositive)/([0-9]{1,2}.[0-9]{1,2}.[0-9]{1,2})"   , false , "NPOS"   , "%s"),
		array("(iCab)/([0-9]{1,2}.[0-9]{1,3})"                     , false , "ICAB"   , "%f"),
		array("(Safari)/"                                          , false , "Safari" , 1.2), // XXX: version
		array("(lynx)/([0-9]{1,2}.[0-9]{1,2}.[0-9]{1,2})"          , false , "LX"     , "%s"),
		array("(links) \(([0-9]{1,2}.[0-9]{1,3})"                  , false , "Links"  , "%f"),
		array("(omniweb/)([0-9]{1,2}.[0-9]{1,3})"                  , false , "OMNI"   , "%f"),
		array("(webtv/)([0-9]{1,2}.[0-9]{1,3})"                    , false , "WebTV"  , "%f"),
		array("(msie) ([0-9]{1,2}.[0-9]{1,3})"                     , false , "IE"     , "%f"),
		array("(Netscape)/([0-9]{1,2}.[0-9]{1,3})"                 , false , "NS"     , "%f"),
		array("(netscape6)/(6.[0-9]{1,3})"                         , false , "NS"     , "%f"),
		array("Mozilla/5.+(rv:)([0-9]{1,2}.[0-9]{1,3})"            , false , "MZ"     , "%f"),
		array("Mozilla/5.+Slurp/cat"                               , true  , "Inktomi", 0),
		array("mozilla/5"                                          , false , "NS"     , 6.0),
		array("(mozilla)/([0-9]{1,2}.[0-9]{1,3})"                  , false , "NS"     , "%f"),
		array("w3m"                                                , false , "w3m"    , 0),
		array("(scooter)-([0-9]{1,2}.[0-9]{1,3})"                  , false , "Scooter"    , "%f"),
		array("(Teleport Pro)/([0-9]{1,2}.[0-9]{1,2})"             , false , "TeleportPro", "%f"),
		array("(WebCopier) v([0-9]{1,2}.[0-9]{1,2})"               , false , "WebCopier"  , "%f"),
		array("(WebStripper)/([0-9]{1,2}.[0-9]{1,2})"              , false , "WebStripper", "%f"),
		array("(WebZIP)/([0-9]{1,2}.[0-9]{1,2})"                   , false , "WebZIP"     , "%f"),
		array("(WWWOFFLE)/([0-9]{1,2}.[0-9]{1,2})"                 , false , "WWWOFFLE"   , "%f"),
		array("(Wget)/([0-9]{1,2}.[0-9]{1,2})"                     , false , "Wget"       , "%f"),
		array("(w3c_validator)/([0-9]{1,2}.[0-9]{1,3})"            , true  , "W3C"        , "%f"),
		array("(WebTrends)/([0-9]{1,2}.[0-9]{1,2})"                , true  , "WebTrends"  , "%f"),
		array("(googlebot)/([0-9]{1,2}.[0-9]{1,3})"                , true  , "Google"     , "%f"),
		array("(FAST-WebCrawler)/([0-9]{1,2}.[0-9]{1,2})"          , true  , "FASTWC"     , "%f"),
		array("(search.ch) V([0-9]{1,2}.[0-9]{1,2}.[0-9]{1,2})"    , true  , "Search.ch"  , "%s"),
		array("(SpaceBison)/([0-9]{1,2}.[0-9]{1,2})"               , true  , "SpaceBison" , "%f"),
		array("(SuperBot)/([0-9]{1,2}.[0-9]{1,2})"                 , true  , "SuperBot"   , "%f")
	);

	$_systems = array(
		// array(pattern, name)
		array("linux"                                              , "Linux"),
		array("Win 9x 4.90"                                        , "WinMe"),
		array("win32"                                              , "Win"),
		array("windows 2000"                                       , "Win2000"),
		array("(win)([9][5,8])"                                    , "Win%s"),
		array("(windows) ([9][5,8])"                               , "Win%s"),
		array("(windows nt)( ){0,1}(5.0)"                          , "Win2000"),
		array("(windows nt)( ){0,1}(5.1)"                          , "WinXP"),
		array("(windows nt)( ){0,1}(5.2)"                          , "WinServ2003"),
		array("windows XP"                                         , "WinXP"),
		array("(winnt)([0-4]{1,2}.[0-9]{1,2}){0,1}"                , "WinNT%s"),
		array("windows nt( ){0,1}([0-4]{1,2}.[0-9]{1,2}){0,1}"     , "WinNT%s"),
		array("PPC Mac OS X"                                       , "MacOSX"),
		array("PPC"                                                , "MacPPC"),
		array("Mac_PowerPC"                                        , "MacPPC"),
		array("mac"                                                , "Mac"),
		array("(sunos) ([0-9]{1,2}.[0-9]{1,2}){0,1}"               , "SunOS%s"),
		array("(beos) r([0-9]{1,2}.[0-9]{1,2}){0,1}"               , "BeOS%s"),
		array("freebsd"                                            , "FreeBSD"),
		array("openbsd"                                            , "OpenBSD"),
		array("irix"                                               , "IRIX"),
		array("os/2"                                               , "OS/2"),
		array("plan9"                                              , "Plan9"),
		array("unix"                                               , "Unix"),
		array("hp-ux"                                              , "Unix"),
		array("osf"                                                , "OSF"),
		array("X11"                                                , "Unix"),
		array("Syllable"                                           , "Syllable")
	);
		
		if(!$this->agt) $agt = $_SERVER["HTTP_USER_AGENT"];
		$this->agent = $this->agt;
		
		// agent detection
		foreach($_agents as $arrAgent) {
			if(eregi($arrAgent[0], $this->agent)) {
				$this->isBot = $arrAgent[1];
				$this->name = $arrAgent[2];
				if (is_string($arrAgent[3])) {
					$this->v_str = sprintf($arrAgent[3], $regs[2]);
					$this->v = (float) $this->v_str;
				} else {
					$this->v = (float) $arrAgent[3];
				}
				if(isset($arrAgent[4])) $this->sys = $arrAgent[4];
				break; // agent detected, done. Jump out of the foreach loop
			}
		}
		// system detection
		if(!$this->sys) {
			foreach($_systems as $arrSystem) {
				if(eregi($arrSystem[0], $this->agent, $regs)) {
					$this->sys = (isset($regs[2])) ? sprintf($arrSystem[1], $regs[2]) : $arrSystem[1];
					break; // system detected, done. Jump out of the foreach loop
				}
			}
		}
		
		$outp = array(
			$this->name,                           // agent name
			$this->isBot,                          // is a bot?
			$this->v,                              // version as float
			$this->v_str,                          // version as string
			$this->sys                             // system
		);
$this->parsed = true;
		return($outp);
	}
	/**
	* gets the agent
	*
	* @return   string
	*/
	public function getAgent()
	{
	if (!$this->parsed)
		{$this->parse();}

	return($this->agent);
	}
	/**
	* gets the agent name
	*
	* @return   string
	*/
	public function getName()
	{
	if (!$this->parsed)
		{$this->parse();}

		return($this->name);
	}
	/**
	* gets the full agent name
	*
	* @return   string
	*/
	public function getFullname($name = '')
	{
	if (!$this->parsed)
		{$this->parse();}

		if(!$name) $name = $this->name;
		return (@$this->browserMap[$name]) ? $this->browserMap[$name] : $name;
	}
	/**
	* gets the operating system
	*
	* @return   string
	*/
	public function getSystem()
	{
	if (!$this->parsed)
		{$this->parse();}

		return($this->sys);
	}
	/**
	* gets the full system name
	*
	* @return   string
	*/
	public function getFullsystem()
	{
	if (!$this->parsed)
		{$this->parse();}

		return (@$this->systemMap[$this->sys]) ? $this->systemMap[$this->sys] : $this->sys;
	}
	/**
	* is agent a bot?
	*
	* @return   boolean
	*/
	public function isBot()
	{
	if (!$this->parsed)
		{$this->parse();}

		return($this->isBot);
	}
	/**
	* gets the browser version
	*
	* @return   string
	*/
	public function getVersion($asString = FALSE)
	{
	if (!$this->parsed)
		{$this->parse();}

		if ($asString && ($this->v_str > '')) {
			return($this->v_str);
		} else {
			return($this->v);
		}
	}
	
	public function getLang()
	{
		return($this->getSystemLanguage());
	}
	
	public function setLang($value)
	{
			if (in_array ($value, $this->getLanguages()))
			{
			 $this->language = $value;
			}
	}
	
	
	public function getSelectedLang ()
	{
		if (isset($_SESSION['idioma_instancia']))
			{$return = $_SESSION['idioma_instancia'];}
		else
			{$return =  $this->language;}
	return $return;
	}
	

	public function setSelectedLanguage ($value)
	{
		if (in_array ($value, $this->getLanguages()))
			{$_SESSION['idioma_instancia'] = $value;
			$this->setSystemLanguage($value);
			}
	
	}
	
	
	public function setSystemLanguage ($value)
	{
		if (in_array ($value, $this->getLanguages()))
			{$_SESSION['idioma_system'] = $value;
			$_SESSION['idioma_instancia'] = $value;}
	
	}
	
	public function getSystemLanguage ()
	{
		if (isset($_SESSION['idioma_system']))
			{$return = $_SESSION['idioma_system'];}
		else
			{$return =  $this->language;}
	return $return;
	}
	
	
	
	/**
	* Get all agent information
	* for debugging purposes
	*
	* @return   string
	* @see      printInfo()
	*/
	public function getInfo()
	{
	if (!$this->parsed)
		{$this->parse();}

		$str = 'agent................:'.htmlspecialchars($this->getAgent())."\n" .
		       'name.................:'.$this->getName()."\n" .
			   'full name............:'.$this->getFullname()."\n" .
			   'is a bot.............:'.(($this->isBot()) ? 'true' : 'false')."\n" .
			   'version..............:'.$this->getVersion()."\n" .
			   'system...............:'.$this->getSystem()."\n" .
			   'lang...............:'.$this->getLang()."\n" .
			   'full system..........:'.$this->getFullsystem()."\n";
		return $str;
	}
	/**
	* Print all agent information
	* for debugging purposes
	*
	* @return   void
	* @see      getInfo()
	*/
	public function printInfo()
	{
		echo '<pre>';
		echo $this->getInfo();
		echo '</pre>';
	}
	
	private function detectLang()
	{
	
		$langs = explode (',' , $this->mainlanguage . "," . $this->acceptedlang);

		$fallback = $this->mainlanguage;
		
		$http_accept_language = strtolower(@$_SERVER['HTTP_ACCEPT_LANGUAGE']);
		
		$this->language = $fallback;
	//	unset ($finalreal);
	//	unset ($finalsecundario);
		
		if(!empty($http_accept_language) && count($langs) > 0) {
			
				$accepted_languages = explode (',', $http_accept_language);
				reset ($accepted_languages);
			

				foreach ($langs as $posible)
				{
					//prueba tal cual
				foreach ($accepted_languages as $cliente)
					{
						if ($posible == $cliente)
						{
						$finalreal = $posible;
						break;
						}
					}	
					
					//prueba las 2 primeras letras
					$posibleori = $posible;
					$posible = substr($posible, 0, strpos($posible, '-'));	
					
				foreach ($accepted_languages as $cliente)
					{
					$cliente = substr($cliente, 0, strpos($cliente, '-'));	
						if ($posible == $cliente)
						{
						$finalsecundario = $posibleori;
						break;
						}
					}	
					
				if (isset($finalreal) || isset($finalsecundario))
				break;				
								
				}
			
			if (isset ($finalreal))
			$this->language = $finalreal;
			
			if (!isset ($finalreal)&& isset($finalsecundario))
			$this->language = $finalsecundario;

			}

	}
	
	/** Devuelve todos los languages aceptados */ 
	public function getLanguages()
	{
		if ($this->acceptedlang != '' || $this->mainlanguage != '')
		{
			$array_lang = explode(',', $this->acceptedlang);
			$array_lang[] = $this->mainlanguage;
			$array_lang = array_unique($array_lang);
			return  $array_lang;
		} 
		else
		{
			return false;
		}
	}	
	
			public function mainLanguage()
	{
		return $this->mainlanguage;	
	}
	
}
?>
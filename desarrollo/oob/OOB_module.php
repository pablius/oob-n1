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
 this class handles the modules in the system 
*/
 class OOB_module {
private $filesdir;
private $mode;
private $module;
private $perspective;
private $lang;
private $config;
private $loaded;
private $nicename = '';
private $status =0;
private $optional ='';
private $description ='';
private $address;
 	 
 /** loads module , and verifies if it exists (default = true)*/
 public function __construct ($module = NULL) {
 global $ari;
 $this->filesdir = $ari->filesdir;
 $this->mode = $ari->mode;
 $this->module = $module;
 $this->perspective = $ari->perspective->name();
 $this->lang = $ari->agent->getLang();
 $this->address = $this->filesdir.DIRECTORY_SEPARATOR.'modulos'.DIRECTORY_SEPARATOR.$this->module.DIRECTORY_SEPARATOR;
 
 // the admin module must be enabled to be used, 
 // but as is not filled, the var must be set here.
 if ($module == 'admin')
 {$this->status = 1;}
 
 if ($module != NULL ) // The admin module isn't really a module  (changed my mind, admin module is a module  -< && $module != 'admin' >-
 	{
 		$this->fill(); }
  
  }
  
  /** lists the modules that the db has loaded, status gets enabled, disabled or  all */
 public static function listModules ($status = 'enabled', $asobject = true, $order="nicename") {
 global $ari;
 $estado = 'WHERE status = 1'; 
 
 if ($status == 'disabled')
 $estado =  'WHERE status = 0';
 
 if ($status == 'all')
 $estado =  '';
 
 switch($order)
 {
 	case "nicename":
 	case "description":
 	case "status":
 	{
 		break;
 	}	
 	default:
 	{
 		$order = "nicename";
 		break;	
 	}
 }

 $savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
		$sql= "SELECT modulename FROM OOB_modules_config $estado ORDER BY $order";

		
		
		
			$rs = $ari->db->Execute($sql);
			$ari->db->SetFetchMode($savem);
			$modulos = false;
			if ($rs && !$rs->EOF){ // aca cambie sin probar, hay q ver si anda!
			
					while (!$rs->EOF){
			 
					$modulos[] = new OOB_module ($rs->fields[0]);
					//if permission
					$rs->MoveNext();
					}
					
			$rs->MoveNext();
			} else
			return false;
	if ($asobject)
	return $modulos;
	else
	{
		# This mode is used to generate the module list on the admin section.
		foreach ($modulos as $module)
			{
			$return["name"][]= $module->nicename();
			$return["value"][]= $module->name();
			$return["selected"] = $ari->get("url")->getModule();
			}
		return $return;
	
	}
 
 }
 
 /** generates the module selector for the admin interface */
 public static function adminModulesSelector () {

global $ari;

//		$return  = OOB_module::listModules ('enabled', false);

$roles = array();
$mismodulos = array();
$return = false;

if ($ari->get('user') == false ) // no hay caso en hacer el menu si no hay usuario logueado!
{return false;}
//-----------
// cache del selector para cada usuario

// Set a id for this cache
$id = 'admin__' . $ari->agent->getLang() . '__'. $ari->user->get('user') . '.php' ;

//Set cache options
$options = array(
				'cacheDir' => $ari->cachedir . DIRECTORY_SEPARATOR,
				'lifeTime' => SQL_CACHE,
				'fileNameProtection' => false,
				'automaticSerialization' => true
);

// 'onlyMemoryCaching' => true,
//  'memoryCaching' => true


		// Create a Cache_Lite object
		$Cache_Lite = new Cache_Lite($options);
		
		// Test if thereis a valide cache for this id
		if ($return = $Cache_Lite->get($id)) {
		
				$return["selected"] = $ari->module->name();
				$return["selectedName"] = $ari->module->nicename();
		  		return ($return);
		
		} else { // No valid cache found 
		

				if ($roles = seguridad_role::myRoles ($ari->get('user')) )
				{
					foreach ($roles as $role)
					{
						$mods = seguridad_role::listModulesFor ($role);
						
						if ($mods != false)
							{$mismodulos = array_merge ($mismodulos, $mods);}
			
					}
				}
				//$mismodulos = array_unique ($mismodulos);
				//sort ($mismodulos);
				
				// in some weird situations array_unique doesn't work, probably because
				// it s an object array
				$out = array();
   				$list = array();
   				foreach ($mismodulos as $key=>$so) 
   				{
       				if (!in_array($so->name(), $list)) 
       						{
          		 			$list[] = $so->name();
          					 $out[$key] = $so;
       						}
   				}
   				$mismodulos = $out; 
				
				//
				
				
					
				if (count ($mismodulos) > 0)
					{
					foreach ($mismodulos as $modula)
						{
					//	$module = new OOB_module($module);
						if ($modula->isenabled())
							{
								$return["name"][]= $modula->nicename();
								$return["value"][]= $modula->name();
							}
						
						}
					 $Cache_Lite->save($return);
				
						$return["selected"] =  $ari->module->name();
						$return["selectedName"] = $ari->module->nicename();			
					}


				
				return $return;
		}

	
	
 }
 
 
 
 
 public static function adminFullMenu () 
 
 {

	global $ari;
	
	//		$return  = OOB_module::listModules ('enabled', false);
	
	$roles = array();
	$mismodulos = array();
	$return = false;
	
	if ($ari->get('user') == false ) // no hay caso en hacer el menu si no hay usuario logueado!
	{
		return false;
	}
	//-----------
	// cache del menu para cada usuario
	
	// Set a id for this cache
	$id = 'admin_fullmenu__' . $ari->agent->getLang() . '__'. $ari->user->get('user') . '.php' ;
	
	//Set cache options
	$options = array(
					'cacheDir' => $ari->cachedir . DIRECTORY_SEPARATOR,
					'lifeTime' => 9000,
					'fileNameProtection' => false,
					'automaticSerialization' => true
	);
	
	// 'onlyMemoryCaching' => true,
	//  'memoryCaching' => true
	
	
			// Create a Cache_Lite object
	$Cache_Lite = new Cache_Lite($options);
	
	
			
			// Test if thereis a valide cache for this id
	if ($return = $Cache_Lite->get($id)) 
	{
		return ($return);
			
	}
	else
	{ // No valid cache found 
			
		if ($roles = seguridad_role::myRoles ($ari->get('user')) )
		{
			foreach ($roles as $role)
			{
				if ($mods = seguridad_role::listModulesFor ($role))
				{
					$mismodulos = array_merge ($mismodulos, $mods);
				}
	
			}
		}
										
		// in some weird situations array_unique doesn't work, probably because
		// it s an object array
		$out = array();
		$list = array();
		foreach ($mismodulos as $key=>$so) 
		{
			if (!in_array($so->name(), $list)) 
			{
				$list[] = $so->name();
				$out[$key] = $so;
			}
		}
		
		$mismodulos = $out; 
						
		if (count ($mismodulos) > 0)
		{
			$i = 0;
			foreach ($mismodulos as $modula)
			{

				$menu = $modula->adminMenu();

				if ($modula->isenabled() && count($menu))
				{
					$return[$i]["name"]= $modula->nicename();
					$return[$i]["id"]= $modula->name();
					$return[$i]['menu'] = $menu;
					$i++;
				}
			
			}
			
			
		}
	
	
		$Cache_Lite->save($return);		

		return $return;
	}
	
 }
 
 
 /** iterates modules dir, to see if there are new modules */
 public function updateModulesList () {

 // iterate throught "modulos" dir to see whats available.
  if ($handle = opendir($this->filesdir . DIRECTORY_SEPARATOR . 'modulos')) {
   while (false !== ($file = readdir($handle))) 
   { 
        if ($file != "." && $file != ".." && $file != ".svn")  /// correccion para que no salga el dir de SUBVERSION
        { 
       		if (file_exists ($this->filesdir . DIRECTORY_SEPARATOR . 'modulos' . DIRECTORY_SEPARATOR . $file . DIRECTORY_SEPARATOR . 'module.conf'))
     			 $availables[] = $file;
       } 
   }
   closedir($handle); 
 }


 
 // get the modules on the DB
 if ($installedobjects = $this->listModules ('all'))
{
 	foreach ($installedobjects as $inst)
		{ $installed[] = $inst->name();
		}



 // compare the arrays
 $add = array_diff ($availables,$installed); // mal el dato, ya se corrigió!

}
else
$add = $availables;
 // add the new modules to the DB


 foreach ($add as $new)
 $this->addModule ($new);
 
 }
 
 /** installs a module */
 private function addModule ($module) { // as its private we trust the module exists
 global $ari;
 
 $config = new OOB_config ($this->filesdir . DIRECTORY_SEPARATOR . 'modulos' . DIRECTORY_SEPARATOR.  $module . DIRECTORY_SEPARATOR . 'module.conf');	

	$description = $ari->db->qMagic($config->get('description', 'definition'));
	$primari = $config->get('primary', 'definition');
	$nicename = $ari->db->qMagic($config->get('name', 'definition'));
	
	$optional = 1;
	$status = 0;
	if ($primari == 'yes')
	{
		$optional = 0;
		$status = 1;
	} 
	
	$module = $ari->db->qMagic($module); // we dont trust you THAT much 
	


					$ari->db->StartTrans();
										
					$sql= "
						   INSERT INTO `OOB_modules_config` 
						   ( `modulename` , `status` , `nicename` , `description` , `optional` ) 
							VALUES ( $module, $status, $nicename, $description, $optional )
							";
						   	
						   	
					$ari->db->Execute($sql);

					if (!$ari->db->CompleteTrans())
					throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "020", "Error en la Base de Datos", false); //return false; 
 }
 
 /** retunrs module config object */ 
 public function config () {
 return $this->config;
 }
 
 /** returns the module admin menu */
 public function adminMenu () {
 	global $ari;
 
 if ($ari->get('user') == false || is_a($ari->module,'OOB_module') == false)
		{return false;}
 
 $menu ="";	
 $array_menu = array();

 // loads localized array
@include ($this->admindir() . DIRECTORY_SEPARATOR . 'menu' . DIRECTORY_SEPARATOR .$ari->agent->getLang() .  '.php');
		
// cache del menu para cada modulo en cada usuario

// Set a id for this cache
$id = 'menu__' . $ari->agent->getLang() . '__'. $ari->user->get('user') .'-'. $this->name() .'.php' ;

//Set cache options
$options = array(
				'cacheDir' => $ari->cachedir . DIRECTORY_SEPARATOR,
				'lifeTime' => SQL_CACHE,
				'fileNameProtection' => false,
				'automaticSerialization' => true
				// 'onlyMemoryCaching' => true,
				//  'memoryCaching' => true
);

		// Create a Cache_Lite object
		$Cache_Lite = new Cache_Lite($options);
		
		// Test if thereis a valide cache for this id
		if ($menu = $Cache_Lite->get($id)) {
		
		  		return ($menu);
		
		} else 
		
		{ // No valid cache found 
	
					if ($permissions = seguridad_permission :: listPermissionsFor($this))
						{
							
							$menu = array();
							$i = 0;
							foreach($permissions as $p)
							{
								
								if ($actions = seguridad_action :: listActionsFor($p, IN_MENU))
								{
									foreach($actions as $a)
									{
									
										 if (seguridad :: isAllowed($a, $ari->perspective) )
										 {
										 	if (!count($array_menu)==0 )
										 	{	
										 		$menu[$i]['name']= $array_menu[$p->get("name") . $a->get("name")];	}
										 	else
										 	{ $menu[$i]['name']= $a->get("nicename");	}
						 				 	$menu[$i]['link']= $this->name() . "/" . $p->get("name") . "/" . $a->get("name");
						 				 	$i++;
										 }	
									}//end foreach $actions
								}
							}//end foreach $permissions
						}
						if (count($menu) == 0)
						{
							$menu = false;

						}
						
		 $Cache_Lite->save($menu);
		return $menu;
		} // end cache


 }
 
  /** shows the module admin menu */
 public function adminSubMenu () {}
 
  /** enables module */
 public function enable () {
  if ($this->status == 0)
  {
  //update DB
  $this->updateField("status","1");
  }
  else
  return true;
  }
 
  /** disables module */
 public function disable () {
  if ($this->status == 1)
  {
  //update DB
  $this->updateField("status","0");
  }
  else
  return true;
  
 }
 
 /** update field */
 private function updateField ($field, $value) {
global $ari;
 $value = $ari->db->qMagic ($value);
 
$sql = "UPDATE `OOB_modules_config` ";
$sql .= "SET $field = $value ";
$sql .= "WHERE ModuleName = '$this->module' ";
$ari->db->StartTrans();
$ari->db->Execute($sql);

if (!$ari->db->CompleteTrans())
	throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "020", "Error en la Base de Datos", false); //return false;
  
}
 
 
  /** gets module status (enabled/disabled) */
 public function isenabled () {

 if ($this->status == 1)
 	return true;
else
	return false;
 }

 /** bool : returns true if module is optional, so can be disabled */
 public function optional () {
 if ($this->optional == 1)
 return true;
 else
 return false;
 }
 
 /** shows module description from db */
 public function description () {
 return $this->description;
 }
 
  /** shows module name from db*/
 public function name () {
 return $this->module;
	}
	
/** shows module "nice" name from db*/
 public function nicename () {
 return $this->nicename;
	}
	
/** Sets the variable (var), with the value (value) */ 	
public function set ($var, $value)
{
	$this->$var= $value;
}
  	
 /** Module Classes Dir */
 public function oodir () {
	return $this->address . "oo" ; 		
 }
  /** Module User Files Dir */
 public function userdir()
 {  
 	return $this->address. "user";
 }
  /** Module User Templates Dir */
 public function usertpldir ()
 {
  if (is_dir ($this->userdir() . DIRECTORY_SEPARATOR ."templates" . DIRECTORY_SEPARATOR. $this->perspective . DIRECTORY_SEPARATOR .$this->lang))
  return $this->userdir() . DIRECTORY_SEPARATOR ."templates" . DIRECTORY_SEPARATOR. $this->perspective . DIRECTORY_SEPARATOR .$this->lang;
  else
  return $this->userdir() . DIRECTORY_SEPARATOR ."templates" . DIRECTORY_SEPARATOR. 'default' . DIRECTORY_SEPARATOR .$this->lang;
	
 }
  /** Module Admin Files Dir */
 public function admindir ()
 {
 	return $this->address."admin" ;
 }
  /** Module Admin Templates Dir */
 public function admintpldir ()
 {
 	return $this->admindir() . DIRECTORY_SEPARATOR ."templates" . DIRECTORY_SEPARATOR .$this->lang;
 }
 
 /** loads data from DB */
 private function fill()
 {
 	global $ari;
 		
 	 	if (!@is_dir($this->address)) //.$this->mode.DIRECTORY_SEPARATOR.'url.php'))
 	 	throw new OOB_exception('', "404", 'Módulo inexistente.');
 	 else
 	 {
 	  $this->config =  new OOB_config ($this->address . 'module.conf');
 	  $savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
		$sql= "SELECT status, description, optional, nicename 
			   FROM OOB_modules_config WHERE modulename = '$this->module'";
			   
			$ari->db->SetFetchMode($savem);
	//		$rs = $ari->db->Execute( $sql);
			$rs = $ari->db->CacheExecute(SQL_CACHE,$sql);

			if ($rs && !$rs->EOF) {
					$this->status = $rs->fields[0];
					$this->description = $rs->fields[1];
					$this->optional = $rs->fields[2];
					$this->nicename = $rs->fields[3];
				
					$rs->Close();
			} else
			throw new OOB_exception('', "501", 'Módulo inexistente.');

 	 
 	 }
 }
 
/** Shows the available status for modules, or all */	
static public function getViews()
{
	$return[] = "all";
	$return[] = "disabled";
	$return[] = "enabled";
	return $return;
}

/** Shows the available sorting ways for modules */
static public function getOrders()
{
	$return[] = "nicename";
	$return[] = "name";
	$return[] = "description";
	$return[] = "status";
	
	return $return;
}

/**  */
static public function includeClass($module,$class)
{
	//@todo: falta verificar q exista la clase y el modulo
	// $module = new OOB_module($module);
	if (!class_exists($class, false))
	{
	global $ari;
	$file = $ari->filesdir . DIRECTORY_SEPARATOR . 'modulos' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR. 'oo' . DIRECTORY_SEPARATOR . $class . '.php';

	if (file_exists ($file))
	{include_once ($file);}
	}
}

 
}//end class
?>

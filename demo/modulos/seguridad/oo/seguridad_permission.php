<?php
/**
########################################
#OOB/N1 Framework [©2004,2006]
#
#  @copyright Pablo Micolini
#  @license BSD
######################################## 
*/

  class seguridad_permission 
  {
 
 	private $permission = ID_UNDEFINED;
	private $name = '';
	private $nicename = '';
	private $modulename = '';

	/** Starts the permission. 
 	    if no role set we must believe is a new one */ 	
 	public function __construct ($permission = ID_UNDEFINED)
 	{
 		global $ari;

		if ($permission > ID_MINIMAL) {
			$this->permission= $permission;
			
			if (!$this->fill ())
			{throw new OOB_exception("Invalid permission {$permission}", "403", "Invalid Permission", false);}
					
		}  
 	}
 	
 	/** Returs the value for the given var */ 	
 	public function get ($var)
 	{
 		if (isset ($this-> $var) && !empty ($this-> $var))
			return $this-> $var;
		else
			return false;
 	}
	
 	/** Fills the permission with the DB data */ 	
 	private function fill ()
 	{
		global $ari;

		//load info
		$permission_id =$ari->db->qMagic($this->permission);
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$sql= "SELECT ModuleName, Name, NiceName 
			   FROM security_permission 
			   WHERE id = $permission_id";
			   
		$rs = $ari->db->Execute($sql);
		
		$ari->db->SetFetchMode($savem);
		if (!$rs || $rs->EOF) {
			return false;
		}
		if (!$rs->EOF) {
			$this->modulename = $rs->fields["ModuleName"];
			$this->name = $rs->fields["Name"];
			$this->nicename = $rs->fields["NiceName"];
		}
		$rs->Close();
		return true; 		
 	}
 	
 	/** Sets the variable (var), with the value (value) */ 	
 	public function set ($var, $value)
 	{
		if (isset ($this->$var))
			$this->$var= $value;
		else
			return false; 	
 	}
	
	/** Lista Permisos */
	static public function listPermission ($sort = 'NiceName', $operator = OPERATOR_EQUAL)
	{
		global $ari;			
		
		$sortby = "ORDER BY `$sort`";		
		
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$sql= "SELECT `ID`, `ModuleName`, `Name`, `NiceName`
			   FROM `Security_permission` 
			   $sortby";
		
		$rs = $ari->db->Execute($sql);

		$ari->db->SetFetchMode($savem);
		$i = 0;
		if ($rs && !$rs->EOF) 
		{ 
			while (!$rs->EOF) 
			{		
	
				$return[$i] = new seguridad_permission(ID_UNDEFINED);
				$return[$i]->set("permission",$rs->fields["ID"]);
				$return[$i]->set("modulename",$rs->fields["ModuleName"]);
				$return[$i]->set("name",$rs->fields["Name"]);
				$return[$i]->set("nicename",$rs->fields["NiceName"]);				
				$i++;
				$rs->MoveNext();
			}			
		} 
		else
		{$return = false;}
		$rs->Close();
		return $return;
	}
 	//end function
	
	
	static public function permissionCount ( $sort = 'NiceName', $text = "")
	{
	
		global $ari;
		$sortby = "ORDER BY $sort";
		

			$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
			$sql= "SELECT id FROM `Security_permission` $sortby";
			$rs = $ari->db->Execute($sql);

			$ari->db->SetFetchMode($savem);
				if ($rs && !$rs->EOF) { 
					$return = $rs->RecordCount();	
			$rs->Close();
			} else
			{return false;}

		return $return;
	}
 	 	
 	/**list permissions of the $module*/
 	static public function listPermissionsFor ($module)
 	{
		global $ari;
		
		$moduleName = $ari->db->qMagic($module->name());
		$savem = $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		
		$sql = "SELECT ID, ModuleName, Name, NiceName 
				FROM Security_Permission 
        		WHERE ModuleName = $moduleName 
				ORDER BY NiceName";

		$rs = $ari->db->Execute($sql);

		$ari->db->SetFetchMode($savem);
		$i = 0;
		
		if ($rs && !$rs->EOF) 
		{ 
			while (!$rs->EOF) 
			{
				$return[$i] = new seguridad_permission(ID_UNDEFINED);
				$return[$i]->set("permission",$rs->fields["ID"]);
				$return[$i]->set("name",$rs->fields["Name"]);
				$return[$i]->set("nicename",$rs->fields["NiceName"]);
				$return[$i]->set("modulename",$rs->fields["NiceName"]);
				$i++;	
				$rs->MoveNext();
			}			
		} 
		else
		{$return = false;}
		$rs->Close();
		return $return;
	}
 	//end function
 	
	/**	graba los datos en la Base de Datos */	
	public function store ()
	{
		global $ari;
		// clean vars !

		$this->name = trim($this->name);
		$this->nicename = trim($this->nicename);

		if (!OOB_validatetext :: isCorrectLength ($this->modulename, 1, MAX_LENGTH))
		{	$ari->error->addError ("seguridad_permission", "INVALID_MODULE");
		} 
		
		if (!OOB_validatetext :: isClean($this->name) || !OOB_validatetext :: isCorrectLength ($this->name, 1, MAX_LENGTH))
		{	$ari->error->addError ("seguridad_permission", "INVALID_NAME");
		} 
		 
		if (!OOB_validatetext :: isClean($this->nicename) || !OOB_validatetext :: isCorrectLength ($this->nicename, 1, MAX_LENGTH))
		{	$ari->error->addError ("seguridad_permission", "INVALID_NICENAME");
		} 		

		//valido q no exista la el permiso
		if ($this->permission == ID_UNDEFINED) 					
		{//para nuevo busco uno con el mismo nombre
			$clausula = "";
		}
		else
		{//si actualizo busco con el mismo nombre pero con el mismo id
			$clausula = " AND id <> $this->permission";	
		}
		
		$savem = $ari->db->SetFetchMode(ADODB_FETCH_NUM);
		$name = $ari->db->qMagic($this->name);
		$modulename = $ari->db->qMagic($this->modulename);
		
		$sql= "SELECT true as permiso FROM security_permission 
			   WHERE name = $name and modulename = $modulename $clausula";
//		echo $sql;
//		exit;
		$rs = $ari->db->Execute($sql);
		$ari->db->SetFetchMode($savem);

		if (!$rs->EOF && !$rs == false) 
		{
			$ari->error->addError ("seguridad_permission", "DUPLICATE_PERMISSION");
		}// end if
					
		if (!$ari->error->getErrorsfor("seguridad_permission"))
		{
			$modulename = $ari->db->qMagic($this->modulename);
			$name = $ari->db->qMagic($this->name);
			$nicename = $ari->db->qMagic($this->nicename);
			 				
			if ($this->permission > ID_MINIMAL)
			{
				// update data
				$permission_id =$ari->db->qMagic($this->permission);
				$ari->db->StartTrans();
				$sql= "UPDATE security_permission
					   SET ModuleName = $modulename,
					   	   Name = $name,
					   	   NiceName = $nicename,
					   WHERE id = $permission_id";
					   
				$ari->db->Execute($sql);
					
							
				if (!$ari->db->CompleteTrans())
				{	throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false);	} //return false;
				else
				{	return true;	}	
					
			} 
			else 
			{
				// insert new and set permissionid with new id
				$ari->db->StartTrans();
									
				$sql= "INSERT INTO security_permission 
				  ( modulename,name, nicename)
				  VALUES ($modulename, $name, $nicename)";
					   	
				$ari->db->Execute($sql);
				$this->permission = $ari->db->Insert_ID();
			
				if (!$ari->db->CompleteTrans())
				{	throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false); 	}//return false;
				else
				{
				return true;
				}
			}//end if
		} 
		else 
		{
			// no validan los datos
			 return false; //devuelve un objeto de error con los errores!
		}//end if
	}//end function	


	/**	Sincroniza las tablas de permisos y acciones de 2 DB distintas 
		Recibe opcionalmente el nombre del modulo del cual se quiere 
		sincronizar sus permsisos y acciones o un array de modulos de los que se quiere 
		sincronizar sus permisos y acciones
	 	
		** Nota:
		1. Copiar la tabla security_permission de la DB origen y renombrarla 
		   en DB destino como security_permission_other
		2. Copiar la tabla security_action de la DB origen y renombrarla 
		   en DB destino como security_action_other	
			
	 */	
	public static function synchronizePermissionsActions($arrayModules = false)
	{
		global $ari;
		$ari->db->StartTrans();
		self::synchronizePermissions($arrayModules);
		self::synchronizeActions($arrayModules);
		if (!$ari->db->CompleteTrans())
		{	throw new OOB_exception("Error en DB al intentar sincronizar permisos y acciones: $ari->db->ErrorMsg()", "010", "Error en Base de Datos al intentar sincronizar permisos y acciones.", true); 
		}
		else
		{	echo ("<br><hr size=1><h2>Proceso de sincronización finalizado con éxito!</h2><hr size=1>");	
			exit;
		}
	}

	/**	Sincroniza las tablas de permisos y acciones
	 */	
	public static function synchronizePermissions($arrayModules = false)
	{
		global $ari;
		
		$clause = "";
		if($arrayModules)
		{	
			if(!is_array($arrayModules))
			{//es un solo modulo	
				$moduleName = $ari->db->qMagic($arrayModules);
				$clause = " AND origen.modulename = $moduleName ";
			}
			else
			{//es un array con los nombres de los modulos de los permisos a sincronizar
				
				$first = true;
				$lista = false;
				foreach($arrayModules as $moduleName)
				{	
					$moduleName = $ari->db->qMagic($moduleName);
					if($first)
					{	$lista = $moduleName;
						$first = false;
					}
					else
					{	$lista = $lista . "," . $moduleName;
					}
				}
				
				if($lista)
				{	$clause = " AND origen.modulename IN ($lista) ";
				}
			}
		}
		
		//consultar los permisos que estan en la tabla security_permission_other 
		//pero que no estan en la tabla security_permission
			//ID, ModuleName, Name, NiceName
		$sql = "SELECT origen.* 
				FROM security_permission_other origen
				WHERE 1=1 
				$clause
				AND NOT EXISTS
								(	SELECT 1
									FROM security_permission destino
									WHERE destino.name = origen.name 
									AND destino.modulename = origen.modulename
								)
				";
				
		//echo $sql;
		//exit;
		
		$savem = $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$rs = $ari->db->Execute($sql);
		$ari->db->SetFetchMode($savem);
		if ($rs && !$rs->EOF) 
		{
			while (!$rs->EOF) 
			{
				$permission = new seguridad_permission(ID_UNDEFINED);
				$permission->set("name", $rs->fields["Name"]);
				$permission->set("nicename", $rs->fields["NiceName"]);
				$permission->set("modulename", $rs->fields["ModuleName"]);
				$permission->store();
				
				$permissionID = $ari->db->qMagic($rs->fields["ID"]);
				
				//consultar las acciones del permiso actual 
				//ID, PermissionID, Name, NiceName, inMenu
				$sql = "SELECT ID, Name, NiceName, InMenu
						FROM security_action_other 
						WHERE PermissionID = $permissionID 
					   ";

				$savem = $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
				$rs2 = $ari->db->Execute($sql);
				$ari->db->SetFetchMode($savem);
				if ($rs2 && !$rs2->EOF) 
				{
					while (!$rs2->EOF) 
					{
						$action = new seguridad_action(ID_UNDEFINED);
						$action->set('name', $rs2->fields["Name"]);
						$action->set('nicename', $rs2->fields["NiceName"]);
						$action->set('inmenu', $rs2->fields["InMenu"]);
						$action->set('permission', $permission);
						$action->store();
						
						$rs2->MoveNext();
						
					}//end while actions
					$rs2->Close();
				}

				$rs->MoveNext();
				
			}//end while permissions
			$rs->Close();
		}
					
	}//end function	

	/**	Sincroniza las tablas de acciones
	 */	
	public static function synchronizeActions($arrayModules = false)
	{
		global $ari;
		
		$clause = "";
		if($arrayModules)
		{	
			if(!is_array($arrayModules))
			{//es un solo modulo	
				$moduleName = $ari->db->qMagic($arrayModules);
				$clause = " AND p_origen.modulename = $moduleName ";
			}
			else
			{//es un array con los nombres de los modulos de los permisos a sincronizar
				
				$first = true;
				$lista = false;
				foreach($arrayModules as $moduleName)
				{	
					$moduleName = $ari->db->qMagic($moduleName);
					if($first)
					{	$lista = $moduleName;
						$first = false;
					}
					else
					{	$lista = $lista . "," . $moduleName;
					}
				}
				
				if($lista)
				{	$clause = " AND p_origen.modulename IN ($lista) ";
				}
			}
		}
		

		//security_permission:  ID, ModuleName, Name, NiceName
		//security_action: 		ID, PermissionID, Name, NiceName, inMenu
		$sql = "SELECT a_origen.Name AS aName, a_origen.NiceName AS aNiceName, a_origen.inMenu AS aInMenu,
					   p_origen.Name AS pName, p_origen.ModuleName AS pModuleName 
				FROM security_permission_other p_origen, security_action_other a_origen
				WHERE p_origen.ID = a_origen.PermissionID
				$clause
				AND NOT EXISTS
								(	SELECT 1
									FROM security_permission p_destino, security_action a_destino
									WHERE p_destino.ID = a_destino.PermissionID
									AND p_destino.name = p_origen.name 
									AND p_destino.modulename = p_origen.modulename
									AND a_destino.name = a_origen.name
								)
				";
				
				
		//echo $sql;
		//exit;
		
		$savem = $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$rs = $ari->db->Execute($sql);
		$ari->db->SetFetchMode($savem);
		if ($rs && !$rs->EOF) 
		{
			while (!$rs->EOF) 
			{
				$permission = self::nameConstructor($rs->fields["pName"], $rs->fields["pModuleName"]);
				//var_dump();exit;
				$action = new seguridad_action(ID_UNDEFINED);
				$action->set('name', $rs->fields["aName"]);
				$action->set('nicename', $rs->fields["aNiceName"]);
				$action->set('inmenu', $rs->fields["aInMenu"]);
				$action->set('permission', $permission);
				$action->store();
				
				$rs->MoveNext();
				
			}//end while 
			$rs->Close();
		}
					
	}//end function	


	/** Devuelve el objeto permiso que se corresponde con 
		el nombre del permiso ($permissionName) y el nombre 
		del modulo ($moduleName) 
	 */
 	static public function nameConstructor($permissionName, $moduleName)
 	{
 		global $ari;
 		$name = $ari->db->qMagic ($permissionName);
 		$moduleName = $ari->db->qMagic ($moduleName);
 
		$sql = "SELECT ID, ModuleName, Name, NiceName 
				FROM Security_Permission 
        		WHERE ModuleName = $moduleName 
				AND Name = $name
			   ";
		
		//echo $sql;exit;
		
		$savem = $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$rs = $ari->db->Execute($sql);
		$ari->db->SetFetchMode($savem);
		if ($rs && !$rs->EOF) 
		{ 
			$return = new seguridad_permission(ID_UNDEFINED);
			$return->set("permission", $rs->fields["ID"]);
			$return->set("name", $rs->fields["Name"]);
			$return->set("nicename", $rs->fields["NiceName"]);
			$return->set("modulename", $rs->fields["ModuleName"]);
			$rs->Close();			
		} 
		else
		{	$return = false;
		}
		
		return $return;
		
 	}//end function

	
}//end class

?>

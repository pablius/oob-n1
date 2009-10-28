<?php
/**
########################################
#OOB/N1 Framework [©2004,2006]
#
#  @copyright Pablo Micolini
#  @license BSD
######################################## 
*/


 class seguridad_action 
 {
	
 	private $action = ID_UNDEFINED;
	private $name = '';
	private $nicename = '';
	private $permission = NO_OBJECT;
	private $inmenu = 0;

	public function id()
	{
		return $this->action;
	}

	/** Starts the action. 
 	    if no action set we must believe is a new one */ 	
 	public function __construct ($action = ID_UNDEFINED)
 	{
 		global $ari;

		if ($action > ID_MINIMAL) {
			$this->action= $action;
			
			if (!$this->fill ())
			{
			throw new OOB_exception("Invalid action {$action}", "403", "Invalid Action " , true);}
					
		}  
 	}
 	
 	/** Returs the value for the given var */ 	
 	public function get ($var)
 	{	if (isset ($this-> $var))
 		{	return $this-> $var;
 		}
 		else
 		{	return false;
 		}	
 	}

	/** Sets the variable (var), with the value (value) */ 	
 	public function set ($var, $value)
 	{	$this->$var= $value;
 	}
  		
 	/** Fills the action with the DB data */ 	
 	private function fill ()
 	{
		global $ari;

		//load info
		$action_id = $ari->db->qMagic ($this->action);
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$sql= "SELECT Name, NiceName, InMenu 
			   FROM Security_Action 
			   WHERE ID = $action_id";
		
		$rs = $ari->db->Execute($sql);
		
		$ari->db->SetFetchMode($savem);
		if (!$rs || $rs->EOF) 
		{	$return = false;	}
		else 
		{
			$this->name = $rs->fields["Name"];
			$this->nicename = $rs->fields["NiceName"];
			$this->inmenu = $rs->fields["InMenu"];
			$return = true;
		}
		$rs->Close();
		return $return;
 	}
 	 	
 	/**list actions of the $permission*/
 	/**list actions of the $permission*/
 	static public function listActionsFor ($permission, $menu = ALL_MENU)
 	{
		global $ari;
		
		$clausula  = "";
		$flagError = false;
				
		if (!is_a($permission,'seguridad_permission'))
		{
			$ari->error->addError ("seguridad_action", "INVALID_PERMISSION");
			$flagError = true;
		}
		
		switch($menu)
		{
			case IN_MENU:
			case NO_MENU:
			{
				$clausula = " AND inMenu = " . $menu . " "; 
				break;
			}
			default:
			{
				$clausula = "";
				break;	
			}			
		} 
		
		if (!$flagError)
		{
			$permission = $ari->db->qMagic($permission->get("permission"));
			
			$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
			
			$sql = "SELECT ID, Name, NiceName, InMenu
				   FROM Security_Action 
	        	   WHERE PermissionID = $permission $clausula 
				   ORDER BY NiceName";
	
			$rs = $ari->db->Execute($sql);

			$ari->db->SetFetchMode($savem);
			
			$i = 0;
			
			if ($rs && !$rs->EOF) 
			{ 
				while (!$rs->EOF) 
				{
					$return[$i] = new seguridad_action(ID_UNDEFINED);
					$return[$i]->set("action", $rs->fields["ID"]);
					$return[$i]->set("name", $rs->fields["Name"]);
					$return[$i]->set("nicename", $rs->fields["NiceName"]);
					$return[$i]->set("inmenu", $rs->fields["InMenu"]);
					$i++;
					$rs->MoveNext();
				}
			} 
			else
			{$return = false;}
		}
		$rs->Close();
		
		return $return;
	}
 	//end function
	
 	/** te devuelve si una acción está dentro de alguno de los roles del array de roles que le pasaste.
 	 * Tambien se le puede pasar un solo rol.
 	 */
  	static public function exists ($action, $role )
 	{
 		global $ari;
		
		$flagError = false;
		
		if (is_a($action, 'seguridad_action'))
		{	$action = $action->get("action");	}
		else
		{	
			$ari->error->addError ("seguridad_action", 'INVALID_ACTION');
			return false;
		}
			
		if (is_array($role))
		{
			$clausula = "";
			foreach($role as $r)
			{
				if (is_a($r,'seguridad_role'))
				{	$clausula .= $r->get('role') . ",";		}
				else
				{
					$ari->error->addError ("seguridad_action", "INVALID_ROLE");
					return false;
				}
			}//end foreach
			
			$role = substr( $clausula, 0,strlen($clausula)-1 );
			 
			$sql = "SELECT True ";
			$sql .= "FROM security_actionsrole ";
        	$sql .= "WHERE ActionID = $action ";
			$sql .= "AND RoleID IN ($role)";
		}
		elseif(is_a($role,"seguridad_role"))
		{
			$roleid = $ari->db->qMagic($role->get('role'));
			
			$sql = "SELECT True ";
			$sql .= "FROM security_actionsrole ";
        	$sql .= "WHERE RoleID = $roleid ";
			$sql .= "AND ActionID = $action ";			
		}
		else
		{
			$ari->error->addError ("seguridad_action", "INVALID_ROLE");
			return false;
		}
		
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);

		$rs = $ari->db->Execute($sql);

			$ari->db->SetFetchMode($savem);
				
			if ($rs && !$rs->EOF) 
			{	$return = $rs->fields[0];	}			
			else
			{	$return = false;	}
			$rs->Close();
			
		return $return;

 	}
 	//end function
 	
    /**  Returns the module  of the current action   */
	public function myModule()
    {
        global $ari;
		$action_id = $ari->db->qMagic ($this->action);
        $sql= "SELECT M.ID, M.ModuleName, M.Status, M.NiceName, M.Description, M.Optional 
        	  FROM Security_Action A, Security_Permission P, OOB_Modules_Config M 
        	  WHERE A.PermissionID = P.ID 
        	  AND P.ModuleName = M.ModuleName
        	  AND A.ID = $action_id ";
			  	
 			$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
			
		
			//		$rs = $ari->db->Execute( $sql);
			$rs = $ari->db->CacheExecute(SQL_CACHE,$sql);
			$ari->db->SetFetchMode($savem);
			if (!$rs->EOF) 
			{ 
				$return = new OOB_module();
				$return->set("module", $rs->fields["ModuleName"]);
				$return->set("status", $rs->fields["Status"]);
				$return->set("primary", $rs->fields["Optional"]);
				$return->set("nicename", $rs->fields["NiceName"]);
				$return->set("description", $rs->fields["Description"]);				
			}
			else
			{	$return = false;	}
		$rs->Close();

		return $return;	
		
    }
    //end function 	

	/** Devuelve la accion ($accion), 
	 * perteneciente al permiso ($permission) 
	 * del modulo ($module) */
 	static public function nameConstructor($action, $permission, $module)
 	{
 		global $ari;
 		$action = $ari->db->qMagic ($action);
 		$permission = $ari->db->qMagic ($permission);
 		$module = $ari->db->qMagic ($module);
 
 		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
//		$ari->db->SetFetchMode($savem);
					
 		$sql= "SELECT A.ID, A.PermissionID, A.Name, A.NiceName, A.InMenu 
 		       FROM Security_Action A, Security_Permission P 
        	   WHERE A.PermissionID = P.ID 
			   AND A.Name = $action         	  
               AND P.Name = $permission 
			   AND P.ModuleName = $module";
 		
			//		$rs = $ari->db->Execute( $sql);
			$rs = $ari->db->CacheExecute(SQL_CACHE,$sql);
		$ari->db->SetFetchMode($savem);
		if (!$rs->EOF) 
		{
			$action = new seguridad_action(ID_UNDEFINED);
			$action->set('action',$rs->fields["ID"]);
			$action->set('name',$rs->fields["Name"]);
			$action->set('nicename',$rs->fields["NiceName"]);
			$action->set('inmenu',$rs->fields["InMenu"]);
			$permiso = new seguridad_permission($rs->fields['PermissionID']);
			$action->set('permission',$permiso);
		}	
		else
		{
			var_export($action,true);
			var_export($permission,true);
			var_export($module,true);
		
			throw new OOB_exception("Invalid action: A: " . var_export($action,true) . ',P: '.var_export($permission,true) .',M: '. var_export($module,true), "403", "Invalid Action", true);
		}

		return $action;	  	
 	}//end function
 	
	/**	graba la accion en la base de datos*/
	public function store ()
	{
		global $ari;
		//clean vars and validation!
		
		$this->name = trim($this->name);
		$this->nicename = trim($this->nicename);
		
		//action        
		if (!OOB_validatetext :: isNumeric ($this->action))
		{	$ari->error->addError ("seguridad_action", "INVALID_ACTION");	}

		//name
		if (!OOB_validatetext :: isClean($this->name) || !OOB_validatetext :: isCorrectLength ($this->name, 1, MAX_LENGTH))
		{	$ari->error->addError ("seguridad_action", "INVALID_NAME");		} 

		//nicename		 
		if (!OOB_validatetext :: isClean($this->nicename) || !OOB_validatetext :: isCorrectLength ($this->nicename, 1, MAX_LENGTH))
		{	$ari->error->addError ("seguridad_action", "INVALID_NICENAME");		} 

		//permission
		if (!OOB_validatetext :: isNumeric ($this->permission->get('permission')) )
		{	$ari->error->addError ("seguridad_action", "INVALID_PERMISSION");		}

		//inmenu
		if (!OOB_validatetext :: isNumeric ($this->inmenu))
		{	$ari->error->addError ("seguridad_action", "INVALID_INMENU");		}
		
		//valido q no exista la accion
		if ($this->action == ID_UNDEFINED) 					
		{//para nuevo busco uno con el mismo nombre
			$clausula = "";
		}
		else
		{//si actualizo busco con el mismo nombre pero con el mismo id
			$clausula = " AND id <> $this->action";	
		}
		
		$savem = $ari->db->SetFetchMode(ADODB_FETCH_NUM);
		$name = $ari->db->qMagic($this->name);
		$permissionid = $ari->db->qMagic($this->permission->get('permission'));
		
		$sql= "SELECT true as accion FROM security_action 
			   WHERE name = $name and permissionid = $permissionid $clausula";

		$rs = $ari->db->Execute($sql);
		$ari->db->SetFetchMode($savem);

		if (!$rs->EOF && !$rs == false) 
		{	$rs->Close();
			$ari->error->addError ("seguridad_action", "DUPLICATE_ACTION");
		}// end if
					
		if (!$ari->error->getErrorsfor("seguridad_action"))
		{	
			$name = $ari->db->qMagic($this->name);
			$nicename = $ari->db->qMagic($this->nicename);
			$inmenu = $ari->db->qMagic($this->inmenu);
			 				
			if ($this->action > ID_MINIMAL)
			{
				// update data
				$action_id = $ari->db->qMagic ($this->action);
				$ari->db->StartTrans();
				$sql= "UPDATE security_action
					   SET Name = $name,
					   NiceName = $nicename,
					   PermissionID = $permissionid,
		   			   InMenu = $inmenu,
					   WHERE id = $action_id";

				$ari->db->Execute($sql);
					
				
				if (!$ari->db->CompleteTrans())
				{	throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false);	} //return false;
				else
				{	return true;	}	
					
			} 
			else 
			{
				// insert new and set actionid with new id
				$ari->db->StartTrans();
									
				$sql= "INSERT INTO security_action 
				  	  (name,nicename,permissionid,inmenu)
				      VALUES ($name,
								$nicename,
								$permissionid,
								$inmenu)";
					   	
				$ari->db->Execute($sql);
				$this->action = $ari->db->Insert_ID();
			
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
 	
 	
}//end class

?>


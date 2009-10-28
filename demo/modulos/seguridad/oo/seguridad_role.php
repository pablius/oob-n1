<?php
/**
########################################
#OOB/N1 Framework [©2004,2006]
#
#  @copyright Pablo Micolini
#  @license BSD
######################################## 
*/
    
 class seguridad_role {
 	
 	private $role = ID_UNDEFINED;
	private $name = '';
	private $description = '';
	private $status = '';
	private $anonymous = NO_ANONIMO;
	private $trustees = NO;
	
	public function id()
	{
		return $this->role;
	}
	
	public function name()
	{
		return $this->name;
	}
	
	/** Starts the role. 
 	    if no role set we must believe is a new one */ 	
 	public function __construct ($role = ID_UNDEFINED)
 	{
 		global $ari;

		if ($role > ID_MINIMAL) {
			$this->role= $role;
			
			if (!$this->fill ())
			{throw new OOB_exception("Invalid role {$role}", "403", "Invalid Role", false);}
					
		}  
 	}
 	
	/** Returs the value for the given var */ 	
 	public function get ($var)
 	{
 		if ( isset ($this-> $var) ) {
			return $this-> $var;
		}	
 	}
	
	/** Sets the variable (var), with the value (value) */ 	
 	public function set ($var, $value)
 	{
		if (isset ($this->$var))
			$this->$var= $value;
		else
			return false; 	
 	}
	
	/** Lista Roles */
	static public function listRoles ($status = USED, $sort = 'name', $operator = OPERATOR_EQUAL)
	{
		global $ari;

		if ($status == "all")
			{$estado = "";}
		else
			{$estado = "WHERE `Status` $operator '". $status. "'";}
				
		if (in_array ($sort, seguridad_role::getOrders()))
			$sortby = "ORDER BY `$sort`";
		else
			$sortby = "ORDER BY `Name`";

		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$sql= "SELECT `ID`, `Name`, `Description`, `Status`, `Anonymous`, `Trustees`
			   FROM `Security_Role` 
			   $estado $sortby";
		
		$rs = $ari->db->Execute($sql);

		$ari->db->SetFetchMode($savem);
		$i = 0;
		if ($rs && !$rs->EOF) 
		{ 
			while (!$rs->EOF) 
			{
				$return[$i] = new seguridad_role (ID_UNDEFINED);
				$return[$i]->set("role",$rs->fields["ID"]);
				$return[$i]->set("name",$rs->fields["Name"]);
				$return[$i]->set("description",$rs->fields["Description"]);
				$return[$i]->set("status",$rs->fields["Status"]);
				$return[$i]->set("anonymous",$rs->fields["Anonymous"]);
				$return[$i]->set("trustees",$rs->fields["Trustees"]);
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
	
	/** Returs the users on the system. status = (enabled/deleted/pending/bloqued/all) shows users */
	static public function roleCount ($status = 'enabled', $sort = 'uname', $text = "")
	{
	global $ari;
	


	if (in_array ($status, seguridad_role::getStatus ("ALL", false)) && $status != "all")
	{
	$estado = "WHERE status = " . seguridad_role::getStatus($status, false);
	}
	else
	{
		if ($status == "all")
			{$estado = "";}
		else
			{$estado = "WHERE status = 1";}
	}
	
		//search text		
		$searchText = "";
		if($text <> ""){
			$searchText = "  $text ";
		}

	if (in_array ($sort, seguridad_role::getOrders()))
		$sortby = "ORDER BY $sort";
	else
		$sortby = "ORDER BY name";

			$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
			$sql= "SELECT id FROM Security_Role  $estado $searchText $sortby";
			$rs = $ari->db->Execute($sql);

			$ari->db->SetFetchMode($savem);
				if ($rs && !$rs->EOF) { // aca cambie sin probar, hay q ver si anda!
					$return = $rs->RecordCount();	
			$rs->Close();
			} else
			{return false;}

		return $return;
	}
	
	
	/** Returs the users on the system. status = (enabled/deleted/pending/bloqued/all) shows users 
		search by $text in id, uname and email fields
	*/
	static public function search ($status = USED, $sort = 'name', $text = "", $pos = 0, $limit = 20, $operator = OPERATOR_EQUAL)
	{
		global $ari;
		
		//status
		if ($status == "all")
			{$estado = "";}
		else
			{$estado = "AND `Status` $operator '". $status. "'";}
		
		
		//search text
		$searchText = "";
		if($text <> ""){
			$searchText = "  $text ";
		}
		
		//sort by	
		if (in_array ($sort, seguridad_role::getOrders()))
		{	$sortby = "ORDER BY $sort";
		}
		else
		{	$sortby = "ORDER BY name";
		}
		
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
		$sql= "SELECT id FROM Security_Role WHERE 1=1 $estado $searchText $sortby ";
		$rs = $ari->db->SelectLimit($sql, $limit, $pos);

		$ari->db->SetFetchMode($savem);
		if ($rs && !$rs->EOF) 
		{
			while (!$rs->EOF) 
			{	//@optimize => patron factory
				$return[] = new seguridad_role ($rs->fields[0]);
				$rs->MoveNext();
			}			
			$rs->Close();
		} 
		else
		{	return false;
		}

		return $return;
	}
	
	/** Fills the role with the DB data */ 	
 	private function fill ()
 	{
		global $ari;

			//load info
			$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
			$role = $ari->db->qMagic ($this->role);
			$sql= "SELECT `Name`, `Description`, `Status`, `Anonymous`, `Trustees`
				   FROM `Security_Role` WHERE `ID` = $role";
				   
			$rs = $ari->db->Execute($sql);
			
			$ari->db->SetFetchMode($savem);
			if (!$rs || $rs->EOF) 
			{
				return false;
			}
			else
			{
				$this->name = $rs->fields["Name"];
				$this->description = $rs->fields["Description"];
				$this->status = $rs->fields["Status"];
				$this->anonymous = $rs->fields["Anonymous"];
				$this->trustees = $rs->fields["Trustees"];
			}
			$rs->Close();
			return true; 		
 	}

	/**	Guarda en la BD el objeto rol */
	public function store ()
	{
		global $ari;
		// clean vars !
	
		if (!OOB_validatetext :: isClean($this->name) || 
			!OOB_validatetext :: isCorrectLength ($this->name, 1, MAX_LENGTH))
		{	$ari->error->addError ("seguridad_role", "INVALID_NAME");
		} 

		if (!OOB_validatetext :: isClean($this->description))
		{	$ari->error->addError ("seguridad_role", "INVALID_DESCRIPTION");
		} 
		
		if ($this->role == ID_UNDEFINED) 					
		{//para nuevo busco uno con el mismo nombre
			$clausula = "";
		}
		else
		{//si actualizo busco con el mismo nombre pero con el mismo id
			$clausula = " AND id <> $this->role";	
		}
		
		$name = $ari->db->qMagic($this->name);
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
		$sql= "SELECT true as cuenta FROM `Security_Role` 
			   WHERE `Name` = $name $clausula";
		$rs = $ari->db->Execute($sql);
		$ari->db->SetFetchMode($savem);

		if (!$rs->EOF && $rs->fields[0]!= 0) 
		{						
			if ($this->role == ID_UNDEFINED) 					
			{//para nuevo
			// si el rol con el mismo nombre esta borrado lo activo, sino da instancio un error
				$sql= "SELECT `ID` 
					   FROM `Security_Role` 
					   WHERE `Name` = $name 
					   AND `Status` = '" . DELETED . "'";
				$rs->Close();
				$rs2 = $ari->db->Execute($sql);
				if (!$rs2->EOF) 
				{
					//asigno el id del el objeto que volvi a activar
					$this->role = $rs2->fields[0];
					$this->status = USED;
				}	
				else
				{
					$ari->error->addError ("seguridad_role", "DUPLICATE_ROLE");
				}
			}
			else
			{
				$ari->error->addError ("seguridad_role", "DUPLICATE_ROLE");
			}
		}

				
		if (!$ari->error->getErrorsfor("seguridad_role"))
		{
		 	$name =$ari->db->qMagic($this->name);
		 	$description =$ari->db->qMagic($this->description);
		 	$status =$ari->db->qMagic($this->status);
		 	$anonymous =$ari->db->qMagic($this->anonymous);
		 	$trustees =$ari->db->qMagic($this->trustees);
			$role = $ari->db->qMagic($this->role);
			if ($this->role > ID_MINIMAL)
			{
				// update data
				$ari->db->StartTrans();
				$sql= "UPDATE `Security_Role` 
					   SET `Name` = $name, 
					   	   `Description` = $description,  
					   	   `Status` = $status, 
					   	   `Anonymous` = $anonymous,
					   	   `Trustees` = $trustees
					   WHERE `ID` = $role";
						   
				$ari->db->Execute($sql);
				
			
				if (!$ari->db->CompleteTrans())
				{	throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false); 	}
				else
				{	return true;	}	
					
			} 
			else 
			{
				// insert new and set roleid with new id
				$ari->db->StartTrans();
										
				$sql= "INSERT INTO `Security_Role` 
					   ( `Name`, `Description`, `Status`, `Anonymous`, `Trustees`)
					   VALUES ( $name, $description, $status, $anonymous, $trustees )
					  ";
				$ari->db->Execute($sql);
				$this->role = $ari->db->Insert_ID();
			
				if (!$ari->db->CompleteTrans())
				{	throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false); 	}
				else
				{
					return true;
				}
				
			}
			
		} 
		else 
		{
			return false; //devuelve un objeto de error con los errores!
		}
 	}
 	//end function
	
	/** Deletes role object from the DB*/ 	
 	public function delete ()
 	{
		global $ari;
		
		$role_id = $ari->db->qMagic($this->role);
		
		// sets status DELETED for a role-id
		if ($this->role > ID_MINIMAL && $this->status != DELETED) {
			
			$ari->db->StartTrans();

			//delete groups
			$sql= "DELETE FROM `Security_GroupsRole` WHERE `RoleID` = $role_id";
			$ari->db->Execute($sql);
			
			//delete users
			$sql= "DELETE FROM `Security_UsersRole` WHERE `RoleID` = $role_id";
			$ari->db->Execute($sql);
			
			//delete modules
			$sql= "DELETE FROM `Security_ModulesRole` WHERE `RoleID` = $role_id";
			$ari->db->Execute($sql);

			//delete actions
			$sql= "DELETE FROM `Security_ActionsRole` WHERE `RoleID` = $role_id";
			$ari->db->Execute($sql);			
	
			$sql= "UPDATE `Security_Role` 
				   SET  `Status` = '" . DELETED . "' 
				   WHERE `ID` = $role_id";
			$ari->db->Execute($sql);	
									
		
			if ($ari->db->CompleteTrans())
			{	return true;	}
			else
			{	throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false);	}
					

		} else {
			if ($this->status == DELETED)
				$ari->error->addError ("seguridad_role", "ALREADY_DELETED");
			else
				$ari->error->addError ("seguridad_role", "NO_SUCH_ROLE");
			
			return false;
		} 		
 	}

 	/** Adds a user to the current role.
 	 * Returns true if successful, false if not.
    */
 	public function addUser ($user)
 	{
 		global $ari;
 		
 		if (is_a($user, 'oob_user') )
        {
 			if ( $this->isMember($user,USER) )
 			{
 				$return = true; 
 			}
 			else
 			{
 				//falta validar si existe el usuario
				$ari->db->StartTrans();
				
				$role_id = $ari->db->qMagic($this->role);
				$user_id = $ari->db->qMagic($user->get('user'));
										
				$sql= "INSERT INTO `Security_UsersRole` 
					  (`RoleID`, `UserID`)
					   VALUES ($role_id, $user_id )
						   	";
				$ari->db->Execute($sql);

				if (!$ari->db->CompleteTrans())
				{
						throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false); //return false;
				}
				else
				{
						return true;
				}
			}
 				
        }
        else
        {
			$ari->error->addError ("seguridad_role", "INVALID_USER");            	
        }
        
        if (!$ari->error->getErrorsfor("seguridad_role"))
        {
        	return $return;
        }
        else
        {
        	return false;
        }
        
 	}
 	//end function

	
 	/** Removes a user to the current role.
 	 * Returns true if successful, false if not.
    */ 	
 	public function removeUser ($user)
 	{
 		global $ari;
 		
 		if (is_a($user, 'oob_user') )
        {
 			if (!$this->isMember($user,USER) )
 			{   //not member
 				$return = false; 
 			}
 			else
 			{	//is member
				$ari->db->StartTrans();
				
				$role_id = $ari->db->qMagic($this->role);
				$user_id = $ari->db->qMagic($user->get('user'));				
										
				$sql= "DELETE FROM `Security_UsersRole`
					   WHERE `UserID` = $user_id 
					   AND `RoleID` = $role_id
					  ";
				$ari->db->Execute($sql);

				
				if (!$ari->db->CompleteTrans())
				{
					throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false); //return false;
				}
				else
				{
					return true;
				}
			}
        }
        else
        {
			$ari->error->addError ("seguridad_role", "INVALID_USER");            	
        }
        
        if (!$ari->error->getErrorsfor("seguridad_role"))
        {
        	return $return;
        }
        else
        {
        	return false;
        }
        
 	}

 	/** Adds a group to the current role.
 	 * Returns true if successful, false if not.
    */
 	public function addGroup ($group)
 	{
 		global $ari;
 		
 		if (is_a($group, "seguridad_group") )
        {
 			if ( $this->isMember($group,GROUP) )
 			{
 				$return = true; 
 			}
 			else
 			{
 				//falta validar si existe el grupo
				$ari->db->StartTrans();
				
				$role_id = $ari->db->qMagic($this->role);
				$group_id = $ari->db->qMagic($group->get('group'));
												
				$sql= "INSERT INTO `Security_GroupsRole` 
					  (`RoleID`, `GroupID`)
					   VALUES ($role_id, $group_id )
						   	";
				$ari->db->Execute($sql);

			
				if (!$ari->db->CompleteTrans())
				{
						throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false); //return false;
				}
				else
				{
						return true;
				}
			}
 				
        }
        else
        {
			$ari->error->addError ("seguridad_role", "INVALID_GROUP");            	
        }
        
        if (!$ari->error->getErrorsfor("seguridad_role"))
        {
        	return $return;
        }
        else
        {
        	return false;
        }
        
 	}
 	//end function
	
 	/** Removes a group to the current role.
 	 * Returns true if successful, false if not.
    */ 	
 	public function removeGroup ($group)
 	{
 		global $ari;
 		
 		if (is_a($group, "seguridad_group") )
        {
 			if (!$this->isMember($group,GROUP) )
 			{   //not member
 				$return = false; 
 			}
 			else
 			{	//is member
				$ari->db->StartTrans();
				
				$role_id = $ari->db->qMagic($this->role);
				$group_id = $ari->db->qMagic($group->get('group'));
														
				$sql= "DELETE FROM `Security_GroupsRole`
					   WHERE `GroupID` = $group_id 
					   AND `RoleID` = $role_id
						   	";
				$ari->db->Execute($sql);

				
				if (!$ari->db->CompleteTrans())
				{
						throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false); //return false;
				}
				else
				{
						return true;
				}
			}
        }
        else
        {
			$ari->error->addError ("seguridad_role", "INVALID_GROUP");            	
        }
        
        if (!$ari->error->getErrorsfor("seguridad_role"))
        {
        	return $return;
        }
        else
        {
        	return false;
        }
        
 	}

 	/** Adds a module to the current role.
 	 * Returns true if successful, false if not.
    */
 	public function addModule ($module)
 	{
 		global $ari;
 		
 		if (is_a($module,"OOB_module") )
        {
 			if ( $this->isMember($module,MODULE) )
 			{
 				$return = true; 
 			}
 			else
 			{
 				//falta validar si existe el módulo
				$ari->db->StartTrans();
				$moduleName = $ari->db->qMagic ($module->name());
				$role_id = $ari->db->qMagic($this->role);
								
				$sql= "INSERT INTO `Security_ModulesRole` 
					  (`RoleID`, `ModuleName`)
					   VALUES ($role_id, $moduleName )
						   	";
				$ari->db->Execute($sql);

				if (!$ari->db->CompleteTrans())
				{
						throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false); //return false;
				}
				else
				{
						return true;
				}
			}
 				
        }
        else
        {
			$ari->error->addError ("seguridad_role", "INVALID_MODULE");            	
        }
        
        if (!$ari->error->getErrorsfor("seguridad_role"))
        {
        	return $return;
        }
        else
        {
        	return false;
        }
        
 	}
 	//end function
	
 	/** Removes a module to the current role.
 	 * Returns true if successful, false if not.
    */ 	
 	public function removeModule ($module)
 	{
 		global $ari;
 		
 		if ( is_a($module, "OOB_module") )
        {
 			
 			if (!$this->isMember($module,MODULE) )
 			{   //not member
 				$return = false; 
 			}
 			else
 			{	//is member
				$ari->db->StartTrans();
				$moduleName = $ari->db->qMagic ($module->name()); 	
				$role_id = $ari->db->qMagic ($this->role); 	
				
				//borro los permisos para las acciones del modulo
				$sql= "DELETE FROM `Security_ActionsRole`
			   		   WHERE `RoleID` = $role_id 
			   		   AND `ActionID` IN 
			   		   (SELECT A.ID 
			   		   FROM `Security_Action` A, `Security_Permission` P
			   		   WHERE A.PermissionID = P.ID
			   		   AND P.ModuleName = $moduleName)";
			   	
			   	$ari->db->Execute($sql);
			   									
				$sql= "DELETE FROM `Security_ModulesRole`
					   WHERE `ModuleName` = $moduleName 
					   AND `RoleID` = $role_id
						   	";
						   	
				$ari->db->Execute($sql);

				if (!$ari->db->CompleteTrans())
				{
						throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false); //return false;
				}
				else
				{
						return true;
				}
			}
        }
        else
        {
			$ari->error->addError ("seguridad_role", "INVALID_MODULE");            	
        }
        
        if (!$ari->error->getErrorsfor("seguridad_role"))
        {
        	return $return;
        }
        else
        {
        	return false;
        }
        
 	}

	/** Lista los usuarios miembros de los roles que se encuentran 
	 *  en el array $roles pasado como paramentro y que tengan el 
	 *  estado = USED
	 * 	*/
	static public function listUsersFor ($roles)
	{
		global $ari;
		
		$roles_array = array();
		//si no es array el parametro lo transformo en uno
		if ( !is_array($roles) )
		{	$roles_array[0] = $roles;	}
		else
		{	$roles_array = $roles;	}
		
		
		//recorro el array, y formo la clausula solo con los id
		// de los objetos seguridad_role
		
		$in_string = "";
		$flagFirst = true;
		foreach($roles_array as $r)
		{
			if (is_a($r, "seguridad_role"))
			{
				if ($flagFirst)
				{
					$in_string .= $ari->db->qMagic( $r->get('role') );
					$flagFirst = false;		
				}
				else
				{	$in_string .= ',' . $ari->db->qMagic( $r->get('role') );	}				
			}//end if
		}//end foreach
			
		if ( $in_string == "" )
		{
			$ari->error->addError ("seguridad_role", "INVALID_ROLE");
			return false;        
		}
			
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		
		$sql = "SELECT `OOB_User_User`.`ID`, `OOB_User_User`.`Uname`, 
					   `OOB_User_User`.`Password`, `OOB_User_User`.`Email`, 
				       `OOB_User_User`.`Connections`, `OOB_User_User`.`Status`, 
				       `OOB_User_User`.`EmployeeID` 
				FROM `Security_UsersRole`, `OOB_User_User`
                WHERE `Security_UsersRole`.`UserID` = `OOB_User_User`.`ID`
				AND `Security_UsersRole`.`RoleID` IN ($in_string) 
				AND `OOB_User_User`.`Status` = '" . USED . "' 
				ORDER BY `OOB_User_User`.`Uname`";
		//var_dump ($sql);
		$rs = $ari->db->Execute($sql); 
		$i = 0;
		$ari->db->SetFetchMode($savem);
		if ($rs && !$rs->EOF) 
		{ 
			while (!$rs->EOF) 
			{
				$return[$i] = new oob_user (ID_UNDEFINED);
				$return[$i]->set("user",$rs->fields["ID"]);
				$return[$i]->set("uname",$rs->fields["Uname"]);
				$return[$i]->set("password",$rs->fields["Password"]);
				$return[$i]->set("email",$rs->fields["Email"]);
				$return[$i]->set("maxcon",$rs->fields["Connections"]);
				$return[$i]->set("status",$rs->fields["Status"]);
				if ( !empty($rs->fields['EmployeeID']) && 
				     OOB_validatetext :: isNumeric($rs->fields['EmployeeID']) && 
				     $rs->fields['EmployeeID'] > ID_MINIMAL )
				{	$return[$i]->set( 'employee', new personnel_employee($rs->fields['EmployeeID']) );	}				
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
	
    /**  Returns true if the type($user|group|module) is a member of the role     */
	public function isMember( $object, $type )
    {
        global $ari;
        $tmp_error = false;
        
        switch($type)
		{
			case USER:
			{
				$field = "UserID";
				$object_id = $ari->db->qMagic ($object->get('user'));
				$error_type = "INVALID_USER";
				$table = "security_usersrole";
				if (!is_a($object, "oob_user"))
				{	$tmp_error = true;	}	
				break;
			}
			case GROUP:
			{				
				$object_id = $ari->db->qMagic ($object->get('group'));
				$field = "GroupID";
				$error_type = "INVALID_GROUP";
				$table = "security_groupsrole";
				if (!is_a($object, "seguridad_group"))
				{	$tmp_error = true;	}
				break;
			}
			case MODULE:
			{
				$field = "ModuleName";
				$object_id = $ari->db->qMagic ($object->name());
				$error_type = "INVALID_MODULE";
				$table = "security_modulesrole";
				if (!is_a($object, "OOB_module"))
				{	$tmp_error = true;	}
				break;
			}				
		}

        if (!$tmp_error)
        {
        	$sql= "SELECT $field FROM $table 
        		   WHERE $field = $object_id AND RoleID = '$this->role'";
			
			$rs = $ari->db->Execute($sql);	
			if (!$rs->EOF) 
			{	$return = true;		}
			else
			{	$return = false;	}	
			$rs->Close();
			return $return; 
        }
        else
        {	
        	$ari->error->addError ("seguridad_role", $error_type);	
        	return false;
        }
        
    }
    //end function 	
	
	/** Shows the available sorting ways for role */
	static public function getOrders()
	{
	$return[] = "name";
	$return[] = "id";
	$return[] = "description";
	$return[] = "status";
	$return[] = "anonymous";
	
	return $return;
	} 	
 	//end function

	/** Shows the role status or all available status 
	 * $one = ID or "status_string" returns the role status; or "ALL" to return an array.
	 * $id => if true, returns a number, else a string.
	 * */
	static public function getStatus ( $one = "ALL" ,$id = true) 
	{ // 1 Used, 9 deleted

	$return[1] = "used";
	$return[9] = "deleted";
	
	if ($id != true)
		$return = array_flip ($return);
	
	if ($one != "ALL")
	{
		if ($return[$one] !== "" )
		{$return = $return[$one];}
		else
		{$return =  false;}
	}

	return $return;

	}
	//end function  	

	/**	*/
    static public function searchNoMembers($string,$status,$operator= OPERATOR_EQUAL, $role,$type)
    {
        global $ari;

		if (!is_a($role, "seguridad_role"))
		{
			$ari->error->addError ("seguridad_role", "INVALID_ROLE");
			return false;        
		}
		
		$return = false;
		$role_id = $ari->db->qMagic($role->get('role'));
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$ari->db->SetFetchMode($savem);
		$i = 0;
        switch($type)
		{
			case USER:
			{
				$sql = "SELECT ID, Uname, Password, Email, Connections, Status ";
	        	$sql .= "FROM OOB_User_User ";
	        	$sql .= "WHERE Uname LIKE '%$string%'AND Status $operator $status ";
	        	$sql .= "AND ID NOT IN ";
	        	$sql .= "(SELECT UserID FROM Security_UsersRole WHERE RoleID = $role_id)";
				$sql .= " ORDER BY Uname";
				
				$rs = $ari->db->Execute($sql);

				if ($rs && !$rs->EOF) 
				{ 
					while (!$rs->EOF) 
					{
						$return[$i] = new oob_user (ID_UNDEFINED);
						$return[$i]->set("user",$rs->fields["ID"]);
						$return[$i]->set("uname",$rs->fields["Uname"]);
						$return[$i]->set("password",$rs->fields["Password"]);
						$return[$i]->set("email",$rs->fields["Email"]);
						$return[$i]->set("maxcon",$rs->fields["Connections"]);
						$return[$i]->set("status",$rs->fields["Status"]);
						$i++;
						$rs->MoveNext();
					}			
					
				} 	        	
	        	$rs->Close();	        					
				break;
			}
			case GROUP:
			{
	        	$sql = "SELECT ID, Name, Description, Status "; 
	        	$sql .= "FROM Security_Group ";
	        	$sql .= "WHERE Name LIKE '%$string%'AND Status $operator $status ";
	        	$sql .= "AND ID NOT IN ";
	        	$sql .= "(SELECT GroupID FROM Security_GroupsRole WHERE RoleID = $role_id)";
				$sql .= " ORDER BY Name";

				$rs = $ari->db->Execute($sql);

				if ($rs && !$rs->EOF) 
				{ 
					while (!$rs->EOF) 
					{
						$return[$i] = new seguridad_group (ID_UNDEFINED);
						$return[$i]->set("group",$rs->fields["ID"]);
						$return[$i]->set("name",$rs->fields["Name"]);
						$return[$i]->set("description",$rs->fields["Description"]);
						$return[$i]->set("status",$rs->fields["Status"]);
						$i++;
						$rs->MoveNext();
					}			
					
				} 	        	
	        	$rs->Close();

				break;
			}
			case MODULE:
			{
				$sql = "SELECT ID, ModuleName, Status, NiceName, Description, Optional ";
	        	$sql .= "FROM OOB_Modules_Config ";
	        	$sql .= "WHERE NiceName LIKE '%$string%'AND Status $operator $status ";
	        	$sql .= "AND ModuleName NOT IN ";
	        	$sql .= "(SELECT ModuleName FROM Security_ModulesRole WHERE RoleID = $role_id)";
				$sql .= " ORDER BY ModuleName";
				
				$rs = $ari->db->Execute($sql);

				if ($rs && !$rs->EOF) 
				{ 
					while (!$rs->EOF) 
					{
						$return[$i] = new OOB_module ();
						$return[$i]->set("module",$rs->fields["ModuleName"]);
						$return[$i]->set("nicename",$rs->fields["NiceName"]);
						$return[$i]->set("description",$rs->fields["Description"]);
						$return[$i]->set("primary",$rs->fields["Optional"]);
						$return[$i]->set("status",$rs->fields["Status"]);
						
						$i++;
						$rs->MoveNext();
					}			
					
				} 	        	
	        	$rs->Close();
	        					
				break;
			}			
		}
			
		return $return;
    }
    //end function 


	/**list groups members of the $role*/
 	static public function listGroupsFor ($role)
 	{
		global $ari;
		
		if (!is_a($role, "seguridad_role"))
		{
			$ari->error->addError ("seguridad_role", "INVALID_ROLE");
			return false;        
		}
		
		$role_id = $ari->db->qMagic($role->get('role'));
		
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
	    $sql = "SELECT G.ID, G.Name, G.Description, G.Status "; 
		$sql .= "FROM security_groupsrole GR, security_group G ";
        $sql .= "WHERE GR.GroupID = G.ID ";
		$sql .= "AND GR.RoleID = $role_id ";
		$sql .= "AND G.status <> " . DELETED . " ";
		$sql .= "ORDER BY G.Name";

		$rs = $ari->db->Execute($sql);
		$i = 0;
		$ari->db->SetFetchMode($savem);
		if ($rs && !$rs->EOF) 
		{ 
			while (!$rs->EOF) 
			{
				$return[$i] = new seguridad_group (ID_UNDEFINED);
				$return[$i]->set("group",$rs->fields["ID"]);
				$return[$i]->set("name",$rs->fields["Name"]);
				$return[$i]->set("description",$rs->fields["Description"]);
				$return[$i]->set("status",$rs->fields["Status"]);
				$i++;
				$rs->MoveNext();
			}			
					
		} 				
		else
		{ $return = false;}
		
		$rs->Close();
		return $return;
	}
 	//end function
	
	/**list modules members of the $role*/
 	static public function listModulesFor ($role, $object = true)
 	{
		global $ari;

		if (!is_a($role, "seguridad_role"))
		{
			$ari->error->addError ("seguridad_role", "INVALID_ROLE");
			return false;        
		}
		
		$role_id = $ari->db->qMagic ($role->get('role'));

		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		
		$sql = "SELECT M.ID, M.ModuleName, M.Status, M.NiceName, M.Description, M.Optional ";
		$sql .= "FROM security_modulesrole MR, oob_modules_config M ";
        $sql .= "WHERE MR.ModuleName = M.ModuleName ";
		$sql .= "AND MR.RoleID = $role_id ";
		$sql .= "AND M.status <> '" . DELETED . "' ";
		$sql .= "ORDER BY M.NiceName";

		$rs = $ari->db->Execute($sql);
		$i = 0;
		$ari->db->SetFetchMode($savem);
		if ($rs && !$rs->EOF) 
		{
			while (!$rs->EOF) 
			{
				if ($object)
				{
					$return[$i] = new OOB_module ();
					$return[$i]->set("module",$rs->fields["ModuleName"]);
					$return[$i]->set("nicename",$rs->fields["NiceName"]);
					$return[$i]->set("description",$rs->fields["Description"]);
					$return[$i]->set("primary",$rs->fields["Optional"]);
					$return[$i]->set("status",$rs->fields["Status"]);
				}
				else
				{
					$return[$i] = $rs->fields["ModuleName"];
				}
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

	/**	*/
	public function removeAction ( $action )
 	{
 		global $ari;
		
		if ( is_a($action,'seguridad_action') )
        {
		
			$ari->db->StartTrans();
		
			$action_id = $ari->db->qMagic ($action->get('action'));
			$role_id = $ari->db->qMagic ($this->role);
					
			$sql= "DELETE FROM security_actionsrole
				   WHERE RoleID = $role_id AND ActionID = $action_id";
			   
			$ari->db->Execute($sql);

			if (!$ari->db->CompleteTrans())
			{
				throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false); //return false;
			}
			else
			{
				return true;
			}
		
		} 
        else
        {
			$ari->error->addError ("seguridad_role", "INVALID_ACTION");  
			return false;          	
        }
		
 	}
 	//end funtion

	/**	*/
	public function addAction ($action)
 	{
 		global $ari;
 		
 		if ( is_a($action,'seguridad_action') )
        {
 			if (seguridad_action :: exists ($action, $this ) )
 			{
 				return true;
 			}
 			else
 			{
 				//falta validar si existe el módulo
				$ari->db->StartTrans();
				
				$action_id = $ari->db->qMagic ($action->get('action'));
				$role_id = $ari->db->qMagic ($this->role);
				
				$sql= "INSERT INTO security_actionsrole 
					  (ActionID, RoleID)
					   VALUES ($action_id, $role_id )";
					   
				$ari->db->Execute($sql);

			if (!$ari->db->CompleteTrans())
				{
						throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false); //return false;
				}
				else
				{
						return true;
				}
			}
 				
        }
        else
        {
			$ari->error->addError ("seguridad_role", "INVALID_ACTION");  
			return false;          	
        }        
 	}
 	//end function
 	
 	/** Returns the roles of the $user */  
	static public function myRoles ($user)
 	{
 		global $ari;
 		
 		if (is_a($user, 'oob_user'))
		{
			$user = $user->get("user");
		}
		
// 		// ----------
// 		// prueba de cache para el rol
// 		// cache del selector para cada usuario
//
//// Set a id for this cache
//	$id = 'myroles__' . $user ;
//
////Set cache options
//$options = array(
//				'cacheDir' => $ari->get ('cachedir'),
//				'lifeTime' => SQL_CACHE,
//				'fileNameProtection' => false,
//				'onlyMemoryCaching' => true,
//				'automaticSerialization' => true
//);
//
//// 
////  'memoryCaching' => true
//
//
//		// Create a Cache_Lite object
//		$Cache_Lite = new Cache_Lite($options);
//		
//		// Test if thereis a valid cache for this id
//		if ($return = $Cache_Lite->get($id)) {
// 		
// 		//-----------
// 		return $return;
//
//		} else {
		
		if ($user === false)
		{
			$sql = "SELECT ID, Name, Description, Status, Anonymous, Trustees 
					FROM Security_Role WHERE Anonymous = ". ANONIMO;			
		}
		else
		{
			$user = $ari->db->qMagic($user);
			$sql = "(
					 SELECT Security_Role.ID, Security_Role.Name, Security_Role.Description,
					 		Security_Role.Status, Security_Role.Anonymous, Security_Role.Trustees   
					 FROM Security_UsersRole, Security_Role   
					 WHERE Security_Role.ID = Security_UsersRole.RoleID
					 AND UserID = $user
					) 
				    UNION DISTINCT 
				    (
					 SELECT Security_Role.ID, Security_Role.Name, Security_Role.Description,
					 		Security_Role.Status, Security_Role.Anonymous, Security_Role.Trustees
					 FROM Security_GroupsRole, Security_Role
					 WHERE Security_Role.ID = Security_GroupsRole.RoleID					 		    
				     AND Security_GroupsRole.GroupID 
	                 IN 
	                 (SELECT GroupID FROM Security_UsersGroup WHERE UserID = $user)
	                )";					
		}
		
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);

		//$rs = $ari->db->CacheExecute(SQL_CACHE, $sql);
		$rs = $ari->db->Execute($sql);
		$ari->db->SetFetchMode($savem);
		$i = 0;
		
		if ($rs && !$rs->EOF) 
		{ 
			while (!$rs->EOF) 
			{
				$return[$i] = new seguridad_role (ID_UNDEFINED);
				$return[$i]->set("role",$rs->fields["ID"]);
				$return[$i]->set("name",$rs->fields["Name"]);
				$return[$i]->set("description",$rs->fields["Description"]);
				$return[$i]->set("status",$rs->fields["Status"]);
				$return[$i]->set("anonymous",$rs->fields["Anonymous"]);
				$return[$i]->set("trustees",$rs->fields["Trustees"]);
				$i++;
				$rs->MoveNext();
			}			
		} 
		else
		{$return = false;}
		$rs->Close();
 		
// 		$Cache_Lite->save($return);
//		
//		} //end cache
		return $return;
		
		
	}
 	//end function

	/**	devuelve un array con las roles cuyos id se encuentran en el 
	 * array pasado como parametro	
	*/
	static public function getRolesForIdArray($id_array)
	{
		global $ari;
		
		$flagError = false;
		$return = false;
		
		if (!is_array($id_array))
		{
			$flagError = true;
		}//end if
		elseif(count($id_array) == 0)
		{
			$flagError = true;
		}
		
		if (!$flagError)
		{
			//armo la clausula de la consulta
			$flagFirst = true;
			$in_string = "";
			foreach($id_array as $id)
			{
				if ($flagFirst)
				{
					$in_string .= $ari->db->qMagic($id);
					$flagFirst = false;		
				}
				else
				{	$in_string .= ',' . $ari->db->qMagic($id);	}
			}//end foreach

			$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
			
			$sql = "SELECT ID, Name, Description, Status, Anonymous, Trustees
					FROM Security_Role
	        		WHERE ID IN ($in_string) 
					AND Status = " . USED . " 
					ORDER BY Name";
	
			$rs = $ari->db->Execute($sql);
	
			$ari->db->SetFetchMode($savem);
			$i = 0;
			if ($rs && !$rs->EOF) 
			{ 
				$return = array();
				while (!$rs->EOF) 
				{
					$return[$i] = new seguridad_role (ID_UNDEFINED);
					$return[$i]->set('role', $rs->fields["ID"]);
					$return[$i]->set('name', $rs->fields["Name"]);
					$return[$i]->set('description', $rs->fields["Description"]);
					$return[$i]->set('status', $rs->fields["Status"]);
					$return[$i]->set('anonymous', $rs->fields["Anonymous"]);
					$return[$i]->set("trustees",$rs->fields["Trustees"]);
					$rs->MoveNext();
					$i++;
				}//end while			
			
			} //end if
			else
			{$return = false;}
			$rs->Close();
		}///end if flagError
		return $return;
	}//end function

	/** Lista Roles para la lista de confiados */
	static public function listRolesForTrustees ($trustees = YES, $sort = 'name', $operator = OPERATOR_EQUAL)
	{
		global $ari;

			
		if (trim($trustees) != "")
		{
			$trustees = $ari->db->qMagic($trustees);
			$trustees = " AND `Trustees` $operator $trustees ";
		}
				
		if (in_array ($sort, seguridad_role::getOrders()))
		{	$sortby = "ORDER BY `$sort`";	}
		else
		{	$sortby = "ORDER BY `Name`";	}

		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$sql= "SELECT `ID`, `Name`, `Description`, `Status`, `Anonymous`, `Trustees`
			   FROM `Security_Role`
			   WHERE Status = '" . USED . "'  
			   $trustees $sortby";
		
		$rs = $ari->db->Execute($sql);

		$ari->db->SetFetchMode($savem);
		$i = 0;
		if ($rs && !$rs->EOF) 
		{ 
			while (!$rs->EOF) 
			{
				$return[$i] = new seguridad_role (ID_UNDEFINED);
				$return[$i]->set("role",$rs->fields["ID"]);
				$return[$i]->set("name",$rs->fields["Name"]);
				$return[$i]->set("description",$rs->fields["Description"]);
				$return[$i]->set("status",$rs->fields["Status"]);
				$return[$i]->set("anonymous",$rs->fields["Anonymous"]);
				$return[$i]->set("trustees",$rs->fields["Trustees"]);
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

	/** Lista los usuarios miembros de los roles que se encuentran 
	 *  en el array $roles pasado como paramentro y que tengan el 
	 *  estado = USED, ademas lista los usuarios q se encuentren en algun
	 *  grupo q sea miembro del algun rol pasado como parametro
	 * 	*/
	static public function listAllUsersFor ($roles)
	{

		global $ari;
				
		$roles_array = array();
		//si no es array el parametro lo transformo en uno
		if ( !is_array($roles) )
		{	$roles_array[0] = $roles;	}
		else
		{	$roles_array = $roles;	}
		
		//recorro el array, y formo la clausula solo con los id
		// de los objetos seguridad_role para usarlo en la clausula in
		
		$in_string = "";
		$flagFirst = true;
		foreach($roles_array as $r)
		{
			if (is_a($r, "seguridad_role"))
			{
				if ($flagFirst)
				{
					$in_string .= $ari->db->qMagic( $r->get('role') );
					$flagFirst = false;		
				}
				else
				{	$in_string .= ',' . $ari->db->qMagic( $r->get('role') );	}				
			}//end if
		}//end foreach
			
		if ( $in_string == "" )
		{
			$ari->error->addError ("seguridad_role", "INVALID_ROLE");
			return false;        
		}
		
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		
		//armo la consulta para traer los usuarios miembros del rol
		$sql1 = "SELECT `OOB_User_User`.`ID`, `OOB_User_User`.`Uname`, 
					    `OOB_User_User`.`Password`, `OOB_User_User`.`Email`, 
				        `OOB_User_User`.`Connections`, `OOB_User_User`.`Status`, 
				        `OOB_User_User`.`EmployeeID` 
				FROM `Security_UsersRole`, `OOB_User_User`
                WHERE `Security_UsersRole`.`UserID` = `OOB_User_User`.`ID`
				AND `Security_UsersRole`.`RoleID` IN ($in_string) 
				AND `OOB_User_User`.`Status` = '" . USED . "' 
				";
		
		//armo la consulta para traer los usuarios miembros de algun grupo q sea
		//miembro de algun rol
		$sql2 = "SELECT `OOB_User_User`.`ID`, `OOB_User_User`.`Uname`, 
					    `OOB_User_User`.`Password`, `OOB_User_User`.`Email`, 
				        `OOB_User_User`.`Connections`, `OOB_User_User`.`Status`, 
				        `OOB_User_User`.`EmployeeID` 
				 FROM `OOB_User_User`, `Security_UsersGroup`
				 WHERE `Security_UsersGroup`.`UserID` = `OOB_User_User`.`ID`
				 AND `OOB_User_User`.`Status` = '" . USED . "' 
				 AND `Security_UsersGroup`.`GroupID` IN  
				 ( SELECT `Security_GroupsRole`.`GroupID` 
				 FROM `Security_GroupsRole` 
				 WHERE `Security_GroupsRole`.`RoleID` IN ($in_string) )
				 ORDER BY 2";
		
		$sql = $sql1 . " UNION  " . $sql2;
		
		$rs = $ari->db->Execute($sql); 
		$i = 0;
		$ari->db->SetFetchMode($savem);
		if ($rs && !$rs->EOF) 
		{ 
			while (!$rs->EOF) 
			{
				$return[$i] = new oob_user (ID_UNDEFINED);
				$return[$i]->set("user",$rs->fields["ID"]);
				$return[$i]->set("uname",$rs->fields["Uname"]);
				$return[$i]->set("password",$rs->fields["Password"]);
				$return[$i]->set("email",$rs->fields["Email"]);
				$return[$i]->set("maxcon",$rs->fields["Connections"]);
				$return[$i]->set("status",$rs->fields["Status"]);
				if ( !empty($rs->fields['EmployeeID']) && 
				     OOB_validatetext :: isNumeric($rs->fields['EmployeeID']) && 
				     $rs->fields['EmployeeID'] > ID_MINIMAL )
				{	$return[$i]->set( 'employee', new personnel_employee($rs->fields['EmployeeID']) );	}				
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

	
	/** Lista Roles activos, no anonimos, no asignados a usr ni a grupos 
	 */
	static public function listRolesNotAssigned()
	{
		global $ari;
		$used = $ari->db->qMagic(USED);
		$no = $ari->db->qMagic(NO);
		
		$sql= "SELECT `ID`, `Name`, `Description`, `Status`, `Anonymous`, `Trustees`
			   FROM `Security_Role` r
			   WHERE `Status` = $used
			   AND Anonymous = $no
			   AND NOT EXISTS 
			   					( SELECT 1 
								  FROM `Security_GroupsRole` 
								  WHERE `RoleID` = r.ID
								)
			   AND NOT EXISTS 
			   					( SELECT 1 
								  FROM `Security_UsersRole` 
								  WHERE `RoleID` = r.ID
								)
								
			   ORDER BY `Name`
			   ";

		/*
			   AND NOT EXISTS 
			   					( SELECT 1 
								  FROM `Security_ModulesRole` 
								  WHERE `RoleID` = r.ID
								)
			  
			   AND NOT EXISTS 
			   					( SELECT 1 
								  FROM `Security_ActionsRole` 
								  WHERE `RoleID` = r.ID
								)
		
		*/
		
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$rs = $ari->db->Execute($sql);
		$ari->db->SetFetchMode($savem);
		$i = 0;
		if ($rs && !$rs->EOF) 
		{ 
			while (!$rs->EOF) 
			{
				$return[$i] = new seguridad_role (ID_UNDEFINED);
				$return[$i]->set("role",$rs->fields["ID"]);
				$return[$i]->set("name",$rs->fields["Name"]);
				$return[$i]->set("description",$rs->fields["Description"]);
				$return[$i]->set("status",$rs->fields["Status"]);
				$return[$i]->set("anonymous",$rs->fields["Anonymous"]);
				$return[$i]->set("trustees",$rs->fields["Trustees"]);
				$i++;
				$rs->MoveNext();
			}
			$rs->Close();			
		} 
		else
		{	$return = false;
		}
		
		return $return;
	}
 	//end function

	/** Retorna true si el rol actual esta activo, no es anonimo,  
		y no esta asignado a usr ni grupos. Sino retorna false 
	 */
	public function isRolNotAssigned()
	{
		global $ari;
		$used = $ari->db->qMagic(USED);
		$id = $ari->db->qMagic($this->role);
		$no = $ari->db->qMagic(NO);
		
		$sql = "SELECT 1
				FROM `Security_Role`
				WHERE ID = $id
				AND `Status` = $used
				AND Anonymous = $no
				AND ID NOT IN   
								( SELECT RoleID 
								  FROM `Security_GroupsRole` 
								)
				AND ID NOT IN 
								( SELECT RoleID 
								  FROM `Security_UsersRole` 
								)
			   ";

			/*
			   AND ID NOT IN 
								( SELECT RoleID 
								  FROM `Security_ModulesRole` 
								)
			  
			   AND ID NOT IN 
								( SELECT RoleID 
								  FROM `Security_ActionsRole` 
								)

			*/	
				
		$rs = $ari->db->Execute($sql);
		if ($rs && !$rs->EOF) 
		{ 	$rs->Close();			
			return true;
		} 
		else
		{	return false;
		}
		
	}
 	//end function

 	 	
 }//end class
 
?>

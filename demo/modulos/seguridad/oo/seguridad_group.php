<?php
/**
########################################
#OOB/N1 Framework [©2004,2006]
#
#  @copyright Pablo Micolini
#  @license BSD
######################################## 
*/

  class seguridad_group {
 	
	private $group = ID_UNDEFINED;
	private $name = '';
	private $description = '';
	private $status = '';

	public function id()
	{
		return $this->group;
	}
	
	public function name()
	{
		return $this->name;
	}
	
	/** Starts the usergroup. 
 	    if no usergroup set we must believe is a new one */ 	
 	public function __construct ($group = ID_UNDEFINED)
 	{
 		global $ari;

		if ($group > ID_MINIMAL) {
			$this->group= $group;
			
			if (!$this->fill ())
			{throw new OOB_exception("Invalid user group {$group}", "403", "Invalid User Group", false);}
					
		}  
 	}
	
	/** Returs the value for the given var */ 	
 	public function get ($var)
 	{
 		if ( isset ($this->$var) ) {
			return $this->$var;		
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
 	
 	/** Lista los usuarios del grupo ($group) q tenga un estado distinto de borrado */
 	static public function listUsersFor ($group)
 	{
		global $ari;

		if (!is_a($group, "seguridad_group"))
		{
			$ari->error->addError ("seguridad_group", "INVALID_GROUP");
			return false; 
		}
		
		$group_id = $ari->db->qMagic($group->get('group'));
		
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$sql = "SELECT U.ID, U.Uname, U.Password, U.Email, U.Connections, U.Status ";
		$sql .= "FROM security_usersgroup UG, oob_user_user U ";
        $sql .= "WHERE UG.UserID = U.ID ";
		$sql .= "AND UG.GroupID = $group_id ";
		$sql .= "AND U.Status <> " . DELETED . " ";
		$sql .= "ORDER BY U.Uname";

		$rs = $ari->db->Execute($sql);

		$ari->db->SetFetchMode($savem);
		$i = 0;
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
		else
		{$return = false;}
		
		$rs->Close();
		return $return;
	}
 	//end function
 	 	
 	/** Adds a user to the current user group.
 	 * Returns true if successful, false if not.
    */
 	public function addUser ($user)
 	{
 		global $ari;
 		
 		if (!is_a($user, 'oob_user'))
        {
			$ari->error->addError ("seguridad_group", "INVALID_USER");
			return false;            	
        }

		if ( $this->isMember($user) )
		{
			$return = true; 
		}
		else
		{
			$ari->db->StartTrans();
			
			$user_id = $ari->db->qMagic($user->get('user'));
			$group_id = $ari->db->qMagic($this->get('group'));
									
			$sql= "INSERT INTO Security_UsersGroup 
				  (UserID, GroupID)
				   VALUES ($user_id, $group_id )
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
 				
		return $return;        
 	}
 	//end function

 	/** Removes a user to the current user group.
 	 * Returns true if successful, false if not.
    */ 	
 	public function removeUser ($user)
 	{
 		global $ari;
 		
 		if (!is_a($user, 'oob_user'))
        {
			$ari->error->addError ("seguridad_group", "INVALID_USER");
			return false;            	
        }
         		
		if (!$this->isMember($user) )
 		{   //not member
 			$return = false; 
 		}
 		else
 		{	//is member
			$ari->db->StartTrans();
			
			$user_id = $ari->db->qMagic($user->get('user'));
			$group_id = $ari->db->qMagic($this->get('group'));
							
			$sql= "DELETE FROM security_usersgroup 
				  WHERE UserID = $user_id AND GroupID = $group_id
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
		return $return;        
 	}
 	
    /**  Returns true if the user $user is a member of the group     */
    public function isMember( $user )
    {
        global $ari;
        
        if ( is_a($user, "oob_user") )
        {
        	
        	$user_id = $ari->db->qMagic($user->get('user')); 
			$group_id = $ari->db->qMagic($this->group); 
        	
			$sql= "SELECT True FROM Security_UsersGroup 
        		   WHERE UserID = $user_id 
        		   AND GroupID = $group_id";
        		   
			$rs = $ari->db->Execute($sql);	
			if (!$rs->EOF && $rs) 
			{	$return = true;		}
			else
			{	$return = false;	}	
			$rs->Close();
        }
        else
        {
			$ari->error->addError ("seguridad_group", "INVALID_USER");  
			return false;      	
        }
        return $return;
    }
    //end function 	

    /**  Returns true if the user $user is a member of the group     */
    static public function searchNoMembers($string,$status,$operator= OPERATOR_EQUAL, $group = ID_UNDEFINED)
    {
        global $ari;
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$string = $ari->db->qMagic("%" . $string . "%");
		$sql = "SELECT U.ID, U.Uname, U.Password, U.Email, U.Connections, U.Status ";
		$sql .= "FROM oob_user_user U ";
		$sql .= "WHERE U.uname LIKE $string AND U.status $operator $status ";
		$sql .= "AND U.ID NOT IN ";
		$sql .= "(SELECT UserID FROM security_usersgroup WHERE GroupID = '$group' )";

	    $rs = $ari->db->Execute($sql);
		$ari->db->SetFetchMode($savem);
		$i = 0;
		
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
		else
		{$return = false;}		
		
		$rs->Close();
					
		return $return;
    }
    //end function 
	
	/** Stores/Updates user group object in the DB */	 	
 	public function store ()
 	{
		global $ari;
	
		$flagStore = true;
		
		//validate description
		if(!OOB_validatetext :: isClean($this->description))
		{
			$ari->error->addError ("seguridad_group", "INVALID_DESCRIPTION");
			$flagStore = false;
		}
		
		//validate the data!
		if (!OOB_validatetext :: isClean($this->name) || 
			!OOB_validatetext :: isCorrectLength ($this->name, 1, MAX_LENGTH))
		{
			$ari->error->addError ("seguridad_group", "INVALID_NAME");
			$flagStore = false;
		} 
		
				
		if ($this->group == ID_UNDEFINED) 					
		{//para nuevo busco uno con el mismo nombre
			$clausula = "";
		}
		else
		{//si actualizo busco con el mismo nombre pero con el mismo id
			$clausula = " AND id <> '$this->group'";	
		}
				
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
		$name = $ari->db->qMagic($this->name);
		$sql= "SELECT true as cuenta FROM security_group WHERE name = $name $clausula";
		$rs = $ari->db->Execute($sql);
		$ari->db->SetFetchMode($savem);

		if (!$rs->EOF && $rs) 
		{						
			if ($this->group == ID_UNDEFINED) 					
			{//para nuevo
				// si el grupo con el mismo nombre esta borrado lo activo, sino da instancio un error
				$sql= "SELECT id FROM security_group WHERE name = $name AND Status = '" . DELETED . "'";
				$rs->Close();
				$rs2 = $ari->db->Execute($sql);
				if (!$rs2->EOF) 
				{
					//asigno el id del el objeto que volvi a activar
					$this->group = $rs2->fields[0];
					$this->status = USED;
				}	
				else
				{
					$ari->error->addError ("seguridad_group", "DUPLICATE_GROUP");
					$flagStore = false;
				}
			}
			else
			{
				$ari->error->addError ("seguridad_group", "DUPLICATE_GROUP");
				$flagStore = false;
			}
		}
		
				
		if ($flagStore)
		{
		 	$name =$ari->db->qMagic($this->name);
		 	$description =$ari->db->qMagic($this->description);
		 	$status =$ari->db->qMagic($this->status);
			$group_id =$ari->db->qMagic($this->group);

			if ($this->group > ID_MINIMAL)
			{
				// update data
				$ari->db->StartTrans();
				$sql= "UPDATE security_group 
					   SET name = $name, 
					   	   description = $description, 
					   	   status = $status  
					   WHERE id = $group_id";
					   
				$ari->db->Execute($sql);
				
							
				if (!$ari->db->CompleteTrans())
				{	throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false); 	}
				else
				{	return true;	}	
					
			} 
			else 
			{
				// insert new and set usergroupid with new id
				$ari->db->StartTrans();
										
				$sql= "INSERT INTO security_group 
					   ( name, description, status)
					   VALUES ( $name, $description, $status )
					   	";
				$ari->db->Execute($sql);
				$this->group = $ari->db->Insert_ID();
			
				if (!$ari->db->CompleteTrans())
				{	throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false); }
				else
				{
				return true;
				}
			}
		} 
		else 
		{
			// no validan los datos
			return false; //devuelve un objeto de error con los errores!
		}
 	}
 	//end function
 	
	/** Fills the usergroup with the DB data */ 	
 	private function fill ()
 	{
		global $ari;

		//load info
		
		$group_id = $ari->db->qMagic($this->group);
		
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$sql= "SELECT Name, Description, Status 
			   FROM security_group WHERE id = $group_id";
			   
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
		}
		$rs->Close();
		return true; 		
 	}

	/** Deletes usergroup object from the DB*/ 	
 	public function delete ()
 	{
		global $ari;
		
		$group_id = $ari->db->qMagic($this->group);
		
		// sets status DELETED for a usergroup-id
		if ($this->group > ID_MINIMAL && $this->status != DELETED) 
		{	
			$ari->db->StartTrans();
			$sql= "UPDATE security_group SET status = '" . DELETED . "' WHERE id = $group_id";
			$ari->db->Execute($sql);
			
			$sql= "DELETE FROM security_usersgroup WHERE GroupID = $group_id";
			$ari->db->Execute($sql);
						
		
			if ($ari->db->CompleteTrans())
			{	return true;	}
			else
			{	throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false);	}
						
		//	return true; // que es esto?
		} 
		else 
		{
			if ($this->status == DELETED)
			{	$ari->error->addError ("seguridad_group", "ALREADY_DELETED");	}
			else
			{	$ari->error->addError ("seguridad_group", "NO_SUCH_GRUOP");		}
			
			return false;
		} 		
 	}
 
 	/** Returs the users group on the system. status = (used/deleted/all) shows groups */
	static public function listGroups ($status = USED, $sort = 'name', $operator = OPERATOR_EQUAL)
	{
		global $ari;

		if ($status == "all")
			{$estado = "";}
		else
			{$estado = "WHERE status $operator '". $status. "'";}
				
		if (in_array ($sort, seguridad_group::getOrders()))
			$sortby = "ORDER BY $sort";
		else
			$sortby = "ORDER BY name";

		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$sql= "SELECT ID, Name, Description, Status
			   FROM security_group $estado $sortby";
		
		$rs = $ari->db->Execute($sql);

		$ari->db->SetFetchMode($savem);
		$i = 0;
		if ($rs && !$rs->EOF) 
		{ 
			while (!$rs->EOF) 
			{
				$return[$i] = new seguridad_group (ID_UNDEFINED);
				$return[$i]->set("group", $rs->fields["ID"]);
				$return[$i]->set("name", $rs->fields["Name"]);
				$return[$i]->set("description", $rs->fields["Description"]);
				$return[$i]->set("status", $rs->fields["Status"]);
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
	static public function groupCount ($status = 'enabled', $sort = 'uname', $text = '')
	{
	global $ari;
	


	if (in_array ($status, seguridad_group::getStatus ("ALL", false)) && $status != "all")
	{
	$estado = "WHERE status = " . seguridad_group::getStatus($status, false);
	}
	else
	{
		if ($status == "all")
			{$estado = "";}
		else
			{$estado = "WHERE status = 1";}
	}
	
	$searchText = "";
		if($text <> ""){
			$searchText = "  $text ";
		}

	if (in_array ($sort, seguridad_group::getOrders()))
		$sortby = "ORDER BY $sort";
	else
		$sortby = "ORDER BY name";

			$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
			$sql= "SELECT id FROM security_group  $estado $searchText $sortby";
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
	static public function search ($status = USED, $sort = 'uname', $text = "", $pos = 0, $limit = 20, $operator = OPERATOR_EQUAL)
	{
		global $ari;
		
		//status
		if ($status == "all")
			{$estado = "";}
		else
			{$estado = "AND status $operator '". $status. "'";}
			
		//search text
		$searchText = "";
		if($text <> ""){
			$searchText = "  $text ";
		}
		
		//sort by	
		if (in_array ($sort, seguridad_group::getOrders()))
		{	$sortby = "ORDER BY $sort";
		}
		else
		{	$sortby = "ORDER BY name";
		}
		
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
		$sql= "SELECT id FROM security_group WHERE 1=1 $estado $searchText $sortby ";
		$rs = $ari->db->SelectLimit($sql, $limit, $pos);

		$ari->db->SetFetchMode($savem);
		if ($rs && !$rs->EOF) 
		{
			while (!$rs->EOF) 
			{	//@optimize => patron factory
				$return[] = new seguridad_group ($rs->fields[0]);
				$rs->MoveNext();
			}			
			$rs->Close();
		} 
		else
		{	return false;
		}

		return $return;
	}
 	 	
	/** Shows the available sorting ways for users group */
	static public function getOrders()
	{
		$return[] = "name";
		$return[] = "id";
		$return[] = "description";
		$return[] = "status";
		
		return $return;
	} 	
 	//end function
 	
	/** Shows the user group status or all available status 
	 * $one = ID or "status_string" returns the user group status; or "ALL" to return an array.
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
 
 }//end class
 
?>

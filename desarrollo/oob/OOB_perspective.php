<?php
/**
########################################
#OOB/N1 Framework [©2004,2006]
#
#  @copyright Pablo Micolini
#  @license BSD
#  @version 1.1
######################################## 
*/
 
 /** This class initalizes system perspectives, and the modules that each one can acces.
  */
 class OOB_perspective {
 	
 	private $perspective;
	public $template;
 	
 public function __construct ($perspective = false)
 {
 	global $ari;
 	
 	if (!$perspective)
	{
		$this->perspective= "default";
	}
	else 
	{
		if (is_dir($ari->filesdir.DIRECTORY_SEPARATOR.'perspectives'.DIRECTORY_SEPARATOR.$perspective))
		{
			$this->perspective= $perspective;
		}
		else 
		{
			throw new OOB_exception('', "072", "Perspectiva no existente");
		}
	}
	
	$this->template = $ari->newTemplate ();
 
 }
 
 public function generateOutput ()
 {
 	global $ari;
			// $ari->internalChrono('p_start');
			//start smarty-section template
			$this->template->caching= 0;
			
			if ($ari->get('mode') == 'admin') {  
				$this->template->template_dir= $ari->enginedir.DIRECTORY_SEPARATOR."admin".DIRECTORY_SEPARATOR.$ari->agent->getLang();
				$this->template->compile_id= $ari->mode."__".$ari->agent->getLang()."__";
			} else {
				$this->template->template_dir= $ari->filesdir.DIRECTORY_SEPARATOR.'perspectives'.DIRECTORY_SEPARATOR.$this->perspective.DIRECTORY_SEPARATOR.$ari->agent->getLang();
				$this->template->compile_id= $ari->agent->getLang()."_".$this->perspective."__";
			}
			
// $ari->internalChrono('p_new');

			///end smarty load
			//check to see if section template exist
			if (file_exists($this->template->template_dir.DIRECTORY_SEPARATOR.$ari->filename)) {
				$this->template->assign("maincontent", $ari->get('mod_content'));
				$this->template->assign("title", $ari->get('title'));
				$this->template->assign("keywords", $ari->get('keywords'));
				$this->template->assign("description", $ari->get('description'));
				$this->template->assign("author", $ari->get('author'));
				$this->template->assign("encoding", $ari->locale->get('encoding', 'general'));
// $ari->internalChrono('p_vars');				
				if ($ari->get('mode') == 'user')
					{$this->template->assign("webdir", $ari->webaddress );} // . $this->safeName()
			
				if ($ari->get('mode') == 'admin')
					{
							$this->template->assign("webdir", $ari->adminaddress);
								// Modules Selector
						/*	if ($menu = oob_module::adminModulesSelector ())
							{	$this->template->assign("mod_names", $menu["name"]);
								$this->template->assign("mod_values", $menu["value"]);
								$this->template->assign("mod_selected", $menu["selected"]);
								$this->template->assign("mod_selectedName", $menu["selectedName"]);
							}
								// Selected Module Menu
								$this->template->assign("modulemenu", $ari->module->adminMenu());
						*/							
								$this->template->assign("modules_menu", oob_module::adminFullMenu());
								$this->template->assign("mod_selected", $ari->module->name());
							
					}			
					//-----------------------------------------------------------------------
					//@todo => ver si quitar luego
					if($ari->get("user")) 
					{	
						$this->template->assign("currentUser", $ari->get("user")->name());
						$this->template->assign("logued", true);
					}
					else
					{
						$this->template->assign("logued", false);
					}

					//-----------------------------------------------------------------------
								
					
// $ari->internalChrono('p_morevars');


				$this->template->display($ari->filename);



			} else
				throw new OOB_exception('No existe la plantilla en la perspectiva: '.$ari->get('filename'), "73", "No existe la plantilla en la perspectiva", true);

// $ari->internalChrono('p_end');
 }
 
 static public function listPerspectives ()
 {
 	global $ari;

 // iterate throught "perspectives" dir to see whats available.
  if ($handle = opendir($ari->filesdir . DIRECTORY_SEPARATOR . 'perspectives')) {
   while (false !== ($file = readdir($handle))) { 
       if ($file != "." && $file != ".." && $file != ".svn") {  /// correccion para que no salga el dir de SUBVERSION
       if (is_dir ($ari->filesdir . DIRECTORY_SEPARATOR . 'perspectives' . DIRECTORY_SEPARATOR . $file))
      $availables[] = $file;
       } 
   }
   closedir($handle); 
 }
return $availables;

 }
 
 public function name ()
 {
 	return $this->perspective;
 }
  
 public function safeName ()
 {
 if ($this->perspective == 'default')
 {return "";}
 else
 {return '/' . $this->perspective;}
 
 }
 
    /**  Returns true if the type($role|module) is a member of the perspective     */
	public function isMember( $object )
    {
        global $ari;

        switch(get_class($object))
		{
			case 'seguridad_role':
			{
				$id = $ari->db->qMagic($object->get('role'));
				$perspective = $ari->db->qMagic($this->name());
				
	        	$sql= "SELECT True 
	        		   FROM OOB_RolesPerspective 
	        		   WHERE RoleID = $id 
	        		   AND PerspectiveName = '$this->perspective'";
				
				$rs = $ari->db->Execute($sql);	
				if (!$rs->EOF && $rs) 
				{	$return = true;		}
				else
				{	$return = false;	}	
				$rs->Close();
								
				break;
			}
			case 'OOB_module':
			{
				$module = $ari->db->qMagic($object->name());
				$perspective = $ari->db->qMagic($this->name());
				
	        	$sql= "SELECT True 
	        		   FROM OOB_ModulesPerspective
	        		   WHERE ModuleName = $module 
	        		   AND PerspectiveName = '$this->perspective'";
				
				$rs = $ari->db->Execute($sql);	
				if (!$rs->EOF && $rs) 
				{	$return = true;		}
				else
				{	$return = false;	}	
				$rs->Close();				
				
				break;
			}	
			default:
			{
				$ari->error->addError ("oob_perspective", "INVALID_MEMBER");
				$return = false; 
				break;	
			}			
		}
        return $return;
    }
    //end function 	
 
 	/**	*/
     static public function searchNoMembers($string,$status,$operator= OPERATOR_EQUAL, $perspective,$type)
    {
        global $ari;
		
		if (!is_a($perspective, "OOB_perspective"))
		{
			$ari->error->addError ("oob_perspective", "INVALID_PERSPECTIVE");
			return false;	
		}
		
		$return = false;
		
		$perspectiveName = $ari->db->qMagic($perspective->name());
		$string = $ari->db->qMagic("%" . $string . "%");
		$i = 0;
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$ari->db->SetFetchMode($savem);
		
        switch($type)
		{
			case ROLE:
			{
				$sql= "SELECT ID, Name, Description, Status, Anonymous "; 
	        	$sql .= "FROM Security_Role ";
	        	$sql .= "WHERE Name LIKE $string AND Status $operator $status ";
	        	$sql .= "AND ID NOT IN ";
	        	$sql .= "(SELECT RoleID FROM OOB_rolesperspective WHERE PerspectiveName = $perspectiveName)";
	        	//var_dump($sql);exit;
	        	$rs = $ari->db->Execute($sql);
	        							
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
	        	$sql .= "WHERE NiceName LIKE $string AND Status $operator $status ";
	        	$sql .= "AND ModuleName NOT IN ";
	        	$sql .= "(SELECT ModuleName FROM OOB_modulesperspective WHERE PerspectiveName = $perspectiveName)";
				
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
 
  	/** Adds a role to the current perspective.
 	 * Returns true if successful, false if not.
    */
 	public function addRole ($role)
 	{
 		global $ari;

		if (!is_a($role, "seguridad_role"))
		{
			$ari->error->addError ("oob_perspective", "INVALID_ROLE");
			return false;	
		}
		
		if ( $this->isMember($role) )
		{	return true; 	}
 		else
 		{
 			$ari->db->StartTrans();
 			
 			$role_id = $ari->db->qMagic($role->get('role'));
 			$perspectiveName = $ari->db->qMagic($this->name());
									
			$sql= "INSERT INTO OOB_rolesperspective 
				  (PerspectiveName, RoleID)
				   VALUES ($perspectiveName, $role_id )
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
 	//end function
 
 	/** Removes a role to the current perspective.
 	 * Returns true if successful, false if not.
    */ 	
 	public function removeRole ($role)
 	{
 		global $ari;
 		
		if (!is_a($role, "seguridad_role"))
		{
			$ari->error->addError ("oob_perspective", "INVALID_ROLE");
			return false;	
		}
		
		if (!$this->isMember($role) )
 		{   //not member
 			return false; 
 		}
 		else
 		{	//is member
			$ari->db->StartTrans();

 			$role_id = $ari->db->qMagic($role->get('role'));
 			$perspectiveName = $ari->db->qMagic($this->name());
									
			$sql= "DELETE FROM OOB_rolesperspective
				  WHERE RoleID = $role_id AND PerspectiveName = $perspectiveName
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
 	//end function 
 
	/**list roles members of the $perspective*/
 	static public function listRolesFor ($perspective)
 	{
		global $ari;
		
		if (!is_a($perspective, "OOB_perspective"))
		{
			$ari->error->addError ("oob_perspective", "INVALID_PERSPECTIVE");
			return false;	
		}
		
		$perspectiveName = $ari->db->qMagic ($perspective->name());
			
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$ari->db->SetFetchMode($savem);
		
		$sql  = "SELECT ID, Name, Description, Status, Anonymous  
	    		 FROM Security_Role 
	    		 WHERE Status <> " . DELETED . " 
	     		 AND ID IN 
	    		 (SELECT RoleID 
	    		  FROM OOB_RolesPerspective
	    		  WHERE PerspectiveName = $perspectiveName
	    		  )
	    		  ORDER BY Name";

		$rs = $ari->db->Execute($sql);
		
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

	/**		*/
	static public function listModulesFor ($perspective)
 	{
		global $ari;
		
		if (!is_a($perspective, "OOB_perspective"))
		{
			$ari->error->addError ("oob_perspective", "INVALID_PERSPECTIVE");
			return false;	
		}
		
		$perspectiveName = $ari->db->qMagic ($perspective->name());
		
		$perspectiveName = $ari->db->qMagic ($perspective->name());
			
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$ari->db->SetFetchMode($savem);

		$sql = "SELECT ID, ModuleName, Status, NiceName, Description, Optional 
	    		FROM OOB_modules_config
	    		WHERE Status <> " . DELETED . " 
	     		AND ModuleName IN 
	    		(SELECT ModuleName 
	    		 FROM OOB_ModulesPerspective
	    		 WHERE PerspectiveName = $perspectiveName
	    		 )
	    		ORDER BY NiceName";
		
		$rs = $ari->db->Execute($sql);
		
		$i = 0;
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
		else
		{$return = false;}
		$rs->Close();
	
		return $return;
		
	}
 	//end function

 	/** Adds a module to the current perspective.
 	 * Returns true if successful, false if not.
    */
 	public function addModule ($module)
 	{
 		global $ari;
		
 		if (is_a($module, "OOB_module"))
        {
 			if ( $this->isMember($module) )
 			{
 				$return = true; 
 			}
 			else
 			{
 				$moduleName = $ari->db->qMagic($module->name());
 				$perspectiveName = $ari->db->qMagic($this->name());
				$ari->db->StartTrans();
				
				$sql= "INSERT INTO OOB_modulesperspective 
					  (PerspectiveName, ModuleName)
					   VALUES ($perspectiveName, $moduleName )
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
			$ari->error->addError ("oob_perspective", "INVALID_MODULE");
			return false;	
		} 
 	}
 	//end function
	
 	/** Removes a module to the current perspecetive.
 	 * Returns true if successful, false if not.
    */ 	
 	public function removeModule ($module)
 	{
 		global $ari;
 		
 		if (is_a($module, "OOB_module"))
        {
 			if (!$this->isMember($module) )
 			{   //not member
 				$return = false; 
 			}
 			else
 			{	//is member
				$ari->db->StartTrans();
 				$moduleName = $ari->db->qMagic($module->name());
 				$perspectiveName = $ari->db->qMagic($this->name());
									
				$sql= "DELETE FROM OOB_modulesperspective 
					  WHERE ModuleName = $moduleName AND PerspectiveName = $perspectiveName
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
			$ari->error->addError ("oob_perspective", "INVALID_MODULE");     
			return false;       	
        }
        
 	}

 
}//end class 
?>

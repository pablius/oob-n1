<?php
/**
########################################
#OOB/N1 Framework [©2004,2006]
#
#  @copyright Pablo Micolini
#  @license BSD
######################################## 
*/
 class OOB_type {
 	
 	protected $id = ID_UNDEFINED;
	protected $name = '';
	protected $description = '';
	protected $status = '';
	//private $delete = true;
	private $table = '';
	protected $class = '';
		
 	public function id()
 	{
		return $this->id; 	
 	}
 	public function name()
 	{
		return $this->name; 	
 	}
	
	/** Starts the type. 
 	    if no type set we must believe is a new one */ 	
 	public function __construct ($id = ID_UNDEFINED, $table, $class, $datos = false)
 	{
 		global $ari;
 		
		$this->table = $table;
		$this->class  = $class;
		
		if ($id > ID_MINIMAL) {
			$this->id= $id;
				
			if (!$this->fill ($datos))
			{throw new OOB_exception("Invalid $class {$class}", "403", "Invalid $class", false);}
					
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
	
	/** Sets the variable (var), with the value (value) */ 	
 	public function set ($var, $value)
 	{
		if (isset ($this->$var))
			$this->$var= $value;
		else
			return false; 	
 	}
		
	/** Fills the type with the DB data */ 	
 	private function fill ($datos)
 	{
		global $ari;
		
		$mas_datos = "";
		if ( is_array($datos) )
		{			
			foreach($datos as $item)
			{	$mas_datos .= ",`" . $item['field'] . "`";	}
		}	
		
		$id = $ari->db->qMagic($this->id);
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
		$sql= "SELECT `Name`, `Description`, `Status` $mas_datos 
			   FROM $this->table 
			   WHERE id = $id
			  ";
			  
		$rs = $ari->db->Execute($sql);
		$ari->db->SetFetchMode($savem);
		if (!$rs || $rs->EOF) 
		{	return false;
		}
		if (!$rs->EOF) 
		{	$this->name = $rs->fields[0];
			$this->description = $rs->fields[1];
			$this->status = $rs->fields[2];
			$j = 3;
			if ( is_array($datos) )
			{	
				foreach($datos as $item)
				{	
					$this->$item['field'] = $rs->fields[$j];
					$j++;	
				}
			}				
		}
		$rs->Close();
		return true; 		
 	}

	
	public function store ($datos = array(), $type = "TYPE")
	{
		global $ari;

		// clean vars !
		$this->name = trim($this->name); 
		 if (!OOB_validatetext :: isClean($this->name) || !OOB_validatetext :: isCorrectLength ($this->name, 1, MAX_LENGTH))
		 {	$ari->error->addError (strtolower($this->class), "INVALID_NAME");
		 } 

		 if (!OOB_validatetext :: isClean($this->description) || 
		 	 !OOB_validatetext :: isCorrectLength ($this->description, 1, MAX_LENGTH))
		 {	
		 	//$ari->error->addError (strtolower($this->class), "INVALID_DESCRIPTION");
		 } 
		 
		 
		 if (!$ari->error->getErrorsfor($this->class))
		 {
			$id = $ari->db->qMagic($this->id);
			$name = $ari->db->qMagic($this->name);
			
			if ($this->id == ID_UNDEFINED) 					
			{//para nuevo busco uno con el mismo nombre
				$clausula = "";
			}
			else
			{//si actualizo busco con el mismo nombre pero con el mismo id
				$clausula = " AND id <> $id ";	
			}
			
			$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
			$sql= "SELECT true as cuenta FROM $this->table WHERE `Name` = $name $clausula";
			$rs = $ari->db->Execute($sql);
			$ari->db->SetFetchMode($savem);

			if (!$rs->EOF && $rs->fields[0]!= 0) 
			{						
				if ($this->id == ID_UNDEFINED) 					
				{//para nuevo 
					
					$status = $ari->db->qMagic(DELETED);
					// si el rol con el mismo nombre esta borrado lo activo, sino da instancio un error
					$sql= "SELECT id FROM $this->table WHERE `Name` = $name AND Status = $status ";
					$rs->Close();
					$rs2 = $ari->db->Execute($sql);
					if (!$rs2->EOF) 
					{	
						
						//asigno el id del el objeto que volvi a activar
						$this->id = $rs2->fields[0];
						$id = $ari->db->qMagic($this->id);
						// $this->status = USED;
					}	
					else
					{
						$ari->error->addError (strtolower($this->class), "DUPLICATE_" . $type);
					}
				}
				else
				{
					$ari->error->addError (strtolower($this->class), "DUPLICATE_" . $type);
				}
			}

		}//--
				
		if (!$ari->error->getErrorsfor($this->class))
		{
			
			$name =$ari->db->qMagic($this->name);
			$description =$ari->db->qMagic($this->description);
			$status =$ari->db->qMagic($this->status);
			
			if ($this->id > ID_MINIMAL)
			{
				$update = "";
				
				foreach($datos as $item)
				{
					$item['value'] = $ari->db->qMagic($item['value']);
					$update .= ",`" . $item['field'] . "` = " . $item['value'];
				}	 
				// update data
				$ari->db->StartTrans();
				$sql= "UPDATE $this->table 
					   SET `Name` = $name, `Description` = $description,  
						   `Status` = $status $update WHERE id = $id
						   ";
				$ari->db->Execute($sql);
				
			
			
				
				if (!$ari->db->CompleteTrans())
					throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false); //return false;
				else
					return true;	
				
				} else {
				// insert new and set roleid with new id
				$ari->db->StartTrans();
				
				$fields = "";
				$values = "";
				
				foreach($datos as $item)
				{
					$fields .= ",`" . $item['field'] . "`";
					$item['value'] = $ari->db->qMagic($item['value']);
					$values .= "," . $item['value'];
				}	
									
				$sql= "INSERT INTO $this->table 
					   ( `Name`, `Description`, `Status` $fields)
					   VALUES 
					   ( $name, $description, $status $values)
						";
				$ari->db->Execute($sql);
				$this->id = $ari->db->Insert_ID();
			
				if (!$ari->db->CompleteTrans())
				{	throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false); 
				}
				else
				{
					return true;
				}
			}
		} 
		else 
		{	// no validan los datos
			return false; 
		}
		
 	}//end function
 	
	
	/** Deletes type object from the DB*/ 	
 	public function delete ()
 	{
		global $ari;
		// sets status DELETED for a type-id
		if ($this->id > ID_MINIMAL && $this->status != DELETED) 
		{
			$id = $ari->db->qMagic($this->id);
			$status =$ari->db->qMagic(DELETED);
			
			$ari->db->StartTrans();
			$sql= "UPDATE $this->table SET  Status = $status WHERE ID = $id";
			
			$ari->db->Execute($sql);
								
		
			if ($ari->db->CompleteTrans())
			{	return true;
			}
			else
			{	throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false);
			}		

		} 
		else 
		{
			if ($this->status == DELETED)
			{	$ari->error->addError ("oob_type", "ALREADY_DELETED");
			}
			else
			{	$ari->error->addError ("oob_type", "NO_SUCH_TYPE");
			}
			return false;
		} 		
 	}
	
	/** Shows the available sorting ways for type */
	static public function getOrders()
	{
		$return[] = "name";
		$return[] = "id";
		$return[] = "description";
		$return[] = "status";
		
		return $return;
		
	}//end function 	

	/** Shows the id status or all available status 
	 * $one = ID or "status_string" returns the id status; or "ALL" to return an array.
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

 	/** Returs the types on the system. status = (used/deleted/all) shows types */
	static public function listTypes ($status = USED, $sort = 'name', $operator = OPERATOR_EQUAL, $table, $class, $datos = false)
	{
		global $ari;
/*
		if (in_array ($status, seguridad_group::getStatus ("ALL",true)) && $status != "all")
		{
			$estado = "WHERE status $operator '$status'";
		}
		else
		{
			if ($status == "all")
				{$estado = "";}
			else
				{$estado = "WHERE status $operator '". USED. "'";}
		}
*/
		if ($status == "all")
		{	$estado = "";}
		else
		{	$status = $ari->db->qMagic($status);
			$estado = "WHERE status $operator $status ";
		}
			
		$mas_datos = "";
		if ( is_array($datos) )
		{			
			foreach($datos as $item)
			{	$mas_datos .= ",`" . $item['field'] . "`";	}
		}		
		
		if (in_array ($sort, OOB_type::getOrders()))
			$sortby = "ORDER BY $sort";
		else
			$sortby = "ORDER BY name";

		$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
		$sql= "SELECT `ID`,`Name`,`Description`,`Status` $mas_datos 
			   FROM $table $estado $sortby";
		
		$rs = $ari->db->Execute($sql);
		$i = 0;
			$ari->db->SetFetchMode($savem);
				if ($rs && !$rs->EOF) { // aca cambie sin probar, hay q ver si anda!
					while (!$rs->EOF) {	
					$return[$i] = new $class (ID_UNDEFINED);
					$return[$i]->set('id',$rs->fields[0]);
					$return[$i]->set('name',$rs->fields[1]);
					$return[$i]->set('description',$rs->fields[2]);
					$return[$i]->set('status',$rs->fields[3]);
					$j = 4;
					if ( is_array($datos) )
					{	
						foreach($datos as $item)
						{	
							$return[$i]->set($item['field'],$rs->fields[$j] );
							$j++;	
						}
					}
					$i++;
					$rs->MoveNext();
					}			
				$rs->Close();
				} else
				{return false;}

		return $return;
	}
 	//end function

	/** Returs the types on the system. status = (used/deleted/all) shows types */
	static public function listTypesRestricted ($status = USED, $sort = 'name', $operator = OPERATOR_EQUAL, $table, $class, $datos = false, $restrictedTypes)
	{
		global $ari;
		
		$clause="";
		if ($status <> "all")
		{	$status = $ari->db->qMagic($status);
			$clause = " AND status $operator $status ";
		}

		if (!is_array ($restrictedTypes))
		{	$restrictedTypes = array($restrictedTypes);
		}
		
		$first = true;
		foreach($restrictedTypes as $type)
		{	
			$type = $ari->db->qMagic($type);
			if($first)
			{	$lista = $type;
				$first = false;
			}
			else
			{	$lista.= "," . $type;
			}
		}
		
		$mas_datos = "";
		if ( is_array($datos) )
		{			
			foreach($datos as $item)
			{	$mas_datos .= "," . $item['field'];	}
		}		
		
		if (in_array ($sort, OOB_type::getOrders()))
			$sortby = " ORDER BY $sort";
		else
			$sortby = " ORDER BY name";

		$sql = "SELECT ID,Name,Description,Status $mas_datos 
				FROM $table 
				WHERE ID NOT IN ($lista)
				$clause 
				$sortby
			   ";
				
		//echo $sql;exit;

		$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
		$rs = $ari->db->Execute($sql);
		$ari->db->SetFetchMode($savem);
		$i = 0;
		if ($rs && !$rs->EOF) 
		{ 
			while (!$rs->EOF) 
			{	
				$return[$i] = new $class (ID_UNDEFINED);
				$return[$i]->set('id',$rs->fields[0]);
				$return[$i]->set('name',$rs->fields[1]);
				$return[$i]->set('description',$rs->fields[2]);
				$return[$i]->set('status',$rs->fields[3]);
				$j = 4;
				if ( is_array($datos) )
				{	
					foreach($datos as $item)
					{	
						$return[$i]->set($item['field'],$rs->fields[$j] );
						$j++;	
					}
				}
				$i++;
				$rs->MoveNext();
			}			
			$rs->Close();
		} 
		else
		{	return false;
		}

		return $return;
		
	}//end function

 	

 }//end class 
 
?>

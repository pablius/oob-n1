<?php
/**
########################################
#OOB/N1 Framework [©2004,2009]
#
#  @copyright Pablo Micolini
#  @license GPL v3
#  @version 1.9.13 - (RC6)
#  Notice: BUGS: There are some cases where filters are not taking into account the operator. 
#  Missing Features for V2: Remote Sort.
######################################## 
*/

/**
 This class defines an extendable ORM storage class 
 */
 
abstract class OOB_model_type extends OOB_model
{
	static protected $public_properties = array(); // property => constraints
	static protected $belongs = array(); // classes that uses the object
	
	static protected $table; // must be redefined for each class 
	static protected $class = __CLASS__; // must be redefined for each class 
	
	public $status;	// object status normally (enabled or deleted) (each object can override this)
	
	protected $array_manager  = array(); // internally used to store relation mods, do not use/change
	protected $hard_delete = false; // if true the object is deleted permanently
	
	/** static method to know an object table in DB*/
	static public function getTable()
	{
		return static::$table;
	}

	/** static method to know where are we calling the method from */
	static public function getClass()
	{
		return static::$class;
	}
	
	public function get($var)
	{
		// no permite entrar a los id directamente
		if (eregi('^id_',$var))
		{
			throw new OOB_exception("Access to property not allowed. Object Expected", "801", "Object Expected", true);
		}
		
		//no permite entrar a los array directamente
		if (eregi('^array_',$var))
		{
			throw new OOB_exception("Access to property not allowed. Use Array Methods instead", "802", "Method not suitable for attribute", true);
		}
		
		// devuelve los relacionados si le pasaste el nombre correcto
		if(array_key_exists('array_' . $var,static::$public_properties) && eregi('(^manyobjects-)(.*)',strtolower(static::$public_properties['array_' . $var]),$reg))
		{
			$class = $reg['2'];
			return $this->__getInverseRelated($class);
		}
				
		// devuelve el objeto que corresponde
		if(array_key_exists('id_' . $var,static::$public_properties) && eregi('(^object-)(.*)',strtolower(static::$public_properties['id_' . $var]),$reg))
		{
			// si tiene relación, busca la clase en el campo correspondiente
			if ($reg['2'] == 'relation')
			{
				$field_name = 'class_' . $var;
				$class = $this->$field_name;
			} 
			else
			{
				$class = $reg['2'];
			}
					
			$var = 'id_' . $var;
			return new $class($this->$var);
		}
		else
		{ 	// es una variable simple
			// @fixme -> if numeric convert to locale
			return $this->$var;
		}
		
	}
	
	/** sets the variable value */
	public function set($var,$value)
	{
		// no permite entrar a los id directamente
		if (eregi('^id_',$var))
		{
			throw new OOB_exception("Access to property not allowed. Object Expected", "803", "Object Expected", true);
		}
		
		//no permite entrar a los array directamente
		if (eregi('^array_',$var))
		{
			throw new OOB_exception("Access to property not allowed. Use Array Methods instead", "804", "Method not suitable for attribute", true);
		}
		
		// agrega el elemento al array si es del tipo correcto
		if(array_key_exists('array_' . $var,static::$public_properties) && eregi('(^manyobjects-)(.*)',strtolower(static::$public_properties['array_' . $var]),$reg))
		{
			throw new OOB_exception("Can't access array from SET, use specific functions", "805", "Can't access array directly, use specific functions", true);
			/*$field_name = 'array_' . $var;

			if (is_a($value,$reg[2]))
			{
				// agrega el elemento al array
				array_push($this->$field_name, $value);
			}
			else
			{
				throw new OOB_exception("Add element of incorrect type not allowed", "806", "Can't add that kind of object", true);
			}*/
		}
		
		// agrega el objeto que corresponde
		if(array_key_exists('id_' . $var,static::$public_properties) && eregi('(^object-)(.*)',strtolower(static::$public_properties['id_' . $var]),$reg))
		{
			
			if (!is_object($value))
			{
				throw new OOB_exception("Element is not an object. An object was expected. Provide an object for: ".$var."!", "807", "An object was expected in {$var}", true);
			}
			
			if ($reg['2'] == 'relation')
			{
				$field_name = 'class_' . $var;
				$this->$field_name = get_class($value);
			}
			
			$var = 'id_' . $var;
			$this->$var = $value->id(); // @fixme -> if the object is not stored, should be marked for storage previous to storing this object.
		}
		else
		{	// es una variable simple
		
			if (is_object($value) && !is_a($value,'Date')) // date no tiene ID!
			{
				throw new OOB_exception("Element is an object. A string/number was expected", "808",
				"A string/number was expected in {$var}, remember that Objects should start with 'id_'", true);
			} 
			else
			{
				// @fixme -> if numeric convert to locale
				$this->$var = $value;
			}
		}
	}
	
	
	 public function __construct ($id = ID_UNDEFINED)
 	{
 		global $ari;
 				
		if ($id > ID_MINIMAL && OOB_numeric::isValid($id)) 
		{
			$this->id= $id;
				
			if (!$this->fill ())
			{throw new OOB_exception("Invalid " . static::getClass() . " id: " .$id , "814", "Invalid class", true);}
					
		}  
 	}
	
	
	/**
	devuelve el listado de status 
	-----------------------------
	$one = > cual devolver (ALL para todos)
	$id => devuelve el string if false
	*/
	static public function getStates ( $status = "ALL" ,$id = true) 
	{

		$return = static::$states;
		
		if ($id != true)
			$return = array_flip ($return);
		
		if ($status != "ALL")
		{
			if ($return[$status] !== "" )
			{
				$return = $return[$status];
			}
			else
			{
				$return =  false;
			}
		}
	
		return $return;

	}
	
	/** muestra las formas de ordenar los datos del objeto */
	static public function getOrders()
	{
		return static::$orders;	
	}
	
	/** Fills the type with the DB data */ 	
 	protected function fill ()
 	{
		global $ari;
		$table = static::getTable();
				
		$id = $ari->db->qMagic($this->id);
		
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		$sql= "SELECT *
			   FROM $table
			   WHERE id = $id
			  "; // $mas_datos, status
	  
		$rs = $ari->db->Execute($sql);
		$ari->db->SetFetchMode($savem);

		if ($rs && !$rs->EOF) 
		{	
			$j = 0;
			
			if ( is_array(static::$public_properties) )
			{	
				foreach(static::$public_properties as $property_key => $property_constraints)
				{	
					if (!stristr(strtolower($property_constraints),'manyobjects'))
					{
						// if constraints says its a date object, change to it
						if (stristr(strtolower($property_constraints),'object-date'))
						{
							$this->$property_key = new Date($rs->fields[$property_key]);
						}
						else
						{
							$this->$property_key = $rs->fields[$property_key];
						}
					
						$j++;
					}	
				}
			}
			
			$this->status = $rs->fields['status'];
			
			$rs->Close();		
			return true; 		
		}
		
		return false;
		
 	}
	
	/* factory method for colections */
	protected function __fill($rs)
	{
		global $ari;
		$j = 0;

		if ( is_array(static::$public_properties) )
		{	
			foreach(static::$public_properties as $property_key => $property_constraints)
			{	
				if (!stristr(strtolower($property_constraints),'manyobjects'))
				{
					// if constraints says its a date object, change to it
					if (stristr(strtolower($property_constraints),'object-date'))
					{
						$this->$property_key = new Date($rs->fields[$property_key]);
					}
					else
					{
						$this->$property_key = $rs->fields[$property_key];
					}
				}	
				
				$j++;
			}
		}
		
		$this->status = $rs->fields['status'];
		$this->id = $rs->fields['id'];
		
	}
	
	/** this function deletes the object, and all related objects under it */
	public function delete()
	{
		global $ari;
		$table = static::getTable();
		
		if (!$this->allowDelete())
		{
			$this->error()->addError("NO_DELETE_ALLOWED");
			return false;
		}
		
		// sets status DELETED 
		if ($this->id > ID_MINIMAL && $this->status != DELETED) 
		{
			$id = $ari->db->qMagic($this->id);
			$status =$ari->db->qMagic(DELETED);
			
			// objetos que no quieren que se cambie el status
			if ($this->hard_delete)
			{
				$sql= "DELETE FROM $table WHERE ID = $id";
			}
			else
			{
				$sql= "UPDATE $table SET  Status = $status WHERE ID = $id";
			}
			
			$ari->db->StartTrans();
			
			$class_remota = array();
			
			foreach (static::$public_properties as $key => $value)
			{
				if (eregi('(^manyobjects-)(.*)',$value,$reg))
				{
					$class_remota[] = $reg[2];
				}
			}
			
			if (count($class_remota) > 0)
			{
				foreach($class_remota as $class_name)
				{
						if ($related_objects = $class_name::getRelated($this))
						{
							foreach ($related_objects as $obj)
							{
								if (!$obj->delete())
								{
									$ari->db->FailTrans();
								}
							}
						}
	
				}
			}
			
		
			$ari->db->Execute($sql);
										
			if ($ari->db->CompleteTrans())
			{	
				return true;
			}
			else
			{	
				return false;
				// throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "815", "Error en la Base de Datos", true);
			}		

		} 
		else 
		{
			if ($this->status == DELETED)
			{	
				$this->error()->addError("ALREADY_DELETED");
			}
			else
			{	
				$this->error()->addError("NO_ID_SET");
			}
			
			return false;
		} 
	}

	/* esta funcion valida si un objeto es duplicado, 
	   la implementación depende da cada objeto en particular, 
	   pero el metodo es llamado siempre que se haga un store 
	*/
	protected  function isDuplicated()
	{
		/* 
		
		CODIGO DE EJEMPLO
		-----------------
		
		global $ari;
		$table = static::getTable();
		 
		 if ($this->id == ID_UNDEFINED) 					
		{	
			//para nuevo busco uno con el mismo nombre
			$clausula = "";
		}
		else
		{	
			//si actualizo busco con el mismo nombre pero con el mismo id
			$clausula = " AND id <> $id ";	
		}
			
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
		$sql= "SELECT true as cuenta FROM $table WHERE `Name` = $name $clausula";
		$rs = $ari->db->Execute($sql);
		$ari->db->SetFetchMode($savem);

		if (!$rs->EOF && $rs->fields[0]!= 0) 
		{
			return true;						
		}
		else
		{
			return false;
		}
		
		
		*/
		return false; 
	}

	/** saves the object in DB, also stores items from m-2-n relations */
	public function store ()
	{
		global $ari;
		$table = static::getTable();

			
		if(!$this->isValid())
		{
			return false;
		}
			
		
		$ari->db->StartTrans();
		
		$status = $ari->db->qMagic($this->status);
		
		if ($this->id > ID_MINIMAL)
		{
			$update_sql_array = array();
			$update_sql = "";
			$id = $ari->db->qMagic($this->id);
			
			foreach(static::$public_properties as $property_key => $property_constraints)
			{
				if (!stristr(strtolower($property_constraints),'manyobjects'))
				{
					// if constraints says its a date object, change to string
					if (stristr(strtolower($property_constraints),'object-date'))
					{
						
						$update_sql_array[] = "`" . $property_key . "` = " . $ari->db->qMagic($this->$property_key->getDate());
					}
					elseif (stristr(strtolower($property_key),'id_') && $this->$property_key == '')
					{
						$update_sql_array[] = "`" . $property_key . "` = " . $ari->db->qMagic(0);
					}
					else
					{
						
						$update_sql_array[] = "`" . $property_key . "` = " . $ari->db->qMagic($this->$property_key);
						
					}
				}
			}
			
			$update_sql = join (',',$update_sql_array);	 
			
			// update data
			$sql= "UPDATE $table 
				   SET $update_sql , `status` = $status
				   WHERE id = $id
				   ";
			$ari->db->Execute($sql);

		}
		else
		{
		
			$fields = "";
			$fields_array = array();
			$values = "";
			$values_array = array();
			
			
			
			foreach(static::$public_properties as $property_key => $property_constraints)
			{
				
				if (!stristr(strtolower($property_constraints),'manyobjects'))
				{
					$fields_array[] = "`" . $property_key . "`";
					
					// if constraints says its a date object, change to string
					if (stristr(strtolower($property_constraints),'object-Date'))
					{
						$values_array[] = $ari->db->qMagic($this->$property_key->getDate());
					}
					elseif (stristr(strtolower($property_key),'id_') && $this->$property_key == '')
					{
						$values_array[] = $ari->db->qMagic(0); // an unexistant ID. This allows for negative ids to be used as special cases
					}
					else
					{
						$values_array[] = $ari->db->qMagic($this->$property_key);
					}
				}
				
				
			}
				
			$fields = join (',',$fields_array);	 
			$values = join (',',$values_array);	 
			
			if (!in_array($this->status, self::getStates()))
			{
				$this->status = ENABLED;
			}
			
			$status = $ari->db->qMagic($this->status); 
					
			$sql= "INSERT INTO $table 
				   ($fields, `status`)
				   VALUES 
				   ($values, $status)
					";
					
				//	var_dump($sql);
			$ari->db->Execute($sql);
			$this->id = $ari->db->Insert_ID();
		
		}
		
		// aca se guardo el objeto y tenemos un ID
		
		// removemos los que estan marcados para borrar si es que se puede
		if(isset($this->array_manager['remove']))
		{
			foreach ($this->array_manager['remove'] as $remove)
			{
				foreach ($remove as $item_id => $item)
				{
					if ($item->allowDelete())
					{
						$item->delete();
					}
					else
					{
						$ari->db->FailTrans();
						// return false; // si o no?
					}
				}
			}
		}

		if(isset($this->array_manager['add']))
		{
			foreach ($this->array_manager['add'] as $key => $add)
			{
				eregi('(^manyobjects-)(.*)',strtolower(static::$public_properties['array_' . $key]),$reg);
				
				$class_remota = $reg[2];
				
				$remote_public_properties = $class_remota::$public_properties;
				
				// 3 casos
				if ($campo_id = array_search('object-' . static::getClass(),$remote_public_properties))
				{	// uno, el objeto de destino tiene un solo campo del tipo en cuestion, lo elegimos y listo
					$campo_clase = false;
				} 
				elseif ($campo_id = array_search('object-related',$remote_public_properties))
				{	// dos, el objeto de destino tiene dos campos, uno para el valor, y el otro para el nombre de la clase
					$campo_clase = 'class_' . substr($campo_id,3,strlen($campo_id));
				}
				else
				{
					throw new OOB_exception("Couldn't find object relation definition for " . static::getClass(), "816", "Object relation definiton not found", true);
				}
								
				// tres, tiene mas de uno, pero nuestro objeto nos dice a cual campo correspondemos nosotros (?)
				// este caso creo q no puede existir!, dejame que lo piense =)
				
				
				// NOTA: En realidad a mi no me hace falta el valor $campo_clase...
				$var = substr($campo_id,3,strlen($campo_id));
				
				
			
				foreach ($add as $item)
				{
					$item->set($var,$this);
					if (!$item->store())
					{
						//var_dump ($item); echo"<br>";
						$ari->db->FailTrans();
					}
				}
			}
		}
			
		if (!$ari->db->CompleteTrans())
		{	
			//throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "817", "Error en la Base de Datos", true); 
			return false;
		}
		else
		{
			return true;
		}					
	}

	/* esta funcion valida si un objeto puede borrarse, 
	   la implementación depende da cada objeto en particular, 
	   pero el metodo es llamado siempre que se haga un delete 
	*/
	public function allowDelete() 
	{
		if (count(static::$belongs) > 0)
		{
			foreach (static::$belongs as $class_name)
			{
				// si hay elementos relacionados no lo podemos borrar
				if ($class_name::getRelated($this,true) > 0) 
				{
					return false;
				}
			}
		
		}
		
		return true;
	}

	
	static public function getList($offset = false, $numrows = false, $sort = false, $sort_mode = false, $status = false, $operator = false, $avoid = false, $sql_extra = '')
	{
		global $ari;
		$table = static::getTable();
		$class = static::getClass();
		
		$clause="";
		$sortby="";
		$avoid_sql = "";
		$return = false;
		
		if (!is_int($offset))
		{
			$offset = 0;
		}
		
		if (!is_int($numrows) || $numrows < 1)
		{
			$numrows = 100;
		}
		
		// operator validation
		if (!in_array($operator,array(OPERATOR_EQUAL, OPERATOR_DISTINCT, OPERATOR_GREATER,OPERATOR_SMALLER)))
		{
			$operator = OPERATOR_EQUAL;
		}
		
		// status validation
		if ($status <> "all" && !in_array($status, static::getStates()))
		{	
			$status = ENABLED;
		}
		
		if ($status == "all")
		{
			$clause = "WHERE 1 = 1"; 
		}
		else
		{
			$status = $ari->db->qMagic($status);
			$clause = " WHERE status $operator $status ";
		}
		
		//sort mode validation
		if (!in_array ($sort_mode, array(ASC,DESC)))
		{
			$sort_mode = ASC;
		}
		
		
		//sort validation
		if (in_array ($sort, static::getOrders()))
		{
			$sortby = " ORDER BY $sort ". $sort_mode;
		}
		else
		{
			$sortby = " ORDER BY id " . $sort_mode;
		}
		
		// items to avoid 
		if ($avoid !== false)
		{
			$avoid_sql = "AND id NOT IN (";
			$avoid_array = array();
			
			
			
			foreach ($avoid as $avoid_object)
			{
			
				if (!is_a($avoid_object, __CLASS__))
				{
					throw new OOB_exception("Invalid class provided", "818", "Invalid class provided", true);
				}
				else
				{
					if (!in_array($avoid_object->id(),$avoid_array))
					{
						$avoid_array[] = $avoid_object->id();
					}
				}

				
			}
			$avoid_sql .= join (',',$avoid_array);
			$avoid_sql .= ") ";
		
		}
		
		// get items from DB
		$sql = "SELECT $table.*
				FROM $table 
				$clause 
				$avoid_sql
				$sql_extra
				$sortby
			   ";
		//var_dump($sql);
		
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
	
		$rs = $ari->db->SelectLimit($sql, $numrows, $offset); 
		$ari->db->SetFetchMode($savem);
		
		$i = 0;
		
		if ($rs && !$rs->EOF) 
		{ 
			while (!$rs->EOF) 
			{	
				$return[$i] = new $class();
				$return[$i]->__fill($rs);
				$i++;
				$rs->MoveNext();
			}			
			$rs->Close();
		
		} 

		return $return;
		
	}
	
	static public function getListCount($status = false, $operator = false, $avoid = false, $sql_extra = '')
	{
		global $ari;
		$table = static::getTable();
		$class = static::getClass();
		
		$clause="";
		$sortby="";
		$avoid_sql = "";
		$return = false;
		
		// operator validation
		if (!in_array($operator,array(OPERATOR_EQUAL, OPERATOR_DISTINCT, OPERATOR_GREATER,OPERATOR_SMALLER)))
		{
			$operator = OPERATOR_EQUAL;
		}
		
		// status validation
		if ($status <> "all" && !in_array($status, static::getStates()))
		{	
			$status = ENABLED;
		}
		
		if ($status == "all")
		{
			$clause = "WHERE 1 = 1"; 
		}
		else
		{
			$status = $ari->db->qMagic($status);
			$clause = " WHERE status $operator $status ";
		}
		
		// items to avoid 
		if ($avoid !== false)
		{
			$avoid_sql = "AND id NOT IN (";
			$avoid_array = array();
			
			
			
			foreach ($avoid as $avoid_object)
			{
			
				if (!is_a($avoid_object, __CLASS__))
				{
					throw new OOB_exception("Invalid class provided", "819", "Invalid class provided", true);
				}
				else
				{
					if (!in_array($avoid_object->id(),$avoid_array))
					{
						$avoid_array[] = $avoid_object->id();
					}
				}
				
					
				
				
			}
			$avoid_sql .= join (',',$avoid_array);
			$avoid_sql .= ") ";
		
		}
		
		// get count from DB
		$sql = "SELECT COUNT(id) as count
				FROM $table 
				$clause 
				$avoid_sql
				$sql_extra
				$sortby
			   ";
			   
		
	
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
	
		$rs = $ari->db->Execute($sql); 
			
		$ari->db->SetFetchMode($savem);
			
		if ($rs && !$rs->EOF) 
		{ 
			$return = $rs->fields[0];
			$rs->Close();
		
		} 

		return $return;
		
	}
	
	
	/* este método valida que los atributos del objeto tengan valores válidos, 
	   puede ser heredado por la implementación, o cambiado completamente */
	public function isValid()
	{
	
		foreach(static::$public_properties as $property_key => $property_constraints)
		{
			if (strtolower($property_constraints) != 'object') // @verify
			{
				$this->validateVariable ($property_key, $property_constraints, "NO_" . strtoupper($property_key));
			} 
			else
			{
				// no tiene que llegar un objeto vacio al SQL 	
				if ($this->$property_key = '' || $this->$property_key < ID_MINIMAL)
				{
					$this->error()->addError ("NO_" . strtoupper($property_key));
				}
			}	
		}
		
		// validamos si está duplicado
		if($this->isDuplicated())
		{
			$this->error()->addError("DUPLICATED");
		}
		
	
		if ($this->error()->areError())
		{
			return false;
		}
		else
		{
			return true;
		}
	
	
	}
	
	/* ORM RELATIONAL METHODS */
		
	/** adds an item to a m-2-n relation */
	public function addRelated ($var, $object)
	{
		// agrega el elemento al array si es del tipo correcto
		
		if(array_key_exists('array_' . $var,static::$public_properties) && eregi('(^manyobjects-)(.*)',strtolower(static::$public_properties['array_' . $var]),$reg))
		{
			$field_name = 'array_' . $var;

			if (is_a($object,$reg[2]))
			{
				// agrega el elemento al array_manager
				if ($object->id() > ID_MINIMAL)
				{
					throw new OOB_exception("Adding previously saved element not allowed", "809", "Adding previously saved element not allowed", true);
				}
				else
				{
					if (!isset($this->array_manager['add'][$var]))
					{
						$this->array_manager['add'][$var] = array();
					}
				
					array_push($this->array_manager['add'][$var], $object);
				}
				
			}
			else
			{
				throw new OOB_exception("Add element of incorrect type not allowed", "810", "Can't add that kind of object", true);
			}
		} 
		else
		{
			throw new OOB_exception("Can't add, non-defined relation", "811", "Can't add, non-defined relation", true);
		}
	
	}
	
	/** returns items of the $class_name that have $this inside */
	protected function __getInverseRelated($class_name)
	{
		global $ari;
		$valor = $ari->db->qMagic($this->id());
		
		foreach($class_name::$public_properties as $campo => $constraint)
		{
			// buscamos en la definición al campo que guarda nuestros datos // falta decirle que asigne el tipo si es del tipo "related"
			if (strcasecmp('object-' . static::getClass(),$constraint) == 0) //(eregi('(^object-'.strtolower(static::getClass()).')(.*)',strtolower($constraint)))
			{
				// pedimos un list de sus objetos que nos tengan a nosotros adentro
				return $class_name::getList(false, false, false, false, false, false, false, "AND $campo = $valor");
			}
			elseif (strcasecmp('object-relation',$constraint) == 0)//(eregi('(^object-relation)(.*)',strtolower($constraint)))
			{
				// si las relaciones son del tipo "object-relation"
				$object_valor = $ari->db->qMagic($class_name);
				$object_campo = 'class' . substr($campo,2,strlen($campo));
				
				return $class_name::getList(false, false, false, false, false, false, false, "AND $campo = $valor AND $object_campo = $object_valor ");				
			}
		} 	
	}
	
	/** returns items of __CLASS__ that have $object inside 
		$count => only returns the ammount of related items.
	*/
	static public function getRelated($object, $count = false)
	{
		// buscamos los datos del objeto
		global $ari;
		$valor = $ari->db->qMagic($object->id());
		$object_class = get_class($object);
				
		$our_class = static::getClass();
		
		$sql = false;
	
		foreach($our_class::$public_properties as $campo => $constraint)
		{
			// tiene que estar definido que hay relaciones multiples en nuestro objeto
			if (strcasecmp('object-' . $object_class,$constraint) == 0) //eregi('(^object-'.strtolower($object_class).')(.*)',strtolower($constraint)))
			{
				// pedimos un list de nuestros objetos que lo tengan a él como hijo
				$sql = "AND $campo = $valor";
			}
			elseif (strcasecmp('object-relation',$constraint) == 0)//(eregi('(^object-relation)(.*)',strtolower($constraint)))
			{
				// si las relaciones son del tipo "object-relation"
				$object_valor = $ari->db->qMagic($object_class);
				$object_campo = 'class' . substr($campo,2,strlen($campo));
				
				$sql = "AND $campo = $valor AND $object_campo = $object_valor ";				
			}
			
		}
		
		if ($sql != false)
		{
			if (!$count)
			{
				return static::getList(false, false, false, false, false, false, false, $sql);
			}
			else
			{
				return static::getListCount(false, false, false, $sql);
			}
		}
		
		return false;		
	}
	
	/** removes an item to a m-2-n relation */
	public function removeRelated ($var, $object)
	{
		global $ari;
	// marcamos para quitar el elemento al array si es del tipo correcto
		if(array_key_exists('array_' . $var,static::$public_properties) && eregi('(^manyobjects-)(.*)',strtolower(static::$public_properties['array_' . $var]),$reg))
		{
			$field_name = 'array_' . $var;

			if (is_a($object,$reg[2]))
			{
				if (!isset($this->array_manager['remove'][$var]))
				{
					$this->array_manager['remove'][$var] = array();
				}

				// nosotros recibimos un objeto, que tiene la relacion, pero no tiene el ID. 
				// Tenemos que buscar el objeto que tenga esa relación,y recuperar ese ID.
				if ($object->id() > ID_MINIMAL)
				{
					// agrega el elemento al array_manager
					if (!array_key_exists($object->id(),$this->array_manager['remove'][$var]))
					{
						$this->array_manager['remove'][$var][$object->id()] = $object;
					}
				}
				else
				{
					// @fixme UNTESTED!
					
					// recuperamos la clase del objeto 
					$class_remota = get_class($object);
					$object_class = static::getClass();
					$field = false;

					// recuperamos la variable en la que está nuestro objeto
					foreach($class_remota::$public_properties as $campo => $constraint)
					{
						// tiene que estar definido que hay relaciones multiples en nuestro objeto
						if (
							(strcasecmp('object-' . $object_class,$constraint) == 0) //eregi('(^object-'.strtolower($object_class).')(.*)',strtolower($constraint)) 
							||
							(strcasecmp('object-relation',$constraint) == 0) //(eregi('(^object-relation)(.*)',strtolower($constraint)))
							)
						{
							 $field = substr($campo,3,strlen($campo));
						}
					}
					
					

					if ($field !== false)
					{
						if ($object->get($field)->id() != NULL && $object->get($field)->id() != $this->id())
						{
							throw new OOB_exception("Element relates to diferent object", "822", "Element relates to diferent object", true);
						}	
						else
						{
							// de pecho que es un objeto del mismo tipo, no? 
							$related_object = $object->set($field,$this); 
						}
					}
					else
					{
						throw new OOB_exception("Can't find element relation attribute", "821", "Can't find element relation attribute", true);
					}
					
					
					
					// creamos un string con las variables del objeto segun sus public properties
					$related_sql_array = array();
					foreach($class_remota::$public_properties as $property_key => $property_constraints)
					{
						if (!stristr(strtolower($property_constraints),'manyobjects'))
						{
							// if constraints says its a date object, change to string
							if (stristr(strtolower($property_constraints),'object-date'))
							{
								
								$related_sql_array[] = "`" . $property_key . "` = " . $ari->db->qMagic($object->$property_key->getDate());
							}
							else
							{
									
								$related_sql_array[] = "`" . $property_key . "` = " . $ari->db->qMagic($object->$property_key);
							}
						}
					}
			
					
					$related_sql = 'AND ' . join (' AND ',$related_sql_array);
					
				//	throw new OOB_exception(var_export($object,true), "820", "Can't remove unrelated element", true);
					
					// hacemos un getRelated mejorado (solo con los datos del objeto que necesitamos)
					if ($relaciones = $class_remota::getList(false, false, false, false, false, false, false, $related_sql))
					{
						foreach ($relaciones as $rel)
						{
							// agrega el elemento al array_manager
							if (!array_key_exists($rel->id(),$this->array_manager['remove'][$var]))
							{
								$this->array_manager['remove'][$var][$rel->id()] = $rel;
							}
						}
						
					}
					else
					{
						throw new OOB_exception("Can't remove unrelated element", "820", "Can't remove unrelated element", true);
					}
				}
			}
			else
			{
				throw new OOB_exception("Remove element of incorrect type ({$reg[2]}) not allowed", "812", "Can't remove that kind of object", true);
			}
		} 
		else
		{
			throw new OOB_exception("Can't remove, non-defined relation", "813", "Can't remove, non-defined relation", true);
		}
	}
	
	/** generates sort string based on filtered query */
	static private function __generateSortSQL($sort, $sort_mode, $table)
	{
		global $ari;
		
		// sort mode
		if (!in_array ($sort_mode, array(ASC,DESC)))
		{
			$sort_mode = ASC;
		}
		
		//sort validation
		if (in_array ($sort, static::getOrders()))
		{
			return ' ORDER BY ' . $table . '.' . $sort .' '. $sort_mode;
		}
		else
		{
			return ' ORDER BY ' . $table . '.id ' . $sort_mode;
		}
	}
	
	static public function getFilteredList($offset = false, $numrows = false, $sort = false, $sort_mode = false, $filters = array())
	{
		return static::__FilteredList($offset, $numrows, $sort, $sort_mode, $filters, false);
	}
	
	static public function getFilteredListCount($filters = array())
	{
		return static::__FilteredList(false, false, false, false, $filters, true);
	}
	
	
	/* SEARCHABLE STORAGE METHODS */
	static private function __FilteredList($offset = false, $numrows = false, $sort = false, $sort_mode = false, $filters = array(), $as_count = false)
	{
		global $ari;
		$table = static::getTable();
		$class = static::getClass();
		
		if (!is_int($offset))
		{
			$offset = 0;
		}
		
		if (!is_int($numrows) || $numrows < 1)
		{
			$numrows = 10000000;
		}
		
		//sort SQL string
		$sortby = static::__generateSortSQL($sort, $sort_mode, $table);
			
		$sql_array = $sql_union = array();
		$u = 0;
		$sql_array[$u] = array();
		$sql_union[$u] = array();
		
		
		
		//filters
		if (is_array($filters) && count($filters) > 0)
		{
			foreach ($filters as $f)
			{
							
				if (!isset($f['value']) || !isset($f['field']) || !isset($f['type'])) 
				{
					throw new OOB_exception("Filter is incomplete: " . var_export($f,true), "823", "Filter is incomplete", true);
					//return false; // oigame no hay caso de buscar si me da los datos mal!
				}
				
				if (!isset($f['comparison']))
				{
					$f['comparison'] = "eq";
				}
				
				if (!isset($f['connector']) || !in_array($f['connector'],array('AND','OR','XOR','UNION')))
				{
					$f['connector'] = 'AND';
				}
				
				// cada union es un array dentro del array de SQL a formar
				if ($f['connector'] == 'UNION' && count($sql_array[$u]) > 0)
				{
					$u++;
					$sql_array[$u] = array();
					$sql_union[$u] = array();
					$f['connector'] = 'AND';
				}
				
				// sacamos la parte que corresponde a esta consulta
				if (strpos($f['field'],'::'))
				{
					$array_field = explode('::',$f['field']);
					$f['field'] = array_shift($array_field);
					$remote_attribute = implode('::',$array_field);
				}
				else
				{
					$remote_attribute = false;
				}
				
				// es una variable del tipo relacion?
				$relation_kind = false;
				if (preg_match('#\((.*?)\)#', $f['field'], $match)) 
				{
					if (class_exists($match[1]))
					{
						$relation_kind = true;
						$remote_class = $match[1];
						$f['field'] =  substr($f['field'],0,strpos($f['field'],'('));
					}
					else
					{
						throw new OOB_exception("Relation class_name is invalid", "827", "Invalid filter parameter", true);
					}
				}
				
				
				// validamos la variable
				if (
				( isset(static::$public_properties[$f['field']]) || $f['field'] == 'status' ||  $f['field'] == 'id')
				&&
				substr ($f['field'],0,3) != 'id_'
				&&
				substr ($f['field'],0,6) != 'array_'
				
				)
				{
					$sql_array[$u][] = static::__SQLsearch($f['field'], $f['comparison'],$f['value'], $f['connector'], $f['type'],static::getTable());		
				} 
				elseif(isset(static::$public_properties['id_' . $f['field']])) // si es un object nos preparamos para ir de recurrencia
				{
					if (!$relation_kind)
					{
						$remote_class = substr(static::$public_properties['id_' . $f['field']],7,strlen(static::$public_properties['id_' . $f['field']]));
					}
					else
					{
//						$sql_data[] = ' AND class_' . $f['field'] . ' = ' . $ari->db->qMagic($remote_class);
					}
					
					if($remote_data = $remote_class::__SQLsearchRemote($remote_attribute, $f['comparison'],$f['value'], $f['connector'], $f['type'], $class, 'id_'.$f['field']))
					{
						$sql_array[$u] = array_merge ($sql_array[$u], $remote_data['data']);
						$sql_union[$u] = array_merge ($sql_union[$u], $remote_data['join']);
					}				
					
				}
				elseif(isset(static::$public_properties['array_' . $f['field']])) // si es un many-objects, nos preparamos para ir de viaje hasta el más alla!
				{
					$remote_class = substr(static::$public_properties['array_' . $f['field']],12,strlen(static::$public_properties['array_' . $f['field']]));
					
					if($remote_data = $remote_class::__SQLsearchRemote($remote_attribute, $f['comparison'],$f['value'], $f['connector'], $f['type'], $class, false))
					{
						$sql_array[$u] = array_merge ($sql_array[$u], $remote_data['data']);
						$sql_union[$u] = array_merge ($sql_union[$u], $remote_data['join']);
					}
				}
				elseif(method_exists(static::getClass(),$f['field'])) 
				{					
						$array_admitidos = array();
						// @fixme: this functionality needs to be optimized, as we are retrieving all items in the DB to filter on a one-by-one basis, 
						//         and calling n^m times the DB in the process.

						
						if( $static_list = static::getFilteredList() )
						{
							foreach( $static_list as $static )
							{
								
								switch ($f['type'])
								{
									case 'numeric':
									{
										$from = $f['value'];
										$to = $static->$f['field']();
										$method = false;
										break;
									}
									
									case 'date':
									{
										$from = strtotime($f['value']);
										$to = strtotime($static->$f['field']()->getDate());
										$method = false;
										break;					
									}
									
									case 'list':
									{
										$from = implode(',',strtotime($f['value']));
										$to = $static->$f['field']();
										$method = 'in_array';
										break;					
									}
									
									default:
									{
										$from = $f['value'];
										$to = $static->$f['field']();
										$method = 'strcasecmp';
										break;					
									}
								}
								
								if (!$method)
								{
									// @fixme: esto tiene que venir en un metodo ya esta por todos lados
									$operadores = array();
									$operadores["eq"] = "==";
									$operadores["lt"] = "<";
									$operadores["gt"] = ">";
									$operadores["eqgt"] = ">=";
									$operadores["ltgt"] = "<=";
									$operadores["neq"] = "!=";
									
									$comparison = $operadores[$f['comparison']];
									
									
									if (eval('if('.$from . $comparison . $to. '){return true;}else{return false;}'))
									{
										$array_admitidos[] = $static->id();
									}
								}
								elseif($method == 'in_array')
								{
									if (in_array($from, $to))
									{
										$array_admitidos[] = $static->id();
									}
								}
								else // if($method == 'strcasecmp')
								{
									if(strcasecmp( $from, $to ) >= 0 )
									{
										$array_admitidos[] = $static->id();
									}
								}
								
								
							}				
						}
				
						$idvalues = implode(",", $array_admitidos);
						$table = static::getTable();
												
						if( count($array_admitidos) > 0 )
						{
							$sql_data = " AND {$table}.id IN({$idvalues})";
							$sql_array[$u] = array_merge ($sql_array[$u], array($sql_data));
						}
				
				}
				else // no existe ese campo, que estamos tratando de buscar??
				{
					throw new OOB_exception("Trying to filter non existant attribute {$f['field']} - " . var_export($class,true) , "824", "Can't filter", true);
				}	
			}
		}
		
		if ($as_count)
		{
			$sql_begin = 'SELECT COUNT('. $table.'.id) as cuenta FROM '. $table . ' ';
		}
		else
		{
			$sql_begin = 'SELECT '. $table.'.* FROM '. $table . ' ';
		}
		
		$sql_final = '';
		
		// unimos el SQL y generamos la consulta
		foreach ($sql_array as $key => $items_array)
		{
			$sql_array[$key] = $sql_begin . implode ("\n",array_unique($sql_union[$key])).' WHERE 1 = 1 ' .implode("\n",$items_array);
		}
		
		if (count($sql_array) > 1)
		{
			$sql_final = '(' . implode(') UNION (', $sql_array) . ' ) GROUP BY ' . $table . '.id';
		}
		else
		{
			$sql_final = $sql_array[0];
		}
		
		$sql_final .= $sortby;
		
		// resultado
		$return = false;
		
		
			
		if ($as_count)
		{
			$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);		
			$rs = $ari->db->Execute($sql_final); 	
			$ari->db->SetFetchMode($savem);
				
			if ($rs && !$rs->EOF) 
			{ 
				$return = $rs->fields[0];
				$rs->Close();
			
			}
		
		}
		else
		{
		
			$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
			// #debug  
			//file_put_contents('oob.sql.txt',$sql_final."\n\r",FILE_APPEND);
			$rs = $ari->db->SelectLimit($sql_final, $numrows, $offset); 
			$ari->db->SetFetchMode($savem);
			
			$i = 0;
			
			if ($rs && !$rs->EOF) 
			{ 
				while (!$rs->EOF) 
				{	
					$return[$i] = new $class();
					$return[$i]->__fill($rs);
					
					$i++;
					$rs->MoveNext();
				}			
				$rs->Close();
			
			} 
		}
		
		return $return;
		
	}
	
	
	/* funcion que genera el string de SQL correspondiente al campo del objeto para el filtro dado 
	   esta funcion es privada, ya que solo puede ser llamada por el mismo objeto
	   no valida mucho ya que toda la validación la hace getFilteredList()
	*/
	static private function __SQLsearch($field,$comparison,$value,$connector,$type,$join_name)
	{
		global $ari;
		$table = static::getTable();
		
		$operadores = array();
		$operadores["eq"] = "=";
		$operadores["lt"] = "<";
		$operadores["gt"] = ">";
		$operadores["eqgt"] = ">=";
		$operadores["ltgt"] = "<=";
		$operadores["neq"] = "!=";
		
		$constraint = "";
		
		//le agregue esto para que funcione la informacion adicional de contactos
		if( $table == 'contactos_informacion_adicional_control_value' && $field != 'control' ){
				switch ($type)
				{
					case 'numeric':
						$constraint = 'isInt';	
					break;
					case 'date':
						$constraint = 'object-Date';
					break;					
				}
		}else{
		
			if (in_array($field,array('id','status')) && $type !='list')
			{
				$constraint = 'isInt';
			}
			elseif (in_array($field,array('id','status')) && $type =='list')
			{
				$constraint = 'list';
			}
			else
			{
				$constraint = static::$public_properties[$field];
			}			
		}	
		switch ($constraint)
		{
			case 'isNumeric':
			case 'isFloat':
			case 'isInt':
			{
				
				if (!is_numeric($value) && !is_float($value) && !OOB_validatetext::isInt($value))
				{
					return false;
				}
					
				$operador_inicio = $operadores[$comparison];			
				$operador_fin = "";	
				break;
			}
			case 'object-Date':
			{
				$value = $ari->db->qMagic (date( 'Y-m-d', strtotime( $value ) )); // @fixme : formato de fecha, revisar
				$operador_inicio = $operadores[$comparison];
				$operador_fin = "";	
				break;
			}
			case 'isArray':
			{
				return false; // no valido
				break;
			}
			
			case 'isBool':
			{
				if ($value == true)
				{$value = 1;}
				else
				{$value = 0;}
				
				$operador_inicio = " = ";
				$operador_fin = "";
				
				break;
			}
			
			case 'list':
			{
				// $value = $ari->db->qMagic($value)
				$operador_inicio = "IN ( ";
				$operador_fin = ") ";
				break;
			}
	
			default: // is string
			{
				$value = $ari->db->qMagic ('%' . $value . '%');
				$operador_inicio = " LIKE ";
				$operador_fin = "";
				break;
			}
			
		}
		
		return ' ' . $connector . ' ' . $join_name.'.'.$field . ' ' . $operador_inicio  . $value . $operador_fin;
	}

	static protected function __SQLsearchRemote($field, $comparison, $value, $connector, $type, $remote_class, $remote_attribute = false, $previous_join = false)
	{
		global $ari;
		$table = static::getTable();
		$class = static::getClass();
		$sql_data = $sql_join = array();
		
		// #debug 	file_put_contents('oob.sr.txt',var_export(array($field, $comparison, $value, $connector, $type, $remote_class, $remote_attribute, $previous_join),true),FILE_APPEND);
		
		if ($previous_join == false)
		{
			$remote_table = $remote_class::getTable();
		}
		else
		{
			$remote_table = $previous_join;
		}
		
		// sacamos la parte que corresponde a esta consulta
		if (strpos($field,'::'))
		{
			$array_field = explode('::',$field);
			$field = array_shift($array_field);
			$remote_attribute_bis = implode('::',$array_field);
		}
		else
		{
			$remote_attribute_bis = false;
		}
		
		
		$relation_kind = false;
		if (preg_match('#\((.*?)\)#', $field, $match)) 
		{
			if (class_exists($match[1]))
			{
				$relation_kind = true;
				$remote_class_bis = $match[1];
				$field =  substr($field,0,strpos($field,'('));
			}
			else
			{
				throw new OOB_exception("Relation class_name is invalid", "827", "Invalid filter parameter", true);
			}
		}
		
			
		// validamos la variable
		if (
					(isset(static::$public_properties[$field]))
					&&
					substr ($field,0,3) != 'id_'
					&&
					substr ($field,0,6) != 'array_'
			)
		{
			
			// esto quiere decir que el dato está en el  objeto de destino y no en el de origen.
			if (!$remote_attribute)
			{
				// tengo que buscar en la remoteclass, la propiedad en la que se almacena el id de mi objeto
				$acquired_attribute = false;
				$relation_class_available = false;
				foreach(static::$public_properties as $property => $constraint)
				{
					if ($constraint == 'object-' . $remote_class)
					{
						$acquired_attribute = $property;
					}
					
					if ($constraint == 'object-relation')
					{
						$relation_class_available =	 $property;
					}
					
				}
				
				if (!$acquired_attribute && !$relation_class_available)
				{			
					// puede ser que sea del tipo relation
					throw new OOB_exception("Trying to filter non existant relation:" . $field .' from: ' . static::getClass(). ' remote class:'. $remote_class , "835", "Can't filter relation", true);
				}
				elseif (!$acquired_attribute && $relation_class_available !== false)
				{
					$campo_id = $relation_class_available;
					$campo_clase = 'class_' . substr($relation_class_available,3,strlen($relation_class_available));
							
					// hacemos un LJ con la tabla remota, pero especificamos nuestro tipo de objeto en el campo del tipo-relacion remoto
					$join_name = $table. '_'.$campo_id;
					$sql_join[] = 'LEFT JOIN ' . $table . ' as '.$join_name.' ON (' . $join_name. '.'.$campo_id.' = ' .  $remote_table . '.id AND ' .  $join_name. '.' . $campo_clase . ' = ' .$ari->db->qMagic($remote_class) . ')';
					
				}
				else
				{
					$join_name = $table .'_' . $acquired_attribute;
					$sql_join[] = 'LEFT JOIN ' . $table . ' as '. $join_name  .' ON (' . $remote_table. '.id = ' .  $join_name . '.' . $acquired_attribute . ')'; 
				}
			}
			else
			{
				$join_name = $remote_table . '_' . $remote_attribute;
				$sql_join[] = ' JOIN ' . $table . ' as '. $join_name  .' ON (' . $join_name. '.id = ' .  $remote_table . '.' . $remote_attribute . ')'; // table
			}
			
			$sql_data[] = static::__SQLsearch($field, $comparison, $value, $connector, $type,$join_name);
		} 
		elseif(isset(static::$public_properties['id_' . $field])) // si es un object nos preparamos para ir de recurrencia
		{
			if (!$remote_attribute)
			{
				// necesito saber en que propiedad mia se guarda el objeto de la otra
				$acquired_attribute = false;
				$relation_class_available = false;
				foreach(static::$public_properties as $property => $constraint)
				{
					if ($constraint == 'object-' . $remote_class)
					{
						$acquired_attribute = $property;
					}
					
					if ($constraint == 'object-relation')
					{
						$relation_class_available =	 $property;
					}
				
				}
				
				if (!$acquired_attribute && !$relation_class_available)
				{			
					throw new OOB_exception("Trying to filter non existant relation:" . 'id_' . $field .' from: ' . static::getClass(). ' remote class:'. $remote_class , "828", "Can't filter relation", true);
				}
				elseif ($relation_class_available != false && $acquired_attribute == false)
				{
					// add una relacion en el sql con la remote_class
					
					$campo_id = $property;
					$campo_clase = 'class_' . substr($property,3,strlen($property));
							
					// hacemos un LJ con la tabla remota, pero especificamos nuestro tipo de objeto en el campo del tipo-relacion remoto
					$join_name = $table. '_'.$campo_id;
					$sql_join[] = ' LEFT JOIN ' . $table . ' as '.$join_name.' ON (' . $join_name. '.'.$campo_id.' = ' .  $remote_table . '.id AND ' .  $join_name. '.' . $campo_clase . ' = ' .$ari->db->qMagic($remote_class) . ')';
					
				} 
				else
				{
					$join_name = $remote_table. '_'. $acquired_attribute;
					$sql_join[] = 'LEFT JOIN ' . $table . ' as '.$join_name.' ON (' . $join_name . '.' . $acquired_attribute. ' = '.$remote_table. '.id )';
				}
				
			}
			else
			{
				$join_name = $table . '_' . $remote_table . '_' . $remote_attribute;
				$sql_join[] = 'JOIN ' . $table . ' as '.$join_name.' ON (' .  $join_name . '.id = ' . $remote_table. '.'.$remote_attribute. ')';
			}
			
			
			if (!$relation_kind)
			{
				$remote_class_bis = substr(static::$public_properties['id_' . $field],7,strlen(static::$public_properties['id_' . $field]));
			}
			else
			{
				$sql_data[] = ' AND '.$join_name.'.class_' . $field . ' = ' . $ari->db->qMagic($remote_class_bis);
			}
			
			$remote_data = $remote_class_bis::__SQLsearchRemote($remote_attribute_bis, $comparison,$value, $connector, $type, $class, 'id_' . $field,$join_name);
			
			$sql_join = array_merge($sql_join, $remote_data['join']);
			$sql_data = array_merge($sql_data, $remote_data['data']);
		
		}
		elseif(isset(static::$public_properties['array_' . $field])) // si es un many-objects, nos preparamos para ir de viaje hasta el más alla!
		{
			$remote_class_bis = substr(static::$public_properties['array_' . $field],12,strlen(static::$public_properties['array_' . $field]));
			
			// tenemos 2 opciones, que el objeto remoto, tenga realmente nuestro ID, o que sea una relacion, y necesitemos buscarlo.
			
			// si tiene nuestro ID, la cuestion es sencilla.. miramos en las propiedades del objeto remoto, cual tiene nuestro objeto.
			$acquired_attribute = false;
			foreach($remote_class_bis::$public_properties as $property => $constraint)
			{
				if ($constraint == 'object-' . $class)
				{
					$acquired_attribute = $property;
				}
			}
			
			if (array_key_exists($remote_attribute,$remote_class_bis::$public_properties))  
			{
				$join_name = $table . '_' . $remote_attribute;
				$sql_join[] = 'LEFT JOIN ' . $table . ' as '.$join_name.' ON (' . $remote_table. '.'.$remote_attribute.' = ' .  $join_name . '.id)';  
			}
			elseif ($campo_id = array_search('object-relation',$remote_class_bis::$public_properties)) // si no tiene nuestro objeto, la posibilidad es que tengamos un objeto relacion
			{	
				// el objeto de destino tiene dos campos, uno para el valor, y el otro para el nombre de la clase
				$campo_clase = 'class_' . substr($campo_id,3,strlen($campo_id));
				
				// hacemos un LJ con la tabla remota, pero especificamos nuestro tipo de objeto en el campo del tipo-relacion remoto
				$join_name = $table . '_' . $campo_id;
				$sql_join[] = 'LEFT JOIN ' . $table . ' as '.$join_name.' ON (' . $remote_table. '.'.$campo_id.' = ' .  $join_name . '.id AND ' . $remote_table. '.' . $campo_clase . ' = ' . $ari->db->qMagic($class) . ')';
			}
			elseif ($acquired_attribute != false)
			{
				//@optimize: this case is left here for backwards compatibility, it should never be used, as is based on magic.
				// hacemos un LJ de esa tabla con la nuestra, y un sqlsearchremote.
				$join_name = $table . '_id';
				$sql_join[] = 'LEFT JOIN ' . $table . ' as '.$join_name.' ON (' . $remote_table. '.'.$acquired_attribute.' = ' .  $join_name . '.id)';  
				
			}
			else // si no se da ninguna de estas condiciones, no hay una relación válida, asi que cortamos.
			{
				throw new OOB_exception("Trying to filter non existant remote relation: " . $field . ' - ' . var_export($class,true), "830", "Can't filter remote", true);
			}	
					
			
			$remote_data = $remote_class_bis::__SQLsearchRemote($remote_attribute_bis, $comparison,$value, $connector, $type, $class, false,$join_name);
		
			$sql_join = array_merge($sql_join, $remote_data['join']);
			$sql_data = array_merge($sql_data, $remote_data['data']);
			
		}
		elseif(($field == false  || $field == 'status' ||  $field == 'id') && $type == 'list') // viene un listado
		{
			if ($field == false) 
			{
				$field = 'id';
			}
			
			$join_name = $table. '_id';
			$sql_join[] = 'JOIN ' . $table . ' as '.$join_name.' ON (' . $join_name. '.id = ' .  $remote_table . '.' . $remote_attribute . ')';
			$sql_data[] = ' ' . $connector . ' ' . $join_name.'.'.$field . ' IN ('  . $value . ')';
		}
		elseif( ($field == 'id' || $field == false) && $type == 'numeric') // estamos intentando filtrar un ID dede otro lado
		{
			if ($field == false) 
			{
				$field = 'id';
			}
			
			$join_name = $table. '_id';
			$sql_join[] = 'LEFT JOIN ' . $table . ' as '.$join_name.' ON (' . $join_name. '.id = ' .  $remote_table . '.' . $remote_attribute . ')';
			$sql_data[] = static::__SQLsearch($field, $comparison, $value, $connector, $type,$join_name);
			
			
			//$sql_data[] = ' ' . $connector . ' ' . $join_name.'.'.$field . ' IN ('  . $value . ')';
		}	
		elseif(method_exists(static::getClass(),$field)) 
		{		

		
				$array_admitidos = array();
				// @fixme: this functionality needs to be optimized, as we are retrieving all items in the DB to filter on a one-by-one basis, 
				//         and calling n^m times the DB in the process.
				if( $static_list = static::getFilteredList() )
				{
					foreach( $static_list as $static )
					{
						
						switch ($f['type'])
						{
							case 'numeric':
							{
								$from = $value;
								$to = $static->$field();
								$method = false;
								break;
							}
							
							case 'date':
							{
								$from = strtotime($value);
								$to = strtotime($static->$field()->getDate());
								$method = false;
								break;					
							}
							
							case 'list':
							{
								$from = implode(',',strtotime($value));
								$to = $static->$field();
								$method = 'in_array';
								break;					
							}
							
							default:
							{
								$from = $value;
								$to = $static->$field();
								$method = 'strcasecmp';
								break;					
							}
						}
						
						if (!$method)
						{
							// @fixme: esto tiene que venir en un metodo ya esta por todos lados
							$operadores = array();
							$operadores["eq"] = "==";
							$operadores["lt"] = "<";
							$operadores["gt"] = ">";
							$operadores["eqgt"] = ">=";
							$operadores["ltgt"] = "<=";
							$operadores["neq"] = "!=";
							
							$comparison = $operadores[$comparison];
							
							
							if (eval('if('.$from . $comparison . $to. '){return true;}else{return false;}'))
							{
								$array_admitidos[] = $static->id();
							}
						}
						elseif($method == 'in_array')
						{
							if (in_array($from, $to))
							{
								$array_admitidos[] = $static->id();
							}
						}
						else // if($method == 'strcasecmp')
						{
							if(strcasecmp( $from, $to ) >= 0 )
							{
								$array_admitidos[] = $static->id();
							}
						}
						
						
					}				
				}
		
				$idvalues = implode(",", $array_admitidos);
				$table = static::getTable();
										
				if( count($array_admitidos) > 0 )
				{
					$join_name = $table. '_' . $field;
					$sql_data[] = " AND {$join_name}.id IN({$idvalues})";
					$sql_join[] = 'LEFT JOIN ' . $table . ' as '.$join_name.' ON (' . $join_name. '.id = ' .  $remote_table . '.' . $remote_attribute . ')';
				}
				
		}
		else // no existe ese campo, que estamos tratando de buscar??
		{
			throw new OOB_exception("Trying to filter non existant remote attribute: " . $field . ' - clase: ' . var_export($class,true), "825", "Can't filter remote", true);
		}
		
		return array(
					'data' => $sql_data,
					'join' => $sql_join
					);
		
	}
	
	public function max($attribute)
	{
		return static::__sql_operation($attribute, 'MAX');
	}

	public function min($attribute)
	{
		return static::__sql_operation($attribute, 'MIN');
	}

	public function avg($attribute)
	{
		return static::__sql_operation($attribute, 'AVG');
	}

	protected function __sql_operation($attribute, $operation)
	{
		global $ari;
		
		$operations = array ('AVG','MIN','MAX','VAR_POP','VAR_SAMP','STDDEV_POP','STDDEV_SAMP');
		
		if (!in_array($operation,$operations))
		{
			throw new OOB_exception("SQL Operation not allowed", "899", "Operation {$operation} not allowed", true);
		}
		
		if (!isset(static::$public_properties[$attribute]))
		{
			throw new OOB_exception("Field {$attribute} non existant in object", "899", "Field {$attribute} non existant in object", true);
		}
		
		$table = static::getTable();
		
		$return = false;
			
		// get from DB
		$sql = "SELECT $operation($attribute) as count 
				FROM $table ";
			
		
		$savem = $ari->db->SetFetchMode(ADODB_FETCH_NUM);
		
		$rs = $ari->db->Execute($sql); 
				
		$ari->db->SetFetchMode($savem);
				
		if ($rs && !$rs->EOF) 
		{ 
			$return = $rs->fields[0];
			$rs->Close();
		} 

		return $return;	
	}
	
}

?>
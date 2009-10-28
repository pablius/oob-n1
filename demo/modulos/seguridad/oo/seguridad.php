<?php
/**
########################################
#OOB/N1 Framework [©2004,2006]
#
#  @copyright Pablo Micolini
#  @license BSD
######################################## 
*/
 
// require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'seguridad_action.php');
// require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'seguridad_group.php');
// require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'seguridad_permission.php');
// require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'seguridad_role.php');
 
 class seguridad {
 
 private function __construct ()
 {}
 
 /** Retorna true si el usuario pasado como parametro tiene permiso para la accion
  *  pasada como parametro en la perspectiva pasada como parametro
  * */
 static public function isAllowed ( $action, $perspective = NO_OBJECT, $user = NO_OBJECT ) 
 {
 	global $ari;
		
	if (!is_a($action, 'seguridad_action'))
 	{	$action = new seguridad_action($action); 	}
 	 	
 	if (!is_a($perspective, 'OOB_perspective'))
 	{	$perspective = $ari->get('perspective');    }
 	
 	//obtengo el modulo de la accion
 	if ($module = $action->myModule())
 	{	
 		/* verfico que la perspectiva tenga el módulo o estemos en el administrador */
 		if ($perspective->isMember ($module) || $ari->get("mode") == "admin")
 		{// la perspectiva contiene el modulo
 		 	
 		 	/* verifico que el usuario tenga permiso para esa accion */
 		 	if (!is_a($user, "oob_user") )
 		 	{	$user = $ari->get("user");	}
 		 	
 			//@verificar cuando my roles devuelve false
 			if (seguridad_action :: exists ($action, seguridad_role :: myRoles($user) ) )
 			{
 				//tiene permiso
 				$return= true;
 			}
 			else
 			{
 				//no tiene permiso
 				
 				$return = false;
 			}
 		}
 		else
 		{
 			 // error el modulo no pertence a la perspectiva
 			$return = false;
 		}
 	
 	}
 	else
 	{
 		// error la accion no pertenece a ningun modulo
 		$return = false;
 	}
 		 	
 	return $return;
 }//end function
 
  /** Este metodo recibe como parametros un array de acciones o una accion individual,
   *  de las cuales	buscara los roles permitidos y no permitidos,
   *  los grupos permitidos y no permitidos, y los usarios permitidos, segun se pase
   *  en el array o variable security_type las constantes ROLE, USER, GROUP
   *  El metodo retorna un array con el id de tipo (rol, usuario, grupo ), el id de la 
   *  accion permitida, y el nombre de la clase del tipo */
 static public function objectsAllowed ($action, $object_action = false, $security_type)
 {
 	/*
	global $ari;
	//si no paso la el objeto devuelvo false
	if (!$object_action )
 	{	return false; 	}
 	
	//busco la clase del objeto pasado como parametro
	$clase_object = $ari->db->qMagic(get_class($object_action));
	$object_id = $object_action->id();
	
	//me fijo si el parametro $action es una array o no
	if (!is_array($action))
	{	//no es array lo transformo en uno
		$action = array($action);
	}
	
	//ya tengo un array de acciones por lo tanto tengo q consultar para todas
	//las acciones del array, formo una clausula IN
	$clausula_action = "";
	foreach($action as $a)
	{	//verifico q sea un objeto para ver si existe
		if (!is_a($a, 'seguridad_action'))
		{	$a = new seguridad_action($a); 	}
	 	//Agrego los id a la clausula IN
	 	$clausula_action .=  $a->get('action') . ","; 	
	}
	
	//depuro y termino de formar la cadena
	$clausula_action = substr($clausula_action,0,strlen($clausula_action)-1);
	$clausula_action = " AND (o.ActionID IN ($clausula_action) )";

	//verifico si el parametro $security_type es una array o no
	if (!is_array($security_type))
	{// no es un array lo transformo en uno
		$security_type = array($security_type); 
	} 	
	
	//formo las consultas para cada type del array [ROLE|GROUP|USER]
	$array_query = array("","","");
	
	foreach($security_type as $type)
	{
		//verifico q cada tipo pertenezca al conjunto [ROLE|GROUP|USER]
		// y formo su consulta
	 	switch($type)
		{
			case USER:
			{
				//consulto solo los usuarios con permiso para las acciones
				//incluidas en la clausula_action
				$array_query[0] = "(SELECT U.ID, O.ActionID, 'OOB_User' AS Clase 
								    FROM OOB_User_User U, Security_Object O 
								    WHERE O.ObjectSecurityID = U.ID
								    AND O.ObjectSecurityClass = 'oob_user' 
								    AND O.ObjectActionClass = $clase_object 
								    AND O.ObjectActionID = $object_id
								    AND U.Status <> " . DELETED ." 
								    $clausula_action ORDER BY 1) ";
				break;
			}
			case GROUP:
			{
				//consulto tanto los grupos con permiso para las acciones
				//incluidas en la clausula_action, como los grupos sin permiso
				$array_query[1] = "(SELECT G.id, O.ActionID, 'Seguridad_Group' AS Clase 
									FROM Security_Group G LEFT JOIN Security_Object O 
										 ON (G.ID = O.ObjectSecurityID)
										 AND (O.ObjectSecurityClass = 'seguridad_group' ) 
										 AND  O.ObjectActionClass = $clase_object 
										 AND O.ObjectActionID = $object_id 
										 $clausula_action
									WHERE G.Status <> " . DELETED . " ORDER BY 1)";
				break;
			}
			case ROLE:
			{
				//consulto tanto los roles con permiso para las acciones
				//incluidas en la clausula_action, como los roles sin permiso
				$array_query[2] = "(SELECT R.id, O.ActionID, 'Seguridad_Role' AS Clase 
									FROM Security_Role R LEFT JOIN Security_Object O 
										 ON (R.ID = O.ObjectSecurityID)
										 AND (O.ObjectSecurityClass = 'seguridad_role' ) 
										 AND  O.ObjectActionClass = $clase_object 
										 AND O.ObjectActionID = $object_id
										 $clausula_action
									WHERE R.Status <> " . DELETED . " ORDER BY 1)";
				break;
			}		
		}//end switch
 			
	}//end foreach
	
	//uno las consultas
	$flagFirst = true;
	$sql = "";
	foreach($array_query as $query)
	{
		if ($query != "")
		{
			if (!$flagFirst)
			{	$sql.= " UNION ";	}
			$flagFirst = false;
			$sql .= $query;
		}
	}

	$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
	
		//		$rs = $ari->db->Execute( $sql);
			$rs = $ari->db->CacheExecute(9000,$sql);

	$ari->db->SetFetchMode($savem);
	if ($rs && !$rs->EOF) 
	{ 
		while (!$rs->EOF) 
		{
			$return[strtolower($rs->fields[2])][$rs->fields[0]][$rs->fields[1]] = true;					
			$rs->MoveNext();
		}			
			$rs->Close();
	} 
	else
	{return false;}
	return $return;
	*/
 }
 //end function


  /**		*/
 static public function objectsNoAllowed ($action, $object_action = false, $security_type, $search="")
 {
 	/*
	global $ari;
	
	//si no paso la el objeto devuelvo false
	if (!$object_action )
 	{	return false; 	}
 	
	//busco la clase del objeto pasado como parametro
	$clase_object = $ari->db->qMagic(get_class($object_action));
	
	//me fijo si el parametro $action es una array o no
	if (!is_array($action))
	{	//no es array lo transformo en uno
		$action = array($action);
	}
			
	//ya tengo un array de acciones por lo tanto tengo q consultar para todas
	//las acciones del array, formo una clausula IN
	$clausula_action = "";
	foreach($action as $a)
	{	//verifico q sea un objeto para ver si existe
		if (!is_a($a, 'seguridad_action'))
		{	$a = new seguridad_action($a); 	}
	 	//Agrego los id a la clausula IN
	 	$clausula_action .=  $a->get('action') . ","; 	
	}
	//depuro y termino de formar la cadena
	$clausula_action = substr($clausula_action,0,strlen($clausula_action)-1);
	$clausula_action = " ActionID IN ($clausula_action) ";	

	if ($security_type != ROLE && $security_type != USER && $security_type != GROUP)
 	{	return false; 	}
 	
 	switch($security_type)
	{
			case USER:
			{
				$class_security = "oob_user";
				$name = "uname";
				$table_security = "OOB_User_User";
				break;
			}
			case GROUP:
			{
				$class_security = "seguridad_group";
				$name = "name";
				$table_security = "Security_Group";
				break;
			}
			case ROLE:
			{
				$class_security = "seguridad_role";
				$name = "name";
				$table_security = "Security_Role";
				break;
			}				
	}
 	
 	$id_object_action = $object_action->id();
 	$clase_object = $ari->db->qMagic(get_class($object_action));
 	
 	$clausula = " AND ObjectSecurityClass = '$class_security' ";
	
	$sql = "SELECT ID FROM $table_security  
			WHERE Status <> " . DELETED . "  
			AND $name LIKE '%$search%' 
			AND ID NOT IN 	
	        (SELECT ObjectSecurityID  
		     FROM security_object  
		     WHERE $clausula_action
		     AND ObjectActionID = $id_object_action 
		     AND ObjectActionClass = $clase_object
		     $clausula)"; 

	$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
	
	$rs = $ari->db->Execute($sql);

	$ari->db->SetFetchMode($savem);
		if ($rs && !$rs->EOF) { // aca cambie sin probar, hay q ver si anda!
			while (!$rs->EOF) {
					$return[] = new $class_security ($rs->fields[0]);
					$rs->MoveNext();
					}			
			$rs->Close();
			} else
			{return false;}
		return $return;
		*/
 }
 //end function 
 

	
  /** Añade o borra permisos de una accion de algun objeto.   	
   *  1º parametro: Se debe pasar un array donde cada elemento del mismo
   *  tiene el objeto accion que se desea dar permiso,  
   *  y el objeto al cual se le permite la accion.
   *  El 2º parametro es el objeto el cual concede el permiso
   *  El 3º parametro se especifica si no se omitieron todos los permisos, por lo caul
   *  se debe realziar los borrados de los permisos q quedaron en la BD, el parametro es un
   *  array de 21 elementos, donde el primero es la accion (q no se permitira mas) y el segundo
   *  es la clase a la cual no se le permitira la accion
   * */	  
  static public function refreshObjectPermission ( $permissions = false, $action_object, $delete_permissions = false)
 {
 	global $ari;
 	
 	//valido q el 1º parametro sea un array
 	if (!is_array($permissions))
 	{	return false; 	}
 	
 	//valido q el 2º parametro (o sea el objeto q permite la accion) sea un objeto
 	if (!is_object($action_object))
 	{	return false; 	}
 	
 	//saco la clase y el id del objeto q permite la accion, ya q es comun 
	$action_object_id = $ari->db->qMagic( $action_object->id() );
 	$action_object_class = $ari->db->qMagic( get_class($action_object) );
 					
	//algunas variables q usare
 	$query_delete = array();
 	$i = 0;
 	$array_store = array();
 	
 	//recorro el aray permission y reacomo los elementos de forma q me quede un array donde
 	//cada elemento queda con la forma
 	//$array_store[id de accion][clase del objeto q le cedo el permiso][ indice incremental ] = id del objeto al q le cedo el permiso
 	//esto me permite agrupar los elementos a los cuales le concedo una accion y pertenecen a la misma clase,
 	//esto me permite reducir la cantidad de consultas q deberia hacer
  	foreach($permissions as $p)
 	{	
  		if ( isset($p['action']) || 
 		     isset($p['allowed_object']) )
 		{
  			if ( is_a($p['action'], "seguridad_action")  && 
 			     is_object($p['allowed_object']) )
 			{
 				$array_store[$p['action']->get('action')][get_class($p['allowed_object'])][$i] = $p['allowed_object']->id();
 				$i++; 
 			}//end if
 		}//end if	
 	}//end if
 	
 	//voy a formar el array de consultas q ejecutare en la transaccion
 	$array_query = array();
 	//voy recorriendo el array por id de accion (o sea la primer clave del array $array store)
 	foreach( array_keys($array_store) as $a)
 	{
 		//ahora para cada accion recorro los elmentos a los q cedo el permiso y son de la misma clase
 		//o sea por la segunda key del array 
 		foreach ( array_keys($array_store[$a]) as $c) 
 		{
 			//formo un string con los id de los elementos nuevos a los q le cedere el permiso
 			//para usarlo en una clausula in
 			$flagFirst = true;
 			foreach($array_store[$a][$c] as $i)
 			{
 				if ($flagFirst)
 				{	
 					$array_store[$a][$c]['in'] = $ari->db->qMagic($i);	
 					$flagFirst = false;		
 				}
 				else
 				{	$array_store[$a][$c]['in'] .= "," . $ari->db->qMagic($i);	}
 			}//end foreach
 			
	 		//armo la consulta de eliminacion, es decir elimino
	 		//todos los elementos permitdos q no esten entre los nuevos permisos
	 		$aQmagic = $ari->db->qMagic($a);
	 		$cQmagic  = $ari->db->qMagic($c);
 			$array_query[]= "DELETE FROM Security_ObjectPermission
				   			 WHERE ActionID = $aQmagic
		   		   			 AND ActionObjectID  = $action_object_id  
		  	   				 AND ActionObjectClass = $action_object_class   
		       				 AND AllowedObjectClass = $cQmagic
		       				 AND AllowedObjectID NOT IN (". $array_store[$a][$c]['in'] . ");";

	 		//armo la consulta de insercion multiple filas, inserto los nuevos
	 		//elementos q ya no esten registrados 
 			$array_query[]= "INSERT INTO `Security_ObjectPermission` 
							 (`ActionID`, `ActionObjectID`, `ActionObjectClass`, `AllowedObjectID`, `AllowedObjectClass`)
							 SELECT $aQmagic, $action_object_id, $action_object_class, ID, $cQmagic 
							 FROM ". seguridad :: getTableName($c) . " WHERE ID IN (" . $array_store[$a][$c]['in'] . " )
						 	 AND ID NOT IN 
			     			 (SELECT AllowedObjectID
		   	  				 FROM Security_ObjectPermission 
		   	   				 WHERE ActionID = $aQmagic  
		       				 AND ActionObjectID  = $action_object_id  
			       			 AND ActionObjectClass = $action_object_class   
		         			 AND AllowedObjectClass = $cQmagic);";	
	       				  			
 		} //end foreach
 		
 	}//end foreach
 	
 	//realizo la transaccion
 	if (is_array($array_query))
 	{
	 	// comienzo la transaccion
		$ari->db->StartTrans();
		
		//borro los items que no se seleccionaron, vienen en el 3º parametro
		if (is_array($delete_permissions))
		{
			foreach($delete_permissions as $d)
			{
				
				if ( isset($d['allowed_class']) && isset($d['action']) )
				{
					if (trim($d['allowed_class']) != "" &&
				    	is_a($d['action'], "seguridad_action") )
					{
						$class = $ari->db->qMagic($d['allowed_class']);
						$action_id = $ari->db->qMagic($d['action']->get('action'));
						$array_query[] = "DELETE FROM Security_ObjectPermission
			   							  WHERE ActionID = $action_id  
		   	   							  AND ActionObjectID  = $action_object_id  
		  	   							  AND ActionObjectClass = $action_object_class   
		       							  AND AllowedObjectClass= $class ";
					}//end if
				}//end if
			}//end foreach
		}//end if
		//ejecuto las demas acciones		
	 	foreach($array_query as $sql)
	 	{ 	
	 		$ari->db->Execute($sql);
	 	}//end foreach
		
		if (!$ari->db->CompleteTrans())
		{
			throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false); //return false;
		}
		else
		{
			return true;
		}//end if
 	}//end if
 	return false;

 }// end function
 
 /**	*/
 public function getTableName($class)
 {
 	switch(trim(strtolower($class)))
 	{
 		case "seguridad_role":
 		{
 			return "security_role";	
 		}
 		case "oob_user":
 		{
 			return "oob_user_user";	

 		} 		
 		case "seguridad_group":
 		{
 			return "security_group";	

 		} 		 		
 	} 	
 }

  /** Devuelve true si algunos de los objetos del array $allowed_objects
   *  (los cuales deben ser de la misma clase) 
   *  tiene permitida la accion $action para el objeto $action_object
   */
 static public function getPermissionForAnyObject ($action, $allowed_objects = NO_OBJECT, $action_object = NO_OBJECT )
 {
 	global $ari;
	
	$flagError = false;
	
	if ( !is_a($action, 'seguridad_action') )
 	{	
 		$ari->error->addError ("seguridad", "INVALID_ACTION");
 		$flagError = true;	
 	}
 	
 	if ( !is_object($action_object) )
 	{	
 		$ari->error->addError ("seguridad", "INVALID_ACTION_OBJECT");
 		$flagError = true;	
 	}
 	
 	$array = array();
 	//si $allowed_objects no es array lo transformo en uno
 	if (! is_array($allowed_objects) )
 	{	$array[0] = $allowed_objects; 	}
 	else
 	{	$array = $allowed_objects;	}
 	
 	//trato de formar la clausual in
 	$in = "";
 	$flagFirst = true;
 	$class = "";
 	foreach ( $array as $item )
 	{
 		if ( is_object($item) )
 		{
 			if($flagFirst)
 			{
 				//es el primer elemento guardo su clase para validar q los otros objetos
 				//sean de la misma clas
 				$class = get_class($item);
 				$in = $ari->db->qMagic($item->id());
 				$flagFirst = false;
 			}
 			else
 			{
 				//valido q el objeto sea de la misma clase q el primer objeto
 				if ( is_a($item, $class) )
 				{
 						$in .= "," . $ari->db->qMagic($item->id());  
 				}//end if
 						
 			}//end if 			
 		}//end if
 	}//end foreach
 	
	if ( trim($in) == "" )
 	{	
 		$ari->error->addError ("seguridad", "INVALID_ALLOWED_OBJECT");
 		$flagError = true;	
 	}
 	
 	//
 	if ($flagError)
 	{
 		return false;
 	}
 	else
 	{
 		$action_id = $ari->db->qMagic($action->get('action'));
 		$action_object_id = $ari->db->qMagic( $action_object->id() );
 		$action_object_class = $ari->db->qMagic( get_class($action_object) );
		$class = $ari->db->qMagic($class);
		
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);	
		
		$sql= "SELECT TRUE
		   	   FROM Security_ObjectPermission 
		   	   WHERE ActionID = $action_id  
		       AND ActionObjectID  = $action_object_id  
		       AND ActionObjectClass = $action_object_class   
		       AND AllowedObjectID IN ($in)
		       AND AllowedObjectClass= $class";	
		
		$rs = $ari->db->Execute($sql);
	
		$ari->db->SetFetchMode($savem);
		if (!$rs || $rs->EOF) 
		{	
			$rs->Close();
			return false;	
		}
		else
		{
			$rs->Close();
			return true;
		}	       
		       
 	}//end if
  	
 	
 }//end function
 
 
 
  /** Devuelve true si el objeto $allowed_object tiene permitida la 
   *  accion $action para el objeto $action_object
   */
 static public function getObjectPermission ($action, $allowed_object = NO_OBJECT, $action_object = NO_OBJECT )
 {
	global $ari;
	
	$flagError = false;
	
	if ( !is_a($action, 'seguridad_action') )
 	{	
 		$ari->error->addError ("seguridad", "INVALID_ACTION");
 		$flagError = true;	
 	}
 	
	if ( !is_object($allowed_object) )
 	{	
 		$ari->error->addError ("seguridad", "INVALID_ALLOWED_OBJECT");
 		$flagError = true;	
 	} 	

	if ( !is_object($action_object) )
 	{	
 		$ari->error->addError ("seguridad", "INVALID_ACTION_OBJECT");
 		$flagError = true;	
 	}
 	
 	if ($flagError)
 	{
 		return false;
 	}
 	else
 	{
 		$action_id = $ari->db->qMagic($action->get('action'));
 		$action_object_id = $ari->db->qMagic( $action_object->id() );
 		$action_object_class = $ari->db->qMagic( get_class($action_object) );
 		$allowed_object_id = $ari->db->qMagic( $allowed_object->id() );
		$allowed_object_class = $ari->db->qMagic(get_class($allowed_object));
		
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);	
		
		$sql= "SELECT TRUE
		   	   FROM Security_ObjectPermission 
		   	   WHERE ActionID = $action_id  
		       AND ActionObjectID  = $action_object_id  
		       AND ActionObjectClass = $action_object_class   
		       AND AllowedObjectID = $allowed_object_id
		       AND AllowedObjectClass= $allowed_object_class";	
		
		$rs = $ari->db->Execute($sql);
	
		$ari->db->SetFetchMode($savem);
		if (!$rs || $rs->EOF) 
		{	
			$rs->Close();
			return false;	
		}
		else
		{
			$rs->Close();
			return true;
		}	       
		       
 	}//end if
 			 
 }//end function
 
   /** Devuelve un array con los objetos q tiene permiso a accion $action 
    * sobre el objeto $allowed_object y que sena instancias de la 
   *  clase $allowed_class
   */
 static public function getAllowedObjectForClass ($action, $action_object = NO_OBJECT, $allowed_class = '' )
 {
	global $ari;
	
	$flagError = false;
	
	if ( !is_a($action, 'seguridad_action') )
 	{	
 		$ari->error->addError ("seguridad", "INVALID_ACTION");
 		$flagError = true;	
 	}
 	
	if ( !is_object($action_object) )
 	{	
 		$ari->error->addError ("seguridad", "INVALID_ACTION_OBJECT");
 		$flagError = true;	
 	}
	
	$allowed_class = trim ($allowed_class);
	if ( $allowed_class == "" )
 	{	
 		$ari->error->addError ("seguridad", "INVALID_ALLOWED_CLASS");
 		$flagError = true;	
 	} 	
 	
 	if ($flagError)
 	{
 		return false;
 	}
 	else
 	{
 		$action_id = $ari->db->qMagic($action->get('action'));
 		$action_object_id = $ari->db->qMagic( $action_object->id() );
 		$action_object_class = $ari->db->qMagic( get_class($action_object) );
		$allowed_class = $ari->db->qMagic($allowed_class);
		
		$sql= "SELECT `Security_ObjectPermission`.`AllowedObjectID` AS `ID`, 
					   `Security_ObjectPermission`.`AllowedObjectClass` AS `Class`
			   FROM Security_ObjectPermission
			   WHERE ActionID = $action_id  
		   	   AND ActionObjectID  = $action_object_id  
		  	   AND ActionObjectClass = $action_object_class   
		       AND AllowedObjectClass= $allowed_class	";
		       
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		
		$rs = $ari->db->Execute( $sql);
		$ari->db->SetFetchMode($savem);
		
		
		if ($rs && !$rs->EOF) 
		{
			$return = array();	
			$i = 0; 
			while (!$rs->EOF) 
			{
				$return[$i] = new $rs->fields['Class'] ($rs->fields['ID']);
				$i++;					
				$rs->MoveNext();
			}			
			$rs->Close();
			return $return;
		} 
		else
		{return false;}
	
 	}//end if
 			 
 }//end function
 
   /** Remueve los permisos de la accion $action sobre el objeto $allowed_object para la 
   *  clase $allowed_class
   */
 static public function removeObjectPermissionForClass ($action, $action_object = NO_OBJECT, $allowed_class = '' )
 {
	global $ari;
	
	$flagError = false;
	
	if ( !is_a($action, 'seguridad_action') )
 	{	
 		$ari->error->addError ("seguridad", "INVALID_ACTION");
 		$flagError = true;	
 	}
 	
	if ( !is_object($action_object) )
 	{	
 		$ari->error->addError ("seguridad", "INVALID_ACTION_OBJECT");
 		$flagError = true;	
 	}
	
	$allowed_class = trim ($allowed_class);
	if ( $allowed_class == "" )
 	{	
 		$ari->error->addError ("seguridad", "INVALID_ALLOWED_CLASS");
 		$flagError = true;	
 	} 	
 	
 	if ($flagError)
 	{
 		return false;
 	}
 	else
 	{
 		$action_id = $ari->db->qMagic($action->get('action'));
 		$action_object_id = $ari->db->qMagic( $action_object->id() );
 		$action_object_class = $ari->db->qMagic( get_class($action_object) );
		$allowed_class = $ari->db->qMagic($allowed_class);
		
		$sql= "DELETE FROM Security_ObjectPermission
			   WHERE ActionID = $action_id  
		   	   AND ActionObjectID  = $action_object_id  
		  	   AND ActionObjectClass = $action_object_class   
		       AND AllowedObjectClass= $allowed_class";
	
		$ari->db->StartTrans();
	
		$ari->db->Execute($sql);


		if (!$ari->db->CompleteTrans())
		{
			throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false); //return false;
		}
		else
		{
			return true;
		}//end if	

 	}//end if
 			 
 }//end function
 
 
   /** Este metodo recibe como parametros un objeto accion,
   *   un array de objetos de los cuales se devuelven los 
   *   objetos q le dieron permisos a estos para realizar la accion
   *   pasada como parametro, y un nombre de clase q especifica cuales
   *   de q tipo de objetos q dieron permisos hay q devolver
   **/
 static public function getActionObjectsFor ($action = false, $allowed_objects = false, $action_classes )
 {
 	
 	global $ari;
 	
	$flagError = false;
	
	if ( !is_a($action, 'seguridad_action') )
 	{	
 		$ari->error->addError ("seguridad", "INVALID_ACTION");
 		$flagError = true;	
 	}
 	
 	$allowed_array = array();
 	if ( !is_array($allowed_objects) )
 	{
 		if (is_object ($allowed_objects) )
 		{	$allowed_array[0] = $allowed_objects;	}
 		else
	 	{	
	 		$ari->error->addError ("seguridad", "INVALID_ALLOWED_OBJECT");
	 		$flagError = true;	
	 	} 		
 	}
 	else
 	{	$allowed_array = $allowed_objects;	}
 
 	$classes = array();
 	if ( !is_array($action_classes) )
 	{
 		$action_classes = trim($action_classes);
 		if ( $action_classes != "" )
 		{	$classes[0] = $action_classes;	}
 		else
	 	{	
	 		$ari->error->addError ("seguridad", "INVALID_ACTION_CLASSE");
	 		$flagError = true;	
	 	} 		
 	}
 	else
 	{	$classes = $action_classes;	} 
 	
 	if ($flagError)
 	{
 		return false;	
 	}	
 	else
 	{
 		//forma la clausula IN de las clases de los objetos a devolver
 		$flagFirst = true;
 		$in_classes = "";
 		foreach($classes as $c)
 		{
 			if ($flagFirst)
 			{
 				$in_classes = $ari->db->qMagic($c);
 				$flagFirst = false;
 			}
 			else
 			{ $in_classes .= "," . $ari->db->qMagic($c);	}
 		}//end foreach
 		
 		$array = array();
 		$i = 0;
	 	//recorro el array de permitidos y reacomo los elementos de forma q me quede un array donde
	 	//cada elemento queda con la forma
	 	//$array[clase del objeto q le cedo el permiso][ indice incremental ] = id del objeto
	 	//esto me permite agrupar los elementos 
	  	foreach($allowed_array as $item)
	 	{	
	  		if ( is_object($item) )
	 		{
	 			$array[get_class($item)][$i] = $item->id();
	 			$i++; 
	 		}//end if
	 	}//end foreach
 		
 		$savem = $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
 		$ari->db->SetFetchMode($savem);
 		$action_id = $ari->db->qMagic($action->get('action'));
 		$return = array();
 		$i = 0; 	 	
	 	//ahora armo y ejecuta las consultas, x lo q recorro el array $array
 		foreach( array_keys($array) as $a)
 		{
			//formo un string con los id de los objetos instancias de la clase
			//actual del bucle($a) para realizar la consulta
			
			$in_id = "";
 			$flagFirst = true;
 			foreach($array[$a] as $id)
 			{
 				if ($flagFirst)
 				{	
 					$in_ids = $ari->db->qMagic($id);	
 					$flagFirst = false;		
 				}
 				else
 				{	$in_ids .= "," . $ari->db->qMagic($id);	}
 			}//end foreach
 			
 			//realizo la consulta para los elementos de la clase actual del bucle
 			$sql= "SELECT DISTINCT `Security_ObjectPermission`.`ActionObjectID` AS `ID`, 
						           `Security_ObjectPermission`.`ActionObjectClass` AS `Class`
			   	   FROM Security_ObjectPermission
			       WHERE ActionID = $action_id  
		   	       AND AllowedObjectID IN ($in_ids)
		   	       AND AllowedObjectClass = " . $ari->db->qMagic($a) . " 
		  	       AND ActionObjectClass IN ($in_classes)   
		           ";		
		           
			$rs = $ari->db->Execute( $sql);
			if ($rs && !$rs->EOF) 
			{
				while (!$rs->EOF) 
				{
					$return[$i] = new $rs->fields['Class'] ($rs->fields['ID']);
					$i++;					
					$rs->MoveNext();
				}//end while		
				$rs->Close();
			}//end if 
 		}//end foreach	
 	}//end if
 	
 	if (!is_array($return))
 	{	return false;	}
 	return $return;

 }
 //end function
 
 static public function RequireLogin ()
 {
	  global $ari;
	
	  if (!$ari->user)
		{
		$_SESSION['redirecting'] = $ari->get('url')->realURI();
			if ($ari->get('mode')== "admin")
				header( "Location: " . $ari->get("adminaddress") . '/seguridad/login/');
			else
				header( "Location: " . $ari->get("webaddress") . '/seguridad/login/');
		
		exit;
		}  
		else
		{return false;}
 }
 
 /** este metodo devuelve true si el usuario tiene permiso para la accion dada en un objeto
  * $object = un objeto
  * $action = la accion para el permiso (seguridad_action)
  * $user = el usuario del que se busca el permiso (oob_user)
  * $tree (opcional)(oob_tree_node) = este hace una busqueda recursiva sobre un arbol (a partir de un nodo), para ver si el objeto padre tiene permiso
  */
 static public function isObjectAllowed ($object, $action, $user, $node = false)
	 {
	
	if (!is_a($action, 'seguridad_action'))
	return false;
	
	if (!is_object($object))
	return false;
	
	if ($user == false || is_a($user, 'oob_user'))
	{}
	else
	return false;

 	$currentUserRoles = & seguridad_role :: myRoles($user);
 	
		if (! seguridad :: getPermissionForAnyObject ($action, $currentUserRoles,$object ) )
		 {
		//saltamos al otro metodo
			if ($node !== false)
				{
						 if (!is_a($node, 'OOB_tree_node'))
							return false;
						
	 						$arbol = new OOB_tree ($node->treename(),false);
	 						$tree = & $arbol->getParents ($node, false);
	 						
					if ($roles = seguridad_role :: listRoles(USED,'name',OPERATOR_EQUAL))
						{	
							$arrayRoles = array();
							foreach($roles as $r)
							{	
								//verifico si tiene permiso de ver
							
						     	if ( seguridad :: getObjectPermission($action, $r, $object))
						     	{	
						     		$arrayRoles[] = true;
						     	}	
							}
						}//end if reload de seguridad	
					if (count ($arrayRoles) > 0)
					{return false;}
					else
					{return seguridad:: isObjectAllowedByInheritance ($action,$currentUserRoles , $tree );}
				
				}
		// end salto
		 return false;
		 }
	else
	{return true;}
	 
	 } // end function
 
 /** metodo privado que hace la busqueda de permiso en el arbol */
 static private function isObjectAllowedByInheritance ($action, &$currentUserRoles, & $tree)
	 {
	 
	 if (!is_array($tree))
	 return false;

	 $tree = array_reverse ($tree);
	
	 foreach ($tree as $elemento)
		{ // veamos q no tenga permisos propios, y si tiene cortamos ahi
		if ($roles = seguridad_role :: listRoles(USED,'name',OPERATOR_EQUAL))
			{	
				$arrayRoles = array();
				foreach($roles as $r)
				{	
					//verifico si tiene permiso de ver
			     	if ( seguridad :: getObjectPermission($action, $r, $elemento->object()))
			     	{	
			     		$arrayRoles[] = true;
			     	}	
				}
			}//end if reload de seguridad
			
			if (seguridad :: getPermissionForAnyObject ($action, $currentUserRoles,$elemento->object() ) )
				{
					return true;
				}
				else
				{
					if (count($arrayRoles) > 0)
						{return false;}
				}
		}
	return false;
	 } // end byinheritance
	 
 }//end class 
?>

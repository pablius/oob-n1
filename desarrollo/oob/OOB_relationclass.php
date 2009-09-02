<?php
/**
########################################
#OOB/N1 Framework [©2004,2006]
#
#  @copyright Pablo Micolini
#  @license BSD
######################################## 
*/
 
// OOB_module :: includeClass('note','note_note');
// OOB_module :: includeClass('account','account_account');
// OOB_module :: includeClass('calendar','calendar_appointment');
 
 class OOB_relationclass
 {
 	private static $array_class = array( "account_account","calendar_appointment","note_note",'imagenes_image','formulario_formulario', 'contenido_instancia', 'contenido_tipoimagen','archivos_archivo'); // "account_account","calendar_appointment","note_note",'','contenido_gestorversiones','contenido_instancia'
 	//private static $array_class = array("account_account","calendar_appointment","note_note",'imagenes_image');
 	
 	public function getArrayClass ()
 	{	return self :: $array_class;	} 
 	
 	public function isMember ($class)
 	{
 		if (in_array($class, self :: $array_class))
 		{	return true;	}
 		else
 		{	return false;	}	
 	}//end function
 	
 	/***/
 	public function includeClasses()
 	{
 		//@todo :: sacar las llamads a esta función
//		 OOB_module :: includeClass('note','note_note');
//		 OOB_module :: includeClass('account','account_account');
//		 OOB_module :: includeClass('calendar','calendar_appointment'); 		
 	}//end fucntion
 	
 	//@todo: metodo q valida q existan los metodos
 	
 	//@todo: metodo q valida si el modulo es activo
 	
 	/** lista las clases q se pueden relacionar siempre y cuando formen
 	 * parte de un modulo habilitado
 	 */
 	static public function listRelationClass()
 	{
 		global $ari;
 		$array_query = array();
 		//se deben listar solo las clases de los modulos activos
 		
 		//busco los modulos habilitados
 		$sql = "SELECT `OOB_Modules_config`.`ModuleName` 
 				FROM `OOB_Modules_config`
				WHERE `OOB_Modules_config`.`Status` = " . USED;
		
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
	
		$rs = $ari->db->Execute($sql);
	
		$ari->db->SetFetchMode($savem);
		$modules = array();
		if ($rs && !$rs->EOF) 
		{
			while (!$rs->EOF) 
			{
				$modules[] = $rs->fields("ModuleName");
				$rs->MoveNext();			 					
			}//end while
		}
		$rs->Close();		
 		
 		//comparo el array de clases con los modulos activos
 		$return = array();
 		foreach(self :: $array_class as $c)
 		{
 			$tmp = explode ("_", $c);
 			if (in_array($tmp[0], $modules))
 			{
 				$return[] = $c;	
 			}//end if
 		}//end if 

 		return $return;	   
 	}//end if
 	
	/**
	 * devuelve la URL de busqueda y seleccion
	*/
	static public function getSelectURLForRelation($class)
	{
		global $ari;
		
		if ( in_array($class, self :: $array_class) )
		{
			$tmp = new $class(ID_UNDEFINED);
			return $tmp->getSelectURLForRelation();	
		}
		else
		{	return false;	}
	}//end function


	/**
	 * devuelve V/F segun el usuario actual tenga o no permiso para la 
	 * vista de los objetos de la clase pasada como parametro 
	*/
	static public function getViewPermission($class)
	{
		global $ari;
		
		if ( in_array($class, self :: $array_class) )
		{
			$tmp = new $class(ID_UNDEFINED);
			return $tmp->getViewPermission();	
		}
		else
		{	return false;	}
	}//end function

 	/**
 	 *  Agrega una relacion entre el objeto A y el/los objetos B
	 *  (elimina las anteriores y vuelve a agregar las pasadas, 
	 *   haciendo un grabado multiple)
 	 */
	static public function addRelationFor($objectA, $objectB)
 	{
	 	global $ari;
		$array_query = array();
		
		//valido q el elemento a relacionar sea un elemento relacionable	 	
	 	if ( ! in_array(get_class($objectA), self :: $array_class) )
	 	{	return false;	
		}
	 	
	 	//saco la clase y el id del objeto del objeto relacionable A
	 	$objectAID = $ari->db->qMagic($objectA->id());
	 	$objectAClass = $ari->db->qMagic(get_class($objectA));

		//elimino todas las entradas donde aparece objectA
		$array_query[] = "DELETE FROM OOB_ObjectRelation 
			     		  WHERE OOB_ObjectRelation.ObjectAID = $objectAID
			     		  AND OOB_ObjectRelation.ObjectAClass = $objectAClass 
			     		  ";		

		$array_query[] = "DELETE FROM OOB_ObjectRelation 
						  WHERE OOB_ObjectRelation.ObjectBID = $objectAID
						  AND OOB_ObjectRelation.ObjectBClass = $objectAClass 
						 ";
		
		//var_dump($objectB);exit;
		
	 	if ( !is_array($objectB) )
	 	{	
			//salir en que caso de que los objetos a relacionar sean iguales, es decir, 
			//se esté intentando relacionar un obj consigo mismo
			if (get_class($objectA) == get_class($objectB) && 
				$objectA->id() == $objectB->id() )
			{	return true;
			}
			$objectB = array($objectB);	 	
		}
		
	 	$values = false;
		if ( count($objectB) > 0 )
		{
			$flagFirst = true;
			//modelo: INSERT INTO table (a,b,c) VALUES (1,2,3),(4,5,6)
			foreach ($objectB as $o)
			{
				if (in_array(get_class($o), self :: $array_class)) 	 		
				{
					//agregar solo aquellos objetos distintos de $objectA
					if ( get_class($objectA) <> get_class($o) ||
						 ( get_class($objectA) == get_class($o) && $objectA->id() <> $o->id() )
					   ) 	 		
					{
						$objectBID = $ari->db->qMagic($o->id());
						$objectBClass = $ari->db->qMagic(get_class($o));
						if ($flagFirst)
						{	$values = "($objectAID, $objectAClass, $objectBID, $objectBClass)";	
							$flagFirst = false;
						}
						else
						{	$values .= ",($objectAID, $objectAClass, $objectBID, $objectBClass)";	
						}
					
					}
				
				}//end if
				
			}//end foreach
			
		}//end if count
	 	
		if($values)
		{
			$array_query[] = "INSERT INTO OOB_ObjectRelation 
					  		  (ObjectAID, ObjectAClass, ObjectBID, ObjectBClass) 
							  VALUES $values
							 ";
		}

	 	//realizo la transaccion
	 	if (count($array_query) > 0 )
	 	{		 	
	 		$ari->db->StartTrans();
		 	foreach($array_query as $sql)
		 	{ 	//echo "<br><br>";var_dump($sql);
				$ari->db->Execute($sql);	
			}
			
			if (!$ari->db->CompleteTrans())
			{	return false; 
			}
			else
			{	return true;	
			}
				 		
	 	}//end if
	 	
		return false;
		
 	}//end function	
 	
	/** Devuelve el nombre de la tabla donde se guardan los objetos
	 *  de la clase de la $class 	
	 */
	static private function getTableName($class)
	{
		switch(trim(strtolower($class)))
	 	{
	 		case "account_account":
	 		{
	 			$return = "`Account_Account`";	
	 			break;
	 		}
	 		case "calendar_appointment":
	 		{
	 			$return = "`Calendar_Appointment`";	
	 			break;
	 		} 
	 		case "note_note":
	 		{
	 			$return = "`Note_Note`";	
	 			break;
	 		}	
	 		
	 		case "imagenes_image":
	 		{
	 			$return = "`imagenes_images`";	
	 			break;
	 		}
	 		
			case "imagenes_directory":
	 		{
	 			$return = "`imagenes_directory`";	
	 			break;
	 		}
	 		
	 		case "formulario_formulario":
	 		{
	 			$return = "`formulario_formulario`";	
	 			break;
	 		}

			case "archivos_archivo":
	 		{
	 			$return = "`archivos_archivo`";	
	 			break;
	 		}

			case "contenido_instancia":
	 		{
	 			$return = "`contenido_instancia`";	
	 			break;
	 		}

			case "contenido_tipoimagen":
	 		{
	 			$return = "`contenido_tipoimagen`";	
	 			break;
	 		}
	 					
	 	} //end switch
	 	
	 	return $return;	
	}//end function
 
	/** Devuelve el nombre de la imagen que pertenece
	 *  de la clase de la $class 	
	 */
	static public function getImage($class)
	{
		switch(trim(strtolower($class)))
	 	{
	 		case "account_account":
	 		{
	 			$return = "account16x16.gif";	
	 			break;
	 		}
	 		case "calendar_appointment":
	 		{
	 			$return = "appointment16x16.gif";	
	 			break;
	 		} 
	 		case "note_note":
	 		{
	 			$return = "note16x16.gif";	
	 			break;
	 		} 	
	 		
	 			case "imagenes_image":
	 			case "contenido_tipoimagen":
	 		{
	 			$return = "image16x16.gif";	
	 			break;
	 		} 		
	 		
				case "imagenes_directory":
	 		{
	 			$return = "imagedirectory16x16.gif";	
	 			break;
	 		} 			
			
			case "formulario_formulario":
	 		{
	 			$return = "form.gif";	
	 			break;
	 		}

			case "archivos_archivo":
	 		{
	 			$return = "archivosarchivo16x16.gif";	
	 			break;
	 		}	
	 		
			case "contenido_instancia":
	 		{
	 			$return = "listar.gif";	
	 			break;
	 		}	

	default:
	 		{
	 			$return = "blank.gif";	
	 			break;
	 		}	



	 	} //end switch
	 	
	 	return $return;	
	}//end function 
 
	/**  Este metodo devuelve las relaciones q tiene el objeto 
	 *   pasado como parametro
	 */
	 static public function getRelationsFor ( $object, $classes = "all" )
	 {
	 	global $ari;

		//valido q el elemento del cual bueso las relaciones sea un elemento relacionable	 	
	 	if ( ! in_array(get_class($object), self :: $array_class) )
	 	{	return false;	}
	 	
		$objectClass = $ari->db->qMagic(get_class($object));
		$objectID = $ari->db->qMagic($object->id());
		
		if ( ! is_array($classes) )
		{	$classes = array($classes);	}//end if	 	
	 	
	 	$in_classes = "";
	 	foreach($classes as $c)
	 	{
		 	if ( in_array($c, self :: $array_class) )
		 	{	
		 		if($in_classes == "")
		 		{	$in_classes = $c;	}
		 		else
		 		{	$in_classes .= "," . $c;	}	 		
		 	}//end if	 		
	 	}//end foreach
	 	
	 	$clause_clasesA = "";
	 	$clause_clasesB = "";
	 	if($in_classes != "")
 		{	
 			$clause_clasesA = " AND `OOB_ObjectRelation`.`ObjectAClass` IN ('$in_classes') ";
 			$clause_clasesB = " AND `OOB_ObjectRelation`.`ObjectBClass` IN ('$in_classes') ";	
 		}//end if
	 	
	 	//busco las relaciones "B" del objeto pasado como parametro
	    $sql = "(SELECT `OOB_ObjectRelation`.`ObjectBID` AS `ID`, `OOB_ObjectRelation`.`ObjectBClass` AS `Class` 
	    		 FROM `OOB_ObjectRelation` 
	    		 WHERE `OOB_ObjectRelation`.`ObjectAID` = $objectID
	    		 AND `OOB_ObjectRelation`.`ObjectAClass` = $objectClass
	    		 $clause_clasesB) "; // 
	 	
	 	//busco las relaciones "A" del objeto pasado como parametro
	    $sql .= "UNION (SELECT `OOB_ObjectRelation`.`ObjectAID` AS `ID`, `OOB_ObjectRelation`.`ObjectAClass` AS `Class` 
	    		        FROM `OOB_ObjectRelation` 
	    		        WHERE `OOB_ObjectRelation`.`ObjectBID` = $objectID
	    	 	         AND `OOB_ObjectRelation`.`ObjectBClass` = $objectClass
	    		        $clause_clasesA) "; //
	    			 		
	 	$savem = $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
	 	$ari->db->SetFetchMode($savem);
		$return = array();
 		$i = 0; 	 	
	 	//ahora armo y ejecuta las consultas, x lo q recorro el array $array
        
	//	var_dump  ($sql);
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

	 	if (count($return) == 0)
	 	{	return false;	}
	 	return $return;
	
	 }
	 //end function


 	/**
 	 *  Agrega UNA relacion entre el objeto A el objeto B 
	 *  (diff con addRelationFor: solo agrega una relacion, 
	 *   manteniendo las anteriores )
 	 */
	static public function addNewRelationFor($objectA, $objectB)
 	{
	 	global $ari;
		
		//valido q el elemento a relacionar sea un elemento relacionable	 	
	 	if ( ! in_array(get_class($objectA), self :: $array_class) || 
			 ! in_array(get_class($objectB), self :: $array_class))
	 	{	return false;	
		}
	 	
	 	//clean vars
	 	$objectAID = $ari->db->qMagic($objectA->id());
	 	$objectAClass = $ari->db->qMagic(get_class($objectA));
	 	$objectBID = $ari->db->qMagic($objectB->id());
	 	$objectBClass = $ari->db->qMagic(get_class($objectB));
		
		$sql = "INSERT INTO OOB_ObjectRelation 
				  (ObjectAID, ObjectAClass, ObjectBID, ObjectBClass) 
				VALUES 
				  ($objectAID,$objectAClass,$objectBID,$objectBClass)
			   ";		
		
		$ari->db->StartTrans();   						
		$ari->db->Execute($sql);	
		if (!$ari->db->CompleteTrans())
		{	return false; 
		}
		else
		{	return true;	
		}
		
 	}//end function	

 	/**
 	 *  Elimina la relacion del objeto A con el objeto B  
 	 */
	static public function deleteRelationFor($objectA, $objectB)
 	{
	 	global $ari;
		
		//valido q el elemento a relacionar sea un elemento relacionable	 	
	 	if ( ! in_array(get_class($objectA), self :: $array_class) || 
			 ! in_array(get_class($objectB), self :: $array_class))
	 	{	return false;	
		}
	 	
	 	//clean vars
	 	$objectAID = $ari->db->qMagic($objectA->id());
	 	$objectAClass = $ari->db->qMagic(get_class($objectA));
	 	$objectBID = $ari->db->qMagic($objectB->id());
	 	$objectBClass = $ari->db->qMagic(get_class($objectB));
		
		//borrar la relacion A -> B
		$array_query[] = "DELETE FROM `OOB_ObjectRelation` 
						  WHERE `OOB_ObjectRelation`.`ObjectAID` = $objectAID
						  AND `OOB_ObjectRelation`.`ObjectAClass` = $objectAClass
						  AND `OOB_ObjectRelation`.`ObjectBID` = $objectBID
						  AND `OOB_ObjectRelation`.`ObjectBClass` = $objectBClass 
						 ";		

		//borrar la relacion B -> A (inversa)
		$array_query[] = "DELETE FROM `OOB_ObjectRelation` 
						  WHERE `OOB_ObjectRelation`.`ObjectAID` = $objectBID
						  AND `OOB_ObjectRelation`.`ObjectAClass` = $objectBClass
						  AND `OOB_ObjectRelation`.`ObjectBID` = $objectAID
						  AND `OOB_ObjectRelation`.`ObjectBClass` = $objectAClass 
						 ";		
	 
		//realizo la transaccion
		$ari->db->StartTrans();
		foreach($array_query as $sql)
		{	//echo "<br><br>";var_dump($sql);
			$ari->db->Execute($sql);	
		}//end foreach
		//exit;
		
		if (!$ari->db->CompleteTrans())
		{	return false; 
		}
		else
		{	return true;	
		}
		
 	}//end function	

 	
 
	/**  Retorna true si el objeto pasado tiene alguna relacion con otro 
	 *   objeto en el sistema
	 */
	 static public function hasRelations( $object, $classes = "all" )
	 {
	 	global $ari;

		//valido q el elemento del cual busco las relaciones sea un elemento relacionable	 	
	 	if ( ! in_array(get_class($object), self :: $array_class) )
	 	{	return false;	
		}
	 	
		$objectClass = $ari->db->qMagic(get_class($object));
		$objectID = $ari->db->qMagic($object->id());
		
		//verificar si se busca relacion con todas las clases
		if (!is_array($classes) && $classes == "all")
		{	$classes = self::$array_class;
		}
		
		if ( ! is_array($classes) )
		{	$classes = array($classes);	}//end if	 	
	 	
	 	$in_classes = "";
	 	foreach($classes as $c)
	 	{
		 	//var_dump($c);exit;
			if ( in_array($c, self :: $array_class) )
		 	{	
		 		if($in_classes == "")
		 		{	$in_classes = $c;	
				}
		 		else
		 		{	$in_classes .= "," . $c;	
				}	
				 		
		 	}
			else
			{	throw new OOB_exception("La Clase de Objeto Relacional no es válida.", "1056", "La Clase de Objeto Relacional no es válida.", true);
			}
			 		
	 	}//end foreach
	 	
	 	$clause_clasesA = "";
	 	$clause_clasesB = "";
	 	if($in_classes != "")
 		{	
 			$clause_clasesA = " AND `OOB_ObjectRelation`.`ObjectAClass` IN ('$in_classes') ";
 			$clause_clasesB = " AND `OOB_ObjectRelation`.`ObjectBClass` IN ('$in_classes') ";	
 		}//end if
	 	
	 	//relaciones "B" del objeto pasado como parametro
	    $sql = "(SELECT 1 
	    		 FROM `OOB_ObjectRelation` 
	    		 WHERE `OOB_ObjectRelation`.`ObjectAID` = $objectID
	    		 AND `OOB_ObjectRelation`.`ObjectAClass` = $objectClass
	    		 $clause_clasesB) ";  
	 	
	 	//relaciones "A" del objeto pasado como parametro
	    $sql .= "UNION (SELECT 1 
	    		        FROM `OOB_ObjectRelation` 
	    		        WHERE `OOB_ObjectRelation`.`ObjectBID` = $objectID
	    	 	         AND `OOB_ObjectRelation`.`ObjectBClass` = $objectClass
	    		        $clause_clasesA) "; 
	    
		//echo "<br><br>" . $sql;
					 		
	 	$savem = $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
	 	$ari->db->SetFetchMode($savem);
		$return = false;
		$rs = $ari->db->Execute( $sql);
		if ($rs && !$rs->EOF) 
		{	$return = true;
			$rs->Close();
		} 

	 	return $return;
	
	 }//end function
	 
 
 
}//end class
?>
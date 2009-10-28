<?php
/**
########################################
#OOB/N1 Framework [©2004,2009]
#
#  @copyright Pablo Micolini
#  @license BSD
#  @version 5
######################################## 
*/
 

 class OOB_tree{
	
	private $name = '';
	private $id = ID_UNDEFINED;
	private $nodes = 0;
	private $multiple;

	
	
	public function id ()
	{
		return $this->id;
	}
	
	public function name ()
	{
		return $this->name;
	}
	
	/** Starts the tree.
	 * $name (string), debe existir en la DB
	 * $multipe (bool), permite ingresar mas de una vez el mismo objeto al arbol 
 	 */ 	
 	public function __construct ($name = '', $multiple = true)
 	{
 		global $ari;

		if ($name != '') 
		{
			$this->name= $name;
				
			if (!$this->fill ())
			{throw new OOB_exception("Invalid tree {$name}", "403", "Invalid Tree", false);} 
	
					
		} 
	
	// @todo Implementar MULTIPLE, para q el tree valide si un objeto puede ser
	// agregado más de una vez al árbol. 
	
		$this->multiple = true;
		if ($multiple == false)
			$this->multiple = false;
 	}
 	
 	/** Returs the value for the given var */ 	
 	public function get ($var)
 	{
			return $this-> $var;
 	}

	/** Sets the variable (var), with the value (value) */ 	
 	public function set ($var, $value)
 	{
			$this->$var= $value;
	}
 	
	/** Fills the tree with the DB data */ 	
 	private function fill ()
 	{
		global $ari;

		//load info
		$name = $ari->db->qMagic ($this->name);
		$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
		$sql= "SELECT ID, Name, Nodes FROM OOB_Tree_Tree WHERE Name = $name";
		$rs = $ari->db->Execute($sql);
			
		$ari->db->SetFetchMode($savem);
		if (!$rs || $rs->EOF) {
			return false;
		}
		if (!$rs->EOF) 
		{
			$this->id = $rs->fields[0];
			$this->nodes = $rs->fields[2];
		}
			$rs->Close();
			return true; 		
 	} 
 
 /** Devuelve un array de OOB_tree_node
  *  $root (OOB_tree_node o TREE_ROOT), lugar desde donde empieza a cargar el arbol
  *  $class_names (false, string o array), nombre de las clases que quiere ver del arbol
  *  $initialize (bool), si desea inicializar los objetos del arbol (penalidad de velocidad)
  *  */
 	public function getTree($root = TREE_ROOT, $class_names = false, $initialize = true)
 	{
 		global $ari;
 		$tree_id =  $ari->db->qMagic ($this->id);

 		if (!is_array($class_names))
 			{
 				if (!$class_names)
				{$classSQL = "";}
				else
 				{$classSQL = " AND N.ClassName = " . $ari->db->qMagic ($class_names);}
 			}
 		else
 			{$classSQL = " AND N.ClassName IN (";
			foreach ($class_names as $clase)
				{
					$classSQL .= "'" . $clase . "', ";
				}
			$classSQL = substr ($classSQL, 0, strlen($classSQL) -2);
			$classSQL .= ")"; 
			}
 		
		
		
 		$init = true;
 		if (OOB_validatetext::isBool($initialize))
 		 	{$init = $initialize;}	
 		 
 		
 		if (!is_a ($root, 'OOB_tree_node')) // $root == TREE_ROOT)
 		{
 			//traigo todo el arbol
			$sql= "SELECT N.ID, N.ClassName, N.ObjectID, N.Left, N.Right  
				   FROM OOB_Tree_Node N
				   WHERE N.TreeID = $tree_id
				    ".  // AND N.Left > 1 (hace falta?)
				   $classSQL 
				   . " ORDER BY N.left ASC";
 		}
 		else
 		{
		$rootID = $ari->db->qMagic ($root->id());
		$rootL = $ari->db->qMagic ($root->left());
		$rootR = $ari->db->qMagic ($root->right());
		
	
		
 			$sql= "SELECT  N.ID, N.ClassName, N.ObjectID, N.Left, N.Right  
			       FROM OOB_Tree_Node N
			       WHERE N.TreeID = $tree_id
			       AND N.Left > $rootL
			       AND N.Right <= $rootR". 
				   $classSQL 
				   . "
			       ORDER BY N.left ASC"; // rright *2
 		}
 		
 		$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
		//	var_dump ($sql);
		$rs = $ari->db->Execute($sql);
	
		$ari->db->SetFetchMode($savem);
		if (!$rs || $rs->EOF) 
		{
			$result =  false;
		}
		else
		{
		//	var_dump ($classes);
			while (!$rs->EOF) 
			{
		//		if (!$classes || in_array($rs->fields[1],$classes))
		//		{
				$result[] =  new OOB_tree_node	($rs->fields[0], $rs->fields[1], $rs->fields[2], $rs->fields[3], $rs->fields[4], $this->id, $this->name(), $init);
		//		}
			$rs->MoveNext(); 	
			}
		}
		$rs->Close();
		return $result;
 		
 	}//end function
 
 /**AddNode `
  * agrega in nodo. Puede pasarle el ID del parent donde lo quiere agregar u otro objeto,
  * y el tree busca cual es el id de nodo solo.
  * $object = objeto a agregar (tiene que tener implementado el metodo ID)
  * $parentID = id del nodo padre, u objeto padre ya en el arbol
  * */
 	public function addNode($object, $parentID, $asLast = true) 
 	{
		global $ari;
		$delta = 0;
		$rootNode = false;
			
		if (is_object($object))
			{	
			$clase = $ari->db->qMagic(get_class($object));
			$id_obj = $ari->db->qMagic($object->id());
			}
		else
			{throw new OOB_exception('', "905", "Invalid Tree Parameter");}
	
		
		// si no perimite multiples veces el mismo objeto, validamos para ver is ya está
		if (!$this->multiple)
		{
			if (OOB_tree :: getNode(false, $object, $this))
				return false;
		}
		
		if ($this->nodes == 0)
			$rootNode = true;



//3 casos
//1- es un objeto X
//2 - es un objeto NODO
//3 - es tree root

if ($parentID === TREE_ROOT || !$parentID )
{
		// buscar el max R, y de ahi sumarle.
		if ($this->nodes = 0)
		{	
			$max_right = 0;
		}
		else
		{
			$max_right = $this->getLastRight();
		}

		$left = $max_right + 1; 
		$right =$max_right + 2;
}
else
{ // no es tree root, tiene que ser objeto
	if (is_object ($parentID))
		{ 
		if (is_a($parentID,'OOB_tree_node'))
			{ // es un nodo
			
						$node_left = $parentID->left();
						$node_right = $parentID->right();
			
			}
			else
			{ // es un objeto adentro del arbol
					if ($parentObject = OOB_tree :: getNode(false, $parentID, $this))
					{
						$node_left = $parentObject->left(); 
						$node_right = $parentObject->right();
					} 
					else
					{ // no encontramos el objeto en el arbol
						return false;
					}
			}
			
			// datos
			if (!$asLast)
				{	
					$left = $node_left + 1; // ['right'];
					$right = $node_left + 2;
				}
				else
				{
				
				  $left = $node_right;
				  $right = $node_right + 1;
				
				}
			$delta = 2;
			// end datos
		} 
		else
		{ // este else no tiene que pasar nunca!
			return false;
		}
}


		//falta validar si ya existe el mismo nodo
		
		$ari->db->StartTrans();
	
		if ($delta > 0)
			$this->addDelta ($left, $delta);
		
		$id_arbol = $ari->db->qMagic ($this->id);
		$left = $ari->db->qMagic ($left);
		$right = $ari->db->qMagic ($right);
		// real insert
		$sql= "INSERT INTO OOB_Tree_Node 
			  ( `TreeID`, `ClassName`, `ObjectID`, `Left`, `Right`)
			  VALUES 
			  ( $id_arbol, $clase, $id_obj, $left, $right )";
			   
		$ari->db->Execute($sql);

		//recuento
		$sql = "UPDATE OOB_Tree_Tree  
			   SET Nodes = Nodes + 1  
			   WHERE ID = $id_arbol ";
			 
 		$ari->db->Execute($sql);	 
	
	
		if (!$ari->db->CompleteTrans())
		{
			throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false); //return false;
		}
		else
		{
			//$this->fill();
			$this->nodes ++;
			return true;
		}
			
	}//end function
 
 	/** Devuelve el ultimo right que existe en el arbol */
	private function getLastRight ()
	{
	global $ari;
	
			$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
			$sql= "SELECT *
					FROM OOB_Tree_Node
					WHERE TreeID = $this->id 
					ORDER BY OOB_Tree_Node.Right DESC
					LIMIT 0 , 1
					";
			$rs = $ari->db->Execute($sql);
			
		$ari->db->SetFetchMode($savem);

		if (!$rs->EOF) 
		{
			$return =  $rs->fields[5];
		}
		else
		{	$return = 0;}
		$rs->Close();
		
		return $return;

	} //end function
 /** Este metodo devuelve un nodo en especifico, existen dos posibilidades para traer un nodo
  * El primero se especifica el id del nodo el cual se encuentra en la tabla OOB_Tree_Node,
  * y los otros parametros $object $tree se ponen en false
  * La otra posibilidad es pasar en $object un objeto y en tree el nombre de un arbol
  * con lo q se devuelve le nodo relacionado con el objeto $object en el arbol $tree 
  * */
 static public function getNode ($id = false, $object = false, $tree)
 {
 	global $ari;
 	
 	$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
 	$id = $ari->db->qMagic($id);
 	
 	if (!is_a($tree, 'OOB_tree'))
 	{return false;}

 	if (is_object($object))
 	{
 		$clase = $ari->db->qMagic(get_class($object));
 		$id_object = $ari->db->qMagic($object->id());
 		$id_tree = $ari->db->qMagic($tree->id());
 		
 		$sql = "SELECT ID, ClassName, ObjectID, OOB_Tree_Node.Left, OOB_Tree_Node.Right, TreeID 
				FROM OOB_Tree_Node 
    			WHERE TreeID = $id_tree 
    			AND ClassName = $clase 
				AND ObjectID = $id_object "; 		
 	}
 	else
 	{
 		$sql = "SELECT ID, ClassName, ObjectID, OOB_Tree_Node.Left, OOB_Tree_Node.Right, TreeID 
				FROM OOB_Tree_Node 
    			WHERE ID = $id ";
 	}
 	
 	$rs = $ari->db->Execute($sql);
	$ari->db->SetFetchMode($savem);
	if ($rs && !$rs->EOF) 
	{  
		$return = new OOB_tree_node	($rs->fields[0], $rs->fields[1], $rs->fields[2], $rs->fields[3], $rs->fields[4], $rs->fields[5],$tree->name(), false);
	}
	else
	{
		$return = false;
	}			
	$rs->Close();
	return $return; 	 	
 }//end function
 
  // nodo anterior
  static public function getPrevious ($node, $tree)
 {
 	global $ari;
 	
 	$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);

 	if (is_a($node, 'OOB_tree_node') && is_a($tree, 'OOB_tree'))
 	{
 		$right =  $ari->db->qMagic($node->left - 1);
 		$id_tree = $ari->db->qMagic($tree->id());
 		
 		$sql = "SELECT ID, ClassName, ObjectID, OOB_Tree_Node.Left, OOB_Tree_Node.Right, TreeID 
				FROM OOB_Tree_Node 
    			WHERE OOB_Tree_Node.TreeID = $id_tree 
    			AND OOB_Tree_Node.Right = $right  "; 		
 	} else
	{return false;}

 	$rs = $ari->db->Execute($sql);
	$ari->db->SetFetchMode($savem);
	if ($rs && !$rs->EOF) 
	{  
		$return = new OOB_tree_node	($rs->fields[0], $rs->fields[1], $rs->fields[2], $rs->fields[3], $rs->fields[4], $rs->fields[5],$tree->name(), false);
	}
	else
	{
		$return = false;
	}			
	$rs->Close();
	return $return; 	 	
 }//end function
 
  // nodo posterior
  static public function getNext ($node, $tree)
 {
 	global $ari;
 	
 	$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);

 	if (is_a($node, 'OOB_tree_node') && is_a($tree, 'OOB_tree'))
 	{
 		$left =  $ari->db->qMagic($node->right + 1);
 		$id_tree = $ari->db->qMagic($tree->id());
 		
 		$sql = "SELECT ID, ClassName, ObjectID, OOB_Tree_Node.Left, OOB_Tree_Node.Right, TreeID 
				FROM OOB_Tree_Node 
    			WHERE OOB_Tree_Node.TreeID = $id_tree 
    			AND OOB_Tree_Node.Left = $left  "; 		
 	} else
	{return false;}

 	$rs = $ari->db->Execute($sql);
	$ari->db->SetFetchMode($savem);
	if ($rs && !$rs->EOF) 
	{  
		$return = new OOB_tree_node	($rs->fields[0], $rs->fields[1], $rs->fields[2], $rs->fields[3], $rs->fields[4], $rs->fields[5],$tree->name(), false);
	}
	else
	{
		$return = false;
	}			
	$rs->Close();
	return $return; 	 	
 }//end function
 
 
 
 /** delete node */
 	public function delNode($node, $cascade = false)
 	{
 		global $ari;
		 $ari->internalChrono('delNode_start');
 
 	if (!is_a($node, 'OOB_tree_node'))
 		{	
 			if ($this->multiple)
 				{return false;}
 			else
 			{
 				if ($node = OOB_tree::getNode (false, $node, $this))
 					{}
 				else
 					{return false;}
 			}
 		
 		}
//var_dump ($node);
//exit;
// 		
 		$id = $ari->db->qMagic($node->id()); // $node['id'];
 		$left =  $ari->db->qMagic($node->left()); // $node['left'];
 		$right  = $ari->db->qMagic($node->right());// $node['right'];
 		$tree =  $ari->db->qMagic($node->treeid());//$node['treeid'];		
 	//	$move_cascade =  $ari->db->qMagic($node->right() - $node->left() + 1); //$node['right'] - $node['left'] +1 ; 
 		$delta = $node->left() -$node->right() - 1;

		$ari->db->StartTrans();	 
		
 		if ($cascade)
 		{  // @todo: verify, no estoy seguro que esto funcione del todo bien!
 			//borro en cascada los hijos
			

 			$sql_cascade = "DELETE FROM OOB_Tree_Node
 			     	 		WHERE TreeID = $tree
						    AND `OOB_Tree_Node.Left` >= $left AND `OOB_Tree_Node.Right` <= $right";
			 			 
			$ari->db->Execute($sql_cascade);
 			
			$this->addDelta($node->right() +1 , $delta);
			
			//actualizo la cantidad de nodos
 			$sql_update = "UPDATE OOB_Tree_Tree 
				           SET `Nodes` = (SELECT COUNT(True) 
				           				  FROM OOB_Tree_Node
 		    	 	       				  WHERE TreeID = $tree) 
					       ";
			$ari->db->Execute($sql_update);	
 		}
 		else
 		{	
				$sql= "DELETE FROM OOB_Tree_Node
 					   WHERE ID = $id ";
 			   
 				$ari->db->Execute($sql);
		
 			//actualizo los hijos del borrado
 			$sql_update = "UPDATE OOB_Tree_Node 
				           SET `Left` = `Left` - 1,  `Right` = `Right` - 1
 		    	 	       WHERE TreeID = $tree 
					       AND `Left` BETWEEN $left AND $right ";
			$ari->db->Execute($sql_update);
			
 			//actualizo el padre del borrado el cual tiene izquiera menor y derecha mayor
 			$sql_update = "UPDATE OOB_Tree_Node 
				           SET `Right` = `Right` - 2
 		    	 	       WHERE TreeID = $tree
					       AND `Right` > $right 
					       AND `Left` < $left ";
			$ari->db->Execute($sql_update);
					
			//actualizo los nodos q no estan en la rama del borrado
 			$sql_update = "UPDATE OOB_Tree_Node 
				           SET `Left` = `Left` - 2,  `Right` = `Right` - 2
 		    	 	       WHERE TreeID = $tree 
					       AND `Left` > $right ";
			$ari->db->Execute($sql_update);

			//actualizo la cantidad de nodos
 			$sql_update = "UPDATE OOB_Tree_Tree 
				           SET `Nodes` = `Nodes` - 1
 		    	 	       WHERE ID = $tree
					       ";
			$ari->db->Execute($sql_update);
		       			
 		}
 		
 	
		if (!$ari->db->CompleteTrans())
		{
			throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false); //return false;
		}
		else
		{	
			
			$this->fill();
			return true;
		}
 		 $ari->internalChrono('delNode_end');
 	}//end function
 
 
// /**Devuelve un array con tres array dentro el primero 'tree' contiene el arbol recorriendo desde $root	
//  * el segundo 'parents' contiene un array con los padres de $node, y el ultimo 'childs' es un array con
//  * los hijos del $node pasado como parametro
//  * */

/** Parametros:
 * $selected  = (oob_tree_node o TREE_ROOT), el nodo que va a volver seleccionado en el array
 * $node  = (oob_tree_node o TREE_ROOT), el nodo en el que estamos parados (para considerar padres e hijos)
 * $root = (oob_tree_node o TREE_ROOT), el nodo desde donde empieza a armarse el arbol
 * $class_name = (false, string o array), nombre de la clase/clases que va a devolver el arbol
 * $initialize = (bool), inicializa los objetos del arbol (penalidad de velocidad, necesario si quiere validar STATUS)
 * $cantNiveles = (int), cantidad de niveles que quiere que el arbol recorra, por defecto todos
 * 
 * Return: Este objeto devuelve un array con 3 array dentro (tree, childs, parents), cada uno a su vez dentro tiene:
 * donde ['nivel'], es su nivel en el árbol
 *       ['nodo'], es un objeto OOB_tree_node
 * */
 	public function getTreeAdvanced($selected = TREE_ROOT, $node = TREE_ROOT, $root = TREE_ROOT, $class_names = false, $initialize = true, $cantNiveles = 99999)
 	{ 
		global $ari;
		 $ari->internalChrono('getTreeAdvanced_start');
		$return['tree'] = array();
		$return['childs'] = array();
		$return['parents'] = array();
		
		// validamos los datos viste
		if (!is_a ($node, 'OOB_tree_node')) // $node === TREE_ROOT) //
		{$node = new OOB_tree_root ($this->id);}
		
		if (is_a($selected, 'OOB_tree_node'))
		{$selected = $selected->id;}
		
		$init = true;
 		if (OOB_validatetext::isBool($initialize))
 		 	{$init = $initialize;}
 		
 		$levels = 0;
 		if (OOB_validatetext::isInt($cantNiveles))
 		 	{$levels = $cantNiveles;} 	
 		 	
 		if (!is_array($class_names))
 			{
 				if (!$class_names)
				{$classes = false;}
				else
 				{$classes[]= $class_names;}
 			}
 		else
 			{$classes= $class_names;}
		
		//my_nivel contendra el nivel del nodo cargado en la pagina
		$my_nivel = 0;
		//bandera q permite o no la carga de hijos del nodo cargado
		$flagCargaHijos = false;
	

			
		if ($nodes = $this->getTree($root,$classes, $init))
		{
		
			$i = 0;
			//array donde acumulo las derecha de los nodos de una raiz completa
			$niveles = array();
			
			foreach ($nodes as $n)
			{
				$ok = USED;
				if ($init)
				{$ok = $n->object()->get('status');}
		

				if($ok <> DELETED) 
				  {
					$cuenta = count($niveles);
					
					// no entiendo que hace esto, pero anda de diez	
					if ($cuenta > 0)
					{ 
						while ($niveles[$cuenta-1]<$n->right()) 
						{ 
							array_pop($niveles);
							$cuenta = count($niveles);
							if ($cuenta == 0)
							{
								//si termino cualquier rama desabilito la carga de hijos 
								$flagCargaHijos = false;
								break;
							}
						}
					}
				
					
					// si hay limitación de nivel, la validamos
					if ($levels > $cuenta || $levels == 0 ) /* si alguien entiende xq esto andaba antes se gana un premio */
					{ 
						
						// lo ponemos en el arbol
						$return['tree'][$i]['nivel'] = "";
						$return['tree'][$i]['node'] = $n;
						
						// marcamos al elegido	 (selected)					
						if ($selected == $n->id() )
						{	$return['tree'][$i]['selected'] = "selected"; 	}
					
						
						
						//guardo el nivel del nodo cargado en la pagina y activo la carga de sus hijos
						//ya q llegue al nodo cargado en el recorrido
						if ($n->left() == $node->left())
						{
							$my_nivel = $cuenta;
							$flagCargaHijos = true;
						
						}
											
						//busco los padres del nodo cargado los cuales deben tener una izquierda mayor q
						//el nodo cargado y una derecha mayor q la derecha del nodo cargado		
						if ($n->left() < $node->left() && $n->right() > $node->right())
						{	
								
							$return['parents'][] = $return['tree'][$i]; 
						} 
						else // un hijo no puede ser padre
						{
							if (($cuenta == 0  && $n->id() != $node->id() && $root !== TREE_ROOT) 
							||  ($my_nivel + 1 == $cuenta && $flagCargaHijos && $node->right() > $n->right()  && $root === TREE_ROOT ))
								{
									
										$return['childs'][] = $return['tree'][$i]; 
								}
						}
	
						$return['tree'][$i]['nivel'] .= str_repeat('--',$cuenta); 
						$niveles[] = $n->right(); //['right']; 
							
						++$i;
					}// end if nivel
				}//end if enabled
			}//end foreach
		}// end if
		 $ari->internalChrono('getTreeAdvanced_end');
	
		return $return ;	
 	}//end function
 	
  /**
   * Get parents for the given node
   * */
    public function getParents($node, $initialize = true)
    {
    global $ari;
	
    if (!is_a ($node, 'OOB_tree_node'))
    	{return false;}
		
		$tree_id = $ari->db->qMagic ($this->id);
 			
	$init = true;
	if (OOB_validatetext::isBool($initialize))
	 	{$init = $initialize;}	
 		
 		$nodeID = $ari->db->qMagic($node->id());
 		$nodeLeft = $ari->db->qMagic($node->left());
 		$nodeRight = $ari->db->qMagic($node->right());
  	# select all nodes where parent.leftvisit<=node.leftvisit and parent.rightvisit>=node.rightvisit
	# order by parent.leftvisit descending gives bottom up path		
			$sql= "SELECT N.ID, N.ClassName, N.ObjectID, N.Left, N.Right  
				   FROM OOB_Tree_Node N
				   WHERE N.TreeID = $tree_id
				   AND N.Left <=  $nodeLeft
				   AND N.Right >= $nodeRight
				   AND N.ID " .OPERATOR_DISTINCT. " $nodeID	
				   ORDER BY N.left ASC";
 		
 		$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
			
		$rs = $ari->db->Execute($sql);
	
		$ari->db->SetFetchMode($savem);
		if (!$rs || $rs->EOF) 
		{
			$result =  false;
		//	print "OOB_ROOT";
		}
		else
		{
			$result = array(); 
			while (!$rs->EOF) 
			{ 
				//	print "nodo: " . $rs->fields[0] . ",obj-id: " .$rs->fields[2]."<br>";
				
				$result[] = new OOB_tree_node	($rs->fields[0], $rs->fields[1], $rs->fields[2], $rs->fields[3], $rs->fields[4], $this->id, $init);
			$rs->MoveNext(); 	
			}
		}
		$rs->Close();
	//	var_dump ($result);
		return $result;  
    
    }
  
  
 
 // empezamos a ver lo de mover arboles
 
 /** agrega '$delta' a todos los valores de  L y R  que son >= '$node->left'.  */
 private function  addDelta ($left, $delta) // _shiftRLValues ($thandle, $first, $delta)
{
global $ari;
 		$left = $ari->db->qMagic ($left); // $node['left'];
 		$tree = $ari->db->qMagic ($this->id);//$node['treeid'];		
		 
 $ari->db->StartTrans();
 $ari->db->Execute("UPDATE OOB_Tree_Node SET OOB_Tree_Node.Left = OOB_Tree_Node.Left + $delta WHERE OOB_Tree_Node.TreeID = $tree AND OOB_Tree_Node.Left >= $left");
 $ari->db->Execute("UPDATE OOB_Tree_Node SET OOB_Tree_Node.Right = OOB_Tree_Node.Right + $delta WHERE OOB_Tree_Node.TreeID = $tree AND OOB_Tree_Node.Right >= $left");

	if (!$ari->db->CompleteTrans())
		{throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false);}
	else
		{return true;}

} // end function


 /** agrega '$delta' a todos los valores de  L y R  que son >= '$primero->left' y <= '$last->left'.  
   returns the shifted first/last values as node array.
 */
  private function addDeltaRango ($primero, $ultimo, $delta)//_shiftRLRange ($thandle, $first, $last, $delta)
{
	global $ari;
 		$leftPrimero = $ari->db->qMagic ($primero); 
		$leftUltimo = $ari->db->qMagic ($ultimo); 
 		$tree = $ari->db->qMagic ($this->id);
	
		 
 $ari->db->StartTrans();
 $ari->db->Execute("UPDATE OOB_Tree_Node SET OOB_Tree_Node.Left = OOB_Tree_Node.Left + $delta 
 					WHERE OOB_Tree_Node.TreeID = $tree 
 					AND OOB_Tree_Node.Left >= $leftPrimero 
 					AND OOB_Tree_Node.Left <= $leftUltimo");
					
 $ari->db->Execute("UPDATE OOB_Tree_Node SET OOB_Tree_Node.Right = OOB_Tree_Node.Right + $delta 
 					WHERE OOB_Tree_Node.TreeID = $tree 
					AND OOB_Tree_Node.Right >= $leftPrimero 
 					AND OOB_Tree_Node.Right <= $leftUltimo");

	if (!$ari->db->CompleteTrans())
		{throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false);}
	else
		{
		return true;
		}
} // end function
 
 /** Mueve el $node a  la posición donde está $to */
private function moveSubtree ($node, $to) 
{ 
global $ari;

	if (!is_a($node, 'OOB_tree_node') || !is_a($to, 'OOB_tree_node'))
 		{	throw new OOB_exception("Uno de los parametros pasados a moveSubTree no es un objeto OOB_tree_node, esto no puede suceder", "749", "Error en Arbol", true);}

	$ari->db->StartTrans();
	
	// veamos si el $to es un hijo del $nodo
	if ($node->left() < $to->left() && $node->right() > $to->right())
	{
	return false;
	// @optimize: es probable que se pueda diseñar una solucion que permita mover un sub-arbol en estas condiciones, no lo he analizado.
	}
	else // no es, estamos a salvo!
	{
		
	$treesize = $node->right() - $node->left() + 1; 

	$this->addDelta ($to->left(), $treesize); 
	
	if($node->left() > $to->left())
		{ 
		$node->left += $treesize;
		$node->right += $treesize;
		}
		
	$this->addDeltaRango($node->left(), $node->right(), $to->left() - $node->left() );
  	$this->addDelta ($node->right() + 1, -$treesize);  
	}
	

  	if (!$ari->db->CompleteTrans())
		{throw new OOB_exception("Error en DB: {$ari->db->ErrorMsg()}", "010", "Error en la Base de Datos", false);}
	else
		{return true;}
  
} // end function


/* ******************************************************************* */
/* Movida publica 
/* ******************************************************************* */


/* moves the node '$src' and all its subtree to  the next sibling of '$dst'. */
public function moveDown ($src, $dst)
	{
	if ($dst === TREE_ROOT) {return false;}
	 
		$dst->left = $dst->right + 1;
		return $this->moveSubtree ($src, $dst);
	}

/* moves the node '$src' and all its subtree to the prev sibling of '$dst'. */
public function moveUp ($src, $dst)
{
	if ($dst === TREE_ROOT) {return false;}
	
	$dst->left = $dst->left; // wadafuck?
	return $this->moveSubtree ($src, $dst); //$dst['l']
}

/* moves the node '$src' and all its subtree to the first child of '$dst'. */
public function moveAsFirstChild ($src, $dst)
{
	//if ($dst === TREE_ROOT) {return false;}

	if ($dst === TREE_ROOT) 
		{ $dst = new OOB_tree_node (0, '', 0, 0, 0,$this->id(),$this->name(), false) ;}
	
	$dst->left = $dst->left + 1;
	return $this->moveSubtree ($src, $dst);
}

/* moves the node '$src' and all its subtree to the last child of '$dst'. */
public function  moveAsLastChild ($src, $dst) 
{
	//if ($dst === TREE_ROOT) {return false;}
	
	if ($dst === TREE_ROOT) 
		{
		$dst = $this->getNodeWhere (false,$this->getLastRight()) ;	
		$dst->right = $dst->right +2;
		}  
	
	$dst->left = $dst->right; /// no será right -1 ?
	return $this->moveSubtree ($src, $dst);
}


/* devuelve el nivel del nodo, si está en el raiz va a ser 0 */
private function getLevel ($node)
{ 

	global $ari;
   	$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
	$id_tree = $ari->db->qMagic($this->id());
	$node_right = $ari->db->qMagic($this->right());
	$node_left = $ari->db->qMagic($this->left());

 		$sql = "SELECT COUNT(*) 
				FROM OOB_Tree_Node 
    			WHERE TreeID = $id_tree 
    			AND 
				AND OOB_Tree_Node.Left < $node_left
				AND OOB_Tree_Node.Right > $node_right"; 	
 	
 	$rs = $ari->db->Execute($sql);
	$ari->db->SetFetchMode($savem);
	if ($rs && !$rs->EOF) 
	{  
		$return = $rs->fields[0];
	}
	else
	{
		$return = false;
	}			
	$rs->Close();
	return $return; 	

}
////////////////

 



 /** Devuelve un nodo dadas una ciertas caracteristicas (left y right), puede definirse los operadores de cada una */ 
 private function getNodeWhere ($left = false, $right = false, $operator_left = "=",$operator_right = "=")
  {
  	global $ari;
   	$savem= $ari->db->SetFetchMode(ADODB_FETCH_NUM);
	$id_tree = $ari->db->qMagic($this->id());
	$left_sql = "";
	$right_sql = "";
	
	if ($left === false && $right === false)
		return false;
		
	if ($left !== false)
	{$left_sql = "AND OOB_Tree_Node.Left " . $operator_left . " " . $left;}
	
	if ($right !== false)
	{$right_sql = "AND OOB_Tree_Node.Right " . $operator_right . " " . $right;}

 		$sql = "SELECT ID, ClassName, ObjectID, OOB_Tree_Node.Left, OOB_Tree_Node.Right, TreeID 
				FROM OOB_Tree_Node 
    			WHERE TreeID = $id_tree 
    			$left_sql 
				$right_sql "; 	
 	
 	$rs = $ari->db->Execute($sql);
	$ari->db->SetFetchMode($savem);
	if ($rs && !$rs->EOF) 
	{  
		$return = new OOB_tree_node	($rs->fields[0], $rs->fields[1], $rs->fields[2], $rs->fields[3], $rs->fields[4], $rs->fields[5],$this->name(), false);
	}
	else
	{
		$return = false;
	}			
	$rs->Close();
	return $return; 	 	
 }//end function 
  
 

 public function hasChildrens($node)
  {
    return (($node->right() - $node->left() -1)/2);
  }


 private function getFirstNode ()
  {
    return $this->getNodeWhere(1);
  }

 private function isFirstNode($node) 
  {
    return ($node->left()==1);
  }

  private function getLastChild($node)
  {
    return $this->getNodeWhere (false, $node->right() -1);
  }


///////--------------------------------------------
 }//end class
 
 /** Clase que tiene todos los datos pertinentes a un nodo*/
 class OOB_tree_node
 {
 public $id;
 public $class_name;
 public $object_id;
 public $left;
 public $right;
 public $treeid;
 public $treename;
 public $object = false;
 public $name = false;
 
 public function __construct ($id , $class_name, $object_id, $left, $right, $treeid, $treename, $init = true)
 {
 $this->id = $id;
 $this->class_name = $class_name;
 $this->object_id = $object_id;
 $this->left = $left;
 $this->right = $right;
 $this->treeid = $treeid;
 $this->treename = $treename;
 if ($init == true)
	 {$this->object();
	 $this->name = $this->object()->get('name');
	 }
 } 
 
public function id()
{return $this->id;	}

public function class_name()
{return $this->class_name;	}

public function object_id()
{return $this->object_id;}

public function left()
{return $this->left;}

public function right()
{return $this->right;}

public function name()
{
if (!$this->name)
	{ $this->name = $this->object()->get('name');}

return $this->name;
}

public function object()
{	if (!$this->object)
	{ $this->object = new $this->class_name ($this->object_id);}

return $this->object;
}	

public function treeid()
{return $this->treeid;}		

public function treename()
{return $this->treename;}				
 
}
 
 /** Clase que identifica al TREE_ROOT */
 class OOB_tree_root
 {
 public $id = 0;
 public $left = 0;
 public $right = 0;
 public $treeid;
 
 public function __construct ($treeid)
 {
 $this->treeid = $treeid;
 } 

public function id()
{return $this->id;	}

public function left()
{return $this->left;}

public function right()
{return $this->right;}

public function treeid()
{return $this->treeid;}		

public function name()
{return 'TREE_ROOT';}				
 
 }
?>

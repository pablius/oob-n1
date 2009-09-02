<?php

class contactos_contacto extends OOB_model_type
{
	static protected $public_properties = array(		
		 'nombre' 							=> 'isClean,isCorrectLength-0-255'
		,'apellido' 						=> 'isClean,isCorrectLength-0-255'			
		,'cuit' 							=> 'isclean'		
		,'ingbrutos' 						=> 'isclean'
		,'numerocliente' 					=> 'isclean'
		,'id_rubro' 						=> 'object-contactos_rubro'
		,'id_usuario' 						=> 'object-OOB_user'		
		,'id_tipo' 							=> 'object-contactos_tipo'		
		,'id_clase' 						=> 'object-contactos_clase'				
		,'dias_pago' 						=> 'isClean,isCorrectLength-0-255'
		,'id_categoria' 					=> 'object-impuestos_categorizacion'
		,'array_contactos_direccion' 		=> 'manyobjects-contactos_direccion'
		,'array_contactos_medios_contacto'  => 'manyobjects-contactos_medios_contacto'		
	); 
	// property => constraints
		
	static protected $table = 'contactos_contacto';
	static protected $class = __CLASS__;	
	static $orders = array('apellido','nombre'); 
	
		
	
	// definimos los attr del objeto
	public $nombre;
	public $apellido;	
	public $cuit;			
	public $clientenumber;			
	public $ingbrutos;		
	public $numerocliente;	
	public $id_rubro;			
    public $id_usuario;						
	public $id_tipo;	
	public $id_clase;	
	public $dias_pago;		
	public $id_categoria;
	public $array_contactos_direccion = array();
	public $array_contactos_medios_contacto = array();
	
	
	public function isValid(){		
		if( oob_validatetext::isCuit($this->get('cuit')) == false && $this->get('cuit') != "___________" )
		{
			$this->error()->addError("NO_CUIT");
		}

		return parent::isValid();
	}
	
	//retorna el nombre del contacto ya formateado
	// si es juridica, retorna el apellido "sino" el apellido,nombre
	function name(){							
			
			// tipo == 1 == juridica
			if( $this->get('tipo')->id() == 1 ){			
				return $this->get('apellido');
			}
			else
			{
				return $this->get('apellido') . ', ' . $this->get('nombre');		
			}//end if
			
	}//end function
	
	
	public function condicion_pago(){
	
		$dias = $this->get('dias_pago');
		if( $dias == 0 ){
			return "Contado";
		}
		else
		{
			return $dias . ' d&iacute;a' . (($dias > 1)?'s':'');
			
		}//end if		
	
	}//end function
	
	//devuelve el listado de usuarios no asignados a ningun contacto
	static public function listNoAssigned($sort = 'uname', $pos = 0, $limit = 20, $where=false )
	{
		global $ari;
		$estado = $ari->db->qMagic(USED);	
		
		if (in_array ($sort, oob_user::getOrders()))
		{	$sortby = "ORDER BY $sort";
		}
		else
		{	$sortby = "ORDER BY uname";
		}
		
		$str_where = "";
		if($where){
			$str_where = " $where AND ";
		}
			
	   $sql = "SELECT u.id,u.uname,u.password,u.email,u.connections,u.status 
			   FROM oob_user_user  u left join contactos_contacto c 
			   on u.id = c.id_usuario
			   WHERE {$str_where} c.id_usuario IS NULL 			   			   
			  ";

		$savem = $ari->db->SetFetchMode(ADODB_FETCH_ASSOC);
		if($pos === false || $limit === false)
		{	$rs = $ari->db->Execute($sql);
		}
		else
		{	$rs = $ari->db->SelectLimit($sql, $limit, $pos);
		}
		$ari->db->SetFetchMode($savem);
		if ($rs && !$rs->EOF) 
		{ 
			$i=0;
			while (!$rs->EOF) 
			{
				$return[$i] = new oob_user (ID_UNDEFINED);
				$return[$i]->set( 'user', $rs->fields["id"] );
				$return[$i]->set( 'uname', $rs->fields["uname"] );
				$return[$i]->set( 'password' , $rs->fields["password"] );
				$return[$i]->set( 'email' , $rs->fields["email"] );
				$return[$i]->set( 'maxcon' , $rs->fields["connections"] );
				$return[$i]->set( 'status' , $rs->fields["status"] );				
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
		
	//detecta si el cuit esta duplicado
	protected function isDuplicated()
	{

				global $ari;
				$table = static::getTable();
				$id = $this->id;

				if ($id < ID_UNDEFINED)
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
				$cuit = $ari->db->qMagic($this->get('cuit'));
				$sql= "SELECT true as cuenta, status, id FROM $table WHERE `cuit` <> '___________' AND `cuit` = $cuit $clausula";


				$rs = $ari->db->Execute($sql);
				$ari->db->SetFetchMode($savem);
			
					//si existe
					if( !$rs->EOF && $rs->fields[0]!= 0 )
					{
						//en caso que exista y el estado sea borrado entonces lo pongo como no duplicado
						if(  $rs->fields[1] == 9 ){					
							$this->id = $rs->fields[2];
							$this->set('status',1);
							return false;
						}else{
							return true;
						}
					}
					else
					{
						return false;
					}
					
				
				
	}//end function

		
	 public function printable_address($tipo){
	 
		$dir = "";
	 
		if( $direcciones = contactos_direccion::getRelated($this) ){
		
			
			foreach( $direcciones as $direccion ){
				
				if( $direccion->get('tipo')->id() == $tipo->id() ){
						$dir = $direccion->get_printable();
				}
			
			}//end each
		
		}
	 
		return $dir;
	 
	 }//end function
	 
	 public function facturas_pendientes(){
	 
		global $ari;
	 
		$return1 = false;
		//con en ventas
		$sql = " select count(*) as cantidad from movimientos_movimiento where id in( ";
		$sql.= " select id_movimiento from movimientos_relacion where id_relacion in( ";
		$sql.= " select id from ventas_venta where id_contacto = '{$this->id()}') and class_relacion = 'ventas_venta' ) ";
		$sql.= " and id_estado in(3,5,7) ";
		
		$rs = $ari->db->Execute($sql);
		
		if( !$rs->EOF && $rs->fields[0] != 0 )
		{
			$return1 = true;
		}
		
		$return2 = false;
		//veo en compras
		$sql = " select count(*) as cantidad from movimientos_movimiento where id in( ";
		$sql.= " select id_movimiento from movimientos_relacion where id_relacion in( ";
		$sql.= " select id from compras_compra where id_contacto = '{$this->id()}') and class_relacion = 'compras_compra' ) ";
		$sql.= " and id_estado in(3,5,7) ";
		
		$rs = $ari->db->Execute($sql);
		
		if( !$rs->EOF && $rs->fields[0] != 0 )
		{
			$return2 = true;
		}
		
		return ( $return1 || $return2 );
	 
	 }//end function
	 
	 public function get_Printable( $returnArray = false ){
	 
		$name = '';
	 
		if( $returnArray ){			
			$name = array();
			$name['name'] = $this->name();							
			$name['id'] = $this->id();
			$name['categoria'] = $this->get('categoria')->get('nombre');
			$name['discrimina'] =  ( $this->get('categoria')->get('discrimina') == "1" );
			$name['letra'] = $this->get('categoria')->get('letra_factura');
			$name['cuit'] = $this->get('cuit');
			$name['domicilio_facturacion'] = $this->printable_address( new contactos_direccion_tipo(2) );								
		}
		else
		{
			$name = $this->name();							
		}//end if	
	 
		return $name;
	 
	 }//end function
	
}//end class

?>
